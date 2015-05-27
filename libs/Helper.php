<?php namespace Libs;

class Helper {

	/**
	 * [isModule description]
	 * @return boolean [description]
	 */
	public static function isModule() {
		return file_exists( __DIR__ . "/../../../../../vendor" );
	}

}