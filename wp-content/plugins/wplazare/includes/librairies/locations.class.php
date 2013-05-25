<?php
/**
* Define the different method to access or create lazare 'maison'
* 
*	Define the different method to access or create lazare 'maison'
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Define the different method to access or create lazare 'maison'
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_locations
{
	
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wplazare_locations';
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
		return WPLAZARE_URL_SLUG_LOCATIONS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_LOCATIONS_EDITION;
	}
	
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
	{
		return WPLAZARE_DBT_LOCATIONS;
	}
	
	/**
	*	Define the title of the page 
	*
	*	@return string $title The title of the page looking at the environnement
	*/
	function pageTitle()
	{
		return 'Gestion habitants-locations';
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
		$pageAction = isset($_REQUEST[wplazare_locations::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_locations::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wplazare_locations::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_locations::getDbTable()]['id']) : '';

		/*	Define the database operation type from action launched by the user	 */
		/*************************				GENERIC				**************************/
		/*************************************************************************/
		if(($pageAction != '') && (($pageAction == 'edit') || ($pageAction == 'editandcontinue') || ($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_edit_appart'))
			{
				$_REQUEST[wplazare_locations::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				if($pageAction == 'delete')
				{
					if(current_user_can('wplazare_delete_location'))
					{
						$_REQUEST[wplazare_locations::getDbTable()]['status'] = 'deleted';
					}
					else
					{
						$actionResult = 'userNotAllowedForActionDelete';
					}
				}
				$actionResult = wplazare_database::update($_REQUEST[wplazare_locations::getDbTable()], $id, wplazare_locations::getDbTable());
			}
			else
			{
				$actionResult = 'userNotAllowedForActionEdit';
			}
		}
		elseif(($pageAction != '') && (($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_delete_location'))
			{
				$_REQUEST[wplazare_locations::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wplazare_locations::getDbTable()]['status'] = 'deleted';
				$actionResult = wplazare_database::update($_REQUEST[wplazare_locations::getDbTable()], $id, wplazare_locations::getDbTable());
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
				$_REQUEST[wplazare_locations::getDbTable()]['creation_date'] = date('Y-m-d H:i:s');
				$actionResult = wplazare_database::save($_REQUEST[wplazare_locations::getDbTable()], wplazare_locations::getDbTable());
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
			//$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wplazare_locations::getDbTable()]['name'] . '</span>';
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
	function elementList()
	{
		$select_true = '';
		$select_false = '';
		if(isset($_REQUEST['search1'])&&($_REQUEST[$_REQUEST['search1']] == 'false')) $select_false = "selected='selected'";
		else $select_true = "selected='selected'";
		
		$options_ville ='<option>- Ville -</option>';
		$options_habite ='<option '.$select_true.' value="true">Location en cours</option><option '.$select_false.' value="false">Location Termin&eacute;e</option>';
		$options_maison ='<option>- Maison -</option>';
		
		foreach (wplazare_locations::getVilles() as $result){
			$selected = "";
			if(isset($_REQUEST['search0'])) 
				if($_REQUEST[$_REQUEST['search0']] == $result->ville)
					$selected = "selected='selected'";
			$options_ville .= '<option '.$selected.' >'.stripslashes($result->ville).'</option>';
		};
		foreach (wplazare_locations::getMaisons() as $result){
			$selected = "";
			if(isset($_REQUEST['search2'])) 
				if($_REQUEST[$_REQUEST['search2']] == $result->id)
					$selected = "selected='selected'";
			$options_maison .= '<option '.$selected.' value="'.$result->id.'">'.stripslashes($result->nom).'</option>';
		};
		
		$selectForm='<form  method="post" enctype="multipart/form-data">
		<select name="ville" id="ville">'.$options_ville.'</select>
		<select name="habite" id="habite">'.$options_habite.'</select>
		<select name="maison" id="maison">'.$options_maison.'</select>		
		<input type="submit" class="button-primary" value="recherche"/>
		<input type="hidden" name="search0" value="ville"/>
		<input type="hidden" name="search1" value="habite"/>
		<input type="hidden" name="search2" value="maison"/>
		</form>
		';
		
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
		
		$listItemOutput = '';
		/*	Start the table definition	*/
		$tableId = 'Acteur_list';
		$tableSummary = 'Liste des locationements de Lazare/';
		$tableTitles = array();
		$tableTitles[] = 'Locataire';
		$tableTitles[] = 'Adresse';
		$tableTitles[] = 'Date Entrée';
		$tableTitles[] = 'Date Sortie';
		$tableClasses = array();
		$tableClasses[] = 'wplazare' . wplazare_locations::getCurrentPageCode() . '_locataire_column';
		$tableClasses[] = 'wplazare' . wplazare_locations::getCurrentPageCode() . '_adresse_column';		
		$tableClasses[] = 'wplazare' . wplazare_locations::getCurrentPageCode() . '_date_entree_column';
		$tableClasses[] = 'wplazare' . wplazare_locations::getCurrentPageCode() . '_date_sortie_column';

		$line = 0;
		if(empty($filter)) $filter = '';
		$elementList = wplazare_locations::getElement('',$filter);
		if(count($elementList) > 0)
		{
			foreach($elementList as $element)
			{
                $current_appart = wplazare_apparts::getElement($element->appartement);
				$tableRowsId[$line] = wplazare_locations::getCurrentPageCode() . '_' . $element->id;

				$subRowActions = '';
				if(current_user_can('wplazare_edit_appart'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_locations::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Modifier', 'wplazare') . '</a>';
				}
				elseif(current_user_can('wplazare_view_appart_details'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_locations::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wplazare') . '</a>';
				}
				if(current_user_can('wplazare_delete_location'))
				{
					if($subRowActions != '')
					{
						$subRowActions .= '&nbsp;|&nbsp;';
					}
					$subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wplazare_locations::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id). '" >' . __('Supprimer', 'wplazare') . '</a>';
				}
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
				unset($tableRowValue);
				$locataire_name=wplazare_tools::getUserLink($element->user,wplazare_tools::getUserName($element->user));
				$tableRowValue[] = array('class' => wplazare_locations::getCurrentPageCode() . '_locataire_cell', 'value' => $locataire_name. $rowActions);
				$adresse_complete = wplazare_apparts::getAdresseComplete($current_appart);
				$tableRowValue[] = array('class' => wplazare_locations::getCurrentPageCode() . '_adresse_cell', 'value' => stripslashes($adresse_complete));
				$tableRowValue[] = array('class' => wplazare_locations::getCurrentPageCode() . '_date_entree_cell', 'value' => $element->date_debut);
				$date_fin='';
				if($element->date_fin != '0000-00-00') $date_fin = $element->date_fin;
				$tableRowValue[] = array('class' => wplazare_locations::getCurrentPageCode() . '_date_sortie_cell', 'value' => $date_fin);
				$tableRows[] = $tableRowValue;
				$line++;
			}
		}
		else
		{
			$subRowActions = '';
			if(current_user_can('wplazare_add_appart'))
			{
				$subRowActions .= '
	<a href="' . admin_url('admin.php?page=' . wplazare_locations::getEditionSlug() . '&amp;action=add') . '" >' . 'Ajouter' . '</a>';
			}
			$rowActions = '
	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
			$tableRowsId[] = wplazare_locations::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wplazare_locations::getCurrentPageCode() . '_name_cell', 'value' => 'Aucune location ne correspond &agrave; votre recherche.' . $rowActions);
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $selectForm.$listItemOutput;
	}
	
function elementEdition($itemToEdit = '')
	{
		$dbFieldList = wplazare_database::fields_to_input(wplazare_locations::getDbTable());

		$editedItem = '';
		if($itemToEdit != '')
		{
			$editedItem = wplazare_locations::getElement($itemToEdit);
		}
		
		$the_form_content_hidden = $the_form_general_content = '';
		$newAppartForm = '';
		
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];
			
			$pageAction = isset($_REQUEST[wplazare_locations::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_locations::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wplazare_locations::getDbTable()][$input_name]) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_locations::getDbTable()][$input_name]) : '';
			$currentFieldValue = $input_value;
			
			if(is_object($editedItem))
			{
				$currentFieldValue = $editedItem->$input_name;
			}
			elseif(($pageAction != '') && ($requestFormValue != ''))
			{
				$currentFieldValue = $requestFormValue;
			}
			
			if(($input_name == 'creation_date') || ($input_name == 'last_update_date') )
			{
				$input_def['type'] = 'hidden';
			}
			
			if($input_name == 'user')
			{
				$input_def['possible_value'] = wplazare_tools::getLocataires();
				$input_def['type'] = 'select';
				$input_def['valueToPut'] = 'index';
				$input_def['option'] = "class='combobox'";
			}
			
			if($input_name == 'appartement')
			{
				$input_def['possible_value'] = wplazare_apparts::getAppartsForSelect();
				$input_def['type'] = 'select';
				$input_def['valueToPut'] = 'index';
				$input_def['option'] = "class='combobox'";
			}
			
			if($input_name == 'date_debut' ||$input_name == 'date_fin' )
			{
				$input_def['option'] = "class='jquery_date_picker'";
			}

			$input_def['value'] = $currentFieldValue;
			
			if(($input_name == 'date_fin') && ($input_def['value']=='0000-00-00'))
				$input_def['value'] = '';

			switch($input_def['name'])
			{
				default:
					$helpForField = '';
				break;
			}

			$the_input = wplazare_form::check_input_type($input_def, wplazare_locations::getDbTable());
			
			$newAppartFormInput = '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_locations::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
				<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
				' . $helpForField . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_locations::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
				' . $the_input . '
			</div>
		</div>';
			if($input_name == 'user')
			{
				$newAppartFormInput .= '
		<script type="text/javascript">
			jQuery(document).ready(function(){
				var valTmp = jQuery("#user").val();
				jQuery("#user option").sort(locataireSort).appendTo("#user");
				jQuery("#user").val(valTmp);
			});
		</script>
		
		';
			}

			
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
		$formAddAction = admin_url('admin.php?page=' . wplazare_locations::getEditionSlug() . '&amp;action=edit');
		$formEditAction = admin_url('admin.php?page=' . wplazare_locations::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit);
		$formAction = $formAddAction;
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit'))
		{
			$formAction = $formEditAction;
		}

		$the_form = '
<form name="' . wplazare_locations::getDbTable() . '_form" id="' . wplazare_locations::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data" >
' . wplazare_form::form_input(wplazare_locations::getDbTable() . '_action', wplazare_locations::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
' . wplazare_form::form_input(wplazare_locations::getDbTable() . '_form_has_modification', wplazare_locations::getDbTable() . '_form_has_modification', 'no' , 'hidden') . '
<div id="wplazareFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wplazare_' . wplazare_locations::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wplazare(document).ready(function(){
		wplazareMainInterface("' . wplazare_locations::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_locations::getEditionSlug()) . '");

		wplazare("#delete").click(function(){
			wplazare("#' . wplazare_locations::getDbTable() . '_action").val("delete");
			deletePaymentForm();
		});
		if(wplazare("#' . wplazare_locations::getDbTable() . '_action").val() == "delete"){
			deletePaymentForm();
		}
		function deletePaymentForm(){
			if(confirm(wplazareConvertAccentTojs(\'&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?\'))){
				wplazare("#' . wplazare_locations::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_locations::getDbTable() . '_action").val("edit");
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
		if(current_user_can('wplazare_delete_location') && ($action != 'add'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="Supprimer" />';
		}

		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_locations::getListingSlug()) . '" class="button add-new-h2" >Retour</a></h2>';

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
			$moreQuery = " WPLOCATIONS.id = '" . $elementId . "' ";
		}
		
		$left_join = '';
		if($filters != ''){
			$left_join = " LEFT JOIN ".wplazare_apparts::getDbTable()." AS WPAPPARTS ON WPLOCATIONS.appartement=WPAPPARTS.id ";
			foreach ($filters as $key => $value){
				switch ($key)
				{
				case 'ville':
					if($value != '- Ville -'):
						if($moreQuery!='') $moreQuery.=" AND";
						$moreQuery .= " WPAPPARTS.ville LIKE '".$value."' ";
					endif;
				break;
				case 'maison':
					if($value != '- Maison -'):
						if($moreQuery!='') $moreQuery.=" AND";
						$moreQuery .= " WPAPPARTS.maison = '".$value."' ";
					endif;
				break;
				case 'habite':
					if($value != 'true'):
						if($moreQuery!='') $moreQuery.=" AND";
						$moreQuery .= " WPLOCATIONS.date_fin IS NOT NULL AND WPLOCATIONS.date_fin != '0000-00-00' ";
					else:
						if($moreQuery!='') $moreQuery.=" AND";
						$moreQuery .= " (WPLOCATIONS.date_fin IS NULL OR WPLOCATIONS.date_fin = '0000-00-00') ";
					endif;
				break;
				}
			}
		}
		if($moreQuery!='') $moreQuery.=" AND";
		$moreQuery .= ' WPLOCATIONS.status LIKE "valid" ';
		
		if($moreQuery != '') $moreQuery = ' WHERE '.$moreQuery;
		
		$query = $wpdb->prepare(
		"SELECT WPLOCATIONS.*
		FROM " . wplazare_locations::getDbTable() . " AS WPLOCATIONS ". $left_join.$moreQuery
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
	* getVilles()
	* return les différentes villes des apparts utilisés dans location dans un tableau d'objet database
	* 
	*/
	function getVilles(){
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT DISTINCT WPAPPARTS.ville AS ville ".
		"FROM ".wplazare_locations::getDbTable()." AS WPLOCATIONS ".
		"LEFT JOIN ".wplazare_apparts::getDbTable()." AS WPAPPARTS ON WPLOCATIONS.appartement=WPAPPARTS.id ".
		" ORDER BY WPAPPARTS.ville"
		);
		return $wpdb->get_results($query);
	}
	/**
	* getMaisons()
	* return les différentes maisons des apparts utilisés dans location dans un tableau d'objet database
	* 
	*/
	function getMaisons(){
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT DISTINCT WPMAISONS.nom AS nom, WPMAISONS.id AS id ".
		"FROM ".wplazare_locations::getDbTable()." AS WPLOCATIONS ".
		"LEFT JOIN ".wplazare_apparts::getDbTable()." AS WPAPPARTS ON WPLOCATIONS.appartement=WPAPPARTS.id ".
		"LEFT JOIN ".wplazare_maisons::getDbTable()." AS WPMAISONS ON WPAPPARTS.maison=WPMAISONS.id ".
		" ORDER BY WPMAISONS.nom"
		);
		return $wpdb->get_results($query);
	}
	
	/**
	 * 
	 * getLocationsForSelect
	 */
	function getLocationsForSelect(){		
		$elements = array(); 

		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT WPLOCATIONS.id AS location_id, WPAPPARTS.code_postal AS CP, WPAPPARTS.ville AS V, WPAPPARTS.adresse AS A, WPLOCATIONS.user AS user_id FROM " . wplazare_locations::getDbTable() ." AS WPLOCATIONS ".
		"LEFT JOIN ".wplazare_apparts::getDbTable()." AS WPAPPARTS ON WPLOCATIONS.appartement=WPAPPARTS.id ".
		"WHERE WPLOCATIONS.status LIKE 'valid' ".
		" ORDER BY WPAPPARTS.code_postal ");
		
		foreach ($wpdb->get_results($query) as $element){
			$elements[$element->location_id] = $element->CP." ".$element->V." - ".$element->A." - ".wplazare_tools::getUserName($element->user_id);
		}
		
		return $elements;
	}
	
	/**
	 * 
	 * getLocationUser($location_id) retourne l'utilisateur de la location
	 * @param int $location_id
	 */
	function getLocationUser($location_id){		
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT WPLOCATIONS.user FROM " . wplazare_locations::getDbTable() ." AS WPLOCATIONS
		 WHERE WPLOCATIONS.id = '". $location_id . "'" );
		
		return $wpdb->get_row($query);;
	}
}