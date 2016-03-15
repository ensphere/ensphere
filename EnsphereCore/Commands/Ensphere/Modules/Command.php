<?php namespace EnsphereCore\Commands\Ensphere\Modules;

use EnsphereCore\Commands\Ensphere\Traits\Module as ModuleTrait;
use Illuminate\Console\Command as IlluminateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use EnsphereCore\Commands\Ensphere\Make\Contract\Command as ContractCommand;
use EnsphereCore\Commands\Ensphere\Make\Controller\Command as ControllerCommand;
use STDclass;

class Command extends IlluminateCommand {

	use ModuleTrait;

	/**
	 * [$name description]
	 * @var string
	 */
	protected $name = 'ensphere:modules';

	/**
	 * [$description description]
	 * @var string
	 */
	protected $description = 'Adds starting modules.';

	/**
	 * [$packagist description]
	 * @var string
	 */
	protected $packagist = 'http://modules.testing.pm/';

	/**
	 * [$currentStructure description]
	 * @var [type]
	 */
	private $currentStructure;

	/**
	 * [fire description]
	 * @return [type] [description]
	 */
	public function fire()
	{
		$use = [];
		$modules = $this->getModules();
		foreach( $modules as $module ) {
			$wantsToUse = $this->ask( "install {$module->name}? [y/n]" );
			if( $wantsToUse === 'y' ) {
				$use[$module->name] = '*';
			}
		}
		$composer = $this->getComposerFiles();
		foreach( $use as $name => $version ) {
			$composer->require->{$name} = $version;
		}
		$this->saveComposerFile( $composer );
	}

	/**
	 * [getModules description]
	 * @return [type] [description]
	 */
	protected function getModules()
    {
    	$file = json_decode( file_get_contents( $this->packagist . 'packages.json' ) )->includes;
    	$file = json_decode( file_get_contents( $this->packagist . key( $file ) ) );
    	return $this->normaliseData( $file->packages );
    }

    /**
     * [normaliseData description]
     * @param  [type] $modules [description]
     * @return [type]          [description]
     */
    protected function normaliseData( $modules )
    {
    	$mods = [];
    	$currentModules = $this->getCurrentModules();
    	foreach( $modules as $moduleName => $moduleVersions ) {
    		$stub = new STDclass;
    		$stub->name = $moduleName;
    		$stub->versions = ['' => '--' ];
    		foreach( $moduleVersions as $version ) {
    			$stub->versions[$version->version] = $version->version;
    		}
    		$stub->description = isset( $version->description ) ? $version->description : 'n/a';
    		$stub->url = 'https://bitbucket.org/' . $moduleName;
    		$stub->git = $version->source->url;
    		$stub->installed = isset( $currentModules[$moduleName] );
    		$stub->tolerance = 'strict';

    		if( $stub->installed ) {

    			$stub->current_version = $currentModules[$moduleName];

    			if( preg_match( "/^([0-9]+)\.\*$/", $stub->current_version, $match ) )
    			{   /** wild match */
    				$greaterThan = ($match[1] . '000') - 1;
    				$lessThan = $greaterThan + 1001;
    				$stub->tolerance = self::WILD;
    			} elseif( preg_match( "/^([0-9]+)\.([0-9]+)\.\*$/", $stub->current_version, $match ) )
    			{   /** semi-wild match */
    				$greaterThan = ($match[1] . $match[2] . '00') - 1;
    				$lessThan = $greaterThan + 101;
    				$stub->tolerance = self::SEMIWILD;
    			} elseif( preg_match( "/^([0-9]+)\.([0-9]+)\.([0-9]+)\.\*$/", $stub->current_version, $match ) )
    			{   /** micro wild match */
    				$greaterThan = ($match[1] . $match[2] . $match[3] . '0') - 1;
    				$lessThan = $greaterThan + 11;
    				$stub->tolerance = self::MICROWILD;
    			}

    			if( isset( $greaterThan ) ) {
    				foreach( $stub->versions as $version2 ) {
    					$toMatch = str_pad( str_replace( ['.','*'], '', $version2 ), 4, 0, STR_PAD_RIGHT );
    					if( $toMatch > $greaterThan && $toMatch < $lessThan ) {
    						$stub->current_version = $version2;
    						continue;
    					}
    				}
    			}

    		}
    		$mods[] = $stub;
    	}
		return $mods;
    }

    /**
     * [getComposerFile description]
     * @return [type] [description]
     */
    protected function getCurrentModules()
    {
    	return (array)$this->getComposerFiles()->require;
    }

   	/**
   	 * [getComposerFiles description]
   	 * @return [type] [description]
   	 */
    protected function getComposerFiles()
    {
    	return json_decode( file_get_contents( base_path( 'composer.json' ) ) );
    }

    /**
	 * [saveComposerFile description]
	 * @param  STDclass $composer [description]
	 * @return [type]             [description]
	 */
	protected function saveComposerFile( STDclass $composer )
	{
		$jsonData = json_encode( $composer, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES );
		file_put_contents( base_path( 'composer.json' ), $jsonData );
	}

	/**
	 * [getArguments description]
	 * @return [type] [description]
	 */
	protected function getArguments()
	{
		return [];
	}

	/**
	 * [getOptions description]
	 * @return [type] [description]
	 */
	protected function getOptions()
	{
		return [];
	}

}