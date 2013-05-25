<?php
/**
* Define the different method to access or create users 'questions'
* 
*	Define the different method to access or create users 'questions'
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Define the different method to access or create users 'questions'
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_questions
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wplazare_questions';
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
		return WPLAZARE_URL_SLUG_QUESTIONS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_QUESTIONS_EDITION;
	}
	
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
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
		return 'Configuration des fiches';
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
		$pageAction = isset($_REQUEST[wplazare_questions::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_questions::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wplazare_questions::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_questions::getDbTable()]['id']) : '';

		/*	Define the database operation type from action launched by the user	 */
		/*************************				GENERIC				**************************/
		/*************************************************************************/
		if(($pageAction != '') && (($pageAction == 'edit') || ($pageAction == 'editandcontinue') || ($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_edit_user_fiches'))
			{
				$_REQUEST[wplazare_questions::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				if($pageAction == 'delete')
				{
					if(current_user_can('wplazare_edit_user_fiches'))
					{
						$_REQUEST[wplazare_questions::getDbTable()]['status'] = 'deleted';
					}
					else
					{
						$actionResult = 'userNotAllowedForActionDelete';
					}
				}
				$actionResult = wplazare_database::update($_REQUEST[wplazare_questions::getDbTable()], $id, wplazare_questions::getDbTable());
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
				$_REQUEST[wplazare_questions::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wplazare_questions::getDbTable()]['status'] = 'deleted';
				$actionResult = wplazare_database::update($_REQUEST[wplazare_questions::getDbTable()], $id, wplazare_questions::getDbTable());
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
				$_REQUEST[wplazare_questions::getDbTable()]['creation_date'] = date('Y-m-d H:i:s');
				$actionResult = wplazare_database::save($_REQUEST[wplazare_questions::getDbTable()], wplazare_questions::getDbTable());
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
			$elementIdentifierForMessage = '<span class="bold" ></span>';
			if($actionResult == 'error')
			{/*	CHANGE HERE FOR SPECIFIC CASE	*/
				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />' . sprintf('Une erreur est survenue lors de l\'enregistrement de %s', $elementIdentifierForMessage);
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
				$actionResultMessage = '<img src="' . WPLAZARE_SUCCES_ICON . '" alt="action success" class="wplazarePageMessage_Icon" />' . sprintf('L\'enregistrement de %s s\'est d&eacute;roul&eacute; avec succ&eacute;s', $elementIdentifierForMessage);
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
		$role = isset($_REQUEST['role']) ? wplazare_tools::varSanitizer($_REQUEST['role']): WPLAZARE_ROLE_PERSONNE_ACCUEILLIE;

		$options_role ='';
		$filter = array();
		$filter['role'] =  $role;
		foreach (wplazare_questions::getRoles() as $result){
			$selected = ($result->role == $role) ? "selected='selected'" : '';
			$options_role .= '<option '.$selected.' value="'.$result->role.'">'.__($result->role,'wplazare').'</option>';
		};
		
		$selectForm='<form  method="post" enctype="multipart/form-data">
		<select name="role" id="role">' . $options_role . '</select>	
		<input type="submit" class="button-primary" value="recherche"/><input type="hidden" name="search0" value="role"/></form>
		';
		
		$listItemOutput = '';
		/*	Start the table definition	*/
		$tableId = 'Question_list';
		$tableSummary = 'Configuration des Fiches/';
		$tableTitles = array();
		$tableTitles[] = 'Role';
		$tableTitles[] = 'Question';
		$tableTitles[] = 'Rang';
		$tableClasses = array();
		$tableClasses[] = 'wplazare' . wplazare_questions::getCurrentPageCode() . '_role_column';
		$tableClasses[] = 'wplazare' . wplazare_questions::getCurrentPageCode() . '_question_column';
		$tableClasses[] = 'wplazare' . wplazare_questions::getCurrentPageCode() . '_rang_column';

		$line = 0;
		if(empty($filter)) $filter = '';
		$elementList = wplazare_questions::getElement('',$filter);
		if(count($elementList) > 0 && $role)
		{
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wplazare_questions::getCurrentPageCode() . '_' . $element->id;

				$subRowActions = '';
				if(current_user_can('wplazare_edit_user_fiches'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_questions::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id.'&role='.$role);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Modifier', 'wplazare') . '</a>';
				}
				elseif(current_user_can('wplazare_view_user_fiches'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_questions::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id.'&role='.$role);
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
		<a href="' . admin_url('admin.php?page=' . wplazare_questions::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id.'&role='.$role). '" >' . __('Supprimer', 'wplazare') . '</a>';
				}
				
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
				unset($tableRowValue);
				$tableRowValue[] = array('class' => wplazare_questions::getCurrentPageCode() . '_role_cell', 'value' => $element->role. $rowActions);
				$tableRowValue[] = array('class' => wplazare_questions::getCurrentPageCode() . '_question_cell', 'value' => stripslashes($element->texte));
				$tableRowValue[] = array('class' => wplazare_questions::getCurrentPageCode() . '_rang_cell', 'value' => $element->rang);
				
				$tableRows[] = $tableRowValue;
				$line++;
			}
		}
		else
		{
			$subRowActions = '';
			if(current_user_can('wplazare_edit_user_questions'))
			{
				$subRowActions .= '
	<a href="' . admin_url('admin.php?page=' . wplazare_questions::getEditionSlug() . '&amp;action=add'.'&user_id='.$user_id) . '" >' . 'Ajouter' . '</a>';
			}
			$rowActions = '
	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
			$tableRowsId[] = wplazare_questions::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wplazare_questions::getCurrentPageCode() . '_name_cell', 'value' => 'Aucun question n\'a encore &eacute;t&eacute; cr&eacute;&eacute;' . $rowActions);
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $selectForm.$listItemOutput;
	}
	
	function elementEdition($itemToEdit = '')
	{
		$role = isset($_REQUEST['role']) ? wplazare_tools::varSanitizer($_REQUEST['role']) : WPLAZARE_ROLE_PERSONNE_ACCUEILLIE;
		global $currencyList;
		$dbFieldList = wplazare_database::fields_to_input(wplazare_questions::getDbTable());

		$editedItem = '';
		$mandatoryFieldList = array();
		if($itemToEdit != '')
		{
			$editedItem = wplazare_questions::getElement($itemToEdit);
		}
		else $editedItem = new wplazare_question('',$role,'',wplazare_questions::getNewRang($role));
		
		$the_form_content_hidden = $the_form_general_content = '';
		$newAppartForm = $newAppartFormMultiple = '';
		
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];
			
			$pageAction = isset($_REQUEST[wplazare_questions::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_questions::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wplazare_questions::getDbTable()][$input_name]) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_questions::getDbTable()][$input_name]) : '';
			$currentFieldValue = $input_value;
			
			if(is_object($editedItem))
			{
				$currentFieldValue = $editedItem->$input_name;
			}
			elseif(($pageAction != '') && ($requestFormValue != ''))
			{
				$currentFieldValue = $requestFormValue;
			}
			
			if(($input_name == 'creation_date') || ($input_name == 'last_update_date')|| ($input_name == 'status'))
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

			$the_input = wplazare_form::check_input_type($input_def, wplazare_questions::getDbTable());
			$newAppartFormInput = '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_questions::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
				<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
				' . $helpForField . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_questions::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
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
		$formAddAction = admin_url('admin.php?page=' . wplazare_questions::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit.'&amp;role=' . $role);
		$formEditAction = admin_url('admin.php?page=' . wplazare_questions::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit.'&amp;role=' . $role);
		$formAction = $formAddAction;
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit'))
		{
			$formAction = $formEditAction;
		}

		$the_form = '
<form name="' . wplazare_questions::getDbTable() . '_form" id="' . wplazare_questions::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data" >
' . wplazare_form::form_input(wplazare_questions::getDbTable() . '_action', wplazare_questions::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
' . wplazare_form::form_input(wplazare_questions::getDbTable() . '_form_has_modification', wplazare_questions::getDbTable() . '_form_has_modification', 'no' , 'hidden') . '
<div id="wplazareFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wplazare_' . wplazare_questions::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wplazare(document).ready(function(){
		wplazareMainInterface("' . wplazare_questions::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_questions::getEditionSlug()) . '&role='.$role.'");

		wplazare("#delete").click(function(){
			wplazare("#' . wplazare_questions::getDbTable() . '_action").val("delete");
			deletePaymentForm();
		});
		if(wplazare("#' . wplazare_questions::getDbTable() . '_action").val() == "delete"){
			deletePaymentForm();
		}
		function deletePaymentForm(){
			if(confirm(wplazareConvertAccentTojs(\'&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?\'))){
				wplazare("#' . wplazare_questions::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_questions::getDbTable() . '_action").val("edit");
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
			$currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="Enregistrer et retour" /><input type="button" class="button-primary" id="saveandcontinue" name="saveandcontinue" value="Question suivante" />';
		}

		$role = isset($_REQUEST['role']) ? wplazare_tools::varSanitizer($_REQUEST['role']) : '';
		
		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_questions::getListingSlug()).'&role='.$role . '" class="button add-new-h2" >Retour</a></h2>';

		return $currentPageButton;
	}
	
	/**
	*	Get the existing element list into database
	*
	*	@param integer $elementId optionnal The element identifier we want to get. If not specify the entire list will be returned
	*	@param string $elementMail optionnal The mail of element to get into database. If not specify the entire list will be returned
	**	@param string $filters optionnal Filter element...
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
			$moreQuery = " AND WPQUESTIONS.id = '" . $elementId . "' ";
		}
		
		if($filters != ''){
			foreach ($filters as $key => $value){
				switch ($key)
				{
				case 'role':
					if($value != '- Role -'):
						$moreQuery .= " AND WPQUESTIONS.role LIKE '".$value."' ";
					endif;
				break;
				}
			}
		}
		
		$query = $wpdb->prepare(
		"SELECT WPQUESTIONS.*
		FROM ".wplazare_questions::getDbTable()." AS WPQUESTIONS WHERE status='valid' ". $moreQuery." ORDER BY WPQUESTIONS.rang ASC" 
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
	
	function getQuestionTexte($id){
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT texte FROM " . wplazare_questions::getDbTable()." WHERE id='".$id."'");

		return $wpdb->get_row($query);
	}
	
	/**
	* getRoles()
	* return les différents roles dans un tableau d'objet database
	* 
	*/
	function getRoles(){
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT DISTINCT role FROM " . wplazare_questions::getDbTable() );

		return $wpdb->get_results($query);
	}
	
	/**
	* getNewId($role)
	* return un nouvel id pour un role donné qui est le plus grand id + 1
	* 
	*/
	function getNewRang($role){
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT MAX(rang) AS max FROM " . wplazare_questions::getDbTable()." WHERE role LIKE '$role'" );

		$results = $wpdb->get_results($query);
		if(!empty($results))return intval($results[0]->max)+1;
		return '';
	}
	
	/**
	* createFiche($user_id)
	* créer la fiche d'un utilisateur en fonction de son role et renvoie l'id de la première question à afficher.
	* 
	*/
	function createFiche($user_id){
		global $wpdb;
		
		$result_id = '';
		$role = wplazare_tools::getRole($user_id);
		
		$query = $wpdb->prepare("SELECT WPQUESTIONS.id FROM " . wplazare_questions::getDbTable()." as WPQUESTIONS WHERE WPQUESTIONS.role LIKE '$role' AND status='valid' ORDER BY WPQUESTIONS.rang ASC" );
		$results = $wpdb->get_results($query);
		$creation_date = $last_update_date = date('Y-m-d H:i:s');
		$first_time = true;
		foreach($results as $question){
			$query = $wpdb->prepare("INSERT INTO  `lazare`.`wp_lazare_fiche` (`id` ,`creation_date` ,`status` ,`last_update_date` ,`user` ,`question` ,`reponse`)
VALUES (NULL , '$creation_date' ,  'valid', '$last_update_date' ,  '$user_id',  '$question->id',  '')");
			$results = $wpdb->get_results($query);
			if($first_time){
				$first_time = false;
				$result_id = $wpdb->insert_id;
			} 
		}
		
		return $result_id;
	}
}