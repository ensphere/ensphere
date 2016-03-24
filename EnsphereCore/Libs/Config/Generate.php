<?php namespace EnsphereCore\Libs\Config;

class Generate {

	/**
	 * [providers description]
	 * @param  [type] $laravelProviders [description]
	 * @param  array  $appProviders     [description]
	 * @return [type]                   [description]
	 */
	public static function providers( $laravelProviders, $appProviders = array() ) {
		$packageProviders = array();
		$path = base_path('config/packages.json');
		if( file_exists( $path ) ) {
			$data = json_decode( file_get_contents( $path ) );
			if( isset( $data->providers ) ) {
				$packageProviders = $data->providers;
			}
		}
		$return = array_merge( $laravelProviders, $packageProviders, $appProviders );
		if ( strpos( php_sapi_name(), 'cli' ) !== false ) {
			foreach( $return as $key => $provider ) {
				if( ! class_exists( $provider ) ) {
					unset( $return[$key] );
				}
			}
		}
		return $return;
	}

	/**
	 * [aliases description]
	 * @param  array  $appAliases [description]
	 * @return [type]             [description]
	 */
	public static function aliases( $appAliases = array() ) {
		$packageAliases = array();
		$path = base_path('config/packages.json');
		if( file_exists( $path ) ) {
			$data = json_decode( file_get_contents( $path ) );
			if( isset( $data->aliases ) ) {
				$packageAliases = (array)$data->aliases;
			}
		}
		//dd(array_merge( $packageAliases, $appAliases ));
		return array_merge( $packageAliases, $appAliases );
	}

}