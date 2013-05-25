<?php
/**
* Offers management utilities
* 
* Define the method and element to manage the different offers
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*	@since v1.1
*/

/**
* Define the method and element to manage the different offers
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_offers
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wplazare_offers';
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
		return WPLAZARE_URL_SLUG_OFFERS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_OFFERS_EDITION;
	}
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
	{
		return WPLAZARE_DBT_OFFERS;
	}

	/**
	*	Define the title of the page 
	*
	*	@return string $title The title of the page looking at the environnement
	*/
	function pageTitle()
	{
		$action = isset($_REQUEST['action']) ? wplazare_tools::varSanitizer($_REQUEST['action']) : '';
		$objectInEdition = isset($_REQUEST['id']) ? wplazare_tools::varSanitizer($_REQUEST['id']) : '';

		$title = __('Liste des offres', 'wplazare' );
		if($action != '')
		{
			if($action == 'edit')
			{
				$editedItem = wplazare_offers::getElement($objectInEdition);
				$title = __('&Eacute;diter l\'offre', 'wplazare');
			}
			elseif($action == 'add')
			{
				$title = __('Ajouter une offre', 'wplazare');
			}
		}
		return $title;
	}

	/**
	*	Define the different message and action after an action is send through the element interface
	*
	*	@return string $actionResultMessage The message to output after an action is launched to advise the user what append
	*/
	function elementAction()
	{
		global $wpdb;
		global $id;
		$actionResultMessage = '';

		$pageMessage = $actionResult = '';
		$pageAction = isset($_REQUEST[wplazare_offers::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_offers::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wplazare_offers::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_offers::getDbTable()]['id']) : '';

		/*	Change the storage format for the amount	*/
		$_REQUEST[wplazare_offers::getDbTable()]['payment_amount'] = ($_REQUEST[wplazare_offers::getDbTable()]['payment_amount'] * 100);
		$_REQUEST[wplazare_offers::getDbTable()]['payment_recurrent_amount'] = ($_REQUEST[wplazare_offers::getDbTable()]['payment_recurrent_amount'] * 100);

		/*	Define the database operation type from action launched by the user	 */
		/*************************				GENERIC				**************************/
		/*************************************************************************/
		if(($pageAction != '') && (($pageAction == 'edit') || ($pageAction == 'editandcontinue') || ($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_edit_offers'))
			{
				$_REQUEST[wplazare_offers::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				if($pageAction == 'delete')
				{
					if(current_user_can('wplazare_delete_offers'))
					{
						$_REQUEST[wplazare_offers::getDbTable()]['status'] = 'deleted';
					}
					else
					{
						$actionResult = 'userNotAllowedForActionDelete';
					}
				}
				$actionResult = wplazare_database::update($_REQUEST[wplazare_offers::getDbTable()], $id, wplazare_offers::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionEdit';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_delete_offers'))
			{
				$_REQUEST[wplazare_offers::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wplazare_offers::getDbTable()]['status'] = 'deleted';
				$actionResult = wplazare_database::update($_REQUEST[wplazare_offers::getDbTable()], $id, wplazare_offers::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionDelete';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'save') || ($pageAction == 'saveandcontinue') || ($pageAction == 'add')))
		{
			if(current_user_can('wplazare_add_offers'))
			{
				$_REQUEST[wplazare_offers::getDbTable()]['creation_date'] = date('Y-m-d H:i:s');
				$actionResult = wplazare_database::save($_REQUEST[wplazare_offers::getDbTable()], wplazare_offers::getDbTable());
				$id = $wpdb->insert_id;
			}
			else
			{
				$actionResult = 'userNotAllowedForActionAdd';
			}
		}

		/*	When an action is launched and there is a result message	*/
		/************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
		/************		CHANGE ERROR MESSAGE FOR SPECIFIC CASE					*************/
		/****************************************************************************/
		if($actionResult != '')
		{
			$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wplazare_offers::getDbTable()]['name'] . '</span>';
			if($actionResult == 'error')
			{/*	CHANGE HERE FOR SPECIFIC CASE	*/
				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />' . sprintf(__('Une erreur est survenue lors de l\'enregistrement de %s', 'wplazare'), $elementIdentifierForMessage);
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
				$actionResultMessage = '<img src="' . WPLAZARE_SUCCES_ICON . '" alt="action success" class="wplazarePageMessage_Icon" />' . sprintf(__('L\'enregistrement de %s s\'est d&eacute;roul&eacute; avec succ&eacute;s', 'wplazare'), $elementIdentifierForMessage);
			}
			elseif(($actionResult == 'userNotAllowedForActionEdit') || ($actionResult == 'userNotAllowedForActionAdd') || ($actionResult == 'userNotAllowedForActionDelete'))
			{
				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />' . __('Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action.', 'wplazare');
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
		global $currencyIconList;
		$listItemOutput = '';

		/*	Start the table definition	*/
		$tableId = wplazare_offers::getDbTable() . '_list';
		$tableSummary = __('Existing payment forms listing', 'wplazare');
		$tableTitles = array();
		$tableTitles[] = __('Libell&eacute; de l\'offre', 'wplazare');
		$tableTitles[] = __('Type d\'offre', 'wplazare');
		$tableTitles[] = __('Montant', 'wplazare');
		$tableTitles[] = __('R&eacute;f&eacute;rence', 'wplazare');
		$tableTitles[] = __('Abonnement', 'wplazare');
		$tableClasses = array();
		$tableClasses[] = 'wplazare_' . wplazare_offers::getCurrentPageCode() . '_name_column';
		$tableClasses[] = 'wplazare_' . wplazare_offers::getCurrentPageCode() . '_payment_type_column';
		$tableClasses[] = 'wplazare_' . wplazare_offers::getCurrentPageCode() . '_payment_amount_column';
		$tableClasses[] = 'wplazare_' . wplazare_offers::getCurrentPageCode() . '_payment_ref_column';
		$tableClasses[] = 'wplazare_' . wplazare_offers::getCurrentPageCode() . '_payment_recurrent_column';

		$line = 0;
		$elementList = wplazare_offers::getElement();
		if(count($elementList) > 0)
		{
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wplazare_offers::getDbTable() . '_' . $element->id;

				$elementLabel = $element->payment_name;
				$subRowActions = '';
				if(current_user_can('wplazare_edit_offers'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_offers::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Modifier', 'wplazare') . '</a>';
					$elementLabel = '<a href="' . $editAction . '" >' . $element->payment_name  . '</a>';
				}
				elseif(current_user_can('wplazare_view_offers_details'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_offers::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wplazare') . '</a>';
					$elementLabel = '<a href="' . $editAction . '" >' . $element->payment_name  . '</a>';
				}
				if(current_user_can('wplazare_delete_offers'))
				{
					if($subRowActions != '')
					{
						$subRowActions .= '&nbsp;|&nbsp;';
					}
					$subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wplazare_offers::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id). '" >' . __('Supprimer', 'wplazare') . '</a>';
				}
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wplazareRowAction" >' . $subRowActions . '
	</div>';

				/*	Display the amount in the goos shape	*/
				$elementAmount = ($element->payment_amount / 100);

				/*	Check if the payement is single or multiple in order to show the parameters	*/
				$recurrentParams = '-';
				if($element->payment_type == 'multiple_payment')
				{
					$recurrent_payment_amount = $element->payment_recurrent_amount;
					if($recurrent_payment_amount == 0)
					{
						$recurrent_payment_amount = $element->payment_amount;
					}
					$recurrentParams = sprintf(__('%d pr&eacute;l&egrave;vements de %s %s'), $element->payment_recurrent_number, ($recurrent_payment_amount / 100), $currencyIconList[$element->payment_currency]);
				}

				unset($tableRowValue);
				$tableRowValue[] = array('class' => wplazare_offers::getCurrentPageCode() . '_name_cell', 'value' => $elementLabel . $rowActions);
				$tableRowValue[] = array('class' => wplazare_offers::getCurrentPageCode() . '_payment_type_cell', 'value' => __($element->payment_type, 'wplazare'));
				$tableRowValue[] = array('class' => wplazare_offers::getCurrentPageCode() . '_paymnet_amount_cell', 'value' => $elementAmount . '&nbsp;' . $currencyIconList[$element->payment_currency]);
				$tableRowValue[] = array('class' => wplazare_offers::getCurrentPageCode() . '_paymnet_reference_cell', 'value' => $element->payment_reference_prefix);
				$tableRowValue[] = array('class' => wplazare_offers::getCurrentPageCode() . '_paymnet_recurrent_cell', 'value' => $recurrentParams);
				$tableRows[] = $tableRowValue;

				$line++;
			}
		}
		else
		{
			$subRowActions = '';
			if(current_user_can('wplazare_add_offers'))
			{
				$subRowActions .= '
	<a href="' . admin_url('admin.php?page=' . wplazare_offers::getEditionSlug() . '&amp;action=add') . '" >' . __('Ajouter', 'wplazare') . '</a>';
			}
			$rowActions = '
	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
			$tableRowsId[] = wplazare_offers::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wplazare_offers::getCurrentPageCode() . '_name_cell', 'value' => __('Aucune offre n\'a encore &eacute;t&eacute; cr&eacute;&eacute;', 'wplazare') . $rowActions);
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $listItemOutput;
	}

    /**
     *    Return the page content to add a new item
     *
     * @param string $itemToEdit
     * @return string The html code that output the interface for adding a nem item
     */
	function elementEdition($itemToEdit = '')
	{
		global $currencyList;
		$dbFieldList = wplazare_database::fields_to_input(wplazare_offers::getDbTable());
		
		$editedItem = '';
		$mandatoryFieldList = array();
		if($itemToEdit != '')
		{
			$editedItem = wplazare_offers::getElement($itemToEdit);
			$mandatoryFieldList = unserialize($editedItem->payment_form_mandatory_fields);
		}

		$the_form_content_hidden = $the_form_general_content = '';
		$newOfferForm = $newOfferFormMultiple = '';
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];

			$pageAction = isset($_REQUEST[wplazare_offers::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_offers::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wplazare_offers::getDbTable()][$input_name]) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_offers::getDbTable()][$input_name]) : '';
			$currentFieldValue = $input_value;
			if(is_object($editedItem))
			{
				$currentFieldValue = $editedItem->$input_name;
			}
			elseif(($pageAction != '') && ($requestFormValue != ''))
			{
				$currentFieldValue = $requestFormValue;
			}

			if(($input_name == 'creation_date') || ($input_name == 'last_update_date'))
			{
				$input_def['type'] = 'hidden';
			}

			$input_def['value'] = $currentFieldValue;

			if($input_def['name'] == 'payment_currency')
			{
				$input_def['type'] = 'select';
				$input_def['possible_value'] = $currencyList;
				$input_def['valueToPut'] = 'index';
			}
			elseif($input_def['name'] == 'payment_amount')
			{
				$input_def['value'] = ($currentFieldValue / 100);
			}

			switch($input_def['name'])
			{
				case 'payment_name';
					$helpForField = '<div class="wplazareFormFieldHelp" >' . __('Permet de localiser une offre rapidement. N\'a aucune incidence sur le paiment en lui m&ecirc;me', 'wplazare') . '</div>';
				break;
				case 'payment_reference_prefix';
					$helpForField = '<div class="wplazareFormFieldHelp" >' . __('D&eacute;finit une pr&eacute;fixe pour l\'offre. Permet d\'obtenir un identifiant unique par transaction', 'wplazare') . '</div>';
				break;
				case 'payment_recurrent_amount';
					$helpForField = '<div class="wplazareFormFieldHelp" >' . __('Si le montant est le m&ecirc;me que le premier paiement, mettre 0 (z&eacute;ro)', 'wplazare') . '</div>';
				break;
				case 'payment_recurrent_frequency';
					$helpForField = '<div class="wplazareFormFieldHelp" >' . __('En mois', 'wplazare') . '</div>';
				break;
				case 'payment_recurrent_day_of_month';
					$helpForField = '<div class="wplazareFormFieldHelp" >' . __('Si le pr&eacute;l&egrave;vement a lieu le m&ecirc;me jour que le premier paiement, mettre 0 (z&eacute;ro)', 'wplazare') . '</div>';
				break;
				case 'payment_recurrent_start_delay';
					$helpForField = '<div class="wplazareFormFieldHelp" >' . __('Nombre de jours avant l\'ex&eacute;cution du paiement (0 pour un d&eacute;but imm&eacute;diat)', 'wplazare') . '</div>';
				break;
				default:
					$helpForField = '';
				break;
			}

			$the_input = wplazare_form::check_input_type($input_def, wplazare_offers::getDbTable());
			$newOfferFormInput = '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_offers::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
				<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
				' . $helpForField . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_offers::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
				' . $the_input . '
			</div>
		</div>';


			if($input_def['type'] == 'hidden')
			{
				$the_form_content_hidden .= '
		' . $the_input;
			}
			else
			{
				if(substr($input_def['name'], 0, 18) == 'payment_recurrent_')
				{
					$newOfferFormMultiple .= str_replace('wplazare_form_input_offer', 'wplazare_form_input_offer_multiple', $newOfferFormInput);
				}
				else
				{
					$newOfferForm .= $newOfferFormInput;
				}
			}
		}

		$the_form_general_content .= $newOfferForm . '
		<fieldset class="clear wplazareHide" id="wplazareMultiplePaymentFieldContainer" >
			<legend>' . __('Param&egrave;tres pour les abonnements', 'wplazare') . '</legend>
			' . $newOfferFormMultiple . '
		</fieldset>';

		/*	Define the different action available for the edition form	*/
		$formAddAction = admin_url('admin.php?page=' . wplazare_offers::getEditionSlug() . '&amp;action=edit');
		$formEditAction = admin_url('admin.php?page=' . wplazare_offers::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit);
		$formAction = $formAddAction;
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit'))
		{
			$formAction = $formEditAction;
		}

		$the_form = '
<form name="' . wplazare_offers::getDbTable() . '_form" id="' . wplazare_offers::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data" >
' . wplazare_form::form_input(wplazare_offers::getDbTable() . '_action', wplazare_offers::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
' . wplazare_form::form_input(wplazare_offers::getDbTable() . '_form_has_modification', wplazare_offers::getDbTable() . '_form_has_modification', 'no' , 'hidden') . '
<div id="wplazareFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wplazare_' . wplazare_offers::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wplazare(document).ready(function(){
		wplazareMainInterface("' . wplazare_offers::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_offers::getEditionSlug()) . '");

		wplazareFormsInterface();

		wplazare("#delete").click(function(){
			wplazare("#' . wplazare_offers::getDbTable() . '_action").val("delete");
			deletePaymentForm();
		});
		if(wplazare("#' . wplazare_offers::getDbTable() . '_action").val() == "delete"){
			deletePaymentForm();
		}
		function deletePaymentForm(){
			if(confirm(wplazareConvertAccentTojs("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?', 'wplazare') . '"))){
				wplazare("#' . wplazare_offers::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_offers::getDbTable() . '_action").val("edit");
			}
		}
	});
</script>';

		return $the_form;
	}
	/**
	*	Return the different button to save the item currently being added or edited
	*
	*	@return string $currentPageButton The html output code with the different button to add to the interface
	*/
	function getPageFormButton()
	{
		$action = isset($_REQUEST['action']) ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'add';
		$currentPageButton = '';

		if($action == 'add')
		{
			if(current_user_can('wplazare_add_offers'))
			{
				$currentPageButton .= '<input type="button" class="button-primary" id="add" name="add" value="' . __('Ajouter', 'wplazare') . '" />';
			}
		}
		elseif(current_user_can('wplazare_edit_offers'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="' . __('Enregistrer', 'wplazare') . '" /><input type="button" class="button-primary" id="saveandcontinue" name="saveandcontinue" value="' . __('Enregistrer et continuer l\'&eacute;dition', 'wplazare') . '" />';
		}
		if(current_user_can('wplazare_delete_offers') && ($action != 'add'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="' . __('Supprimer', 'wplazare') . '" />';
		}

		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_offers::getListingSlug()) . '" class="button add-new-h2" >' . __('Retour', 'wplazare') . '</a></h2>';

		return $currentPageButton;
	}
	/**
	*	Get the existing element list into database
	*
	*	@param integer $elementId optionnal The element identifier we want to get. If not specify the entire list will be returned
	*	@param string $elementStatus optionnal The status of element to get into database. Default is set to valid element
	*
	*	@return object $elements A wordpress database object containing the element list
	*/
	function getElement($elementId = '', $elementStatus = "'valid', 'moderated'")
	{
		global $wpdb;
		$elements = array();
		$moreQuery = "";

		if($elementId != '')
		{
			$moreQuery = "
			AND WPBOFFERS.id = '" . $elementId . "' ";
		}

		$query = $wpdb->prepare(
		"SELECT WPBOFFERS.*
		FROM " . wplazare_offers::getDbTable() . " AS WPBOFFERS
		WHERE WPBOFFERS.status IN (".$elementStatus.") " . $moreQuery
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

	/**
	*	Get the different offers associated to a form
	*	
	*	@return object A wordpress obje
	*/
	function getOffersOfForm($formIdentifier)
	{
		global $wpdb;

		$query = $wpdb->prepare(
			"SELECT LINK_OFFERS_FORMS.id AS LINK_ID, LINK_OFFERS_FORMS.offer_id, LINK_OFFERS_FORMS.offer_title, OFFERS.*
			FROM " . WPLAZARE_DBT_LINK_FORMS_OFFERS . " AS LINK_OFFERS_FORMS
				INNER JOIN " . wplazare_offers::getDbTable() . " AS OFFERS ON (OFFERS.id = LINK_OFFERS_FORMS.offer_id)
			WHERE LINK_OFFERS_FORMS.form_id = %s
				AND LINK_OFFERS_FORMS.status = 'valid' ", $formIdentifier);
		$offersForForm = $wpdb->get_results($query);

		return $offersForForm;
	}

	/**
	*	Create the html output code for 
	*
	*	@return string $offerList The html code of the offer list
	*/
	function getOfferListOutput($formIdentifier)
	{
		global $currencyIconList;
		$offerList = $offer = $associatedOfferList = $associatedOffersListText = '';

		/*	Get the already associated offer	*/
		$associatedOffers = wplazare_offers::getOffersOfForm($formIdentifier);
		$storedOffers = array();
		$storedOffersInfos = array();
		foreach($associatedOffers as $associatedOffer)
		{
			$associatedOffersListText .= $associatedOffer->offer_id . ', ';
			$storedOffers[] = $associatedOffer->offer_id;
			$storedOffersInfos[$associatedOffer->offer_id] = $associatedOffer;
		}

		/*	Get the existing offer list	*/
		$existingOfferList = wplazare_offers::getElement('', "'valid'");
		if(count($existingOfferList) > 0)
		{
			$offerList = array();
			$jsOfferList = '';
			foreach($existingOfferList as $offer)
			{
				$multiple_payment_offer = __('Paiement unique', 'wplazare');
				if($offer->payment_type == 'multiple_payment')
				{
					$recurrentAmount = $offer->payment_recurrent_amount;
					if($offer->payment_recurrent_amount == 0)
					{
						$recurrentAmount = $offer->payment_amount;
					}
					$multiple_payment_offer = sprintf(__('Abonnement: %s&nbsp;%s; %s&nbsp;fois', 'wplazare'), ($recurrentAmount / 100),  $currencyIconList[$offer->payment_currency], $offer->payment_recurrent_number);
				}
				$offerList[$offer->id] = $offer->payment_name . '&nbsp;&nbsp;(' . ($offer->payment_amount / 100) . '&nbsp;' . $currencyIconList[$offer->payment_currency] . '&nbsp;' . $multiple_payment_offer . ')';
				$jsOfferList .= 'offerList["' . $offer->id . '"] = "<input class=\"offerTitleinput\" type=\"text\" value=\"' . wplazare_offers::generateOfferTitle($offer) . '\" name=\"associatedOfferTitle[' . $offer->id . ']\" id=\"offerTitle' . $offer->id . '\" />";';

				if(in_array($offer->id, $storedOffers))
				{
					$associatedOfferList .= '<div id="selectedOffer' . $offer->id . '" >';
					if(current_user_can('wplazare_delete_forms_offers_link'))
					{
						$associatedOfferList .= '<span id="offer' . $offer->id . '" title="' . __('Supprimer l\'association enter l\'offre et le formulaire', 'wplazare') . '" class="deleteOfferAssociation ui-icon alignleft" >&nbsp;</span>&nbsp;';
					}
					$offerTitle = wplazare_offers::generateOfferTitle($offer);
					if($storedOffersInfos[$offer->id] != '')
					{
						$offerTitle = $storedOffersInfos[$offer->id]->offer_title;
					}
					$associatedOfferList .= '<input class="offerTitleinput" type="text" value="' . $offerTitle . '" name="associatedOfferTitle[' . $offer->id . ']" id="offerTitle' . $offer->id . '" /></div>';
				}
			}

			$input_def['name'] = 'existingOffers';
			$input_def['type'] = 'select';
			$input_def['possible_value'] = $offerList;
			$input_def['valueToPut'] = 'index';
			$offer = wplazare_form::check_input_type($input_def, wplazare_offers::getDbTable()) . '<input type="button" class="button-secondary" name="wplazareAssociateOffer" value="' . __('Associer cette offre', 'wplazare') . '" id="wplazareAssociateOffer" />
		<script type="text/javascript" >
			var offerList = new Array();
			' . $jsOfferList . '
		</script>';
		}
		else
		{
			$offer = __('Aucune offre n\'a &eacute;t&eacute; d&eacute;finie pour le moment. Pour en cr&eacute;er une, rendez-vous dans le menu "Offres" &agrave; gauche', 'wplazare');
		}

		$offerList = '
		<div class="clear" >&nbsp;</div>
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_offers::getCurrentPageCode() . '_label alignleft" >
				<label >' . __('Liste des offres', 'wplazare') . '</label>
				<div class="wplazareFormFieldHelp" >' . __('Un formulaire peut contenir une ou plusieurs offres que le client pourra choisir', 'wplazare') . '</div>
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_offers::getCurrentPageCode() . '_input alignleft" >';
			if(current_user_can('wplazare_view_forms_offers_link'))
			{
				$offerList .= '
				<input type="hidden" name="associatedOfferList" id="associatedOfferList" value="' . $associatedOffersListText . '" />
				<div id="associatedOfferListOutput" class="associated_offer_list" >' . $associatedOfferList . '</div>
				<br/>
				<div id="existingOfferListContainer" >' . $offer . '</div>';
			}
			else
			{
				$offerList .= '
				' . __('Vous ne disposez pas des droits suffisant pour visualiser la liste des offres affect&eacute;es &agrave; ce formulaire', 'wplazare');
			}
			$offerList .= '
			</div>
		</div>';

		return $offerList;
	}

	/**
	*	Generate a title for an offer
	*
	*	@param object $offer A wordpresse database objet containing informations about the offer to generate the title for
	*
	*	@return string $offer_title The title for the offer
	*/
	function generateOfferTitle($offer)
	{
		global $currencyIconList;

		/*	In case of single payment offer	*/
		$offer_title = sprintf(__('%s &agrave; %s %s', 'wplazare'), $offer->payment_name, ($offer->payment_amount / 100), $currencyIconList[$offer->payment_currency]);

		/*	In case of multiple payement offer	*/
		if($offer->payment_type == 'multiple_payment')
		{
			$frequency = '';
			if($frequency > 1)
			{
				$frequency = $offer->payment_recurrent_frequency;
			}
			$recurrent_amount = $offer->payment_recurrent_amount;
			if($recurrent_amount == 0)
			{
				$recurrent_amount = ($offer->payment_amount / 100);
			}
			$offer_title = sprintf(__('%s &agrave; %s %s, puis pr&eacute;l&eacute;vement de %s %s tous les %s mois pendant %s mois', 'wplazare'), $offer->payment_name, ($offer->payment_amount / 100), $currencyIconList[$offer->payment_currency], $recurrent_amount, $currencyIconList[$offer->payment_currency], $frequency, $offer->payment_recurrent_number);
		}

		return $offer_title;
	}
}