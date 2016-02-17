<?php namespace Libs\Config;

use App;

class Database {

	/**
	 * [mySQLconnecttion description]
	 * @param  array  $array [description]
	 * @return [type]        [description]
	 */
	public static function mySQLconnecttion( array $array ) {
		if ( App::environment( 'local' ) && $socket = env( 'DB_SOCKET' ) ) {
			$array['socket'] = $socket;
		}
		return $array;
	}


}