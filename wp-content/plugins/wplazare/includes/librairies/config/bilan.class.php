<?php
/**
 * Plugin options
 * 
 * Allows to manage the different option for the plugin
 * @author Eoxia <dev@eoxia.com>
 * @version 1.0
 * @package wp-lazare
 * @subpackage librairies
 */

/**
 * Allows to manage the different option for the plugin
 * @package wp-lazare
 * @subpackage librairies
 */
class wplazare_bilan
{		
	//-----------------------------------------------	
	function wplazare_bilan_notif_function()
	{
		$return = 0;

        $day_now = date("d", (current_time("timestamp", 0)));
        if( $day_now != "10" )
            return($return);
		//--------------------------------------------------------------------------
		// Générer le bilan
		//--------------------------------------------------------------------------

        $month_now = date("n", (current_time("timestamp", 0)));
        $year_now = date("Y", (current_time("timestamp", 0)));
        $the_bilan = wplazare_bilan::getBilan($year_now, $month_now);

        $the_bilan_message = "";
        foreach($the_bilan as $sub_bilan)
        {
            $the_bilan_message .= "L'association ".$sub_bilan->user_association." a re&ccedil;u ".($sub_bilan->subTotal/100)." euros par ".__($sub_bilan->payment_type, 'wplazare');
            $the_bilan_message .= "<br />";
        }

        $assos = wplazare_associations::getElement('','');
        foreach($assos AS $asso)
        {
            //$responsable_user_data = get_userdata( $asso->responsable );
            //$responsable_email =  $responsable_user_data->user_email;
            $responsable_email = "admin@maisonlazare.com";                   //DEBUG

            $headers = "From: ".get_option('blogname')." <".get_option('admin_email').">\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-type: text/html; charset=utf-8\r\n";

            $message = "Bilan mensuel :<br />";
            $message .= $the_bilan_message;
            $message .= "<br />L'&eacute;quipe de ".get_option('blogname');

            $date_now = date("d-m-Y", (current_time("timestamp", 0)) );
            $subject = "Bilan mensuel du ".$date_now;

            wp_mail( $responsable_email, $subject, $message, $headers);
        }
        $return = 1;

		return $return;
	}
	//-----------------------------------------------

    function getBilan($year, $month)
    {
        global $wpdb;

        $month_max = $month;
        $year_max = $year_min = $year;
        $month_min = $month_max-1;
        if($month_min == 0)
        {
            $month_min = 12;
            $year_min = $year_max-1;
        }
        $date_min = $year_min."-".$month_min."-10";
        $date_max = $year_max."-".$month_max."-10";

        $sql = "SELECT SUM(O.order_amount) AS subTotal, O.user_association, O.payment_type, O.last_update_date  AS Date
                FROM ".wplazare_orders::getDbTable()." AS O
                WHERE O.status = 'valid' AND
                    ( O.payment_type = 'multiple_payment' OR
                        ( O.payment_type != 'multiple_payment' AND O.order_status = 'closed' AND O.last_update_date BETWEEN '".$date_min."' AND '".$date_max."' )
                    )
                GROUP BY O.user_association, O.payment_type
                ORDER BY O.user_association, O.payment_type, subTotal" ;

        $query = $wpdb->prepare( $sql );
        $data = $wpdb->get_results($query);

        return $data;
    }
}

add_action('bilan_notif_hook', array('wplazare_bilan', 'wplazare_bilan_notif_function'));