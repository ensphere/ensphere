<?php namespace EnsphereCore\Commands\Ensphere\Bower;

class Bower {

	/**
	 * [$bower description]
	 * @var null
	 */
	private $bower = null;

	/**
	 * [$basePath description]
	 * @var null
	 */
	private $basePath = null;

	/**
	 * [$uri description]
	 * @var null
	 */
	private $uri = null;

	/**
	 * [$dependencies description]
	 * @var array
	 */
	private $dependencies = array();

	/**
	 * [$name description]
	 * @var null
	 */
	private $name = null;

	/**
	 * [$files description]
	 * @var array
	 */
	private $files = array();

	/**
	 * [__construct description]
	 * @param [type] $path [description]
	 */
	public function __construct( $name, $packageData ) {
		$this->name = $name;
		$this->dependencies = isset( $packageData->dependencies ) ? $packageData->dependencies : array();
		$this->files = isset( $packageData->files ) ? $packageData->files : array();
		$this->basePath = public_path("vendor/{$name}/");
		$this->uri = str_replace( public_path(), '', $this->basePath );
	}

	/**
	 * [name description]
	 * @return [type] [description]
	 */
	public function name() {
		return $this->name;
	}

	/**
	 * [getDependencies description]
	 * @return [type] [description]
	 */
	public function getDependencies() {
		return $this->dependencies;
	}

	/**
	 * [getJavascriptFiles description]
	 * @return [type] [description]
	 */
	public function getJavascriptFiles() {
		$javascripts = [];
		foreach( $this->files as $file ) {
			if( preg_match( "#\.js$#is", $file ) ) $javascripts[] = $this->uri . $file;
		}
		return $javascripts;
	}

	/**
	 * [getStyleFiles description]
	 * @return [type] [description]
	 */
	public function getStyleFiles() {
		$styles = [];
		foreach( $this->files as $file ) {
			if( preg_match( "#\.css$#is", $file ) ) $styles[] = $this->uri . $file;
		}
		return $styles;
	}

}