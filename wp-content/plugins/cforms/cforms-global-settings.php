<?php
/*
please see cforms.php for more information
*/

### db settings
global $wpdb;
$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### new global settings container, will eventually be the only one!
$cformsSettings = get_option('cforms_settings');

$plugindir   = $cformsSettings['global']['plugindir'];
$cforms_root = $cformsSettings['global']['cforms_root'];

### check if pre-9.0 update needs to be made
if( $cformsSettings['global']['update'] )
	require_once (dirname(__FILE__) . '/update-pre-9.php');

if( isset($_REQUEST['AddDoc'])  ){
	require_once(dirname(__FILE__) . '/lib_options_sub.php');
}
	
### SMPT sever configured?
$smtpsettings=explode('$#$',$cformsSettings['global']['cforms_smtp']);

### Check Whether User Can Manage Database
check_access_priv();


### if all data has been erased quit
if ( check_erased() )
	return;

if ( isset($_REQUEST['deletetables']) ) {

	//$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformssubmissions");
	//$wpdb->query("DROP TABLE IF EXISTS $wpdb->cformsdata");

    $cformsSettings['global']['cforms_database'] = '0';
    update_option('cforms_settings',$cformsSettings);

	?>
	<div id="message" class="updated fade">
		<p>
		<strong><?php echo sprintf (__('cforms tracking tables %s have been deleted.', 'cforms'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?></strong>
			<br />
			<?php _e('Please backup/clean-up your upload directory, chances are that when you turn tracking back on, existing (older) attachments may be <u>overwritten</u>!') ?>
			<br />
			<small><?php _e('(only of course, if your form includes a file upload field)') ?></small>
		</p>
	</div>
	<?php

} else if( isset($_REQUEST['cforms_rsskeysnew']) ) {

	### new RSS key computed
	$cformsSettings['global']['cforms_rsskeyall'] = md5(rand());
	update_option('cforms_settings',$cformsSettings);

} else if( isset($_REQUEST['restoreallcformsdata']) )
	require_once(dirname(__FILE__) . '/lib_options_up.php');

// Update Settings
if( isset($_REQUEST['SubmitOptions']) ) {


    $cformsSettings['global']['cforms_register_accept'] = magic($_REQUEST['cforms_register_accept']);
	$cformsSettings['global']['cforms_register_deny'] = magic($_REQUEST['cforms_register_deny']);
	$cformsSettings['global']['cforms_register_desist'] = magic($_REQUEST['cforms_register_desist']);
	$cformsSettings['global']['cforms_register_rdv'] = magic($_REQUEST['cforms_register_rdv']);
	$cformsSettings['global']['cforms_register_accept_rdv'] = magic($_REQUEST['cforms_register_accept_rdv']);
	$cformsSettings['global']['cforms_register_deny_rdv'] = magic($_REQUEST['cforms_register_deny_rdv']);
	$cformsSettings['global']['cforms_register_ged'] = magic($_REQUEST['cforms_register_ged']);

    $cformsSettings['global']['cforms_register_accept_obj'] = stripslashes(magic($_REQUEST['cforms_register_accept_obj']));
	$cformsSettings['global']['cforms_register_deny_obj'] = stripslashes(magic($_REQUEST['cforms_register_deny_obj']));
	$cformsSettings['global']['cforms_register_desist_obj'] = stripslashes(magic($_REQUEST['cforms_register_desist_obj']));
	$cformsSettings['global']['cforms_register_rdv_obj'] = stripslashes(magic($_REQUEST['cforms_register_rdv_obj']));
	$cformsSettings['global']['cforms_register_accept_rdv_obj'] = stripslashes(magic($_REQUEST['cforms_register_accept_rdv_obj']));
	$cformsSettings['global']['cforms_register_deny_rdv_obj'] = stripslashes(magic($_REQUEST['cforms_register_deny_rdv_obj']));
	$cformsSettings['global']['cforms_register_ged_obj'] = stripslashes(magic($_REQUEST['cforms_register_ged_obj']));
	
	

	$cformsSettings['global']['cforms_rdv_lun'] = $_REQUEST['cforms_rdv_lun']?'1':'0';
	$cformsSettings['global']['cforms_rdv_mar'] = $_REQUEST['cforms_rdv_mar']?'1':'0';
	$cformsSettings['global']['cforms_rdv_mer'] = $_REQUEST['cforms_rdv_mer']?'1':'0';
	$cformsSettings['global']['cforms_rdv_jeu'] = $_REQUEST['cforms_rdv_jeu']?'1':'0';
	$cformsSettings['global']['cforms_rdv_ven'] = $_REQUEST['cforms_rdv_ven']?'1':'0';
	$cformsSettings['global']['cforms_rdv_sam'] = $_REQUEST['cforms_rdv_sam']?'1':'0';
	$cformsSettings['global']['cforms_rdv_dim'] = $_REQUEST['cforms_rdv_dim']?'1':'0';

	$cformsSettings['global']['cforms_rdv_lun_from'] = $_REQUEST['cforms_rdv_lun_from'];
	$cformsSettings['global']['cforms_rdv_lun_to'] = $_REQUEST['cforms_rdv_lun_to'];
	
	$cformsSettings['global']['cforms_rdv_mar_from'] = $_REQUEST['cforms_rdv_mar_from'];
	$cformsSettings['global']['cforms_rdv_mar_to'] = $_REQUEST['cforms_rdv_mar_to'];
	
	$cformsSettings['global']['cforms_rdv_mer_from'] = $_REQUEST['cforms_rdv_mer_from'];
	$cformsSettings['global']['cforms_rdv_mer_to'] = $_REQUEST['cforms_rdv_mer_to'];
	
	$cformsSettings['global']['cforms_rdv_jeu_from'] = $_REQUEST['cforms_rdv_jeu_from'];
	$cformsSettings['global']['cforms_rdv_jeu_to'] = $_REQUEST['cforms_rdv_jeu_to'];
	
	$cformsSettings['global']['cforms_rdv_ven_from'] = $_REQUEST['cforms_rdv_ven_from'];
	$cformsSettings['global']['cforms_rdv_ven_to'] = $_REQUEST['cforms_rdv_ven_to'];
	
	$cformsSettings['global']['cforms_rdv_sam_from'] = $_REQUEST['cforms_rdv_sam_from'];
	$cformsSettings['global']['cforms_rdv_sam_to'] = $_REQUEST['cforms_rdv_sam_to'];
	
	$cformsSettings['global']['cforms_rdv_dim_from'] = $_REQUEST['cforms_rdv_dim_from'];
	$cformsSettings['global']['cforms_rdv_dim_to'] = $_REQUEST['cforms_rdv_dim_to'];
	
	
	$cformsSettings['global']['ged'] = stripslashes(magic($_REQUEST['ged']));

    $cformsSettings['global']['cforms_show_quicktag'] = $_REQUEST['cforms_show_quicktag']?'1':'0';
	$cformsSettings['global']['cforms_sec_qa'] = 		magic($_REQUEST['cforms_sec_qa']);
	$cformsSettings['global']['cforms_codeerr'] = 		magic($_REQUEST['cforms_codeerr']);
	$cformsSettings['global']['cforms_database'] = 		$_REQUEST['cforms_database']?'1':'0';
	$cformsSettings['global']['cforms_showdashboard'] = $_REQUEST['cforms_showdashboard']?'1':'0';
	$cformsSettings['global']['cforms_datepicker'] = 	$_REQUEST['cforms_datepicker']?'1':'0';
	$cformsSettings['global']['cforms_dp_date'] = 		magic($_REQUEST['cforms_dp_date']);
	$cformsSettings['global']['cforms_dp_days'] = 		magic($_REQUEST['cforms_dp_days']);
	$cformsSettings['global']['cforms_dp_start'] = 		$_REQUEST['cforms_dp_start']==''?'0':$_REQUEST['cforms_dp_start'];
	$cformsSettings['global']['cforms_dp_months'] = 	magic($_REQUEST['cforms_dp_months']);

	$nav=array();
	$nav[0]=magic($_REQUEST['cforms_dp_prevY']);
	$nav[1]=magic($_REQUEST['cforms_dp_prevM']);
	$nav[2]=magic($_REQUEST['cforms_dp_nextY']);
	$nav[3]=magic($_REQUEST['cforms_dp_nextM']);
	$nav[4]=magic($_REQUEST['cforms_dp_close']);
	$nav[5]=magic($_REQUEST['cforms_dp_choose']);
	$cformsSettings['global']['cforms_dp_nav'] = $nav;

 	$cformsSettings['global']['cforms_inexclude']['ex'] = '';
  if( $_REQUEST['cforms_inc-or-ex']=='exclude' )
  	$cformsSettings['global']['cforms_inexclude']['ex'] = '1';

 	$cformsSettings['global']['cforms_inexclude']['ids'] = $_REQUEST['cforms_include'];

	$cformsSettings['global']['cforms_commentsuccess'] =magic($_REQUEST['cforms_commentsuccess']);
	$cformsSettings['global']['cforms_commentWait'] =  	$_REQUEST['cforms_commentWait'];
	$cformsSettings['global']['cforms_commentParent'] =	$_REQUEST['cforms_commentParent'];
	$cformsSettings['global']['cforms_commentHTML'] =	magic($_REQUEST['cforms_commentHTML']);
	$cformsSettings['global']['cforms_commentInMod'] =	magic($_REQUEST['cforms_commentInMod']);
	$cformsSettings['global']['cforms_avatar'] =	   	$_REQUEST['cforms_avatar'];

	$cformsSettings['global']['cforms_crlf']['h'] =	   	$_REQUEST['cforms_crlfH']?'1':'0';
	$cformsSettings['global']['cforms_crlf']['b'] =	   	$_REQUEST['cforms_crlf']?'1':'0';

	$smtpsettings[0] = $_REQUEST['cforms_smtp_onoff']?'1':'0';
	$smtpsettings[1] = $_REQUEST['cforms_smtp_host'];
	$smtpsettings[2] = magic($_REQUEST['cforms_smtp_user']);
	if ( !preg_match('/^\*+$/',$_REQUEST['cforms_smtp_pass']) ) {
		$smtpsettings[3] = magic($_REQUEST['cforms_smtp_pass']);
		}
	$smtpsettings[4] = $_REQUEST['cforms_smtp_ssltls'];
	$smtpsettings[5] = $_REQUEST['cforms_smtp_port'];
    $smtpsettings[6] = $_REQUEST['cforms_smtp_pop']?'1':'0';
    $smtpsettings[7] = $_REQUEST['cforms_smtp_pop_host'];
    $smtpsettings[8] = $_REQUEST['cforms_smtp_pop_port'];
    $smtpsettings[9] = $_REQUEST['cforms_smtp_pop_ln'];
	if ( !preg_match('/^\*+$/',$_REQUEST['cforms_smtp_pop_pw']) ) {
		$smtpsettings[10] = magic($_REQUEST['cforms_smtp_pop_pw']);
		}

	$cformsSettings['global']['cforms_smtp'] = implode('$#$',$smtpsettings) ;

	$cformsSettings['global']['cforms_upload_err1'] = magic($_REQUEST['cforms_upload_err1']);
	$cformsSettings['global']['cforms_upload_err2'] = magic($_REQUEST['cforms_upload_err2']);
	$cformsSettings['global']['cforms_upload_err3'] = magic($_REQUEST['cforms_upload_err3']);
	$cformsSettings['global']['cforms_upload_err4'] = magic($_REQUEST['cforms_upload_err4']);
	$cformsSettings['global']['cforms_upload_err5'] = magic($_REQUEST['cforms_upload_err5']);

	$cap = array();
	$cap['i'] = $_REQUEST['cforms_cap_i'];
	$cap['w'] = $_REQUEST['cforms_cap_w'];
	$cap['h'] = $_REQUEST['cforms_cap_h'];
	$cap['c'] = $_REQUEST['cforms_cap_c'];
	$cap['l'] = $_REQUEST['cforms_cap_l'];
	$cap['bg']= $_REQUEST['cforms_cap_b'];
	$cap['f'] = $_REQUEST['cforms_cap_f'];
	$cap['fo']= $_REQUEST['cforms_cap_fo'];
	$cap['foqa']= $_REQUEST['cforms_cap_foqa'];
	$cap['f1']= $_REQUEST['cforms_cap_f1'];
	$cap['f2']= $_REQUEST['cforms_cap_f2'];
	$cap['a1']= $_REQUEST['cforms_cap_a1'];
	$cap['a2']= $_REQUEST['cforms_cap_a2'];
	$cap['c1']= $_REQUEST['cforms_cap_c1'];
	$cap['c2']= $_REQUEST['cforms_cap_c2'];
	$cap['ac']= $_REQUEST['cforms_cap_ac'];

    ###	update new settings container
	$cformsSettings['global']['cforms_show_quicktag_js'] = $_REQUEST['cforms_show_quicktag_js']?true:false;
	$cformsSettings['global']['cforms_rssall'] = $_REQUEST['cforms_rss']?true:false;
	$cformsSettings['global']['cforms_rssall_count'] = $_REQUEST['cforms_rsscount'];
    $cformsSettings['global']['cforms_captcha_def'] = $cap;

    update_option('cforms_settings',$cformsSettings);

	// Setup database tables ?
	if ( isset($_REQUEST['cforms_database']) && $_REQUEST['cforms_database_new']=='true' ) {

		if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") <> $wpdb->cformssubmissions ){

			$sql = "CREATE TABLE " . $wpdb->cformssubmissions . " (
					  id int(11) unsigned auto_increment,
					  form_id varchar(3) default '',
					  sub_date timestamp,
					  email varchar(40) default '',
					  first_name varchar(100) default '',
					  last_name varchar(100) default '',
					  note varchar(100) default '',
					  ip varchar(15) default '',
					  state varchar(60) default 'Attente d\'un RDV',
					  PRIMARY KEY  (id) ) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);

			$sql = "CREATE TABLE " . $wpdb->cformsdata . " (
					  f_id int(11) unsigned auto_increment primary key,
					  sub_id int(11) unsigned NOT NULL,
					  field_name varchar(100) NOT NULL default '',
					  field_val text) ENGINE=MyISAM DEFAULT CHARSET=utf8;";

			require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			dbDelta($sql);
			
			
            ###check
	        if( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") <> $wpdb->cformssubmissions ) {
	            ?>
	            <div id="message" class="updated fade">
	                <p><strong><?php echo sprintf(__('ERROR: cforms tracking tables %s could not be created.', 'cforms'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?></strong></p>
	            </div>
	            <?php
			    $cformsSettings['global']['cforms_database'] = '0';
			    update_option('cforms_settings',$cformsSettings);
            }else{
	            ?>
	            <div id="message" class="updated fade">
	                <p><strong><?php echo sprintf(__('cforms tracking tables %s have been created.', 'cforms'),'(<code>cformssubmissions</code> &amp; <code>cformsdata</code>)') ?></strong></p>
	            </div>
	            <?php
            }

		} else {

			$sets = $wpdb->get_var("SELECT count(id) FROM $wpdb->cformssubmissions");
			?>
			<div id="message" class="updated fade">
				<p><strong><?php echo sprintf(__('Found existing cforms tracking tables with %s records!', 'cforms'),$sets) ?></strong></p>
			</div>
			<?php
		}
	}

}

### check for abspath.php
abspath_check();
	
	
?>

<div class="wrap" id="top">
    <div id="icon-cforms-global" class="icon32"><br/></div><h2><?php _e('Paramètres globaux','cforms')?></h2>

    <?php if ( isset($_POST['showinfo']) ) : ###debug "easter egg" 

        echo '<h2>'.__('Debug Info (all major setting groups)', 'cforms').'</h2><br/><pre style="font-size:11px;background-color:#F5F5F5;">';
        echo print_r(array_keys($cformsSettings),1)."</pre>";
        echo '<h2>'.__('Debug Info (all cforms settings)', 'cforms').'</h2><br/><pre style="font-size:11px;background-color:#F5F5F5;">'.print_r($cformsSettings,1)."</pre>";
    
	else : ?>
	
    <p><?php _e('Tous les paramètres de cette page s\'applique partout et à tous les formulaires (formulaire multi-parties).', 'cforms') ?></p>

	<form enctype="multipart/form-data" id="cformsdata" name="mainform" method="post" action="">
		<input type="hidden" name="cforms_database_new" value="<?php if($cformsSettings['global']['cforms_database']=="0") echo 'true'; ?>"/>


		<fieldset id="autoresponse" class="cformsoptions">
		<div class="cflegend op-closed" id="p13" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Messages prédéfinis', 'cforms')?>
            </div>
			<div class="cf-content" id="o13"><p>
			<?php
			echo sprintf(__('Vous pouvez ici pre-remplir les messages à envoyer dans certain cas. Dans tous les messages, vous pouvez utiliser les <a href="%s">variables autorisées</a> ( {<em>label du champ</em>} ).<br />Utilisez {_hash_sid} pour inclure l\'identifiant sécurisé.'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"');
			?></p>
			
			<table class="form-table">

				<tr class="ob ">
					<td class="obL"><label for="cforms_register_accept"><?php _e('<strong>Message d\'acceptation</strong><br />de l\'inscription', 'cforms'); ?></label></td>
					<td class="obR">
                    	<table><tr><td>
						Objet : <input type="text" id="cforms_register_accept_obj" name="cforms_register_accept_obj" value="<?php echo $cformsSettings['global']['cforms_register_accept_obj']; ?>">
						<?php $content = stripslashes($cformsSettings['global']['cforms_register_accept']);
						wp_editor( $content,'cforms_register_accept', $settings = array('editor_class' => 'content resizable processed fldrequired'));  ?>
						</td></tr></table>
					</td>
				</tr>
				
				<tr class="ob ">
					<td class="obL"><label for="cforms_register_deny"><?php _e('<strong>Message de refus</strong><br />de l\'inscription', 'cforms'); ?></label></td>
					<td class="obR">
                    	<table><tr><td>
						Objet : <input type="text" id="cforms_register_deny_obj" name="cforms_register_deny_obj" value="<?php echo $cformsSettings['global']['cforms_register_deny_obj']; ?>">
						<?php $content = stripslashes($cformsSettings['global']['cforms_register_deny']);
						wp_editor( $content,'cforms_register_deny', $settings = array('editor_class' => 'content resizable processed fldrequired'));  ?>
						</td></tr></table>
					</td>
				</tr>

				<tr class="ob ">
					<td class="obL"><label for="cforms_register_desist"><?php _e('<strong>Message suite à<br />un désistement</strong>', 'cforms'); ?></label></td>
					<td class="obR">
                    	<table><tr><td>
						Objet : <input type="text" id="cforms_register_desist_obj" name="cforms_register_desist_obj" value="<?php echo $cformsSettings['global']['cforms_register_desist_obj']; ?>">
						<?php $content = stripslashes($cformsSettings['global']['cforms_register_desist']);
						wp_editor( $content,'cforms_register_desist', $settings = array('editor_class' => 'content resizable processed fldrequired'));  ?>
						</td></tr></table>
					</td>
				</tr>
				
				<tr class="ob ">
					<td class="obL"><label for="cforms_register_rdv"><?php _e('<strong>Message de proposition rendez-vous</strong>', 'cforms'); ?></label></td>
					<td class="obR">
                    	<table><tr><td>
						Objet : <input type="text" id="cforms_register_rdv_obj" name="cforms_register_rdv_obj" value="<?php echo $cformsSettings['global']['cforms_register_rdv_obj']; ?>">
						<?php $content = stripslashes($cformsSettings['global']['cforms_register_rdv']);
						wp_editor( $content,'cforms_register_rdv', $settings = array('editor_class' => 'content resizable processed fldrequired'));  ?>
                    	<td>utilisez la variable {_date}<br />pour la date qui sera choisie.</td></td></tr></table>
					</td>
				</tr>			

				<tr class="ob ">
					<td class="obL"><label for="cforms_register_accept_rdv"><?php _e('<strong>Réponse positive</strong><br />à l\'issue du rendez-vous', 'cforms'); ?></label></td>
					<td class="obR">
						<table><tr><td>
						Objet : <input type="text" id="cforms_register_accept_rdv_obj" name="cforms_register_accept_rdv_obj" value="<?php echo $cformsSettings['global']['cforms_register_accept_rdv_obj']; ?>">
						<?php $content = stripslashes($cformsSettings['global']['cforms_register_accept_rdv']);
						wp_editor( $content,'cforms_register_accept_rdv', $settings = array('editor_class' => 'content resizable processed fldrequired'));  ?>
                    	<td>utilisez la variable {_documents} pour la liste des documents à fournir et {_convention_volontaire}, {_lecture_reglement}, http://www.lazare.eu/coordonnees-bancaires/?sub_id={_hash_sid}  pour inserer les différents liens.</td></tr></table>
					</td>
				</tr>

				<tr class="ob ">
					<td class="obL"><label for="cforms_register_deny_rdv"><?php _e('<strong>Réponse négative</strong><br />à l\'issue du rendez-vous', 'cforms'); ?></label></td>
					<td class="obR">
						<table><tr><td>
						Objet : <input type="text" id="cforms_register_deny_rdv_obj" name="cforms_register_deny_rdv_obj" value="<?php echo $cformsSettings['global']['cforms_register_deny_rdv_obj']; ?>">
						<?php $content = stripslashes($cformsSettings['global']['cforms_register_deny_rdv']);
						wp_editor( $content,'cforms_register_deny_rdv', $settings = array('editor_class' => 'content resizable processed fldrequired'));  ?>
                    	</td></tr></table>
					</td>
				</tr>
				
				<tr class="ob ">
					<td class="obL"><label for="cforms_register_ged"><?php _e('<strong>Message de relance</strong><br />s\'il manque des documents', 'cforms'); ?></label></td>
					<td class="obR">
						<table><tr><td>
						Objet : <input type="text" id="cforms_register_ged_obj" name="cforms_register_ged_obj" value="<?php echo $cformsSettings['global']['cforms_register_ged_obj']; ?>">
						<?php $content = stripslashes($cformsSettings['global']['cforms_register_ged']);
						wp_editor( $content,'cforms_register_ged', $settings = array('editor_class' => 'content resizable processed fldrequired'));  ?>
                    	</td><td>utilisez la variable {_documents_manquants}<br />pour la liste des documents qui manquent.</td></tr></table>
					</td>
				</tr>
				
				</table>
			
			</div>
		</fieldset>
		
		<fieldset id="ged" class="cformsoptions">
		<div class="cflegend op-closed" id="p15" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Gestion des documents', 'cforms')?>
            </div>
			<div class="cf-content" id="o15"><p>
			<?php
			echo sprintf(__('Si l\'inscrit doit fournir des documents, vous pouvez en gerer la liste ici (ne pas utiliser les points-virgules).'));
			echo '<div class="groupWrapper">';
			echo '<div class="fh1" title="Nom du document"><br /><span class="abbr">Nom du document</span></div>';
			$docs = explode(";", $cformsSettings['global']['ged']);
			$id = 0;
			foreach($docs as $doc){
				if(!empty($doc)){
					echo '<div id="div_'.++$id.'">';
					echo '<input type="text" id="doc_'.$id.'" name="doc_'.$id.'" class="inpfld" last="'.$doc.'" value="'.$doc.'" onChange="jQuery(\'#inpged\').val(jQuery(\'#inpged\').val().replace(jQuery(this).attr(\'last\')+\';\', jQuery(this).val()+\';\'));jQuery(this).attr(\'last\', jQuery(this).val());"/>';
					echo '<input class="xbutton" type="button" name="'.$id.'" value="" title="Supprimer le document" alt="Supprimer le document" onfocus="this.blur()" onclick="jQuery(\'#inpged\').val(jQuery(\'#inpged\').val().replace(jQuery(\'#doc_'.$id.'\').attr(\'last\')+\';\', \'\'));jQuery(\'#div_'.$id.'\').hide();"/>';
					echo '</div>';
				}
			}
			?>		
				<br />
			<input type="hidden"  value="<?php echo $cformsSettings['global']['ged']; ?>" name="ged" id="inpged"/>
			 <input type="submit" name="AddDoc" id="AddDoc" class="allbuttons addbutton" title="<?php _e('Ajouter un autre document', 'cforms'); ?>" value="<?php _e('Ajouter', 'cforms'); ?>" onfocus="this.blur()" onclick="javascript:document.mainform.action='#ged';" />
			</div>
		</div>
		</fieldset>

		<fieldset id="rdv" class="cformsoptions">
		<div class="cflegend op-closed" id="p16" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Disponibilité de rendez-vous', 'cforms')?>
            </div>
			<div class="cf-content" id="o16"><p>
			<?php
			echo sprintf(__('Vous pouvez spécifier ici vos disponibilités pour les rendez-vous. Si rien n\'est renseigné, toutes les dates/heures seront sélectionnables.'));
			?>
			<table class="form-table">
			<tr class="ob ">
						<td class="obL"><b>Jour</b></label></td>
						<td ><b>Disponible</b></td>
						<td class=""><b>De</b></td>
						<td class="obR"><b>À</b></td>
				</tr>
				<tr class="ob ">
						<td class="obL"><label for="cforms_rdv_lun">Lundi</label></td>
						<td ><input type="checkbox" name="cforms_rdv_lun" id="cforms_rdv_lun" value="lun" <?php if($cformsSettings['global']['cforms_rdv_lun']=="1") echo "checked=\"checked\""; ?>/></td>
						<td class=""><input class="cf_time" name="cforms_rdv_lun_from" id="cforms_rdv_lun_from" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_lun_from'] )); ?>"/></td>
						<td class="obR"><input class="cf_time" name="cforms_rdv_lun_to" id="cforms_rdv_lun_to" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_lun_to'] )); ?>"/></td>
				</tr>
				<tr class="ob ">
						<td class="obL"><label for="cforms_rdv_mar">Mardi</label></td>
						<td ><input type="checkbox" name="cforms_rdv_mar" id="cforms_rdv_mar" value="mar" <?php if($cformsSettings['global']['cforms_rdv_mar']=="1") echo "checked=\"checked\""; ?>/></td>
						<td class=""><input class="cf_time" name="cforms_rdv_mar_from" id="cforms_rdv_mar_from" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_mar_from'] )); ?>"/></td>
						<td class="obR"><input class="cf_time" name="cforms_rdv_mar_to" id="cforms_rdv_mar_to" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_mar_to'] )); ?>"/></td>
				</tr>
				<tr class="ob ">
						<td class="obL"><label for="cforms_rdv_mer">Mercredi</label></td>
						<td ><input type="checkbox" name="cforms_rdv_mer" id="cforms_rdv_mer" value="mer" <?php if($cformsSettings['global']['cforms_rdv_mer']=="1") echo "checked=\"checked\""; ?>/></td>
						<td class=""><input class="cf_time" name="cforms_rdv_mer_from" id="cforms_rdv_mer_from"/ value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_mer_from'] )); ?>"></td>
						<td class="obR"><input class="cf_time" name="cforms_rdv_mer_to" id="cforms_rdv_mer_to"/ value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_mer_to'] )); ?>"></td>
				</tr>
				<tr class="ob ">
						<td class="obL"><label for="cforms_rdv_jeu">Jeudi</label></td>
						<td ><input type="checkbox" name="cforms_rdv_jeu" id="cforms_rdv_jeu" value="jeu" <?php if($cformsSettings['global']['cforms_rdv_jeu']=="1") echo "checked=\"checked\""; ?>/></td>
						<td class=""><input class="cf_time" name="cforms_rdv_jeu_from" id="cforms_rdv_jeu_from"/ value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_jeu_from'] )); ?>"></td>
						<td class="obR"><input class="cf_time" name="cforms_rdv_jeu_to" id="cforms_jeu_mer_to"/ value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_jeu_to'] )); ?>"></td>
				</tr>
				<tr class="ob ">
						<td class="obL"><label for="cforms_rdv_ven">Vendredi</label></td>
						<td ><input type="checkbox" name="cforms_rdv_ven" id="cforms_rdv_ven" value="ven" <?php if($cformsSettings['global']['cforms_rdv_ven']=="1") echo "checked=\"checked\""; ?>/></td>
						<td class=""><input class="cf_time" name="cforms_rdv_ven_from" id="cforms_rdv_ven_from"/ value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_ven_from'] )); ?>"></td>
						<td class="obR"><input class="cf_time" name="cforms_rdv_ven_to" id="cforms_rdv_ven_to"/ value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_ven_to'] )); ?>"></td>
				</tr>
				<tr class="ob ">
						<td class="obL"><label for="cforms_rdv_sam">Samedi</label></td>
						<td ><input type="checkbox" name="cforms_rdv_sam" id="cforms_rdv_sam" value="sam" <?php if($cformsSettings['global']['cforms_rdv_sam']=="1") echo "checked=\"checked\""; ?>/></td>
						<td class=""><input class="cf_time" name="cforms_rdv_sam_from" id="cforms_rdv_sam_from"/ value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_sam_from'] )); ?>"></td>
						<td class="obR"><input class="cf_time" name="cforms_rdv_sam_to" id="cforms_rdv_sam_to" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_sam_to'] )); ?>"/></td>
				</tr>
				<tr class="ob ">
						<td class="obL"><label for="cforms_rdv_dim">Dimanche</label></td>
						<td ><input type="checkbox" name="cforms_rdv_dim" id="cforms_rdv_dim" value="dim" <?php if($cformsSettings['global']['cforms_rdv_dim']=="1") echo "checked=\"checked\""; ?>/></td>
						<td class=""><input class="cf_time" name="cforms_rdv_dim_from" id="cforms_rdv_dim_from" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_dim_from'] )); ?>"/></td>
						<td class="obR"><input class="cf_time" name="cforms_rdv_dim_to" id="cforms_rdv_dim_to" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_rdv_dim_to'] )); ?>"/></td>
				</tr>

				
			</table>
			</p>
			
			</div>
		</fieldset> 
		
		
		<fieldset id="popupdate" class="cformsoptions">
			<div class="cflegend op-closed" id="p9" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Popup DatePicker', 'cforms')?>
            </div>

			<div class="cf-content" id="o9">
				<p><?php echo sprintf(__('Si vous voulez ajouter un DatePicker javascript pour entrer des dates plus facilement, activez cette option. Ceci va ajouter un <strong>nouveau champ <i>Date</i></strong>. Regardez l\'<a href="%s" %s>Aide</a> pour plus d\'infos.', 'cforms'),'?page='.$plugindir.'/cforms-help.php#datepicker','onclick="setshow(19)"') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_datepicker" name="cforms_datepicker" <?php if($cformsSettings['global']['cforms_datepicker']=="1") echo "checked=\"checked\""; ?>/><label for="cforms_datepicker"><strong><?php _e('Activer le DatePicker javascript', 'cforms') ?></strong></label> ** <a class="infobutton" href="#" name="it10"><?php _e('Note &raquo;', 'cforms'); ?></a></td>
				</tr>
				<tr id="it10" class="infotxt"><td>&nbsp;</td><td class="ex"><?php _e('Activer cette fonctionnalité va entrainer le chargement de scripts supplémentaires.', 'cforms') ?></td></tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_dp_date"><strong><?php _e('Format de la date', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_dp_date" name="cforms_dp_date" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_dp_date'] )); ?>"/><a href="http://docs.jquery.com/UI/Datepicker/formatDate" target="_blank"><?php _e('Voir les formats acceptés &raquo;', 'cforms'); ?></a></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_dp_days"><strong><?php _e('Jours (Colonnes)', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_dp_days" name="cforms_dp_days" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_dp_days'] )); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_dp_months"><strong><?php _e('Mois', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_dp_months" name="cforms_dp_months" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_dp_months'] )); ?>"/></td>
				</tr>
				
				<tr class="ob">
					<td class="obL"><strong><?php _e('Label:', 'cforms'); ?></strong></td>
					<td class="obR"></td>
				</tr>				
				<tr class="ob">
					<?php $nav = $cformsSettings['global']['cforms_dp_nav']; ?>
					<td class="obL"><label for="cforms_dp_prevY"><?php _e('Année précédente', 'cforms'); ?></label></td>
					<td class="obR"><input type="text" id="cforms_dp_prevY" name="cforms_dp_prevY" value="<?php echo stripslashes(htmlspecialchars( $nav[0] )); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_dp_prevM"><?php _e('Mois précédent', 'cforms'); ?></label></td>
					<td class="obR"><input type="text" id="cforms_dp_prevM" name="cforms_dp_prevM" value="<?php echo stripslashes(htmlspecialchars( $nav[1] )); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_dp_nextY"><?php _e('Année suivante', 'cforms'); ?></label></td>
					<td class="obR"><input type="text" id="cforms_dp_nextY" name="cforms_dp_nextY" value="<?php echo stripslashes(htmlspecialchars( $nav[2] )); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_dp_nextM"><?php _e('Mois suivant', 'cforms'); ?></label></td>
					<td class="obR"><input type="text" id="cforms_dp_nextM" name="cforms_dp_nextM" value="<?php echo stripslashes(htmlspecialchars( $nav[3] )); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_dp_close"><?php _e('Fermer', 'cforms'); ?></label></td>
					<td class="obR"><input type="text" id="cforms_dp_close" name="cforms_dp_close" value="<?php echo stripslashes(htmlspecialchars( $nav[4] )); ?>"/></td>
				</tr>
				<tr class="ob">		
					<td class="obL"><label for="cforms_dp_choose"><?php _e('Choisissez une date', 'cforms'); ?></label></td>
					<td class="obR"><input type="text" id="cforms_dp_choose" name="cforms_dp_choose" value="<?php echo stripslashes(htmlspecialchars( $nav[5] )); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_dp_start"><?php _e('Début de semaine', 'cforms'); ?></label></td>
					<td class="obR"><input type="text" id="cforms_dp_start" name="cforms_dp_start" value="<?php echo stripslashes(htmlspecialchars( $cformsSettings['global']['cforms_dp_start'] )); ?>"/> <?php _e('0 = dimanche, 1 = lundi, etc.', 'cforms'); ?></td>
				</tr>
				</table>
			</div>
		</fieldset>


		<fieldset id="smtp" class="cformsoptions">
			<div class="cflegend op-closed" id="p10" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Paramètres du serveur mail', 'cforms')?>
            </div>

			<div class="cf-content" id="o10">

				<p><?php _e('Les e-mails envoyés respectent les RFC avec les CRLF (carriage-return/line-feed) comme séparateur de lignes. Si votre serveur de messagerie ajoute des sauts de lignes supplémentaire à l\'e-mail, vous devrez peut-être essayer et activer l\'option ci-dessous.', 'cforms') ?>
				<table class="form-table">
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_crlfH" name="cforms_crlfH" <?php if($cformsSettings['global']['cforms_crlf']['h']=="1") echo "checked=\"checked\""; ?>/><label for="cforms_crlfH"><?php echo sprintf(__('Separer les lignes de l\'%sen-tête%s de l\'e-mail avec LF seulement (CR supprimé)', 'cforms'),'<strong>','</strong>') ?></label></td>
				</tr>
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_crlf" name="cforms_crlf" <?php if($cformsSettings['global']['cforms_crlf']['b']=="1") echo "checked=\"checked\""; ?>/><label for="cforms_crlf"><?php echo sprintf(__('Separer les lignes du %scorp%s de l\'e-mail avec LF seulement (CR supprimé)', 'cforms'),'<strong>','</strong>') ?></label></td>
				</tr>
				<tr class="obSEP"><td colspan="2"></td></tr>
				</table>

				<p><img style="vertical-align:middle;margin-right:10px;" src="<?php echo $cforms_root; ?>/images/phpmailer.png" alt="phpmailerV2"/> <?php _e('Dans un environnement WP normal vous n\'avez pas à configurer ces paramètres!', 'cforms') ?>
				<?php _e('Si votre hébergement ne support pas la fonction <strong>PHP mail()</strong>, vous pouvez configurer l\'application pour utiliser un <strong>serveur mail SMTP</strong> externe pour envoyer les e-mails.', 'cforms') ?></p>
				<?php
				$userconfirm = $cformsSettings['global']['cforms_confirmerr'];
				if ( $smtpsettings[0]=='1' && $smtpsettings[4]<>'' && ($userconfirm&32)==0 ){
					if ( isset($_GET['cf_confirm']) && $_GET['cf_confirm']=='confirm32' ){
						$cformsSettings['global']['cforms_confirmerr'] = $userconfirm|32;
						update_option('cforms_settings',$cformsSettings);
						echo '<div id="message32" class="updated fade"><p>ok</p></div>';
                    } else {
						$text = '<strong>'.__('Important:','cforms').'</strong> '.__('Si vous avez besoin d\'utiliser SSL / TLS soyer sûr que votre serveur/environnement PHP le permet ! En cas de doute, demander à votre hébergeur s\'il supporte <strong>openssl</strong>.', 'cforms');
						echo '<div id="message32" class="updated fade"><p>'.$text.'</p><p><a href="?page='.$plugindir.'/cforms-global-settings.php&cf_confirm=confirm32" class="rm_button allbuttons">'.__('Remove Message','cforms').'</a></p></div>';
					}
				}
				?>

				<table class="form-table">
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_smtp_onoff" name="cforms_smtp_onoff" <?php if($smtpsettings[0]=="1") echo "checked=\"checked\""; ?>/><label for="cforms_smtp_onoff"><strong><?php _e('Activer le serveur SMTP externe', 'cforms') ?></strong></label> <a class="infobutton" href="#" name="it11"><?php _e('Note &raquo;', 'cforms'); ?></a></td>
				</tr>
				<tr id="it11" class="infotxt"><td>&nbsp;</td><td class="ex"><?php echo sprintf(__('Pour eviter les erreurs, le plugin utilise PHPmailer 2.0 , qui <strong>supporte</strong> à la fois <strong>SSL</strong> et <strong>TLS</strong> pour l\'authentification.','cforms')); ?></td></tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_smtp_host"><strong><?php _e('Adresse du serveur SMTP', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_smtp_host" name="cforms_smtp_host" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[1])); ?>"/></td>
				</tr>
				<tr class="ob space15">
					<td class="obL"><label for="cforms_smtp_ssl"><strong><?php _e('Securité', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<input type="radio" class="allchk" id="cforms_smtp_none" value="" name="cforms_smtp_ssltls" <?php echo ($smtpsettings[4]=='')?'checked="checked"':''; ?>/><label for="cforms_smtp_none"><strong><?php _e('Aucune', 'cforms'); ?></strong></label><br />
						<input type="radio" class="allchk" id="cforms_smtp_ssl" value="ssl" name="cforms_smtp_ssltls" <?php echo ($smtpsettings[4]=='ssl')?'checked="checked"':''; ?>/><label for="cforms_smtp_ssl"><strong><?php _e('SSL (e.g. gmail)', 'cforms'); ?></strong></label><br />
						<input type="radio" class="allchk" id="cforms_smtp_tls" value="tls" name="cforms_smtp_ssltls" <?php echo ($smtpsettings[4]=='tls')?'checked="checked"':''; ?>/><label for="cforms_smtp_tls"><strong><?php _e('TLS', 'cforms'); ?></strong></label>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_smtp_port"><strong><?php _e('Port', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_smtp_port" name="cforms_smtp_port" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[5])); ?>"/> <?php _e('Habituellement 465 (e.g. gmail) ou 587', 'cforms'); ?></td>
				</tr>
				<tr class="ob space15">
					<td class="obL">&nbsp;</td>
					<td class="obR"><strong><?php _e('Authentification SMTP (laisser vide si non demandé):', 'cforms'); ?></strong></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_smtp_user"><strong><?php _e('Nom d\'utilisateur', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_smtp_user" name="cforms_smtp_user" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[2])); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_smtp_pass"><strong><?php _e('Mot de passe', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_smtp_pass" name="cforms_smtp_pass" value="<?php echo str_repeat('*',strlen($smtpsettings[3])); ?>"/></td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_smtp_pop" name="cforms_smtp_pop" value="1" <?php if($smtpsettings[6]=="1") echo "checked=\"checked\""; ?>/><label for="cforms_smtp_pop"><strong><?php _e('POP avant SMTP', 'cforms') ?></strong></label></td>
				</tr>

				<?php if( $smtpsettings[6]=="1" ): ?>
				<tr class="ob">
					<td class="obL"><label for="cforms_smtp_pop_host"><strong><?php _e('Adresse du serveur POP', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_smtp_pop_host" name="cforms_smtp_pop_host" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[7])); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_smtp_pop_port"><strong><?php _e('Port', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_smtp_pop_port" name="cforms_smtp_pop_port" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[8])); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_smtp_pop_ln"><strong><?php _e('Login', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_smtp_pop_ln" name="cforms_smtp_pop_ln" value="<?php echo stripslashes(htmlspecialchars($smtpsettings[9])); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_smtp_pop_pw"><strong><?php _e('Mot de passe', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_smtp_pop_pw" name="cforms_smtp_pop_pw" value="<?php echo str_repeat('*',strlen($smtpsettings[10])); ?>"/></td>
				</tr>
                <?php endif; ?>
				</table>
			</div>
		</fieldset>


		<fieldset id="upload" class="cformsoptions">
			<div class="cflegend op-closed" id="p11" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Paramètres d\'upload', 'cforms')?>
            </div>

			<div class="cf-content" id="o11">
				<p>
					<?php echo sprintf(__('Configurez ces paramètres si vous ajoutez un champ "<code>Upload</code>" à un formulaire (regardez aussi l\'<a href="%s" %s>Aide</a> pour plus d\'information).', 'cforms'),'?page='.$plugindir.'/cforms-help.php#upload','onclick="setshow(19)"'); ?>
					

				<p class="ex">
					<?php _e('Si vous ajoutez un champ <em>Upload</em> à votre formulaire, la validation AJAX sera <strong>automatiquement desactivée</strong>, à cause des limitations HTML.', 'cforms') ?>
				</p>
	
				<p>Messages d'erreur:</p>
				<table class="form-table">
				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err5"><strong><?php _e('Type non autorisé', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea rows="80px" cols="280px" class="errmsgbox resizable" name="cforms_upload_err5" id="cforms_upload_err5" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err5'])); ?></textarea></td></tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err1"><strong><?php _e('Erreur génerique (inconnu)', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea rows="80px" cols="280px" class="errmsgbox resizable" name="cforms_upload_err1" id="cforms_upload_err1" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err1'])); ?></textarea></td></tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err2"><strong><?php _e('Fichier vide', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea  rows="80px" cols="280px" class="errmsgbox resizable" name="cforms_upload_err2" id="cforms_upload_err2" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err2'])); ?></textarea></td></tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err3"><strong><?php _e('Fichier trop gros', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea rows="80px" cols="280px" class="errmsgbox resizable" name="cforms_upload_err3" id="cforms_upload_err3" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err3'])); ?></textarea></td></tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_err4"><strong><?php _e('Erreur pendant l\'upload', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<table><tr><td><textarea rows="80px" cols="280px" class="errmsgbox resizable" name="cforms_upload_err4" id="cforms_upload_err4" ><?php echo stripslashes(htmlspecialchars($cformsSettings['global']['cforms_upload_err4'])); ?></textarea></td></tr></table>
					</td>
				</tr>
				</table>
			</div>
		</fieldset>


		<fieldset id="wpeditor" class="cformsoptions">
			<div class="cflegend op-closed" id="p12" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Bouton d\'edition WP', 'cforms')?>
            </div>

			<div class="cf-content" id="o12">
				<p><?php _e('Si vous voulez utiliser l\'editeur pour inserer vos formulaire activez l\'option ici.', 'cforms') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL"><img src="<?php echo $cforms_root; ?>/images/button.gif" alt=""/></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_show_quicktag" name="cforms_show_quicktag" <?php if($cformsSettings['global']['cforms_show_quicktag']=="1") echo "checked=\"checked\""; ?>/> <label for="cforms_show_quicktag"><strong><?php _e('Activer le bouton de l\'editeur TinyMCE', 'cforms') ?></strong></label></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_show_quicktag_js"><strong><?php _e('Resoudre les erreurs TinyMCE', 'cforms'); ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_show_quicktag_js" name="cforms_show_quicktag_js" <?php if( $cformsSettings['global']['cforms_show_quicktag_js']==true) echo "checked=\"checked\""; ?>/> <?php _e('En cas d\'erreurs due à d\'autres plugins.', 'cforms') ?></td>
				</tr>
				</table>
			</div>
		</fieldset>

		<fieldset id="captcha" class="cformsoptions">
			<div class="cflegend op-closed" id="p26" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Paramètres CAPTCHA', 'cforms')?>
            </div>

			<div class="cf-content" id="o26">
				<p><?php _e('Paramètres pour changer l\'apparence des images CAPTCHA.', 'cforms') ?></p>

				<?php
					$cap = $cformsSettings['global']['cforms_captcha_def'];
					$h = prep2( $cap['h'],25 );
					$w = prep2( $cap['w'],115 );
					$c = prep2( $cap['c'],'000066' );
					$l = prep2( $cap['l'],'000066' );
					$f = prep2( $cap['f'],'font4.ttf' );
					$a1 = prep2( $cap['a1'],-12 );
					$a2 = prep2( $cap['a2'],12 );
					$f1 = prep2( $cap['f1'],17 );
					$f2 = prep2( $cap['f2'],19 );
					$bg = prep2( $cap['bg'],'1.gif' );
					$c1 = prep2( $cap['c1'],4 );
					$c2 = prep2( $cap['c2'],5 );
					$i  = prep2( $cap['i'],'i' );
					$ac = prep2( $cap['ac'],'abcdefghijkmnpqrstuvwxyz23456789' );

					$img = "&amp;c1={$c1}&amp;c2={$c2}&amp;ac={$ac}&amp;i={$i}&amp;w={$w}&amp;h={$h}&amp;c={$c}&amp;l={$l}&amp;f={$f}&amp;a1={$a1}&amp;a2={$a2}&amp;f1={$f1}&amp;f2={$f2}&amp;b={$bg}";

					$fonts = '<select name="cforms_cap_f" id="cforms_cap_f">'.cf_get_files('captchafonts',$f,'ttf').'</select>';
					$backgrounds = '<select name="cforms_cap_b" id="cforms_cap_b">'.cf_get_files('captchabg',$bg,'gif').'</select>';

				?>

				<div style="position:absolute; z-index:9999;">
				<div id="mini" onmousedown="coreXY('mini',event)" style="top:0px; left:10px; display:none; margin-left:5%;">
					<div class="north"><span id="mHEX">FFFFFF</span><div onmousedown="$cfS('mini').display='none';">x</div></div>
					<div class="south" id="mSpec" style="HEIGHT: 128px; WIDTH: 128px;" onmousedown="coreXY('mCur',event)">
						<div id="mCur" style="TOP: 86px; LEFT: 68px;"></div>
						<img src="<?php echo $cforms_root; ?>/images/circle.png" onmousedown="return false;" alt=""/>
						<img src="<?php echo $cforms_root; ?>/images/resize.gif" id="mSize" onmousedown="coreXY('mSize',event); return false;" alt=""/>
					</div>
				</div>
				</div>

				<table class="form-table">
				<tr class="ob">
					<td class="obL"><strong><?php _e('Aperçu', 'cforms') ?></strong><br /><span id="pnote" style="display:none; color:red;"><?php _e('N\'oubliez pas de sauvegarder les changements !', 'cforms'); ?></span></td>
					<td class="obR" id="adminCaptcha">
                        <a title="<?php _e('Recharger la captcha', 'cforms'); ?>" href="javascript:resetAdminCaptcha('<?php echo $cforms_root; ?>')"><?php _e('Reload Captcha Image', 'cforms'); ?> &raquo;</a>
					</td>
				</tr>

				
				<tr class="ob space15">
					<td class="obL"><label for="cforms_cap_w"><strong><?php _e('Largeur', 'cforms') ?></strong></label></td>
					<td class="obR">
						<input class="cap" type="text" id="cforms_cap_w" name="cforms_cap_w" value="<?php echo $w; ?>"/>
						<label for="cforms_cap_h" class="second-l"><strong><?php _e('Hauteur', 'cforms') ?></strong></label><input class="cap" type="text" id="cforms_cap_h" name="cforms_cap_h" value="<?php echo $h; ?>"/>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="inputID1"><strong><?php _e('Couleur de la bordure', 'cforms') ?></strong></label></td>
					<td class="obR">
						<input class="cap" type="text" id="inputID1" name="cforms_cap_l" onclick="javascript:currentEL=1;" value="<?php echo $l; ?>"/><input type="button" name="col-border" class="colorswatch" style="background-color:#<?php echo $l; ?>" id="plugID1" onclick="this.blur(); currentEL=1; $cfS('mini').display='block';"/>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cap_b"><strong><?php _e('Arrière-plan', 'cforms') ?></strong></label></td>
					<td class="obR">
						<?php echo $backgrounds; ?>
					</td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_cap_f"><strong><?php _e('Police', 'cforms') ?></strong></label></td>
					<td class="obR">
						<?php echo $fonts; ?>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cap_f1"><strong><?php _e('Taille min', 'cforms') ?></strong></label></td>
					<td class="obR">
						<input class="cap" type="text" id="cforms_cap_f1" name="cforms_cap_f1" value="<?php echo $f1; ?>"/>
						<label for="cforms_cap_f2" class="second-l"><strong><?php _e('Taille max', 'cforms') ?></strong></label><input class="cap" type="text" id="cforms_cap_f2" name="cforms_cap_f2" value="<?php echo $f2; ?>"/>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cap_a1"><strong><?php _e('Angle min', 'cforms') ?></strong></label></td>
					<td class="obR">
						<input class="cap" type="text" id="cforms_cap_a1" name="cforms_cap_a1" value="<?php echo $a1; ?>"/>
						<label for="cforms_cap_a2" class="second-l"><strong><?php _e('Angle max', 'cforms') ?></strong></label><input class="cap" type="text" id="cforms_cap_a2" name="cforms_cap_a2" value="<?php echo $a2; ?>"/>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="inputID2"><strong><?php _e('Couleur', 'cforms') ?></strong></label></td>
					<td class="obR">
						<input class="cap" type="text" id="inputID2" name="cforms_cap_c" onclick="javascript:currentEL=2;" value="<?php echo $c; ?>"/><input type="button" name="col-border" class="colorswatch" style="background-color:#<?php echo $c; ?>" id="plugID2" onclick="this.blur(); currentEL=2; $cfS('mini').display='block';"/>
					</td>
				</tr>

				<tr class="ob space15">
					<td class="obL">&nbsp;</td>
                    <td class="obR"><strong><?php _e('Nombre de caratères', 'cforms') ?></strong></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cap_c1"><strong><?php _e('Minimum', 'cforms') ?></strong></label></td>
					<td class="obR">
						<input class="cap" type="text" id="cforms_cap_c1" name="cforms_cap_c1" value="<?php echo $c1; ?>"/>
						<label for="cforms_cap_c2" class="second-l"><strong><?php _e('Maximum', 'cforms') ?></strong></label><input class="cap" type="text" id="cforms_cap_c2" name="cforms_cap_c2" value="<?php echo $c2; ?>"/>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cap_ac"><strong><?php _e('Caractères autorisés', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_cap_ac" name="cforms_cap_ac" value="<?php echo $ac; ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_cap_i" name="cforms_cap_i" value="i" <?php if($cap['i']=='i') echo "checked=\"checked\""; ?>/><label for="cforms_cap_i"><?php _e('Insensible à la casse', 'cforms') ?></label></td>
				</tr>
				</table>
			</div>
		</fieldset>


		<fieldset id="tracking" class="cformsoptions">
			<div class="cflegend op-closed" id="p14" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('DB Tracking', 'cforms')?>
            </div>

			<div class="cf-content" id="o14">
				<p><?php _e('Ne modifez ces paramètres que si vous savez ce que vous faites. Vous pourriez perdre toutes vos données.', 'cforms') ?></p>

				<table class="form-table">
				
				<tr class="ob space15">
					<td class="obL"><label for="cforms_database"><strong><?php _e('Activer le DB Tracking', 'cforms') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_database" name="cforms_database" <?php if($cformsSettings['global']['cforms_database']=="1") echo "checked=\"checked\""; ?>/></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_showdashboard"><strong><?php _e('Afficher sur le dashboard', 'cforms') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_showdashboard" name="cforms_showdashboard" <?php if($cformsSettings['global']['cforms_showdashboard']=="1") echo "checked=\"checked\""; ?>/></td>
				</tr>

				</table>
			</div>
		</fieldset>

	    <div class="cf_actions" id="cf_actions" style="display:none;">
			<input id="cfbar-showinfo" class="allbuttons addbutton" type="submit" name="showinfo" value=""/>
			<input id="cfbar-deleteall" class="jqModalDelAll allbuttons deleteall" type="button" name="deleteallbutton" value=""/>
			<input id="deletetables" class="allbuttons deleteall" type="submit" name="deletetables" value=""/>
			<input id="backup" type="button" class="jqModalBackup allbuttons" name="backup"  value=""/>
			<input id="cfbar-SubmitOptions" type="submit" name="SubmitOptions" class="allbuttons updbutton formupd" value="" />
	    </div>
		
	</form>

	<?php endif; ### not showing debug msgs. ?> 
	
	<?php cforms_footer(); ?>
</div>

<div class="jqmWindow" id="cf_backupbox">
    <div class="cf_ed_header jqDrag"><?php _e('Backup &amp; Restore All Settings','cforms'); ?></div>
    <div class="cf_ed_main_backup">
        <form enctype="multipart/form-data" action="" name="backupform" method="post">
            <div class="controls">

				<p class="ex"><?php _e('Restoring all settings will overwrite all form specific &amp; global settings!', 'cforms') ?></p>
				<p>
                	<input type="submit" name="saveallcformsdata" title="<?php _e('Backup all settings now!', 'cforms') ?>" class="allbuttons" value="<?php _e('Backup all settings now!', 'cforms') ?>" onclick="javascript:jQuery('#cf_backupbox').jqmHide();"/>&nbsp;&nbsp;&nbsp;
                	<input type="file" id="importall" name="importall" size="25" /><input type="submit" name="restoreallcformsdata" title="<?php _e('Restore all settings now!', 'cforms') ?>" class="allbuttons deleteall" value="<?php _e('Restore all settings now!', 'cforms') ?>" onclick="return confirm('<?php _e('With a broken backup file, this action may erase all your settings! Do you want to continue?', 'cforms') ?>');"/>
				</p>
				<em><?php _e('PS: Individual form configurations can be backup up on the respective form admin page.', 'cforms') ?></em>
                <p class="cancel"><a href="#" id="cancel" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></p>

            </div>
            <input type="hidden" name="noSub" value="<?php echo $noDISP; ?>"/>
        </form>
    </div>
</div>
<div class="jqmWindow" id="cf_delall_dialog">
    <div class="cf_ed_header jqDrag"><?php _e('Uninstalling / Removing cforms','cforms'); ?></div>
    <div class="cf_ed_main_backup">
        <form action="" name="deleteform" method="post">
            <div id="cf_target_del"><?php _e('Warning!','cforms'); ?></div>
            <div class="controls">
				<p><?php _e('Generally, simple deactivation of cforms does <strong>not</strong> erase any of its data. If you like to quit using cforms for good, please erase all data before deactivating the plugin.', 'cforms') ?></p>
				<p><strong><?php _e('This is irrevocable!', 'cforms') ?></strong>&nbsp;&nbsp;&nbsp;<br />
					 <input type="submit" name="cfdeleteall" title="<?php _e('Are you sure you want to do this?!', 'cforms') ?>" class="allbuttons deleteall" value="<?php _e('DELETE *ALL* CFORMS DATA', 'cforms') ?>" onclick="return confirm('<?php _e('Final Warning!', 'cforms') ?>');"/></p>

                <p class="cancel"><a href="#" id="cancel" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></p>
            </div>
        </form>
    </div>
</div>

<?php
add_action('admin_bar_menu', 'add_items');
function add_items($admin_bar){
	
	global $wpdb;

	addAdminBar_root($admin_bar, 'cforms-bar', 'Inscription Admin');
	
	//addAdminBar_item('cforms-showinfo', __('Produce debug output', 'cforms'), __('Outputs -for debug purposes- all cforms settings', 'cforms'), 'jQuery("#cfbar-showinfo").trigger("click"); return false;');
	//addAdminBar_item('cforms-dellAllButton', __('Uninstalling / removing cforms', 'cforms'), __('Be careful here...', 'cforms'), 'jQuery("#cfbar-deleteall").trigger("click"); return false;');

	// if ( $wpdb->get_var("show tables like '$wpdb->cformssubmissions'") == $wpdb->cformssubmissions ) 
		// addAdminBar_item('cforms-deletetables', __('Delete cforms tracking tables', 'cforms'), __('Be careful here...', 'cforms'), 'if ( confirm("'.__('Do you really want to erase all collected data?', 'cforms').'") ) jQuery("#deletetables").trigger("click"); return false;');

	// addAdminBar_item('cforms-backup', __('Backup / restore all settings', 'cforms'), __('Better safe than sorry ;)', 'cforms'), 'jQuery("#backup").trigger("click"); return false;');
	
	addAdminBar_item($admin_bar, 'cforms-SubmitOptions', __('Sauvegarder les modifications', 'cforms'), '', 'document.mainform.action="#"+getFieldset(focusedFormControl); jQuery("#cfbar-SubmitOptions").trigger("click"); return false;', 'root-default');

}

function cf_get_files($dir,$currentfile,$ext){
	global	$cformsSettings;

	$s = $cformsSettings['global']['cforms_IIS'];
	$presetsdir		= $cformsSettings['global']['cforms_root_dir'] .$s.'..'.$s .'cforms-custom';
	$list 			= '';
	$allfiles		= array();

	if ( file_exists($presetsdir) ){

		$list .= '<option disabled="disabled" style="background:#e4e4e4">&nbsp;&nbsp;*** ' .__('custom files','cforms'). ' ***&nbsp;&nbsp;</option>';

		if ($handle = opendir($presetsdir)) {
		    while (false !== ($file = readdir($handle))) {
		        if (eregi('\.'.$ext.'$',$file) && $file != "." && $file != ".." && filesize($presetsdir.'/'.$file) > 0)
					$list .= '<option value="../../cforms-custom/'.$file.'"'.(('../../cforms-custom/'.$file==$currentfile)?' style="background:#fbd0d3" selected="selected"':'').'>' .$file. '</option>';
		    }
		    closedir($handle);
		}

		$list .= '<option disabled="disabled" style="background:#e4e4e4">&nbsp;&nbsp;*** ' .__('cform css files','cforms'). ' ***&nbsp;&nbsp;</option>';
	}

	$presetsdir		= $cformsSettings['global']['cforms_root_dir'].$s. $dir .$s;
	if ($handle = opendir($presetsdir)) {
	    while (false !== ($file = readdir($handle))) {
	        if (eregi('\.'.$ext.'$',$file) && $file != "." && $file != ".." && filesize($presetsdir.$file) > 0)
				$list .= '<option value="'.$file.'"'.(($file==$currentfile)?' style="background:#fbd0d3" selected="selected"':'').'>' .$file. '</option>';
	    }
	    closedir($handle);
	}

    return ($list=='')?'<li>'.__('Not available','cforms').'</li>':$list;
}


### strip stuff
function prep2($v,$d) {
	return ($v<>'')?stripslashes(htmlspecialchars($v)):$d;
}
?>