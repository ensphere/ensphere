<?php namespace Ensphere\Ensphere\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;
use Illuminate\Routing\Router;
use Illuminate\Contracts\Foundation\Application;
use EnsphereCore\Libs\HttpKernel\Generate;

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
		'web' => [
			"Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse",
            "Illuminate\Session\Middleware\StartSession",
            "Illuminate\View\Middleware\ShareErrorsFromSession",
		],
		"api" : [
			"throttle:60,1"
		]
    ];

	/**
	 * [__construct description]
	 * @param Application $app    [description]
	 * @param Router      $router [description]
	 */
	public function __construct( Application $app, Router $router )
	{
		$this->routeMiddleware = Generate::routeMiddleware( $this->routeMiddleware );

		$this->middleware = Generate::middleware( [

			/** Application middleware */

			"Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode"

		], $this->middleware );

		$this->middlewareGroups = Generate::middlewareGroups( $this->middlewareGroups );

		parent::__construct( $app, $router );
	}

}
