<?php
/**
 * Plugin Installer
 * 
 * Define the different action when activate the plugin. Create the different element as option and database, set the users' permissions
 * @author Charles Dupoiron <contact@charlesdupoiron.com>
 * @version 1.0
 * @package wp-lazare
 * @subpackage librairies
 */

/**
 * Define the different action when activate the plugin. Create the different element as option and database, set the users' permissions
 * @package wp-lazare
 * @subpackage librairies
 */
class wplazare_install
{

	/**
	*	Define actions lauched after plugin activation. Create the database, create the different option, call the permisssion setters
	*/
	function wplazare_activate()
	{
		global $wplazare_db_option;
	
		/*	Create an instance for the database option	*/
		$wplazare_db_option = new wplazare_db_option();
		$currentDBVersion = $wplazare_db_option->get_db_version();
		if(!($currentDBVersion > 0))
		{
			$wplazare_db_option->create_db_option();
		}

		/*	Create 	*/
		wplazare_database::wplazare_db_creation();

		/*	Create the option for the store option	*/
		$wplazare_store_mainoption = get_option('wplazare_store_mainoption');
		if($wplazare_store_mainoption == '')
		{
			unset($optionList);$optionList = array();
			$optionList['storeTpe'] = $testEnvironnement['tpe'];
			$optionList['storeRang'] = $testEnvironnement['rang'];
			$optionList['storeIdentifier'] = $testEnvironnement['identifier'];
			$optionList['environnement'] = 'test';
			wplazare_option::saveStoreConfiguration('wplazare_store_mainoption', $optionList, false);
		}

		/*	Create the option for the return url	*/
		$wplazare_store_urloption = get_option('wplazare_store_urloption');
		if($wplazare_store_urloption == '')
		{
			unset($optionList);$optionList = array();
			$optionList['urlSuccess'] = get_bloginfo('siteurl') . '/';
			$optionList['urlDeclined'] = get_bloginfo('siteurl') . '/';
			$optionList['urlCanceled'] = get_bloginfo('siteurl') . '/';
			wplazare_option::saveStoreConfiguration('wplazare_store_urloption', $optionList, false);
		}

		/*	Set the different permissions	*/
		wplazare_install::wplazare_set_permissions();
	}

	/**
	*	Define actions launched when plugin is deactivate.
	*/
	function wplazare_deactivate()
	{
		global $wpdb;

		// $wpdb->query("DROP TABLE " . WPAYBOX_DBT_ORDERS . ", " . WPAYBOX_DBT_FORMS . ", " . WPAYBOX_DBT_OFFERS . ", " . WPAYBOX_DBT_LINK_FORMS_OFFERS . ";");
		// delete_option('wplazare_store_urloption');
		// delete_option('wplazare_store_urloption');
		// delete_option('wplazare_db_option');
	}

	/**
	*	Define the different permissions affected to users.
	*/
	function wplazare_set_permissions()
	{
		$wplazare_permission_list = array();
		$wplazare_permission_list[] = 'wplazare_manage_config';

		$wplazare_permission_list[] = 'wplazare_view_user';
		$wplazare_permission_list[] = 'wplazare_view_user_details';
		$wplazare_permission_list[] = 'wplazare_add_user';
		$wplazare_permission_list[] = 'wplazare_edit_user';
		$wplazare_permission_list[] = 'wplazare_edit_user_details';
		
		$wplazare_permission_list[] = 'wplazare_view_user_fiches';
		$wplazare_permission_list[] = 'wplazare_edit_user_fiches';
		
		$wplazare_permission_list[] = 'wplazare_view_appart';
		$wplazare_permission_list[] = 'wplazare_view_appart_details';
		$wplazare_permission_list[] = 'wplazare_add_appart';
		$wplazare_permission_list[] = 'wplazare_edit_appart';
		$wplazare_permission_list[] = 'wplazare_edit_appart_details';
		
		$wplazare_permission_list[] = 'wplazare_view_association';
		$wplazare_permission_list[] = 'wplazare_edit_association';
		
		$wplazare_permission_list[] = 'wplazare_view_historique';
		$wplazare_permission_list[] = 'wplazare_edit_historique';
		
		$wplazare_permission_list[] = 'wplazare_view_loyer';
		$wplazare_permission_list[] = 'wplazare_edit_loyer';
		
		$wplazare_permission_list[] = 'wplazare_view_config';
		$wplazare_permission_list[] = 'wplazare_edit_config';
		
		$wplazare_permission_list[] = 'wplazare_view_statistiques';
		
		$wplazare_permission_list[] = 'wplazare_view_orders';
		$wplazare_permission_list[] = 'wplazare_view_orders_details';
		$wplazare_permission_list[] = 'wplazare_delete_orders';

		$wplazare_permission_list[] = 'wplazare_view_forms';
		$wplazare_permission_list[] = 'wplazare_view_forms_details';
		$wplazare_permission_list[] = 'wplazare_add_forms';
		$wplazare_permission_list[] = 'wplazare_edit_forms';
		$wplazare_permission_list[] = 'wplazare_delete_forms';

		$wplazare_permission_list[] = 'wplazare_view_forms_offers_link';
		$wplazare_permission_list[] = 'wplazare_delete_forms_offers_link';

		$wplazare_permission_list[] = 'wplazare_view_offers';
		$wplazare_permission_list[] = 'wplazare_view_offers_details';
		$wplazare_permission_list[] = 'wplazare_add_offers';
		$wplazare_permission_list[] = 'wplazare_edit_offers';
		$wplazare_permission_list[] = 'wplazare_delete_offers';

		/**
		*	Add capabilities to the administrator role
		*/
		$role = get_role('administrator');
		foreach($wplazare_permission_list as $permission)
		{
			if( ($role != null) && !$role->has_cap($permission) ) 
			{
				$role->add_cap($permission);
			}
		}
		unset($role);

		$wplazare_permission_list = array();

		$wplazare_permission_list[] = 'wplazare_view_user';
		$wplazare_permission_list[] = 'wplazare_view_user_details';
		$wplazare_permission_list[] = 'wplazare_add_user';
		$wplazare_permission_list[] = 'wplazare_edit_user';
		$wplazare_permission_list[] = 'wplazare_edit_user_details';
		
		$wplazare_permission_list[] = 'wplazare_view_appart';
		$wplazare_permission_list[] = 'wplazare_view_appart_details';
		$wplazare_permission_list[] = 'wplazare_add_appart';
		$wplazare_permission_list[] = 'wplazare_edit_appart';
		$wplazare_permission_list[] = 'wplazare_edit_appart_details';
		
		$wplazare_permission_list[] = 'wplazare_view_loyer';
		$wplazare_permission_list[] = 'wplazare_edit_loyer';
		/**
		*	Add capabilities to the editor role
		*/
		$role = get_role('editor');
		foreach($wplazare_permission_list as $permission)
		{
			if( ($role != null) && !$role->has_cap($permission) ) 
			{
				$role->add_cap($permission);
			}
		}
		unset($role);
	}

}