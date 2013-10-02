<?php
/**
 * Template manager
 * 
 * Define the different method to create a form dynamically from a database table field list
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 * @package wp-lazare
 * @subpackage librairies
 */

/**
 * Define the different method to manage the plugin template
 * @package wp-lazare
 * @subpackage librairies
 */
class wplazare_display
{

	/**
	*	Returns the header display of a classical HTML page.
	*
	*	@see afficherFinPage
	*
	*	@param string $pageTitle Title of the page.
	*	@param string $pageIcon Path of the icon.
	*	@param string $iconTitle Title attribute of the icon.
	*	@param string $iconAlt Alt attribute of the icon.
	*	@param boolean $hasAddButton Define if there must be a "add" button for this page
	*	@param string $actionInformationMessage A message to display in case of action is send
	*
	*	@return string Html code composing the page header
	*/
	function displayPageHeader($pageTitle, $pageIcon, $iconTitle, $iconAlt, $hasAddButton = true, $addButtonLink = '', $formActionButton = '', $actionInformationMessage = '')
	{
?>
<div class="wrap wplazareMainWrap" >
	<div id="wplazareMessage" class="fade below-h2 wplazarePageMessage <?php echo (($actionInformationMessage != '') ? 'wplazarePageMessage_Updated' : ''); ?>" ><?php _e($actionInformationMessage); ?></div>
<?php
	if($pageIcon != '')
	{
?>
	<div class="icon32 wplazarePageIcon" ><img alt="<?php _e($iconAlt); ?>" src="<?php _e($pageIcon); ?>" title="<?php _e($iconTitle); ?>" /></div>
<?php
	}
?>
	<div class="pageTitle" id="pageTitleContainer" >
		<h2 class="alignleft" ><?php _e($pageTitle);
		if($hasAddButton)
		{
?>
			<a href="<?php echo $addButtonLink ?>" class="button add-new-h2" ><?php _e('Ajouter', 'wplazare') ?></a>
<?php
		}
?>
		</h2>
		<div id="wplazarePageHeaderButtonContainer" class="wplazarePageHeaderButton" ><?php _e($formActionButton); ?></div>
	</div>
	<div id="champsCaches" class="clear wplazareHide" ></div>
	<div class="clear" id="wplazareMainContent" >
<?php
	}

	/**
	*	Returns the end of a classical page
	*
	*	@see displayPageHeader
	*
	*	@return string Html code composing the page footer
	*/
	function displayPageFooter()
	{
?>
	</div>
	<div class="clear wplazareHide" id="ajax-response"></div>
</div>
<?php
	}

	/**
	*	Return The complete output page code
	*
	*	@return string The complete html page output
	*/
	function displayPage()
	{
		global $id;

		$pageAddButton = false;
		$pageMessage = $addButtonLink = $pageFormButton = $pageIcon = $pageIconTitle = $pageIconAlt = $objectType = '';
		$outputType = 'listing';
		$objectToEdit = isset($_REQUEST['id']) ? wplazare_tools::varSanitizer($_REQUEST['id']) : '';
		$pageSlug = isset($_REQUEST['page']) ? wplazare_tools::varSanitizer($_REQUEST['page']) : '';
		$action = isset($_REQUEST['action']) ? wplazare_tools::varSanitizer($_REQUEST['action']) : '';

		/*	Select the content to add to the page looking for the parameter	*/
		switch($pageSlug)
		{
			case WPLAZARE_URL_SLUG_USERS_LISTING:
			case WPLAZARE_URL_SLUG_USERS_EDITION:
				$objectType = new wplazare_users();
				if(current_user_can('wplazare_edit_user'))
				{
					$pageAddButton = true;
				}
			break;
			
			case WPLAZARE_URL_SLUG_APPARTS_LISTING:
			case WPLAZARE_URL_SLUG_APPARTS_EDITION:
				$objectType = new wplazare_apparts();
				if(current_user_can('wplazare_add_appart'))
				{
					$pageAddButton = true;
				}
			break;
			
			case WPLAZARE_URL_SLUG_MAISONS_LISTING:
			case WPLAZARE_URL_SLUG_MAISONS_EDITION:
				$objectType = new wplazare_maisons();
				if(current_user_can('wplazare_add_appart'))
				{
					$pageAddButton = true;
				}
			break;
			
			case WPLAZARE_URL_SLUG_ASSOCIATIONS_LISTING:
			case WPLAZARE_URL_SLUG_ASSOCIATIONS_EDITION:
				$objectType = new wplazare_associations();
				if(current_user_can('wplazare_edit_association'))
				{
					$pageAddButton = true;
				}
			break;

			case WPLAZARE_URL_SLUG_LOYERS_LISTING:
			case WPLAZARE_URL_SLUG_LOYERS_EDITION:
				$objectType = new wplazare_loyers();
			
			break;
			
			case WPLAZARE_URL_SLUG_QUESTIONS_LISTING:
			case WPLAZARE_URL_SLUG_QUESTIONS_EDITION:
				$objectType = new wplazare_questions();
				if(current_user_can('wplazare_edit_user_fiches'))
				{
					$pageAddButton = true;
				}			
			break;
			
			case WPLAZARE_URL_SLUG_LOCATIONS_LISTING:
			case WPLAZARE_URL_SLUG_LOCATIONS_EDITION:
				$objectType = new wplazare_locations();
				if(current_user_can('wplazare_edit_loyer'))
				{
					$pageAddButton = true;
				}
			break;
			
			case WPLAZARE_URL_SLUG_HISTORIQUE_LISTING:
			case WPLAZARE_URL_SLUG_HISTORIQUE_EDITION:
				$objectType = new wplazare_historique();
			break;
			
			case WPLAZARE_URL_SLUG_STATS_LISTING:
			case WPLAZARE_URL_SLUG_STATS_EDITION:
				$objectType = new wplazare_stats();
			break;	
			
			case WPLAZARE_URL_SLUG_ORDERS_LISTING:
			case WPLAZARE_URL_SLUG_ORDERS_EDITION:
                $columns_to_show = array('date', 'name', 'amount', 'type', 'fiscal','reason', 'status', 'city');
                $columns_data_value = array('', '', '', '','', 'data-value="Don"', 'data-value="Terminé"', '');
				$objectType = new wplazare_orders($columns_to_show,$columns_data_value);
			break;

            case WPLAZARE_URL_SLUG_M_ORDERS_LISTING:
            case WPLAZARE_URL_SLUG_M_ORDERS_EDITION:
                $columns_to_show = array('reference', 'reason', 'amount', 'prelevement_date', 'name', 'city', 'ref_ediweb');
                $columns_data_value = array('', '', '', '', '', '', '');
                $forced_filters = array( 'payment_type' => 'multiple_payment' );
                $objectType = new wplazare_orders($columns_to_show, $columns_data_value , $forced_filters, TRUE, TRUE, WPLAZARE_URL_SLUG_M_ORDERS_LISTING, WPLAZARE_URL_SLUG_M_ORDERS_EDITION, TRUE);
                break;

			case WPLAZARE_URL_SLUG_FORMS_LISTING:
			case WPLAZARE_URL_SLUG_FORMS_EDITION:
				$objectType = new wplazare_payment_form();
				if(current_user_can('wplazare_add_forms'))
				{
					$pageAddButton = true;
				}
			break;
			
			case WPLAZARE_URL_SLUG_OFFERS_LISTING:
			case WPLAZARE_URL_SLUG_OFFERS_EDITION:
				$objectType = new wplazare_offers();
				if(current_user_can('wplazare_add_offers'))
				{
					$pageAddButton = true;
				}
			break;		
			
			default:
			{
				$pageTitle = sprintf(__('Cette page doit &ecirc;tre cr&eacute;&eacute; dans %s &agrave; la ligne %d', 'wplazare'), __FILE__, (__LINE__ - 3));
			}
			break;
		}

		if($objectType != '')
		{			
			if(($action != '') && (($action == 'edit') || ($action == 'add') || ($action == 'delete')))
			{
				$outputType = 'adding';
			}
			elseif($action == 'view')
			{
				$outputType = 'view';
			}
			
			if( (isset( $_REQUEST[wplazare_orders::getDbTable() . '_action']) && $_REQUEST[wplazare_orders::getDbTable() . '_action'] == 'export')
					|| $action == 'export') $outputType = 'export';
					
			$pageMessage = $objectType->elementAction();
			
			$pageIcon = wplazare_display::getPageIconInformation('path', $objectType);
			$pageIconTitle = wplazare_display::getPageIconInformation('title', $objectType);
			$pageIconAlt = wplazare_display::getPageIconInformation('alt', $objectType);

			if($outputType == 'listing')
			{
				$filter = isset($_REQUEST['filter']) ? wplazare_tools::varSanitizer($_REQUEST['filter']) : '';
				$pageContent = $objectType->elementList($filter);
			}
			elseif($outputType == 'adding')
			{
				if(($objectToEdit == '') && ($id != ''))
				{
					$objectToEdit = $id;
				}
				
				$objectToEdit = isset($_REQUEST['nextQuestion']) ? wplazare_tools::varSanitizer($_REQUEST['nextQuestion']) : $objectToEdit;
				$pageAddButton = false;
				$pageFormButton = $objectType->getPageFormButton();
				if( $pageSlug != WPLAZARE_URL_SLUG_HISTORIQUE_EDITION )
					$pageContent = $objectType->elementEdition($objectToEdit);
				else $pageContent = '';
			}			
			elseif($outputType == 'view')
			{
				$pageAddButton = false;

				$pageFormButton = $objectType->getPageFormButton();

				if( $pageSlug != WPLAZARE_URL_SLUG_HISTORIQUE_EDITION )
					$pageContent = $objectType->elementEdition($objectToEdit);
				else $pageContent = '';
			}
			elseif($outputType == 'export')
			{
				$pageFormButton = $objectType->getPageFormButton('export');
				echo $pageFormButton.$objectType->export($objectToEdit);
				
				return;
			}

			$pageTitle = $objectType->pageTitle();
			$posts='';
			$addButtonLink = admin_url('admin.php?page=' . $objectType->getEditionSlug() . '&amp;action=add'.$posts);
		}

		/*	Page content header	*/
		wplazare_display::displayPageHeader($pageTitle, $pageIcon, $pageIconTitle, $pageIconAlt, $pageAddButton, $addButtonLink, $pageFormButton, $pageMessage);

		/*	Page content	*/
		echo $pageContent;			
		if($pageSlug == WPLAZARE_URL_SLUG_HISTORIQUE_EDITION && $outputType == 'adding')
				$pageContent = $objectType->elementEdition($objectToEdit);
		
		/*	Page content footer	*/
		wplazare_display::displayPageFooter();
	}


	/**
	*	Return the page help content
	*
	*	@return void
	*/
	function addContextualHelp()
	{
		$pageSlug = isset($_REQUEST['page']) ? wplazare_tools::varSanitizer($_REQUEST['page']) : '';

		/*	Select the content to add to the page looking for the parameter	*/
		switch($pageSlug)
		{
			default:
				$pageHelpContent = __('Aucune aide n\'est disponible pour cette page.', 'wplazare');
			break;
		}

		add_contextual_help('boutique_page_' . $pageSlug , __($pageHelpContent, 'wplazare') );
	}

	/*
	* Return a complete html table with header, body and content
	*
	*	@param string $tableId The unique identifier of the table in the document
	*	@param array $tableTitles An array with the different element to put into the table's header and footer
	*	@param array $tableRows An array with the different value to put into the table's body
	*	@param array $tableClasses An array with the different class to affect to table rows and cols
	*	@param array $tableRowsId An array with the different identifier for table lines
	*	@param string $tableSummary A summary for the table
	*	@param boolean $withFooter Allow to define if the table must be create with a footer or not
	*
	*	@return string $table The html code of the table to output
	*/
	function getTable($tableId, $tableTitles, $tableRows, $tableClasses, $tableRowsId, $tableSummary, $withFooter = true, $tableDataValues = array())
	{
		$tableTitleBar = $tableBody = '';

		/*	Create the header and footer row	*/
		for($i=0; $i<count($tableTitles); $i++)
		{
            $data_values = '';
            if ($i < count($tableDataValues)) {
                $data_values = $tableDataValues[$i];
            }
			$tableTitleBar .= '
				<th class="' . $tableClasses[$i] . '" scope="col" '.$data_values.' >' . $tableTitles[$i] . '</th>';
		}
		
		/*	Create each table row	*/
		for($lineNumber=0; $lineNumber<count($tableRows); $lineNumber++)
		{
			$tableRow = $tableRows[$lineNumber];
			$tableBody .= '
		<tr id="' . $tableRowsId[$lineNumber] . '" class="tableRow" >';
			for($i=0; $i<count($tableRow); $i++)
			{
				$tableBody .= '
			<td class="' . $tableClasses[$i] . ' ' . $tableRow[$i]['class'] . '" >' . $tableRow[$i]['value'] . '</td>';
			}
			$tableBody .= '
		</tr>';
		}

		/*	Create the table output	*/
		$table = '
<table id="' . $tableId . '" cellspacing="0" cellpadding="0" class="tablesorter widefat post fixed" summary="' . $tableSummary . '" >';
		if($tableTitleBar != '')
		{
			$table .= '
	<thead>
			<tr class="tableTitleHeader" >' . $tableTitleBar . '
			</tr>
	</thead>';
			if($withFooter)
			{
				$table .= '
	<tfoot>
			<tr class="tableTitleFooter" >' . $tableTitleBar . '
			</tr>
	</tfoot>';
			}
		}
		$table .= '
	<tbody>' . $tableBody . '
	</tbody>
</table>
';

		return $table;
	}

	/**
	*	Define the icon informations for the page
	*
	*	@param string $infoType The information type we want to get Could be path / alt / title
	*
	*	@return string $pageIconInformation The information to output in the page
	*/
	function getPageIconInformation($infoType, $object)
	{
		switch($infoType)
		{
			case 'path':
				$pageIconInformation = $object->getPageIcon();
			break;
			case 'alt':
			case 'title':
			default:
				$pageIconInformation = $object->pageTitle();
			break;
		}

		return $pageIconInformation;
	}

}