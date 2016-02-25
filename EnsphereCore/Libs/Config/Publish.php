<?php namespace EnsphereCore\Libs\Config;

use DirectoryIterator;

class Publish {

	/**
	 * [bower description]
	 * @param  array  $publish [description]
	 * @return [type]          [description]
	 */
	public static function bower( array $publish = array(), $providerPath )
	{
		foreach( new DirectoryIterator( $providerPath . '/../../public/vendor/' ) as $folderInfo )
		{
			if( $folderInfo->isDot() || ! $folderInfo->isDir() ) continue;
			$publish[$providerPath . '/../../public/vendor/' . $folderInfo->getFilename()] = base_path( 'public/vendor/' . $folderInfo->getFilename() );
		}
		return $publish;
	}

}