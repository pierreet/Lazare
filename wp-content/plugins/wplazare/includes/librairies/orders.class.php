<?php
/**
 * Define the different method to access or create orders
 *
 *	Define the different method to access or create orders
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 * @package wp-lazare
 * @subpackage librairies
 */

/**
 * Define the different method to access or create orders
 * @package wp-lazare
 * @subpackage librairies
 */
class wplazare_orders
{
    protected $columns_to_show = array();
    protected $columns_data_value = array();
    protected $forced_filters = array();
    protected $disable_search_form = FALSE;
    protected $editable_elements = FALSE;
    protected $custom_listing_slug = NULL;
    protected $custom_editing_slug = NULL;
    protected $hide_total = FALSE;

    /*
     * _construct
     *
     * Constructor
     *
     * @param array $columns_to_show        The columns we want to have, if empty, switch to default
     * @param array $forced_params          The filters added to the request used to retrieve data
     * @param array $disable_search_form    Do we disable the search form (currently, search by date)
     * @param array $editable_elements       Can elements be edited ?
     * @param array $custom_listing_slug    If the slug is not the default one (see getListingSlug())
     * @param array $custom_editing_slug    If the slug is not the default one (see getEditionSlug())
     */
    function __construct($columns_to_show=array(), $columns_data_value = array(), $forced_filters=array(), $disable_search_form=FALSE, $editable_elements=FALSE, $custom_listing_slug=NULL, $custom_editing_slug=NULL, $hide_total=FALSE)
    {
        $this->columns_to_show = $columns_to_show;
        $this->columns_data_value = $columns_data_value;
        $this->forced_filters = $forced_filters;
        $this->disable_search_form = $disable_search_form;
        $this->editable_elements = $editable_elements;
        $this->custom_listing_slug = $custom_listing_slug;
        $this->custom_editing_slug = $custom_editing_slug;
        $this->hide_total = $hide_total;
    }

    /**
     *	Get the url listing slug of the current class
     *
     *	@return string The table of the class
     */
    public static function getCurrentPageCode()
    {
        return 'wplazare_orders';
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
        if($this->custom_listing_slug)
            return $this->custom_listing_slug;
        else
            return WPLAZARE_URL_SLUG_ORDERS_LISTING;
    }
    /**
     *	Get the url edition slug of the current class
     *
     *	@return string The table of the class
     */
    function getEditionSlug()
    {
        if($this->custom_editing_slug)
            return $this->custom_editing_slug;
        else
            return WPLAZARE_URL_SLUG_ORDERS_EDITION;
    }
    /**
     *	Get the database table of the current class
     *
     *	@return string The table of the class
     */
    public static function getDbTable()
    {
        return WPLAZARE_DBT_ORDERS;
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

        $title = __('Liste des commandes', 'wplazare' );
        if($action != '')
        {
            if(($action == 'edit') || ($action == 'view') || ($action == 'delete'))
            {
                $editedItem = wplazare_orders::getElement($objectInEdition);
                $title = sprintf(__('Voir la commande "%s"', 'wplazare'), $editedItem->id);
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
        $pageAction = isset($_REQUEST[wplazare_orders::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_orders::getDbTable() . '_action']) : '';
        $id = isset($_REQUEST[wplazare_orders::getDbTable()]['id']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_orders::getDbTable()]['id']) : '';

        /*	Start definition of output message when action is doing on another page	*/
        /************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
        /****************************************************************************/
        $action = isset($_REQUEST['action']) ? wplazare_tools::varSanitizer($_REQUEST['action']) : '';
        $saveditem = isset($_REQUEST['saveditem']) ? wplazare_tools::varSanitizer($_REQUEST['saveditem']) : '';
        $actionResult= "";
        $pageMessage = "";
        if(($action != '') && ($action == 'deleteok') && ($saveditem > 0))
        {
            $editedElement = wplazare_orders::getElement($saveditem, "'deleted'");
            $pageMessage = '<img src="' . WPLAZARE_SUCCES_ICON . '" alt="action success" class="wplazarePageMessage_Icon" />' . sprintf(__('La commande "%s" a &eacute;t&eacute; supprim&eacute;e avec succ&eacute;s', 'wplazare'), '<span class="bold" >' . $editedElement->id . '</span>');
        }

        if($pageAction == 'edit')
        {
            if(current_user_can('wplazare_delete_orders') )
            {
                $_REQUEST[wplazare_orders::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
                if( !is_numeric($_REQUEST[wplazare_orders::getDbTable()]['payment_recurrent_day_of_month']) || $_REQUEST[wplazare_orders::getDbTable()]['payment_recurrent_day_of_month'] == 0 )
                    $_REQUEST[wplazare_orders::getDbTable()]['payment_recurrent_day_of_month'] = 10;

                $_REQUEST[wplazare_orders::getDbTable()]['order_amount'] = $_REQUEST[wplazare_orders::getDbTable()]['order_amount']*100;
                $_REQUEST[wplazare_orders::getDbTable()]['payment_amount'] = $_REQUEST[wplazare_orders::getDbTable()]['order_amount'];

                $actionResult = wplazare_database::update($_REQUEST[wplazare_orders::getDbTable()], $id, wplazare_orders::getDbTable());
            }
            else
            {
                $actionResult = 'userNotAllowedForActionEdit';
            }
        }

        if( isset($_REQUEST['generate']) )
        {
            $reference = $_REQUEST['generate'];
            $message = wplazare_orders::getAutorisationMessage($reference);

            if ($message != "error")
                $actionResult = 'done_validate';
            else
                $actionResult = 'error_validate';
        }

        if( isset($_REQUEST['rf_don_prelevement']) )
        {
            $reference = $_REQUEST['generate'];
            $message = wplazare_orders::getRecuFiscalDonPrelevement($reference);

            if ($message != "error")
                $actionResult = 'done_validate';
            else
                $actionResult = 'error_validate';
        }

        if($pageAction == 'delete')
        {
            if(current_user_can('wplazare_delete_orders'))
            {
                $_REQUEST[wplazare_orders::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
                $_REQUEST[wplazare_orders::getDbTable()]['status'] = 'deleted';
                $actionResult = wplazare_database::update($_REQUEST[wplazare_orders::getDbTable()], $id, wplazare_orders::getDbTable());
            }
            else
            {
                $actionResult = 'userNotAllowedForActionDelete';
            }
        }

        if($pageAction == 'validatePaiement')
        {
            if(current_user_can('wplazare_delete_orders'))
            {
                $_REQUEST[wplazare_orders::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
                $_REQUEST[wplazare_orders::getDbTable()]['order_status'] = 'closed';
                $_REQUEST[wplazare_orders::getDbTable()]['status'] = 'valid';
                // 2013 1000000
                $last_numero_fiscal = wplazare_orders::getLastNumeroFiscal(date('Y'));
                $last_id = ($last_numero_fiscal % 1000000) +1;

                $_REQUEST[wplazare_orders::getDbTable()]['order_reference'] = date('Y').substr_replace("000000",$last_id, -strlen($last_id));
                $actionResult = wplazare_database::update($_REQUEST[wplazare_orders::getDbTable()], $id, wplazare_orders::getDbTable());

                $currentOrder = wplazare_orders::getElement($id, "'valid'", 'id');
                if($currentOrder->user_recu > 0) wplazare_orders::sendRecu($currentOrder);

            }
            else
            {
                $actionResult = 'userNotAllowedForActionDelete';
            }
        }

        if($pageAction == 'changePaiementToVirement')
        {
            if(current_user_can('wplazare_delete_orders'))
            {
                $_REQUEST[wplazare_orders::getDbTable()]['last_update_date'] = date('Y-m-d H:i:s');
                $_REQUEST[wplazare_orders::getDbTable()]['order_status'] = 'closed';
                $_REQUEST[wplazare_orders::getDbTable()]['payment_type'] = 'virement_payment';
                $_REQUEST[wplazare_orders::getDbTable()]['status'] = 'valid';
                // 2013 1000000
                $last_numero_fiscal = wplazare_orders::getLastNumeroFiscal(date('Y'));
                $last_id = ($last_numero_fiscal % 1000000) +1;

                $_REQUEST[wplazare_orders::getDbTable()]['order_reference'] = date('Y').substr_replace("000000",$last_id, -strlen($last_id));
                $actionResult = wplazare_database::update($_REQUEST[wplazare_orders::getDbTable()], $id, wplazare_orders::getDbTable());

                $currentOrder = wplazare_orders::getElement($id, "'valid'", 'id');
                if($currentOrder->user_recu > 0) wplazare_orders::sendRecu($currentOrder);

            }
            else
            {
                $actionResult = 'userNotAllowedForActionDelete';
            }
        }

        /*	When an action is launched and there is a result message	*/
        /************		CHANGE THE FIELD NAME TO TAKE TO DISPLAY				*************/
        /************		CHANGE ERROR MESSAGE FOR SPECIFIC CASE					*************/
        /****************************************************************************/

        if($actionResult != '')
        {
            //$elementIdentifierForMessage = '<span class="bold" >' . $_REQUEST[wplazare_orders::getDbTable()]['name'] . '</span>';
            $elementIdentifierForMessage = "";

            if($actionResult == 'error')
            {/*	CHANGE HERE FOR SPECIFIC CASE	*/
                $pageMessage .= '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />' . sprintf(__('Une erreur est survenue lors de la suppression de %s', 'wplazare'), $elementIdentifierForMessage);
                if(WPLAZARE_DEBUG)
                {
                    $pageMessage .= '<br/>' . $wpdb->last_error;
                }
            }
            elseif(($actionResult == 'done') || ($actionResult == 'nothingToUpdate'))
            {
                /*************************			GENERIC				****************************/
                /*************************************************************************/
                $pageMessage .= '<img src="' . WPLAZARE_SUCCES_ICON . '" alt="action success" class="wplazarePageMessage_Icon" />' . sprintf(__('L\'enregistrement de %s s\'est d&eacute;roul&eacute; avec succ&eacute;s', 'wplazare'), $elementIdentifierForMessage);
                if($pageAction == 'delete')
                {
                    wp_redirect(admin_url('admin.php?page=' . wplazare_orders::getListingSlug() . "&action=deleteok&saveditem=" . $id));
                }
            }
            elseif(($actionResult == 'userNotAllowedForActionEdit') || ($actionResult == 'userNotAllowedForActionAdd') || ($actionResult == 'userNotAllowedForActionDelete'))
            {
                $pageMessage .= '<img src="' . WPLAZARE_ERROR_ICON . '" alt="action error" class="wplazarePageMessage_Icon" />' . __('Vous n\'avez pas les droits n&eacute;cessaire pour effectuer cette action.', 'wplazare');
            }
            elseif ($actionResult == 'done_validate')
            {
                $pageMessage = '<img src="' . WPLAZARE_SUCCES_ICON
                    . '" alt="action success" class="wplazarePageMessage_Icon" />'
                ;

            } elseif ($actionResult == 'error_validate')
            {
                $pageMessage = '<img src="' . WPLAZARE_ERROR_ICON
                    . '" alt="action error" class="wplazarePageMessage_Icon" />'
                    . 'Une erreur est survenue.';
            }
        }
        return $pageMessage;
    }
    /**
     *	Return the list page content, containing the table that present the item list
     *
     *	@return string $listItemOutput The html code that output the item list
     */
    function elementList()
    {
        global $currencyIconList;

        $options_mois ='';
        $options_annee ='';

        /* How will we filter the data request ? */
        $filter = array();
        if(!$this->disable_search_form)
        {
            if(isset($_REQUEST['search2']) && $_REQUEST['search2']!=''){
                $filter[$_REQUEST['search2']] =  $_REQUEST[$_REQUEST['search2']];
            }
            else $filter['mois'] = date('m');
            if(isset($_REQUEST['search3']) && $_REQUEST['search3']!=''){
                $filter[$_REQUEST['search3']] =  $_REQUEST[$_REQUEST['search3']];
            }
            else $filter['annee'] = date('Y');
        }
        foreach($this->forced_filters as $key=>$value)
            $filter[$key] = $value;

        /* Generate the search form */
        $selectForm = '';
        if(!$this->disable_search_form)
        {
            foreach (wplazare_orders::getMois() as $result){
                $selected = "";
                if(isset($_REQUEST['search2']) && isset($_REQUEST[$_REQUEST['search2']]) )
                {
                    if($_REQUEST[$_REQUEST['search2']] == $result['value'])
                        $selected = "selected='selected'";
                }
                else
                {
                    if( date("n", (current_time("timestamp", 0))) == $result['value'])
                        $selected = "selected='selected'";
                }
                $options_mois .= '<option '.$selected.' value='.$result['value'].'>'.$result['name'].'</option>';
            };
            foreach (wplazare_orders::getAnnees() as $result){
                $selected = "";
                if(isset($_REQUEST['search3']) && isset($_REQUEST[$_REQUEST['search3']]) )
                {
                    if($_REQUEST[$_REQUEST['search3']] == $result)
                        $selected = "selected='selected'";
                }
                else
                {
                    if( date("Y", (current_time("timestamp", 0))) == $result)
                        $selected = "selected='selected'";
                }
                $options_annee .= '<option '.$selected.'>'.$result.'</option>';
            };

            $formAction = admin_url('admin.php?page=' . wplazare_orders::getEditionSlug());

            $mois = date("n", (current_time("timestamp", 0)));
            $annee =  date("Y", (current_time("timestamp", 0)));
            if(isset($_REQUEST['search2']) && isset($_REQUEST[$_REQUEST['search2']]) )
            {
                $mois = $_REQUEST[$_REQUEST['search2']];
            }
            if(isset($_REQUEST['search3']) && isset($_REQUEST[$_REQUEST['search3']]) )
            {
                $annee = $_REQUEST[$_REQUEST['search3']];
            }

            $selectForm='<form  method="post" action="' . $formAction . '" enctype="multipart/form-data">
                            <select name="mois" id="mois">'.$options_mois.'</select>
                            <select name="annee" id="annee" class="'.wplazare_loyers::getCurrentPageCode().'_annee">'.$options_annee.'</select>
                            <input type="submit" class="button-primary" value="recherche"/>
                            <input type="hidden" name="search2" value="mois"/>
                            <input type="hidden" name="search3" value="annee"/>
                        </form>
                        <div class="alignright">
                            <a href="'.admin_url('admin.php?page=wplazare_orders&amp;action=export&year='.$annee.'&month='.$mois).'">Export vers Excel</a>
                        </div>
            ';
        }
        /*	Start the table definition	*/
        $tableId = wplazare_orders::getDbTable() . '_list';
        $tableSummary = __('orders listing', 'wplazare');

        if($this->columns_to_show)
            $column_list = $this->columns_to_show;
        else
            $column_list = array('reference', 'date', 'amount', 'status', 'type');

        if($this->columns_data_value)
            $tableDataValues = $this->columns_data_value;
        else
            $tableDataValues = array('', '', '', '', '');


        $titles_list = array(
            'reference'     => __('R&eacute;f&eacute;rence', 'wplazare'),
            'date'          => __('Date', 'wplazare'),
            'amount'        => __('Montant', 'wplazare'),
            'status'        => __('Statut', 'wplazare'),
            'type'          => __('Type', 'wplazare'),
            'name'          => __('Nom', 'wplazare'),
            'city'          => __('Ville', 'wplazare'),
            'association'   => __('Association', 'wplazare'),
            'reason'        => __('Don/Charge', 'wplazare'),
            'prelevement_date' => __('Jour de prélèvement', 'wplazare'),
            'ref_ediweb'    => __('Référence EDIWEB', 'wplazare'),
            'fiscal'        => 'Numéro recu fiscal'
        );
        $classes_list = array(
            'reference'     => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_reference_column',
            'date'          => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_date_column',
            'amount'        => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_ammount_column',
            'status'        => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_order_status_column filter-select',
            'type'          => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_type_column filter-select',
            'name'          => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_name_column filter-select',
            'city'          => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_city_column filter-select',
            'association'   => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_asso_column filter-select',
            'reason'        => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_reason_column filter-select',
            'prelevement_date'  => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_prelevement_date_column filter-select',
            'ref_ediweb'    => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_ref_ediweb_column filter-select',
            'fiscal'        => 'wplazare_' . wplazare_orders::getCurrentPageCode() . '_ref_fiscal_column'
        );

        $tableTitles = array();
        $tableClasses = array();
        foreach( $column_list as $column)
        {
            $tableTitles[] = $titles_list[$column];
            $tableClasses[] = $classes_list[$column];
        }
        unset($column);

        $line = 0;
        if(empty($filter)) $filter = '';
        $elementList = wplazare_orders::getElement('', "'valid', 'moderated'", '', " ORDER BY O.creation_date DESC",$filter);
        $total_sum = 0;
        if(count($elementList) > 0)
        {
            foreach($elementList as $element)
            {
                $tableRowsId[$line] = wplazare_orders::getDbTable() . '_' . $element->id;

                $elementLabel = $element->id;
                $subRowActions = '';
                $editAction = "";
                if(current_user_can('wplazare_view_orders_details'))
                {
                    if($this->editable_elements)
                        $editAction = admin_url('admin.php?page=' . wplazare_orders::getEditionSlug() . '&amp;action=edit&amp;id=' . $element->id);
                    else
                        $editAction = admin_url('admin.php?page=' . wplazare_orders::getEditionSlug() . '&amp;action=view&amp;id=' . $element->id);

                    $editText1 = ($this->editable_elements) ? __('Modifier', 'wplazare') : __('Voir', 'wplazare');
                    $subRowActions .= '
		<a href="' . $editAction . '" >' .$editText1. '</a>';
                    $elementLabel = '<a href="' . $editAction . '" >' . $element->id  . '</a>';
                }
                if(current_user_can('wplazare_delete_orders'))
                {
                    if($subRowActions != '')
                    {
                        $subRowActions .= '&nbsp;|&nbsp;';
                    }
                    $subRowActions .= '
		<a href="' . admin_url('admin.php?page=' . wplazare_orders::getEditionSlug() . '&amp;action=delete&amp;id=' . $element->id). '" >' . __('Supprimer', 'wplazare') . '</a>';
                }
                $rowActions = '
	<div id="rowAction' . $element->id . '" class="wplazareRowAction" >' . $subRowActions . '
	</div>';

                $orderAmount = ($element->order_amount / 100);

                $boldClass = "";
                if( ($element->order_status == 'initialised') && ($element->payment_type == 'cheque_payment') ){
                    $boldClass = "bold";
                }

                $tableRowValue = array();
                foreach( $column_list as $column)
                {
                    if($column == 'reference')
                        $tableRowValue[] = array('class' => wplazare_orders::getCurrentPageCode() . '_reference_cell', 'value' => $elementLabel);
                    if($column == 'fiscal')
                        $tableRowValue[] = array('class' => wplazare_orders::getCurrentPageCode() . '_reference_cell', 'value' => '<a href="' . $editAction . '" >' .$element->order_reference. '</a>');
                    if($column == 'date')
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_date_cell', 'value' => '<a href="' . $editAction . '" >' . mysql2date('d M Y H:i:s', $element->creation_date)  . '</a>'. $rowActions, true);
                    if($column == 'amount'){
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_amount_cell', 'value' => $orderAmount . '&nbsp;' . $currencyIconList[$element->order_currency]);
                        if($element->order_status == "closed"){
                            $total_sum += $orderAmount;
                        }
                    }

                    if($column == 'status')
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_order_status_cell', 'value' => __($element->order_status, 'wplazare'));
                    if($column == 'type')
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_type_cell', 'value' => __($element->payment_type, 'wplazare'));

                    if($column == 'name')
                    {
                        if($element->location_id)
                        {
                            $location = wplazare_locations::getElement($element->location_id);
                            $locataire_id = $location->user;
                            $full_name = wplazare_tools::getUserName($locataire_id);
                        }
                        else
                            $full_name = $element->user_firstname." ".$element->user_lastname;

                        $full_name = (trim($full_name) != '') ? $full_name : __('Non renseigné', 'wplazare');
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_type_cell', 'value' => $full_name);
                    }
                    if($column == 'city')
                    {
                        if($element->location_id)
                        {
                            $location = wplazare_locations::getElement($element->location_id);
                            $appartement_id = $location->appartement;
                            $appart = wplazare_apparts::getElement($appartement_id);
                            if(isset($appart->ville))
                                $city = $appart->ville;
                            else
                                $city = '';
                        }
                        else
                            $city = $element->user_ville;

                        $city = ($city != '') ? $city : __('Non renseigné', 'wplazare');
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_type_cell', 'value' => $city);
                    }
                    if($column == 'association')
                    {
                        $asso = ($element->user_association != '') ? $element->user_association : __('Non renseigné', 'wplazare');
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_type_cell', 'value' => $asso);
                    }
                    if($column == 'reason')
                    {
                        $reason = is_null($element->location_id) ? __('Don', 'wplazare') : __('Charge', 'wplazare');
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_type_cell', 'value' => $reason);
                    }
                    if($column == 'prelevement_date')
                    {
                        $prelevement_date = ($element->payment_recurrent_day_of_month != '') ? $element->payment_recurrent_day_of_month : __('Non renseigné', 'wplazare');
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_type_cell', 'value' => $prelevement_date);
                    }
                    if($column == 'ref_ediweb')
                    {
                        $ref_ediweb = ($element->order_transaction != '') ? $element->order_transaction : __('Non renseigné', 'wplazare');
                        $tableRowValue[] = array('class' => $boldClass.' '.wplazare_orders::getCurrentPageCode() . '_type_cell', 'value' => $ref_ediweb);
                    }
                }
                $tableRows[] = $tableRowValue;

                $line++;
            }
        }
        else
        {
            $tableRowsId[] = wplazare_orders::getDbTable() . '_noResult';
            unset($tableRowValue);
            $tableRowValue[] = array('class' => wplazare_orders::getCurrentPageCode() . '_label_cell', 'value' => __('Aucun don', 'wplazare'));
            $tableRows[] = $tableRowValue;
        }
        $listItemOutput = wplazare_display::getTable($tableId, $tableTitles, $tableRows, $tableClasses , $tableRowsId, $tableSummary, true, $tableDataValues);

        $total_line = "";
        if(!$this->hide_total){
            $total_line = "<h2>Montant total du mois (Don avec statut = \"Terminé\"): ".$total_sum.$currencyIconList[$element->order_currency]."</h2>";
        }

        return $selectForm.$listItemOutput."<br/>".$total_line;
    }
    /*
    *	Return the page content to add a new item
    *
    *	@return string The html code that output the interface for adding a nem item
    */
    function elementEdition($itemToEdit = '')
    {
        global $currencyIconList;
        $dbFieldList = wplazare_database::fields_to_input(wplazare_orders::getDbTable());

        $editedItem = '';
        if($itemToEdit != '')
        {
            $editedItem = wplazare_orders::getElement($itemToEdit);
        }

        $orderStatus = '';
        $isChequePayment = false;

        $the_form_content_hidden = $the_form_general_content = '';
        $the_form_payment_content = $the_form_user_content = $the_form_order_content = $the_form_bank_content = $the_form_charge_content = '';
        foreach($dbFieldList as $input_key => $input_def)
        {
            $input_name = $input_def['name'];
            $input_value = $input_def['value'];
            $pageAction = isset($_REQUEST[wplazare_orders::getDbTable() . '_action']) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_orders::getDbTable() . '_action']) : '';
            $requestFormValue = isset($_REQUEST[wplazare_orders::getDbTable()][$input_name]) ? wplazare_tools::varSanitizer($_REQUEST[wplazare_orders::getDbTable()][$input_name]) : '';
            $currentFieldValue = $input_value;

            if(is_object($editedItem))
            {
                $currentFieldValue = $editedItem->$input_name;
            }
            elseif(($pageAction != '') && ($requestFormValue != ''))
            {
                $currentFieldValue = $requestFormValue;
            }

            /*	Translate the field value	*/
            $input_def['value'] = __($currentFieldValue, 'wplazare');

            /*	Store the payment definition fields	*/
            if(substr($input_name, 0, 8) == 'payment_')
            {
                if($input_name == 'payment_recurrent_day_of_month' && $editedItem->payment_type == 'multiple_payment' && $_REQUEST['action'] == 'edit')
                {
                    $days_list = array_combine(range(1, 28),range(1, 28));
                    if( $currentFieldValue == 0 )
                    {
                        $input_def['value'] = __('Non renseign&eacute;', 'wplazare');
                        $days_list[0] = $input_def['value'];
                    }

                    $input_def['possible_value'] = $days_list;

                    $input_def['type'] = 'select';
                    $input_def['valueToPut'] = 'index';
                    $the_input = wplazare_form::check_input_type($input_def, wplazare_orders::getDbTable());
                    $the_form_payment_content .= '
        <div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
                    ' . __($input_name, 'wplazare') . '
                    </div>
			<div class="wplazare_form_input wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
                    ' . $the_input . '
                    </div>
		</div>';
                }

                if($input_name == 'payment_type')
                {
                    $the_form_payment_content .= '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wplazare') . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $input_def['value'] . '
			</div>
		</div>';
                }

                if($editedItem->payment_type == 'cheque_payment')
                    $isChequePayment = true;
            }
            /*	Store the user fields	*/
            elseif(substr($input_name, 0, 5) == 'user_')
            {
                $input_name = $input_name . '_admin_side';
                $the_form_user_content .= '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wplazare') . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . wplazare_tools::varSanitizer($input_def['value']) . '
			</div>
		</div>';
            }

            /*	Store the payment return fields	*/
            elseif(substr($input_name, 0, 6) == 'order_')
            {
                if($input_name == 'order_currency')
                {
                    $input_def['value'] = $currencyIconList[$currentFieldValue];
                    $the_input = $input_def['value'];
                }
                elseif($input_name == 'order_amount')
                {
                    $input_def['value'] = ($currentFieldValue / 100);
                    if($editedItem->payment_type == 'multiple_payment' && ($_REQUEST['action'] == 'edit'))
                        $the_input = wplazare_form::check_input_type($input_def, wplazare_orders::getDbTable());
                    else
                        $the_input = $input_def['value'];
                }
                else
                {
                    $the_input = $input_def['value'];
                }

                if($input_name == 'order_status')
                    $orderStatus = $input_def['value'];

                $the_form_order_content .= '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wplazare') . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $the_input . '
			</div>
		</div>';
            }

            elseif(substr($input_name, 0, 7) == 'banque_')
            {
                $the_form_bank_content .= '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wplazare') . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $input_def['value'] . '
			</div>
		</div>';
            }

            elseif(substr($input_name, 0, 9) == 'location_')
            {
                if($input_name == 'location_id' && $input_def['value'] != "")
                {
                    $current_location = wplazare_locations::getElement($input_def['value']);
                    $current_user_id = $current_location->user;
                    $current_appartement = wplazare_apparts::getElement($current_location->appartement);
                    $editUserAction = admin_url(
                        'admin.php?page='
                        . wplazare_users::getEditionSlug()
                        . '&amp;action=edit&amp;id=' . $current_user_id);
                    $editLocationAction = admin_url(
                        'admin.php?page='
                        . wplazare_locations::getEditionSlug()
                        . '&amp;action=edit&amp;id=' . $current_location->id);
                    $the_form_charge_content .= '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __('Locataire', 'wplazare') . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
			    <a href="'.$editUserAction.'">
				' .wplazare_tools::getUserName($current_user_id) . '
				</a>
			</div>
		</div>';
                    $the_form_charge_content .= '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wplazare') . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
			    <a href="'.$editLocationAction.'">
				' . $input_def['value'] . ' - '. wplazare_apparts::getAdresseComplete($current_appartement) .'
				</a>
			</div>
		</div>';
                }
                else{
                    $the_form_charge_content .= '
		<div class="clear" >
			<div class="wplazare_form_label wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
				' . __($input_name, 'wplazare') . '
			</div>
			<div class="wplazare_form_input wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
				' . $input_def['value'] . '
			</div>
		</div>';
                }
            }

            /*	For all the other field	*/
            else
            {
                if($input_name == 'creation_date')
                {
                    $input_name = 'order_creation_date';
                    $input_def['value'] = mysql2date('d M Y H:i', $currentFieldValue, true);
                }

                if(($input_name == 'status') || ($input_name == 'last_update_date') || ($input_name == 'form_id' ) || ($input_name == 'offer_id' ))
                {
                    $input_def['type'] = 'hidden';
                    $input_def['value'] = $currentFieldValue;
                }

                if($input_def['type'] != 'hidden')
                {
                    $the_form_general_content .= '
			<div class="clear" >
				<div class="wplazare_form_label wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_label alignleft" >
					' . __($input_name, 'wplazare') . '
				</div>
				<div class="wplazare_form_input wplazare_' . wplazare_orders::getCurrentPageCode() . '_' . $input_name . '_input alignleft" >
					' . $input_def['value'] . '
				</div>
			</div>';
                }
                else
                {
                    $the_form_content_hidden .= '
			' . wplazare_form::check_input_type($input_def, wplazare_orders::getDbTable());
                }

            }
        }

        /*	Build the general output with the different order's element	*/
        $the_form_general_content .= '
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Informations commandes', 'wplazare') . '</legend>
			<div>' . $the_form_order_content . '</div>
		</fieldset>
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Informations client', 'wplazare') . '</legend>
			<div>' . $the_form_user_content . '</div>
		</fieldset>
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Informations paiement', 'wplazare') . '</legend>
			<div>' . $the_form_payment_content	. '</div>
		</fieldset>
        <fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Banque', 'wplazare') . '</legend>
			<div>' . $the_form_bank_content	. '</div>
		</fieldset>
		<fieldset class="clear orderSection" >
			<legend class="orderSectionMainTitle" >' . __('Paiement Charges', 'wplazare') . '</legend>
			<div>' . $the_form_charge_content	. '</div>
		</fieldset>';

        if($isChequePayment && $orderStatus != __('closed', 'wplazare')){
            $the_form_general_content .= '<br/><input type="button" class="button-primary" id="validatePaiement" name="validatePaiement" value="D&eacute;clar&eacute; Pay&eacute; et envoyer le re&ccedil;u"><br/>';
            $the_form_general_content .= '<br/><input type="button" class="button-primary" id="changePaiementToVirement" name="changePaiementToVirement" value="Changer le type de paiement en virement et envoyer le re&ccedil;u">';
        }

        if($editedItem->payment_type == "multiple_payment" && $editedItem->location_id == NULL){
            $formEditRecuAction = admin_url('admin.php?page=' . wplazare_orders::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit);
            $recu_form = '
 <fieldset class="clear orderSection">
			<legend class="orderSectionMainTitle">Edition reçu</legend>
            <div class="clear" >
				<div class="wplazare_form_label alignleft" >
					Nombre de mois pay&eacute;s
				</div>
				<div class="wplazare_form_input alignleft" >
					<input type="number" name="nbr_mois" min="1" max="12"/>
				</div>
			</div>
			<div class="clear" >
				<div class="wplazare_form_label alignleft" >
					Premier mois de pr&eacute;l&egrave;vement
				</div>
				<div class="wplazare_form_input alignleft" >
					<input type="text" name="premier_mois"/>
				</div>
			</div>
			<div class="clear" >
				<div class="wplazare_form_label alignleft" >
					Ann&eacute;e premier mois
				</div>
				<div class="wplazare_form_input alignleft" >
					<input type="number" name="premier_mois_annee"/>
				</div>
			</div>
			<div class="clear" >
				<div class="wplazare_form_label alignleft" >
					Dernier mois de pr&eacute;l&egrave;vement
				</div>
				<div class="wplazare_form_input alignleft" >
					<input type="text" name="dernier_mois"/>
				</div>
			</div>
			<div class="clear" >
				<div class="wplazare_form_label alignleft" >
					Ann&eacute;e dernier mois
				</div>
				<div class="wplazare_form_input alignleft" >
					<input type="number" name="dernier_mois_annee"/>
				</div>
			</div>
			<div class="clear" >
				<div class="wplazare_form_label alignleft" >
					<input type="button" class="button-primary" value="Editer le recu" id="editRecuDonMensuel" />
				</div>
			</div>
		</fieldset>
            ';
            $the_form_general_content .= $recu_form;
        }


        if($orderStatus == __('closed', 'wplazare')) $the_form_general_content .= '<br/><br/><input type="button" class="button-primary" id="editRecu" name="editRecu" value="Editer le re&ccedil;u">';

        $formEditAction = admin_url('admin.php?page=' . wplazare_orders::getEditionSlug() . '&amp;action=edit&amp;id=' . $itemToEdit);
        $formAction = "";
        if(isset($_REQUEST['action']) && ($_REQUEST['action'] == 'view'))
        {
            $formAction = $formEditAction;
        }

        $link_to_autorisation = "";
        if($editedItem->payment_type == 'multiple_payment')
        {
            $link_to_autorisation = '
                <div class="clear orderSection">
                    <a href="'.admin_url('admin.php?page=' . wplazare_orders::getEditionSlug() . '&amp;action=edit&amp;id='.$itemToEdit.'&amp;generate='.$itemToEdit).'"> '.__("G&eacute;n&eacute;rer l'autorisation de pr&eacute;l&egrave;vement" , 'wplazare').'</a>
                </div>';
        }

        $the_form = '
<form name="' . wplazare_orders::getDbTable() . '_form" id="' . wplazare_orders::getDbTable() . '_form" method="post" action="'.$formAction.'" >
' . wplazare_form::form_input(wplazare_orders::getDbTable() . '_action', wplazare_orders::getDbTable() . '_action', (isset($_REQUEST['action']) && ($_REQUEST['action'] != '') ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'save') , 'hidden') . '
<div id="wplazareFormManagementContainer" >
	' . $the_form_content_hidden .'
	<div id="wplazare_' . wplazare_orders::getCurrentPageCode() . '_main_infos_form" >' . $the_form_general_content . '
	</div>
</div>
</form>
'.$link_to_autorisation.'
<script type="text/javascript" >
    wplazareMainInterface("' . wplazare_orders::getDbTable() . '", "' . __('&Ecirc;tes vous s&ucirc;r de vouloir quitter cette page? Vous perdrez toutes les modification que vous aurez effectu&eacute;es', 'wpshop') . '", "' . admin_url('admin.php?page=' . wplazare_orders::getEditionSlug()) . '");

	wplazare(document).ready(function(){
		wplazare("#delete").click(function(){
			wplazare("#' . wplazare_orders::getDbTable() . '_action").val("delete");
			deleteOrder();
		});
		if(wplazare("#' . wplazare_orders::getDbTable() . '_action").val() == "delete"){
			deleteOrder();
		}
		function deleteOrder(){
			if(confirm(wplazareConvertAccentTojs("' . __('&Ecirc;tes vous s&ucirc;r de vouloir supprimer cette commande?', 'wplazare') . '"))){
                wplazare("#' . wplazare_orders::getDbTable() . '_form").attr("action","'.$formEditAction.'&noheader=true");
				wplazare("#' . wplazare_orders::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_orders::getDbTable() . '_action").val("edit");
			}
		}

		wplazare("#validatePaiement").click(function(){
			wplazare("#' . wplazare_orders::getDbTable() . '_action").val("validatePaiement");
			validatePaiement();
		});
		if(wplazare("#' . wplazare_orders::getDbTable() . '_action").val() == "validatePaiement"){
			validatePaiement();
		}
		function validatePaiement(){
			if(confirm(wplazareConvertAccentTojs("' . __('&Ecirc;tes vous s&ucirc;r de vouloir valider le paiement de cette commande?', 'wplazare') . '"))){
				wplazare("#' . wplazare_orders::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_orders::getDbTable() . '_action").val("edit");
			}
		}

		wplazare("#changePaiementToVirement").click(function(){
			wplazare("#' . wplazare_orders::getDbTable() . '_action").val("changePaiementToVirement");
			changePaiementToVirement();
		});
		if(wplazare("#' . wplazare_orders::getDbTable() . '_action").val() == "changePaiementToVirement"){
			changePaiementToVirement();
		}
		function changePaiementToVirement(){
			if(confirm(wplazareConvertAccentTojs("' . __('&Ecirc;tes vous s&ucirc;r de vouloir changer le type de paiement en virement?', 'wplazare') . '"))){
				wplazare("#' . wplazare_orders::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_orders::getDbTable() . '_action").val("edit");
			}
		}

		wplazare("#editRecuDonMensuel").click(function(){
			wplazare("#' . wplazare_orders::getDbTable() . '_action").val("export");
			editRecuDonMensuel();
		});
		if(wplazare("#' . wplazare_orders::getDbTable() . '_action").val() == "export"){
			editRecuDonMensuel();
		}
		function editRecuDonMensuel(){
			if(confirm(wplazareConvertAccentTojs("' . __('&Ecirc;tes vous s&ucirc;r de vouloir &eacute;diter le re&ccedil;u avec les param&egrave;tres actuels?', 'wplazare') . '"))){
				wplazare("#' . wplazare_orders::getDbTable() . '_form").submit();
			}
			else{
				wplazare("#' . wplazare_orders::getDbTable() . '_action").val("edit");
			}
		}

		wplazare("#editRecu").click(function(){
			wplazare("#' . wplazare_orders::getDbTable() . '_action").val("export");
			wplazare("#' . wplazare_orders::getDbTable() . '_form").submit();
		});
		if(wplazare("#' . wplazare_orders::getDbTable() . '_action").val() == "export"){
			wplazare("#' . wplazare_orders::getDbTable() . '_form").submit();
		}
	});
</script>';

        return $the_form;
    }
    /*
    *	Return the different button to save the item currently being added or edited
    *
    *	@return string $currentPageButton The html output code with the different button to add to the interface
    */
    function getPageFormButton($export ='')
    {
        $action = isset($_REQUEST['action']) ? wplazare_tools::varSanitizer($_REQUEST['action']) : 'add';
        $currentPageButton = '';

        if(current_user_can('wplazare_delete_orders') && ($action != 'add') && ($export == ''))
        {
            if($this->editable_elements)
                $currentPageButton .= '<input type="button" class="button-primary" id="save" name="save" value="'.__('Enregistrer', 'wplazare').'" />';

            $currentPageButton .= '<input type="button" class="button-primary" id="delete" name="delete" value="' . __('Supprimer', 'wplazare') . '" />';
        }

        $currentPageButton .= '<h2 class="alignright wplazareCancelButton" ><a href="' . admin_url('admin.php?page=' . wplazare_orders::getListingSlug()) . '" class="button add-new-h2" >' . __('Retour', 'wplazare') . '</a></h2>';

        return $currentPageButton;
    }
    /*
    *	Get the existing element list into database
    *
    *	@param integer $elementId optionnal The element identifier we want to get. If not specify the entire list will be returned
    *	@param string $elementStatus optionnal The status of element to get into database. Default is set to valid element
    *
    *	@return object $elements A wordpress database object containing the element list
    */
    function getElement($elementId = '', $elementStatus = "'valid', 'moderated'", $whatToGet = 'id', $orderByStatement = '',$filters = '')
    {
        global $wpdb;
        $moreQuery_filters = "";

        if($elementId != '')
        {
            $moreQuery_filters = "
			AND O." . $whatToGet . " = '" . $elementId . "' ";
        }
        if($filters != ''){
            foreach ($filters as $key => $value){
                switch ($key)
                {
                    case 'status':
                        if($value != '- Status -'):
                            $moreQuery_filters .= "AND O.order_status LIKE '$value' ";
                        endif;
                        break;
                    case 'payment_type':
                        if($value != '- Type Paiement -'):
                            $moreQuery_filters .= "AND O.payment_type = '$value' ";
                        endif;
                        break;
                    case 'mois':
                        $moreQuery_filters .= "AND MONTH(O.creation_date) ='$value' ";
                        break;
                    case 'annee':
                        $moreQuery_filters .= "AND YEAR(O.creation_date) ='$value' ";
                        break;
                }
            }
        }

        $query = $wpdb->prepare(
            "SELECT O.*
            FROM " . wplazare_orders::getDbTable() . " AS O
		WHERE O.status IN (".$elementStatus.") " . $moreQuery_filters . "
		" . $orderByStatement
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

    /*
     * Edite le re?u de la commande en parametre.
     */
    function export($itemToEdit){

        $return = "";

        if($itemToEdit && $itemToEdit != 0){
            $template_name ="recu_fiscal";
            $pdfator = new wplazare_pdfator();

            $currentOrder = wplazare_orders::getElement($itemToEdit, "'valid'", 'id');

            /* TODO faire test si paiement multiple => récuperer année mois, préparer les balises, changer le remplate name
            ET BIM
             */

            $balises_replace = wplazare_orders::prepareBalisesReplace($currentOrder);

            if($currentOrder->payment_type == "multiple_payment" && $currentOrder->location_id == NULL){
                $template_name="recu_fiscal_don_mensuel";
                $premier_mois = (wplazare_tools::varSanitizer($_POST['premier_mois']));
                $premier_mois_annee = intval(wplazare_tools::varSanitizer($_POST['premier_mois_annee']));
                $dernier_mois = (wplazare_tools::varSanitizer($_POST['dernier_mois']));
                $dernier_mois_annee = intval(wplazare_tools::varSanitizer($_POST['dernier_mois_annee']));

                $nbr_mois = intval(wplazare_tools::varSanitizer($_POST['nbr_mois']));
                $somme_dons_mensuels = $nbr_mois * $currentOrder->order_amount / 100 ;
                $balises_replace[] = array( "balise" => "{PREMIER_MOIS}", "new_text" => $premier_mois );
                $balises_replace[] = array( "balise" => "{PREMIER_MOIS_ANNEE}", "new_text" => $premier_mois_annee );
                $balises_replace[] = array( "balise" => "{DERNIER_MOIS}", "new_text" => $dernier_mois );
                $balises_replace[] = array( "balise" => "{DERNIER_MOIS_ANNEE}", "new_text" => $dernier_mois_annee );
                $balises_replace[] = array( "balise" => "{SOMME_DONS_MENSUELS}", "new_text" => $somme_dons_mensuels );
                $balises_replace[] = array( "balise" => "{DEDUCTION_DONS_MENSUELS}", "new_text" => wplazare_orders::prepareDeductionDonsMensuels($currentOrder, $somme_dons_mensuels) );
            }


            /* TODO donner l'id de l'utilisateur en paramètre pour sauvegarder sous ID/ANNEE MOIS */
            $file_path = $pdfator->getPdf($template_name, $balises_replace);

            if($file_path != ''){
                $return = '<h3>Re&ccedil;u fiscal</h3>';
                $return .= '<div>'.'Le re&ccedil;u a bien &eacute;t&eacute; g&eacute;n&eacute;r&eacute;:<br/>'.
                    '<a href="'.plugins_url( '/wplazare/includes/librairies/html2pdf/output/'. basename($file_path)).'"><img src="'.get_theme_root_uri().'/lazare/images/download.jpg" alt="T&eacute;l&eacutecharger" /></a>'.
                    '</div>';
            }
            else {
                $return = wplazare_orders::elementEdition($itemToEdit);
            }
        }
        else{
            $excelator = new wplazare_excelator();

            $year = intval(wplazare_tools::varSanitizer($_GET['year']));
            $month = intval(wplazare_tools::varSanitizer($_GET['month']));

            if($year >0 && month >=0 ){
                $results = wplazare_orders::getDonsByMonth($year, $month, 'closed');

                unlink(WPLAZARE_EXCEL_PLUGIN_DIR . '/don_lazare.xlsx');

                $excelator->getDons($results,$month." ".$year);

                $return .= '<h3>Export dons du mois.</h3>';

                $return .= '<div>'
                    . 'Le fichier xls &eacute;t&eacute; g&eacute;n&eacute;r&eacute;:<br/>'
                    . '<a class="excel" href="'
                    . plugins_url(
                        '/wplazare/includes/librairies/excel/don_lazare.xlsx')
                    . '">Dons du mois</a>';

                '</div>';
            }
            else{
                $return = "Erreur, veuillez faire précédent sur votre navigateur.";
            }

            return $return;
        }


        return $return	;
    }

    /**
     *	Save a new order into database from the given informations
     *
     *	@param array $orderInformations The informations sent by the user through the form and from the payment form definition
     *
     *	@return integer $orderReference The last order Identifier to create an unique
     */
    function saveNewOrder($orderInformations)
    {
        global $wpdb;
        $orderReference = 0;

        /*	Get the last order identifier	*/
        $offer = wplazare_offers::getElement($_POST['selectedOffer']);

        /*	Create the new order	*/
        $orderMoreInformations['id'] = '';
        $orderMoreInformations['form_id'] = $orderInformations['formIdentifier'];
        $orderMoreInformations['offer_id'] = $orderInformations['selectedOffer'];
        $orderMoreInformations['status'] = 'valid';
        $orderMoreInformations['order_status'] = 'initialised';
        $orderMoreInformations['creation_date'] = date('Y-m-d H:i:s');

        if (array_key_exists('order_user', $orderInformations)) {
            foreach($orderInformations['order_user'] as $orderUserField => $orderUserFieldValue)
            {
                if($orderUserField != 'newsletter') $orderMoreInformations[$orderUserField] = $orderUserFieldValue;
            }
        }


        //check payment type
        if(isset($_POST['carte']) && $_POST['carte'] == 'true' ){
            $offer->payment_type='single_payment';
        }
        elseif(isset($_POST['prelevement']) && $_POST['prelevement'] == 'true' ){
            $offer->payment_type='multiple_payment';
        }
        elseif(isset($_POST['cheque']) && $_POST['cheque'] == 'true' ){
            $offer->payment_type='cheque_payment';
        }

        //newsletter payment
        if(isset($_POST['order_user']['newsletter']))
            wplazare_tools::subscribeNewsletter(WPLAZARE_ROLE_DONATEUR,$orderMoreInformations['user_firstname']
                ,$orderMoreInformations['user_lastname'],$orderMoreInformations['user_email']);

        /*	Save offer informations in case of modification in future	*/
        $orderMoreInformations['payment_type'] = $offer->payment_type;
        $orderMoreInformations['payment_recurrent_amount'] = $offer->payment_recurrent_amount;
        $orderMoreInformations['payment_recurrent_number'] = $offer->payment_recurrent_number;
        $orderMoreInformations['payment_recurrent_frequency'] = $offer->payment_recurrent_frequency;
        $orderMoreInformations['payment_recurrent_day_of_month'] = $offer->payment_recurrent_day_of_month;
        $orderMoreInformations['payment_recurrent_start_delay'] = $offer->payment_recurrent_start_delay;
        $orderMoreInformations['payment_reference_prefix'] = $offer->payment_reference_prefix;
        $orderMoreInformations['payment_name'] = $offer->payment_name;
        $orderMoreInformations['payment_currency'] = $offer->payment_currency;
        $orderMoreInformations['payment_amount'] = $offer->payment_amount;
        $orderMoreInformations['order_currency'] = $offer->payment_currency;
        $orderMoreInformations['order_amount'] = $offer->payment_amount;

        if(isset($_POST['valeur'] )){
            $orderMoreInformations['payment_amount'] = $_POST['valeur'] * 100;
            $orderMoreInformations['order_amount'] = $_POST['valeur'] * 100;
        }
        $actionResult = wplazare_database::save($orderMoreInformations, wplazare_orders::getDbTable());
        if($actionResult == 'done')
        {
            $orderReference = $wpdb->insert_id;
            /*	Update the new order reference	*/
            $orderMoreInformations['last_update_date'] = date('Y-m-d H:i:s');
            $orderMoreInformations['order_reference'] = $offer->payment_reference_prefix . $orderReference;
            wplazare_database::update($orderMoreInformations, $orderReference, wplazare_orders::getDbTable());
        }


        /*	Check the payment type in case that this is a multiple payment	*/
        if($offer->payment_type == 'multiple_payment')
        {
            //$orderReference .= 'IBS_2MONT' . zeroise($offer->payment_recurrent_amount, 10) . 'IBS_NBPAIE' . zeroise($offer->payment_recurrent_number, 2) . 'IBS_FREQ' . zeroise($offer->payment_recurrent_frequency, 2) . 'IBS_QUAND' . zeroise($offer->payment_recurrent_day_of_month, 2) . 'IBS_DELAIS' . zeroise($offer->payment_recurrent_start_delay, 3);
        }

        return $orderReference;
    }

    /**
     *	Output the result of a transaction we return form lazare. Called by a shortcode on the return page (success/canceled/declined)
     *
     *	@return string $outputMessage A message to output to the end-user when transaction is finished
     */
    function paymentReturn()
    {
        global $currencyIconList;

        $reference = isset($_REQUEST['reference']) ? wplazare_tools::varSanitizer($_REQUEST['reference']) : '';
        $autorisation = isset($_REQUEST['autorisation']) ? wplazare_tools::varSanitizer($_REQUEST['autorisation']) : '';
        $transaction = isset($_REQUEST['transaction']) ? wplazare_tools::varSanitizer($_REQUEST['transaction']) : '';
        $error = isset($_REQUEST['error']) ? wplazare_tools::varSanitizer($_REQUEST['error']) : '';

        if($reference != '')
        {
            $referenceComponent = explode('IBS_2MONT', $reference);
            if(is_array($referenceComponent) && (count($referenceComponent) >=2 ))
            {
                $reference = $referenceComponent[0];
            }
            /*	Get the orders informations to update with the lazare return infos	*/
            $currentOrder = wplazare_orders::getElement($reference, "'valid'", 'id');

            /*	Update the current order	*/
            $orderMoreInformations['last_update_date'] = date('Y-m-d H:i:s');
            $orderMoreInformations['order_autorisation'] = $autorisation;
            $orderMoreInformations['order_transaction'] = $transaction;
            $orderMoreInformations['order_error'] = $error;

            $return = $_REQUEST[wplazare_payment_form::getDbTable()];

            if($currentOrder->payment_type == 'multiple_payment'){
                $orderMoreInformations['banque_code'] = wplazare_tools::varSanitizer($return['banque_code']);
                $orderMoreInformations['banque_code_guichet'] = wplazare_tools::varSanitizer($return['banque_code_guichet']);
                $orderMoreInformations['banque_code_numero_compte'] = wplazare_tools::varSanitizer($return['banque_code_numero_compte']);
                $orderMoreInformations['banque_code_cle_rib'] = wplazare_tools::varSanitizer($return['banque_code_cle_rib']);
                $orderMoreInformations['banque_iban'] = wplazare_tools::varSanitizer($return['banque_iban']);
                $orderMoreInformations['banque_nom'] = wplazare_tools::varSanitizer($return['banque_nom']);
                $orderMoreInformations['banque_adresse'] = wplazare_tools::varSanitizer($return['banque_adresse']);
                $orderMoreInformations['banque_code_postal'] = wplazare_tools::varSanitizer($return['banque_code_postal']);
                $orderMoreInformations['banque_ville'] = wplazare_tools::varSanitizer($return['banque_ville']);

                if(wplazare_tools::check_iban($orderMoreInformations['banque_iban'])){
                    $outputMessage = wplazare_orders::buildPrelevementReturn($currentOrder,$orderMoreInformations);
                }
                else{
                    $order_status = 'error';
                    $outputMessage = wplazare_payment_form::getPrelevementForm($reference, $orderMoreInformations,__('L\'IBAN n\'est pas valide. Veuillez vérifier votre saisie.', 'wplazare'));
                }


            }
            elseif($currentOrder->payment_type == 'cheque_payment'){
                $outputMessage = wplazare_orders::buildChequeReturn($currentOrder);
            }
            elseif ($currentOrder->payment_type == 'single_payment'){
                switch($error)
                {
                    case '00000':
                        $order_status = 'closed';
                        $last_numero_fiscal = wplazare_orders::getLastNumeroFiscal(date('Y'));
                        $last_id = ($last_numero_fiscal % 1000000) +1;

                        $orderMoreInformations['order_reference'] = date('Y').substr_replace("000000",$last_id, -strlen($last_id));

                        /*	Get the orders informations to update with the lazare return infos	*/
                        $currentOrder = wplazare_orders::getElement($reference, "'valid'", 'id');
                        $currentOrder->order_reference = $orderMoreInformations['order_reference'];
                        $amount = $currentOrder->order_amount / 100;
                        $outputMessage = sprintf(__('Votre paiement de %s a bien &eacute;t&eacute; effectu&eacute;', 'wplazare'), $amount . '&nbsp;' . $currencyIconList[$currentOrder->order_currency]);
                        if($currentOrder->user_recu > 0) wplazare_orders::sendRecu($currentOrder);
                        break;
                    case '00001':
                    case '00003':
                    case '00004':
                    case '00006':
                    case '00008':
                    case '00009':
                    case '00010':
                    case '00011':
                    case '00015':
                    case '00016':
                    case '00021':
                    case '00029':
                    case '00030':
                    case '00031':
                    case '00032':
                    case '00033':
                        $order_status = 'error';
                        $outputMessage = __('Une erreur est survenue lors de votre paiement, pour plus d\'informations contactez nous en pr&eacute;cisant le code d\'erreur suivant: PaymentReturn#' . $reference . 'E' . $error . '', 'wplazare');
                        break;
                }

                $orderMoreInformations['order_status'] = $order_status;
            }
            wplazare_database::update($orderMoreInformations, $currentOrder->id, wplazare_orders::getDbTable());
        }
        else
        {
            $outputMessage = '';
        }

        return '<div class="paymentReturnResponse" >' . $outputMessage . '</div>';
    }

    function chargePaymentReturn()
    {
        $autorisation = isset($_REQUEST['autorisation']) ? wplazare_tools::varSanitizer($_REQUEST['autorisation']) : '';
        $transaction = isset($_REQUEST['transaction']) ? wplazare_tools::varSanitizer($_REQUEST['transaction']) : '';
        $formIdentifier = isset($_POST['formIdentifier']) ? wplazare_tools::varSanitizer($_POST['formIdentifier']) : '';
        $error = isset($_REQUEST['error']) ? wplazare_tools::varSanitizer($_REQUEST['error']) : '';
        $outputMessage = '';

        if($formIdentifier != '')
        {
            $orderIdentifier = wplazare_orders::saveNewOrder($_POST);
            $currentOrder = wplazare_orders::getElement($orderIdentifier, "'valid'", 'id');
        }

        /*	Update the current order	*/
        $orderMoreInformations['last_update_date'] = date('Y-m-d H:i:s');
        $orderMoreInformations['order_autorisation'] = $autorisation;
        $orderMoreInformations['order_transaction'] = $transaction;
        $orderMoreInformations['order_error'] = $error;

        if( $currentOrder && $currentOrder->payment_type == 'multiple_payment' ){
            $return = $_REQUEST[wplazare_payment_form::getDbTable()];

            $orderMoreInformations['banque_code'] = wplazare_tools::varSanitizer($return['banque_code']);
            $orderMoreInformations['banque_code_guichet'] = wplazare_tools::varSanitizer($return['banque_code_guichet']);
            $orderMoreInformations['banque_code_numero_compte'] = wplazare_tools::varSanitizer($return['banque_code_numero_compte']);
            $orderMoreInformations['banque_code_cle_rib'] = wplazare_tools::varSanitizer($return['banque_code_cle_rib']);
            $orderMoreInformations['banque_iban'] = wplazare_tools::varSanitizer($return['banque_iban']);
            $orderMoreInformations['banque_nom'] = wplazare_tools::varSanitizer($return['banque_nom']);
            $orderMoreInformations['banque_adresse'] = wplazare_tools::varSanitizer($return['banque_adresse']);
            $orderMoreInformations['banque_code_postal'] = wplazare_tools::varSanitizer($return['banque_code_postal']);
            $orderMoreInformations['banque_ville'] = wplazare_tools::varSanitizer($return['banque_ville']);
            $orderMoreInformations['location_id'] = wplazare_tools::varSanitizer($return['location_id']);
            $orderMoreInformations['location_type_charge'] = wplazare_tools::varSanitizer($return['location_type_charge']);
            $orderMoreInformations['payment_recurrent_day_of_month'] = wplazare_tools::varSanitizer($return['payment_recurrent_day_of_month']);
            if(isset($_POST['valeur'] )){
                $orderMoreInformations['payment_amount'] = $_POST['valeur'] * 100;
                $orderMoreInformations['order_amount'] = $_POST['valeur'] * 100;
            }
            $location = wplazare_locations::getElement($orderMoreInformations['location_id']);
            $user = get_userdata($location->user);
            $currentOrder->user_email = $user->user_email;
            $currentOrder->user_firstname = ucfirst(get_user_meta($user->ID,'first_name',true));
            $currentOrder->user_lastname = ucfirst(get_user_meta($user->ID,'last_name',true));
            $current_appart = wplazare_apparts::getElement($location->appartement);
            $currentOrder->user_adress = $current_appart->adresse;
            $currentOrder->user_code_postal = $current_appart->code_postal;
            $currentOrder->user_ville = $current_appart->ville;
            $current_association = wplazare_associations::getElement($current_appart->association);
            $currentOrder->user_association = $current_association->nom;

            $orderMoreInformations['user_email'] = $currentOrder->user_email;
            $orderMoreInformations['user_firstname'] = $currentOrder->user_firstname;
            $orderMoreInformations['user_lastname'] = $currentOrder->user_lastname;
            $orderMoreInformations['user_adress'] = $currentOrder->user_adress;
            $orderMoreInformations['user_code_postal'] = $currentOrder->user_code_postal;
            $orderMoreInformations['user_ville'] = $currentOrder->user_ville;

            if(wplazare_tools::check_iban($orderMoreInformations['banque_iban'])){
                $outputMessage = wplazare_orders::buildPrelevementReturn($currentOrder,$orderMoreInformations);
                wplazare_database::update($orderMoreInformations, $currentOrder->id, wplazare_orders::getDbTable());
            }
            else{
                $outputMessage = wplazare_payment_form::chooseLocation($orderMoreInformations,__('L\'IBAN n\'est pas valide. Veuillez vérifier votre saisie.', 'wplazare'));
            }
        }

        return '<div class="paymentReturnResponse" >' . $outputMessage . '</div>';
    }

    function sendRecu($currentOrder){

        $headers = "From: ".get_option('blogname')." <".get_option('admin_email').">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";

        $message = "Bonjour,<br /><br />";
        $message .= "Vous trouverez ci-joint le recu fiscal correspondant au don de $currentOrder->user_firstname $currentOrder->user_lastname.<br />";
        $message .= "A envoyer au:<br />";
        $message .= "$currentOrder->user_adress<br />";
        $message .= "$currentOrder->user_code_postal $currentOrder->user_ville<br /><br />";
        $message .= "<br />L'&eacute;quipe de ".get_option('blogname');

        $subject = "Recu fiscal LAZARE";

        $template_name ="recu_fiscal";

        $pdfator = new wplazare_pdfator();

        $balises_replace = wplazare_orders::prepareBalisesReplace($currentOrder);

        $attachments = array( $pdfator->getPdf($template_name, $balises_replace) );
        $destinataire = get_option('admin_email');

        if($currentOrder->user_reception_recu == 'email'){
            $message = "Bonjour,<br /><br />";
            $message .= "Vous trouverez ci-joint le recu fiscal correspondant &agrave; votre dernier don.<br />";
            $message .= "<br />L'&eacute;quipe de ".get_option('blogname');
            $destinataire = $currentOrder->user_email;
        }

        if($destinataire != "" )
            wp_mail( $destinataire, $subject, $message, $headers, $attachments);

        $pdfator->clearTempPdf($template_name);
    }

    function prepareBalisesReplace($currentOrder){
        $paiement_type = '';
        switch ($currentOrder->payment_type){
            case 'single_payment' : $paiement_type = 'Carte bancaire';
                break;
            case 'multiple_payment' : $paiement_type = 'Pr&eacute;l&egrave;vement';
                break;
            case 'cheque_payment' : $paiement_type = 'Ch&egrave;que';
                break;
            case 'virement_payment' : $paiement_type = 'Virement';
                break;
        }

        $balises_replace = array(
            array( "balise" => "{YEARPLUSUN}", "new_text" => (date('Y')+1) ),
            array( "balise" => "{YEAR}", "new_text" => date('Y') ),
            array( "balise" => "{NUMERO_RECU}", "new_text" => $currentOrder->order_reference ),
            array( "balise" => "{PRENOM}", "new_text" => $currentOrder->user_firstname ),
            array( "balise" => "{NOM}", "new_text" => $currentOrder->user_lastname ),
            array( "balise" => "{ADRESSE}", "new_text" => $currentOrder->user_adress ),
            array( "balise" => "{CODE_POSTAL}", "new_text" => $currentOrder->user_code_postal ),
            array( "balise" => "{VILLE}", "new_text" => $currentOrder->user_ville ),
            array( "balise" => "{SOMME}", "new_text" => $currentOrder->order_amount / 100 ),
            array( "balise" => "{DATE_DON}", "new_text" => date('d/m/Y',strtotime($currentOrder->creation_date)) ),
            array( "balise" => "{DATE_RECU}", "new_text" => date('d/m/Y',current_time('timestamp')) ),
            array( "balise" => "{PATH}", "new_text" => plugins_url( '/wplazare/includes/librairies/html2pdf/templates/') ),
            array( "balise" => "{TYPE_PAIEMENT}", "new_text" => $paiement_type ),
            array( "balise" => "{DEDUCTION}", "new_text" => wplazare_orders::prepareDeduction($currentOrder) ),
            array( "balise" => "{ASSOCIATION}", "new_text" => strtoupper($currentOrder->user_association) ),
            array( "balise" => "{ADRESSE_ASSOCIATION}", "new_text" => wplazare_tools::getAdresseAssociation($currentOrder->user_association) )
        );
        return $balises_replace;
    }

    function prepareDeduction($currentOrder){
        $deduction_max = 521;
        $last_don = intval($currentOrder->order_amount / 100);

        $total_don = intval(wplazare_orders::getDonTotalFrom($currentOrder->user_firstname, $currentOrder->user_lastname, $currentOrder->last_update_date) / 100);

        $return = '';

        if($total_don < $deduction_max){
            $return = 'Votre don de <b>'.$last_don.' &euro;</b> est d&eacute;ductible &agrave; <b>75 %.</b><br/>';
            $cout_reel = round($last_don * 0.25,2);
        }
        else{
            if( ($total_don-$last_don) > $deduction_max){
                $return = 'Votre don de <b>'.$last_don.' &euro;</b> est d&eacute;ductible &agrave; <b>66 %.</b><br/>';
                $cout_reel = round($last_don * 0.34,2);
            }
            else{
                $part75 = $deduction_max - ($total_don-$last_don);
                $part66 = $last_don - $part75;
                $return .= 'De votre don, <b>'.$part75.' &euro;</b> sont d&eacute;ductibles &agrave; <b>75 %</b>.<br/>';
                $return .= 'Les <b>'.$part66.' &euro;</b> restants sont d&eacute;ductibles &agrave; <b>66 %</b>.<br/>';
                $cout_reel = round(($part75 * 0.25) + ($part66 * 0.34),2);
            }
        }
        $return .= '(le co&ucirc;t r&eacute;el de votre don est de '.$cout_reel.' &euro; )';
        return $return;
    }

    function prepareDeductionDonsMensuels($currentOrder,$somme){
        $deduction_max = 521;
        $last_don = intval($somme);

        $total_don = intval(wplazare_orders::getDonTotalFrom($currentOrder->user_firstname, $currentOrder->user_lastname, $currentOrder->last_update_date) / 100)
        + $last_don;

        $return = '';

        if($total_don < $deduction_max){
            $return = 'Votre don de <b>'.$last_don.' &euro;</b> est d&eacute;ductible &agrave; <b>75 %.</b><br/>';
            $cout_reel = round($last_don * 0.25,2);
        }
        else{
            if( ($total_don-$last_don) > $deduction_max){
                $return = 'Votre don de <b>'.$last_don.' &euro;</b> est d&eacute;ductible &agrave; <b>66 %.</b><br/>';
                $cout_reel = round($last_don * 0.34,2);
            }
            else{
                $part75 = $deduction_max - ($total_don-$last_don);
                $part66 = $last_don - $part75;
                $return .= 'De votre don, <b>'.$part75.' &euro;</b> sont d&eacute;ductibles &agrave; <b>75 %</b>.<br/>';
                $return .= 'Les <b>'.$part66.' &euro;</b> restants sont d&eacute;ductibles &agrave; <b>66 %</b>.<br/>';
                $cout_reel = round(($part75 * 0.25) + ($part66 * 0.34),2);
            }
        }
        $return .= '(le co&ucirc;t r&eacute;el de votre don est de '.$cout_reel.' &euro; )';
        return $return;
    }

    function getDonTotalFrom($firstname, $lastname, $last_update_date){
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT SUM(WPORDERS.order_amount) AS somme ".
            "FROM ".wplazare_orders::getDbTable()." AS WPORDERS WHERE WPORDERS.user_firstname LIKE '$firstname' ".
            "AND WPORDERS.user_lastname LIKE '$lastname' ".
            "AND YEAR(WPORDERS.last_update_date) = '".date('Y',strtotime($last_update_date))."' ".
            "AND WPORDERS.last_update_date <= '$last_update_date' ".
            "AND WPORDERS.order_status LIKE 'closed' ".
            "AND WPORDERS.payment_type != 'multiple_payment' "
        );

        $res = $wpdb->get_row($query);
        $somme = $res->somme;
        return $somme;
    }

    function getDons($user_email,$order_status = ""){
        global $wpdb;

        $order_status_filter = '';
        if($order_status != ''){
            $order_status_filter = " AND WPORDERS.order_status LIKE '" . $order_status . "' ";
        }

        $query = $wpdb->prepare(
            "SELECT * ".
            "FROM ".wplazare_orders::getDbTable()." AS WPORDERS WHERE WPORDERS.user_email LIKE '$user_email' ". $order_status_filter .
            "ORDER BY WPORDERS.last_update_date"
        );

        return $wpdb->get_results($query);
    }

    function getDonsByMonth($year, $month, $order_status = ""){
        global $wpdb;

        $order_status_filter = '';
        if($order_status != ''){
            $order_status_filter = " AND WPORDERS.order_status LIKE '" . $order_status . "' ";
        }

        $query = $wpdb->prepare(
            "SELECT * ".
            "FROM ".wplazare_orders::getDbTable()." AS WPORDERS WHERE ".
            "YEAR(WPORDERS.creation_date) ='$year' ".
            "AND MONTH(WPORDERS.creation_date) ='$month' ".
            $order_status_filter .
            " ORDER BY WPORDERS.last_update_date"
        );

        return $wpdb->get_results($query);
    }

    function buildChequeReturn($currentOrder){
        $return = "<p>Nous avons bien enregistr&eacute; votre promesse de don en faveur de l'association $currentOrder->user_association.</p>";
        $return .= "<h3>Mes coordonn&eacute;es</h3>";
        $return .= $currentOrder->user_firstname.' '.$currentOrder->user_lastname.'<br/>';
        $return .= $currentOrder->user_email.'<br/>';
        $return .= $currentOrder->user_adress.'<br/>';
        $return .= $currentOrder->user_code_postal.' '.$currentOrder->user_ville.'<br/>';
        $return .= "<h3>Mon don</h3>";
        $return .= ''.($currentOrder->payment_amount/100).' euros par ch&egrave;que<br/>';
        $return .= "<h3>Libell&eacute; du ch&egrave;que</h3>";
        $return .= 'Association '.$currentOrder->user_association.'<br/>';
        $return .= "<h3>J'envoie mon ch&egrave;que &agrave; l'adresse suivante</h3>";
        $return .= "<p>Lazare - 1 rue du Pl&acirc;tre - 75004 Paris</p>";

        return $return;
    }

    function buildPrelevementReturn($currentOrder,$orderMoreInformations){
        $paiement_don = "don";
        if(array_key_exists('location_type_charge', $orderMoreInformations) && $orderMoreInformations['location_type_charge'] != ''){
            $paiement_don = "paiement de ".$orderMoreInformations['location_type_charge'];
        }

        $return = "<p>Nous avons bien enregistr&eacute; votre $paiement_don en faveur de l'association $currentOrder->user_association.</p>";
        $return .= "<h3>Mes coordonn&eacute;es</h3>";
        $return .= wplazare_tools::varSanitizer($currentOrder->user_firstname).' '.wplazare_tools::varSanitizer($currentOrder->user_lastname).'<br/>';
        $return .= $currentOrder->user_email.'<br/>';
        $return .= wplazare_tools::varSanitizer($currentOrder->user_adress).'<br/>';
        $return .= $currentOrder->user_code_postal.' '.$currentOrder->user_ville.'<br/>';
        $return .= "<h3>Mon $paiement_don</h3>";
        $return .= ''.($currentOrder->payment_amount/100).' euros par mois par pr&eacute;l&egrave;vement.<br/>';

        $currentOrderFull = new stdClass();
        $currentOrderFull->user_lastname = $currentOrder->user_lastname;
        $currentOrderFull->user_firstname = $currentOrder->user_firstname;
        $currentOrderFull->user_adress = $currentOrder->user_adress;
        $currentOrderFull->user_ville = $currentOrder->user_ville;
        $currentOrderFull->user_code_postal = $currentOrder->user_code_postal;
        $currentOrderFull->banque_nom = $orderMoreInformations['banque_nom'];
        $currentOrderFull->banque_adresse = $orderMoreInformations['banque_adresse'];
        $currentOrderFull->banque_code_postal = $orderMoreInformations['banque_code_postal'];
        $currentOrderFull->banque_ville = $orderMoreInformations['banque_ville'];
        $currentOrderFull->banque_code = $orderMoreInformations['banque_code'];
        $currentOrderFull->banque_code_guichet = $orderMoreInformations['banque_code_guichet'];
        $currentOrderFull->banque_code_numero_compte = $orderMoreInformations['banque_code_numero_compte'];
        $currentOrderFull->banque_iban = $orderMoreInformations['banque_iban'];
        $currentOrderFull->banque_code_cle_rib = $orderMoreInformations['banque_code_cle_rib'];


        $file_path = wplazare_orders::buildAutorisationPrelevement($currentOrderFull);

        if($file_path != ''){
            $return .= '<div class="popup_block open_popup">';
            $return .= '<a href="#" class="close"><img src="'.get_theme_root_uri().'/lazare/images/close_pop.png" class="btn_close" title="Fermer la fenêtre" alt="Fermer"></a>';
            $return .= '<h3>Autorisation</h3>';
            $return .= '<div>'.'L\'autorisation de prélèvement a bien &eacute;t&eacute; g&eacute;n&eacute;r&eacute;e. Veuillez la t&eacute;l&eacute;charger, l\'imprimer et l\'envoyer à votre banque.<br/>'.
                '<a class="pdf" href="'.plugins_url( '/wplazare/includes/librairies/html2pdf/output/'. basename($file_path)).'">T&eacute;l&eacute;charger l\'autorisation</a>'.
                '</div>';
            $return .= '</div>';

            /* send mail to admin */
            $headers = "From: ".get_option('blogname')." <".get_option('admin_email').">\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            //$headers .= "Content-type: multipart/mixed; charset=UTF-8\r\n";
            $headers .= "Content-type: text/html; charset=UTF-8\r\n";

            $message = "Bonjour,<br /><br />";
            $message .= "<p>Nous avons enregistr&eacute; un $paiement_don par pr&eacute;l&egrave;vement de ".($currentOrder->payment_amount/100)."&euro; par $currentOrder->user_firstname $currentOrder->user_lastname en faveur de l'association $currentOrder->user_association.</p><br/>";
            $message .= "<br />L'&eacute;quipe de ".get_option('blogname');

            $subject = "Prélèvement: ".$paiement_don;

            $attachments = array( $file_path );
            // envoi au locataire ou au responsable
            //$destinataire_email = "admin@maisonlazare.com";
            $destinataire_email = "alienorrousseau@maisonlazare.com";
            wp_mail( $destinataire_email, $subject, $message, $headers, $attachments);
        }
        else{
            $return .= '<p class="error erreur">Une erreur s\'est produite lors de la g&eacute;n&eacute;ration de l\'autorisation de pr&eacute;l&egrave;vement. Veuillez contacter l\'association pour l\'obtenir. Merci.</p>';
        }


        return $return;
    }

    function buildAutorisationPrelevement($currentOrderFull){
        $template_name ="autorisation";
        $pdfator = new wplazare_pdfator();

        $balises_replace = array(
            array( "balise" => "{NOM_DEBITEUR}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->user_lastname)),
            array( "balise" => "{PRENOM_DEBITEUR}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->user_firstname)),
            array( "balise" => "{ADRESSE_DEBITEUR}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->user_adress)),
            array( "balise" => "{CODE_POSTAL_DEBITEUR}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->user_code_postal)),
            array( "balise" => "{VILLE_DEBITEUR}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->user_ville)),
            array( "balise" => "{NOM_BANQUE}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->banque_nom)),
            array( "balise" => "{ADRESSE_BANQUE}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->banque_adresse)),
            array( "balise" => "{CODE_POSTAL_BANQUE}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->banque_code_postal)),
            array( "balise" => "{VILLE_BANQUE}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->banque_ville)),
            array( "balise" => "{CODE_ETABLISSEMENT}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->banque_code)),
            array( "balise" => "{CODE_GUICHET}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->banque_code_guichet)),
            array( "balise" => "{NUMERO_COMPTE}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->banque_code_numero_compte)),
            array( "balise" => "{IBAN}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->banque_iban)),
            array( "balise" => "{CLE}", "new_text" => wplazare_tools::varSanitizer($currentOrderFull->banque_code_cle_rib))
        );

        $file_path = $pdfator->getPdf($template_name, $balises_replace);
        return($file_path);
    }

    function getAutorisationMessage($reference)
    {
        $currentOrderFull = wplazare_orders::getElement($reference, "'valid'", 'id');
        $file_path = wplazare_orders::buildAutorisationPrelevement($currentOrderFull);
        if(file_exists($file_path))
            $message =  '<div>'
                . 'Le fichier a bien &eacute;t&eacute; g&eacute;n&eacute;r&eacute;:<br/>'
                . '<a href="' . plugins_url( '/wplazare/includes/librairies/html2pdf/output/'. basename($file_path))
                . '"><img src="' . get_theme_root_uri()
                . '/lazare/images/download.jpg" alt="T&eacute;l&eacutecharger" /></a>'
                . '</div>';
        else
            $message = 'error';

        return $message;
    }

    function getAnnees()
    {
        global $wpdb;

        $query = $wpdb->prepare(
            "SELECT MIN(YEAR(".wplazare_orders::getDbTable().".creation_date)) AS first_year ".
            "FROM ".wplazare_orders::getDbTable()
        );

        // ajoute l'ann?e actuelle si elle n'est pas dedans
        $res = $wpdb->get_row($query);
        $year = $res->first_year;
        $current_year = date("Y", (current_time("timestamp", 0)));
        $elements = array();
        do {
            $elements[] = $year;
            $year++;
        } while ($year <= $current_year);

        return $elements;
    }


    function getMois()
    {
        $elements = array ( array( 'value' => 1, 'name' => 'Janvier'),
            array( 'value' => 2, 'name' => 'F&eacute;vrier'),
            array( 'value' => 3, 'name' => 'Mars'),
            array( 'value' => 4, 'name' => 'Avril'),
            array( 'value' => 5, 'name' => 'Mai'),
            array( 'value' => 6, 'name' => 'Juin'),
            array( 'value' => 7, 'name' => 'Juillet'),
            array( 'value' => 8, 'name' => 'Aout'),
            array( 'value' => 9, 'name' => 'Septembre'),
            array( 'value' => 10, 'name' => 'Octobre'),
            array( 'value' => 11, 'name' => 'Novembre'),
            array( 'value' => 12, 'name' => 'D&eacute;cembre')
        );
        return $elements;
    }

    function newOrdersCount($date, $filters=array())
    {
        global $wpdb;

        $where_clause = "";
        foreach( $filters as $key=>$value )
        {
            $where_clause .= " AND $key = '$value'";
        }

        $query = $wpdb->prepare(
            "SELECT * ".
            "FROM ".wplazare_orders::getDbTable(). " WHERE creation_date > '$date' AND order_status='closed'".$where_clause
        );

        // ajoute l'année actuelle si elle n'est pas dedans
        $res = $wpdb->get_results($query);
        return count($res);
    }

    public static function getLastNumeroFiscal($date)
    {
        global $wpdb;
        $query = $wpdb->prepare(
            "SELECT MAX(order_reference) AS last_id ".
            "FROM ".wplazare_orders::getDbTable()." AS WPORDERS WHERE ".
            "YEAR(WPORDERS.last_update_date) = '".$date."' ".
            "AND WPORDERS.order_status LIKE 'closed' "
        );

        $res = $wpdb->get_row($query);
        if($res->last_id)
            return $res->last_id;
        else
            return 0;
    }
}