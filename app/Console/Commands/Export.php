<?php namespace Ensphere\Ensphere\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Export extends Command {

	/**
	 * [$name description]
	 * @var string
	 */
	protected $name = 'ensphere:export';

	/**
	 * [$description description]
	 * @var string
	 */
	protected $description = 'Clears up the module to align with installation.';

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
		$this->currentStructure = $this->getCurrentVendorAndModuleName();
		$this->exportMigrations();
	}

	/**
	 * [exportMigrations description]
	 * @return [type] [description]
	 */
	private function exportMigrations()
	{
		$databaseFolder = base_path( 'database/migrations/vendor/authentication/' );
		if( ! file_exists( $databaseFolder ) ) {
			return $this->line( 'no migrations to move' );
		}
		$this->moveFilesToResources( $databaseFolder );
	}

	/**
	 * [moveFilesToResources description]
	 * @return [type] [description]
	 */
	private function moveFilesToResources( $folder )
	{
		$moved = array();
		foreach( new RecursiveIteratorIterator( new RecursiveDirectoryIterator( $folder ) ) as $file ) {
			if( $file->getExtension() === 'php' ) {
				copy( $folder . $file->getBasename(), base_path( 'resources/database/migrations/' . $file->getBasename() ) );
				$moved[] = $file->getBasename() . ' moved to resources';
				unlink( $folder . $file->getBasename() );
			}
		}
		if( ! $moved ) {
			$this->line( 'no migrations to move' );
		} else {
			foreach( $moved as $message ) {
				$this->info( $message );
			}
		}
	}

	/**
	 * [getCurrentVendorAndModuleName description]
	 * @return [type] [description]
	 */
	private function getCurrentVendorAndModuleName()
	{
		$composerDotJson = json_decode( file_get_contents( base_path('composer.json') ) );
		$psr4Autoload = $composerDotJson->autoload->{'psr-4'};
		$name = null;
		foreach( $psr4Autoload as $nameSpace => $folder ) {
			if( $folder == 'app/' ) {
				$name = $nameSpace;
				break;
			}
		}
		if( is_null( $name ) ) {
			$this->error('Could not find namespace from composer.json');
		}
		if( ! preg_match( "#^([a-z0-9]+)\\\([a-z0-9]+)\\\\$#is", $name, $match ) ) {
			$this->error('Could not find namespace from composer.json');
		}
		return [
			'vendor' => $this->getComputerNameFromCamelCase($match[1]),
			'module' => $this->getComputerNameFromCamelCase($match[2]),
			'camelCasedVendor' => $match[1],
			'camelCasedModule' => $match[2]
		];
	}

	/**
	 * [getComputerNameFromCamelCase description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	private function getComputerNameFromCamelCase( $string )
	{
		return strtolower( implode( "-", array_filter( preg_split( "/(?=[A-Z])/", $string ) ) ) );
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