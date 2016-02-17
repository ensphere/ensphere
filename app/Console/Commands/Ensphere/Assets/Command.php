<?php namespace Ensphere\Ensphere\Console\Commands\Ensphere\Assets;

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
	protected $name = 'ensphere:assets';

	/**
	 * [$description description]
	 * @var string
	 */
	protected $description = 'Combines the module assets into single files';

	/**
	 * [$dir description]
	 * @var [type]
	 */
	protected $dir;

	/**
	 * [fire description]
	 * @return [type] [description]
	 */
	public function fire()
	{
		$this->dir = public_path( 'package' );
		$cssFiles = $this->getModuleCssFiles();
		$jsFiles = $this->getModuleJsFiles();
		$this->combine( $cssFiles, public_path( 'css/packages.all.css' ) );
		$this->combine( $jsFiles, public_path( 'js/packages.all.js' ) );
	}

	/**
	 * [combine description]
	 * @param  array  $files        [description]
	 * @param  [type] $savePathname [description]
	 * @return [type]               [description]
	 */
	protected function combine( array $files, $savePathname ) {
		$data = '';
		foreach( $files as $file ) {
			$data .= file_get_contents( $file );
		}
		file_put_contents( $savePathname, $data );
		$this->info( count( $files ) . " combined to {$savePathname}");
	}

	/**
	 * [getModuleJsFiles description]
	 * @return [type] [description]
	 */
	protected function getModuleJsFiles()
	{
		$files = array();
		if( file_exists( $this->dir ) ) {
			$it = new RecursiveDirectoryIterator( $this->dir );
			foreach( new RecursiveIteratorIterator( $it ) as $file ) {
				if( $file->getExtension() === 'js' ) {
					$files[] = $file->getPathname();
				}
			}
		}
		return $files;
	}

	/**
	 * [getModuleCssFiles description]
	 * @return [type] [description]
	 */
	protected function getModuleCssFiles()
	{
		$files = array();
		if( file_exists( $this->dir ) ) {
			$it = new RecursiveDirectoryIterator( $this->dir );
			foreach( new RecursiveIteratorIterator( $it ) as $file ) {
				if( $file->getExtension() === 'css' ) {
					$files[] = $file->getPathname();
				}
			}
		}
		return $files;
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