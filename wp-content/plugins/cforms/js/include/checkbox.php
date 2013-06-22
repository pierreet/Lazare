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

	<label for="cf_edit_label_left"><?php _e('Label à gauche de la checkbox ...', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label_left" name="cf_edit_label_left" value="">

	<label for="cf_edit_label_right"><?php _e('... ou le label à droite de la checkbox', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label_right" name="cf_edit_label_right" value="">

	<label for="cf_edit_checked"><?php _e('Etat par défaut', 'cforms'); ?></label>
	<input type="checkbox" id="cf_edit_checked" name="cf_edit_checked" class="allchk chkBox">

	<label for="cf_edit_title"><?php _e('Titre du champ (Affiché quand la souris passe sur le champ)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">

	<label for="cf_edit_customerr"><?php _e('Message d\'erreur (il faut activer les messages d\'erreurs pour les champs!)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value="">

</form>