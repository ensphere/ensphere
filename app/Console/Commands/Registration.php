<?php namespace Ensphere\Ensphere\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Registration extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ensphere:register';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Register package Middleware and Providers.';

	/**
	 * [$writePath description]
	 * @var null
	 */
	private $writePath = 'config/packages.json';

	/**
	 * [__construct description]
	 */
	public function __construct()
	{
		parent::__construct();
		$this->writePath = base_path($this->writePath);
	}

	/**
	 * [fire description]
	 * @return [type] [description]
	 */
	public function fire()
	{
		$packages = $this->getPendingPackageDirs();
		if( empty( $packages ) ) {
			return $this->error('no packages require registering.');
		}
		$this->save( $this->getRegistrationData( $packages ) );
		return $this->info('config file generated!');
	}

	/**
	 * [getRegistrationFiles description]
	 * @return [type] [description]
	 */
	protected function getPendingPackageDirs() {
		$files = [];
		$it = new RecursiveDirectoryIterator( base_path( 'vendor' ) );
		foreach( new RecursiveIteratorIterator( $it ) as $file ) {
			if( $file->getFilename() == 'registration.json' ) {
				$files[] = $file->getPath();
			}
		}
		return $files;
	}

	/**
	 * [getRegistrationData description]
	 * @param  [type] $paths [description]
	 * @return [type]        [description]
	 */
	protected function getRegistrationData( $paths ) {
		$config = [];
		foreach( $paths as $path ) {
			$data = json_decode( file_get_contents( $path . '/registration.json' ) );
			if( ! is_object( $data ) ) {
				return $this->error('incorrect json format: ' . $path . '/registration.json');
			}
			$config[] = (array)$data;
		}
		return $this->combineConfigs( $config );
	}

	/**
	 * [combineConfigs description]
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	protected function combineConfigs( $configs ) {
		$return = [
			'providers' => array(),
			'aliases' => array(),
			'middleware' => array(),
			'routeMiddleware' => array()
		];
		foreach( $configs as $config ) {
			if( isset( $config['providers'] ) ) {
				$return['providers'] = array_merge( $return['providers'], $config['providers'] );
			}
			if( isset( $config['aliases'] ) ) {
				$return['aliases'] = array_merge( $return['aliases'], $config['aliases'] );
			}
			if( isset( $config['middleware'] ) ) {
				$return['middleware'] = array_merge( $return['middleware'], $config['middleware'] );
			}
			if( isset( $config['routeMiddleware'] ) ) {
				foreach( $config['routeMiddleware'] as $name => $namespace ) {
					$return['routeMiddleware'][$name] = $namespace;
				}
			}
		}
		return $return;
	}

	/**
	 * [save description]
	 * @param  [type] $config [description]
	 * @return [type]         [description]
	 */
	protected function save( $config ) {
		touch($this->writePath);
		file_put_contents( $this->writePath, json_encode( $config ) );
	}

}