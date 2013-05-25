<?php
/**
* Define the different method to access or create users 'fiches'
* 
*	Define the different method to access or create users 'fiches'
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Define the different method to access or create users 'fiches'
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_fiches
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wplazare_fiches';
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
		return WPLAZARE_URL_SLUG_FICHES_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_FICHES_EDITION;
	}
	
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
	{
		return WPLAZARE_DBT_FICHES;
	}
	
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getQuestionDbTable()
	{
		return WPLAZARE_DBT_QUESTIONS;
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

		$title = __('Fiche utilisateur', 'wplazare');
		
		if(isset($_REQUEST['user_id']) && $_REQUEST['user_id'] != ''){
			$title .= ' de '.wplazare_tools::getUserName(wplazare_tools::varSanitizer($_REQUEST['user_id']));
		}
		 
		if($action != '')
		{
			if($action == 'edit')
			{
				//$editedItem = wplazare_fiches::getElement($objectInEdition);
				//$title .= sprintf(__('&Eacute;diter l\'offre "%s"', 'wplazare'), $editedItem->payment_name);
			}
			elseif($action == 'add')
			{
				$title .=' - ' . __('Question', 'wplazare');
			}
		}
		return $title;
		
	}

	/**
	*	Define the different message and action after an action is send through the element interface
	*/
	function elementAction()
	{
		global $wpdb;
		global $id;
		$actionResultMessage = '';

		$pageMessage = $actionResult = '';
		$pageAction = isset($_REQUEST[wplazare_fiches::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_fiches::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wplazare_fiches::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_fiches::getDbTable()]['id']) : '';

		/*	Define the database operation type from action launched by the user	 */
		/*************************				GENERIC				**************************/
		/*************************************************************************/
		if(($pageAction != '') && (($pageAction == 'edit') || ($pageAction == 'editandcontinue') || ($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_edit_user_fiches'))
			{
				$_REQUEST[wplazare_fiches::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				if($pageAction == 'delete')
				{
					if(current_user_can('wplazare_edit_user_fiches'))
					{
						$_REQUEST[wplazare_fiches::getDbTable()]['status'] = 'deleted';
					}
					else
					{
						$actionResult = 'userNotAllowedForActionDelete';
					}
				}
				$actionResult = wplazare_database::update($_REQUEST[wplazare_fiches::getDbTable()], $id, wplazare_fiches::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionEdit';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_edit_user_fiches'))
			{
				$_REQUEST[wplazare_fiches::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wplazare_fiches::getDbTable()]['status'] = 'deleted';
				$actionResult = wplazare_database::update($_REQUEST[wplazare_fiches::getDbTable()], $id, wplazare_fiches::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionDelete';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'save') || ($pageAction == 'saveandcontinue') || ($pageAction == 'add')))
		{
			if(current_user_can('wplazare_edit_user_fiches'))
			{
				$_REQUEST[wplazare_fiches::getDbTable()]['creation_date'] = date('Y-m-d H:i:s');
				$actionResult = wplazare_database::save($_REQUEST[wplazare_fiches::getDbTable()], wplazare_fiches::getDbTable());
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
			//$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wplazare_fiches::getDbTable()]['name'] . '</span>';
			if($actionResult == 'error')
			{/*	CHANGE HERE FOR SPECIFIC CASE	*/
				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />Une erreur est survenue lors de l\'enregistrement';
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
				$actionResultMessage = '<img src="' . WPLAZARE_SUCCES_ICON . '" alt="action success" class="wplazarePageMessage_Icon" />L\'enregistrement s\'est d&eacute;roul&eacute; avec succ&eacute;s';
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
	function elementList($filter = '')
	{
		$user_id = isset($_REQUEST['user_id']) ? wplazare_tools::varSanitizer($_REQUEST['user_id']): '';

		$tableId = 'Acteur_list';
		$tableSummary = 'Fiche personnelle/';
		$tableTitles = array();
		$tableTitles[] = 'Question';
		$tableTitles[] = 'R&eacute;ponse';
		$tableClasses = array();
		$tableClasses[] = 'wplazare' . wplazare_fiches::getCurrentPageCode() . '_question_column';
		$tableClasses[] = 'wplazare' . wplazare_fiches::getCurrentPageCode() . '_reponse_column';

		$line = 0;
		$elementList = wplazare_fiches::getElement('',$user_id);
		if(count($elementList) > 0 && $user_id)
		{
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wplazare_fiches::getCurrentPageCode() . '_' . $element->id;

				$subRowActions = '';
				if(current_user_can('wplazare_edit_user_fiches'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_fiches::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id.'&user_id='.$user_id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Modifier', 'wplazare') . '</a>';
				}
				elseif(current_user_can('wplazare_view_user_fiches'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_fiches::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id.'&user_id='.$user_id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wplazare') . '</a>';
				}
				if(current_user_can('wplazare_edit_user_fiches'))
				{
					if($subRowActions != '')
					{
						$subRowActions .= '&nbsp;|&nbsp;';
					}
					$subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wplazare_fiches::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id.'&user_id='.$user_id). '" >' . __('Supprimer', 'wplazare') . '</a>';
				}
				
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
				unset($tableRowValue);
				$tableRowValue[] = array('class' => wplazare_fiches::getCurrentPageCode() . '_question_cell', 'value' => stripslashes($element->texte). $rowActions);
				$tableRowValue[] = array('class' => wplazare_fiches::getCurrentPageCode() . '_reponse_cell', 'value' => stripslashes($element->reponse));
				
				$tableRows[] = $tableRowValue;
				$line++;
			}
		}
		else
		{
			$subRowActions = '';
			if(current_user_can('wplazare_edit_user_fiches'))
			{
				$subRowActions .= '
	<a href="' . admin_url('admin.php?page=' . wplazare_fiches::getEditionSlug() . '&amp;action=add'.'&user_id='.$user_id) . '" >' . 'Cr&eacute;er la fiche' . '</a>';
			}
			$rowActions = '
	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
			$tableRowsId[] = wplazare_fiches::getDbTable() . '_noResult';
			unset($tableRowValue);
			if($user_id == '') $tableRowValue[] = array('class' => wplazare_fiches::getCurrentPageCode() . '_name_cell', 'value' => 'Vous n\'avez pas s&eacute;lectionn&eacute; d\'utilisateur.' . '
	<a href="' . admin_url('admin.php?page='.WPLAZARE_URL_SLUG_USERS_LISTING) . '" >' . 'Afficher les utilisateurs' . '</a>');
			else $tableRowValue[] = array('class' => wplazare_fiches::getCurrentPageCode() . '_name_cell', 'value' => 'La fiche n\'a pas &eacute;t&eacute; cr&eacute;&eacute;.' . $rowActions);
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $listItemOutput;
	}
	
	function elementEdition($itemToEdit = '')
	{
		$user_id = wplazare_tools::varSanitizer($_REQUEST['user_id']);
		
		$dbFieldList = wplazare_database::fields_to_input(wplazare_fiches::getDbTable());
		
		$editedItem = '';
		
		if($itemToEdit != '')
		{
			$editedItem = wplazare_fiches::getElement($itemToEdit);
		}
		else{
			$id = wplazare_questions::createFiche($user_id);
			return wplazare_fiches::elementList();
		}
		
		$the_form_content_hidden = $the_form_general_content = '';
		$newAppartForm = '';
		
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];
			
			$pageAction = isset($_REQUEST[wplazare_fiches::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_fiches::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wplazare_fiches::getDbTable()][$input_name]) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_fiches::getDbTable()][$input_name]) : '';
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
			
			
			switch($input_def['name'])
			{
				default:
					$helpForField = '';
				break;
			}	

			

			$the_input = wplazare_form::check_input_type($input_def, wplazare_fiches::getDbTable());
			if(is_object($editedItem)){
				if($input_name == 'question'){
					$result = wplazare_questions::getQuestionTexte($input_def['value']);
					$the_input = '<input type="hidden" name="'.wplazare_fiches::getDbTable().'[question]" id="question" value="'.stripslashes($input_def['value']).'">
					<input type="text" value="'.stripslashes($result->texte).'">';
				}
				if($input_name == 'user'){
					$resp_name = get_user_meta($input_def['value'],'last_name',true). get_user_meta($input_def['value'],'first_name',true);
					if($resp_name=='') $resp_name = get_user_meta($input_def['value'],'nickname',true);
					$the_input = '<input type="hidden" name="'.wplazare_fiches::getDbTable().'[user]" id="user" value="'.stripslashes($input_def['value']).'">
					<input type="text" value="'.stripslashes($resp_name).'">';
				}
			}
			$newAppartFormInput = '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_fiches::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
				<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
				' . $helpForField . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_fiches::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
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
				$newAppartForm .= $newAppartFormInput;
			}
		}
		
		
		$the_form_general_content .= $newAppartForm;

		/*	Define the different action available for the edition form	*/
		$formAddAction = admin_url('admin.php?page=' . wplazare_fiches::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit.'&amp;user_id=' . $user_id);
		$formEditAction = admin_url('admin.php?page=' . wplazare_fiches::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit.'&amp;user_id=' . $user_id);
		$formAction = $formAddAction;
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit'))
		{
			$formAction = $formEditAction;
		}

		$the_form = '
<form name="' . wplazare_fiches::getDbTable() . '_form" id="' . wplazare_fiches::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data" >
' . wplazare_form::form_input(wplazare_fiches::getDbTable() . '_action', wplazare_fiches::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
' . wplazare_form::form_input(wplazare_fiches::getDbTable() . '_form_has_modification', wplazare_fiches::getDbTable() . '_form_has_modification', 'no' , 'hidden') . '
<input type="hidden" name="nextQuestion" value="'.wplazare_fiches::getNextQuestionId(wplazare_tools::varSanitizer($editedItem->id), wplazare_tools::varSanitizer($_REQUEST['user_id'])).'"
<div id="wplazareFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wplazare_' . wplazare_fiches::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wplazare(document).ready(function(){
		wplazareMainInterface("' . wplazare_fiches::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_fiches::getEditionSlug()) . '&user_id='.$user_id.'");

		wplazare("#delete").click(function(){
			wplazare("#' . wplazare_fiches::getDbTable() . '_action").val("delete");
			deletePaymentForm();
		});
		if(wplazare("#' . wplazare_fiches::getDbTable() . '_action").val() == "delete"){
			deletePaymentForm();
		}
		function deletePaymentForm(){
			if(confirm(wplazareConvertAccentTojs(\'&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?\'))){
				wplazare("#' . wplazare_fiches::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_fiches::getDbTable() . '_action").val("edit");
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
			if(current_user_can('wplazare_edit_user_fiches'))
			{
				$currentPageButton .= '<input type="button" class="button-primary" id="add" name="add" value="Ajouter" />';
			}
		}
		elseif(current_user_can('wplazare_edit_user_fiches'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="Enregistrer et retour" />';
			if(wplazare_fiches::getNextQuestionId(wplazare_tools::varSanitizer($_REQUEST['id']), wplazare_tools::varSanitizer($_REQUEST['user_id']))!=wplazare_tools::varSanitizer($_REQUEST['id']))
				$currentPageButton .= '<input type="button" class="button-primary" id="saveandcontinue" name="saveandcontinue" value="Question suivante" />';
		}

		if(isset($_REQUEST['user_id']))
			$user_post = '&user_id='.wplazare_tools::varSanitizer($_REQUEST['user_id']);
		
		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_fiches::getListingSlug()).$user_post . '" class="button add-new-h2" >Retour</a></h2>';

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
	function getElement($elementId = '',$user = '')
	{
		global $wpdb;
		$elements = array();
		$moreQuery = "";

		if($elementId != '')
		{
			$moreQuery = " AND WPFICHES.id = '" . $elementId . "' ";
		}
		
		if($user != '')
		{
			$moreQuery = " AND WPFICHES.user = '" . $user . "' ";
		}

		$query = $wpdb->prepare(
		"SELECT WPFICHES.*, WPQUESTIONS.texte
		FROM " . wplazare_fiches::getDbTable() . " AS WPFICHES, ".wplazare_questions::getDbTable()." AS WPQUESTIONS WHERE WPFICHES.question=WPQUESTIONS.id ". $moreQuery." ORDER BY WPQUESTIONS.rang ASC" 
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
	* getNextQuestionId($id,$user_id)
	* return l'id de la prochaine 'fiche' ou l'id elle même si il n'y en a pas
	* 
	* @param int $id l'id dont on cherche le suivant
	* @param int $user_id l'id de l'utilisateur
	* 
	* @return int $id Un id référence de fiche
	*/
	function getNextQuestionId($id,$user_id){
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT id FROM " . wplazare_fiches::getDbTable()." WHERE id>'".$id."' AND user='".$user_id."' ORDER BY id");

		$elements = $wpdb->get_results($query);
		
		if(count($elements = $wpdb->get_results($query))>0) return $elements[0]->id;
		return $id;
	}
}