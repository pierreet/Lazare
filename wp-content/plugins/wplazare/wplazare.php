<?php
/*
Plugin Name: wplazare
Description: This plugin allows to manage lazare needs
Version: 1.0
Author: Charles Dupoiron
Author URI: http://www.charlesdupoiron.com
*/

/**
* Plugin main file.
* 
*	This file is the main file called by wordpress for our plugin use. It define the basic vars and include the different file needed to use the plugin
* @author Charles Dupoiron <contact@charlesdupoiron.com>
* @version 1.0
* @package wp-lazare
*/

/**
*	First thing we define the main directory for our plugin in a super global var	
*/
DEFINE('WPLAZARE_PLUGIN_DIR', basename(dirname(__FILE__)));
/**
*	Include the different config for the plugin	
*/
require_once(WP_PLUGIN_DIR . '/' . WPLAZARE_PLUGIN_DIR . '/includes/configs/config.php' );
/**
*	Define the path where to get the config file for the plugin
*/
DEFINE('WPLAZARE_CONFIG_FILE', WPLAZARE_INC_PLUGIN_DIR . 'configs/config.php');
/**
*	Include the file which includes the different files used by all the plugin
*/
require_once(	WPLAZARE_INC_PLUGIN_DIR . 'includes.php' );

/*	Create an instance for the database option	*/
$wplazare_db_option = new wplazare_db_option();

/**
*	Include tools that will launch different action when plugin will be loaded
*/
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'install.class.php' );
/**
*	On plugin loading, call the different element for creation output for our plugin	
*/
register_activation_hook( __FILE__ , array('wplazare_install', 'wplazare_activate') );
register_deactivation_hook( __FILE__ , array('wplazare_install', 'wplazare_deactivate') );

/**
*	Include tools that will launch different action when plugin will be loaded
*/
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'init.class.php' );

if ( !function_exists('wp_new_user_notification') ) :
/**
 * Notify the blog admin of a new user, normally via email.
 *
 * @since 2.0
 *
 * @param int $user_id User ID
 * @param string $plaintext_pass Optional. The user's plaintext password
 */
function wp_new_user_notification($user_id, $plaintext_pass = '') {
	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$headers = sprintf("From: %s <%s>\r\n\\", $blogname, $from_email);
	
	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";
	$message .= 'Voir les Utilisateurs: '.admin_url('admin.php?page=wplazare_users&role=postulant'). "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message, $headers);

	if ( empty($plaintext_pass) )
		return;

	$message  = sprintf(__('Username: %s'), $user_login) . "\r\n";
	$message .= sprintf(__('Password: %s'), $plaintext_pass) . "\r\n";
	$message .= wp_login_url() . "\r\n";

	wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message);

}
endif;


/**
*	On plugin loading, call the different element for creation output for our plugin	
*/
add_action('plugins_loaded', array('wplazare_init', 'wplazare_plugin_load'));
add_action('wp_login', array('wplazare_tools', 'lastLogin'));
add_action('delete_user', array('wplazare_tools', 'unSubscribeNewsletter') );
add_action('edit_user_profile_update', array('wplazare_tools', 'my_profile_update'));
add_action('plugins_loaded', array('wplazare_tools', 'synchroList'));


add_shortcode('wplazare_payment_return', array('wplazare_orders', 'paymentReturn'));
add_shortcode('wplazare_charge_payment_return', array('wplazare_orders', 'chargePaymentReturn'));
add_shortcode('wplazare_payment_form', array('wplazare_payment_form', 'displayForm'));
add_shortcode('wplazare_events_list', array('wplazare_events', 'displayEvents'));
add_shortcode('wplazare_locataires_list', array('wplazare_payment_form', 'chooseLocation'));
add_shortcode('wplazare_orienter', array('wplazare_users', 'newAccompagnateur'));