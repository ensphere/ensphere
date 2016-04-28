<?php namespace EnsphereCore\Commands\Ensphere\Rename;

use EnsphereCore\Commands\Ensphere\Traits\Module as ModuleTrait;
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
		if( $this->isOkToRun() ) {
			$this->laravelRename();
			$this->moduleRename();
			$this->dumpAutoload();
			$this->info("done!");
		} else {
			$this->error( 'Cannot rename module, vendor/module alread exists in application!' );
		}
	}

	/**
	 * [isOkToRun description]
	 * @return boolean [description]
	 */
	private function isOkToRun()
	{
		return file_exists( base_path("public/package/{$this->vendor}/{$this->module}/" ) ) ? false : true;
	}

	/**
	 * [dumpAutoload description]
	 * @return [type] [description]
	 */
	private function dumpAutoload()
	{
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
	private function laravelRename()
	{
		$this->call( "app:name",  ["name" => "{$this->camelCasedVendor}\\{$this->camelCasedModule}"]);
	}

	/**
	 * [moduleRename description]
	 * @return [type] [description]
	 */
	private function moduleRename()
	{
		$this->renamePublicFolders();
		$this->renameDatabaseFolders();
		$this->updateRegistrationFile();
		$this->updateReferences();
		$this->updateGulpFile();
		$this->updateComposerFile();
		$this->updatePackagesFile();
		$this->updateDotGitIgnoreFile();
	}

	/**
	 * [renameDatabaseFolders description]
	 * @return [type] [description]
	 */
	private function renameDatabaseFolders()
	{
		$this->renameMigrationFolders();
		$this->renameSeedsFolders();
	}

	/**
	 * [updateReferences description]
	 * @return [type] [description]
	 */
	private function updateReferences()
	{
		// Application files
		$files = $this->findAllFiles( app_path() );
		foreach( $files as $file ) {
			$contents = preg_replace( "/([\/']){$this->currentVendor}([\/\.]){$this->currentModule}([\/']|(?:::))/", "$1{$this->vendor}$2{$this->module}$3", file_get_contents( $file ) );
			file_put_contents( $file, $contents );
		}
		// Resource views
		$files = $this->findAllFiles( base_path('resources/views') );
		foreach( $files as $file ) {
			$contents = preg_replace( "/([\/']){$this->currentVendor}([\/\.]){$this->currentModule}([\/']|(?:::))/", "$1{$this->vendor}$2{$this->module}$3", file_get_contents( $file ) );
			file_put_contents( $file, $contents );
		}
	}

	/**
	 * [renameMigrationFolders description]
	 * @return [type] [description]
	 */
	private function renameMigrationFolders()
	{
		$this->copyOrRename( 'database/migrations/vendor' );
	}

	/**
	 * [renameSeedsFolders description]
	 * @return [type] [description]
	 */
	private function renameSeedsFolders()
	{
		$this->copyOrRename( 'database/seeds/vendor' );
	}

	/**
	 * [renamePublicFolders description]
	 * @return [type] [description]
	 */
	private function renamePublicFolders()
	{
		$this->copyOrRename( 'public/package' );
	}

	/**
	 * [updatePackagesFile description]
	 * @return [type] [description]
	 */
	private function updatePackagesFile()
	{
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
	private function updateComposerFile()
	{
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
	private function updateRegistrationFile()
	{
		$file = base_path('EnsphereCore/ensphere-registration.json');
		if( ! file_exists( $file ) ) return;
		$contents = file_get_contents( $file );
		$newContents = str_replace( "{$this->currentCamelCasedVendor}\\\\{$this->currentCamelCasedModule}", "{$this->camelCasedVendor}\\\\{$this->camelCasedModule}", $contents );
		file_put_contents( $file, $newContents );
	}

	/**
	 * [updateGulpFile description]
	 * @return [type] [description]
	 */
	private function updateGulpFile()
	{
		$file = base_path('gulpfile.js');
		if( ! file_exists( $file ) ) return;
		$contents = file_get_contents( $file );
		$newContents = str_replace( "public/package/{$this->currentVendor}/{$this->currentModule}/", "public/package/{$this->vendor}/{$this->module}/", $contents );
		file_put_contents( $file, $newContents );
	}

	/**
	 * [updateDotGitIgnoreFile description]
	 * @return [type] [description]
	 */
	private function updateDotGitIgnoreFile()
	{
		$file = base_path('.gitignore');
		if( ! file_exists( $file ) ) return;
		$contents = explode( "\n", file_get_contents( $file ) );
		foreach( $contents as $key => $line ) {
			$line = trim( $line );
			$line = preg_replace( "#^(.*){$this->currentVendor}/{$this->currentModule}(.*)$#is", "$1{$this->vendor}/{$this->module}$2", $line );
			$contents[$key] = preg_replace( "#^!/public/package/{$this->currentVendor}/$#is", "!/public/package/{$this->vendor}/", $line );
		}
		$contents = implode( "\n", $contents );
		file_put_contents( $file, $contents );
	}

	/**
	 * [deleteDirectory description]
	 * @param  [type] $dir [description]
	 * @return [type]      [description]
	 */
	private function deleteDirectory($dir)
	{
	    if ( ! file_exists( $dir ) ) return true;
	    if ( ! is_dir( $dir ) ) return unlink( $dir );
	    foreach ( scandir( $dir ) as $item ) {
	        if ( $item == '.' || $item == '..' ) continue;
	        if ( ! $this->deleteDirectory($dir . DIRECTORY_SEPARATOR . $item ) ) return false;
	    }
	    return rmdir( $dir );
	}

	/**
	 * [copyOrRename description]
	 * @param  [type] $pathPrefix [description]
	 * @return [type]             [description]
	 */
	public function copyOrRename( $pathPrefix )
	{
		if( $this->currentVendor !== $this->vendor ) {
			$newDir = base_path( "{$pathPrefix}/{$this->vendor}" );
			if( ! file_exists( $newDir ) ) mkdir( $newDir, 0755 );
			$this->copy(
				base_path( "{$pathPrefix}/{$this->currentVendor}/{$this->currentModule}" ),
				base_path("{$pathPrefix}/{$this->vendor}/{$this->module}")
			);
		} else {
			rename( base_path( "{$pathPrefix}/{$this->currentVendor}/{$this->currentModule}" ), base_path("{$pathPrefix}/{$this->currentVendor}/{$this->module}" ) );
			rename( base_path( "{$pathPrefix}/{$this->currentVendor}"), base_path("{$pathPrefix}/{$this->vendor}" ) );
		}
    	$this->deleteDirectory( base_path( "{$pathPrefix}/{$this->currentVendor}/{$this->currentModule}" ) );
    	if( !(new \FilesystemIterator( base_path( "{$pathPrefix}/{$this->currentVendor}" ) ) )->valid() ) {
    		rmdir( base_path( "{$pathPrefix}/{$this->currentVendor}" ) );
    	}
	}

	/**
	 * [findAllFiles description]
	 * @param  [type] $dir [description]
	 * @return [type]      [description]
	 */
	private function findAllFiles( $dir )
	{
		$dir = rtrim( $dir, '/' );
	    $root = scandir( $dir );
	    foreach( $root as $value ) {
	        if( $value === '.' || $value === '..' ) continue;
	        if( is_file( "{$dir}/{$value}" ) ) {
	        	$result[] = "{$dir}/{$value}";
	        	continue;
	        }
	        foreach( $this->findAllFiles( "{$dir}/{$value}" ) as $value ) {
	            $result[] = $value;
	        }
	    }
	    return $result;
	}

	/**
	 * [copy description]
	 * @param  [type] $source      [description]
	 * @param  [type] $destination [description]
	 * @return [type]              [description]
	 */
	private function copy( $source, $destination )
	{
		$delete = [];
		$source = rtrim( $source, '/' ) . '/';
		$destination = rtrim( $destination, '/' ) . '/';
	    $files = $this->findAllFiles( $source );
	    foreach ( $files as $file ) {
	        if ( in_array( $file, array( ".", ".." ) ) ) continue;
	        $file = str_replace( $source, '', $file );
	        if( ! file_exists( $destination . dirname( $file ) ) ) {
				mkdir( $destination . dirname( $file ), 0755, true);
			}
	        if ( @copy( $source . $file, $destination . $file ) ) {
	            $delete[] = $source . $file;
	        }
	    }
	    foreach ( $delete as $file ) {
	        unlink( $file );
	    }
	}

}