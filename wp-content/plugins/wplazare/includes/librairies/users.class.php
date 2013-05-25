<?php
class wplazare_users {
	/**
	 *	Get the url listing slug of the current class
	 *
	 *	@return string The table of the class
	 */

	function getCurrentPageCode() {
		return 'wplazare_users';
	}

	/**
	 *	Get the url listing slug of the current class
	 *
	 *	@return string The table of the class
	 */

	function getPageIcon() {	

		return '';

	}

	/**
	 *	Get the url listing slug of the current class
	 *
	 *	@return string The table of the class
	 */

	function getListingSlug() {
		return WPLAZARE_URL_SLUG_USERS_LISTING;
	}

	/**
	 *	Get the url edition slug of the current class
	 *
	 *	@return string The table of the class
	 */

	function getEditionSlug() {

		return WPLAZARE_URL_SLUG_USERS_EDITION;

	}

	/**
	 *	Get the database table of the current class
	 *
	 *	@return string The table of the class
	 */

	function getOrdersDbTable() {

		return WPLAZARE_DBT_ORDERS;

	}

	/**
	 *	Define the title of the page 
	 *
	 *	@return string $title The title of the page looking at the environnement
	 */

	function pageTitle() {

		/*$action = isset($_REQUEST['action']) ? wplazare_tools::varSanitizer($_REQUEST['action']) : '';
		
		$objectInEdition = isset($_REQUEST['id']) ? wplazare_tools::varSanitizer($_REQUEST['id']) : '';
		
		
		
		$title = __('Liste des acteurs', 'wplazare' );
		
		return $title;*/

		if (isset($_REQUEST['id']) && $_REQUEST['id'] != '')
			return 'Fiche utilisateur';

		if (isset($_REQUEST['action']))
			return 'Ajout Utilisateur';

		return 'Liste des utilisateurs';

	}

	/**
	 *	Define the different message and action after an action is send through the element interface
	 */

	function elementAction() {

		global $wpdb;

		$actionResultMessage = '';

		$pageMessage = $actionResult = '';

		$pageAction = isset(
				$_REQUEST[wplazare_users::getCurrentPageCode() . '_action']) ? wplazare_tools::varSanitizer(
						$_REQUEST[wplazare_users::getCurrentPageCode()
								. '_action']) : '';

		/*	Define the database operation type from action launched by the user	 */

		/*************************				GENERIC				**************************/

		/*************************************************************************/ 

		$message = '';

		if (current_user_can('wplazare_edit_user')) {

			if (isset($_REQUEST['validate'])) {

				if ($_REQUEST['validate'] != "") {

					$locataire_id = $_REQUEST['validate'];

					$message = wplazare_users::validate($locataire_id);

					$actionResult = 'done_validate';

				} else {

					$actionResult = 'error_validate';

				}

			} elseif (isset($_REQUEST['validatePostulant'])) {

				if ($_REQUEST['validatePostulant'] != "") {

					$locataire_id = $_REQUEST['validatePostulant'];

					$message = wplazare_users::validatePostulant($locataire_id);

					$actionResult = 'done_validate';

				} else {

					$actionResult = 'error_validate';

				}

			} elseif ($pageAction == 'add') {

				$message = wplazare_users::insertUser();

				if ($message != "error") {

					$actionResult = 'done_validate';

				} else {

					$actionResult = 'error_validate';

				}

			} elseif ($pageAction == 'edit') {

				$message = wplazare_users::updateUser();

				if ($message != "error") {

					$actionResult = 'done_validate';

				} else {

					$actionResult = 'error_validate';

				}

			} elseif ($pageAction == 'delete') {

				$message = wplazare_users::deleteUser();

				if ($message != "error") {

					$actionResult = 'done_validate';

				} else {

					$actionResult = 'error_validate';

				}

			}

		} else {

			$actionResult = 'userNotAllowedForActionEdit';

		}

		/*	When an action is launched and there is a result message	*/

		/************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/

		/************		CHANGE ERROR MESSAGE FOR SPECIFIC CASE					*************/

		/****************************************************************************/

		if ($actionResult != '') {

			$elementIdentifierForMessage = '<span class="bold" >'
					. wplazare_loyers::getDbTable() . '</span>';

			if ($actionResult == 'error') {/*	CHANGE HERE FOR SPECIFIC CASE	*/

				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON
						. '" alt="action error" class="wplazarePageMessage_Icon" />'
						. sprintf(
								'Une erreur est survenue lors de l\'enregistrement dans la table %s',
								$elementIdentifierForMessage);

				if (WPLAZARE_DEBUG) {

					$actionResultMessage .= '<br/>' . $wpdb->last_error;

				}

			} elseif ($actionResult == 'done_validate') {

				$actionResultMessage = '<img src="' . WPLAZARE_SUCCES_ICON
						. '" alt="action success" class="wplazarePageMessage_Icon" />'
						. $message;

			} elseif (actionResult == 'error_validate') {

				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON
						. '" alt="action error" class="wplazarePageMessage_Icon" />'
						. 'Une erreur est survenue.';

			} elseif (($actionResult == 'userNotAllowedForActionEdit')
					|| ($actionResult == 'userNotAllowedForActionAdd')
					|| ($actionResult == 'userNotAllowedForActionDelete')) {

				$actionResultMessage = '<img src="' . WPLAZARE_ERROR_ICON
						. '" alt="action error" class="wplazarePageMessage_Icon" />'
						. 'Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action.';

			}

		}

		return $actionResultMessage;

	}

	/**
	 *	Return the list page content, containing the table that present the item list
	 *
	 *	@return string $listItemOutput The html code that output the item list
	 */

	function elementList() {

		$annuaire_link = '<div class="alignright"><a href="'
				. admin_url(
						'admin.php?page=' . WPLAZARE_URL_SLUG_USERS_LISTING
								. '&amp;action=export', 'http')
				. '">Annuaire</a></div>';

		$class = '';

		$role = '';

		if (isset($_REQUEST['role']))
			$role = wplazare_tools::varSanitizer($_REQUEST['role']);

		if (!isset($_REQUEST['role']))
			$class = 'current';

		$roles_link = '<ul class="clear subsubsub"><a href="'
				. admin_url(
						'admin.php?page=' . WPLAZARE_URL_SLUG_USERS_LISTING,
						'http') . '" class=' . $class . '>Tous</a>';

		foreach (wplazare_tools::getRoles() as $role_tmp) {

			$class = '';

			if (isset($_REQUEST['role']) && $_REQUEST['role'] == $role_tmp)
				$class = 'current';

			$roles_link .= ' | ';

			$roles_link .= '<li>' . '<a href="'
					. admin_url(
							'admin.php?page=' . WPLAZARE_URL_SLUG_USERS_LISTING
									. '&role=' . $role_tmp, 'http') . '"'
					. ' class="' . $class . '">' . __($role_tmp, 'wplazare')
					. '</a>' . '('
					. wplazare_tools::getUserCountByRole($role_tmp) . ')</li>';

		}

		$role_tmp = "attente";

		$class = '';

		if (isset($_REQUEST['role']) && $_REQUEST['role'] == $role_tmp)
			$class = 'current';

		$roles_link .= ' | ';

		$roles_link .= '<li>' . '<a href="'
				. admin_url(
						'admin.php?page=' . WPLAZARE_URL_SLUG_USERS_LISTING
								. '&role=' . $role_tmp, 'http') . '"'
				. ' class="' . $class . '">liste d\'attente volontaire</a>'
				. '(' . wplazare_tools::getUserCountByRole($role_tmp)
				. ')</li>';

		$roles_link .= '</ul>';

		$tableId = 'Acteur_list';

		$tableSummary = 'Liste des acteurs de Lazare/';

		$tableTitles = array();

		$tableTitles[] = 'Nom';

		$tableTitles[] = 'Email';

		$tableTitles[] = 'Adresse';

		$tableTitles[] = 'Tel';

		$tableTitles[] = 'Role';

		$tableTitles[] = 'Liens';

		$tableClasses = array();

		$tableClasses[] = 'wplazare' . wplazare_users::getCurrentPageCode()
				. '_nom_column';

		$tableClasses[] = 'wplazare' . wplazare_users::getCurrentPageCode()
				. '_email_column';

		$tableClasses[] = 'wplazare' . wplazare_users::getCurrentPageCode()
				. '_adresse_column';

		$tableClasses[] = 'wplazare' . wplazare_users::getCurrentPageCode()
				. '_tel_column';

		$tableClasses[] = 'wplazare' . wplazare_users::getCurrentPageCode()
				. '_role_column filter-select';

		$tableClasses[] = 'wplazare' . wplazare_users::getCurrentPageCode()
				. '_lien_column filter-false';

		$line = 0;

		$elementList = wplazare_users::getElement('', $role);

		if (count($elementList) > 0) {

			foreach ($elementList as $element) {

				$tableRowsId[$line] = wplazare_users::getCurrentPageCode()
						. '_' . $line;

				$subRowActions = '';

				if (current_user_can('wplazare_view_user_details')) {

					$id = $element['id'];

					if ($element['role'] == WPLAZARE_ROLE_DONATEUR)
						$id = $element['email'];

					$editAction = admin_url(
							'admin.php?page='
									. wplazare_users::getEditionSlug()
									. '&amp;action=edit&amp;id=' . $id);

					$subRowActions .= '

					<a href="' . $editAction . '" >'
							. __('Modifier', 'wplazare') . '</a>';

				}

				if (current_user_can('wplazare_edit_user')
						&& $element['role'] != WPLAZARE_ROLE_DONATEUR) {

					if ($subRowActions != '') {

						$subRowActions .= '&nbsp;|&nbsp;';

					}

					$url = add_query_arg(
							array('user_id' => $id,
									wplazare_users::getCurrentPageCode()
											. '_action' => 'delete'));

					$subRowActions .= '

		<a href="' . $url . '" >' . __('Supprimer', 'wplazare') . '</a>';

				}

				$rowActions = '

	<div id="rowAction' . $element['id'] . '" class="wplazareRowAction" >'
						. $subRowActions . '

	</div>';

				unset($tableRowValue);

				$tableRowValue[] = array(
						'class' => wplazare_users::getCurrentPageCode()
								. '_nom_cell',
						'value' => $element['nom'] . ' ' . $element['prenom']
								. $rowActions);

				$tableRowValue[] = array(
						'class' => wplazare_users::getCurrentPageCode()
								. '_email_cell', 'value' => $element['email']);

				$tableRowValue[] = array(
						'class' => wplazare_users::getCurrentPageCode()
								. '_adresse_cell',
						'value' => $element['adresse_complete']);

				$tableRowValue[] = array(
						'class' => wplazare_users::getCurrentPageCode()
								. '_tel_cell', 'value' => $element['tel']);

				$tableRowValue[] = array(
						'class' => wplazare_users::getCurrentPageCode()
								. '_role_cell',
						'value' => __($element['role'], 'wplazare'));

				$liens = wplazare_users::generateLinksByRole($element['id'],
						$element['role'], ' | ');

				$tableRowValue[] = array(
						'class' => wplazare_users::getCurrentPageCode()
								. '_lien_cell', 'value' => $liens);

				$tableRows[] = $tableRowValue;

				$line++;

			}

		} else {

			$subRowActions = '';

			$rowActions = '

	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions
					. '

	</div>';

			$tableRowsId[] = wplazare_offers::getDbTable() . '_noResult';

			unset($tableRowValue);

			$tableRowValue[] = array(
					'class' => wplazare_users::getCurrentPageCode()
							. '_name_cell',
					'value' => 'Aucun utilisateur n\'a encore &eacute;t&eacute; cr&eacute;&eacute;'
							. $rowActions);

			$tableRows[] = $tableRowValue;

		}

		$listItemOutput = wplazare_display::getTable($tableId, $tableTitles,
				$tableRows, $tableClasses, $tableRowsId, $tableSummary, true);

		return $annuaire_link . $roles_link . $listItemOutput;

	}

	function elementEdition($itemToEdit = '') {

		$the_view = '';

		if ($itemToEdit != '') {

			if (is_numeric($itemToEdit)) {

				$editedItem = wplazare_users::getElement($itemToEdit);

			} else {

				if ($user = get_user_by('email', $itemToEdit)) {

					$editedItem = wplazare_users::getElement($user->ID);

				} else {

					$editedItem = wplazare_users::getElement($itemToEdit,
							WPLAZARE_ROLE_DONATEUR);

				}

			}

			$user_id = $editedItem['id'];

			$fields = array('prenom' => $editedItem['prenom'],
					'nom' => $editedItem['nom'],
					'email' => $editedItem['email'],
					'adresse' => $editedItem['adresse'],
					'tel' => $editedItem['tel'],
					'date_de_naissance' => $editedItem['date_de_naissance'],
					'role' => __($editedItem['role'], 'wplazare'),
					'commentaires' => $editedItem['commentaires']);

			$prenom = array('name' => 'prenom', 'type' => 'text',
					'option' => "class='required'",
					'value' => $editedItem['prenom']);

			$nom = array('name' => 'nom', 'type' => 'text',
					'option' => "class='required'",
					'value' => $editedItem['nom']);

			$email = array('name' => 'email', 'type' => 'text',
					'option' => "class='email'",
					'value' => $editedItem['email']);

			$adresse = array('name' => 'adresse', 'type' => 'text',
					'value' => $editedItem['adresse']);

			$code_postal = array('name' => 'code_postal', 'type' => 'text',
					'value' => $editedItem['code_postal']);

			$ville = array('name' => 'ville', 'type' => 'text',
					'value' => $editedItem['ville']);

			$tel = array('name' => 'tel', 'type' => 'text',
					'value' => $editedItem['tel']);

			$date_de_naissance = array('name' => 'date_de_naissance',
					'type' => 'text',
					'value' => $editedItem['date_de_naissance']);

			$user_id_tab = array('name' => 'user_id', 'type' => 'hidden',
					'value' => $user_id);

			$role = array('name' => 'role', 'type' => 'select',
					'valueToPut' => 'index',
					'possible_value' => array(
							WPLAZARE_ROLE_POSTULANT => __(
									WPLAZARE_ROLE_POSTULANT, 'wplazare'),
							WPLAZARE_ROLE_BENEVOLE => __(
									WPLAZARE_ROLE_BENEVOLE, 'wplazare'),
							WPLAZARE_ROLE_PARTENAIRE_SOCIAL => __(
									WPLAZARE_ROLE_PARTENAIRE_SOCIAL,
									'wplazare'),
							WPLAZARE_ROLE_PERSONNE_ACCUEILLIE => __(
									WPLAZARE_ROLE_PERSONNE_ACCUEILLIE,
									'wplazare'),
							WPLAZARE_ROLE_EVENT => __(WPLAZARE_ROLE_EVENT,
									'wplazare')),
					'value' => $editedItem['role']);

			$fields = array($prenom, $nom, $email, $adresse, $code_postal,
					$ville, $tel, $date_de_naissance, $role);

			$the_form = '';

			foreach ($fields as $field) {

				$the_input = wplazare_form::check_input_type($field,
						wplazare_users::getCurrentPageCode());

				$the_form .= '

				<div class="clear" >

					<div class="wplazare_form_label wplazare_'
						. wplazare_orders::getCurrentPageCode() . '_'
						. '_label alignleft" >

						' . __($field['name'], 'wplazare')
						. '

					</div>

					<div class="wplazare_form_input wplazare_'
						. wplazare_orders::getCurrentPageCode() . '_'
						. '_input alignleft" >

						' . $the_input . '

					</div>

				</div>';

			}

			$user_id_input = wplazare_form::check_input_type($user_id_tab,
					wplazare_users::getCurrentPageCode());

			$the_form .= $user_id_input;

			$formAddAction = admin_url(
					'admin.php?page=' . wplazare_users::getEditionSlug()
							. '&amp;action=edit');

			$the_view .= ' 

			<form name="' . wplazare_users::getEditionSlug() . '_form" id="'
					. wplazare_users::getEditionSlug()
					. '_form" method="post" action="' . $formAddAction
					. '" enctype="multipart/form-data" >

				'
					. wplazare_form::form_input(
							wplazare_users::getEditionSlug() . '_action',
							wplazare_users::getEditionSlug() . '_action',
							(isset($_REQUEST['action'])
									&& ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer(
											$_REQUEST['action']) : 'edit'),
							'hidden') . '

				'
					. wplazare_form::form_input(
							wplazare_users::getEditionSlug()
									. '_form_has_modification',
							wplazare_users::getEditionSlug()
									. '_form_has_modification', 'no', 'hidden')
					. '

				<div id="wplazareFormManagementContainer" >

					<div id="wplazare_' . wplazare_users::getCurrentPageCode()
					. '_main_infos_form" >' . $the_form
					. '

					</div>

				</div>

			</form>

			<script type="text/javascript" >

				wplazare(document).ready(function(){

					jQuery("#' . wplazare_users::getEditionSlug()
					. '_form").validate();

					wplazareMainInterface("' . wplazare_users::getEditionSlug()
					. '", "'
					. __(
							'&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es',
							'wpshop') . '", "'
					. admin_url(
							'admin.php?page='
									. wplazare_users::getEditionSlug())
					. '");

				});

			</script>';

			$the_view .= '<div class="clear">'
					. wplazare_users::generateLinksByRole($editedItem['id'],
							$editedItem['role'], ' | ') . '</div>';

			$the_view .= wplazare_users::buildHasAppartView($editedItem['id'],
					$editedItem['role']);

			$the_view .= wplazare_users::buildHasHistoriqueView(
					$editedItem['id'], $editedItem['role']);

			$the_view .= wplazare_users::buildHasDonView($editedItem['email'],
					$editedItem['role']);

		} else {

			$prenom = array('name' => 'prenom', 'type' => 'text',
					'option' => "class='required'");

			$nom = array('name' => 'nom', 'type' => 'text',
					'option' => "class='required'");

			$email = array('name' => 'email', 'type' => 'text',
					'option' => "class='email'");

			$adresse = array('name' => 'adresse', 'type' => 'text');

			$code_postal = array('name' => 'code_postal', 'type' => 'text');

			$ville = array('name' => 'ville', 'type' => 'text');

			$tel = array('name' => 'tel', 'type' => 'text');

			$date_de_naissance = array('name' => 'date_de_naissance',
					'type' => 'text', 'option' => "class='jquery_date_picker'");

			$role = array('name' => 'role', 'type' => 'select',
					'valueToPut' => 'index',
					'possible_value' => array(
							WPLAZARE_ROLE_POSTULANT => __(
									WPLAZARE_ROLE_POSTULANT, 'wplazare'),
							WPLAZARE_ROLE_BENEVOLE => __(
									WPLAZARE_ROLE_BENEVOLE, 'wplazare'),
							WPLAZARE_ROLE_PARTENAIRE_SOCIAL => __(
									WPLAZARE_ROLE_PARTENAIRE_SOCIAL,
									'wplazare'),
							WPLAZARE_ROLE_PERSONNE_ACCUEILLIE => __(
									WPLAZARE_ROLE_PERSONNE_ACCUEILLIE,
									'wplazare')));

			$fields = array($prenom, $nom, $email, $adresse, $code_postal,
					$ville, $tel, $date_de_naissance, $role);

			$the_form = '';

			foreach ($fields as $field) {

				$the_input = wplazare_form::check_input_type($field,
						wplazare_users::getCurrentPageCode());

				$the_form .= '

				<div class="clear" >

					<div class="wplazare_form_label wplazare_'
						. wplazare_orders::getCurrentPageCode() . '_'
						. '_label alignleft" >

						' . __($field['name'], 'wplazare')
						. '

					</div>

					<div class="wplazare_form_input wplazare_'
						. wplazare_orders::getCurrentPageCode() . '_'
						. '_input alignleft" >

						' . $the_input . '

					</div>

				</div>';

			}

			$formAddAction = admin_url(
					'admin.php?page=' . wplazare_users::getEditionSlug()
							. '&amp;action=add');

			$the_view .= ' 

			<form name="' . wplazare_users::getEditionSlug() . '_form" id="'
					. wplazare_users::getEditionSlug()
					. '_form" method="post" action="' . $formAddAction
					. '" enctype="multipart/form-data" >

				'
					. wplazare_form::form_input(
							wplazare_users::getEditionSlug() . '_action',
							wplazare_users::getEditionSlug() . '_action',
							(isset($_REQUEST['action'])
									&& ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer(
											$_REQUEST['action']) : 'add'),
							'hidden') . '

				'
					. wplazare_form::form_input(
							wplazare_users::getEditionSlug()
									. '_form_has_modification',
							wplazare_users::getEditionSlug()
									. '_form_has_modification', 'no', 'hidden')
					. '

				<div id="wplazareFormManagementContainer" >

					<div id="wplazare_' . wplazare_users::getCurrentPageCode()
					. '_main_infos_form" >' . $the_form
					. '

					</div>

				</div>

			</form>

			<script type="text/javascript" >

				wplazare(document).ready(function(){

					jQuery("#' . wplazare_users::getEditionSlug()
					. '_form").validate();

					wplazareMainInterface("' . wplazare_users::getEditionSlug()
					. '", "'
					. __(
							'&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es',
							'wpshop') . '", "'
					. admin_url(
							'admin.php?page='
									. wplazare_users::getEditionSlug())
					. '");

				});

			</script>';

		}

		return $the_view;

	}

	/**
	 *	Return the different button to save the item currently being added or edited
	 *
	 *	@return string $currentPageButton The html output code with the different button to add to the interface
	 */

	function getPageFormButton() {

		$action = isset($_REQUEST['action']) ? wplazare_tools::varSanitizer(
						$_REQUEST['action']) : 'add';

		$currentPageButton = '';

		if ($action == 'export') {

			return "";

		}

		if ($action == 'add') {

			if (current_user_can('wplazare_add_user')) {

				$currentPageButton .= '<input type="button" class="button-primary" id="add" name="add" value="Ajouter" />';

			}

		} elseif (current_user_can('wplazare_edit_user')) {

			$currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="Enregistrer" />';

		}

		if (current_user_can('wplazare_edit_user') && ($action != 'add')) {

			$currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="Supprimer" />';

		}

		$currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="'
				. admin_url(
						'admin.php?page=' . wplazare_users::getListingSlug())
				. '" class="button add-new-h2" >Retour</a></h2>';

		return $currentPageButton;

	}

	/**
	 *	Get the existing element list into database
	 *
	 *	@param integer $elementId optionnal The element identifier we want to get. If not specify the entire list will be returned
	 *	@param string $elementMail optionnal The mail of element to get into database. If not specify the entire list will be returned
	 **	@param string $filter optionnal Filter element...
	 *
	 *	@return object un tableau d'element utilisateur si $elementId n'est pas d�fini. Un element utilisateur sinon
	 */

	function getElement($elementId = '', $role = '') {

		global $wpdb;

		if ($elementId != '') {

			if ($role != WPLAZARE_ROLE_DONATEUR) {

				$user = get_userdata($elementId);

				if (!$user)
					return;

				$element = array();

				$element['id'] = $user->ID;

				$element['email'] = $user->user_email;

				$element['prenom'] = ucfirst(
						get_user_meta($user->ID, 'first_name', true));

				$element['nom'] = ucfirst(
						get_user_meta($user->ID, 'last_name', true));

				$element['adresse_complete'] = get_cimyFieldValue($user->ID,
						'ADRESSE') . ' '
						. get_cimyFieldValue($user->ID, 'CODE_POSTAL') . ' '
						. get_cimyFieldValue($user->ID, 'VILLE');

				$element['adresse'] = get_cimyFieldValue($user->ID, 'ADRESSE');

				$element['code_postal'] = get_cimyFieldValue($user->ID,
						'CODE_POSTAL');

				$element['ville'] = get_cimyFieldValue($user->ID, 'VILLE');

				$element['tel'] = wplazare_tools::addSpaceOnPhone(
						get_cimyFieldValue($user->ID, 'TEL'));

				$element['date_de_naissance'] = get_cimyFieldValue($user->ID,
						'DATE_DE_NAISSANCE');

				$element['role'] = wplazare_tools::getRole($user->ID);

				$element['commentaires'] = get_cimyFieldValue($user->ID,
						'COMMENTAIRES');

				return $element;

			} else {

				$query = "SELECT DISTINCT O.user_email,O.user_firstname,O.user_lastname,O.user_phone,O.user_adress,O.user_birthday

				FROM " . wplazare_users::getOrdersDbTable()
						. " AS O WHERE O.user_firstname NOT LIKE '' AND O.user_lastname NOT LIKE ''";

				$query .= " AND O.user_email='$elementId'";

				$result = $wpdb->prepare($query);

				$results = $wpdb->get_results($result);

				foreach ($results as $result) {

					$element = array();

					$element['id'] = '';

					$element['email'] = $result->user_email;

					$element['prenom'] = ucfirst($result->user_firstname);

					$element['nom'] = ucfirst($result->user_lastname);

					$element['adresse_complete'] = $result->user_adress;

					$element['adresse'] = '';

					$element['code_postal'] = '';

					$element['ville'] = '';

					$element['tel'] = wplazare_tools::addSpaceOnPhone(
							$result->user_phone);

					$element['date_de_naissance'] = $result->user_birthday;

					$element['role'] = WPLAZARE_ROLE_DONATEUR;

					$element['commentaires'] = '';

					$elements[] = $element;

				}

				$elements = wplazare_tools::sortByOneKey($elements, 'nom');

				return $elements[0];

			}

		}

		$elements = array();

		if ($role != '') {

			if ($role != WPLAZARE_ROLE_DONATEUR) {

				if ($role == "attente") {

					$blogusers = get_users("role=" . WPLAZARE_ROLE_BENEVOLE);

				} else {

					$blogusers = get_users('role=' . $role);

				}

				foreach ($blogusers as $user) {

					$element = array();

					$element['id'] = $user->ID;

					$element['email'] = $user->user_email;

					$element['prenom'] = ucfirst(
							get_user_meta($user->ID, 'last_name', true));

					$element['nom'] = ucfirst(
							get_user_meta($user->ID, 'first_name', true));

					$element['adresse_complete'] = get_cimyFieldValue(
							$user->ID, 'ADRESSE') . ' '
							. get_cimyFieldValue($user->ID, 'CODE_POSTAL')
							. ' ' . get_cimyFieldValue($user->ID, 'VILLE');

					$element['adresse'] = get_cimyFieldValue($user->ID,
							'ADRESSE');

					$element['code_postal'] = get_cimyFieldValue($user->ID,
							'CODE_POSTAL');

					$element['ville'] = get_cimyFieldValue($user->ID, 'VILLE');

					$element['tel'] = wplazare_tools::addSpaceOnPhone(
							get_cimyFieldValue($user->ID, 'TEL'));

					$element['date_de_naissance'] = get_cimyFieldValue(
							$user->ID, 'DATE_DE_NAISSANCE');

					$element['role'] = wplazare_tools::getRole($user->ID);

					$element['commentaires'] = get_cimyFieldValue($user->ID,
							'COMMENTAIRES');

					if ($role == "attente") {

						if (wplazare_tools::getLocation($user->ID) == '') {

							$elements[] = $element;

						}

					} else {

						$elements[] = $element;

					}

				}

			} else {

				$query = "SELECT DISTINCT O.user_email,O.user_firstname,O.user_lastname,O.user_phone,O.user_adress,O.user_birthday

				FROM " . wplazare_users::getOrdersDbTable()
						. " AS O WHERE O.user_firstname NOT LIKE '' AND O.user_lastname NOT LIKE ''";

				$result = $wpdb->prepare($query);

				$results = $wpdb->get_results($result);

				foreach ($results as $result) {

					$element = array();

					$element['id'] = '';

					$element['email'] = $result->user_email;

					$element['prenom'] = ucfirst($result->user_firstname);

					$element['nom'] = ucfirst($result->user_lastname);

					$element['adresse_complete'] = $result->user_adress;

					$element['adresse'] = '';

					$element['code_postal'] = '';

					$element['ville'] = '';

					$element['tel'] = wplazare_tools::addSpaceOnPhone(
							$result->user_phone);

					$element['date_de_naissance'] = $result->user_birthday;

					$element['role'] = WPLAZARE_ROLE_DONATEUR;

					$element['commentaires'] = '';

					$elements[] = $element;

				}

			}

		} else {

			$blogusers = get_users('');

			foreach ($blogusers as $user) {

				$element = array();

				$element['id'] = $user->ID;

				$element['email'] = $user->user_email;

				$element['prenom'] = ucfirst(
						get_user_meta($user->ID, 'last_name', true));

				$element['nom'] = ucfirst(
						get_user_meta($user->ID, 'first_name', true));

				$element['adresse_complete'] = get_cimyFieldValue($user->ID,
						'ADRESSE') . ' '
						. get_cimyFieldValue($user->ID, 'CODE_POSTAL') . ' '
						. get_cimyFieldValue($user->ID, 'VILLE');

				$element['adresse'] = get_cimyFieldValue($user->ID, 'ADRESSE');

				$element['code_postal'] = get_cimyFieldValue($user->ID,
						'CODE_POSTAL');

				$element['ville'] = get_cimyFieldValue($user->ID, 'VILLE');

				$element['tel'] = wplazare_tools::addSpaceOnPhone(
						get_cimyFieldValue($user->ID, 'TEL'));

				$element['date_de_naissance'] = get_cimyFieldValue($user->ID,
						'DATE_DE_NAISSANCE');

				$element['role'] = wplazare_tools::getRole($user->ID);

				$element['commentaires'] = get_cimyFieldValue($user->ID,
						'COMMENTAIRES');

				$elements[] = $element;

			}
		}

		$elements = wplazare_tools::sortByOneKey($elements, 'nom');

		if ($elementId != '' && (count($elements) > 0))
			return $elements[0];

		return $elements;

	}

	/**
	 * 
	 * export() permet de generer le pdf correspondant a l'annuaire de lazare
	 */

	function export($itemToEdit = '') {

		$template_name = "annuaire";

		$pdfator = new wplazare_pdfator();
		$excelator = new wplazare_excelator();

		$newText = wplazare_users::buildAnnuaireView();
		$balises_replace = array(
				array("balise" => "{A_APPARTEMENT}", "new_text" => $newText));

		$file_path = $pdfator->getPdf($template_name, $balises_replace);

		if ($file_path != '') {

			$return = '<h3>Annuaire</h3>';

			$return .= '<div>'
					. 'L\'annuaire a bien &eacute;t&eacute; g&eacute;n&eacute;r&eacute;:<br/>'
					. '<a class="pdf" href="'
					. plugins_url(
							'/wplazare/includes/librairies/html2pdf/output/'
									. basename($file_path)) . '">Annuaire</a>';

			'</div>';

		} else {

			$tableId = 'Acteur_list';

			$tableSummary = 'Liste des acteurs de Lazare/';

			$tableTitles = array();

			$subRowActions = '';

			$rowActions = '

	<div id="rowAction" class="wplazareRowAction" >' . $subRowActions
					. '

	</div>';

			$tableRowsId[] = wplazare_users::getDbTable() . '_noResult';

			unset($tableRowValue);

			$tableRowValue[] = array(
					'class' => wplazare_users::getCurrentPageCode()
							. '_name_cell',
					'value' => 'Le fichier n\'a pas pu etre g&eacute;n&eacute;r&eacute;. Veuillez contacter votre administrateur.'
							. $rowActions);

			$tableRows[] = $tableRowValue;

			$tableClasses = array();

			$return = wplazare_display::getTable($tableId, $tableTitles,
					$tableRows, $tableClasses, $tableRowsId, $tableSummary,
					true);

		}

		$results = wplazare_tools::getLocatairesEtAppartsOccupes();

		unlink(WPLAZARE_EXCEL_PLUGIN_DIR . '/annuaire_lazare.xlsx');

		$excelator->getAnnuaire($results);

		$return .= '<h3>Annuaire Xls</h3>';

		$return .= '<div>'
				. 'L\'annuaire a bien &eacute;t&eacute; g&eacute;n&eacute;r&eacute;:<br/>'
				. '<a class="excel" href="'
				. plugins_url(
						'/wplazare/includes/librairies/excel/annuaire_lazare.xlsx')
				. '">Annuaire Excel</a>';

		'</div>';

		return $return;

	}

	/**
	
	 *	Retourne des liens vers les pages sp�ciales des utilisateurs en fonction de leur r�le
	 *
	 *	@param integer $id Id de l'utilisateur
	 *	@param string $role Role de l'utilisateur
	 *	@param string $separateur Le s�parateur entre les liens
	 *
	 *	@return string $result les liens
	 */

	function generateLinksByRole($id, $role, $separateur) {

		$result = '';

		switch ($role) {

		case WPLAZARE_ROLE_BENEVOLE:
			$current_location = wplazare_tools::getLocation($id);

			$result .= '<a href="'
					. admin_url(
							'admin.php?page='
									. WPLAZARE_URL_SLUG_HISTORIQUE_EDITION
									. '&utilisateur=' . $id, 'http')
					. '">Historique</a>';

			if ($current_location && $current_location->appartement != '')
				$result .= $separateur . '<a href="'
						. admin_url(
								'admin.php?page='
										. WPLAZARE_URL_SLUG_APPARTS_EDITION
										. '&action=view&id=' . $current_location->appartement, 'http')
						. '">Appart</a>';

			break;

		case WPLAZARE_ROLE_PERSONNE_ACCUEILLIE:
            $current_location = wplazare_tools::getLocation($id);

			$result .= '<a href="'
					. admin_url(
							'admin.php?page='
									. WPLAZARE_URL_SLUG_HISTORIQUE_EDITION
									. '&utilisateur=' . $id, 'http')
					. '">Historique</a>';

			if ($current_location && $current_location->appartement != '')
				$result .= $separateur . '<a href="'
						. admin_url(
								'admin.php?page='
										. WPLAZARE_URL_SLUG_APPARTS_EDITION
										. '&action=view&id=' . $current_location->appartement, 'http')
						. '">Appart</a>';

			break;

		case WPLAZARE_ROLE_POSTULANT:
			$result .= '<a href="'
					. admin_url(
							'admin.php?page=' . WPLAZARE_URL_SLUG_USERS_EDITION
									. '&validate=' . $id, 'http') . '">'
					. wplazare_users::buildSurveyLink($id) . '</a>';

			$result .= $separateur . '<a href="'
					. admin_url(
							'admin.php?page=' . WPLAZARE_URL_SLUG_USERS_EDITION
									. '&validatePostulant=' . $id, 'http')
					. '">Valider B&eacute;n&eacute;vole</a>';

			break;

		/*case WPLAZARE_ROLE_DONATEUR:
		
		        $result .= '<a href="'.admin_url( 'admin.php?page='.WPLAZARE_URL_SLUG_STATS_LISTING.'&action=view&id=' . $id, 'http' ).'">Statistiques</a>';
		
		break;*/

		}

		if ($role != WPLAZARE_ROLE_DONATEUR) {

			$appart = wplazare_tools::isResponsable($id);

			if ($appart != '')
				$result .= $separateur . '<a href="'
						. admin_url(
								'admin.php?page='
										. WPLAZARE_URL_SLUG_APPARTS_EDITION
										. '&action=view&id=' . $appart, 'http')
						. '">Responsable</a>';

		}

		return $result;

	}

	/**
	 * cmp()
	 * Fonction utilis�e dans le tri de getElement
	 * 
	 */

	function cmp($a, $b) {

		if ($a['nom'] == $b['nom']) {

			return 0;

		}

		return ($a['nom'] < $b['nom']) ? -1 : 1;

	}

	/**
	 * 
	 * Construit la vue appart si l'utilisateur id a une location en cours.
	 * @param int $id id de l'utilisateur
	 * @param string $role le role de l'utilisateur
	 */

	function buildHasAppartView($user_id, $role) {

		$view = '';

		if ($role != WPLAZARE_ROLE_DONATEUR) {

            $current_location = wplazare_tools::getLocation($user_id);
			if ($current_location != '') {
                $appart_id = $current_location->appartement;
                $current_appartement = wplazare_apparts::getElement($appart_id);

                $editLocationAction = admin_url(
                    'admin.php?page='
                        . wplazare_locations::getEditionSlug()
                        . '&amp;action=edit&amp;id=' . $current_location->id);

				$view = '<h3>Appartement</h3><p>';

				$view .= wplazare_tools::getFirstName($user_id) . ' vit au '
                        . '<a href="'.$editLocationAction.'">'
						. stripslashes(wplazare_apparts::getAdresseComplete($current_appartement))
						. '</a> depuis le '
						. date("d/m/Y",	strtotime($current_location->date_debut))
						. "<br/>Locataires: ";

				$colocataires_id = wplazare_tools::getLocatairesByAppart($appart_id);

				$colocataires_names = '';

				foreach ($colocataires_id as $colocataire_id) {

					if ($colocataires_names != '')
						$colocataires_names .= ', ';

					$colocataires_names .= wplazare_tools::getUserLink(
							$colocataire_id->id,
							wplazare_tools::getFirstName($colocataire_id->id));

				}

				$view .= $colocataires_names;

				$responsable = wplazare_tools::getFirstName($current_appartement->responsable);

				if ($responsable != '') {

					$view .= '<br/>Responsable de l\'appartement: '
							. wplazare_tools::getUserLink($current_appartement->responsable,$responsable);

				}

			}

			$view .= '</p>';

		}

		return $view;

	}

	/**
	 * 
	 * Construit la vue historique si l'utilisateur id a une histoire.
	 * @param int $id id de l'utilisateur
	 */

	function buildHasHistoriqueView($user_id, $role) {

		$view = '';

		if ($role != WPLAZARE_ROLE_DONATEUR) {

            $current_location = wplazare_tools::getLocation($user_id);
			if ($current_location != '') {

				$view = '<h3>Historique</h3><p>';

				$args = array('category' => WPLAZARE_CATEGORY_HISTORIQUE,
						'orderby' => 'post_date', 'order' => 'DESC',
						'author' => $user_id);

				foreach (get_posts($args) as $post) {

					$attachment = wplazare_historique::hasAttachment($post->ID) ? 'OUI'
							: 'NON';

					$lien = admin_url(
							'admin.php?page='
									. wplazare_historique::getEditionSlug()
									. '&amp;action=edit&amp;id=' . $post->ID
									. '&user_id=' . $user_id);

					$newDate = date("d M Y", strtotime($post->post_date));

					$view .= "<a href='$lien'>" . $post->post_title
							. '</a> le ' . $newDate . ', Pi&egravece jointe: '
							. $attachment . '</p><p>';

				}

				$view .= '</p>';

			}

		}

		return $view;

	}

	/**
	 * 
	 * Construit la vue don si l'utilisateur id en a fait.
	 * @param int $id id de l'utilisateur
	 */

	function buildHasDonView($user_email, $role) {

		$view = '';

		$dons = wplazare_orders::getDons($user_email, 'closed');

		if (!empty($dons)) {

			$view = '<h3>Dons</h3><p>';

			foreach ($dons as $don) {

				$lien = admin_url(
						'admin.php?page=' . wplazare_orders::getEditionSlug()
								. '&amp;action=view&amp;id=' . $don->id);

				$view .= '<a href="' . $lien . '" >Don '
						. __($don->order_status, 'wplazare') . ' de '
						. ($don->order_amount / 100) . ' &euro; le '
						. $don->last_update_date . '.</a><br/>';

			}

			$view .= '</p>';

		}

		return $view;

	}

	/**
	 * 
	 * buildAnnuaireView() retourne la vue g�n�r�e pour construire le fichier annuaire.
	 */

	function buildAnnuaireView() {

		$current_appart_id = '';

		$current_appart_adresse_complete = '';

		$retour = '';

		$results = wplazare_tools::getLocatairesEtAppartsOccupes();

		foreach ($results as $result) {

			$appart_id_tmp = $result->appart_id;

			if ($appart_id_tmp != $current_appart_id) {

				$current_appart_id = $appart_id_tmp;

				if ($retour != '')
					$retour .= '</table>';

				$retour .= '<h3>Appartement '
						. stripslashes(
								wplazare_apparts::getAdresseComplete(
										$current_appart_id)) . '</h3>';

				$retour .= '<table>';

				$retour .= wplazare_users::buildTh();

				$user_id = $result->locataire_id;

				$retour .= '<tr>';

				$retour .= '<td>'
						. ucfirst(get_user_meta($user_id, 'first_name', true))
						. '</td>';

				$retour .= '<td>'
						. ucfirst(get_user_meta($user_id, 'last_name', true))
						. '</td>';

				$retour .= '<td>'
						. wplazare_tools::addSpaceOnPhone(
								get_cimyFieldValue($user_id, 'TEL')) . '</td>';

				$retour .= '<td>' . get_user_by('id', $user_id)->user_email
						. '</td>';

				$retour .= '<td>'
						. get_cimyFieldValue($user_id, 'DATE_DE_NAISSANCE')
						. '</td>';

				$retour .= '</tr>';

			} else {

				$user_id = $result->locataire_id;

				$retour .= '<tr>';

				$retour .= '<td>'
						. ucfirst(get_user_meta($user_id, 'first_name', true))
						. '</td>';

				$retour .= '<td>'
						. ucfirst(get_user_meta($user_id, 'last_name', true))
						. '</td>';

				$retour .= '<td>'
						. wplazare_tools::addSpaceOnPhone(
								get_cimyFieldValue($user_id, 'TEL')) . '</td>';

				$retour .= '<td>' . get_user_by('id', $user_id)->user_email
						. '</td>';

				$retour .= '<td>'
						. get_cimyFieldValue($user_id, 'DATE_DE_NAISSANCE')
						. '</td>';

				$retour .= '</tr>';

			}

		}

		$retour .= '</table>';

		return $retour;

	}

	function buildTh() {

		return '<tr>' . '<th style="width: 150px">' . __('prenom', 'wplazare')
				. '</th>' . '<th style="width: 150px">' . __('nom', 'wplazare')
				. '</th>' . '<th style="width: 150px">' . __('tel', 'wplazare')
				. '</th>' . '<th style="width: 150px">'
				. __('email', 'wplazare') . '</th>'
				. '<th style="width: 150px">'
				. __('date_de_naissance', 'wplazare') . '</th>' . '</tr>';

	}

	function validate($user_id) {

		$user = get_userdata($user_id);

		global $wpdb;

		$query = $wpdb
				->prepare(
						"SELECT * FROM " . LIMESURVEY_DB_PREFIX . "tokens_"
								. LIMESURVEY_SURVEYID_LAZARE
								. " AS O WHERE email LIKE '$user->user_email'");

		$element = $wpdb->get_row($query);

		if ($element == null) {

			if ($retour = wplazare_users::subscribeSurveyUser($user->user_email)) {

				wplazare_users::sendSurveyInvitation(LIMESURVEY_SURVEYID_LAZARE);

				return '<p>Invitation envoy&eacute;e.</p>';

			} else
				return 'error:' . $retour;

		} else {

			if ($element->completed == 'N')
				return 'Envoyer un Rappel n\'est pas encore disponible. Veuillez vous connecter directement &agrave; LimeSurvey: <a href="http://www.maisonlazare.com/limesurvey/admin">Page Administration Limesurvey</a>.';

			else
				return wplazare_users::displaySurveyResult($element->token);

		}

	}

	function randomChars($length, $pattern = "23456789abcdefghijkmnpqrstuvwxyz") {

		$patternlength = strlen($pattern) - 1;

		for ($i = 0; $i < $length; $i++) {

			if (isset($key))
				$key .= $pattern{rand(0, $patternlength)};

			else
				$key = $pattern{rand(0, $patternlength)};

		}

		return $key;

	}

	function subscribeSurveyUser($user_email) {

		global $wpdb;

		$from = LIMESURVEY_DB_PREFIX . 'tokens_' . LIMESURVEY_SURVEYID_LAZARE;

		$newtoken = wplazare_users::randomChars(15);

		$user = get_user_by('email', $user_email);

		$query = $wpdb
				->prepare(
						"INSERT INTO  $from (`firstname` ,`lastname` ,`email` ,`emailstatus`"
								. ",`token` ,`language` ,`sent`,`remindersent`,`completed`,`usesleft`)

VALUES ('" . str_replace(array("'", ' '), '',wplazare_tools::varSanitizer(ucfirst($user->first_name)))
								. "' ,  '"
								.
                            str_replace(array("'", ' '), '',wplazare_tools::varSanitizer(
                                ucfirst($user->last_name)))
								. "', '$user_email' ,  'OK',  '$newtoken',  'fr','N','N','N',1)");

		if ($retour = $wpdb->query($query)) {

			return $retour;

		} else {

			echo $query;

			return false;

		}

	}

	function sendSurveyInvitation($survey_id) {

		require_once 'json-rpc/jsonRPCClient.php';

		$survey_id = $survey_id;

		$myJSONRPCClient = new jsonRPCClient(
				LS_BASEURL . '/admin/remotecontrol');

		$sessionKey = $myJSONRPCClient->get_session_key(LS_USER, LS_PASSWORD);

		$attachments = $myJSONRPCClient
				->invite_participants($sessionKey, $survey_id);

		$myJSONRPCClient->release_session_key($sessionKey);

	}

	function buildSurveyLink($user_id) {

		$user = get_userdata($user_id);

		global $wpdb;

		$query = $wpdb
				->prepare(
						"SELECT * FROM " . LIMESURVEY_DB_PREFIX . "tokens_"
								. LIMESURVEY_SURVEYID_LAZARE
								. " AS O WHERE email LIKE '$user->user_email'");

		$element = $wpdb->get_row($query);

		if ($element == null)
			return 'Envoyer Entretien';

		else {

			if ($element->completed == 'N')
				return 'Envoyer Rappel';

			else
				return 'Afficher Entretien';

		}

	}

	function displaySurveyResult($token) {

		require_once 'json-rpc/jsonRPCClient.php';

		// the survey to process
		$survey_id = LIMESURVEY_SURVEYID_LAZARE;

		// instanciate a new client 

        $myJSONRPCClient = new jsonRPCClient(
				LS_BASEURL . 'index.php/admin/remotecontrol');
		// receive session key
		$sessionKey = $myJSONRPCClient->get_session_key(LS_USER, LS_PASSWORD);
		$sFilter = "token LIKE '$token'";


		$attachments = $myJSONRPCClient
				->export_responses($sessionKey, $survey_id, 'pdf', null, 'all',
						'full', 'long', null, null, null, $sFilter);

		rename($attachments, $attachments . '.pdf');

		$basename = basename($attachments . '.pdf');

		$return = '<h3>Questionnaire</h3>';

		$return .= '<div>'
				. 'Le questionnaire a bien &eacute;t&eacute; g&eacute;n&eacute;r&eacute;:<br/>'
				. '<a href="' . home_url('/limesurvey/tmp/') . $basename
				. '"><img src="' . get_theme_root_uri()
				. '/lazare/images/download.jpg" alt="T&eacute;l&eacutecharger" /></a>'
				. '</div>';

		// release the session key

		$myJSONRPCClient->release_session_key($sessionKey);

		return $return;

	}

	function validatePostulant($user_id) {

		/* GET TOKEN */

		$user = get_userdata($user_id);

		global $wpdb;

		$query = $wpdb
				->prepare(
						"SELECT * FROM " . LIMESURVEY_DB_PREFIX . "tokens_"
								. LIMESURVEY_SURVEYID_LAZARE
								. " AS O WHERE email LIKE '$user->user_email'");

		$element = $wpdb->get_row($query);

		$token = $element->token;

		/* CHANGE ROLE */

		$userdata = array();

		$userdata['ID'] = $user_id;

		$userdata['role'] = WPLAZARE_ROLE_BENEVOLE;

		wp_update_user($userdata);

		/* CREATE POST */

		$my_post = array();

		$my_post['post_author'] = $user_id;

		$my_post['post_title'] = 'Questionnaire Entrée';

		$my_post['post_content'] = '';

		$my_post['post_category'] = array(WPLAZARE_CATEGORY_HISTORIQUE);

		$my_post['post_status'] = 'publish';

		$post_id = wp_insert_post($my_post);

		/* GET SURVEY RESULT AS PDF */

		require_once 'json-rpc/jsonRPCClient.php';

		$survey_id = LIMESURVEY_SURVEYID_LAZARE;

		$myJSONRPCClient = new jsonRPCClient(
				LS_BASEURL . '/admin/remotecontrol');

		$sessionKey = $myJSONRPCClient->get_session_key(LS_USER, LS_PASSWORD);

		$sFilter = "token LIKE '$token'";

		$attachments = $myJSONRPCClient
				->export_responses($sessionKey, $survey_id, 'pdf', null, 'all',
						'full', 'long', null, null, null, $sFilter);

		rename($attachments, $attachments . '.pdf');

		$basename = basename($attachments . '.pdf');

		$filename = home_url('') . '/../limesurvey/tmp/' . $basename;

		$upload = wp_upload_bits($basename, null, file_get_contents($filename));

		$myJSONRPCClient->release_session_key($sessionKey);

		/* ADD PDF TO THE LAST POST*/

		if ($post_id != 0 && $upload['error'] != 'false') {

			$wp_filetype = wp_check_filetype(basename($upload['url']), null);

			$wp_upload_dir = wp_upload_dir();

			$attachment = array('guid' => $upload['url'],
					'post_mime_type' => $wp_filetype['type'],
					'post_title' => preg_replace('/\.[^.]+$/', '',
							basename($upload['url'])), 'post_content' => '',
					'post_status' => 'inherit');

			$attach_id = wp_insert_attachment($attachment, $upload['url'],
					$post_id);

			require_once(ABSPATH . 'wp-admin/includes/image.php');

			$attach_data = wp_generate_attachment_metadata($attach_id,
					$upload['url']);

			wp_update_attachment_metadata($attach_id, $attach_data);

		}

		return wplazare_tools::getUserName($user_id) . ' est devenu '
				. __(WPLAZARE_ROLE_BENEVOLE) . '.';

	}

	function insertUser() {

		$post_data = $_REQUEST[wplazare_users::getCurrentPageCode()];

		$first_name = str_replace(array("'", ' '), '', $post_data['prenom']);

		$last_name = str_replace(array("'", ' '), '', $post_data['nom']);

		if ($first_name != "" && $last_name != "") {
			$user_name = strtolower($first_name[0] . $last_name);

			$user_id = username_exists($user_name);
		}
		if ($first_name != "" && $last_name != "" && !$user_id) {

			$random_password = wp_generate_password($length = 12,
					$include_standard_special_chars = false);

			$user_id = wp_create_user($user_name, $random_password);

			if ($user_id != '') {

				$userdata = array('ID' => $user_id,
						'first_name' => $first_name, 'last_name' => $last_name,
						'user_email' => $post_data['email'],
						'role' => $post_data['role']);

				wp_update_user($userdata);

				set_cimyFieldValue($user_id, 'ADRESSE', $post_data['adresse']);

				set_cimyFieldValue($user_id, 'CODE_POSTAL',
						$post_data['code_postal']);

				set_cimyFieldValue($user_id, 'VILLE', $post_data['ville']);

				set_cimyFieldValue($user_id, 'DATE_DE_NAISSANCE',
						$post_data['date_de_naissance']);

				set_cimyFieldValue($user_id, 'TEL', $post_data['tel']);

			}

		}

		return 'Utilisateur cr&eacute;&eacute; avec succ&egrave;s.';

	}

	function updateUser() {

		$post_data = $_REQUEST[wplazare_users::getCurrentPageCode()];

		$user_id = $post_data['user_id'];

		if ($user_id != '') {

			$userdata = array('ID' => $user_id,
					'first_name' => $post_data['prenom'],
					'last_name' => $post_data['nom'],
					'user_email' => $post_data['email'],
					'role' => $post_data['role']);

			wp_update_user($userdata);

			set_cimyFieldValue($user_id, 'ADRESSE', $post_data['adresse']);

			set_cimyFieldValue($user_id, 'CODE_POSTAL',
					$post_data['code_postal']);

			set_cimyFieldValue($user_id, 'VILLE', $post_data['ville']);

			set_cimyFieldValue($user_id, 'DATE_DE_NAISSANCE',
					$post_data['date_de_naissance']);

			set_cimyFieldValue($user_id, 'TEL', $post_data['tel']);

		}

		return 'Utilisateur mis &agrave; jour avec succ&egrave;s.';

	}

	function deleteUser() {

		$user_id = wplazare_tools::varSanitizer(intval($_REQUEST['user_id']));

		if ((wplazare_tools::getRole($user_id) != 'administrator')
				&& current_user_can('wplazare_edit_user')) {

			wp_delete_user($user_id);

			return 'Utilisateur supprim&eacute; avec succ&egrave;s.';

		}

		return 'Utilisateur non supprim&eacute;. Vous ne disposez pas des droits suffisants.';

	}

	function getEmails($locataire_ids) {
		$result = array();
		foreach ($locataire_ids as $locataire_id) {
			$user = get_userdata($locataire_id->id);
			if (!in_array($user->user_email, $result))
				$result[] = $user->user_email;
		}
		return $result;
	}

	function buildMailTo($locataire_ids) {
		$emails = wplazare_users::getEmails($locataire_ids);
		$result = "";
		foreach ($emails as $email) {
			if ($email != '') {
				$result .= $email . ';';
			}
		}
		if ($result != "") {
			return "<a href='mailto:$result'>Email</a>";
		}
		return '';
	}

	function newAccompagnateur() {
		$the_view = "";
		if (!isset($_POST['identifier'])) {
			$prenom = array('name' => 'prenom', 'type' => 'text',
					'option' => "class='required'", 'value' => "");

			$nom = array('name' => 'nom', 'type' => 'text',
					'option' => "class='required'", 'value' => "");

			$email = array('name' => 'email', 'type' => 'text',
					'option' => "class='email'", 'value' => "");

			$adresse = array('name' => 'adresse', 'type' => 'text',
					'value' => "");

			$code_postal = array('name' => 'code_postal', 'type' => 'text',
					'value' => "");

			$ville = array('name' => 'ville', 'type' => 'text', 'value' => "");

			$tel = array('name' => 'tel', 'type' => 'text', 'value' => "");

			$date_de_naissance = array('name' => 'date_de_naissance',
					'type' => 'text', 'option' => "class='jquery_date_picker'",
					'value' => "");

			$commentaires = array('name' => 'commentaires',
					'type' => 'textarea', 'value' => "");

			$fields = array($prenom, $nom, $email, $adresse, $code_postal,
					$ville, $tel, $date_de_naissance, $commentaires);

			$the_form = '';

			foreach ($fields as $field) {
				$the_input = wplazare_form::check_input_type($field,
						wplazare_users::getCurrentPageCode());
				$the_form .= '
				<div class="clear" >
					<div class="wplazare_form_label '
						. wplazare_users::getCurrentPageCode() . '_'
						. $field['name'] . '_label alignleft" >
						' . __($field['name'], 'wplazare')
						. '
					</div>
					<div class="wplazare_form_input '
						. wplazare_users::getCurrentPageCode() . '_'
						. $field['name'] . '_input alignleft" >
						' . $the_input . '
					</div>
				</div>';
			}

			$formAddAction = "";

			$the_view .= ' 
			<form name="' . wplazare_users::getEditionSlug() . '_form" id="'
					. wplazare_users::getEditionSlug()
					. '_form" method="post" action="' . $formAddAction
					. '" enctype="multipart/form-data" >
				<div id="wplazareFormManagementContainer" >
					<div id="wplazare_' . wplazare_users::getCurrentPageCode()
					. '_main_infos_form" class="orienter_personne" >'
					. $the_form
					. '
					</div>
				</div>
				<div class="clear" >
					<input type="submit" value="Envoyer" />
				</div>
				<input type="hidden" value="ok" name="identifier" />
				<input type="hidden" value="' . WPLAZARE_ROLE_ORIENTER
					. '" name="' . wplazare_users::getCurrentPageCode()
					. '[role]" />
			</form>
			<script type="text/javascript">
			wplazare("#' . wplazare_users::getEditionSlug()
					. '_form").validate({
			 	submitHandler: function(form) {
			 		wplazare(form).submit();
				}
			});
			</script>
			';
		} else {
			wplazare_users::insertUser();
			$the_view = "Merci pour votre inscription, nous prendrons rapidement contact avec vous.";
		}
		return $the_view;
	}
}
