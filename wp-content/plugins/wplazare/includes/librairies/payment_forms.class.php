<?php
/**
* Payment form utilities
* 
* Define the method and element to manage the different payment form
* @author Eoxia <dev@eoxia.com>
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Define the method and element to manage the different payment form
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_payment_form
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
    public static function getCurrentPageCode()
	{
		return 'payment_form';
	}	
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
    public static function getPageIcon()
	{
		return '';
	}	
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
    public static function getListingSlug()
	{
		return WPLAZARE_URL_SLUG_FORMS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
    public static function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_FORMS_EDITION;
	}
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
    public static function getDbTable()
	{
		return WPLAZARE_DBT_FORMS;
	}

	/**
	*	Define the title of the page 
	*
	*	@return string $title The title of the page looking at the environnement
	*/
    public static function pageTitle()
	{
		$action = isset($_REQUEST['action']) ? wplazare_tools::varSanitizer($_REQUEST['action']) : '';
		$objectInEdition = isset($_REQUEST['id']) ? wplazare_tools::varSanitizer($_REQUEST['id']) : '';

		$title = __('Liste des formulaires', 'wplazare' );
		if($action != '')
		{
			if($action == 'edit')
			{
				$editedItem = wplazare_payment_form::getElement($objectInEdition);
				$title = __('&Eacute;diter le formulaire', 'wplazare');
			}
			elseif($action == 'add')
			{
				$title = __('Ajouter un formulaire', 'wplazare');
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
		$pageAction = isset($_REQUEST[wplazare_payment_form::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_payment_form::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wplazare_payment_form::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_payment_form::getDbTable()]['id']) : '';

		/*	Add the list of mandatory field in serialsed array shape	*/
		$_POST['user_mandatory_fields']['user_email'] = 'user_email';
		$_REQUEST[wplazare_payment_form::getDbTable()]['payment_form_mandatory_fields'] = serialize($_POST['user_mandatory_fields']);

		/*	Define the database operation type from action launched by the user	 */
		/*************************				GENERIC				**************************/
		/*************************************************************************/
		if(($pageAction != '') && (($pageAction == 'edit') || ($pageAction == 'editandcontinue') || ($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_edit_forms'))
			{
				$_REQUEST[wplazare_payment_form::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				if($pageAction == 'delete')
				{
					if(current_user_can('wplazare_delete_forms'))
					{
						$_REQUEST[wplazare_payment_form::getDbTable()]['status'] = 'deleted';
					}
					else
					{
						$actionResult = 'userNotAllowedForActionDelete';
					}
				}
				$actionResult = wplazare_database::update($_REQUEST[wplazare_payment_form::getDbTable()], $id, wplazare_payment_form::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionEdit';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_delete_forms'))
			{
				$_REQUEST[wplazare_payment_form::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wplazare_payment_form::getDbTable()]['status'] = 'deleted';
				$actionResult = wplazare_database::update($_REQUEST[wplazare_payment_form::getDbTable()], $id, wplazare_payment_form::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionDelete';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'save') || ($pageAction == 'saveandcontinue') || ($pageAction == 'add')))
		{
			if(current_user_can('wplazare_add_forms'))
			{
				$_REQUEST[wplazare_payment_form::getDbTable()]['creation_date'] = date('Y-m-d H:i:s');
				$actionResult = wplazare_database::save($_REQUEST[wplazare_payment_form::getDbTable()], wplazare_payment_form::getDbTable());
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
			$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wplazare_payment_form::getDbTable()]['payment_form_name'] . '</span>';
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
				if(isset($_REQUEST['associatedOfferList']) && ($_REQUEST['associatedOfferList'] != ''))
				{
					/*	Get and read the new offer list to associate to the form	*/
					$offersToAssociate = explode(', ', $_REQUEST['associatedOfferList']);

					/*	Get the already associated to check if there are no element to unassociate before associate new one	*/
					$associatedOffers = wplazare_offers::getOffersOfForm($id);
					$storedOffers = array();
					foreach($associatedOffers as $associatedOffer)
					{
						$storedOffers[] = $associatedOffer->offer_id;
						if((!isset($offersToAssociate) && !is_array($offersToAssociate)) || !in_array($associatedOffer->offer_id, $offersToAssociate))
						{
							$associateNewOffer['status'] = 'deleted';
							$associateNewOffer['last_update_date'] = date('Y-m-d H:i:s');
							$actionResult = wplazare_database::update($associateNewOffer, $associatedOffer->LINK_ID, WPLAZARE_DBT_LINK_FORMS_OFFERS);
						}
					}

					foreach($offersToAssociate as $offerId)
					{
						if(($offerId > 0) && (!in_array($offerId, $storedOffers)))
						{
							$associateNewOffer['id'] = '';
							$associateNewOffer['status'] = 'valid';
							$associateNewOffer['creation_date'] = date('Y-m-d H:i:s');
							$associateNewOffer['form_id'] = $id;
							$associateNewOffer['offer_id'] = $offerId;
							$actionResult = wplazare_database::save($associateNewOffer, WPLAZARE_DBT_LINK_FORMS_OFFERS);
						}

						/*	Define a specific title for the offer in this form	*/
						if($offerId > 0)
						{
							$offerLinkToChangeToTitle = wplazare_offers::getElement($offerId, "'valid'");
							$associateOffer = array();
							if(isset($_REQUEST['associatedOfferTitle'][$offerLinkToChangeToTitle->id]) && ($_REQUEST['associatedOfferTitle'][$offerLinkToChangeToTitle->id] != ''))
							{
								$associateOffer['offer_title'] = $_REQUEST['associatedOfferTitle'][$offerLinkToChangeToTitle->id];
							}
							$associateOffer['last_update_date'] = date('Y-m-d H:i:s');
							$query = $wpdb->prepare("SELECT id FROM " . WPLAZARE_DBT_LINK_FORMS_OFFERS . " WHERE offer_id = '" . $offerId . "' AND form_id = '" . $id . "' AND status = 'valid' ");
							$linkOfferForm = $wpdb->get_row($query);
							$actionResult = wplazare_database::update($associateOffer, $linkOfferForm->id, WPLAZARE_DBT_LINK_FORMS_OFFERS);
						}
					}
				}
				else
				{/*	In case that we delete all the offer of the form	*/
					/*	Get the already associated to check if there are no element to unassociate before associate new one	*/
					$associatedOffers = wplazare_offers::getOffersOfForm($id);

					foreach($associatedOffers as $associatedOffer)
					{
						$associateNewOffer['status'] = 'deleted';
						$associateNewOffer['last_update_date'] = date('Y-m-d H:i:s');
						$actionResult = wplazare_database::update($associateNewOffer, $associatedOffer->LINK_ID, WPLAZARE_DBT_LINK_FORMS_OFFERS);
					}
				}

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
		$tableId = wplazare_payment_form::getDbTable() . '_list';
		$tableSummary = __('Existing payment forms listing', 'wplazare');
		$tableTitles = array();
		$tableTitles[] = __('Nom du formulaire', 'wplazare');
		$tableClasses = array();
		$tableClasses[] = 'wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_name_column';

		$line = 0;
		$elementList = wplazare_payment_form::getElement();
		if(count($elementList) > 0)
		{
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wplazare_payment_form::getDbTable() . '_' . $element->id;

				$elementLabel = $element->payment_form_name;
				$subRowActions = '';
				if(current_user_can('wplazare_edit_forms'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_payment_form::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Modifier', 'wplazare') . '</a>';
					$elementLabel = '<a href="' . $editAction . '" >' . $element->payment_form_name  . '</a>';
				}
				elseif(current_user_can('wplazare_view_forms_details'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_payment_form::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wplazare') . '</a>';
					$elementLabel = '<a href="' . $editAction . '" >' . $element->payment_form_name  . '</a>';
				}
				if(current_user_can('wplazare_delete_forms'))
				{
					if($subRowActions != '')
					{
						$subRowActions .= '&nbsp;|&nbsp;';
					}
					$subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wplazare_payment_form::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id). '" >' . __('Supprimer', 'wplazare') . '</a>';
				}
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wplazareRowAction" >' . $subRowActions . '
	</div>';

				$elementAmount = $element->initial_amount / 100;
				unset($tableRowValue);
				$tableRowValue[] = array('class' => wplazare_payment_form::getCurrentPageCode() . '_label_cell', 'value' => $elementLabel . $rowActions);
				$tableRows[] = $tableRowValue;

				$line++;
			}
		}
		else
		{
			$subRowActions = '';
			if(current_user_can('wplazare_add_forms'))
			{
				$subRowActions .= '
	<a href="' . admin_url('admin.php?page=' . wplazare_payment_form::getEditionSlug() . '&amp;action=add') . '" >' . __('Ajouter', 'wplazare') . '</a>';
			}
			$rowActions = '
	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
			$tableRowsId[] = wplazare_payment_form::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wplazare_payment_form::getCurrentPageCode() . '_name_cell', 'value' => __('Aucun formulaire n\'a encore &eacute;t&eacute; cr&eacute;&eacute;', 'wplazare') . $rowActions);
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $listItemOutput;
	}
	/**
	*	Return the page content to add a new item
	*
	*	@return string The html code that output the interface for adding a nem item
	*/
	function elementEdition($itemToEdit = '')
	{
		$dbFieldList = wplazare_database::fields_to_input(wplazare_payment_form::getDbTable());

		$editedItem = '';
		$mandatoryFieldList = array();
		if($itemToEdit != '')
		{
			$editedItem = wplazare_payment_form::getElement($itemToEdit);
			$mandatoryFieldList = unserialize($editedItem->payment_form_mandatory_fields);
		}

		$the_form_content_hidden = $the_form_general_content = '';
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];

			$pageAction = isset($_REQUEST[wplazare_payment_form::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_payment_form::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wplazare_payment_form::getDbTable()][$input_name]) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_payment_form::getDbTable()][$input_name]) : '';
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
			$the_input = wplazare_form::check_input_type($input_def, wplazare_payment_form::getDbTable());

			$helpForField = '';
			if($input_name == 'initial_amount')
			{
				$helpForField = '<div class="wplazareFormFieldHelp" >' . __('Le montant est exprim&eacute; en centimes.<br/>exemple: pour 1&euro; mettre 100', 'wplazare') . '</div>';
			}

			if(($input_name != 'payment_form_mandatory_fields'))
			{
				if(($input_def['type'] != 'hidden'))
				{
					$label = 'for="' . $input_name . '"';
					if(($input_def['type'] == 'radio') || ($input_def['type'] == 'checkbox'))
					{
						$label = '';
					}
					$input = '
			<div class="clear" >
				<div class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					<label ' . $label . ' >' . __($input_name, 'wplazare') . '</label>
					' . $helpForField . '
				</div>
				<div class="wplazare_form_input wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
					' . $the_input . '
				</div>
			</div>';
					if(($editedItem->is_default != 'yes') || (($editedItem->is_default == 'yes') && ($input_name != 'status')))
					{
						$the_form_general_content .= $input;
					}
				}
				else
				{
					$the_form_content_hidden .= '
			' . $the_input;
				}
			}
			else
			{
				/*	Get the fields from the order table concerning the user	*/
				$dbFieldList = wplazare_database::fields_to_input(WPLAZARE_DBT_ORDERS);

				$userFieldList = '';
				foreach($dbFieldList as $input_key => $input_def)
				{
					$input_def['option'] = '';
					$input_def['type'] = 'checkbox';
					if(substr($input_def['name'], 0, 5) == 'user_')
					{
						if(in_array($input_def['name'], $mandatoryFieldList))
						{
							$input_def['value'] = $input_def['name'];
						}
						if($input_def['name'] == 'user_email')
						{
							$input_def['value'] = $input_def['name'];
							$input_def['option'] .= ' disabled="disabled" ';
						}
						$input_def['possible_value'] = $input_def['name'];
						$inputOutputName = $input_def['name'] . '_admin_side';
						$the_input = wplazare_form::check_input_type($input_def, 'user_mandatory_fields');
					$userFieldList .=  
	'	<div class="clear" >
			' . $the_input . '
			<label class=" wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_label" for="' . $input_def['name'] . '" >' . __($inputOutputName, 'wplazare') . '</label>
		</div>
	';
					}
				}
			
				$helpForField = '<div class="wplazareFormFieldHelp" >' . __('Cochez les champs que vous souhaitez d&eacute;finir comme obligatoire pour ce formulaire', 'wplazare') . '</div>';
				$the_form_general_content .= '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				<label >' . __($input_name, 'wplazare') . '</label>
				' . $helpForField . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $userFieldList . '
			</div>
		</div>';
			}
		}

		/*	Add the offer list for the form	*/
		{
			/*	get the offer list	*/
			$the_form_general_content .= wplazare_offers::getOfferListOutput($itemToEdit);
		}

		/*	Define the different action available for the edition form	*/
		$formAddAction = admin_url('admin.php?page=' . wplazare_payment_form::getEditionSlug() . '&amp;action=edit');
		$formEditAction = admin_url('admin.php?page=' . wplazare_payment_form::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit);
		$formAction = $formAddAction;
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit'))
		{
			$formAction = $formEditAction;
		}

		$the_form = '
<form name="' . wplazare_payment_form::getDbTable() . '_form" id="' . wplazare_payment_form::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data" >
' . wplazare_form::form_input(wplazare_payment_form::getDbTable() . '_action', wplazare_payment_form::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
' . wplazare_form::form_input(wplazare_payment_form::getDbTable() . '_form_has_modification', wplazare_payment_form::getDbTable() . '_form_has_modification', 'no' , 'hidden') . '
<div id="wplazareFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wplazare(document).ready(function(){
		wplazareMainInterface("' . wplazare_payment_form::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_payment_form::getEditionSlug()) . '");

		wplazareFormsInterface("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer l\'association entre cette offre et ce formulaire?', 'wplazare') . '");

		wplazare("#delete").click(function(){
			wplazare("#' . wplazare_payment_form::getDbTable() . '_action").val("delete");
			deletePaymentForm();
		});
		if(wplazare("#' . wplazare_payment_form::getDbTable() . '_action").val() == "delete"){
			deletePaymentForm();
		}
		function deletePaymentForm(){
			if(confirm(wplazareConvertAccentTojs("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?', 'wplazare') . '"))){
				wplazare("#' . wplazare_payment_form::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_payment_form::getDbTable() . '_action").val("edit");
			}
		}
	});
</script>';

		if($itemToEdit != '')
		{
			ob_start();
			wplazare_payment_form::getInitPaymentForm($itemToEdit);
			$userFormCode = ob_get_contents();
			ob_end_clean();
			$the_form .= '<div class="clear paymentFormContainer" ><br/><br/><br/><hr/>' . __('Pour utiliser ce formulaire, ins&eacute;rer le code ci-dessous &agrave; l\'endroit que vous souhaitez', 'wplazare') . '<div class="clear payment_form_code" >' . wplazare_payment_form::getPaymentFormShortCode($itemToEdit) . '</div></div>';
		}

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
			if(current_user_can('wplazare_add_forms'))
			{
				$currentPageButton .= '<input type="button" class="button-primary" id="add" name="add" value="' . __('Ajouter', 'wplazare') . '" />';
			}
		}
		elseif(current_user_can('wplazare_edit_forms'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="' . __('Enregistrer', 'wplazare') . '" /><input type="button" class="button-primary" id="saveandcontinue" name="saveandcontinue" value="' . __('Enregistrer et continuer l\'&eacute;dition', 'wplazare') . '" />';
		}
		if(current_user_can('wplazare_delete_forms') && ($action != 'add'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="' . __('Supprimer', 'wplazare') . '" />';
		}

		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_payment_form::getListingSlug()) . '" class="button add-new-h2" >' . __('Retour', 'wplazare') . '</a></h2>';

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
			AND PFORM.id = '" . $elementId . "' ";
		}

		$query = $wpdb->prepare(
		"SELECT PFORM.*
		FROM " . wplazare_payment_form::getDbTable() . " AS PFORM
		WHERE PFORM.status IN (".$elementStatus.") " . $moreQuery
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
	*	Return the short code to put into the page for displaying a form
	*
	*	@param integer $formIdentifier The identifier of the form we want to output the shortcode for
	*
	*	@return string The shortcode to put directly into a page to output a form
	*/
	function getPaymentFormShortCode($formIdentifier)
	{
		return '[wplazare_payment_form id="' . $formIdentifier . '" ]';
	}
	
	function chooseLocation($orderMoreInformations = '',$error = '')
	{
		if($orderMoreInformations == ""){
			$orderMoreInformations = array();
			$orderMoreInformations['banque_code'] = '';
			$orderMoreInformations['banque_code_guichet'] = '';
			$orderMoreInformations['banque_code_numero_compte'] = '';
			$orderMoreInformations['banque_code_cle_rib'] = '';
			$orderMoreInformations['banque_iban'] = '';
			$orderMoreInformations['banque_nom'] = '';
			$orderMoreInformations['banque_adresse'] = '';
			$orderMoreInformations['banque_code_postal'] = '';
			$orderMoreInformations['banque_ville'] = '';
			$orderMoreInformations['location_id'] = 0;
			$orderMoreInformations['location_type_charge'] = "";
			$orderMoreInformations['order_amount'] = "0";
            $orderMoreInformations['payment_recurrent_day_of_month'] = 10;
		}

		$input_def['value'] = $orderMoreInformations['location_id'];
		$input_def['name'] = 'wp_lazare_forms[location_id]';
		$input_name = $input_def['name'];
		$input_def['type'] = 'select';
		$input_def['valueToPut'] = 'index';
		$input_def['possible_value'] = wplazare_locations::getLocationsForSelect();
		$input_def['option'] = ' class="wplazare_form_input combobox clearInputValue" ';
		$the_input = wplazare_form::check_input_type($input_def);
		$message = "";
		if($error != '') $message .= '<p class="error">'.$error.'</p>';
		$message .= '
		<div id="prelevement_div">
			<form action="'. wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlCharge').'" method="post" name="prelevement_form" id="prelevement_form" class="locataireCharge" >';
		$message .= '
			<input type="hidden" value="1" name="formIdentifier" />
			<input type="hidden" value="1" name="selectedOffer" />
			<input type="hidden" value="true" name="prelevement" />
			<div class="clear" >
				<div class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					<label>Locataire:</label>'.
				'</div>
				<div sclass="wplazare_form_input wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
					' . $the_input . '
				</div>
			</div>';
		$logement_selected = $orderMoreInformations['location_type_charge'] == "Frais Logement"? "selected='selected'" : "";
		$nourriture_selected = $orderMoreInformations['location_type_charge'] == "Frais Logement"? "selected='selected'" : "";
		$message .= '<div class="clear" >
				<div class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					<label>Charge:</label>'.
				'</div>
				<div class="wplazare_form_input wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
					<select name="wp_lazare_forms[location_type_charge]">
						<option value="Frais logement" '.$logement_selected.'>Frais logement</option>
						<option value="Frais nourriture" '.$nourriture_selected.'>Frais nourriture</option>
					</select>
				</div>
			</div>';
		$message .= '<div class="clear" >
				<div class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					<label>Montant:</label>'.
				'</div>
				<div class="wplazare_form_input wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
					<input id="valeur" type="text" value="' . $orderMoreInformations['order_amount']/100 .'" name="valeur"> &euro;
				</div>
			</div>
			<br/>';

        $dayOfMonth_array = array_combine(range(1, 28),range(1, 28));
        $input_def['value'] = $orderMoreInformations['payment_recurrent_day_of_month'];
        $input_def['name'] = 'wp_lazare_forms[payment_recurrent_day_of_month]';
        $input_name = $input_def['name'];
        $input_def['type'] = 'select';
        $input_def['valueToPut'] = 'index';
        $input_def['possible_value'] = $dayOfMonth_array;
        $input_def['option'] = ' class="wplazare_form_input combobox" ';
        $the_input = wplazare_form::check_input_type($input_def);
        $message .= '<div class="clear" >
				<div class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					<label>Jour du pr&eacute;l&egrave;vement:</label>'.
            '</div>
                    <div class="wplazare_form_input wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >'.
                        $the_input.
                    '</div>
                </div>';


		$message .= '<div id="prelevement_div"><form action="'. wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlSuccess').'" method="post" name="prelevement_form" id="prelevement_form" >';
		$message .= "<h3>Mes coordonn&eacute;es bancaires</h3>";
		$message .= "<table>".
					"<tr><th>IBAN</th></tr>".
					"<tr>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_iban','id' => 'banque_iban', 'type' => 'text', 'value' => $orderMoreInformations['banque_iban']), wplazare_payment_form::getDbTable())."</td>".
					"</tr></table>";
		$message .= "<table>".
					"<tr><th>Code Banque</th><th>Code Guichet</th><th>N° du compte</th><th>Clé Rib</th></tr>".
					"<tr>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_code','id' => 'banque_code', 'type' => 'text', 'value' => $orderMoreInformations['banque_code']), wplazare_payment_form::getDbTable())."</td>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_code_guichet','id' => 'banque_code_guichet', 'type' => 'text', 'value' => $orderMoreInformations['banque_code_guichet']), wplazare_payment_form::getDbTable())."</td>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_code_numero_compte','id' => 'banque_code_numero_compte', 'type' => 'text', 'value' => $orderMoreInformations['banque_code_numero_compte']), wplazare_payment_form::getDbTable())."</td>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_code_cle_rib','id' => 'banque_code_cle_rib', 'type' => 'text', 'value' => $orderMoreInformations['banque_code_cle_rib']), wplazare_payment_form::getDbTable())."</td>".
					"</tr></table>";
		$message .= "<h3>Adresse de la banque</h3>";
		
		$inputs = array();
		$inputs[]= array(	'name' => 'banque_nom','id' => 'banque_nom','type' => 'text', 'value' => $orderMoreInformations['banque_nom']);
		$inputs[]= array(	'name' => 'banque_adresse','id' => 'banque_adresse','type' => 'text', 'value' => $orderMoreInformations['banque_adresse']);
		$inputs[]= array(	'name' => 'banque_code_postal','id' => 'banque_code_postal','type' => 'text', 'value' => $orderMoreInformations['banque_code_postal']);
		$inputs[]= array(	'name' => 'banque_ville','id' => 'banque_ville','type' => 'text', 'value' => $orderMoreInformations['banque_ville']);		
		
		foreach($inputs as $input_def){
			$the_input = wplazare_form::check_input_type($input_def, wplazare_payment_form::getDbTable());
			$message .= '
			<div class="clear" >
				<div class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
					<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
				</div>
				<div class="wplazare_form_input wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
					' . $the_input . '
				</div>
			</div>';
		}
		$message .= '<div class="clear"><br/><input type="submit" value="Valider"/></div>';
		$message .='
		</form>
		</div>';
		return '<div class="paymentReturnResponse" >' . $message . '</div>';
	}

	/**
	*	Function to decode the shortcode to output a payment fom into a page
	*
	*	@param mixed $atts optionnal The attributes list of the shortcode
	*
	*	@return string $formContent THe html code of the form to display according to the shortcode parameters
	*/
	function displayForm($atts = '')
	{
		$formContent = '';

		$formIsComplete = false;
		$formIdentifier = isset($_POST['formIdentifier']) ? wplazare_tools::varSanitizer($_POST['formIdentifier']) : '';
		if($formIdentifier != '')
		{
			$mandatoryUserField = array();
			
			if($formIdentifier == "2"){
				
			}
			else{
				/*	Get the informations about the current form	*/
				$currentForm = wplazare_payment_form::getElement($formIdentifier, "'valid'");
	
				/*	Set the mandatory fiel list	*/
				$mandatoryUserField = unserialize($currentForm->payment_form_mandatory_fields);
			}


			$orderIdentifier = wplazare_orders::saveNewOrder($_POST);
			$formIsComplete = true;
			foreach($mandatoryUserField as $field)
			{
				$testField = isset($_POST['order_user'][$field]) ? wplazare_tools::varSanitizer($_POST['order_user'][$field]) : '';
				if($testField == '')
				{
					$formIsComplete = false;
					break;
				}
			}

			$formHasError = false;
			$error = '';
			/*	Check if the given email is a good email	*/
			if(!is_email($_POST['order_user']['user_email']))
			{
				$formHasError = true;
				$error .= __('L\'adresse email fournie n\'est pas une adresse email valable', 'wplazare') . '<br/>';
			}

			if($formIsComplete && !$formHasError)
			{
				if(isset($_POST['carte']) && $_POST['carte'] == 'true' ){
					/*	Get the form to ouput	*/
					ob_start();
					wplazare_payment_form::getPaymentFormTemplate($formIdentifier,$_POST['order_user']['user_email'],$orderIdentifier);
					$formContent = ob_get_contents();
					ob_end_clean();
	
					/*	Replace the full dynamic vars into the form	*/
					/*$formContent = str_replace('#PBXPORTEUR#', $_POST['order_user']['user_email'], $formContent);
					$formContent = str_replace('#PBXCMDIDENTIANT#', $orderIdentifier, $formContent);*/
					$formContent .= '<script type="text/javascript" >jQuery("#lazarePayment").submit();</script>';
				}
				elseif(isset($_POST['prelevement']) && $_POST['prelevement'] == 'true' ){
					$formContent .= wplazare_payment_form::getPrelevementForm($orderIdentifier);
				}
				elseif(isset($_POST['cheque']) && $_POST['cheque'] == 'true' ){
					ob_start();
					wplazare_payment_form::getChequeLink($orderIdentifier);
					$formContent = ob_get_contents();
					ob_end_clean();
				}
				else{
					$formHasError = true;
					$error .= __('ERREUR, veuillez contacter le webmaster.', 'wplazare') . '<br/>';
				}
			}
			elseif(!$formIsComplete)
			{
				$formContent .= '<div class="mandatoryFieldAlert" >' . __('Tous les champs marqu&eacute;s d\'une &eacute;toile sont obligatoires', 'wplazare') . '</div>';
			}
			elseif($formHasError)
			{
				$formContent .= '<div class="errorFieldAlert" >' . $error . '</div>';
			}
		}

		/*	Get the shortcode parameter to know which form to output	*/
		extract(shortcode_atts(array('id' => ''), $atts));

		/*	Get the current form informations	*/
		$currentForm = wplazare_payment_form::getElement($formIdentifier);
		if($currentForm->status == 'valid')
		{
			ob_start();
			wplazare_payment_form::getInitPaymentForm($formIdentifier);
			$formContent .= ob_get_contents();
			ob_end_clean();
		}
		else
		{/*	If the current form is no longer valid we output a message	*/
			$formContent .= sprintf(__('Une erreur est survenue. Merci de nous contacter en pr&eacute;cisant le code d\'erreur suivant: Form%dInvalid', 'wplazare'), $formIdentifier);
		}

		return $formContent;
	}

	/**
	*	Return the form to display before the user is sending on the payment page. In order to collect informations about the user
	*
	*	@return mixed The html code of the form that contains the different fields for the user enter its informations
	*/
	function getInitPaymentForm($formIdentifier)
	{
		global $currencyIconList;
		$mandatoryUserField = array();

		/*	Get the informations about the current form	*/
		$currentForm = wplazare_payment_form::getElement($formIdentifier, "'valid'");

		/*	Set the mandatory fiel list	*/
		$mandatoryUserField = unserialize($currentForm->payment_form_mandatory_fields);
		
		$excludeDatas = array('user_association','user_recu','user_reception_recu');
		
		/*	Get the fields from the order table concerning the user	*/
		$dbFieldList = wplazare_database::fields_to_input(WPLAZARE_DBT_ORDERS);
		
?>
<form action="" method="post" name="edit_form" id="donForm" >
	<input type="hidden" name="formIdentifier" id="formIdentifier" value="<?php echo $formIdentifier; ?>" />
<?php

			$input_radio = array('name' => 'user_association', 'type' => 'radio', 'value' => 'Lazare', 'possible_value' => array('Lazare', 'APA'));
			$dbFieldList[] = $input_radio;
			
			/*	Put the different input form the order	*/
			foreach($dbFieldList as $input_key => $input_def)
			{
				if(substr($input_def['name'], 0, 5) == 'user_' && !in_array($input_def['name'],$excludeDatas))
				{
					$mandatoryField = '';
					if(in_array($input_def['name'], $mandatoryUserField))
					{
						$mandatoryField = '<span class="isMandatoryField" >*</span>';
					}
					if(isset($_POST['order_user'][$input_def['name']]))
					{
						$input_def['value'] = $_POST['order_user'][$input_def['name']];
					}
					$input_def['option'] = ' class="wplazare_form_input" ';
					$the_input = wplazare_form::check_input_type($input_def, 'order_user');
				echo 
'	<div class="clear" >
		<label class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_label" for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . $mandatoryField . '</label>
		<br/>' . $the_input . '
	</div>
';
				}
			}

			/*	Add the offer list for the current form	*/
			$associatedOffers = wplazare_offers::getOffersOfForm($formIdentifier);
			if(count($associatedOffers) > 0)
			{
				$storedOffers = array();
				foreach($associatedOffers as $associatedOffer)
				{
					if($associatedOffer->offer_title != '')
					{
						$storedOffers[$associatedOffer->offer_id] = $associatedOffer->offer_title;
					}
					else
					{
						$storedOffers[$associatedOffer->offer_id] = wplazare_offers::generateOfferTitle($associatedOffer);
					}
				}
				$input_def['name'] = 'selectedOffer';
				$input_def['type'] = 'select';
				$input_def['valueToPut'] = 'index';
				$input_def['value'] = 1;
				$input_def['possible_value'] = $storedOffers;
				$input_def['option'] = ' class="wplazare_form_input" ';
				$the_input = wplazare_form::check_input_type($input_def);
				echo 
'	<div class="clear" style="display:none;" >
		<label class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_label" for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . $mandatoryField . '</label>
		<br/>' . $the_input . '
	</div>
';
			}
			else
			{
				echo 
'	<div class="clear" >
		<label class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_selectedOffer_label" >' . __('selectedOffer', 'wplazare') . $mandatoryField . '</label>
		' . sprintf(__('Aucune offre n\'est associ&eacute;e &agrave; ce formulaire. Nous ne pouvons donner suite &agrave; votre demande. Pour plus d\'informations, contactez-nous en indiquant le code d\'erreur suivant: ErrorNOF#%d', 'wplazare'), $formIdentifier) . '
	</div>
';
			}
			$checked_email = 'checked="checked';
			$checked_courier = '';
	if(isset($_POST['order_user']['user_reception_recu']) && $_POST['order_user']['user_reception_recu'] == 'courrier')
	{
		$checked_email = '';
		$checked_courier = 'checked="checked';
	}
?>
<div class="association_choice_div">
	<label class="wplazare_form_label wplazare_payment_form_user_association_label" for="user_association">Association</label>
	<br><input type="radio" name="order_user[user_association]" id="order_user[user_association]_Lazare" value="Lazare" checked="checked" class="wplazare_form_input"> Lazare <input type="radio" name="order_user[user_association]" id="order_user[user_association]_APA" value="APA" class="wplazare_form_input"> APA
</div> 
<div><input type="checkbox" class="checkbox" name="order_user[user_recu]" id="chxRecu" value="1" checked="checked" style="margin: 0;" onclick="dispChxRecu();"> Je souhaite recevoir mon reçu fiscal<div id="choixRecu">
	<input class="radio" type="radio" id="e-mail" name="order_user[user_reception_recu]" value="email" <?php echo $checked_email?>/>
	<label for="e-mail">Par e-mail</label>
	<input class="radio" type="radio" id="courrier" name="order_user[user_reception_recu]" value="courrier" <?php echo $checked_courier?>/>
	<label for="courrier">Par courrier</label>
</div>
<div>
	<input type="checkbox" name="order_user[newsletter]" id="order_user[newsletter]_Lazare" value="Lazare" checked="checked" style="margin: 0;"> Je souhaite m'inscrire &agrave; la Newsletter
</div> 
</div>

<div id="popup_name" class="popup_block">
	<a href="#" class="close"><img src="<?php echo get_theme_root_uri(); ?>/lazare/images/close_pop.png" class="btn_close" title="Fermer la fenêtre" alt="Fermer"></a>
	<h4>Vérifier vos données</h4>
	<table>
		<tr><td>Email: </td><td><span id="user_email_copy"></span></td></tr>
		<tr><td>Nom: </td><td><span id="user_lastname_copy"></span></td></tr>
		<tr><td>Prénom: </td><td><span id="user_firstname_copy"></span></td></tr>
		<tr><td>Téléphone: </td><td><span id="user_phone_copy"></span></td></tr>
		<tr><td>Adresse: </td><td><span id="user_adress_copy"></span></td></tr>
		<tr><td>Code Postal: </td><td><span id="user_code_postal_copy"></span></td></tr>
		<tr><td>Ville: </td><td><span id="user_ville_copy"></span></td></tr>
		<tr><td>Date de naissance: </td><td><span id="user_birthday_copy"></span></td></tr>
		<tr><td>Association: </td><td><span id="user_association_copy"></span></td></tr>
		<tr><td>Reçu fiscal: </td><td><span id="user_reception_recu_copy"></span></td></tr>
		<tr><td>Votre don: </td><td><span id="user_don"></span> &euro;</td> </tr>
		<tr><td>Type de paiement: </td><td><span id="user_type"></span></td></tr>
	</table>
	<button id="go_button">Je valide</button> <button class="close">Je veux modifier</button>
</div>

<input type="hidden" name="cheque" id="submit_cheque" value="false" />
<input type="hidden" name="carte" id="submit_carte" value="false" />
<input type="hidden" name="prelevement" id="submit_prelevement" value="false" />
<input type="hidden" name="don_selectionne" id="don_selectionne" value="" />
<input type="hidden" name="valeur" id="valeur" value="0" />
<script type="text/javascript" >
jQuery(document).ready(function(){
	
	params = {
			'order_user[user_email]': {
            	required: true,
            	email: true
			},
			'order_user[user_lastname]': {
            	required: true
			},
			'order_user[user_firstname]': {
            	required: true
			},
			'order_user[user_adress]': {
            	required: true
			},
			'order_user[user_code_postal]': {
            	required: true,
            	minlength: 5,
            	maxlength: 5,
            	digits: true
			},
			'order_user[user_ville]': {
            	required: true
			},
			'order_user[user_phone]': {
				digits: true
			}
	}

	jQuery('#cheque_button').click(function() {
		if(jQuery("#donForm").validate({rules: params}).form())
			popupVerification('Chèque');
	});

	jQuery('#prelevement_button').click(function() {
		if(jQuery("#donForm").validate({rules: params}).form())
			popupVerification('Prélèvement');
	});

	jQuery('#carte_button').click(function() {
		if(jQuery("#donForm").validate({rules: params}).form())
			popupVerification('Carte');
	});
	
	function goCheque(){
		jQuery('#fade , .popup_block').fadeOut();
		if(jQuery('input[type=radio][name=offre_dp]:checked').length == 1 || jQuery('#offre_libre_1').val() != ''){
			if(jQuery("#user_email").val() == "") jQuery("#user_email").val("noemail@maisonlazare.com");
			jQuery('#submit_cheque').val('true');
			jQuery('#donForm').submit();
		}
		else {
			alert(wplazareConvertAccentTojs("<?php _e('Vous devez choisir un montant dans la zone \" Je fais un don ponctuel  \".', 'wplazare') ?>"));
			return false;
		}
	}
	function goPrelevement(){
		if(jQuery('input[type=radio][name=offre_pdr]:checked').length == 1 || jQuery('#offre_libre_2').val() != ''){
			jQuery('#submit_prelevement').val('true');
			jQuery('#donForm').submit();
		}
		else {
			alert(wplazareConvertAccentTojs("<?php _e('Vous devez choisir un montant dans la zone \" Je soutiens par pr&eacute;l&egrave;vement mensuel \".', 'wplazare') ?>"));
			return false;
		}
	}
	function goCarte(){
		if(jQuery('input[type=radio][name=offre_dp]:checked').length == 1 || jQuery('#offre_libre_1').val() != ''){
			jQuery('#submit_carte').val('true');
			jQuery('#donForm').submit();
		}
		else {
			alert(wplazareConvertAccentTojs("<?php _e('Vous devez choisir un montant dans la zone \" Je fais un don ponctuel \".', 'wplazare') ?>"));
			return false;
		}
		jQuery('#submit_carte').val('true');
		jQuery('#donForm').submit();
	}
	<?php
	if(isset($_POST['offre_dp_libre']) && $_POST['offre_dp_libre'] != '')
		echo "jQuery('#offre_libre_1').val(".$_POST['offre_dp_libre'].");";
	if(isset($_POST['offre_pdr_libre']) && $_POST['offre_pdr_libre'] != '')
		echo "jQuery('#offre_libre_2').val(".$_POST['offre_pdr_libre'].");";
	if(isset($_POST['simulation']) && $_POST['simulation'] != '')
		echo "jQuery('#simulation').val(".$_POST['simulation'].");";
	if(isset($_POST['don_selectionne']) && isset($_POST['valeur'])){
		if(substr($_POST['don_selectionne'],0,11) != 'offre_libre')
			echo "jQuery('#".$_POST['don_selectionne']."').attr('checked', true);";
		else echo "jQuery('#".$_POST['don_selectionne']."').val(".$_POST['valeur'].");";
	}
	?>
	
	function popupVerification(type){
		jQuery("#user_email_copy").html(jQuery("#user_email").val());
		jQuery("#user_lastname_copy").html(jQuery("#user_lastname").val());
		jQuery("#user_firstname_copy").html(jQuery("#user_firstname").val());
		jQuery("#user_phone_copy").html(jQuery("#user_phone").val());
		jQuery("#user_adress_copy").html(jQuery("#user_adress").val());
		jQuery("#user_code_postal_copy").html(jQuery("#user_code_postal").val());
		jQuery("#user_ville_copy").html(jQuery("#user_ville").val());
		jQuery("#user_birthday_copy").html(jQuery("#user_birthday").val());
		jQuery("#user_email_copy").html(jQuery("#user_email").val());
		jQuery("#user_association_copy").html(jQuery('input[name="order_user[user_association]"]:checked', '#donForm').val());
		jQuery("#user_don").html(jQuery("#valeur").val());
		jQuery("#user_type").html(type);
		if(jQuery('#chxRecu').is(':checked'))
			jQuery("#user_reception_recu_copy").html('Par ' + jQuery('input[name="order_user[user_reception_recu]"]:checked', '#donForm').val());
		
		var popID = "popup_name";
	
		jQuery('#' + popID).fadeIn().css({
			'width': '500px'
		})
		.prepend('');
	
		var popMargTop = (jQuery('#' + popID).height() + 80) / 2;
		var popMargLeft = (jQuery('#' + popID).width() + 80) / 2;
	
		jQuery('#' + popID).css({
			'margin-top' : -popMargTop,
			'margin-left' : -popMargLeft
		});
	
		jQuery('body').append('');
		jQuery('#fade').css({'filter' : 'alpha(opacity=80)'}).fadeIn();

		jQuery('#go_button').off('click');
		switch(type){
			case "Chèque":
				jQuery('#go_button').click(function() { goCheque();	});
			break;
			case "Prélèvement":
				jQuery('#go_button').click(function() {	goPrelevement(); });
			break;
			case "Carte":
				jQuery('#go_button').click(function() { goCarte(); });
			break;
		}
	
		return false;
	}
	
	
	jQuery('a.close, #fade,#popup_name button').live('click', function() { 
		jQuery('#fade , .popup_block').fadeOut();
		return false;
	});
});
</script>
<?php
	}

	/**
	*	Return the lazare payment form
	*
	*	@param integer $formIdentifier The form identifier to get the different information about the payment like amount, currency, and so on
	*
	*	@return mixed The html code representing the lazare payment form
	*/
	function getPaymentFormTemplate($formIdentifier,$user_email,$orderIdentifier)
	{
		/*	Define the test environnement vars*/
		global $testEnvironnement;

		/*	Get the last order identifier	*/
		$offer = wplazare_offers::getElement($_POST['selectedOffer']);
		$offer->payment_amount = 100 * $_POST['valeur'];

		/*	Get tje current form informations	*/
		$formInformations = wplazare_payment_form::getElement($formIdentifier);

?>
<form action="<?php echo wplazare_option::getStoreConfigOption('wplazare_store_mainoption', 'urlCgi') ?>" method="post" id="lazarePayment" >
<?php
		if(wplazare_option::getStoreConfigOption('wplazare_store_mainoption', 'environnement') == 'test')
		{
            $pbx_site = "1999888";//$testEnvironnement['tpe'];
            $pbx_rang = "32";//$testEnvironnement['rang'];
            $pbx_identifiant = "110647233";//$testEnvironnement['identifier'];
            $keyTest = "0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF0123456789ABCDEF";
		}
		else
		{
            $pbx_site = wplazare_option::getStoreConfigOption('wplazare_store_mainoption', 'storeTpe');
            $pbx_rang = wplazare_option::getStoreConfigOption('wplazare_store_mainoption', 'storeRang');
            $pbx_identifiant = wplazare_option::getStoreConfigOption('wplazare_store_mainoption', 'storeIdentifier');
            $keyTest = wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'keyTest');
		}
    $dateTime = date("c");
    $binKey = pack("H*", $keyTest);

// On crée la chaîne à hacher sans URLencodage
    $msg = "PBX_SITE=$pbx_site".
        "&PBX_RANG=$pbx_rang".
        "&PBX_IDENTIFIANT=$pbx_identifiant".
        "&PBX_TOTAL=".zeroise($offer->payment_amount, 3).
        "&PBX_DEVISE=$offer->payment_currency".
        "&PBX_CMD=".$offer->payment_reference_prefix.$orderIdentifier.
        "&PBX_PORTEUR=".$user_email.
        "&PBX_RETOUR=amount:M;reference:R;autorisation:A;transaction:T;error:E".
        "&PBX_HASH=SHA512".
        "&PBX_TIME=".$dateTime.
        "&PBX_EFFECTUE=".wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlSuccess').
        "&PBX_REFUSE=".wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlDeclined').
        "&PBX_ANNULE=".wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlCanceled');



    // On récupère la clé secrète HMAC (stockée dans une base de données par exemple) et que l’on renseigne dans la variable $keyTest;
    // Si la clé est en ASCII, On la transforme en binaire
    //$binKey = pack("H*", $keyTest);
    // On calcule l’empreinte (à renseigner dans le paramètre PBX_HMAC) grâce à la fonction hash_hmac et
    // la clé binaire
    // On envoie via la variable PBX_HASH l'algorithme de hachage qui a été utilisé (SHA512 dans ce cas)
    // Pour afficher la liste des algorithmes disponibles sur votre environnement, décommentez la ligne
    // suivante
    // print_r(hash_algos());
    $hmac = strtoupper(hash_hmac('sha512', $msg, $binKey));
    // La chaîne sera envoyée en majuscules, d'où l'utilisation de strtoupper()
    // On crée le formulaire à envoyer à Paybox System
    // ATTENTION : l'ordre des champs est extrêmement important, il doit
    // correspondre exactement à l'ordre des champs dans la chaîne hachée
    ?>
    <input type="hidden" name="PBX_SITE" value="<?php echo $pbx_site; ?>" />
    <input type="hidden" name="PBX_RANG" value="<?php echo $pbx_rang; ?>" />
    <input type="hidden" name="PBX_IDENTIFIANT" value="<?php echo $pbx_identifiant; ?>" />
    <input type="hidden" name="PBX_TOTAL" value="<?php echo zeroise($offer->payment_amount, 3); ?>" />
	<input type="hidden" name="PBX_DEVISE" value="<?php echo $offer->payment_currency ?>" />
	<input type="hidden" name="PBX_CMD" value="<?php echo $offer->payment_reference_prefix.$orderIdentifier; ?>" />
    <input type="hidden" name="PBX_PORTEUR" value="<?php echo $user_email; ?>" />
	<input type="hidden" name="PBX_RETOUR" value="amount:M;reference:R;autorisation:A;transaction:T;error:E" />
    <input type="hidden" name="PBX_HASH" value="SHA512" />
    <input type="hidden" name="PBX_TIME" value="<?php echo $dateTime; ?>" />
	<input type="hidden" name="PBX_EFFECTUE" value="<?php echo wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlSuccess'); ?>" />
	<input type="hidden" name="PBX_REFUSE" value="<?php echo wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlDeclined'); ?>" />
	<input type="hidden" name="PBX_ANNULE" value="<?php echo wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlCanceled'); ?>" />
    <input type="hidden" name="PBX_HMAC" value="<?php echo $hmac; ?>" />
	<input type="submit" value="paiement" class="lazareButtonFormPayment" />
</form>
<?php
	}
	
	function getChequeLink($formIdentifier){
		?>
		<script type="text/javascript" >
		window.location.href = "<?php echo wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlSuccess'); ?>?reference=<?php echo $formIdentifier;?>";
		</script>
		<?php
	}
	
	function getPrelevementForm($orderIdentifier, $orderMoreInformations = null,$error = ''){
		if($orderMoreInformations == null){
			$orderMoreInformations['banque_code'] = '';
			$orderMoreInformations['banque_code_guichet'] = '';
			$orderMoreInformations['banque_code_numero_compte'] = '';
			$orderMoreInformations['banque_code_cle_rib'] = '';
			$orderMoreInformations['banque_iban'] = '';
			$orderMoreInformations['banque_nom'] = '';
			$orderMoreInformations['banque_adresse'] = '';
			$orderMoreInformations['banque_code_postal'] = '';
			$orderMoreInformations['banque_ville'] = '';	
		}
		$currentOrder = wplazare_orders::getElement($orderIdentifier, "'valid'", 'order_reference');
		$return = '';
		if($error != '') $return .= '<p class="error">'.$error.'</p>';
		$return .= '<div id="prelevement_div"><form action="'. wplazare_option::getStoreConfigOption('wplazare_store_urloption', 'urlSuccess').'" method="post" name="prelevement_form" id="prelevement_form" >';
		$return .= "<h3>Mes coordonn&eacute;es bancaires</h3>";
		$return .= "<table>".
					"<tr><th>IBAN</th></tr>".
					"<tr>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_iban','id' => 'banque_iban', 'type' => 'text', 'value' => $orderMoreInformations['banque_iban']), wplazare_payment_form::getDbTable())."</td>".
					"</tr></table>";
		$return .= "<table>".
					"<tr><th>Code Banque</th><th>Code Guichet</th><th>N° du compte</th><th>Clé Rib</th></tr>".
					"<tr>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_code','id' => 'banque_code', 'type' => 'text', 'value' => $orderMoreInformations['banque_code']), wplazare_payment_form::getDbTable())."</td>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_code_guichet','id' => 'banque_code_guichet', 'type' => 'text', 'value' => $orderMoreInformations['banque_code_guichet']), wplazare_payment_form::getDbTable())."</td>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_code_numero_compte','id' => 'banque_code_numero_compte', 'type' => 'text', 'value' => $orderMoreInformations['banque_code_numero_compte']), wplazare_payment_form::getDbTable())."</td>".
					"<td>".wplazare_form::check_input_type(array('name' => 'banque_code_cle_rib','id' => 'banque_code_cle_rib', 'type' => 'text', 'value' => $orderMoreInformations['banque_code_cle_rib']), wplazare_payment_form::getDbTable())."</td>".
					"</tr></table>";
		$return .= "<h3>Adresse de la banque</h3>";
		
		$inputs = array();
		$inputs[]= array(	'name' => 'banque_nom','id' => 'banque_nom','type' => 'text', 'value' => $orderMoreInformations['banque_nom']);
		$inputs[]= array(	'name' => 'banque_adresse','id' => 'banque_adresse','type' => 'text', 'value' => $orderMoreInformations['banque_adresse']);
		$inputs[]= array(	'name' => 'banque_code_postal','id' => 'banque_code_postal','type' => 'text', 'value' => $orderMoreInformations['banque_code_postal']);
		$inputs[]= array(	'name' => 'banque_ville','id' => 'banque_ville','type' => 'text', 'value' => $orderMoreInformations['banque_ville']);		
		
		foreach($inputs as $input_def){
			$the_input = wplazare_form::check_input_type($input_def, wplazare_payment_form::getDbTable());
			$return .= '
			<div class="clear" >
				<div class="wplazare_form_label wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
					<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
				</div>
				<div class="wplazare_form_input wplazare_' . wplazare_payment_form::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
					' . $the_input . '
				</div>
			</div>';
		}
		$type_virement = "Je fais un don";
		if(isset($currentOrder->type_charge) && $currentOrder->type_charge) {
			$type_virement = "Je paye des charges de '$currentOrder->type_charge'";
		}
		$return .= "<h3>Mon soutien</h3>";
		$return .= "<p>$type_virement de ".($currentOrder->payment_amount/100)." &euro; tous les mois par pr&eacute;l&egrave;vement.</p>";
		$return .= '<input type="submit" value=" Valider " />';
		$return .= '<input type="hidden" name="reference" value="' . $orderIdentifier . '" />';
		$return .= '</form></div>
		<script type="text/javascript" >
			jQuery(document).ready(function(){
				jQuery("#prelevement_div").appendTo(".entry-content");
				jQuery(".colonnes").css("display","none");
				jQuery(".cnil").css("display","none");
			});
			
		</script>
		';
		return $return;
	}
}