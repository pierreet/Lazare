<?php



### supporting WP2.6 wp-load & custom wp-content / plugin dir
### check if called from cforms-database.php
if ( !defined('ABSPATH') ){
	if ( file_exists('../../abspath.php') )
	    include_once('../../abspath.php');
	else
	    $abspath='../../../../../';

	if ( file_exists( $abspath . 'wp-load.php') )
	    require_once( $abspath . 'wp-load.php' );
	else
	    require_once( $abspath . 'wp-config.php' );
}

if( !current_user_can('track_cforms') )
	wp_die("access restricted.");

	include_once('../../lib_aux.php');
	
### mini firewall

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### new global settings container, will eventually be the only one!
$cformsSettings = get_option('cforms_settings');

$plugindir   = $cformsSettings['global']['plugindir'];

### get form names
for ($i=1; $i <= $cformsSettings['global']['cforms_formcount']; $i++){
	$n = ( $i==1 )?'':$i;
	$fnames[$i]=stripslashes($cformsSettings['form'.$n]['cforms'.$n.'_fname']);
}

$showIDs = $_POST['showids'];
$sortBy = ($_POST['sortby']<>'')?$_POST['sortby']:'sub_id';
$sortOrder = ($_POST['sortorder']<>'')?substr($_POST['sortorder'],1):'desc';

$qtype = $_POST['qtype'];
$query = $_POST['query'];

### get form id from name
$query = str_replace('*','',$query);
$form_ids = false;
if ( $qtype == 'form_id' && $query <> '' ){

	$forms = $cformsSettings['global']['cforms_formcount'];

	for ($i=0;$i<$forms;$i++) {
		$no = ($i==0)?'':($i+1);

		if ( preg_match( '/'.$query.'/i', $cformsSettings['form'.$no]['cforms'.$no.'_fname'] ) ){
        	$form_ids = $form_ids . "'$no',";
		}
	}
	$querystr = ( !$form_ids )?'$%&/':' form_id IN ('.substr($form_ids,0,-1).')';
}else{
	$querystr = '%'.$query.'%';
}


if ( $form_ids )
	$where = "AND $querystr";
elseif ( $query<>'' )
	$where = "AND $qtype LIKE '$querystr'";
else
	$where = '';

if ($showIDs<>'') {

	if ( $showIDs<>'all' )
		$in_list = 'AND sub_id in ('.substr($showIDs,0,-1).')';
	else
		$in_list = '';

	$sql="SELECT *, form_id, ip FROM {$wpdb->cformsdata},{$wpdb->cformssubmissions} WHERE sub_id=id $in_list $where ORDER BY $sortBy $sortOrder, f_id";
	$entries = $wpdb->get_results($sql);
	?>

	<div id="top">
	<?php if ($entries) :

		$sub_id='';
		$track = array();
		$dates_rdv = '';
		$ged = false;
		$miss_ged = true;
		
		foreach ($entries as $entry){

			if( $sub_id<>$entry->sub_id ){

				if( $sub_id<>'' )
					echo '</div>';

				$sub_id = $entry->sub_id;
			
				$date = mysql2date(get_option('date_format'), $entry->sub_date);
	            $time = mysql2date(get_option('time_format'), $entry->sub_date);

				echo '<div class="showform" id="entry'.$entry->sub_id.'">'.
					 '<table class="dataheader"><tr><td>'.__('Détails:','cforms').' </td><td class="b"></td><td class="e">(ID:' . $entry->sub_id . ')</td><td class="d">' . $time.' &nbsp; '.$date. '</td>' .
					 '<td class="s">&nbsp;</td><td><a href="#" class="xdatabutton allbuttons deleteall" type="submit" id="xbutton'.$entry->sub_id.'">'.__('Supprimer cette entrée', 'cforms').'</a></td>' .
					 '<td><a class="cdatabutton" type="submit" id="cbutton'.$entry->sub_id.'" title="'.__('Fermer', 'cforms').'" value=""></a></td>' .
                     "</tr></table>\n";
			}

			$name = $entry->field_name==''?'':stripslashes($entry->field_name);
			$val  = $entry->field_val ==''?'':stripslashes($entry->field_val);
			
			$track[$name] = $val;
			
			if (strpos($name,'[*')!==false) {  // attachments?

					preg_match('/.*\[\*(.*)\]$/i',$name,$r);
					$no   = $r[1]==''?$entry->form_id:($r[1]==1?'':$r[1]);

					$temp = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'])) );
					$fileuploaddir = $temp[0];
					$fileuploaddirurl = $temp[1];

					$subID = ($cformsSettings['form'.$no]['cforms'.$no.'_noid'])?'':$entry->sub_id.'-';

					if ( $fileuploaddirurl=='' )
	                    $fileurl = $cformsSettings['global']['cforms_root'].substr($fileuploaddir,strpos($fileuploaddir,$cformsSettings['global']['plugindir'])+strlen($cformsSettings['global']['plugindir']),strlen($fileuploaddir));
					else
	                    $fileurl = $fileuploaddirurl;

                    $fileurl .= '/'.$subID.strip_tags($val);

					echo '<div class="showformfield meta"><div class="L">';
					echo substr($name, 0,strpos($name,'[*'));
					if ( $entry->field_val == '' )
						echo 	'</div><div class="R">' . __('-','cforms') . '</div></div>' . "\n";
					else
						echo 	'</div><div class="R">' . '<a href="' . $fileurl . '">' . str_replace("\n","<br />", strip_tags($val) ) . '</a>' . '</div></div>' . "\n";

			}
			elseif ($name=='Réponse' || $name=='Rendez-vous fixé le' || $name=='Désistement'){
				$rep = ($name=='Réponse' || $rep);
				$desist = ($name=='Désistement' || $desist);
				$rdv = ($name=='Rendez-vous fixé le' || $rdv);
			
				echo '<div class="showformfield"><div class="L">' . $name . '</div>' .
							'<div id="'.$entry->f_id.'" class="R " >' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";
			}
			elseif ($name=='page') {  // special field: page

					// echo '<div class="showformfield meta"><div class="L">';
					// _e('Soumis via:', 'cforms');
					// echo 	'</div><div class="R">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";
			
					// echo '<div class="showformfield meta"><div class="L">';
					// _e('Adresse IP:', 'cforms');
					// echo 	'</div><div class="R"><a href="http://geomaplookup.net/?ip='.$entry->ip.'" title="'.__('IP Lookup', 'cforms').'">'.$entry->ip.'</a></div></div>' . "\n";
					
					echo '<div class="showformfield meta"><div class="L">';
					_e('Note:', 'cforms');
					echo 	'</div><div id="note'.$sub_id.'" class="R editable">'.$entry->note.'</div></div>' . "\n";
					

			} elseif ( strpos($name,'Fieldset')!==false ) {

					if ( strpos($name,'FieldsetEnd')===false )
                    	echo '<div class="showformfield tfieldset"><div class="L">&nbsp;</div><div class="R">' . strip_tags($val)  . '</div></div>' . "\n";

			} else {

					echo '<div class="showformfield"><div class="L">' . $name . '</div>';
					
					if($val !='true' && $val !='false')
						echo '<div id="'.$entry->f_id.'" class="R editable" title="'.__('modifier ce champ', 'cforms').'">' . str_replace("\n","<br />", strip_tags($val) ) . '</div></div>' . "\n";
					else{
						echo '<div id="'.$entry->f_id.'" class="R checkable" title="'.__('modifier ce champ', 'cforms').'"><input class="ged" type="checkbox" '.(($val=='true')?'checked="checked"':'').' name="'.$name.'" state="'.$val.'" onclick="var that = this;var val = (jQuery(that).attr(\'state\')==\'true\')?\'false\':\'true\';jQuery.post(jQuery(\'#geturl\').attr(\'title\')+\'lib_database_savedata.php\', {element_id: \''.$entry->f_id.'\', update_value: val}, function(data){jQuery(that).attr(\'state\', val);} );" /></div></div>' . "\n";
						$miss_ged = ($val=='true' && $miss_ged);
						$ged = true;
					}
			}
			
			if(preg_match("#^[0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{2}:[0-9]{2}$#", trim($val))) //date de rdv
				$dates_rdv .= $val.',';

		}
		
/* 	 echo '<pre>';
		//print_r($track);
			echo '00'.$rdv.'/'.$ged.'00'.$rep.'00';
		echo '</pre>';  */
		
		$e = '&nbsp;&nbsp;&nbsp;&nbsp';
		
		echo '<br />'.$e;
		
		
		$msg_accept = urlencode(check_cust_vars(stripslashes(htmlspecialchars($cformsSettings['global']['cforms_register_accept'])), $track, $entry->sub_id));
		$msg_deny = urlencode(check_cust_vars(stripslashes(htmlspecialchars($cformsSettings['global']['cforms_register_deny'])), $track, $entry->sub_id));
		$msg_desist = urlencode(check_cust_vars(stripslashes(htmlspecialchars($cformsSettings['global']['cforms_register_desist'])), $track, $entry->sub_id));
		
		$msg_ged = urlencode(check_cust_vars(stripslashes(htmlspecialchars($cformsSettings['global']['cforms_register_ged'])), $track, $entry->sub_id));
		
		$dates_rdv = trim($dates_rdv, ',');
		$msg_rdv = urlencode(check_cust_vars(stripslashes(htmlspecialchars($cformsSettings['global']['cforms_register_rdv'])), $track, $entry->sub_id));
		
		$msg_accept_rdv = urlencode(check_cust_vars(stripslashes(htmlspecialchars($cformsSettings['global']['cforms_register_accept_rdv'])), $track, $entry->sub_id));
		$msg_deny_rdv = urlencode(check_cust_vars(stripslashes(htmlspecialchars($cformsSettings['global']['cforms_register_deny_rdv'])), $track, $entry->sub_id));
		
		echo '<table class="dataheader"><tr><td>';
		
		//action
		if(!$rdv && !$rep) //action rdv on ne met pas admin.php dans href car ajouter dans le javascript
			echo '<a href="?page='.$plugindir.'/cforms-mail.php&mail='.$entry->email.'&body='.$msg_rdv.'&obj='.$cformsSettings['global']['cforms_register_rdv_obj'].'" class="allbuttons rdv" id="rdvbutton'.$entry->sub_id.'" dates="'.$dates_rdv.'" type="submit" id="rdvbutton'.$entry->sub_id.'">'.__('Choisir le RDV', 'cforms').'</a>'.$e;

		/*if(!$ged && $rdv && !$rep){ //action post rdv*/
			echo '<a href="?page='.$plugindir.'/cforms-mail.php&mail='.$entry->email.'&body='.$msg_accept_rdv.'&obj='.$cformsSettings['global']['cforms_register_accept_rdv_obj'].'" class="allbuttons rdv_accept"  type="submit" id="rep_rdv_acccept_button'.$entry->sub_id.'">'.__('Réponse positive', 'cforms').'</a>'.$e;
			echo '<a href="?page='.$plugindir.'/cforms-mail.php&mail='.$entry->email.'&body='.$msg_deny_rdv.'&obj='.$cformsSettings['global']['cforms_register_deny_rdv_obj'].'" class="allbuttons rdv_deny deleteall"  type="submit" id="rep_rdv_deny_button'.$entry->sub_id.'">'.__('Réponse négative', 'cforms').'</a>'.$e;
			
		/*}*/
		if($ged && !$miss_ged && !$rep)//si il manque des documents
			echo '<a href="?page='.$plugindir.'/cforms-mail.php&mail='.$entry->email.'&body='.$msg_ged.'&obj='.$cformsSettings['global']['cforms_register_ged_obj'].'" class="allbuttons ged_submit"  type="submit" id="ged_button'.$entry->sub_id.'">'.__('Relance documents', 'cforms').'</a>'.$e;
	
	//mail				
	echo '<a href="admin.php?page='.$plugindir.'/cforms-mail.php&mail='.$entry->email.'" class="allbuttons" id="mailbutton'.$entry->sub_id.'">'.__('Envoyer un e-mail', 'cforms').'</a>';
					
		echo '</td>' .
			 '<td class="s">&nbsp;</td><td>';
			 
		if(!$rep && !$desist){
			echo 'Réponse définitive: ';
			
			//groupes de l'utilsateur
			global $oUserAccessManager;
			$aUserGroupsForObject = $oUserAccessManager->getAccessHandler()->getUserGroupsForObject(
				'user',
				wp_get_current_user()->get('ID')
			);
			$maisons="";
			foreach($aUserGroupsForObject as $group)
				$maisons .="|".$group->getGroupName()."#".$group->getId();
			//roles
			$roles=WPLAZARE_ROLE_BENEVOLE."#".__(WPLAZARE_ROLE_BENEVOLE, 'wplazare')."|".WPLAZARE_ROLE_PERSONNE_ACCUEILLIE."#".__(WPLAZARE_ROLE_PERSONNE_ACCUEILLIE,'wplazare');
				
			//accepter
			echo '<a data="'.$entry->first_name.'|'.$entry->last_name.'|'.$entry->email.'" maisons="'.$maisons.'" roles="'.$roles.'" href="?page='.$plugindir.'/cforms-mail.php&mail='.$entry->email.'&body='.$msg_accept.'&obj='.$cformsSettings['global']['cforms_register_accept_obj'].'" class="allbuttons accept" id="acceptbutton'.$entry->sub_id.'">'.__('Accepter la candidature', 'cforms').'</a>'.$e;
			//refuser
			echo '<a href="?page='.$plugindir.'/cforms-mail.php&mail='.$entry->email.'&body='.$msg_deny.'&obj='.$cformsSettings['global']['cforms_register_deny_obj'].'" class="allbuttons deleteall deny" id="denybutton'.$entry->sub_id.'">'.__('Refuser la candidature', 'cforms').'</a>'.$e;
		}
		if(!$desist){
			//desistement
			echo '<a href="?page='.$plugindir.'/cforms-mail.php&mail='.$entry->email.'&body='.$msg_desist.'&obj='.$cformsSettings['global']['cforms_register_desist_obj'].'" class="allbuttons deleteall desist" id="desistbutton'.$entry->sub_id.'">'.__('Désistement', 'cforms').'</a>'.$e;
		}
		
		echo '</td>' .
		     '</tr></table>';
		echo '</div>';
		

	else : ?>

		<p align="center"><?php _e('Impossible de trouver les données demandées. Veuillez rafraichir le tableau de données.', 'cforms') ?></p>
		</div>

	<?php endif;

}
//dialog for date picker rdv
echo '<div id="dialog"></div>
<script>
jQuery(document).ready(function () {
	jQuery(\'#dialog\').dialog({autoOpen: false, modal: true, draggable: false, resizable: false});	
});
</script>';
?>