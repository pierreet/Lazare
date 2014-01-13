<?php
/**
* Main config file for the pluging
* 
* The non-specific config will be found in this file, other config files includes too
* @author Charles Dupoiron <contact@charlesdupoiron.com>
* @version 1.0
* @package wp-lazare
* @subpackage config
*/


/**
*	Start main plugin variable definition
*/
{
	DEFINE('WPLAZARE_VERSION', '1.0');
	DEFINE('WPLAZARE_DEBUG', false);
	
	DEFINE('WPLAZARE_OPTION_MENU', 'wplazare_option');
	DEFINE('WPLAZARE_CONFIG_MENU', 'wplazare_config');

	DEFINE('WPLAZARE_URL_SLUG_USERS_LISTING', 'wplazare_users');
	DEFINE('WPLAZARE_URL_SLUG_USERS_EDITION', 'wplazare_users');
	
	DEFINE('WPLAZARE_URL_SLUG_APPARTS_LISTING', 'wplazare_apparts');
	DEFINE('WPLAZARE_URL_SLUG_APPARTS_EDITION', 'wplazare_apparts');
	
	DEFINE('WPLAZARE_URL_SLUG_MAISONS_LISTING', 'wplazare_maisons');
	DEFINE('WPLAZARE_URL_SLUG_MAISONS_EDITION', 'wplazare_maisons');
	
	DEFINE('WPLAZARE_URL_SLUG_ASSOCIATIONS_LISTING', 'wplazare_associations');
	DEFINE('WPLAZARE_URL_SLUG_ASSOCIATIONS_EDITION', 'wplazare_associations');

	DEFINE('WPLAZARE_URL_SLUG_LOCATIONS_LISTING', 'wplazare_locations');
	DEFINE('WPLAZARE_URL_SLUG_LOCATIONS_EDITION', 'wplazare_locations');
	
	DEFINE('WPLAZARE_URL_SLUG_LOYERS_LISTING', 'wplazare_loyers');
	DEFINE('WPLAZARE_URL_SLUG_LOYERS_EDITION', 'wplazare_loyers');	
	
	DEFINE('WPLAZARE_URL_SLUG_QUESTIONS_LISTING', 'wplazare_questions');
	DEFINE('WPLAZARE_URL_SLUG_QUESTIONS_EDITION', 'wplazare_questions');
	
	DEFINE('WPLAZARE_URL_SLUG_HISTORIQUE_LISTING', 'wplazare_historique');
	DEFINE('WPLAZARE_URL_SLUG_HISTORIQUE_EDITION', 'wplazare_historique');
	
	DEFINE('WPLAZARE_URL_SLUG_STATS_LISTING', 'wplazare_stats');
	DEFINE('WPLAZARE_URL_SLUG_STATS_EDITION', 'wplazare_stats');
	
	DEFINE('WPLAZARE_ROLE_PERSONNE_ACCUEILLIE', 'subscriber');
	DEFINE('WPLAZARE_ROLE_DONATEUR', 'donateur');
	DEFINE('WPLAZARE_ROLE_BENEVOLE', 'benevole');
	DEFINE('WPLAZARE_ROLE_PARTENAIRE_SOCIAL', 'partenaire_social');
	DEFINE('WPLAZARE_ROLE_POSTULANT', 'postulant');
	DEFINE('WPLAZARE_ROLE_EVENT', 'attendee');
	DEFINE('WPLAZARE_ROLE_ORIENTER', 'orienter');
	DEFINE('WPLAZARE_UAM_GROUP_BUREAU', 3);
	
	DEFINE('WPLAZARE_CATEGORY_HISTORIQUE', 11);
	
	DEFINE('WPLAZARE_URL_SLUG_ORDERS_LISTING', 'wplazare_orders');
	DEFINE('WPLAZARE_URL_SLUG_ORDERS_EDITION', 'wplazare_orders');

    DEFINE('WPLAZARE_URL_SLUG_M_ORDERS_LISTING', 'wplazare_m_orders');
    DEFINE('WPLAZARE_URL_SLUG_M_ORDERS_EDITION', 'wplazare_m_orders');

	DEFINE('WPLAZARE_URL_SLUG_FORMS_LISTING', 'wplazare_forms');
	DEFINE('WPLAZARE_URL_SLUG_FORMS_EDITION', 'wplazare_forms');

	DEFINE('WPLAZARE_URL_SLUG_OFFERS_LISTING', 'wplazare_offers');
	DEFINE('WPLAZARE_URL_SLUG_OFFERS_EDITION', 'wplazare_offers');

    DEFINE('WPLAZARE_URL_SLUG_DOCUMENTS_LISTING', 'wplazare_documents');
    DEFINE('WPLAZARE_URL_SLUG_DOCUMENTS_EDITION', 'wplazare_documents');
}


/**
*	Start plugin path definition
*/
{
	DEFINE('WPLAZARE_HOME_URL', WP_PLUGIN_URL . '/' . WPLAZARE_PLUGIN_DIR . '/');
	DEFINE('WPLAZARE_HOME_DIR', WP_PLUGIN_DIR . '/' . WPLAZARE_PLUGIN_DIR . '/');
	
	DEFINE('WPLAZARE_INC_PLUGIN_DIR', WPLAZARE_HOME_DIR . 'includes/');
	DEFINE('WPLAZARE_LIB_PLUGIN_DIR', WPLAZARE_INC_PLUGIN_DIR . 'librairies/');
	DEFINE('WPLAZARE_HTML2PDF_PLUGIN_DIR', WPLAZARE_LIB_PLUGIN_DIR . 'html2pdf/');
	DEFINE('WPLAZARE_EXCEL_PLUGIN_DIR', WPLAZARE_LIB_PLUGIN_DIR . 'excel/');

	DEFINE('WPLAZARE_CSS_URL', WPLAZARE_HOME_URL . 'css/');
	DEFINE('WPLAZARE_JS_URL', WPLAZARE_HOME_URL . 'js/');
}


/**
*	Start database definition
*/
{
	/**
	* Get the global wordpress prefix for database table
	*/
	global $wpdb;
	/**
	* Define the main plugin prefix
	*/
	DEFINE('WPLAZARE_DB_PREFIX', $wpdb->prefix . "lazare_");
	DEFINE('WPLAZARE_DBT_APPARTS', WPLAZARE_DB_PREFIX . 'appartement');
	DEFINE('WPLAZARE_DBT_LIENS_MAISON_APPART', WPLAZARE_DB_PREFIX . 'lien_maison_appartement');
	DEFINE('WPLAZARE_DBT_LOCATIONS', WPLAZARE_DB_PREFIX . 'location');
	DEFINE('WPLAZARE_DBT_LOYERS', WPLAZARE_DB_PREFIX . 'loyer');
	DEFINE('WPLAZARE_DBT_MAISONS', WPLAZARE_DB_PREFIX . 'maison');
	DEFINE('WPLAZARE_DBT_ASSOCIATIONS', WPLAZARE_DB_PREFIX . 'association');
	DEFINE('WPLAZARE_DBT_CONFIG', WPLAZARE_DB_PREFIX . 'config');
	DEFINE('WPLAZARE_DBT_FICHES', WPLAZARE_DB_PREFIX . 'fiche');
	DEFINE('WPLAZARE_DBT_QUESTIONS', WPLAZARE_DB_PREFIX . 'question');
	DEFINE('WPLAZARE_DBT_ORDERS', WPLAZARE_DB_PREFIX . 'orders');
	DEFINE('WPLAZARE_DBT_FORMS', WPLAZARE_DB_PREFIX . 'forms');
	DEFINE('WPLAZARE_DBT_LINK_FORMS_OFFERS', WPLAZARE_DB_PREFIX . 'forms_offers_link');
	DEFINE('WPLAZARE_DBT_OFFERS', WPLAZARE_DB_PREFIX . 'offers');
	
	DEFINE('LIMESURVEY_DB_PREFIX', 'lime_');
	
}


/**
*	Start picture definition
*/
{
	DEFINE('WPLAZARE_SUCCES_ICON', admin_url('images/yes.png'));
	DEFINE('WPLAZARE_ERROR_ICON', admin_url('images/no.png'));
}

/**
*	Define the currency list
*/
{
	$currencyList = array();
	$currencyList[978] = __('Euro', 'wplazare');
	$currencyList[840] = __('US Dollar', 'wplazare');

	$currencyIconList = array();
	$currencyIconList[978] = '&euro;';
	$currencyIconList[840] = '&dollar;';
}

/**
*	Define the field to hide into a combobox
*/
{
	$comboxOptionToHide = array('');
}

/*
 * LIME SURVEY
 */
{
	DEFINE('LS_BASEURL', 'http://www.maisonlazare.com/limesurvey/');
	DEFINE('LS_USER', 'admin');
	DEFINE('LS_PASSWORD', 'lazare*2012');
	DEFINE('LIMESURVEY_SURVEYID_LAZARE', 782383);
	/*
	 * 	DEFINE('LS_BASEURL', 'http://www.maisonlazare.com/limesurvey/');
	DEFINE('LS_USER', 'admin');
	DEFINE('LS_PASSWORD', 'lazare*2012');
	DEFINE('LIMESURVEY_SURVEYID_LAZARE', 782383);
	*/
}