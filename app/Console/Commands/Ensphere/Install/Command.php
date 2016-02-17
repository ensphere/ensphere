<?php namespace Ensphere\Ensphere\Console\Commands\Ensphere\Install;

use Ensphere\Ensphere\Console\Commands\Ensphere\Traits\Module as ModuleTrait;
use Illuminate\Console\Command as IlluminateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Command extends IlluminateCommand {

	use ModuleTrait;

	/**
	 * [$name description]
	 * @var string
	 */
	protected $name = 'ensphere:install';

	/**
	 * [$description description]
	 * @var string
	 */
	protected $description = 'Initial installation for application';

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
		if( ! $this->hasBeenInstalled() ) {
			$this->installNodeModules();
			$this->installBowerComponents();
			$this->generateRegistrationFile();
			$this->publishVendorAssets();
			$this->combineVendorAssets();
			$this->runGulp();
			$this->defineAsinstalled();
		} else {
			$this->error( "application has already ran the install process --canceled" );
		}
	}

	/**
	 * [installNodeModules description]
	 * @return [type] [description]
	 */
	private function installNodeModules()
	{
		$this->info( shell_exec( "npm install --silent" ) );
	}

	/**
	 * [installBowerComponents description]
	 * @return [type] [description]
	 */
	private function installBowerComponents()
	{
		$this->info( shell_exec( "bower install" ) );
	}

	/**
	 * [generateRegistrationFile description]
	 * @return [type] [description]
	 */
	private function generateRegistrationFile()
	{
		$this->info( shell_exec( "php artisan ensphere:register" ) );
	}

	/**
	 * [publishVendorAssets description]
	 * @return [type] [description]
	 */
	private function publishVendorAssets()
	{
		$this->info( shell_exec( "php artisan vendor:publish --force" ) );
	}

	/**
	 * [combineVendorAssets description]
	 * @return [type] [description]
	 */
	private function combineVendorAssets()
	{
		$this->info( shell_exec( "php artisan ensphere:bower" ) );
	}

	/**
	 * [runGulp description]
	 * @return [type] [description]
	 */
	private function runGulp()
	{
		$this->info( shell_exec( "gulp" ) );
	}

	/**
	 * [hasBeenInstalled description]
	 * @return boolean [description]
	 */
	private function hasBeenInstalled()
	{
		return file_exists( __DIR__ . '/.installed');
	}

	/**
	 * [defineAsinstalled description]
	 * @return [type] [description]
	 */
	private function defineAsinstalled()
	{
		touch( __DIR__ . '/.installed' );
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