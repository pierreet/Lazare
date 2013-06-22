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

	<label for="cf_edit_label_group"><?php _e('Label', 'cforms'); ?></label>
	<input type="text" id="cf_edit_label_group" name="cf_edit_label_group" value="">

	<div class="cf_edit_groups_header">
		<span class="cf_option"><?php _e('Valeur Check box/radio box (affiché)', 'cforms'); ?></span>
		<span class="cf_optVal"><?php _e('Valeur optionelle (transmise)', 'cforms'); ?></span>
		<span class="cf_chked" title="<?php _e('Etat par défaut', 'cforms'); ?>"></span>
		<span class="cf_br" title="<?php _e('Retour chariot / Nouvelle ligne', 'cforms'); ?>"></span>
	</div>

	<div id="cf_edit_groups">
	</div>
	<div class="add_group_item"><a href="#" id="add_group_button" class="cf_edit_plus"></a></div>

	<label style="clear:left; padding-top:5px;" for="cf_edit_title"><?php _e('Titre du champ (Affiché quand la souris passe sur le champ)', 'cforms'); ?></label>
	<input type="text" id="cf_edit_title" name="cf_edit_title" value="">

	<!--label for="cf_edit_customerr"><?php _e('Message d\'erreur (il faut activer les messages d\'erreurs pour les champs!)', 'cforms'); ?></label-->
	<!--input type="text" id="cf_edit_customerr" name="cf_edit_customerr" value=""-->

</form>