
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

	<label for="cf_edit_label"><?php _e('Label', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label" name="cf_edit_label" value="">

	<label for="cf_edit_default"><?php _e('Valeur par défaut', 'cforms'); ?></label>
	<input type="text" id="cf_edit_default" name="cf_edit_default" value="">

	<label for="cf_edit_regexp"><?php echo sprintf(__('Expression régulière pour la validation (e.g. %s). Regardez l\'Aide pour des exemples.', 'cforms'),'^[A-Za-z ]+$'); ?></label>
	<input type="text" id="cf_edit_regexp" name="cf_edit_regexp" value="">

	<label for="cf_edit_title"><?php _e('Titre du champ (Affiché quand la souris passe sur le champ)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">

	<label for="cf_edit_customerr"><?php _e('Message d\'erreur (il faut activer les messages d\'erreurs pour les champs!)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value="">

</form>