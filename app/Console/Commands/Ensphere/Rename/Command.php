<?php namespace Ensphere\Ensphere\Console\Commands\Ensphere\Rename;

use Ensphere\Ensphere\Console\Commands\Ensphere\Traits\Module as ModuleTrait;
use Illuminate\Console\Command as IlluminateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Command extends IlluminateCommand {

	use ModuleTrait;

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
		$this->renameDatabaseFolders();
		$this->updateRegistrationFile();
		$this->updateGulpFile();
		$this->updateComposerFile();
		$this->updatePackagesFile();
	}

	/**
	 * [renameDatabaseFolders description]
	 * @return [type] [description]
	 */
	private function renameDatabaseFolders() {
		rename( base_path( "database/migrations/vendor/{$this->currentVendor}/{$this->currentModule}" ), base_path( "database/migrations/vendor/{$this->currentVendor}/{$this->module}" ) );
		rename( base_path( "database/migrations/vendor/{$this->currentVendor}" ), base_path( "database/migrations/vendor/{$this->vendor}" ) );
		rename( base_path( "database/seeds/vendor/{$this->currentVendor}/{$this->currentModule}" ), base_path( "database/seeds/vendor/{$this->currentVendor}/{$this->module}" ) );
		rename( base_path( "database/seeds/vendor/{$this->currentVendor}" ), base_path( "database/seeds/vendor/{$this->vendor}" ) );
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
		$newContents = str_replace( "\"{$this->currentVendor}/{$this->currentModule}\"", "\"{$this->vendor}/{$this->module}\"", $newContents );
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

}