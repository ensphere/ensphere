<?php namespace EnsphereCore\Libs\HttpKernel;

class Generate {

	/**
	 * [middleware description]
	 * @param  array  $middleware [description]
	 * @return [type]             [description]
	 */
	public static function middleware( $laravelMiddleware, $middleware = array() ) {
		$packageMiddleware = array();
		$path = base_path('config/packages.json');
		if( file_exists( $path ) ) {
			$data = json_decode( file_get_contents( $path ) );
			if( isset( $data->middleware ) ) {
				$packageMiddleware = (array)$data->middleware;
			}
		}
		return array_merge( $laravelMiddleware, $middleware, $packageMiddleware );
	}


	/**
	 * [routeMiddleware description]
	 * @param  array  $routeMiddleware [description]
	 * @return [type]                  [description]
	 */
	public static function routeMiddleware( $routeMiddleware = array() ) {
		$packageRouteMiddleware = array();
		$path = base_path('config/packages.json');
		if( file_exists( $path ) ) {
			$data = json_decode( file_get_contents( $path ) );
			if( isset( $data->routeMiddleware ) ) {
				$packageRouteMiddleware = (array)$data->routeMiddleware;
			}
		}
		//dd(array_merge( $packageRouteMiddleware, $routeMiddleware ));
		return array_merge( $packageRouteMiddleware, $routeMiddleware );
	}

	/**
	 * [middlewareGroups description]
	 * @param  array  $middlewareGroups [description]
	 * @return [type]                   [description]
	 */
	public static function middlewareGroups( $middlewareGroups = array() ) {
		$packageMiddlewareGroups = array();
		$path = base_path('config/packages.json');
		if( file_exists( $path ) ) {
			$data = json_decode( file_get_contents( $path ) );
			if( isset( $data->middlewareGroups ) ) {
				$packageMiddlewareGroups = (array)$data->middlewareGroups;
			}
		}
		foreach( $packageMiddlewareGroups as $groupName => $middlewareArray ) {
			if( ! isset( $middlewareGroups[$groupName] ) ) {
				$middlewareGroups[$groupName] = [];
			}
			$middlewareGroups[$groupName] = array_unique( array_merge( $middlewareGroups[$groupName], $middlewareArray ) );
		}
		return $middlewareGroups;
	}

}