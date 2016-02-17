<?php namespace Libs\Config;

use App;

class Database {

	/**
	 * [mySQLconnecttion description]
	 * @param  array  $array [description]
	 * @return [type]        [description]
	 */
	public static function mySQLconnection( $array ) {
		if ( env( 'APP_ENV' ) === 'local' && ! is_null( $socket = env( 'DB_SOCKET' ) ) ) {
			$array['socket'] = $socket;
		}
		return $array;
	}

}