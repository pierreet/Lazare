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
class wplazare_birthday
{		
	//-----------------------------------------------	
	function wplazare_birthday_notif_function()
	{
		// expression r�guli�re pour la date d'anniversaire en jj/mm/aaaa : /([0-9]{2})\/([0-9]{2})\/([0-9]{4})/
		
		$return = 0;
		
		//--------------------------------------------------------------------------
		// G�n�rer la liste des utilisteurs
		//--------------------------------------------------------------------------
		$locataires_birthday = get_cimyFieldValue(false, 'DATE_DE_NAISSANCE');	// liste de tous les users avec leurs date de naissance
		$date_obj = date("d-m", (current_time("timestamp", 0)+86400) ); 		// date de demain au format jj/mm (utilise le fuseau horaire configur� dans wordpress)		
		
		$users_list = array();
		foreach ($locataires_birthday as $birthday_data) {
			$anniv = $birthday_data['VALUE'];
			if( (strlen($anniv)==10) && (substr($anniv,0,5) === $date_obj) )
			{
				$users_list[] = $birthday_data['user_id'];							// ajoute l'utilisateur dont ce sera l'anniv demain au tableau $anniv_users
			}
		}
		
		if( !empty($users_list) )
		{
			$responsables_list = wplazare_tools::getResponsables($users_list);
			
			foreach ($responsables_list as $responsables_line)
			{				
				$headers = "From: ".get_option('blogname')." <".get_option('admin_email').">\r\n";
				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-type: text/html; charset=utf-8\r\n";
				
				$locataire_name = wplazare_tools::getUserName($responsables_line->locataire_id);
						
				$message = "Bonjour,<br />";	
				$message .= "Demain, nous f&ecirc;terons l&apos;anniversaire de ";
				$message .= wplazare_tools::getUserLink( $responsables_line->locataire_id ,$locataire_name);
				$message .= " !<br />";
				$message .= "<br />L'&eacute;quipe de ".get_option('blogname');
					
				$date_tomorrow = date("d-m-Y", (current_time("timestamp", 0)+86400) );
				$subject = "Anniversaire de ".$locataire_name." demain le ".$date_tomorrow;
				
				if($responsables_line->responsable_Appart_id)
				{
					$responsable_user_data = get_userdata( $responsables_line->responsable_Appart_id );
					$responsable_email =  $responsable_user_data->user_email;

					wp_mail( $responsable_email, $subject, $message, $headers);
				}
				if($responsables_line->responsable_Maison_id)
				{							
					$responsable_user_data = get_userdata( $responsables_line->responsable_Maison_id );
					$responsable_email =  $responsable_user_data->user_email;
									
					wp_mail( $responsable_email, $subject, $message, $headers);
				}
				if($responsables_line->responsable_Association_id)
				{							
					$responsable_user_data = get_userdata( $responsables_line->responsable_Association_id );
					$responsable_email =  $responsable_user_data->user_email;
									
					wp_mail( $responsable_email, $subject, $message, $headers);
				}		
			}
			
			$return = 1;
		}
	
		return $return;
	}
	//-----------------------------------------------
	

	
}

add_action('birthday_notif_hook', array('wplazare_birthday', 'wplazare_birthday_notif_function'));