<?php namespace Ensphere\Ensphere\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Foundation\Application;

class Kernel extends HttpKernel {

	/**
	 * $middleware
	 * ONLY DEFINE MIDDLEWARE IF THIS IS AN APPLICATION.
	 * IF A MODULE, SET IN THE REGISTRATION.JSON FILE
	 * @var [type]
	 */
	protected $middleware = [

	];

	/**
	 * $routeMiddleware
	 * ONLY DEFINE MIDDLEWARE IF THIS IS AN APPLICATION.
	 * IF A MODULE, SET IN THE REGISTRATION.JSON FILE
	 * @var [type]
	 */
	protected $routeMiddleware = [

	];

	/**
	 * $middlewareGroups
	 * ONLY DEFINE MIDDLEWARE IF THIS IS AN APPLICATION.
	 * IF A MODULE, SET IN THE REGISTRATION.JSON FILE
	 * @var [type]
	 */
	protected $middlewareGroups = [

    ];

	/**
	 * [__construct description]
	 * @param Application $app    [description]
	 * @param Router      $router [description]
	 */
	public function __construct( Application $app, Router $router ) {
		$this->routeMiddleware = \Libs\HttpKernel\Generate::routeMiddleware( $this->routeMiddleware );
		$this->middleware = \Libs\HttpKernel\Generate::middleware( [], $this->middleware );
		$this->middlewareGroups = \Libs\HttpKernel\Generate::middlewareGroups( $this->middlewareGroups );
		parent::__construct( $app, $router );
	}

}
