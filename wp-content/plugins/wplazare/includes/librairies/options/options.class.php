<?php
/**
 * Plugin options
 * 
 * Allows to manage the different option for the plugin
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 * @package wp-lazare
 * @subpackage librairies
 */

/**
 * Allows to manage the different option for the plugin
 * @package wp-lazare
 * @subpackage librairies
 */
class wplazare_option
{
	/**
	*	Function to get the different value for a given option
	*
	*	@param string $optionToGet The option we want to get the value for
	*	@param string $fieldToGet The specific value we want to get
	*
	*	@return mixed $optionValue The option value we want to get
	*/
	function getStoreConfigOption($optionToGet, $fieldToGet)
	{
		$optionValue = '';

		$option = get_option($optionToGet);
		if(!is_array($option))
		{
			$option = unserialize($option);
		}
		if(isset($option[$fieldToGet]))
		{
			$optionValue = $option[$fieldToGet];
		}

		return $optionValue;
	}
	/**
	*	Function to save an option into wordpress option database table
	*/
	function saveStoreConfiguration($optionToGet, $optionList, $outputMessage = true)
	{
		$updateOptionResult = update_option($optionToGet, serialize($optionList));
		if((($updateOptionResult == 1) || ($updateOptionResult == '')) && ($outputMessage))
		{
			echo '<div class="updated optionMessage" >' . __('Les options ont bien &eacute;t&eacute; enregistr&eacute;es', 'wplazare') . '</div>';
		}
	}

	/**
	*	Create the main option page for the plugin
	*/
	function doOptionsPage()
	{
		/*	Declare the different settings	*/
		register_setting('wplazare_store_config_group', 'storeTpe', '' );
		register_setting('wplazare_store_config_group', 'storeRang', '' );
		register_setting('wplazare_store_config_group', 'storeIdentifier', '' );
		register_setting('wplazare_store_config_group', 'environnement', '' );
		register_setting('wplazare_store_config_group', 'urlCgi', '' );
		register_setting('wplazare_store_config_group', 'urlSuccess', '' );
		register_setting('wplazare_store_config_group', 'urlDeclined', '' );
		register_setting('wplazare_store_config_group', 'urlCanceled', '' );
		register_setting('wplazare_store_config_group', 'urlCharge', '' );
        register_setting('wplazare_store_config_group', 'keyTest', '' );
		settings_fields( 'wplazare_url_config_group' );

		/*	Add the section about the store main configuration	*/
		add_settings_section('wplazare_store_config', __('Informations de la boutique', 'wplazare'), array('wplazare_option', 'storeConfigForm'), 'wplazareStoreConfig');

		/*	Add the section about the back url	*/
		add_settings_section('wplazare_url_config', __('Urls de retour apr&eacute;s un paiement', 'wplazare'), array('wplazare_option', 'urlConfigForm'), 'wplazareUrlConfig');
?>
<form action="" method="post" >
<input type="hidden" name="saveOption" id="saveOption" value="save" />
	<?php 
		do_settings_sections('wplazareStoreConfig'); 
 
		/*	Save the configuration in case that the form has been send with "save" action	*/
		if(isset($_POST['saveOption']) && ($_POST['saveOption'] == 'save'))
		{
			/*	Save the store main configuration	*/
			unset($optionList);$optionList = array();
			$optionList['storeTpe'] = $_POST['storeTpe'];
			$optionList['storeRang'] = $_POST['storeRang'];
			$optionList['storeIdentifier'] = $_POST['storeIdentifier'];
			$optionList['urlCgi'] = $_POST['urlCgi'];
			$optionList['environnement'] = $_POST['environnement'];
			wplazare_option::saveStoreConfiguration('wplazare_store_mainoption', $optionList);
		}
	?>
	<table summary="Store main configuration form" cellpadding="0" cellspacing="0" class="storeMainConfiguration" >
		<?php do_settings_fields('wplazareStoreConfig', 'mainWPayboxStoreConfig'); ?>
	</table>
	<br/><br/><br/>
	<?php 
		do_settings_sections('wplazareUrlConfig');

		/*	Save the configuration in case that the form has been send with "save" action	*/
		if(isset($_POST['saveOption']) && ($_POST['saveOption'] == 'save'))
		{
			/*	Save the configuration for bakc url after payment	*/
			unset($optionList);$optionList = array();
			$optionList['urlSuccess'] = $_POST['urlSuccess'];
			$optionList['urlDeclined'] = $_POST['urlDeclined'];
			$optionList['urlCanceled'] = $_POST['urlCanceled'];
			$optionList['urlCharge'] = $_POST['urlCharge'];
            $optionList['keyTest'] = $_POST['keyTest'];
			wplazare_option::saveStoreConfiguration('wplazare_store_urloption', $optionList);
		}
	?>
	<table summary="Back url main configuration form" cellpadding="0" cellspacing="0" class="storeMainConfiguration" >
		<tr>
			<td colspan="2" >
		<?php echo sprintf(__('Ajouter : %s dans les pages que vous allez cr&eacute;er.', 'wplazare'), '<span class=" bold" >[wplazare_payment_return title="Paybox return page" ]</span>'); ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" >&nbsp;</td>
		</tr>
<?php 
		do_settings_fields('wplazareUrlConfig', 'backUrlConfig'); 
?>
	</table>
	<br/><br/><br/>
	<input type="submit" class="button-primary" value="<?php _e('Enregistrer les options', 'wplazare'); ?>" />
</form>
<?php
	}

	/**
	*	Create the form for store configuration
	*/
	function storeConfigForm()
	{
		/*	Add the field for the store configuration	*/
		add_settings_field('wplazare_store_tpe', __('Num&eacute;ro de TPE de la boutique', 'wplazare'), array('wplazare_option', 'storeTpe'), 'wplazareStoreConfig', 'mainWPayboxStoreConfig');
		add_settings_field('wplazare_store_rang', __('Num&eacute;ro de rang de la boutique', 'wplazare'), array('wplazare_option', 'storeRang'), 'wplazareStoreConfig', 'mainWPayboxStoreConfig');
		add_settings_field('wplazare_store_id', __('Identifiant de la boutique', 'wplazare'), array('wplazare_option', 'storeIdentifier'), 'wplazareStoreConfig', 'mainWPayboxStoreConfig');
		add_settings_field('wplazare_cgi_url', __('Url du fichier cgi', 'wplazare'), array('wplazare_option', 'urlCgi'), 'wplazareStoreConfig', 'mainWPayboxStoreConfig');
		add_settings_field('wplazare_environnement', __('Environnement de la boutique', 'wplazare'), array('wplazare_option', 'environnement'), 'wplazareStoreConfig', 'mainWPayboxStoreConfig');
	}
	/**
	*	Create an input for the store TPE number
	*/
	function storeTpe()
	{
		$input_def['id'] = 'storeTpe';
		$input_def['name'] = 'storeTpe';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) == ''))
		{
			$inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wplazare_form::check_input_type($input_def);
	}
	/**
	*	Create an input for the store "Rang" number
	*/
	function storeRang()
	{
		$input_def['id'] = 'storeRang';
		$input_def['name'] = 'storeRang';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) == ''))
		{
			$inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wplazare_form::check_input_type($input_def);
	}
	/**
	*	Create an input for the store indentifier
	*/
	function storeIdentifier()
	{
		$input_def['id'] = 'storeIdentifier';
		$input_def['name'] = 'storeIdentifier';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) == ''))
		{
			$inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wplazare_form::check_input_type($input_def);
	}	/**
	*	Create an input for the environnement
	*/
	function environnement()
	{
		$input_def['id'] = 'environnement';
		$input_def['name'] = 'environnement';
		$input_def['type'] = 'select';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) == ''))
		{
			$inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;
		$environnement['test'] = __('Mode test', 'wplazare');
		$environnement['production'] = __('Mode Production', 'wplazare');
		$input_def['possible_value'] = $environnement;
		$input_def['valueToPut'] = 'index';

		echo wplazare_form::check_input_type($input_def);
	}
	/**
	*	Create an input for the cgi-bin script url�
	*/
	function urlCgi()
	{
		$input_def['id'] = 'urlCgi';
		$input_def['name'] = 'urlCgi';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) == ''))
		{
			$inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = wplazare_option::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wplazare_form::check_input_type($input_def);
	}
	
	/**
	*	Create the form for back url configuration
	*/
	function urlConfigForm()
	{
		/*	Add the field for the back link configuration	*/
		add_settings_field('wplazare_payment_success', __('Url de retour pour un paiement accept&eacute;', 'wplazare'), array('wplazare_option', 'urlSuccess'), 'wplazareUrlConfig', 'backUrlConfig');
		add_settings_field('wplazare_payment_canceled', __('Url de retour pour un paiement annul&eacute;', 'wplazare'), array('wplazare_option', 'urlCanceled'), 'wplazareUrlConfig', 'backUrlConfig');
		add_settings_field('wplazare_payment_declined', __('Url de retour pour un paiement refus&eacute;', 'wplazare'), array('wplazare_option', 'urlDeclined'), 'wplazareUrlConfig', 'backUrlConfig');
		add_settings_field('wplazare_payment_charge', __('Url de retour pour les charges', 'wplazare'), array('wplazare_option', 'urlCharge'), 'wplazareUrlConfig', 'backUrlConfig');
        add_settings_field('wplazare_payment_keytest', __('Cle de HMAC', 'wplazare'), array('wplazare_option', 'keyTest'), 'wplazareUrlConfig', 'backUrlConfig');
	}
	/**
	*	Create an input for the succes url
	*/
	function urlSuccess()
	{
		$input_def['id'] = 'urlSuccess';
		$input_def['name'] = 'urlSuccess';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) == ''))
		{
			$inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) != '')
		{
			$inputValue = wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wplazare_form::check_input_type($input_def);
	}
	/**
	*	Create an input for the canceled url
	*/
	function urlCanceled()
	{
		$input_def['id'] = 'urlCanceled';
		$input_def['name'] = 'urlCanceled';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) == ''))
		{
			$inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) != '')
		{
			$inputValue = wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wplazare_form::check_input_type($input_def);
	}	
	/**
	*	Create an input for the declined url
	*/
	function urlDeclined()
	{
		$input_def['id'] = 'urlDeclined';
		$input_def['name'] = 'urlDeclined';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) == ''))
		{
			$inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) != '')
		{
			$inputValue = wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wplazare_form::check_input_type($input_def);
	}
	
	/**
	*	Create an input for the charge url
	*/
	function urlCharge()
	{
		$input_def['id'] = 'urlCharge';
		$input_def['name'] = 'urlCharge';
		$input_def['type'] = 'text';
		$inputValue = '';
		if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) == ''))
		{
			$inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
		}
		elseif(wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) != '')
		{
			$inputValue = wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']);
		}
		$input_def['value'] = $inputValue;

		echo wplazare_form::check_input_type($input_def);
	}

    /**
     *	Create an input for the succes url
     */
    function keyTest()
    {
        $input_def['id'] = 'keyTest';
        $input_def['name'] = 'keyTest';
        $input_def['type'] = 'text';
        $inputValue = '';
        if(isset($_POST[$input_def['name']]) && (wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) == ''))
        {
            $inputValue = wplazare_tools::varSanitizer($_POST[$input_def['name']], '');
        }
        elseif(wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']) != '')
        {
            $inputValue = wplazare_option::getStoreConfigOption('wplazare_store_urloption', $input_def['name']);
        }
        $input_def['value'] = $inputValue;

        echo wplazare_form::check_input_type($input_def);
    }

}