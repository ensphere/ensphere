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
	 * [__construct description]
	 * @param Application $app    [description]
	 * @param Router      $router [description]
	 */
	public function __construct( Application $app, Router $router ) {
		$this->routeMiddleware = \Libs\HttpKernel\Generate::routeMiddleware( $this->routeMiddleware );
		$this->middleware = \Libs\HttpKernel\Generate::middleware( [
			'Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode',
			'Illuminate\Cookie\Middleware\EncryptCookies',
			'Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse',
			'Illuminate\Session\Middleware\StartSession',
			'Illuminate\View\Middleware\ShareErrorsFromSession'
		],
			$this->middleware
		);
		parent::__construct( $app, $router );
	}

}
