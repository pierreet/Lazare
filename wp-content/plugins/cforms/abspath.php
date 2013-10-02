<?php 
		$abspath = dirname(__FILE__) . '/../../../';

	if ( file_exists( $abspath . 'wp-load.php') )
		require_once( $abspath . 'wp-load.php' );
	else
		require_once( $abspath . 'wp-config.php' );
		
		$abspath = addslashes(ABSPATH);
?>