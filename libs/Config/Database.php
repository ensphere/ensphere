<?php namespace Libs\Config;

use App;

class Database {

	/**
	 * If on local environment, if we can find a MAMP socket, that's the default db socket
	 * @param  array  $array [description]
	 * @return [type]        [description]
	 */
	public static function mySQLconnection( $array ) {
		$mampSocket = '';
		if ( env( 'APP_ENV' ) === 'local' ) {
 			$path = '/Applications/MAMP/tmp/mysql/mysql.sock';
 			if( file_exists( $path ) ) $mampSocket = $path;
  		}
  		$array['unix_socket'] = env( 'DB_SOCKET', $mampSocket );
		return $array;
	}

}
