<?php
/**
 * Plugin Loader
 *
 * Define the different element usefull for the plugin usage. The menus, includes script, start launch script, css, translations
 * @author Charles Dupoiron <contact@charlesdupoiron.com>
 * @version 1.0
 * @package wp-lazare
 * @subpackage librairies
 */

/**
 * Define the different element usefull for the plugin usage. The menus, includes script, start launch script, css, translations
 * @package wp-lazare
 * @subpackage librairies
 */
class wplazare_init
{

	/**
	 *	Load the different element need to create the plugin environnement
	 */
	function wplazare_plugin_load()
	{
		global $wplazare_db_option;
		/*	Call function to create the main left menu	*/
		add_action('admin_menu', array('wplazare_init', 'wplazare_menu') );

		/*	Get the current language to translate the different text in plugin	*/
		$locale = get_locale();
		$moFile = WPLAZARE_INC_PLUGIN_DIR . 'languages/wplazare-' . $locale . '.mo';
		if( !empty($locale) && (is_file($moFile)) )
		{
			load_textdomain('wplazare', $moFile);
		}

		/*	Do the update on the database	*/
		wplazare_database::wplazare_db_update();

		/*	Check the last optimisation date if it was not perform today weoptimise the database	*/
		if($wplazare_db_option->get_db_optimisation_date() != date('Y-m-d'))
		{
			wplazare_database::wplazare_db_optimisation();

			$wplazare_db_option->set_db_optimisation_date(date('Y-m-d'));
			$wplazare_db_option->set_db_option();
		}

		/*	Include the different css	*/
		add_action('init', array('wplazare_init', 'wplazare_front_css') );
		/*	Include the different css	*/
		add_action('init', array('wplazare_init', 'wplazare_front_js') );
		/*	Include the different css	*/
		add_action('admin_init', array('wplazare_init', 'wplazare_admin_css') );
		/*	Include the different js	*/
		add_action('admin_init', array('wplazare_init', 'wplazare_admin_js') );
	}

	/**
	 *	Create the main left menu with different parts
	 */
	function wplazare_menu()
	{

		$order_count = wplazare_orders::newOrdersCount(wplazare_tools::getLastLogin());
		$order_count = wplazare_orders::newOrdersCount(wplazare_tools::getLastLogin());

        $filters = array();
        $filters['payment_type'] = "multiple_payment";
        $m_order_count = wplazare_orders::newOrdersCount(wplazare_tools::getLastLogin(), $filters);

		/*	Add the options menu in the options section	*/
		add_options_page(__('Options principale du module de paiement paybox', 'wplazare'), __('Paybox', 'wplazare'), 'wplazare_manage_config', WPLAZARE_OPTION_MENU, array('wplazare_option', 'doOptionsPage'));

		/*	Add the options menu in the options section	*/
		add_options_page(__('Options principale du module Lazare', 'wplazare'), __('Lazare', 'wplazare'), 'wplazare_manage_config', WPLAZARE_CONFIG_MENU, array('wplazare_config', 'doOptionsPage'));

		/*	Main menu */
		add_menu_page('LAZARE', 'Lazare <span class="update-plugins count-' . $user_count . '"><span class="plugin-count">' . $user_count . '</span></span>', 'wplazare_view_user', WPLAZARE_URL_SLUG_ORDERS_LISTING, array('wplazare_display', 'displayPage'));

		/*	Redefine the dashboard page	*/
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_orders::pageTitle(), __('Dons / Charges', 'wplazare' ).' <span class="update-plugins count-' . $order_count . '"><span class="plugin-count">' . $user_count . '</span></span>', 'wplazare_view_orders', WPLAZARE_URL_SLUG_ORDERS_LISTING, array('wplazare_display','displayPage'));
        add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_orders::pageTitle(), __('Prélèvements', 'wplazare' ).' <span class="update-plugins count-' . $m_order_count . '"><span class="plugin-count">' . $user_count . '</span></span>', 'wplazare_view_orders', WPLAZARE_URL_SLUG_M_ORDERS_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_users::pageTitle(), 'Utilisateurs <span class="update-plugins count-' . $user_count . '"><span class="plugin-count">' . $user_count . '</span></span>', 'wplazare_view_user', WPLAZARE_URL_SLUG_USERS_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_apparts::pageTitle(), 'Appartements', 'wplazare_view_appart', WPLAZARE_URL_SLUG_APPARTS_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_maisons::pageTitle(), 'Maisons', 'wplazare_view_appart', WPLAZARE_URL_SLUG_MAISONS_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_associations::pageTitle(), 'Associations', 'wplazare_view_association', WPLAZARE_URL_SLUG_ASSOCIATIONS_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_loyers::pageTitle(), 'Loyers', 'wplazare_view_loyer', WPLAZARE_URL_SLUG_LOYERS_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_locations::pageTitle(), 'Locations', 'wplazare_view_loyer', WPLAZARE_URL_SLUG_LOCATIONS_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_historique::pageTitle(), 'Historique', 'wplazare_view_historique', WPLAZARE_URL_SLUG_HISTORIQUE_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_stats::pageTitle(), 'Statistiques', 'wplazare_view_statistiques', WPLAZARE_URL_SLUG_STATS_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_payment_form::pageTitle(), __('Formulaires', 'wplazare' ), 'wplazare_view_forms', WPLAZARE_URL_SLUG_FORMS_LISTING, array('wplazare_display','displayPage'));
		add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_offers::pageTitle(), __('Offres', 'wplazare' ), 'wplazare_view_offers', WPLAZARE_URL_SLUG_OFFERS_LISTING, array('wplazare_display','displayPage'));
        add_submenu_page( WPLAZARE_URL_SLUG_ORDERS_LISTING, wplazare_documents::pageTitle(), "Attestation assurance", 'wplazare_view_offers', WPLAZARE_URL_SLUG_DOCUMENTS_LISTING, array('wplazare_display','displayPage'));
	}

	/**
	 *	Define the javascript to include in each page
	 */
	function wplazare_admin_js()
	{
		if(!wp_script_is('jquery-ui-datepicker', 'queue')){
			wp_enqueue_script('jquery-ui-datepicker','jquery-ui-core');
		}
		if(!wp_script_is('jquery-validate', 'queue'))
		wp_enqueue_script('jquery-validate', WPLAZARE_JS_URL . 'jquery.validate.min.js', array('jquery'));
		if(!wp_script_is('jquery-tablesorter', 'queue'))
		wp_enqueue_script('jquery-tablesorter', WPLAZARE_JS_URL . 'jquery.tablesorter.min.js', array('jquery'));
		if(!wp_script_is('jquery-tablesorter-pager', 'queue'))
		wp_enqueue_script('jquery-tablesorter-pager', WPLAZARE_JS_URL . 'jquery.tablesorter.pager.min.js', array('jquery-tablesorter'));
		if(!wp_script_is('jquery-tablesorter-widgets', 'queue'))
		wp_enqueue_script('jquery-tablesorter-widgets', WPLAZARE_JS_URL . 'jquery.tablesorter.widgets.min.js', array('jquery-tablesorter'));
		
		wp_enqueue_script('wplazare_main_js', WPLAZARE_JS_URL . 'wplazare.js');
	}

	/**
	 *	Define the javascript to include in each page
	 */
	function wplazare_front_js(){
		if(!wp_script_is('jquery', 'queue')){
			wp_enqueue_script('jquery');
		}
		if(!wp_script_is('jquery-ui-core', 'queue')){
			wp_enqueue_script('jquery-ui-core');
		}
		if(!wp_script_is('jquery-ui-widget', 'queue')){
			wp_enqueue_script('jquery-ui-widget');
		}
		wp_enqueue_script('wplazare_main_js', WPLAZARE_JS_URL . 'wplazare.js');
		wp_enqueue_script( 'jquery.bgiframe' );
		wp_enqueue_script( 'jquery.bgiframe', get_theme_root_uri().'/lazare/js/jquery.bgiframe-2.1.2.js');
		if(!wp_script_is('jquery-combobox', 'queue'))
			wp_enqueue_script('jquery-combobox', WPLAZARE_JS_URL . 'jquery.combobox.js',array('jquery-ui-widget','jquery-ui-autocomplete','jquery-ui-button'));
	}

	/**
	 *	Define the css to include in each page
	 */
	function wplazare_admin_css()
	{
		wp_enqueue_style('jquery-ui');
		wp_register_style('wplazare_main_css', WPLAZARE_CSS_URL . 'wplazare.css');
		wp_enqueue_style('wplazare_main_css');
		wp_register_style('jquery-tablesorter-theme-blue', WPLAZARE_CSS_URL . 'theme.blue.css');
		wp_enqueue_style('jquery-tablesorter-theme-blue');
		wp_register_style('jquery-tablesorter-pager', WPLAZARE_CSS_URL . 'jquery.tablesorter.pager.css');
		wp_enqueue_style('jquery-tablesorter-pager');
	}

	/**
	 *	Define the css to include in frontend
	 */
	function wplazare_front_css()
	{
		wp_register_style('wplazare-jquery-ui', WPLAZARE_CSS_URL . 'jquery-ui.css');
		wp_enqueue_style('wplazare-jquery-ui');
		wp_register_style('wplazare_front_main_css', WPLAZARE_CSS_URL . 'wplazare_front.css');
		wp_enqueue_style('wplazare_front_main_css');
	}
}