<?php namespace Ensphere\Ensphere\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class ModuleName extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ensphere:rename';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Rename your module';

	/**
	 * [$vendor description]
	 * @var string
	 */
	private $vendor = '';

	/**
	 * [$camelCasedVendor description]
	 * @var string
	 */
	private $camelCasedVendor = '';

	/**
	 * [$module description]
	 * @var string
	 */
	private $module = '';

	/**
	 * [$camelCasedModule description]
	 * @var string
	 */
	private $camelCasedModule = '';

	/**
	 * [$currentVendor description]
	 * @var string
	 */
	private $currentVendor = '';

	/**
	 * [$currentCamelCasedVendor description]
	 * @var string
	 */
	private $currentCamelCasedVendor = '';

	/**
	 * [$currentModule description]
	 * @var string
	 */
	private $currentModule = '';

	/**
	 * [$currentCamelCasedModule description]
	 * @var string
	 */
	private $currentCamelCasedModule = '';

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		parent::__construct();
		$currentData = $this->getCurrentVendorAndModuleName();
		$this->currentVendor = $currentData['vendor'];
		$this->currentCamelCasedVendor = $currentData['camelCasedVendor'];
		$this->currentModule = $currentData['module'];
		$this->currentCamelCasedModule = $currentData['camelCasedModule'];
	}

	/**
	 * [fire description]
	 * @return [type] [description]
	 */
	public function fire()
	{
		$this->vendor = $this->ask('Whats your Vendor name?');
		$this->camelCasedVendor = ucfirst( camel_case( $this->vendor ) );
		$this->module = $this->ask('Whats your Module name?');
		$this->camelCasedModule = ucfirst( camel_case( $this->module ) );
		$this->laravelRename();
		$this->moduleRename();
		$this->dumpAutoload();
		$this->info("done!");
	}

	/**
	 * [dumpAutoload description]
	 * @return [type] [description]
	 */
	private function dumpAutoload() {
		$localComposerFile = base_path('composer.phar');
		if( file_exists( $localComposerFile ) ) {
			echo shell_exec("php {$localComposerFile} dump-autoload");
			$this->info("...autoload dumped!");
		} else {
			$this->info("Couldn't find local composer file, please run dump-autoload via composer");
		}
	}

	/**
	 * [laravelRename description]
	 * @return [type] [description]
	 */
	private function laravelRename() {
		$this->call( "app:name",  ["name" => "{$this->camelCasedVendor}\\{$this->camelCasedModule}"]);
	}

	/**
	 * [moduleRename description]
	 * @return [type] [description]
	 */
	private function moduleRename() {
		$this->renamePublicFolders();
		$this->updateRegistrationFile();
		$this->updateGulpFile();
		$this->updateComposerFile();
		$this->updatePackagesFile();
	}

	/**
	 * [renamePublicFolders description]
	 * @return [type] [description]
	 */
	private function renamePublicFolders() {
		rename( public_path("package/{$this->currentVendor}/{$this->currentModule}"), public_path("package/{$this->currentVendor}/{$this->module}") );
		rename( public_path("package/{$this->currentVendor}"), public_path("package/{$this->vendor}") );
	}

	/**
	 * [updatePackagesFile description]
	 * @return [type] [description]
	 */
	private function updatePackagesFile() {
		$file = base_path('config/packages.json');
		if( ! file_exists( $file ) ) return;
		$contents = file_get_contents( $file );
		$newContents = str_replace( "{$this->currentCamelCasedVendor}\\\\{$this->currentCamelCasedModule}", "{$this->camelCasedVendor}\\\\{$this->camelCasedModule}", $contents );
		file_put_contents( $file, $newContents );
	}

	/**
	 * [updateComposerFile description]
	 * @return [type] [description]
	 */
	private function updateComposerFile() {
		$file = base_path('composer.json');
		if( ! file_exists( $file ) ) return;
		$contents = file_get_contents( $file );
		$newContents = str_replace( "{$this->currentCamelCasedVendor}\\\\{$this->currentCamelCasedModule}", "{$this->camelCasedVendor}\\\\{$this->camelCasedModule}", $contents );
		file_put_contents( $file, $newContents );
	}

	/**
	 * [updateRegistrationFile description]
	 * @return [type] [description]
	 */
	private function updateRegistrationFile() {
		$file = base_path('registration.json');
		if( ! file_exists( $file ) ) return;
		$contents = file_get_contents( $file );
		$newContents = str_replace( "{$this->currentCamelCasedVendor}\\\\{$this->currentCamelCasedModule}", "{$this->camelCasedVendor}\\\\{$this->camelCasedModule}", $contents );
		file_put_contents( $file, $newContents );
	}

	/**
	 * [updateGulpFile description]
	 * @return [type] [description]
	 */
	private function updateGulpFile() {
		$file = base_path('gulpfile.js');
		if( ! file_exists( $file ) ) return;
		$contents = file_get_contents( $file );
		$newContents = str_replace( "/{$this->currentVendor}/{$this->currentModule}/", "/{$this->vendor}/{$this->module}/", $contents );
		file_put_contents( $file, $newContents );
	}

	/**
	 * [getCurrentVendorAndModuleName description]
	 * @return [type] [description]
	 */
	private function getCurrentVendorAndModuleName() {
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
	private function getComputerNameFromCamelCase( $string ) {
		return strtolower( implode( "-", array_filter( preg_split( "/(?=[A-Z])/", $string ) ) ) );
	}

}