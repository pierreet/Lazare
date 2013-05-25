<?php
/*
 *
 * Template Name: formulaire-inscription
 */
 
 get_header(); // Inclut votre header
 global $post;
 
 $page_id = $post->ID;
?>
<div id="primary">
	<div role="main" id="content">		
	<form name="registerform" id="registerform" action="http://www.charlesdupoiron.com/lazare/wp-login.php?action=register" method="post">
		<p>
			<label for="user_login">Identifiant<br />
			<input type="text" name="user_login" id="user_login" class="input" value="" size="20" tabindex="10" /></label>
		</p>
		<p>
			<label for="user_email">E-mail<br />
			<input type="email" name="user_email" id="user_email" class="input" value="" size="25" tabindex="20" /></label>
		</p>
		<input type="hidden" name="cimy_post" value="1" />
		<p id="cimy_uef_p_field_1">
			<label for="cimy_uef_1">Ville</label><input type="text" name="cimy_uef_VILLE" id="cimy_uef_1" class="cimy_uef_input_27" value="" maxlength="30" tabindex="21" />
		</p>
		<p id="cimy_uef_p_field_2">
			<label for="cimy_uef_2">Code Postal</label><input type="text" name="cimy_uef_CODE_POSTAL" id="cimy_uef_2" class="cimy_uef_input_27" value="" maxlength="30" tabindex="22" />
		</p>
		<p id="cimy_uef_p_field_3">
			<label for="cimy_uef_3">Date de naissance</label><input type="text" name="cimy_uef_DATE_DE_NAISSANCE" id="cimy_uef_3" class="cimy_uef_input_27" value="" maxlength="10" tabindex="23" />
		</p>
		<br />
		<p id="reg_passmail">Un mot de passe vous sera envoyé sur votre adresse de messagerie.</p>
		<br class="clear" />
		<input type="hidden" name="redirect_to" value="" />
		<p class="submit"><input type="submit" name="wp-submit" id="wp-submit" class="button-primary" value="Inscription" tabindex="100" /></p>
	</form>
		<?php $page_data = get_page( $page_id ); ?> 
		<?php echo apply_filters('the_content', $page_data->post_content);?>
	</div>
</div>
<?php get_footer(); ?>