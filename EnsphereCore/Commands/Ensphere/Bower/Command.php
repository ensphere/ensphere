<?php namespace EnsphereCore\Commands\Ensphere\Bower;

use Illuminate\Console\Command as IlluminateCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class Command extends IlluminateCommand {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'ensphere:bower';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Compacts Bower Components.';

	/**
	 * [$writePath description]
	 * @var null
	 */
	private $writePath = 'resources/views/';

	/**
	 * [$order description]
	 * @var [type]
	 */
	private $order = [];

	/**
	 * [$ordered description]
	 * @var [type]
	 */
	private $ordered = [];

	/**
	 * [$satisfield description]
	 * @var integer
	 */
	private $satisfield = 0;

	/**
	 * [$bowers description]
	 * @var [type]
	 */
	private $bowers = [];

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->writePath = base_path($this->writePath);
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function fire()
	{
		$it = new RecursiveDirectoryIterator( base_path() );
		foreach( new RecursiveIteratorIterator( $it ) as $file ) {
			if( $file->getFilename() === 'ensphere-assets.json' ) {
				$packages = $this->getPackages( $file->getPath() );
				foreach( $packages as $name => $packageData ) {
					$this->bowers[] = new Bower( $name, $packageData );
				}
			}
		}
		// Order the array by dependencies
		$this->order();
		// Generate the blade template
		$this->generateTemplate();
		$this->info('HTTP snippets generated!');
	}

	/**
	 * [getPackages description]
	 * @param  [type] $path [description]
	 * @return [type]       [description]
	 */
	private function getPackages( $path ) {
		return json_decode( file_get_contents( $path . '/ensphere-assets.json' ) );
	}

	/**
	 * [order description]
	 * @return [type] [description]
	 */
	private function order() {
		foreach( $this->bowers as $bower ) {
			$this->order[$bower->name()] = [
				'dependencies' => $bower->getDependencies(),
				'bower' => $bower
			];
		}
		$this->orderItems();
	}

	/**
	 * [orderItems description]
	 * @return [type] [description]
	 */
	private function orderItems(){
		while( ! empty( $this->order ) ) {
			$item = array_splice( $this->order, 0, 1 );
			$data = end( $item );
			$name = key( $item );
			if( empty( $data['dependencies'] ) ) {
				$this->ordered[$name] = $data;
			} else {
				$satisafied = true;
				foreach( $data['dependencies'] as $dependency ) {
					if( isset( $this->order[$dependency] ) ) $satisafied = false;
				}
				if( $satisafied ) {
					$this->ordered[$name] = $data;
				} else {
					$this->order = $this->order + $item;
				}
			}
		}
	}

	/**
	 * [assetLoaderTemplate description]
	 * @param  [type] $jsFiles  [description]
	 * @param  [type] $cssFiles [description]
	 * @return [type]           [description]
	 */
	public static function assetLoaderTemplate( $jsFiles, $cssFiles ) {

		$return = '';
		foreach( $cssFiles as $file ) {
			$return .= "<link href='{$file}' rel='stylesheet' type='text/css'>\n\r";
		}
		foreach( $jsFiles as $file ) {
			$return .= "<script src='{$file}'></script>\n\r";
		}
		return $return;
	}

	/**
	 * [buildCombinedAssets description]
	 * @param  [type] $assetGroups [description]
	 * @return [type]              [description]
	 */
	protected function buildCombinedAssets( $assetGroups, $minify = true )
	{
		foreach( $assetGroups as $saveAs => $assets ) {
			$data = '';
			foreach( $assets as $asset ) {
				$data .= file_get_contents( public_path( ltrim( $asset, '/' ) ) );
			}
			if( $saveAs === 'javascripts.js' ) {

				if( $minify ) {
					$minifier = new \MatthiasMullie\Minify\JS;
					$minifier->add( $data );
					$data = $minifier->minify();
				}

				file_put_contents( public_path( $saveAs ), $data );

			} else {

				if( $minify ) {
					$minifier = new \MatthiasMullie\Minify\CSS;
					$minifier->add( $data );
					$data = $minifier->minify();
				}

				file_put_contents( public_path( $saveAs ), $data );
			}
		}
	}

	/**
	 * [generateTemplate description]
	 * @return [type] [description]
	 */
	private function generateTemplate()
	{

		if ( \App::environment( 'local' ) ) {
			$js = array_merge( $this->getJavascriptFiles(), $this->getModuleJsFiles() );
			$css = array_merge( $this->getStyleFiles(), $this->getModuleCssFiles() );
		} else {
			$this->buildCombinedAssets([
				'javascripts.js' => $this->getModuleJsFiles(),
				'stylesheets.css' => $this->getModuleCssFiles()
			]);
			$js = array_merge( $this->getJavascriptFiles(), ['/javascripts.js'] );
			$css = array_merge( $this->getStyleFiles(), ['/stylesheets.css'] );
		}
		file_put_contents( $this->writePath . 'loader.blade.php', self::assetLoaderTemplate( $js, $css ) );
	}

	/**
	 * [getModuleJsFiles description]
	 * @return [type] [description]
	 */
	protected function getModuleJsFiles()
	{
		$files = array();
		if( file_exists( public_path( 'package' ) ) ) {
			$it = new RecursiveDirectoryIterator( public_path( 'package' ) );
			foreach( new RecursiveIteratorIterator( $it ) as $file ) {
				if( $file->getExtension() === 'js' ) {
					$files[] = str_replace( public_path(), '', $file->getPathname() );
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
		if( file_exists( public_path( 'package' ) ) ) {
			$it = new RecursiveDirectoryIterator( public_path( 'package' ) );
			foreach( new RecursiveIteratorIterator( $it ) as $file ) {
				if( $file->getExtension() === 'css' ) {
					$files[] = str_replace( public_path(), '', $file->getPathname() );
				}
			}
		}
		return $files;
	}

	/**
	 * [getJavascriptFiles description]
	 * @return [type] [description]
	 */
	private function getJavascriptFiles() {
		$files = [];
		foreach( $this->ordered as $data ) {
			$bower = $data['bower'];
			$files = array_merge( $files, $bower->getJavascriptFiles() );
		}
		return $files;
	}

	/**
	 * [getStyleFiles description]
	 * @return [type] [description]
	 */
	private function getStyleFiles() {
		$files = [];
		foreach( $this->ordered as $data ) {
			$bower = $data['bower'];
			$files = array_merge( $files, $bower->getStyleFiles() );
		}
		return $files;
	}

	/**
	 * Get the console command arguments.
	 *
	 * @return array
	 */
	protected function getArguments()
	{
		return [];
		return [
			['example', InputArgument::REQUIRED, 'An example argument.'],
		];
	}

	/**
	 * Get the console command options.
	 *
	 * @return array
	 */
	protected function getOptions()
	{
		return [];
		return [
			['example', null, InputOption::VALUE_OPTIONAL, 'An example option.', null],
		];
	}

}
