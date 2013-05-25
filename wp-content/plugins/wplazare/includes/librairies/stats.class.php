<?php
/**
* Show orders stats
* 
*	Show orders stats
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Show orders stats
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_stats
{
	/**
	*	Get the url listing slug of the current class
	*
	*	@return string The table of the class
	*/
	function getCurrentPageCode()
	{
		return 'wplazare_stats';
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
		return WPLAZARE_URL_SLUG_STATS_LISTING;
	}
	/**
	*	Get the url edition slug of the current class
	*
	*	@return string The table of the class
	*/
	function getEditionSlug()
	{
		return WPLAZARE_URL_SLUG_STATS_EDITION;
	}
	
	/**
	*	Get the database table of the current class
	*
	*	@return string The table of the class
	*/
	function getDbTable()
	{
		return WPLAZARE_DBT_STATS;
	}

	/**
	*	Define the title of the page 
	*
	*	@return string $title The title of the page looking at the environnement
	*/
	function pageTitle()
	{
		return 'Statistiques';
	}

	/**
	*	Define the different message and action after an action is send through the element interface
	*/
	function elementAction()
	{

	}
	/**
	*	Return the list page content, containing the table that present the item list
	*
	*	@return string $listItemOutput The html code that output the item list
	*/
	function elementList()
	{
		$annee = isset($_REQUEST['annee']) ? wplazare_tools::varSanitizer($_REQUEST['annee']): date('Y');
		
		$options_annee = '';
		$res = wplazare_stats::getAnneeForSelect();
		foreach($res as $result){
			$selected = "";
			if(isset($_REQUEST['annee'])) 
				if($_REQUEST['annee'] == $result->annee)
					$selected = "selected='selected'";
			$options_annee .= '<option '.$selected.'>'.stripslashes($result->annee).'</option>';
		}
		
		$selectForm='<form  method="post" enctype="multipart/form-data">
		<select name="annee" id="annee">'.$options_annee.'</select>	
		<input type="submit" class="button-primary" value="recherche"/></form>
		';
		
		$listItemOutput = "";
		$data = wplazare_stats::getStatsByMonth($annee);
		
		//print_r($data);
		$listItemOutput .= '
		<table>
			<tbody>
				<tr><th>Année</th><th>Mois</th><th>SOMME</th><th>MAX</th><th>MIN</th><th>MOYENNE</th></tr>
		';
		foreach($data as $stat_mois)
		{
			
			$max_id_link = admin_url('admin.php?page=wplazare_orders&amp;action=view&amp;id=' . wplazare_stats::getExtremeOrderId('MAX',$annee, $stat_mois->mois));
			$min_id_link = admin_url('admin.php?page=wplazare_orders&amp;action=view&amp;id=' . wplazare_stats::getExtremeOrderId('MIN',$annee, $stat_mois->mois));
			
			$listItemOutput .= '<tr>'.
				'<td>'.$stat_mois->annee.'</td>'.
				'<td>'.$stat_mois->mois.'</td>'.
				'<td>'.(($stat_mois->sum_dons /100)." &euro;").'</td>'.
				'<td><a href="' . $max_id_link . '">'.(($stat_mois->max_dons /100)." &euro;").'</a></td>'.
				'<td><a href="' . $min_id_link . '">'.(($stat_mois->min_dons /100)." &euro;").'</a></td>'.
				'<td>'.number_format(($stat_mois->avg_dons /100),2)." &euro;".'</td>'.
			'</tr>';
		}
		$listItemOutput .= '
			</tbody>
		</table>
		';
		
		return $selectForm.$listItemOutput;
	}
	
	function elementEdition($itemToEdit = '')
	{
		return "";
	}
	
	/**
	*	Return the different button to save the item currently being added or edited
	*
	*	@return string $currentPageButton The html output code with the different button to add to the interface
	*/
	function getPageFormButton()
	{
		return "";
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
	}
	
	function getStatsByMonth($annee='')
	{
		global $wpdb;
		
		$elementStatus = "'valid', 'moderated'";
		
		$more_query = '';
		if($annee != '') $more_query = ' AND YEAR(ORDERS.creation_date) = '.$annee;
		
		$query = $wpdb->prepare( 'SELECT MONTH(ORDERS.creation_date) AS mois, '.
										'YEAR(ORDERS.creation_date) AS annee, '.
										'SUM(ORDERS.payment_amount +0) AS sum_dons, '.
										'MAX(ORDERS.payment_amount +0) AS max_dons, '.
										'MIN(ORDERS.payment_amount +0) AS min_dons, '.
										'AVG(ORDERS.payment_amount +0) AS avg_dons, '.
										'DATE_FORMAT(ORDERS.creation_date , "%%m %%Y") AS date '.
									'FROM '.wplazare_orders::getDbTable().' AS ORDERS '.
									'WHERE ORDERS.status IN ('.$elementStatus.') '.$more_query.
									' AND ORDERS.order_status=\'closed\''.
									' GROUP BY date ORDER BY ORDERS.creation_date DESC'
		);

		$elements = $wpdb->get_results($query);
		
		return $elements;
	}
	
	/**
	 * 
	 * getAnneeForSelect() retourne les différentes années où il y aeu des dons
	 */
	function getAnneeForSelect(){
		global $wpdb;
		
		$query = $wpdb->prepare( 'SELECT DISTINCT YEAR(ORDERS.creation_date) AS annee '.
									'FROM '.wplazare_orders::getDbTable().' AS ORDERS '.
									'ORDER BY annee DESC'
		);

		$elements = $wpdb->get_results($query);
		
		return $elements;
	}
	
	function getExtremeOrderId($extreme,$annee, $mois){
		global $wpdb;
		
		$query = $wpdb->prepare( 'SELECT ORDERS.* '.
			'FROM '.wplazare_orders::getDbTable().' AS ORDERS '.
			'WHERE payment_amount = '.
				'(SELECT '.$extreme.'(ORDERS2.payment_amount)'.
				'FROM '.wplazare_orders::getDbTable().' AS ORDERS2 '.
				'WHERE YEAR(ORDERS2.creation_date) ='.$annee.' '. 
				'AND MONTH(ORDERS2.creation_date) ='.$mois.') '.
			'AND YEAR(ORDERS.creation_date) ='.$annee.' '. 
			'AND MONTH(ORDERS.creation_date) ='.$mois.' '.
			' AND ORDERS.order_status=\'closed\''.
			'ORDER BY ORDERS.creation_date DESC'
		);

		$elements = $wpdb->get_results($query);
		if(count($elements) > 0)
			return $elements[0]->id;
		return '';
	}

}