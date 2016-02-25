<?php namespace EnsphereCore\Commands\Ensphere\Traits;

trait Module {

	/**
	 * [getCurrentVendorAndModuleName description]
	 * @return [type] [description]
	 */
	private function getCurrentVendorAndModuleName()
	{
		$composerDotJson = json_decode( file_get_contents( base_path('composer.json') ) );
		$psr4Autoload = $composerDotJson->autoload->{'psr-4'};
		$name = null;
		foreach( $psr4Autoload as $nameSpace => $folder ) {
			if( $folder == 'app/' ) {
				$name = $nameSpace;
				break;
			}
		}
		if( is_null( $name ) ) {
			$this->error('Could not find namespace from composer.json');
		}
		if( ! preg_match( "#^([a-z0-9]+)\\\([a-z0-9]+)\\\\$#is", $name, $match ) ) {
			$this->error('Could not find namespace from composer.json');
		}
		return [
			'vendor' => $this->getComputerNameFromCamelCase($match[1]),
			'module' => $this->getComputerNameFromCamelCase($match[2]),
			'camelCasedVendor' => $match[1],
			'camelCasedModule' => $match[2]
		];
	}

	/**
	 * [getComputerNameFromCamelCase description]
	 * @param  [type] $string [description]
	 * @return [type]         [description]
	 */
	private function getComputerNameFromCamelCase( $string )
	{
		return strtolower( implode( "-", array_filter( preg_split( "/(?=[A-Z])/", $string ) ) ) );
	}

}