<?php
### supporting WP2.6 wp-load & custom wp-content / plugin dir
if ( file_exists('../../abspath.php') )
	include_once('../../abspath.php');
else
	$abspath='../../../../../';

if ( file_exists( $abspath . 'wp-load.php') )
	require_once( $abspath . 'wp-load.php' );
else
	require_once( $abspath . 'wp-config.php' );
?>

<form method="post">

	<label for="cf_edit_label"><?php _e('Nom du fieldset ( seulement requis pour les <em>nouveau fieldset</em> )', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label" name="cf_edit_label" value="">

</form>