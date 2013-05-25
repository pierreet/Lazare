<?php
/**
* Define the different method to access or create users
* 
*	Define the different method to access or create users
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Define the different method to access or create users
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_loyers
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wplazare_loyers';
	}	
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getPageIcon()
	{
		return '';
	}	
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getListingSlug()
	{
		return WPLAZARE_URL_SLUG_LOYERS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_LOYERS_EDITION;
	}
	
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
	{
		return WPLAZARE_DBT_LOYERS;
	}

	/**
	*	Define the title of the page 
	*
	*	@return string $title The title of the page looking at the environnement
	*/
	function pageTitle()
	{
		return 'Gestion des loyers';
	}

	/**
	*	Define the different message and action after an action is send through the element interface
	*/
	function elementAction()
	{
		global $wpdb;
		$actionResultMessage = '';

		$pageMessage = $actionResult = '';
		$pageAction = isset($_REQUEST[wplazare_loyers::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_loyers::getDbTable() . '_action']) : '';
//		$id = isset($_REQUEST[wplazare_loyers::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_loyers::getDbTable()]['id']) : '';

		/*	Define the database operation type from action launched by the user	 */
		/*************************				GENERIC				**************************/
		/*************************************************************************/		

		//print_r($_REQUEST);
		if(current_user_can('wplazare_edit_loyer'))
		{
			if( isset($_REQUEST['rappel']) )
			{				
				if( isset($_GET["loc_id"]) && ($_GET["loc_id"]!="") )
				{
					$locataire_id = $_GET["loc_id"];
					$locataire_name = wplazare_loyers::send_rappel_mail($locataire_id);
					$actionResult = 'done_rappel';
				}
				else
				{
					$actionResult = 'error_rappel';
				}
			}		
			elseif( isset($_REQUEST['recherche']) )
			{
				
				
			}
			elseif( ($pageAction != '') && (($pageAction == 'edit') || ($pageAction == 'editandcontinue')))
			{
				$_REQUEST[wplazare_apparts::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				
				// save everything
				foreach (wplazare_loyers::getLocationsIds() as $locationId_row)
				{
					$locationId = $locationId_row->location_id;
					if( isset( $_REQUEST["annee_".$locationId] )  && ($_REQUEST["annee_".$locationId]!="")
					&& isset( $_REQUEST["mois_".$locationId] )  && ($_REQUEST["mois_".$locationId]!="")
					&& isset( $_REQUEST["user_id_".$locationId] )  && ($_REQUEST["user_id_".$locationId]!="") )
					{
						$locataire_id = $_REQUEST["user_id_".$locationId];
                        $current_mois = $_REQUEST["mois_".$locationId];
                        $current_annee = $_REQUEST["annee_".$locationId];
                        $filters = array( 'mois' => $current_mois, 'annee' => $current_annee);
                        $current_location = wplazare_loyers::getElement($locationId, $filters);
						$is_payed = ( isset( $_REQUEST["payed_".$locationId] ) && ($_REQUEST["payed_".$locationId]!="") ) ? 1 : 0;
                        
						$_REQUEST[wplazare_loyers::getDbTable()]['mois'] = $current_mois;
						$_REQUEST[wplazare_loyers::getDbTable()]['annee'] = $current_annee;
                        $_REQUEST[wplazare_loyers::getDbTable()]['location'] = $locationId;
						$_REQUEST[wplazare_loyers::getDbTable()]['payed'] = $is_payed;
						$_REQUEST[wplazare_apparts::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
                        
                        
						if( isset( $_REQUEST["loyer_id_".$locationId] ) && ($_REQUEST["loyer_id_".$locationId] == "") && !isset($current_location->loyer_id) )
						{
							// add a new loyer
							$_REQUEST[wplazare_loyers::getDbTable()]['creation_date'] = date('Y-m-d H:i:s');
							$actionResult = wplazare_database::save($_REQUEST[wplazare_loyers::getDbTable()], wplazare_loyers::getDbTable() );
							//$new_loyer_id = $wpdb->insert_id;
							if( ($actionResult=="done") && isset( $_REQUEST["notif_email".$locationId] ) && ($is_payed==1) )
							{
								wplazare_loyers::send_attestation_mail($locataire_id, $current_mois, $current_annee);
							}
						}
						elseif( isset( $_REQUEST["loyer_id_".$locationId] ) && ($_REQUEST["loyer_id_".$locationId] != "") )
						{
                            $already_payed = isset($current_location->etat) ? $current_location->etat : 0;
							// edit an existing loyer
							$id = wplazare_tools::varSanitizer( $_REQUEST["loyer_id_".$locationId] );
							$actionResult = wplazare_database::update($_REQUEST[wplazare_loyers::getDbTable()], $id, wplazare_loyers::getDbTable());
							if( ($actionResult=="done") && isset( $_REQUEST["notif_email_".$locationId] ) && ($is_payed==1) && ($already_payed != 1) )
							{
 								wplazare_loyers::send_attestation_mail($locataire_id, $current_mois, $current_annee);
							}
						}
						else
						{
							$actionResult = 'error1';
                            break;
						}
					}
				}
			}
		}
		else
		{
			$actionResult = 'userNotAllowedForActionEdit';
		}
				
				
		/*	When an action is launched and there is a result message	*/
		/************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
		/************		CHANGE ERROR MESSAGE FOR SPECIFIC CASE					*************/
		/****************************************************************************/
		if($actionResult != '')
		{
			$elementIdentifierForMessage = '<span class="bold" >' . wplazare_loyers::getDbTable() . '</span>';
			if($actionResult == 'error' || $actionResult == 'error1' || $actionResult == 'error2')
			{/*	CHANGE HERE FOR SPECIFIC CASE	*/
				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />' . sprintf('Une erreur est survenue lors de l\'enregistrement dans la table %s : %s', $elementIdentifierForMessage, $actionResult);
				if(WPLAZARE_DEBUG)
				{
					$actionResultMessage .= '<br/>' . $wpdb->last_error;
				}
			}
			elseif(($actionResult == 'done') || ($actionResult == 'nothingToUpdate'))
			{
				/*****************************************************************************************************************/
				/*************************			CHANGE FOR SPECIFIC ACTION FOR CURRENT ELEMENT				****************************/
				/*****************************************************************************************************************/

				/*************************			GENERIC				****************************/
				/*************************************************************************/
				$actionResultMessage = '<img src="' . WPLAZARE_SUCCES_ICON . '" alt="action success" class="wplazarePageMessage_Icon" />' . sprintf('L\'enregistrement s\'est d&eacute;roul&eacute; avec succ&egrave;s');
			}
			elseif($actionResult == 'done_rappel')
			{
				$actionResultMessage = '<img src="' . WPLAZARE_SUCCES_ICON . '" alt="action success" class="wplazarePageMessage_Icon" />' . sprintf('L\'envoi du rappel de %s s\'est d&eacute;roul&eacute; avec succ&egrave;s', $locataire_name);
			}
			elseif(actionResult == 'error_rappel')
			{		
				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />' . 'Une erreur est survenue lors de l\'envoi du rappel.';
			}
			elseif(($actionResult == 'userNotAllowedForActionEdit') || ($actionResult == 'userNotAllowedForActionAdd') || ($actionResult == 'userNotAllowedForActionDelete'))
			{
				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />' . 'Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action.';
			}
		}

		return $actionResultMessage;
	}
	/**
	*	Return the list page content, containing the table that present the item list
	*
	*	@return string $listItemOutput The html code that output the item list
	*/
	function elementList()
	{
		$editAction = admin_url('admin.php?page=' . wplazare_loyers::getEditionSlug() . '&amp;action=edit&amp;id=1');
		$editForm = '<form action="'.$editAction.'" method="post" >
						<input type="submit" class="button-primary" value="Modifier" />
					</form>';
		
		//prepare the sort lists
		$options_adresse='<option>- Adresse -</option>';
		$options_mois ='';
		$options_annee ='';
		
		
		$filter = array();
		if(isset($_REQUEST['search1']) && $_REQUEST['search1']!=''){
			$filter[$_REQUEST['search1']] =  $_REQUEST[$_REQUEST['search1']];
		}
		if(isset($_REQUEST['search3']) && $_REQUEST['search3']!=''){
			$filter[$_REQUEST['search3']] =  $_REQUEST[$_REQUEST['search3']];
		}
		if(isset($_REQUEST['search4']) && $_REQUEST['search4']!=''){
			$filter[$_REQUEST['search4']] =  $_REQUEST[$_REQUEST['search4']];
		}
		
		foreach (wplazare_loyers::getApparts() as $result){
			$selected = "";
			if(isset($_REQUEST['search1'])) 
				if($_REQUEST[$_REQUEST['search1']] == $result->adresse)
					$selected = "selected='selected'";
			$options_adresse .= '<option '.$selected.'>'.stripslashes($result->adresse).'</option>';
		};
		
		foreach (wplazare_loyers::getMois() as $result){
			$selected = "";
			if(isset($_REQUEST['search3']) && isset($_REQUEST[$_REQUEST['search3']]) ) 
			{
				if($_REQUEST[$_REQUEST['search3']] == $result['value'])
					$selected = "selected='selected'";
			}
			else
			{
				if( date("n", (current_time("timestamp", 0))) == $result['value'])
					$selected = "selected='selected'";					
			}
			$options_mois .= '<option '.$selected.' value='.$result['value'].'>'.$result['name'].'</option>';
		};
		foreach (wplazare_loyers::getAnnee() as $result){
			$selected = "";
			if(isset($_REQUEST['search4']) && isset($_REQUEST[$_REQUEST['search4']]) ) 
			{
				if($_REQUEST[$_REQUEST['search4']] == $result)
					$selected = "selected='selected'";
			}
			else
			{
				if( date("Y", (current_time("timestamp", 0))) == $result)
					$selected = "selected='selected'";					
			}
			$options_annee .= '<option '.$selected.'>'.$result.'</option>';
		};
		
		
		$formAction = admin_url('admin.php?page=' . wplazare_loyers::getEditionSlug());
		
		$selectForm='<form  method="post" action="' . $formAction . '" enctype="multipart/form-data">
						<select name="adresse" id="adresse">'.$options_adresse.'</select>	
						<select name="mois" id="mois">'.$options_mois.'</select>
						<select name="annee" id="annee" class="'.wplazare_loyers::getCurrentPageCode().'_annee">'.$options_annee.'</select>
						<input type="submit" class="button-primary" value="recherche"/>
						<input type="hidden" name="search1" value="adresse"/>
						<input type="hidden" name="search3" value="mois"/>
						<input type="hidden" name="search4" value="annee"/>
					</form>
		';
		
		/*	Start the table definition	*/
		$tableId = 'Loyers_list';
		$tableSummary = 'Etats des loyers/';
		$tableTitles = array();
		$tableTitles[] = 'Ville';
		$tableTitles[] = 'Adresse';
		$tableTitles[] = 'locataire';
		$tableTitles[] = 'Etat';
		$tableTitles[] = 'Rappel';

		$tableClasses = array();
		$tableClasses[] = 'wplazare' . wplazare_loyers::getCurrentPageCode() . '_ville_column filter-select';
		$tableClasses[] = 'wplazare' . wplazare_loyers::getCurrentPageCode() . '_adresse_column';		
		$tableClasses[] = 'wplazare' . wplazare_loyers::getCurrentPageCode() . '_locataire_column';
		$tableClasses[] = 'wplazare' . wplazare_loyers::getCurrentPageCode() . '_etat_column filter-select';
		$tableClasses[] = 'wplazare' . wplazare_loyers::getCurrentPageCode() . '_rappel_column filter-false';

		if(empty($filter)) $filter = '';
		$elementList = wplazare_loyers::getElement('',$filter);

		if(count($elementList) > 0)
		{
			$line = 0;
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wplazare_loyers::getCurrentPageCode() . '_' . $element->location_id;

				$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_ville_cell', 'value' => stripslashes($element->ville) );
				$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_adresse_cell', 'value' => stripslashes($element->adresse));
				
				$locataire_name = wplazare_tools::getUserName($element->user_id);
				$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_user_id_cell', 'value' => $locataire_name);
				
				//etat (payé/non payé)
				$location_etat = ( $element->etat == 1 ) ? "Pay&eacute;" : "Non pay&eacute;";
				$etat = ($element->etat==1) ? 1 : 0;
				$value_etat = $location_etat;
				// ajouter des options de rappel si l'état est "non payé"
				if($etat==0)
				{
					//$value_etat .= " ( Envoyer un email (&agrave; faire) )";
				}
				$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_etat_cell_'.$etat, 'value' => $value_etat);
				
				
				// rappel
				$value_rappel = "";
				if($etat == 0)
				{
					// .$element->user_id.
					$formAction = admin_url('admin.php?page='.wplazare_loyers::getEditionSlug().'&amp;loc_id='.$element->user_id);
					$value_rappel .= '<form id="'.$element->location_id.'"  method="post" action="'.$formAction.'" enctype="multipart/form-data">'.
										'<input type="submit" value="Envoyer un rappel" name="rappel" />'.
									 '</form>';
				}	
				$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_etat_cell_'.$etat, 'value' => $value_rappel);
				
				$tableRows[] = $tableRowValue;
				$line++;
                unset($tableRowValue);
			}
		}
		else
		{
			$tableRowsId[] = wplazare_loyers::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_name_cell', 'value' => 'Aucun loyer n\'a encore &eacute;t&eacute; cr&eacute;&eacute;');
			$tableRows[] = $tableRowValue;
		}
		
		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $editForm.$selectForm.$listItemOutput;
	}
	
	function elementEdition($itemToEdit = '',$checkbox_option = '')
	{		
		//prepare the sort lists
		$options_ville ='<option>- Ville -</option>';
		$options_etat ='<option>- Etat -</option>';
		$options_adresse='<option>- Adresse -</option>';
		$options_mois ='';
		$options_annee ='';
	
		
		$filter = array();
		if(isset($_REQUEST['search0']) && $_REQUEST['search0']!=''){
			$filter[$_REQUEST['search0']] =  $_REQUEST[$_REQUEST['search0']];
		}
		if(isset($_REQUEST['search1']) && $_REQUEST['search1']!=''){
			$filter[$_REQUEST['search1']] =  $_REQUEST[$_REQUEST['search1']];
		}
		if(isset($_REQUEST['search2']) && $_REQUEST['search2']!=''){
			$filter[$_REQUEST['search2']] =  $_REQUEST[$_REQUEST['search2']];
		}		
		if(isset($_REQUEST['search3']) && $_REQUEST['search3']!=''){
			$filter[$_REQUEST['search3']] =  $_REQUEST[$_REQUEST['search3']];
		}
		if(isset($_REQUEST['search4']) && $_REQUEST['search4']!=''){
			$filter[$_REQUEST['search4']] =  $_REQUEST[$_REQUEST['search4']];
		}		
			
		
		foreach (wplazare_loyers::getVilles() as $result){
			$selected = "";
			if(isset($_REQUEST['search0'])) 
				if($_REQUEST[$_REQUEST['search0']] == $result->ville)
					$selected = "selected='selected'";
			$options_ville .= '<option '.$selected.'>'.$result->ville.'</option>';
		};
		foreach (wplazare_loyers::getApparts() as $result){
			$selected = "";
			if(isset($_REQUEST['search1'])) 
				if($_REQUEST[$_REQUEST['search1']] == $result->adresse)
					$selected = "selected='selected'";
			$options_adresse .= '<option '.$selected.'>'.$result->adresse.'</option>';
		};
		foreach (wplazare_loyers::getEtats() as $result){
			$selected = "";
			if(isset($_REQUEST['search2'])) 
				if(strcmp($_REQUEST[$_REQUEST['search2']], $result['value'])==0)
					$selected = "selected='selected'";
			$options_etat .= '<option '.$selected.' value='.$result['value'].'>'.$result['name'].'</option>';
		};
			
		foreach (wplazare_loyers::getMois() as $result){
			$selected = "";
			if(isset($_REQUEST['search3']) && isset($_REQUEST[$_REQUEST['search3']]) ) 
			{
				if($_REQUEST[$_REQUEST['search3']] == $result['value'])
					$selected = "selected='selected'";
			}
			else
			{
				if( date("n", (current_time("timestamp", 0))) == $result['value'])
					$selected = "selected='selected'";					
			}
			$options_mois .= '<option '.$selected.' value='.$result['value'].'>'.$result['name'].'</option>';
		};
		
		foreach (wplazare_loyers::getAnnee() as $result){
			$selected = "";
			if(isset($_REQUEST['search4']) && isset($_REQUEST[$_REQUEST['search4']]) ) 
			{
				if($_REQUEST[$_REQUEST['search4']] == $result)
					$selected = "selected='selected'";
			}
			else
			{
				if( date("Y", (current_time("timestamp", 0))) == $result)
					$selected = "selected='selected'";					
			}
			$options_annee .= '<option '.$selected.'>'.$result.'</option>';
		};
		
		
		$selectForm='	<select name="ville" id="ville">'.$options_ville.'</select>
						<select name="adresse" id="adresse">'.$options_adresse.'</select>	
						<select name="etat" id="etat">'.$options_etat.'</select>
						<select name="mois" id="mois">'.$options_mois.'</select>
						<select name="annee" id="annee" class="'.wplazare_loyers::getCurrentPageCode().'_annee">'.$options_annee.'</select>
						<input type="submit" value="Recherche" class="button-primary" name="recherche"/>
						<input type="hidden" name="search0" value="ville"/>
						<input type="hidden" name="search1" value="adresse"/>
						<input type="hidden" name="search2" value="etat"/>
						<input type="hidden" name="search3" value="mois"/>
						<input type="hidden" name="search4" value="annee"/>
		';
		
		$listItemOutput = '<script type="text/javascript" >
							wplazare(document).ready(function(){
								wplazareMainInterface("' . wplazare_loyers::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_loyers::getEditionSlug()) . '");
						
								wplazare("#delete").click(function(){
									wplazare("#' . wplazare_loyers::getDbTable() . '_action").val("delete");
									deletePaymentForm();
								});
								if(wplazare("#' . wplazare_loyers::getDbTable() . '_action").val() == "delete"){
									deletePaymentForm();
								}
								function deletePaymentForm(){
									if(confirm(wplazareConvertAccentTojs(\'&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?\'))){
										wplazare("#' . wplazare_loyers::getDbTable() . '_form").submit();
									}
									else{
										wplazare("#' . wplazare_loyers::getDbTable() . '_action").val("edit");
									}
								}
							});
						</script>';
		
		$formAction = admin_url('admin.php?page=' . wplazare_loyers::getEditionSlug(). '&amp;action=edit&amp;id=1');
		$listItemOutput .= '<form name="' . wplazare_loyers::getDbTable() . '_form" id="' . wplazare_loyers::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data">';
		$listItemOutput .= wplazare_form::form_input(wplazare_loyers::getDbTable() . '_action', wplazare_loyers::getDbTable() . '_action', 'edit' , 'hidden');
		$listItemOutput .= $selectForm;
		/*	Start the table definition	*/
		$tableId = 'Loyers_list';
		$tableSummary = 'Etats des loyers/';
		$tableTitles = array();
		$tableTitles[] = 'Ville';
		$tableTitles[] = 'Adresse';
		$tableTitles[] = 'locataire';
		$tableTitles[] = 'Etat';

		$tableClasses = array();
		$tableClasses[] = 'wplazare' . wplazare_loyers::getCurrentPageCode() . '_ville_column';
		$tableClasses[] = 'wplazare' . wplazare_loyers::getCurrentPageCode() . '_adresse_column';		
		$tableClasses[] = 'wplazare' . wplazare_loyers::getCurrentPageCode() . '_locataire_column';
		$tableClasses[] = 'wplazare' . wplazare_loyers::getCurrentPageCode() . '_etat_column';

		if(empty($filter)) $filter = '';
		$elementList = wplazare_loyers::getElement('',$filter);

		$dbFieldList = wplazare_database::fields_to_input(wplazare_loyers::getDbTable());

		if(count($elementList) > 0)
		{
			$line = 0;
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wplazare_loyers::getCurrentPageCode() . '_' . $element->location_id;

				unset($tableRowValue);
				$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_ville_cell', 'value' => stripslashes($element->ville) );
				$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_adresse_cell', 'value' => stripslashes($element->adresse));
				
				$locataire_name = wplazare_tools::getUserName($element->user_id);
				$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_user_id_cell', 'value' => $locataire_name);
				
				//$location_etat = ( $element->etat == 1 ) ? "Pay&eacute;" : "Non pay&eacute;";
				//$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_etat_cell', 'value' => $location_etat);

				$row_col_content = "";
				$etat = ($element->etat == "1") ? 1 : 0;
				
				// add the checkbox
				$input_def['id'] = 'payed_'.$element->location_id;
				$input_def['name'] = 'payed_'.$element->location_id;
				$input_def['type'] = 'checkbox';
				$input_def['possible_value'] = '1';

                $inputValue = $etat ? '1' : '2';
				$input_def['value'] = $inputValue;
					
				$row_col_content .= wplazare_form::check_input_type($input_def);
				// end - add the checkbox
				
				$input_def['id'] = 'loyer_id_'.$element->location_id;
				$input_def['name'] = 'loyer_id_'.$element->location_id;
				$input_def['type'] = 'hidden';			
				$input_def['value'] = wplazare_tools::varSanitizer($element->loyer_id);
				$row_col_content .= wplazare_form::check_input_type($input_def);

				$input_def['id'] = 'user_id_'.$element->location_id;
				$input_def['name'] = 'user_id_'.$element->location_id;
				$input_def['type'] = 'hidden';		
				$input_def['value'] = wplazare_tools::varSanitizer($element->user_id);
				$row_col_content .= wplazare_form::check_input_type($input_def);

				if ( !$etat)
				{
					$input_def['id'] = 'notif_email_'.$element->location_id;
					$input_def['name'] = 'notif_email_'.$element->location_id;
					$input_def['type'] = 'hidden';
					$input_def['value'] = 'notif_email_'.$element->location_id;
					$row_col_content .= wplazare_form::check_input_type($input_def);
				}
				
				
				$input_def['id'] = 'mois_'.$element->location_id;
				$input_def['name'] = 'mois_'.$element->location_id;
				$input_def['type'] = 'hidden';
				$value = wplazare_tools::varSanitizer($element->mois);		
				if($value == '')
					$value = ( ($filter!="") && array_key_exists('mois', $filter)) ? $filter['mois'] : date("n", (current_time("timestamp", 0)));
				$input_def['value'] = $value;
				$row_col_content .= wplazare_form::check_input_type($input_def);
				
				$input_def['id'] = 'annee_'.$element->location_id;
				$input_def['name'] = 'annee_'.$element->location_id;
				$input_def['type'] = 'hidden';
				$value = wplazare_tools::varSanitizer($element->annee);
				if($value == '')
					$value = ( ($filter!="") && array_key_exists('annee', $filter)) ? $filter['annee'] : date("Y", (current_time("timestamp", 0)));			
				$input_def['value'] = $value;
				$row_col_content .= wplazare_form::check_input_type($input_def);
				
				
				$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_etat_cell', 'value' => $row_col_content);
				$tableRows[] = $tableRowValue;
				$line++;
			}
		}
		else
		{
			$tableRowsId[] = wplazare_loyers::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wplazare_loyers::getCurrentPageCode() . '_name_cell', 'value' => 'Aucun loyer n\'a encore &eacute;t&eacute; cr&eacute;&eacute;');
			$tableRows[] = $tableRowValue;
		}
		
		$listItemOutput .= wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);
		$listItemOutput .= '</form>';
		
		return $listItemOutput;
	}
	
	/**
	*	Return the different button to save the item currently being added or edited
	*
	*	@return string $currentPageButton The html output code with the different button to add to the interface
	*/
	function getPageFormButton()
	{
		//$action = isset($_REQUEST['action']) ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'edit';
		$currentPageButton = '';

		if(current_user_can('wplazare_edit_loyer'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="Enregistrer" /><input type="button" class="button-primary" id="saveandcontinue" name="saveandcontinue" value="' . __('Enregistrer et continuer l\'&eacute;dition', 'wplazare') . '" />';
		}

		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_loyers::getListingSlug()) . '" class="button add-new-h2" >Retour</a></h2>';

		return $currentPageButton;
	}
	
	/**
	*	Get the existing element list into database
	*
	*	@param integer $elementId optionnal The element identifier we want to get. If not specify the entire list will be returned
	*	@param string $elementMail optionnal The mail of element to get into database. If not specify the entire list will be returned
	**	@param string $filter optionnal Filter element...
	*
	*	@return object $elements A wordpress database object containing the element list
	*/
	function getElement($elementId = '',$filters = '')
	{
		global $wpdb;
		$elements = array();
		$moreQuery_filters = "";
		
		$mois_selected = "";
		$annee_selected = "";

		if($elementId != '')
		{
			$moreQuery_filters = " WPLOCATIONS.id = '" . $elementId . "' ";
		}
		if($filters != ''){
			foreach ($filters as $key => $value){
				switch ($key)
				{
				case 'ville':
					if($value != '- Ville -'):
						if($moreQuery_filters!='') $moreQuery_filters.=" AND ";
						$moreQuery_filters .= "WPAPPARTS.ville LIKE '".$value."' ";
					endif;
				break;
				case 'adresse':
					if($value != '- Adresse -'):
						if($moreQuery_filters!='') $moreQuery_filters.=" AND ";
						$moreQuery_filters .= "WPAPPARTS.adresse = '".$value."' ";
					endif;
				break;
				case 'etat':
					if($value != '- Etat -'):
						$value = ( $value ) ? "=1 " : "!=1 OR WPLOYERS.payed IS NULL ";
						if($moreQuery_filters!='') $moreQuery_filters.=" AND ";
						$moreQuery_filters .= "(WPLOYERS.payed".$value.") ";
					endif;
				break;
				case 'mois':
					$mois_selected = $value;
				break;
				case 'annee':
					$annee_selected = $value;
				break;
				}
			}
		}
		
		if($moreQuery_filters != "")
			$moreQuery_filters = " AND ".$moreQuery_filters;
			
		if($mois_selected == '')
			$mois_selected = date("n", (current_time("timestamp", 0)));
			
		if($annee_selected == '')
			$annee_selected = date("Y", (current_time("timestamp", 0)));
			
			
		$moreQuery_dates = "WPLOCATIONS.date_debut<'".$annee_selected."/".$mois_selected."/28' ";
		$moreQuery_dates .= "AND ( WPLOCATIONS.date_fin>'".$annee_selected."/".$mois_selected."/1' ";
		$moreQuery_dates .= "OR WPLOCATIONS.date_fin IS NULL ";
		$moreQuery_dates .= "OR WPLOCATIONS.date_fin = '0000-00-00' ) ";
		$moreQuery_dates .= 'AND WPLOCATIONS.status LIKE "valid" ';
		
		   
		$moreQuery = 'WHERE '.$moreQuery_dates.$moreQuery_filters;
		
		$query = $wpdb->prepare(
		"SELECT WPAPPARTS.adresse AS adresse, WPAPPARTS.ville AS ville, WPLOYERS.payed AS etat, WPLOCATIONS.user AS user_id, 
				WPLOCATIONS.id AS location_id, WPLOYERS.id AS loyer_id, WPLOYERS.mois AS mois, WPLOYERS.annee AS annee
		FROM " . wplazare_locations::getDbTable() . " AS WPLOCATIONS ".
		"LEFT JOIN ".wplazare_apparts::getDbTable()." AS WPAPPARTS ON WPLOCATIONS.appartement=WPAPPARTS.id ".
		"LEFT JOIN ".wplazare_loyers::getDbTable()." AS WPLOYERS ON ( WPLOCATIONS.id=WPLOYERS.location AND WPLOYERS.mois =".$mois_selected." AND WPLOYERS.annee =".$annee_selected." )".
		$moreQuery.
		"ORDER BY ville, adresse"
		);

		/*	Get the query result regarding on the function parameters. If there must be only one result or a collection	*/
		if($elementId == '')
		{
			$elements = $wpdb->get_results($query);
		}
		else
		{
			$elements = $wpdb->get_row($query);
		}
		
		return $elements;
	}

	function getVilles()
	{
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT DISTINCT ".wplazare_apparts::getDbTable().".ville AS ville ".
		"FROM ".wplazare_locations::getDbTable()." ".
		"LEFT JOIN ".wplazare_apparts::getDbTable()." ON ".wplazare_locations::getDbTable().".appartement=".wplazare_apparts::getDbTable().".id ".
		"ORDER BY ville"
		);
		return $wpdb->get_results($query);
	}
	
	
	function getEtats()
	{
		$elements = array(); 
		$elements[] = array('name' => 'Pay&eacute;', 'value' => 1);
		$elements[] = array('name' => 'Non pay&eacute;', 'value' => 0);

		return $elements;
	}

	function getApparts(){
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT DISTINCT ".wplazare_apparts::getDbTable().".adresse AS adresse ".
		"FROM ".wplazare_locations::getDbTable()." ".
		"LEFT JOIN ".wplazare_apparts::getDbTable()." ON ".wplazare_locations::getDbTable().".appartement=".wplazare_apparts::getDbTable().".id ".
		"ORDER BY adresse"
		);
		return $wpdb->get_results($query);
	}

	function getAnnee()
	{
		global $wpdb;
		
		$query = $wpdb->prepare(
		"SELECT MIN(YEAR(".wplazare_locations::getDbTable().".date_debut)) AS first_year ".
		"FROM ".wplazare_locations::getDbTable()
		);
		
		// ajoute l'année actuelle si elle n'est pas dedans
		$res = $wpdb->get_row($query);
		$year = $res->first_year;
		$current_year = date("Y", (current_time("timestamp", 0)));
		$elements = array();
		do {
		    $elements[] = $year;
		    $year++;
		} while ($year <= $current_year);
		
		//$elements_sorted = arsort($elements);
		return $elements;		
	}

	
	function getMois()
	{
		$elements = array ( array( 'value' => 1, 'name' => 'Janvier'),
							array( 'value' => 2, 'name' => 'F&eacute;vrier'),
							array( 'value' => 3, 'name' => 'Mars'),
							array( 'value' => 4, 'name' => 'Avril'),
							array( 'value' => 5, 'name' => 'Mai'),
							array( 'value' => 6, 'name' => 'Juin'),
							array( 'value' => 7, 'name' => 'Juillet'),
							array( 'value' => 8, 'name' => 'Aout'),
							array( 'value' => 9, 'name' => 'Septembre'),
							array( 'value' => 10, 'name' => 'Octobre'),
							array( 'value' => 11, 'name' => 'Novembre'),
							array( 'value' => 12, 'name' => 'D&eacute;cembre')
						); 
		return $elements;
	}
	

	function getLocationsIds()
	{
		global $wpdb;
		
		$query = $wpdb->prepare(
		"SELECT DISTINCT ".wplazare_locations::getDbTable().".id AS location_id ".
		"FROM ".wplazare_locations::getDbTable()." "
		);
		return $wpdb->get_results($query);
	}
	
	function send_rappel_mail($locataire_id)
	{
		$locataire_name = wplazare_tools::getUserName($locataire_id);
		
		$headers = "From: ".get_option('blogname')." <".get_option('admin_email').">\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		//$headers .= "Content-type: multipart/mixed; charset=UTF-8\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";
						
		$message = "Bonjour,<br /><br />";	
		$message .= "Attention, le virement de ";
		$message .= wplazare_tools::getUserLink($locataire_id,$locataire_name);
		$message .= " a &eacute;t&eacute; rejet&eacute;.<br />";
		$message .= "<br />L'&eacute;quipe de ".get_option('blogname');

		$subject = "Loyer de ".$locataire_name;
		
		$template_name ="rappel";
		$pdfator = new wplazare_pdfator();

        $current_location = wplazare_tools::getLocation($locataire_id);
		$appart_id = $current_location->appartement;
        $current_appart = wplazare_apparts::getElement($appart_id);
		$appart_adresse = wplazare_apparts::getAdresseComplete( $current_appart );
        $current_association = wplazare_associations::getElement($current_appart->association);
		$balises_replace = array( array( "balise" => "{U_USERNAME}", "new_text" => $locataire_name),
								 array( "balise" => "{A_ADDRESS}", "new_text" => stripslashes($appart_adresse) ),
								 array( "balise" => "{ASSOCIATION}", "new_text" => stripslashes($current_association->nom) )
								);
		
		$attachments = array( $pdfator->getPdf($template_name, $balises_replace) );		
		
		// envoi au locataire
		$locataire_user_data = get_userdata( $locataire_id );
        $result = "";
		if( ($locataire_user_data != "") && ($locataire_user_data->user_email != "") )
		{
			$locataire_email =  $locataire_user_data->user_email;
			$result = wp_mail( $locataire_email, $subject, $message, $headers/*, $attachments*/);
		}
		
		// envoi au responsable
		$responsables_list = wplazare_tools::getResponsables($locataire_id);
		$responsable_appart_id = $responsables_list->responsable_Appart_id;
		$responsable_user_data = get_userdata( $responsable_appart_id );
		if( ($responsable_user_data != "") && ($responsable_user_data->user_email != "") )
		{
			$responsable_email =  $responsable_user_data->user_email;
			wp_mail( $responsable_email, $subject, $message, $headers/*, $attachments*/);
		}
		
		// envoi au tresorier
		$tresorier_id = wplazare_associations::getTresorier($locataire_id);
		$tresorier_user_data = get_userdata( $tresorier_id );
		if( $tresorier_user_data != "" )
		{
			$tresorier_email =  $tresorier_user_data->user_email;
			wp_mail( $tresorier_email, $subject, $message, $headers/*, $attachments*/);
		}	
		
		$pdfator->clearTempPdf($template_name);
		
		return $locataire_name;
	}
	
	function send_attestation_mail($locataire_id, $current_mois, $current_annee)
	{
		$locataire_name = stripslashes( wplazare_tools::getUserName($locataire_id) );
		
		$headers = "From: ".get_option('blogname')." <".get_option('admin_email').">\r\n";
		$headers .= "MIME-Version: 1.0\r\n";
		//$headers .= "Content-type: multipart/mixed; charset=UTF-8\r\n";
		$headers .= "Content-type: text/html; charset=UTF-8\r\n";


		$subject = "Quittance de ".$locataire_name;
		
		$template_name ="quittance_loyer";
		$pdfator = new wplazare_pdfator();
        $current_location = wplazare_tools::getLocation($locataire_id);
		$appart_id = $current_location->appartement;
		$current_appart = wplazare_apparts::getElement($appart_id);
		$date_entree = date("d/m/Y", strtotime($current_location->date_debut));
        $current_association = wplazare_associations::getElement($current_appart->association);

        $mois = array("","Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Août","Septembre","Octobre","Novembre","Décembre");

        if($current_association->nom == 'Lazare'){
			$association = 'Lazare';
			$nom_president = 'Etienne Villemain';
			$coordonnees_president = 'Tel : 06 63 68 12 31';
			$signature_president = 'sign4.jpg';
			$logo_association = 'lazare.png';
		}
		else{
			$association = 'APA';
			$nom_president = 'Louis Alexandre Grangé';
			$coordonnees_president = 'Tel : 06 75 29 99 34';
			$signature_president = 'sign3.jpg';
			$logo_association = 'apa.png';
		}

        $message = "Bonjour,<br /><br />";
        $message .= "Vous trouverez ci-joint la quittance de vos indemnit&eacute;s d&rsquo;occupation pour ce mois-ci.<br/><br/>Bonne r&eacute;ception,<br/><br/>L&rsquo;&eacute;quipe ".$association;

        $balises_replace = array(
            array( "balise" => "{U_USERNAME}", "new_text" => $locataire_name),
            array( "balise" => "{ASSOCIATION}", "new_text" => $current_association->nom),
            array( "balise" => "{LOGO_ASSOCIATION}", "new_text" => $logo_association),
            array( "balise" => "{ADRESSE}", "new_text" => stripslashes($current_appart->adresse)),
            array( "balise" => "{CODE_POSTAL}", "new_text" => $current_appart->code_postal),
            array( "balise" => "{VILLE}", "new_text" => $current_appart->ville),
            array( "balise" => "{DATE}", "new_text" => date("d - m - Y")),
            array( "balise" => "{NOM_PRESIDENT}", "new_text" => $nom_president),
            array( "balise" => "{COORDONNEES_PRESIDENT}", "new_text" => $coordonnees_president),
            array( "balise" => "{SIGNATURE_PRESIDENT}", "new_text" => $signature_president),
            array( "balise" => "{PATH}", "new_text" => plugins_url( '/wplazare/includes/librairies/html2pdf/templates/') ),
            array( "balise" => "{DATE_ENTREE}", "new_text" => $date_entree ),
            array( "balise" => "{MOIS_ATTESTATION}", "new_text" => $mois[$current_mois] ),
            array( "balise" => "{ANNEE_ATTESTATION}", "new_text" => $current_annee ),
            array( "balise" => "{PRIX_LOYER}", "new_text" => ( $current_appart->prix - $current_location->remise ) )
        );
								
		$attachments = array( $pdfator->getPdf($template_name, $balises_replace) );
		// envoi au locataire ou au responsable
		$locataire_user_data = get_userdata( $locataire_id );
		if( ($locataire_user_data) && ($locataire_user_data->user_email != "") )
		{
			$destinataire_email =  $locataire_user_data->user_email;
		}
		else{
			$responsable_user_data = get_userdata( $current_appart->responsable );
            $destinataire_email =  $responsable_user_data->user_email;
		}
        wp_mail( $destinataire_email, $subject, $message, $headers, $attachments);
				
		$pdfator->clearTempPdf($template_name);
		
		return $locataire_name;
	}	
	
}