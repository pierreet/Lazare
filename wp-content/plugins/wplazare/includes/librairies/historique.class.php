<?php
/**
* Define the different method to access or create users 'historique'
* 
*	Define the different method to access or create users 'historique'
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Define the different method to access or create users 'historique'
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_historique
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wplazare_historique';
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
		return WPLAZARE_URL_SLUG_HISTORIQUE_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_HISTORIQUE_EDITION;
	}
	
	/**
	*	Get the constant name of the current class
	*
	*	@return string The constant name of the class
	*/
	function getDbTable()
	{
		return 'wp_lazare_historique';
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
		$slug = isset($_REQUEST['page']) ? wplazare_tools::varSanitizer($_REQUEST['page']) : '';

		$title = __('Historique', 'wplazare');
		 
		if($action != '')
		{
			if($slug == WPLAZARE_URL_SLUG_HISTORIQUE_LISTING && $action == 'edit' && $objectInEdition!='')
			{
				$editedItem = wplazare_historique::getElement($objectInEdition);
				$title .= ' de '.wplazare_tools::getUserName($editedItem->post_author)
				.' du '.date("d/m/Y", strtotime($editedItem->post_date));
			}
			elseif($action == 'add')
			{
				$title .=' - ' . __('Nouvelle page', 'wplazare');
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
		$pageAction = isset($_REQUEST[wplazare_historique::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_historique::getDbTable() . '_action']) : '';
		$id = isset($_REQUEST[wplazare_historique::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_historique::getDbTable()]['id']) : '';
		if(current_user_can('wplazare_edit_historique') && ($pageAction == 'edit' || $pageAction == 'add' || $pageAction == 'editandcontinue'))
		{
			$my_post = array();
			$my_post['ID'] = $id;
			$my_post['post_author'] = wplazare_tools::varSanitizer($_REQUEST[wplazare_historique::getDbTable()]['user']);
			$my_post['post_title'] = wp_strip_all_tags(wplazare_tools::varSanitizer($_REQUEST[wplazare_historique::getDbTable()]['titre']));
			$my_post['post_content'] = wp_kses_post($_REQUEST[wplazare_historique::getDbTable()]['content']);
			wp_update_post( $my_post );
			
			if($my_post['post_content'] == '' && !wplazare_historique::hasAttachment($id))
				wp_delete_post($id);
		}
		else if($pageAction == 'add')
		{
			$actionResult = 'userNotAllowedForActionAdd';
		}

		/*	When an action is launched and there is a result message	*/
		/************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
		/************		CHANGE ERROR MESSAGE FOR SPECIFIC CASE					*************/
		/****************************************************************************/
		if($actionResult != '')
		{
			//$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wplazare_historique::getDbTable()]['name'] . '</span>';
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
				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />' . 'Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action.'.$actionResult;
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
		
		$options_utilisateur ='<option>- Utilisateurs -</option>';
		$options_utilisateur_seuls = '';
		$selected_true='';
		$selected_false='';
		if(isset($_REQUEST['search1']) && $_REQUEST['search1']!='' && $_REQUEST[$_REQUEST['search1']] == 'true') $selected_true = "selected='selected'";
		else if(isset($_REQUEST['search1']) && $_REQUEST['search1']!='' && $_REQUEST[$_REQUEST['search1']] == 'false') $selected_false = "selected='selected'";
		$options_piece_jointe ="<option>- Pi&egrave;ce Jointe -</option><option $selected_true value='true'>Avec Pi&egrave;ce jointe</option><option $selected_false value='false'>Sans pi&egrave;ce jointe</option>";
		
		$filter = array();
		if(isset($_REQUEST['search0']) && $_REQUEST['search0']!=''){
			$filter[$_REQUEST['search0']] =  $_REQUEST[$_REQUEST['search0']];
		}
		if(isset($_REQUEST['utilisateur']) && $_REQUEST['utilisateur']!=''){
			$filter['utilisateur'] =  $_REQUEST['utilisateur'];
		}
		if(isset($_REQUEST['search1']) && $_REQUEST['search1']!=''){
			$filter[$_REQUEST['search1']] =  $_REQUEST[$_REQUEST['search1']];
		}
		if(!isset($_REQUEST['search0'])) $_REQUEST['search0'] = 'utilisateur';
		foreach (wplazare_tools::getLocataires() as $key => $value){
			$selected = "";
			if(isset($_REQUEST['search0'])) 
				if($_REQUEST[$_REQUEST['search0']] == $key)
					$selected = "selected='selected'";
			$options_utilisateur_seuls .= "<option $selected value='$key'>$value</option>";
		};
		
		$selectForm='<form  method="post" enctype="multipart/form-data">
		<select name="utilisateur" id="utilisateur">'.$options_utilisateur.$options_utilisateur_seuls.'</select>
		<select name="piece_jointe" id="piece_jointe">'.$options_piece_jointe.'</select>	
		<input type="submit" class="button-primary" value="'.__('recherche', 'wplazare').'"/><input type="hidden" name="search0" value="utilisateur"/>
		<input type="hidden" name="search1" value="piece_jointe"/>
		</form>
		';
		
		$addForm='<form method="post" style="float:right;" enctype="multipart/form-data" action="'.admin_url('admin.php?page=' . wplazare_historique::getEditionSlug() . '&amp;action=add').'">
		<select name="utilisateur" id="utilisateur">'.$options_utilisateur_seuls.'</select>	
		<input type="submit" class="button-primary" value="'.__('ajouter_page', 'wplazare').'"/>
		</form>';
		
		/*	Start the table definition	*/
		$tableId = 'Historique_list';
		$tableSummary = 'Historique des utilisateurs/';
		$tableTitles = array();
		$tableTitles[] = 'Utilisateur';
		$tableTitles[] = 'Date';
		$tableTitles[] = 'Titre';
		$tableTitles[] = 'Pi&egrave;ce jointe';
		$tableClasses = array();
		$tableClasses[] = 'wplazare' . wplazare_historique::getCurrentPageCode() . '_utilisateur_column';
		$tableClasses[] = 'wplazare' . wplazare_historique::getCurrentPageCode() . '_date_column';
		$tableClasses[] = 'wplazare' . wplazare_historique::getCurrentPageCode() . '_titre_column';
		$tableClasses[] = 'wplazare' . wplazare_historique::getCurrentPageCode() . '_piece_jointe_column filter-select';

		$line = 0;
		$elementList = wplazare_historique::getElement('',$filter);
		if(count($elementList) > 0)
		{
			foreach($elementList as $element)
			{
				$tableRowsId[$line] = wplazare_historique::getCurrentPageCode() . '_' . $element->ID;

				$subRowActions = '';
				if(current_user_can('wplazare_edit_historique'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_historique::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->ID.'&user_id='.$user_id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Modifier', 'wplazare') . '</a>';
				}
				elseif(current_user_can('wplazare_view_historique'))
				{
					$editAction = admin_url('admin.php?page=' . wplazare_historique::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->ID.'&user_id='.$user_id);
					$subRowActions .= '
		<a href="' . $editAction . '" >' . __('Voir', 'wplazare') . '</a>';
				}
				
				$rowActions = '
	<div id="rowAction' . $element->ID . '" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
				unset($tableRowValue);
				$utilisateur = wplazare_tools::getUserName($element->post_author);
				$tableRowValue[] = array('class' => wplazare_historique::getCurrentPageCode() . '_utilisateur_cell', 'value' => $utilisateur. $rowActions);
				$tableRowValue[] = array('class' => wplazare_historique::getCurrentPageCode() . '_date_cell', 'value' => $element->post_date);
				$tableRowValue[] = array('class' => wplazare_historique::getCurrentPageCode() . '_titre_cell', 'value' => $element->post_title);
				$piece_jointe = wplazare_historique::hasAttachment($element->ID) ? 'OUI' : 'NON'; 
				$tableRowValue[] = array('class' => wplazare_historique::getCurrentPageCode() . '_piece_jointe_cell', 'value' => $piece_jointe);
				
				$tableRows[] = $tableRowValue;
				$line++;
			}
		}
		else
		{
			$subRowActions = '';
			if(current_user_can('wplazare_edit_user_historique'))
			{
				$subRowActions .= '
	<a href="' . admin_url('admin.php?page=' . wplazare_historique::getEditionSlug() . '&amp;action=add') . '" >' . 'Cr&eacute;er la fiche' . '</a>';
			}
			$rowActions = '
	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions . '
	</div>';
			$tableRowsId[] = wplazare_historique::getDbTable() . '_noResult';
			unset($tableRowValue);
			if($user_id == '') $tableRowValue[] = array('class' => wplazare_historique::getCurrentPageCode() . '_name_cell', 'value' => 'Pas de r&eacute;sultat.' . '
	<a href="' . admin_url('users.php?page=users_extended') . '" >' . 'Afficher les utilisateurs' . '</a>');
			else $tableRowValue[] = array('class' => wplazare_historique::getCurrentPageCode() . '_name_cell', 'value' => 'L\'historique n\'a pas &eacute;t&eacute; cr&eacute;&eacute;.' . $rowActions);
			$tableRows[] = $tableRowValue;
		}
		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $addForm.$selectForm.$listItemOutput;
	}
	
	function elementEdition($itemToEdit = '')
	{
		global $post_ID;
		$editedItem = '';
		
		if($itemToEdit != '')
		{
			$editedItem = wplazare_historique::getElement($itemToEdit);
			$post_ID = $editedItem->ID;
		}
		else{
			/* Création du post vide */
			$my_post = array(
				'post_title' => 'Page du '.date('d/m/Y'),
				'post_content' => '',
				'post_author' => wplazare_tools::varSanitizer($_REQUEST['utilisateur']),
				'post_category' => array(WPLAZARE_CATEGORY_HISTORIQUE),
				'post_status' => 'publish',
  				);

  			$post_ID = wp_insert_post( $my_post );
  			$editedItem = wplazare_historique::getElement($post_ID);
		}
		
		
		$the_form_content_hidden = $the_form_general_content = '';
		$newForm = '';
		
		/* ID */
		$input_def = array('type' => 'hidden', 'name' => 'id', 'value' => $editedItem->ID);
		
		$newForm .= wplazare_form::check_input_type($input_def, wplazare_historique::getDbTable());
		
		/* USER */
		$input_def = array('type' => 'select', 'name' => 'user', 'value' => $editedItem->post_author, 'possible_value' => wplazare_tools::getLocataires(), 'valueToPut' => 'index');
		
		$the_input = wplazare_form::check_input_type($input_def, wplazare_historique::getDbTable());

		$newFormInput = '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_apparts::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
				<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_apparts::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
				' . $the_input . '
			</div>
		</div>';
		
		$newForm .= $newFormInput;
		
		/* DATE */
		$input_def = array('type' => 'hidden', 'name' => 'date', 'value' => $editedItem->post_date);
		
		$newForm .= wplazare_form::check_input_type($input_def, wplazare_historique::getDbTable());
		
		/* TITRE */
		$input_def = array('type' => 'text', 'name' => 'titre', 'value' => $editedItem->post_title);
		
		$the_input = wplazare_form::check_input_type($input_def, wplazare_historique::getDbTable());

		$newFormInput = '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_apparts::getCurrentPageCode() . '_' . $input_def['name'] . '_label alignleft" >
				<label for="' . $input_def['name'] . '" >' . __($input_def['name'], 'wplazare') . '</label>
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_apparts::getCurrentPageCode() . '_' . $input_def['name'] . '_input alignleft" >
				' . $the_input . '
			</div>
		</div>';
		
		$newForm .= $newFormInput;
		
		
		$the_form_general_content .= $newForm;

		/*	Define the different action available for the edition form	*/
		$formAddAction = admin_url('admin.php?page=' . wplazare_historique::getEditionSlug() . '&amp;action=edit');
		$formEditAction = admin_url('admin.php?page=' . wplazare_historique::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit);
		$formAction = $formAddAction;
		if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'edit'))
		{
			$formAction = $formEditAction;
		}

		echo '
<form name="' . wplazare_historique::getDbTable() . '_form" id="' . wplazare_historique::getDbTable() . '_form" method="post" action="' . $formAction . '" enctype="multipart/form-data" >
' . wplazare_form::form_input(wplazare_historique::getDbTable() . '_action', wplazare_historique::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
' . wplazare_form::form_input(wplazare_historique::getDbTable() . '_form_has_modification', wplazare_historique::getDbTable() . '_form_has_modification', 'no' , 'hidden') . '
<div id="wplazareFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wplazare_' . wplazare_historique::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content;
		wplazare_historique::buildEditor($editedItem);
		wplazare_historique::buildDocuments($editedItem);
		echo '
	</div>
</div>
</form>
<script type="text/javascript" >
	wplazare(document).ready(function(){
		wplazareMainInterface("' . wplazare_historique::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_historique::getEditionSlug()) . '");

		wplazare("#delete").click(function(){
			wplazare("#' . wplazare_historique::getDbTable() . '_action").val("delete");
			deletePaymentForm();
		});
		if(wplazare("#' . wplazare_historique::getDbTable() . '_action").val() == "delete"){
			deletePaymentForm();
		}
		function deletePaymentForm(){
			if(confirm(wplazareConvertAccentTojs(\'&Ecirc;tes vous s&ucirc;r de vouloir supprimer ce formulaire?\'))){
				wplazare("#' . wplazare_historique::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_historique::getDbTable() . '_action").val("edit");
			}
		}
	});
</script>';
		return '';
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
			if(current_user_can('wplazare_edit_historique'))
			{
				$currentPageButton .= '<input type="button" class="button-primary" id="add" name="add" value="Ajouter" />';
			}
		}
		elseif(current_user_can('wplazare_edit_historique'))
		{
			$currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="Enregistrer et retour" />';
		}
		
		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_historique::getListingSlug()) . '" class="button add-new-h2" >Retour</a></h2>';

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
		$elements = array();
		$piece_jointe_filter = '';

		if($elementId != '')
		{
			return get_post($elementId);
		}
		
		$args = array(
		    'category'        => WPLAZARE_CATEGORY_HISTORIQUE,
		    'orderby'         => 'post_date',
		    'order'           => 'DESC'
		);
		if($filters != ''){
			foreach ($filters as $key => $value){
				switch ($key)
				{
					case 'utilisateur':
						if($value != '- Utilisateur -'):
							$args['author'] = $value;
						endif;
					break;
					case 'piece_jointe':
						if($value != '- Pièce Jointe -'):
							$piece_jointe_filter = $value;
						endif;
					break;
				}
			}
		}
		if($piece_jointe_filter == '')
			return get_posts($args);
			
		$results = array();
		foreach (get_posts($args) as $post){
			if ( wplazare_historique::hasAttachment($post->ID) && ($piece_jointe_filter == 'true') ) $results[] = $post;
		}
		return $results;
	}
	
	/**
	* hasAttachment($post_id)
	* return si un post à un fichier attaché
	* 
	* @param int $post_id l'id du post
	* 
	* @return bool si oui ou non, le post a un fichier attaché
	*/
	function hasAttachment($post_id){
		$args = array(
			'post_type' => 'attachment',
			'numberposts' => null,
			'post_status' => null,
			'post_parent' => $post_id
		);
		$results = get_posts( $args );
		return !(empty( $results ) ) ;
	}
	/**
	* hasAttachment($post_id)
	* Construit l'éditeur et le rempli avec le content du post donné en paramètre
	* 
	* @param objet post $editedItem le post
	* 
	*/
	function buildEditor($editedItem){
		if($editedItem != '')
		{
			$content = $editedItem->post_content;
			$editor_id = wplazare_historique::getDbTable().'[content]';
			$args = array('teeny' => 'true');
			wp_editor( $content, $editor_id, $args );
		}
	}
	
	function buildDocuments($editedItem){
		echo '<h3>Les documents liés:</h3><ul>';
		$args = array(
			'post_type' => 'attachment',
			'numberposts' => null,
			'post_status' => null,
			'post_parent' => $editedItem->ID
		); 
		$attachments = get_posts($args);
		if ($attachments) {
			foreach ($attachments as $attachment) {
				echo '<li>';
				the_attachment_link($attachment->ID, false);
				echo '</li>';
			}
		}
		echo '</ul>';
	}
}