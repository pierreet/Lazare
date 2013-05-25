<?php
define( 'HEADER_IMAGE_WIDTH', apply_filters( 'twentyten_header_image_width', 1000 ) );
define( 'HEADER_IMAGE_HEIGHT', apply_filters( 'twentyten_header_image_height', 500 ) );

/**
 *	Define the javascript to include in each page
 */
function charlesdupoiron_theme_front_js(){
	wp_enqueue_script( 'jquery.cycle', get_theme_root_uri().'/lazare/js/jquery.cycle.all.js');
	wp_enqueue_script( 'jquery.validate',get_theme_root_uri().'/lazare/js/jquery.validate.min.js');
	wp_enqueue_script( 'charlesdupoiron_theme',get_theme_root_uri().'/lazare/js/lazare.js');
}

add_action('init','charlesdupoiron_theme_front_js' );

function register_role($user_id, $password="", $meta=array()) {
   $userdata = array();
   $userdata['ID'] = $user_id;
   $userdata['role'] = $_POST['cimy_uef_USERTYPE'];
   if ($userdata['role'] == 'Devenir Accompagnateur') {
      $userdata['role'] = 'postulant';
   }
   if (preg_match("/Orienter/i", $userdata['role'])) {
      $userdata['role'] = 'orienter';
   }

   //only allow if user role is my_role
   if (($userdata['role'] == "postulant")){
      wp_update_user($userdata);
   }
}

add_action('user_register', 'register_role');

function logingk() {
	echo '<link rel="stylesheet" type="text/css" href="' . get_stylesheet_directory_uri() . '/login.css" />';
}
add_action('login_head', 'logingk');

function browser_body_class($classes = '') {
	$classes[] = 'one-column';
	return $classes;
}

add_filter('body_class','browser_body_class');

function __my_registration_redirect()
{
    return home_url( '/confirmation-inscription/' );
}
add_filter( 'registration_redirect', '__my_registration_redirect' );

function my_event_form(){
?>
	<input type="hidden" name="register_user" value="1"/>
	<p>
		<label for="cimy_uef_wp_1">Prénom</label>
		<input type="text" name="cimy_uef_wp_FIRSTNAME" id="cimy_uef_wp_1" class="required" <?php if(!empty($_REQUEST['cimy_uef_wp_FIRSTNAME'])) echo "value='{$_REQUEST['cimy_uef_wp_FIRSTNAME']}'"; ?> maxlength="100" tabindex="21">
	</p>
	<p>
		<label for="cimy_uef_wp_2">Nom</label>
		<input type="text" name="cimy_uef_wp_LASTNAME" id="cimy_uef_wp_2" <?php if(!empty($_REQUEST['cimy_uef_wp_LASTNAME'])) echo "value='{$_REQUEST['cimy_uef_wp_LASTNAME']}'"; ?> maxlength="100" tabindex="22">
	</p>
	<p>
		<label for="cimy_uef_6">Adresse</label>
		<input type="text" name="cimy_uef_ADRESSE" id="cimy_uef_6" <?php if(!empty($_REQUEST['cimy_uef_ADRESSE'])) echo "value='{$_REQUEST['cimy_uef_ADRESSE']}'"; ?> maxlength="150" tabindex="23">
	</p>
	<p>
		<label for="cimy_uef_2">Code Postal</label>
		<input type="text" name="cimy_uef_CODE_POSTAL" id="cimy_uef_2" <?php if(!empty($_REQUEST['cimy_uef_CODE_POSTAL'])) echo "value='{$_REQUEST['cimy_uef_CODE_POSTAL']}'"; ?> maxlength="30" tabindex="24">
	</p>
	<p>
		<label for="cimy_uef_1">Ville</label>
		<input type="text" name="cimy_uef_VILLE" id="cimy_uef_1" <?php if(!empty($_REQUEST['cimy_uef_VILLE'])) echo "value='{$_REQUEST['cimy_uef_VILLE']}'"; ?> maxlength="30" tabindex="25">
	</p>
	<p>
		<label for='dbem_phone'><?php _e('Phone','dbem') ?></label>
		<input type="text" name="dbem_phone" id="dbem_phone" <?php if(!empty($_REQUEST['dbem_phone'])) echo "value='{$_REQUEST['dbem_phone']}'"; ?> />
	</p>
	<p>
		<label for='user_email'><?php _e('E-mail','dbem') ?></label> 
		<input type="text" name="user_email" id="user_email"<?php if(!empty($_REQUEST['user_email'])) echo "value='{$_REQUEST['user_email']}'"; ?>  />
	</p>	
	<p>
		<label for="cimy_uef_3">Date de naissance</label>
		<input type="text" name="cimy_uef_DATE_DE_NAISSANCE" id="cimy_uef_3" <?php if(!empty($_REQUEST['cimy_uef_DATE_DE_NAISSANCE'])) echo "value='{$_REQUEST['cimy_uef_DATE_DE_NAISSANCE']}'";else echo "value='01/01/1970'"; ?> maxlength="10" tabindex="26">
	</p>				
	<p>
		<br/>
		<label for='booking_comment'><?php _e('Comment', 'dbem') ?></label>
		<textarea name='booking_comment'><?php echo !empty($_POST['booking_comment']) ? $_POST['booking_comment']:'' ?></textarea>
	</p>	
	<select name="cimy_uef_USERTYPE" id="cimy_uef_4" tabindex="28" style="display: none;">
		<option value="Devenir Accompagnateur">Devenir Accompagnateur</option>
		<option selected="selected" value=" Participer aux évènements de Lazare"> Participer aux évènements de Lazare</option>
	</select>
	
<?php 
}

add_action('em_booking_form_custom', 'my_event_form');

?>