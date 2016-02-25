<?php namespace EnsphereCore\Libs\Providers;

class Service {

	/**
	 * [contracts description]
	 * @param  array  $contracts [description]
	 * @return [type]            [description]
	 */
	public static function contracts( $contracts = array() )
	{
		$packageContracts = array();
		$path = base_path('config/packages.json');
		if( file_exists( $path ) ) {
			$data = json_decode( file_get_contents( $path ) );
			if( isset( $data->contracts ) ) {
				$packageContracts = (array)$data->contracts;
			}
		}
		return array_merge( $packageContracts, $contracts );
	}

}