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
class wplazare_apparts
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
    public static function getCurrentPageCode()
	{
		return 'wplazare_apparts';
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
		return WPLAZARE_URL_SLUG_APPARTS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
    public static function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_APPARTS_EDITION;
	}
	
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	public static function getDbTable()
	{
		return WPLAZARE_DBT_APPARTS;
	}

	/**
	*	Define the title of the page 
	*
	*	@return string $title The title of the page looking at the environnement
	*/
    public static function pageTitle()
	{
		return 'Gestion des appartements';
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
		$pageAction = isset($_REQUEST[wplazare_apparts::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_apparts::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wplazare_apparts::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_apparts::getDbTable()]['id']) : '';

		/*	Define the database operation type from action launched by the user	 */
		/*************************				GENERIC				**************************/
		/*************************************************************************/
		if(($pageAction != '') && (($pageAction == 'edit') || ($pageAction == 'editandcontinue') || ($pageAction == 'delete')))
		{
			if(current_user_can('wplazare_edit_appart'))
			{
				$_REQUEST[wplazare_apparts::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				if($pageAction == 'delete')
				{
					if(current_user_can('wplazare_delete_appart'))
					{
						$_REQUEST[wplazare_apparts::getDbTable()]['status'] = 'deleted';
					}
					else
					{
						$actionResult = 'userNotAllowedForActionDelete';
					}
				}
				$actionResult = wplazare_database::update($_REQUEST[wplazare_apparts::getDbTable()], $id, wplazare_apparts::getDbTable());
                if( $_REQUEST[wplazare_apparts::getDbTable()]['status'] == "deleted")
                {
                    $locations_list = wplazare_tools::getLocationByAppart($id);
                    foreach($locations_list as $location)
                    {
                        $location_id = $location->id;
                        $informationsToSet = array('status' => 'deleted');
                        wplazare_database::update($informationsToSet, $location_id, wplazare_locations::getDbTable());
                    }
                }
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
				$_REQUEST[wplazare_apparts::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
				$_REQUEST[wplazare_apparts::getDbTable()]['status'] = 'deleted';
				$actionResult = wplazare_database::update($_REQUEST[wplazare_apparts::getDbTable()], $id, wplazare_apparts::getDbTable());
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
				$_REQUEST[wplazare_apparts::getDbTable()]['creation_date'] = date('Y-m-d H:i:s');
				$actionResult = wplazare_database::save($_REQUEST[wplazare_apparts::getDbTable()], wplazare_apparts::getDbTable());
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
			//$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wplazare_apparts::getDbTable()]['name'] . '</span>';
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
				$actionResultMessage = '<img src="' . WPLAZARE_SUCCES_ICON . '" alt="action success" class="wplazarePageMessage_Icon" />L\'enregistrement s\'est d&eacute;roul&eacute; avec succ&egrave;s';
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
		$options_ville ='<option>- Ville -</option>';
		$options_responsable ='<option>- Responsable -</option>';
		$options_maison='<option>- Maison -</option>';
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
		foreach (wplazare_apparts::getVilles() as $result){
			$selected = "";
			if(isset($_REQUEST['search0'])) 
				if($_REQUEST[$_REQUEST['search0']] == $result->ville)
					$selected = "selected='selected'";
			$options_ville .= '<option '.$selected.'>'.stripslashes($result->ville).'</option>';
		};
		foreach (wplazare_tools::getLocataires() as $key => $value){
			if($key!='0'){
				$selected = "";
				if(isset($_REQUEST['search1'])) 
					if($_REQUEST[$_REQUEST['search1']] == $key)
						$selected = "selected='selected'";
				$options_responsable .= '<option '.$selected.' value='.$key.'>'.$value.'</option>';
			}
		};
		foreach (wplazare_apparts::getMaisons() as $result){
			$selected = "";
			if(isset($_REQUEST['search2'])) 
				if($_REQUEST[$_REQUEST['search2']] == $result['id'])
					$selected = "selected='selected'";
			$options_maison .= '<option '.$selected.' value='.$result['id'].'>'.stripslashes($result['name']).'</option>';
		};
		
		$selectForm='<form  method="post" enctype="multipart/form-data">
		<select name="ville" id="ville">'.$options_ville.'</select>
		<select name="responsable" id="responsable">'.$options_responsable.'</select>
		<select name="maison" id="maison">'.$options_maison.'</select>		
		<input type="submit" class="button-primary" value="recherche"/><input type="hidden" name="search0" value="ville"/><input type="hidden" name="search1" value="responsable"/><input type="hidden" name="search2" value="maison"/></form>
		';
		
		$listItemOutput = '';
		/*	Start the table definition	*/
		$tableId = 'Acteur_list';
		$tableSummary = 'Liste des appartements de Lazare/';
		$tableTitles = array();
		$tableTitles[] = 'Adresse';
		$tableTitles[] = 'Responsable';
		$tableTitles[] = 'Loyer';
		$tableTitles[] = 'Maison';
		$tableTitles[] = 'Locataires';
		$tableTitles[] = '% occupation';
		$tableTitles[] = 'Email';
		$tableClasses = array();
		$tableClasses[] = 'wplazare' . wplazare_apparts::getCurrentPageCode() . '_adresse_column';		
		$tableClasses[] = 'wplazare' . wplazare_apparts::getCurrentPageCode() . '_responsable_column';
		$tableClasses[] = 'wplazare' . wplazare_apparts::getCurrentPageCode() . '_prix_column';
		$tableClasses[] = 'wplazare' . wplazare_apparts::getCurrentPageCode() . '_maison_column';
		$tableClasses[] = 'wplazare' . wplazare_apparts::getCurrentPageCode() . '_locataires_column';
		$tableClasses[] = 'wplazare' . wplazare_apparts::getCurrentPageCode() . '_pourcentage_occupation_column';
		$tableClasses[] = 'wplazare' . wplazare_apparts::getCurrentPageCode() . '_email_column filter-false';
		
		$line = 0;
		if(empty($filter)) $filter = '';
		$elementList = wplazare_apparts::getElement('',$filter);
		if(count($elementList) > 0)
		{
			foreach($elementList as $element)
			{
				
				$tableRowsId[$line] = wplazare_apparts::getCurrentPageCode() . '_' . $element->id;

				$subRowActions = '';
				if(current_user_can('wplazare_edit_appart'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_apparts::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Modifier', 'wplazare') . '</a>';
				}
				elseif(current_user_can('wplazare_view_appart_details'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_apparts::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wplazare') . '</a>';
				}
				if(current_user_can('wplazare_delete_appart'))
				{
					if($subRowActions != '')
					{
						$subRowActions .= '&nbsp;|&nbsp;';
					}
					$subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wplazare_apparts::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id). '" >' . __('Supprimer', 'wplazare') . '</a>';
				}
				$rowActions = '
	<div id="rowAction' . $element->id . '" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
				unset($tableRowValue);
				$tableRowValue[] = array('class' => wplazare_apparts::getCurrentPageCode() . '_adresse_cell', 'value' => stripslashes($element->code_postal.' '.$element->ville.' - '.$element->adresse ). $rowActions);
				
				$resp_name=wplazare_tools::getUserLink($element->responsable,wplazare_tools::getUserName($element->responsable));
				$tableRowValue[] = array('class' => wplazare_apparts::getCurrentPageCode() . '_responsable_cell', 'value' => stripslashes($resp_name));
				$tableRowValue[] = array('class' => wplazare_apparts::getCurrentPageCode() . '_prix_cell', 'value' => $element->prix);
                $current_maison = wplazare_maisons::getElement($element->maison);
				$maison_name='<a href="'.admin_url( 'admin.php?page=wplazare_maisons&action=edit&id='.$element->maison, 'http' ).'">'.$current_maison->nom.'</a>';
				$tableRowValue[] = array('class' => wplazare_apparts::getCurrentPageCode() . '_maison_cell', 'value' => stripslashes($maison_name));
				$locataires_id = wplazare_tools::getLocatairesByAppart($element->id);
				$locataires = '';
				foreach($locataires_id as $locataire_id){
					if($locataires != '') $locataires .= ', ';
					$locataires .= wplazare_tools::getUserLink($locataire_id->id,wplazare_tools::getFirstName($locataire_id->id));
				}
				$tableRowValue[] = array('class' => wplazare_apparts::getCurrentPageCode() . '_locataires_cell', 'value' => stripslashes($locataires));
				$pourcentage_occupation = round((count($locataires_id) / $element->nbr_logements)*100,0) . '%';
				$tableRowValue[] = array('class' => wplazare_apparts::getCurrentPageCode() . '_pourcentage_occupation_cell', 'value' => $pourcentage_occupation);
				$tableRowValue[] = array('class' => wplazare_apparts::getCurrentPageCode() . '_email_cell', 'value' => wplazare_users::buildMailTo($locataires_id));
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
	<a href="' . admin_url('admin.php?page=' . wplazare_apparts::getEditionSlug() . '&amp;action=add') . '" >' . 'Ajouter' . '</a>';
			}
			$rowActions = '
	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
			$tableRowsId[] = wplazare_apparts::getDbTable() . '_noResult';
			unset($tableRowValue);
			$tableRowValue[] = array('class' => wplazare_apparts::getCurrentPageCode() . '_name_cell', 'value' => 'Aucun appartement ne correspond &agrave; votre recherche.' . $rowActions);
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $selectForm.$listItemOutput;
	}
	
function elementEdition($itemToEdit = '')
	{
		$dbFieldList = wplazare_database::fields_to_input(wplazare_apparts::getDbTable());

		$editedItem = '';
		if($itemToEdit != '')
		{
			$editedItem = wplazare_apparts::getElement($itemToEdit);
		}
		
		$the_form_content_hidden = $the_form_general_content = '';
		$newAppartForm = '';
		
		foreach($dbFieldList as $input_key => $input_def)
		{
			$input_name = $input_def['name'];
			$input_value = $input_def['value'];
			
			$pageAction = isset($_REQUEST[wplazare_apparts::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_apparts::getDbTable() . '_action']) : '';
			$requestFormValue = isset($_REQUEST[wplazare_apparts::getDbTable()][$input_name]) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_apparts::getDbTable()][$input_name]) : '';
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
			
			if($input_name == 'maison')
			{
				$input_def['possible_value'] = wplazare_tools::getMaisonsForSelect();
				$input_def['type'] = 'select';
				$input_def['valueToPut'] = 'index';
			}
			
			if($input_name == 'responsable')
			{
				$input_def['possible_value'] = wplazare_tools::getResponsablesForSelect();
				$input_def['type'] = 'select';
				$input_def['valueToPut'] = 'index';
			}
			
			if($input_name == 'association')
			{
				$input_def['possible_value'] = wplazare_tools::getAssociationsForSelect();
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

			$the_input = wplazare_form::check_input_type($input_def, wplazare_apparts::getDbTable());
			
			$newAppartFormInput = '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_apparts::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
				<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
				' . $helpForField . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_apparts::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
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
		$formAddAction = admin_url('admin.php?page=' . wplazare_apparts::getEditionSlug() . '&amp;action=edit');
		$formEditAction = admin_url('admin.php?page=' . wplazare_apparts::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit);
		$formAction = $formAddAction;
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit'))
		{
			$formAction = $formEditAction;
		}

		$the_form = '
<form name="' . wplazare_apparts::getDbTable() . '_form" id="' . wplazare_apparts::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data" >
' . wplazare_form::form_input(wplazare_apparts::getDbTable() . '_action', wplazare_apparts::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
' . wplazare_form::form_input(wplazare_apparts::getDbTable() . '_form_has_modification', wplazare_apparts::getDbTable() . '_form_has_modification', 'no' , 'hidden') . '
<div id="wplazareFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wplazare_' . wplazare_apparts::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
<script type="text/javascript" >
	wplazare(document).ready(function(){
		wplazareMainInterface("' . wplazare_apparts::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_apparts::getEditionSlug()) . '");

		wplazare("#delete").click(function(){
			wplazare("#' . wplazare_apparts::getDbTable() . '_action").val("delete");
			deletePaymentForm();
		});
		if(wplazare("#' . wplazare_apparts::getDbTable() . '_action").val() == "delete"){
			deletePaymentForm();
		}
		function deletePaymentForm(){
			if(confirm(wplazareConvertAccentTojs(\'&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?\'))){
				wplazare("#' . wplazare_apparts::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_apparts::getDbTable() . '_action").val("edit");
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

		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_apparts::getListingSlug()) . '" class="button add-new-h2" >Retour</a></h2>';

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
	public static function getElement($elementId = '',$filters = '')
	{
		global $wpdb;
		$elements = array();
		$moreQuery = "";
		
		$moreQuery = 'WHERE WPAPPARTS.status LIKE "valid" ';

		if($elementId != '')
		{
			$moreQuery .= " AND WPAPPARTS.id = '" . $elementId . "' ";
		}
		
		
		
		if($filters != ''){
			foreach ($filters as $key => $value){
				switch ($key)
				{
				case 'ville':
					if($value != '- Ville -'):
						if($moreQuery!='') $moreQuery.=" AND ";
						$moreQuery .= "WPAPPARTS.ville LIKE '".$value."' ";
					endif;
				break;
				case 'responsable':
					if($value != '- Responsable -'):
						if($moreQuery!='') $moreQuery.=" AND ";
						$moreQuery .= "WPAPPARTS.responsable = '".$value."' ";
					endif;
				break;
				case 'maison':
					if($value != '- Maison -'):
						if($moreQuery!='') $moreQuery.=" AND ";
						$moreQuery .= "WPAPPARTS.maison = '".$value."' ";
					endif;
				break;
				}
			}
		}
		
		$query = $wpdb->prepare(
		"SELECT WPAPPARTS.*
		FROM " . wplazare_apparts::getDbTable() . " AS WPAPPARTS ". $moreQuery." ORDER BY code_postal, adresse"
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
	* return les différentes villes dans un tableau d'objet database
	* 
	*/
	function getVilles(){
		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT DISTINCT ville FROM " . wplazare_apparts::getDbTable() );

		return $wpdb->get_results($query);
	}
	/**
	* getAppartResponsables()
	* return les différents responsables dans un tableau d'objet database
	* 
	*/
	function getAppartResponsables(){		
		$elements = array(); 

		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT DISTINCT responsable FROM " . wplazare_apparts::getDbTable() );

		foreach ($wpdb->get_results($query) as $element){
			$elements[$element->responsable] = wplazare_tools::getUserName($element->responsable);
		}
		
		return $elements;
	}
	/**
	* getMaisons()
	* return les différentes maisons dans un tableau d'objet database
	* 
	*/
	function getMaisons(){
		global $wpdb;
		
		$elements = array(); 

		$query = $wpdb->prepare(
		"SELECT DISTINCT maison FROM " . wplazare_apparts::getDbTable()." WHERE maison IS NOT NULL ORDER BY maison" );

		foreach ($wpdb->get_results($query) as $element){
			$maison = wplazare_maisons::getElement($element->maison);
			$elements[] = array('name' => $maison->nom, 'id' => $element->maison);
		}
		
		return $elements;
	}
	/**
	* getMaisonsList()
	* return les différentes maisons dans un tableau ('id_maison' => 'maison')
	* 
	*/
	function getMaisonsList(){
		global $wpdb;
		
		$elements = array(); 

		$query = $wpdb->prepare(
		"SELECT DISTINCT maison FROM " . wplazare_apparts::getDbTable()." WHERE maison IS NOT NULL ORDER BY maison" );
		
		foreach ($wpdb->get_results($query) as $element){
            $maison = wplazare_maisons::getElement($element->maison);;
			$elements[$element->maison] = $maison->nom;
		}
		return $elements;
	}
	
	/**
	* getAdresse($id)
	* return l'adresse d'un appart donné
	* 
	*/
	function getAdresseComplete($appart){
		if($appart) return $appart->adresse.', '.$appart->code_postal.' '.$appart->ville;
		return '';
	}
	/**
	* getAppartsForSelect()
	* return les différents apparts dans un tableau de type 'id' => 'code_postal + adresse'
	* 
	*/
	function getAppartsForSelect(){		
		$results = array(); 

		global $wpdb;

		$query = $wpdb->prepare(
		"SELECT WPAPPARTS.* FROM " . wplazare_apparts::getDbTable() ." AS WPAPPARTS WHERE WPAPPARTS.status LIKE 'valid'" );

		foreach ($wpdb->get_results($query) as $element){
			$results[$element->id] = $element->code_postal . " - " . $element->adresse;
		}
		
		return $results;
	}
}