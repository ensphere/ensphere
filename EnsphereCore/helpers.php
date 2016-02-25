<?php

if( ! defined( 'is_module' ) ) {
	function is_module( $dir )
	{
		return file_exists( $dir . "/../../../../../vendor" );
	}
}
