<?php
/**
* Here we include every common file needed for the plugin. Just the file used in the entire plugin
* 
* @author Eoxia <dev@eoxia.com>
* @version 1.1
* @package wp-lazare
* @subpackage includes
*/


/**
* Include the database option management file
*/
require_once('librairies/options/db_options.class.php');
/**
* Include the plugin option management file
*/
require_once('librairies/options/options.class.php');
/**
* Include the tools to manage plugin's display
*/
require_once('librairies/display.class.php');
/**
* Include the tools
*/
require_once('librairies/tools.class.php');
/**
* Include the tools to manage database plugin
*/
require_once('librairies/database.class.php');
/**
* Include the tools to manage form into the plugin
*/
require_once('librairies/form.class.php');

/**
* Include the plugin config management file
*/
require_once('librairies/config/birthday.class.php');
require_once('librairies/config/bilan.class.php');
require_once('librairies/config/config.class.php');


/**
* Include the tools to manage users, maisons, apparts etc...
*/
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'users.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'apparts.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'maisons.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'association.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'locations.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'loyers.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'historique.class.php');
require_once(WPLAZARE_HTML2PDF_PLUGIN_DIR . 'pdfator.class.php');
require_once(WPLAZARE_EXCEL_PLUGIN_DIR . 'excelator.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'stats.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'offers.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'orders.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'payment_forms.class.php');
require_once(WPLAZARE_LIB_PLUGIN_DIR . 'events.class.php');
