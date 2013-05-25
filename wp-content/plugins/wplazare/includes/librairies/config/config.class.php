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
class wplazare_config
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
	function saveConfiguration($optionToGet, $optionList, $outputMessage = true)
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
		register_setting('wplazare_store_config_group', 'birthday_notif', '' );
        register_setting('wplazare_store_config_group', 'bilan_notif', '' );
		settings_fields( 'wplazare_url_config_group' );

		/*	Add the section about the store main configuration	*/
		add_settings_section('wplazare_store_config', __('Configuration g&eacute;n&eacute;rale', 'wplazare'), array('wplazare_config', 'generalConfigForm'), 'wplazareGeneralConfig');

?>
<form action="" method="post" >
<input type="hidden" name="saveOption" id="saveOption" value="save" />
	<?php 
		do_settings_sections('wplazareGeneralConfig'); 
 
		/*	Save the configuration in case that the form has been send with "save" action	*/
		if(isset($_POST['saveOption']) && ($_POST['saveOption'] == 'save'))
		{
			/*	Save the store main configuration	*/
			unset($optionList);$optionList = array();
			$optionList['birthdayNotif'] = isset($_POST['birthdayNotif']) ? $_POST['birthdayNotif'] : '';
            $optionList['bilanNotif'] = isset($_POST['bilanNotif']) ? $_POST['bilanNotif'] : '';
			wplazare_config::saveConfiguration('wplazare_store_mainoption', $optionList);
		}
	?>
	<table summary="Store main configuration form" cellpadding="0" cellspacing="0" class="storeMainConfiguration" >
		<?php do_settings_fields('wplazareGeneralConfig', 'mainwplazareGeneralConfig'); ?>
	</table>
		
	<br/><br/><br/>
	<input type="submit" class="button-primary" value="<?php _e('Enregistrer les options', 'wplazare'); ?>" />
</form>
<?php
	}

	/**
	*	Create the form for store configuration
	*/
	function generalConfigForm()
	{
		/*	Add the field for the store configuration	*/
		add_settings_field('wplazare_birthdayNotif', __('Avertir par email des futurs anniversaires', 'wplazare'), array('wplazare_config', 'birthdayNotif'), 'wplazareGeneralConfig', 'mainwplazareGeneralConfig');
        add_settings_field('wplazare_bilanNotif', __('Envoyer le bilan mensuel par email', 'wplazare'), array('wplazare_config', 'bilanNotif'), 'wplazareGeneralConfig', 'mainwplazareGeneralConfig');
	}
	/**
	*	Create an input for the store TPE number
	*/
	function birthdayNotif()
	{		
		$input_def['id'] = 'birthdayNotif';
		$input_def['name'] = 'birthdayNotif';
		$input_def['type'] = 'checkbox';
		$input_def['possible_value'] = 'Checkzy';
		
		$hook = 'birthday_notif_hook';
		$doItNow_flag = 0;
		
		if( wplazare_config::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) == '' )
		{
			$inputValue = '';
			wp_clear_scheduled_hook($hook);
		}
		elseif(wplazare_config::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) != '')
		{
			$inputValue = 'Checkzy';
			$checkhour = " 12:00:00";
			if( !wp_get_schedule( $hook ))
			{
				$today = date("Y-m-d", current_time("timestamp", 0)).$checkhour;
				//echo($today."|".strtotime($today)."|".current_time("timestamp", 0));
				
				if( strtotime($today) > current_time("timestamp", 0))													// if current_time is before 12:00
					$next_schedule = $today;																			// then next schedule is today @ 12:00
				else
				{
					$next_schedule = date("Y-m-d", current_time("timestamp", 0)+86400 ).$checkhour;		// else next schedule is tomorrow @ 12:00
					$doItNow_flag = 1;																			// and also, do it now
				}
				$timestamp = strtotime($next_schedule);
				wp_schedule_event( $timestamp, 'daily', $hook);
			}
		}
		$input_def['value'] = $inputValue;
		
		echo wplazare_form::check_input_type($input_def);
		
		if($doItNow_flag)
			do_action($hook);
	}

    function bilanNotif()
    {
        $input_def['id'] = 'bilanNotif';
        $input_def['name'] = 'bilanNotif';
        $input_def['type'] = 'checkbox';
        $input_def['possible_value'] = 'Checkzy';

        $hook = 'bilan_notif_hook';
        $doItNow_flag = 0;

        if( wplazare_config::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) == '' )
        {
            $inputValue = '';
            wp_clear_scheduled_hook($hook);
        }
        elseif(wplazare_config::getStoreConfigOption('wplazare_store_mainoption', $input_def['name']) != '')
        {
            $inputValue = 'Checkzy';
            $checkhour = " 12:00:00";
            if( !wp_get_schedule( $hook ))
            {
                $today = date("Y-m-d", current_time("timestamp", 0)).$checkhour;
                //echo($today."|".strtotime($today)."|".current_time("timestamp", 0));

                if( strtotime($today) > current_time("timestamp", 0))													// if current_time is before 12:00
                    $next_schedule = $today;																			// then next schedule is today @ 12:00
                else
                {
                    $next_schedule = date("Y-m-d", current_time("timestamp", 0)+86400 ).$checkhour;		// else next schedule is tomorrow @ 12:00
                }
                $timestamp = strtotime($next_schedule);
                wp_schedule_event( $timestamp, 'daily', $hook);
                $doItNow_flag = 1;																			// and also, do it now
            }
        }
        $input_def['value'] = $inputValue;

        echo wplazare_form::check_input_type($input_def);

        if($doItNow_flag)
            do_action($hook);
    }
}