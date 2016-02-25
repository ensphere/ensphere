<?php namespace EnsphereCore\Libs\Config;

use App;

class Database {

	/**
	 * If on local environment, DB_SOCKET is an available .env option, if we can find a MAMP socket, that's the default
	 * @param  array  $array [description]
	 * @return [type]        [description]
	 */
	public static function mySQLconnection( $array ) {
		if ( env( 'APP_ENV' ) === 'local' ) {
 			$path = '/Applications/MAMP/tmp/mysql/mysql.sock';
 			$mampSocket = ( file_exists( $path ) ) ? $path : '';
 			$array['unix_socket'] = env( 'DB_SOCKET', $mampSocket );
  		}
		return $array;
	}

}
