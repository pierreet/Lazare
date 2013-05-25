<?php
/**
* Define the different method to access or create lazare 'association'
* 
*	Define the different method to access or create lazare 'association'
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Define the different method to access or create lazare 'association'
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_associations
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wplazare_associations';
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
		return WPLAZARE_URL_SLUG_ASSOCIATIONS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_ASSOCIATIONS_EDITION;
	}
	
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
	{
		return WPLAZARE_DBT_ASSOCIATIONS;
	}

	/**
	*	Define the title of the page 
	*
	*	@return string $title The title of the page looking at the environnement
	*/
	function pageTitle()
	{
		return 'Gestion des associations';
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
		$pageAction = isset($_REQUEST[wplazare_associations::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_associations::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wplazare_associations::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_associations::getDbTable()]['id']) : '';

		/*	Define the database operation type from action launched by the user	 */
		/*************************				GENERIC				**************************/
		/*************************************************************************/
		if(($pageAction != '') && (($pageAction == 'edit') || ($pageAction == 'editandcontinue') || ($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_edit_appart'))
			{
				$_REQUEST[wplazare_associations::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				if($pageAction == 'delete')
				{
					if(current_user_can('wplazare_delete_appart'))
					{
						$_REQUEST[wplazare_associations::getDbTable()]['status'] = 'deleted';
					}
					else
					{
						$actionResult = 'userNotAllowedForActionDelete';
					}
				}
				$actionResult = wplazare_database::update($_REQUEST[wplazare_associations::getDbTable()], $id, wplazare_associations::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionEdit';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_delete_appart'))
			{
				$_REQUEST[wplazare_associations::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wplazare_associations::getDbTable()]['status'] = 'deleted';
				$actionResult = wplazare_database::update($_REQUEST[wplazare_associations::getDbTable()], $id, wplazare_associations::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionDelete';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'save') || ($pageAction == 'saveandcontinue') || ($pageAction == 'add')))
		{
			if(current_user_can('wplazare_add_appart'))
			{
				$_REQUEST[wplazare_associations::getDbTable()]['creation_date'] = date('Y-m-d H:i:s');
				$actionResult = wplazare_database::save($_REQUEST[wplazare_associations::getDbTable()], wplazare_associations::getDbTable());
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
			//$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wplazare_associations::getDbTable()]['name'] . '</span>';
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
		global $currencyIconList;
		$listItemOutput = '';
		/*	Start the table definition	*/
		$tableId = 'Acteur_list';
		$tableSummary = 'Liste des associationements de Lazare/';
		$tableTitles = array();
		$tableTitles[] = 'Nom';
		$tableTitles[] = 'Responsable';
		$tableTitles[] = 'Tr&eacute;sorier';
		$tableTitles[] = 'Email';
		$tableClasses = array();
		$tableClasses[] = 'wplazare' . wplazare_associations::getCurrentPageCode() . '_nom_column';
		$tableClasses[] = 'wplazare' . wplazare_associations::getCurrentPageCode() . '_responsable_column';
		$tableClasses[] = 'wplazare' . wplazare_associations::getCurrentPageCode() . '_tresorier_column';
		$tableClasses[] = 'wplazare' . wplazare_associations::getCurrentPageCode() . '_email_column filter-false';

		$line = 0;
		$elementList = wplazare_associations::getElement();
		if(count($elementList) > 0)
		{
			foreach($elementList as $element)
			{
				
				$tableRowsId[$line] = wplazare_associations::getCurrentPageCode() . '_' . $element->id;

				$subRowActions = '';
				if(current_user_can('wplazare_edit_association'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_associations::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Modifier', 'wplazare') . '</a>';
				}
				elseif(current_user_can('wplazare_view_association'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_associations::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wplazare') . '</a>';
				}
				if(current_user_can('wplazare_delete_association'))
				{
					if($subRowActions != '')
					{
						$subRowActions .= '&nbsp;|&nbsp;';
					}
					$subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wplazare_associations::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id). '" >' . __('Supprimer', 'wplazare') . '</a>';
				}
				
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
				unset($tableRowValue);
				$tableRowValue[] = array('class' => wplazare_associations::getCurrentPageCode() . '_nom_cell', 'value' => stripslashes($element->nom). $rowActions);
				$resp_name=wplazare_tools::getUserLink($element->responsable,wplazare_tools::getUserName($element->responsable));
				$tableRowValue[] = array('class' => wplazare_associations::getCurrentPageCode() . '_responsable_cell', 'value' => stripslashes($resp_name));
				$tresorier_name=wplazare_tools::getUserLink($element->responsable,wplazare_tools::getUserName($element->responsable));
				$tableRowValue[] = array('class' => wplazare_associations::getCurrentPageCode() . '_tresorier_cell', 'value' => stripslashes($tresorier_name));
				$apparts = wplazare_associations::findApparts($element->id);
				$locataires_id = array();
				foreach($apparts as $appart){
					$locataires_id = array_merge(wplazare_tools::getLocatairesByAppart($appart->id),$locataires_id);
				}
				$tableRowValue[] = array('class' => wplazare_associations::getCurrentPageCode() . '_email_cell', 'value' => wplazare_users::buildMailTo($locataires_id));
				$tableRows[] = $tableRowValue;
				$line++;
			}
		}
		else
		{
			$subRowActions = '';
			if(current_user_can('wplazare_edit_association'))
			{
				$subRowActions .= '
	<a href="' . admin_url('admin.php?page=' . wplazare_associations::getEditionSlug() . '&amp;action=add') . '" >' . 'Ajouter' . '</a>';
			}
			$rowActions = '
	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
			$tableRowsId[] = wplazare_associations::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wplazare_associations::getCurrentPageCode() . '_name_cell', 'value' => 'Aucun association n\'a encore &eacute;t&eacute; cr&eacute;&eacute;e' . $rowActions);
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $listItemOutput;
	}
	
function elementEdition($itemToEdit = '')
	{
		
		$tmp='';
		global $currencyList;
		$dbFieldList = wplazare_database::fields_to_input(wplazare_associations::getDbTable());

		$editedItem = '';
		$mandatoryFieldList = array();
		if($itemToEdit != '')
		{
			$editedItem = wplazare_associations::getElement($itemToEdit);
		}
		
		$the_form_content_hidden = $the_form_general_content = '';
		$newAppartForm = $newAppartFormMultiple = '';
		
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];
			
			$pageAction = isset($_REQUEST[wplazare_associations::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_associations::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wplazare_associations::getDbTable()][$input_name]) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_associations::getDbTable()][$input_name]) : '';
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
			
			if($input_name == 'responsable')
			{
				$input_def['possible_value'] = wplazare_tools::getResponsablesForSelect();
				$input_def['type'] = 'select';
				$input_def['valueToPut'] = 'index';
			}
			
			if($input_name == 'tresorier')
			{
				$input_def['possible_value'] = wplazare_tools::getTresoriersForSelect();
				$input_def['type'] = 'select';
				$input_def['valueToPut'] = 'index';
			}

			$input_def['value'] = $currentFieldValue;

			switch($input_def['name'])
			{
				default:
					$helpForField = '';
				break;
			}

			$the_input = wplazare_form::check_input_type($input_def, wplazare_associations::getDbTable());
			$newAppartFormInput = '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_associations::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
				<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
				' . $helpForField . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_associations::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
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
		$formAddAction = admin_url('admin.php?page=' . wplazare_associations::getEditionSlug() . '&amp;action=edit');
		$formEditAction = admin_url('admin.php?page=' . wplazare_associations::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit);
		$formAction = $formAddAction;
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit'))
		{
			$formAction = $formEditAction;
		}

		$the_form = '
<form name="' . wplazare_associations::getDbTable() . '_form" id="' . wplazare_associations::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data" >
' . wplazare_form::form_input(wplazare_associations::getDbTable() . '_action', wplazare_associations::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
' . wplazare_form::form_input(wplazare_associations::getDbTable() . '_form_has_modification', wplazare_associations::getDbTable() . '_form_has_modification', 'no' , 'hidden') . '
<div id="wplazareFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wplazare_' . wplazare_associations::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wplazare(document).ready(function(){
		wplazareMainInterface("' . wplazare_associations::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_associations::getEditionSlug()) . '");

		wplazare("#delete").click(function(){
			wplazare("#' . wplazare_associations::getDbTable() . '_action").val("delete");
			deletePaymentForm();
		});
		if(wplazare("#' . wplazare_associations::getDbTable() . '_action").val() == "delete"){
			deletePaymentForm();
		}
		function deletePaymentForm(){
			if(confirm(wplazareConvertAccentTojs(\'&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?\'))){
				wplazare("#' . wplazare_associations::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_associations::getDbTable() . '_action").val("edit");
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
			if(current_user_can('wplazare_add_appart'))
			{
				$currentPageButton .= '<input type="button" class="button-primary" id="add" name="add" value="Ajouter" />';
			}
		}
		elseif(current_user_can('wplazare_edit_appart'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="Enregistrer" /><input type="button" class="button-primary" id="saveandcontinue" name="saveandcontinue" value="' . __('Enregistrer et continuer l\'&eacute;dition', 'wplazare') . '" />';
		}
		if(current_user_can('wplazare_delete_appart') && ($action != 'add'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="Supprimer" />';
		}

		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_associations::getListingSlug()) . '" class="button add-new-h2" >Retour</a></h2>';

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
		$moreQuery = "";

		if($elementId != '')
		{
			$moreQuery = " WPASSOCIATIONS.id = '" . $elementId . "' ";
		}
		
		if($filters != ''){
			foreach ($filters as $key => $value){
				switch ($key)
				{
				case 'responsable':
					if($value != '- Responsable -'):
						if($moreQuery!='') $moreQuery.=" AND ";
						$moreQuery .= "WPASSOCIATIONS.responsable = '".$value."' ";
					endif;
				break;
				}
			}
		}
		
		
		
		if($moreQuery != '') $moreQuery = 'WHERE '.$moreQuery;
		
		$moreQuery .= " ORDER BY WPASSOCIATIONS.nom";

		$query = $wpdb->prepare(
		"SELECT WPASSOCIATIONS.*
		FROM " . wplazare_associations::getDbTable() . " AS WPASSOCIATIONS ". $moreQuery
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
	
	function findApparts($id){
		global $wpdb;
		
		$query = $wpdb->prepare(
		"SELECT WPAPPARTS.*	FROM " . wplazare_apparts::getDbTable() . " AS WPAPPARTS WHERE WPAPPARTS.association = ".$id." ORDER BY WPAPPARTS.code_postal");
		
		/*	Get the query result regarding on the function parameters. If there must be only one result or a collection	*/
		return $wpdb->get_results($query);
		 
	}
	
	function getTresorier($user_id){
		global $wpdb;
		
		if(!is_numeric($id)) return ''; 
		
		$query = $wpdb->prepare(
		"SELECT WPASSOCIATIONS.tresorier ".
		"FROM wplazare_locations::getDbTable() AS WPLOCATIONS ".
		"LEFT JOIN wplazare_associations::getDbTable() AS WPASSOCIATIONS ON WPLOCATIONS.association=WPASSOCIATIONS.id ".
		"WHERE WPLOCATIONS.user=$user_id");
		
		$elements = $wpdb->get_results($query);
		
		if(count($elements)==1)return $elements[0]->tresorier;
		return '';
		 
	}
}