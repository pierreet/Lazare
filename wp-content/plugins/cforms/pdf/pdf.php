<?php
/**
* 	Create a pdf from an html template
* 
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/
	ob_start();
	
define( 'WP_USE_THEMES', false );  
require('../../../../wp-blog-header.php');

$cformsSettings				= get_option('cforms_settings');
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

	
	$html_template = $_GET['tpl'].".html";
		if( @file_exists($html_template) )
		{
			$content = file_get_contents($html_template);
		
			$results = $wpdb->get_results('SELECT * FROM '.$wpdb->cformssubmissions.' s, '.$wpdb->cformsdata.' d WHERE s.id = '.str_replace('xP312qNW', '', substr($_GET['sub_id'], 8)).' AND s.id=d.sub_id');
			if($results){
				$balises_replace = array(
					array( "balise" => "{DATE}", "new_text" => date('d/m/Y') ),
					array( "balise" => "{NOM}", "new_text" => $results[0]->last_name ),
					array( "balise" => "{PRENOM}", "new_text" => $results[0]->first_name ),
					array( "balise" => "{NUM}", "new_text" => str_replace('xP312qNW', '', substr($_GET['sub_id'], 8)) ),
					array( "balise" => "{PATH}", "new_text" => plugins_url( '/wplazare/includes/librairies/html2pdf/templates/') )
				);
				foreach($results as $result){	
					$name = $result->field_name==''?'':stripslashes($result->field_name);
					$val  = $result->field_val ==''?'':stripslashes($result->field_val);
			
					if(preg_match("#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#", trim($val)) && (strpos($name,'naissance')!==false || strpos($name,'Né')!==false )) //date naiss
						$balises_replace[] = array( "balise" => "{DATE_NAISS}", "new_text" => $val);					
						
					if($name == 'IBAN') //IBAN
						$balises_replace[] = array( "balise" => "{IBAN}", "new_text" => $val);
					if($name == 'BIC') //BIC
						$balises_replace[] = array( "balise" => "{BIC}", "new_text" => $val);
						
					if($name == 'Votre lieu de naissance') //lieu naissance
						$balises_replace[] = array( "balise" => "{LIEU_NAISS}", "new_text" => $val);
						
					//adresse actuelle
					if($name == 'Votre adresse personnelle actuelle')
						$balises_replace[] = array( "balise" => "{ADRESSE}", "new_text" => $val);
					if($name == 'Votre code postal')
						$balises_replace[] = array( "balise" => "{ADRESSE_CP}", "new_text" => $val);
					if($name == 'Votre ville')
						$balises_replace[] = array( "balise" => "{ADRESSE_VILLE}", "new_text" => $val);
						
					//maison
					global $oUserAccessManager;
					$aUserGroupsForObject = $oUserAccessManager->getAccessHandler()->getUserGroups(null);
					foreach($aUserGroupsForObject as $group){
						if($group->getGroupName()==$val){
							$balises_replace[] = array( "balise" => "{MAISON}", "new_text" => $val);
							$balises_replace[] = array( "balise" => "{ADRESSE_MAISON}", "new_text" => nl2br($group->getGroupAddress()));
						}
					}
					
				}
							
				if( is_array($balises_replace) )
				{
					foreach($balises_replace as $balise_replace)
					{
						$content = str_replace ( $balise_replace["balise"] , $balise_replace["new_text"] , $content );	
					}
				}
			}			
			else
				$content = '<div>URL non valide</div>';
		}else
			$content = "Erreur: le fichier demandé n'existe pas.";	
	
	require_once(dirname(__FILE__).'/../../wplazare/includes/librairies/html2pdf//html2pdf.class.php');
    $html2pdf = new HTML2PDF('P','A4','fr');
	$html2pdf->WriteHTML($content);
    
	ob_end_clean();
	$html2pdf->Output();
?>