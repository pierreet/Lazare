<?php

###
### please see cforms.php for more information
###

### new global settings container, will eventually be the only one!
$cformsSettings = get_option('cforms_settings');

$plugindir   = $cformsSettings['global']['plugindir'];
$cforms_root = $cformsSettings['global']['cforms_root'];

### check if pre-9.0 update needs to be made
if( $cformsSettings['global']['update'] )
	require_once (dirname(__FILE__) . '/update-pre-9.php');

### Check Whether User Can Manage Database
check_access_priv();

### if all data has been erased quit
if ( check_erased() )
	return;


### default to 1 & get real #
$FORMCOUNT=$cformsSettings['global']['cforms_formcount'];

if(isset($_REQUEST['addbutton'])){
	require_once(dirname(__FILE__) . '/lib_options_add.php');

} elseif(isset($_REQUEST['dupbutton'])) {
	require_once(dirname(__FILE__) . '/lib_options_dup.php');

} elseif( isset($_REQUEST['uploadcformsdata']) ) {
	require_once(dirname(__FILE__) . '/lib_options_up.php');

} elseif(isset($_REQUEST['delbutton']) && $FORMCOUNT>1) {
	require_once(dirname(__FILE__) . '/lib_options_del.php');

} else {

	### set paramters to default, if not exists
	$noDISP='1';$no='';
	if( isset($_REQUEST['switchform']) ) { ### only set when hitting form chg buttons
		if( $_REQUEST['switchform']<>'1' )
			$noDISP = $no = $_REQUEST['switchform'];
	}
	else if( isset($_REQUEST['go']) ) { ### only set when hitting form chg buttons
		if( $_REQUEST['pickform']<>'1' )
			$noDISP = $no = $_REQUEST['pickform'];
	}
	else{
		if( isset($_REQUEST['noSub']) && (int)$_REQUEST['noSub']>1 ) ### otherwise stick with the current form
			$noDISP = $no = $_REQUEST['noSub'];
	}

}

### PRESETS
if ( isset($_REQUEST['formpresets']) )
	require_once(dirname(__FILE__) . '/lib_options_presets.php');


### default: $field_count = what's in the DB
$field_count = $cformsSettings['form'.$no]['cforms'.$no.'_count_fields'];


### check if T-A-F action is required
$alldisabled=false;
$allenabled=0;
if( isset($_REQUEST['addTAF']) || isset($_REQUEST['removeTAF']) )
{

	$posts = $wpdb->get_results("SELECT ID FROM $wpdb->posts");

	if ( isset($_REQUEST['addTAF']) ){

		foreach($posts as $post) {
			if ( add_post_meta($post->ID, 'tell-a-friend', '1', true) )
				$allenabled++;
		}

	} else if ( isset($_REQUEST['removeTAF']) ){
		$wpdb->query("DELETE FROM $wpdb->postmeta WHERE meta_key = 'tell-a-friend'");
		$alldisabled=true;
	}

}


### Update Settings
if( isset($_REQUEST['SubmitOptions']) || isset($_REQUEST['AddField']) || array_search("X", $_REQUEST) ){
	require_once(dirname(__FILE__) . '/lib_options_sub.php');
}


### new RSS key computed
if( isset($_REQUEST['cforms_rsskeysnew']) ) {
	$cformsSettings['form'.$no]['cforms'.$no.'_rsskey'] = md5(rand());
	update_option('cforms_settings',$cformsSettings);
}

### Reset Admin and AutoConf messages
if( isset($_REQUEST['cforms_resetAdminMsg']) ) {
	$cformsSettings['form'.$no]['cforms'.$no.'_header'] = __('A new submission (form: "{Form Name}")', 'cforms') . "\r\n============================================\r\n" . __('Submitted on: {Date}', 'cforms') . "\r\n" . __('Via: {Page}', 'cforms') . "\r\n" . __('By {IP} (visitor IP)', 'cforms') . ".\r\n" . ".\r\n";
	$cformsSettings['form'.$no]['cforms'.$no.'_header_html'] = '<p '.$cformsSettings['global']['cforms_style']['meta'].'>' . __('A form has been submitted on {Date}, via: {Page} [IP {IP}]', 'cforms') . '</p>';
	update_option('cforms_settings',$cformsSettings);
}
if( isset($_REQUEST['cforms_resetAutoCMsg']) ) {
	$cformsSettings['form'.$no]['cforms'.$no.'_cmsg'] = __('Merci pour votre inscription', 'cforms') . "\n". __('Nous vous recontacterons dès que possible.', 'cforms') . "\n\n";
		$cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'] = '<div '.$cformsSettings['global']['cforms_style']['autoconf'].'><p '.$cformsSettings['global']['cforms_style']['dear'] .'>'. __('Bonjour,', 'cforms') . "</p>\n<p ". $cformsSettings['global']['cforms_style']['confp'].'>'. __('Merci pour votre inscription.', 'cforms') . "</p>\n<p ".$cformsSettings['global']['cforms_style']['confp'].'>'. __('Nous vous recontacterons dès que possible.', 'cforms') . "\n<div ".$cformsSettings['global']['cforms_style']['confirmationmsg'].'>'.__('Ceci est un message de confirmation envoyé automatiquement.', 'cforms')." {Date}.</div></div>\n\n";
	update_option('cforms_settings',$cformsSettings);
}

### delete field if we find one and move the rest up
$deletefound = 0;
if(strlen($cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $field_count]) > 0) {

	$temp_count = 1;
	while($temp_count <= $field_count) {

		if(isset($_REQUEST['DeleteField' . $temp_count])) {
			$deletefound = 1;
			$cformsSettings['form'.$no]['cforms'.$no.'_count_fields'] = ($field_count - 1);
		}

		if($deletefound && $temp_count<$field_count) {
			$temp_val = $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . ($temp_count+1)];
			$cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . ($temp_count)] = $temp_val;
		}

		$temp_count++;
	} ### while

	if($deletefound == 1) {  ### now delete
	  	unset( $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $field_count] );
		$field_count--;
	}
    update_option('cforms_settings',$cformsSettings);
} ### if


### check possible errors
require_once(dirname(__FILE__) . '/lib_options_err.php');


###
### prep drop down box for form selection
###
$formlistbox = ' <select id="pickform" name="pickform">';
for ($i=1; $i<=$FORMCOUNT; $i++){
	$j   = ( $i > 1 )?$i:'';
	$sel = ($noDISP==$i)?' selected="selected"':'';
	$formlistbox .= '<option value="'.$i.'" '.$sel.'>'.stripslashes($cformsSettings['form'.$j]['cforms'.$j.'_fname']).'</option>';
}
$formlistbox .= '</select>';


### make sure at least the default FROM: address is set
if ( $cformsSettings['form'.$no]['cforms'.$no.'_fromemail'] == '' ){
	$cformsSettings['form'.$no]['cforms'.$no.'_fromemail'] = '"'.get_option('blogname').'" <wordpress@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME'])) . '>';
    update_option('cforms_settings',$cformsSettings);
}

### check if HTML needs to be enabled
$fd = $cformsSettings['form'.$no]['cforms'.$no.'_formdata'];
if( strlen($fd)<=2 ) {
	$fd .= ( $cformsSettings['form'.$no]['cforms'.$no.'_header_html']<>''  )?'1':'0';
	$fd .= ( $cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html']<>'' )?'1':'0';
	$cformsSettings['form'.$no]['cforms'.$no.'_formdata'] = $fd;
    update_option('cforms_settings',$cformsSettings);
}

### check for abspath.php
abspath_check();

$userconfirm = $cformsSettings['global']['cforms_confirmerr'];
if ( ($userconfirm & 64) == 0 ){	### 64 = upgrade to 13.0
	if ( isset($_GET['cf_confirm']) && $_GET['cf_confirm']=='confirm64' ){
		$cformsSettings['global']['cforms_confirmerr'] = $userconfirm|64;
		update_option('cforms_settings',$cformsSettings);
	} else {
		$text = '<p><strong><u>'.__('Please note the main changes for v13.0','cforms').'</u></strong></p>'.
				'<p>'.__('<strong>Admin Action Menu & Saving Settings</strong><br/>Note that the floating admin drop down on the right side has been moved into the admin bar at the top!', 'cforms').'</p>'.
				'<p>'.__('<strong>Date Picker</strong><br/>going forward, cforms will exclusively utilize WP\'s jQuery date picker version! See global settings for supported date formats.', 'cforms').'</p>'.
				'<p>'.__('<strong>Admin and Auto Confirmation Messages</strong><br/>The email layouts have been revised and improved, please goto your individual Message Settings and <u>reset to default</u>.', 'cforms').'</p>';
		//echo '<div id="message64" class="updated fade">'.$text.'<p><a href="?page='.$plugindir.'/cforms-options.php&cf_confirm=confirm64" class="rm_button allbuttons">'.__('Remove Message','cforms').'</a></p></div>';
	}
}
				
?>

<div class="wrap" id="top">
	<div id="icon-cforms-settings" class="icon32"><br/></div><h2><?php _e('Paramètres du formulaire','cforms')?></h2>

	<form enctype="multipart/form-data" id="cformsdata" name="mainform" method="post" action="#">
		<table class="chgformbox" title="<?php _e('Changer de formulaire.', 'cforms') ?>">
		<tr>
            <td class="chgL">
            	<label for="switchform" class="bignumber navbar"><?php _e('Aller à', 'cforms') ?> </label>
                <?php echo $formlistbox; ?><input type="submit" class="allbuttons go" id="go" name="go" value="<?php _e('Go', 'cforms');?>"/>
            </td>
            <td class="chgM">
                <?php
                for ($i=1; $i<=$FORMCOUNT; $i++) {
                    $j   = ( $i > 1 )?$i:'';
                    echo '<input id="switchform" title="'.stripslashes($cformsSettings['form'.$j]['cforms'.$j.'_fname']).'" class="allbuttons chgbutton'.(($i <> $noDISP)?'':'hi').'" type="submit" name="switchform" value="'.$i.'"/>';
                }
                ?>
        	</td>
			</tr>
        </table>
		<input type="hidden" name="no" value="<?php echo $noDISP; ?>"/>
		<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>" />
<!--
	    <p>
	        <?php //echo sprintf(__('<strong>cforms</strong> allows you <a href="%s" %s>to insert</a> one or more custom designed contact forms, which on submission (preferably via Ajax) will send the visitor info via email and optionally stores the feedback in the database.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#inserting','onclick="setshow(18)"'); ?>
	        <?php //echo sprintf(__('<a href="%s" %s>Here</a> is a quick step by step quide to get you up and running quickly.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#guide','onclick="setshow(17)"'); ?>
	    </p>
-->
		<table class="mainoptions">
		<tr>
			<td class="chgL">
            	<label for="cforms_fname" class="bignumber"><?php _e('Nom du formulaire', 'cforms') ?></label>
				<input id="cforms_fname" name="cforms_fname" class="cforms_fname" size="40" value="<?php echo stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fname']);  ?>" title="<?php _e('Chaque formulaire doit avoir un nom différent.', 'cforms') ?>"/>
				<!--<input title="<?php _e('Enables or disables Ajax support for this form.', 'cforms') ?>" id="cforms_ajax" type="checkbox" class="allchk cforms_ajax" name="cforms_ajax" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_ajax']=="1") echo "checked=\"checked\""; ?>/>
				<label title="<?php _e('Enables or disables Ajax support for this form.', 'cforms') ?>" for="cforms_ajax" class="bignumber"><?php _e('Ajax enabled', 'cforms') ?></label>-->
			</td>
        </tr>
        </table>

	<fieldset id="anchorfields" class="cf-content">

		<div>
			<?php echo sprintf(__('Regardez la section <strong>Aide</strong> pour avoir plus d\'information sur les différents <a href="%s" %s>type de champs</a>,', 'cforms'),'?page='.$plugindir.'/cforms-help.php#fields','onclick="setshow(19)"') . ' ' .
					   sprintf(__(' les <a href="%s" %s>FIELDSETS</a>,', 'cforms'), '?page='.$plugindir.'/cforms-help.php#hfieldsets','onclick="setshow(19)"') .
					   sprintf(__(' <a href="%s" %s>valeur par défaut</a> et <a href="%s" %s>expressions régulières</a>. ', 'cforms'),'?page='.$plugindir.'/cforms-help.php#single','onclick="setshow(19)"','?page='.$plugindir.'/cforms-help.php#regexp','onclick="setshow(19)"') .
					   sprintf(__('Vous pouvez aussi ajouter vos <a href="%s" %s>messages d\'erreur personnalisés</a>.', 'cforms'),'?page='.$plugindir.'/cforms-help.php#customerr','onclick="setshow(20)"'); ?>
		</div>

		<div class="tableheader">
        	<div id="cformswarning" style="display:none"><?php echo __('Sauvegardez le nouvel ordre des champs (<em>Sauvegarder les modifications</em>)!','cforms'); ?></div>
        	<div>
	            <div class="fh1" title="<?php _e('Peut-être un simple titre ou une expression plus complexe. Regardez l\'Aide!', 'cforms'); ?>"><br /><span class="abbr"><?php _e('Nom du champ', 'cforms'); ?></span></div>
	            <div class="fh2" title="<?php _e('Choissisez un type champ dans la liste ci-dessous.', 'cforms'); ?>"><br /><span class="abbr"><?php _e('Type', 'cforms'); ?></span></div>
	            <div><img src="<?php echo $cforms_root; ?>/images/ic_required.png" title="<?php _e('Le champ est requis pour valider le formulaire.', 'cforms'); ?>" alt="" /><br /><?php _e('requis', 'cforms'); ?></div>
	            <div><img src="<?php echo $cforms_root; ?>/images/ic_email.png" title="<?php _e('Le champ devra contenir une adresse e-mail valide.', 'cforms'); ?>" alt="" /><br /><?php _e('e-mail', 'cforms'); ?></div>
	            <div><img src="<?php echo $cforms_root; ?>/images/ic_phone.png" title="<?php _e('Le champ devra contenir un téléphone valide.', 'cforms'); ?>" alt="" /><br /><?php _e('tél', 'cforms'); ?></div>
	            <div><img src="<?php echo $cforms_root; ?>/images/ic_clear.png" title="<?php _e('Efface la valeur du champ quand il a le focus.', 'cforms'); ?>" alt="" /><br /><?php _e('auto-clear', 'cforms'); ?></div>
	            <div><img src="<?php echo $cforms_root; ?>/images/ic_disabled.png" title="<?php _e('Grise le champ (le champ est entierment desactivé).', 'cforms'); ?>" alt="" /><br /><?php _e('desactivé', 'cforms'); ?></div>
	            <div><img src="<?php echo $cforms_root; ?>/images/ic_readonly.png" title="<?php _e('Le champ sera en lecture seule.', 'cforms'); ?>" alt="" /><br /><?php _e('lecture seule', 'cforms'); ?></div>
       		</div>
		</div>

   		<div id="allfields" class="groupWrapper">

                    <?php

                    $isTAF = (int)substr($cformsSettings['form'.$no]['cforms'.$no.'_tellafriend'],0,1);
					$ti = 1;

                    ### pre-check for verification field
                    $ccboxused=false;
                    $emailtoboxused=false;
                    $verificationused=false;
                    $captchaused=false;
                    $fileupload=false; ### only for hide/show options

                    $alternate=' ';
                    $fieldsadded = false;

                    for($i = 1; $i <= $field_count; $i++) {
                            $allfields[$i] = $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i];
                            if ( strpos($allfields[$i],'verification')!==false )    $verificationused = true;
                            if ( strpos($allfields[$i],'captcha')!==false )         $captchaused = true;
                            if ( strpos($allfields[$i],'emailtobox')!==false )      $emailtoboxused = true;
                            if ( strpos($allfields[$i],'ccbox')!==false )           $ccboxused = true;
                            if ( strpos($allfields[$i],'upload')!==false )          $fileupload = true; //needed for config
                    }

                    for($i = 1; $i <= $field_count; $i++) {

                        $field_stat = explode('$#$', $allfields[$i] );

                        ### default vals
                        $field_name = __('Nouveau champ', 'cforms');
                        $field_type = 'textfield';
                        $field_required = '0';
                        $field_emailcheck = '0';
                        $field_clear = '0';
                        $field_disabled = '0';
                        $field_readonly = '0';
                        $field_telcheck = '0';

                        if(sizeof($field_stat) >= 3) {
                            $field_name = stripslashes(htmlspecialchars($field_stat[0]));
                            $field_type = $allfields[$i] = $field_stat[1];
                            $field_required = $field_stat[2];
                            $field_emailcheck = $field_stat[3];
                            $field_clear = $field_stat[4];
                            $field_disabled = $field_stat[5];
                            $field_readonly = $field_stat[6];
                            $field_telcheck = $field_stat[7];
                        }
                        else if(sizeof($field_stat) == 1){
                            $cformsSettings['form'.$no]['cforms'.$no.'_count_field_' . $i] = __('New Field', 'cforms').'$#$textfield$#$0$#$0$#$0$#$0$#$0';
                            $fieldsadded = true;
                        }
                    	switch ( $field_type ) {
	                       case 'emailtobox':   $specialclass = 'style="background:#CBDDFE"'; break;
	                        case 'ccbox':       $specialclass = 'style="background:#D8FFCA"'; break;
	                        case 'verification':
	                        case 'captcha':     $specialclass = 'style="background:#D1B6E9"'; break;
	                        case 'textonly':    $specialclass = 'style="background:#E1EAE6"'; break;
	                        case 'fieldsetstart':
	                        case 'fieldsetend': $specialclass = 'style="background:#ECFEA5"'; break;
	                        default:            $specialclass = ''; break;
                        }

                    	$alternate = ($alternate=='')?' rowalt':''; ?>

                    	<div id="f<?php echo $i; ?>" class="groupItem<?php echo $alternate; ?>">

                        	<div class="itemContent">

	                            <span class="itemHeader<?php echo ($alternate<>'')?' altmove':''; ?>" title="<?php _e('Deplacez moi','cforms')?>"><?php echo (($i<10)?'0':'').$i; ?></span>

	                            <input tabindex="<?php echo $ti++ ?>" title="<?php _e('Please enter field definition', 'cforms'); ?>" class="inpfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_name" id="field_<?php echo($i); ?>_name" size="30" value="<?php echo ($field_type == 'fieldsetend')?'--':$field_name; ?>" /><span title="<?php echo $cforms_root.'/js/include/'; ?>"><input value="" type="submit" onfocus="this.blur()" class="wrench jqModal" title="<?php _e('Edit', 'cforms'); ?>"/></span><select tabindex="<?php echo $ti++ ?>" title="<?php _e('Pick a field type', 'cforms'); ?>" class="fieldtype selfld" <?php echo $specialclass; ?> name="field_<?php echo($i); ?>_type" id="field_<?php echo($i); ?>_type">

                                <option value="fieldsetstart" <?php echo($field_type == 'fieldsetstart'?' selected="selected"':''); ?>><?php _e('Debut du Fieldset', 'cforms'); ?></option>
                                <option value="textonly" <?php echo($field_type == 'textonly'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Label seulement (pas de champ)', 'cforms'); ?></option>
                                <option value="textfield" <?php echo($field_type == 'textfield'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Texte sur une ligne', 'cforms'); ?></option>
                                <option value="textarea" <?php echo($field_type == 'textarea'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Texte sur plusieurs lignes', 'cforms'); ?></option>
                                <option value="checkbox" <?php echo($field_type == 'checkbox'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Case à cocher', 'cforms'); ?></option>
                                <option value="checkboxgroup" <?php echo($field_type == 'checkboxgroup'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Groupe de cases', 'cforms'); ?></option>
                                <option value="radiobuttons" <?php echo($field_type == 'radiobuttons'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Boutons radio', 'cforms'); ?></option>
                                <option value="selectbox" <?php echo($field_type == 'selectbox'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Liste deroulante', 'cforms'); ?></option>
                                <option value="multiselectbox" <?php echo($field_type == 'multiselectbox'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Liste deroulant multiple', 'cforms'); ?></option>
                                <option value="upload" <?php echo($field_type == 'upload'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Upload de fichier', 'cforms'); ?></option>
                                <option value="maison" <?php echo($field_type == 'maison'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Liste des maisons', 'cforms'); ?></option>
                                <option<?php if ( $cformsSettings['global']['cforms_datepicker']<>'1' ) echo ' disabled="disabled" class="disabled"'; ?> value="datepicker" <?php echo($field_type == 'datepicker'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Date', 'cforms'); ?></option>
                                <option<?php if ( $cformsSettings['global']['cforms_datepicker']<>'1' ) echo ' disabled="disabled" class="disabled"'; ?> value="agedatepicker" <?php echo($field_type == 'agedatepicker'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Date (age)', 'cforms'); ?></option>
								<option<?php if ( $cformsSettings['global']['cforms_datepicker']<>'1' ) echo ' disabled="disabled" class="disabled"'; ?> value="datetimepicker" <?php echo($field_type == 'datetimepicker'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Date et heure', 'cforms'); ?></option>
                                <option value="pwfield" <?php echo($field_type == 'pwfield'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Mot de passe', 'cforms'); ?></option>
                                <option value="hidden" <?php echo($field_type == 'hidden'?' selected="selected"':''); ?>>&nbsp;&nbsp;<?php _e('Champ caché', 'cforms'); ?></option>
                                <option value="fieldsetend" <?php echo($field_type == 'fieldsetend'?' selected="selected"':''); ?>><?php _e('Fin du Fieldset', 'cforms'); ?></option>

							
                                <?php if ( class_exists('sg_subscribe') ) : ?>
                                    <option<?php echo $dis; ?> value="subscribe" <?php echo($field_type == 'subscribe'?' selected="selected"':''); ?>><?php _e('Subscribe To Comments', 'cforms'); ?></option>
                                <?php endif; ?>
                                <?php if ( function_exists('commentluv_setup') ) : ?>
                                    <option<?php echo $dis; ?> value="luv" <?php echo($field_type == 'luv'?' selected="selected"':''); ?>><?php _e('Comment Luv', 'cforms'); ?></option>
                                <?php endif; ?>

                            	</select><?php

                            echo '<input tabindex="'.($ti++).'" '.(($field_count<=1)?'disabled="disabled"':'').' class="'.(($field_count<=1)?'noxbutton':'xbutton').'" type="submit" name="DeleteField'.$i.'" value="" title="'.__('Supprimer le champ', 'cforms').'" alt="'.__('Supprimer le champ', 'cforms').'" onfocus="this.blur()"/>';

                            if( in_array($field_type,array('hidden','checkboxgroup', 'fieldsetstart','fieldsetend','ccbox','captcha','verification','textonly')) )
                                echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Non disponible.', 'cforms').'"/>';
                            else
                                echo '<input tabindex="'.($ti++).'" class="allchk fieldisreq chkfld" type="checkbox" title="'.__('champ requis', 'cforms').'" name="field_'.($i).'_required" value="required"'.($field_required == '1'?' checked="checked"':'').'/>';


                            if( ! in_array($field_type,array('textfield','youremail','friendsemail','email')) || $field_telcheck == '1')
                                echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Non disponible.', 'cforms').'"/>';
                            else
                                echo '<input tabindex="'.($ti++).'" class="allchk fieldisemail chkfld" type="checkbox" title="'.__('email requis', 'cforms').'" name="field_'.($i).'_emailcheck" value="required"'.($field_emailcheck == '1'?' checked="checked"':'').'/>';

                            if( ! in_array($field_type,array('textfield','youremail','friendsemail','email')) || $field_emailcheck == '1')
                                echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Non disponible.', 'cforms').'"/>';
                            else
                                echo '<input tabindex="'.($ti++).'" class="allchk fieldisemail chkfld" type="checkbox" title="'.__('téléphone requis', 'cforms').'" name="field_'.($i).'_telcheck" value="required"'.($field_telcheck == '1'?' checked="checked"':'').'/>';


                            if( ! in_array($field_type,array('pwfield','textarea','textfield','datepicker','yourname','youremail','friendsname','friendsemail','email','author','url','comment')) )
                                echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Non disponible.', 'cforms').'"/>';
                            else
                                echo '<input tabindex="'.($ti++).'" class="allchk fieldclear chkfld" type="checkbox" title="'.__('auto-clear', 'cforms').'" name="field_'.($i).'_clear" value="required"'.($field_clear == '1'?' checked="checked"':'').'/>';


                            if( ! in_array($field_type,array('pwfield','textarea','textfield','datepicker','checkbox','checkboxgroup','selectbox','multiselectbox','radiobuttons','upload')) )
                                echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Non disponible.', 'cforms').'"/>';
                            else
                                echo '<input tabindex="'.($ti++).'" class="allchk fielddisabled chkfld" type="checkbox" title="'.__('desactivé', 'cforms').'" name="field_'.($i).'_disabled" value="required"'.($field_disabled == '1'?' checked="checked"':'').'/>';


                            if( ! in_array($field_type,array('pwfield','textarea','textfield','datepicker','checkbox','checkboxgroup','selectbox','multiselectbox','radiobuttons','upload')) )
                                echo '<img class="chkno" src="'.$cforms_root.'/images/chkbox_grey.gif" alt="'.__('n/a', 'cforms').'" title="'.__('Non disponible.', 'cforms').'"/>';
                            else
                                echo '<input tabindex="'.($ti++).'" class="allchk fieldreadonly chkfld" type="checkbox" title="'.__('lecture seule', 'cforms').'" name="field_'.($i).'_readonly" value="required"'.($field_readonly == '1'?' checked="checked"':'').'/>';

                        ?></div> <!--itemContent-->

                    </div> <!--groupItem-->

            <?php   }   ### for loop
                    if( $fieldsadded )
                        update_option('cforms_settings',$cformsSettings);
            ?>
		</div> <!--groupWrapper-->

		<p class="addfieldbox">
            <input tabindex="<?php echo $ti++;?>" type="submit" name="AddField" class="allbuttons addbutton" title="<?php _e('Ajouter un autre champ', 'cforms'); ?>" value="** <?php _e('Ajouter', 'cforms'); ?> **" onfocus="this.blur()" onclick="javascript:document.mainform.action='#anchorfields';" />
        	<input tabindex="<?php echo $ti++;?>" type="text" name="AddFieldNo" value="1" class="addfieldno"/><?php _e('nouveau champ(s) à la position', 'cforms'); ?>
			<select tabindex="<?php echo $ti++;?>" name="AddFieldPos" class="addfieldno">
			<?php
	            for($i = 1; $i <= $field_count; $i++)
    	        	echo '<option value="'.$i.'">'.$i.'</option>';
			?>
            </select>

	        <input type="hidden" name="field_order" value="" />
	        <input type="hidden" name="field_count_submit" value="<?php echo($field_count); ?>" />
        </p>

	</fieldset>


    <?php if( $fileupload) : ?>
	<fieldset id="fileupload" class="cformsoptions">
			<div class="cflegend op-closed" id="p0" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Paramètres d\'upload', 'cforms')?>
            </div>

			<div class="cf-content" id="o0">
				<p>
					<?php //echo sprintf(__('Configure and double-check these settings in case you are adding a "<code>File Upload Box</code>" to your form (also see the <a href="%s" %s>Help!</a> for further information).', 'cforms'),'?page='.$plugindir.'/cforms-help.php#upload','onclick="setshow(19)"'); ?>
					<?php //echo sprintf(__('You may also want to verify the global, file upload specific  <a href="%s" %s>error messages</a>.', 'cforms'),'?page='.$plugindir.'/cforms-global-settings.php#upload','onclick="setshow(11)"'); ?>
				</p>

			    <?php
			    $temp = explode( '$#$',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_dir'])) );
			    $fileuploaddir = $temp[0];
			    $fileuploaddirurl = $temp[1];
				if ( $fileupload && !file_exists($fileuploaddir) ) {
			        echo '<div class="updated fade"><p>' . __('Impossible de trouver le <strong>dossier</strong> ! Verifiez qu\'il existe !', 'cforms' ) . '</p></div>';
			    }
				?>
				<table class="form-table">
				<tr class="ob space15">
					<td class="obL"><label for="cforms_upload_dir"><strong><?php _e('Dossier (chemin absolu)', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_upload_dir" name="cforms_upload_dir" value="<?php echo $fileuploaddir; ?>"/> <?php _e('e.g. /home/user/www/wp-content/my-upload-dir', 'cforms') ?></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_dir_url"><strong><?php _e('URL du dossier (chemin relatif)', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_upload_dir_url" name="cforms_upload_dir_url" value="<?php echo $fileuploaddirurl; ?>"/> <?php _e('e.g. /wp-content/my-upload-dir', 'cforms') ?></td>
				</tr>

				<tr class="ob space10">
					<td class="obL"><label for="cforms_upload_noid"><strong><?php _e('Prefixer par un tracking ID', 'cforms') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_upload_noid" name="cforms_upload_noid" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_noid']=='1') echo "checked=\"checked\""; ?>/></td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_upload_ext"><strong><?php _e('Extensions autorisées', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_upload_ext" name="cforms_upload_ext" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_ext'])); ?>"/> <?php _e('e.g. [pdf, doc, docx, odt] [vide=tous les fichiers autorisés]', 'cforms') ?></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_upload_size"><strong><?php _e('Taille maximum<br />en Ko', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" id="cforms_upload_size" name="cforms_upload_size" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_upload_size'])); ?>"/></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_noattachments"><strong><?php _e('Ne pas envoyer les pieces jointes par e-mail', 'cforms') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_noattachments" name="cforms_noattachments" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_noattachments']=='1') echo "checked=\"checked\""; ?>/><br /><?php echo sprintf(__('<u>Note</u>: Les pièces jointes sont stockées sur le serveur. Elles peuvent être récupérées sur la page de <a href="%s" %s>gestion</a>.', 'cforms'),'?page='. $plugindir.'/cforms-database.php','onclick="setshow(14)"'); ?></td>
				</tr>
				</table>
			</div>
		</fieldset>
    <?php endif; ?>


		<fieldset class="cformsoptions" id="anchormessage">
			<div class="cflegend op-closed" id="p1" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Messages, texte et bouton', 'cforms')?>
            </div>

			<div class="cf-content" id="o1">
				<p><?php echo sprintf(__('Messages affichés à l\'utilisateur après avoir soumis le formulaire. Ces messages sont spécifiques au formulaire.')); ?></p>

				<table class="form-table">

				<tr class="ob">
					<td class="obL"><label for="cforms_submit_text"><strong><?php _e('Bouton de validation', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_submit_text" id="cforms_submit_text" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_submit_text']));  ?>" /></td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_working"><strong><?php _e('Message d\'attente', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_working" id="cforms_working" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_working']));  ?>" /></td>
				</tr>
				<tr class="ob space15">
					<td class="obL"><label for="cforms_required"><strong><?php _e('Label "requis"', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_required" id="cforms_required" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_required'])); ?>"/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_emailrequired"><strong><?php _e('Label "e-mail requis"', 'cforms'); ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_emailrequired" id="cforms_emailrequired" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_emailrequired'])); ?>"/></td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_success"><?php _e('<strong>Message de succès</strong><br />quand le formulaire est correctement remplis', 'cforms'); ?></label></td>
					<td class="obR">
                    	<table><tr>
						<td><textarea class="resizable" rows="80px" cols="200px" name="cforms_success" id="cforms_success"><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_success'])); ?></textarea></td>
						<td><input class="allchk" type="checkbox" id="cforms_popup1" name="cforms_popup1" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],0,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_popup1"><?php _e('Popup', 'cforms'); ?></label></td>
                    	</tr></table>
					</td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_failure"><?php _e('<strong>Message echec</strong><br />champs manquants ou mal remplis<br />(mauvais format)', 'cforms'); ?></label></td>
					<td class="obR">
                    	<table><tr>
						<td><textarea class="resizable" rows="80px" cols="200px" name="cforms_failure" id="cforms_failure" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_failure'])); ?></textarea></td>
						<td><input class="allchk" type="checkbox" id="cforms_popup2" name="cforms_popup2" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_popup'],1,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_popup2"><?php _e('Popup', 'cforms'); ?></label></td>
                    	</tr></table>
					</td>
				</tr>
				<tr class="ob space15">
					<td class="obL"><label for="cforms_showposa"><strong><?php _e('Afficher les messages', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_showposa" name="cforms_showposa" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],0,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_showposa"><?php _e('Au-dessus', 'cforms'); ?></label><br />
						<input class="allchk" type="checkbox" id="cforms_showposb" name="cforms_showposb" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],1,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_showposb"><?php _e('Au-dessous', 'cforms'); ?></label>
					</td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_jump"><strong><?php _e('Aller à l\'erreur', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_jump" name="cforms_jump" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],4,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_jump"><?php _e('(Javascript)', 'cforms'); ?></label>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_errorLI"><strong><?php _e('Message d\'erreur customisé', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_errorLI" name="cforms_errorLI" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],2,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_errorLI"><?php _e('Change l\'affichage des erreurs', 'cforms'); ?></label>
					</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_errorINS"><strong><?php _e('Messages d\'erreur attachés', 'cforms'); ?></strong></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_errorINS" name="cforms_errorINS" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_showpos'],3,1)=="y") echo "checked=\"checked\""; ?>/><label for="cforms_errorINS"><?php _e('Les messages d\'erreur liés à un champ seront affichés à côté', 'cforms'); ?></label>
					</td>
				</tr>
		 		</table>
			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="anchoremail">
			<div class="cflegend op-closed" id="p2" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Paramètre de base d\'administration', 'cforms')?>
            </div>

			<div class="cf-content" id="o2">
				<p><?php echo sprintf(__('Les deux formats, %s et %s, sont valides, mais verifiez que votre serveur mail les acceptent.', 'cforms'),'"<strong>xx@yy.zz</strong>"','"<strong>abc &lt;xx@yy.zz&gt;</strong>"') ?></p>
				<p><?php _e('L\'adresse FROM: par défaut est basé sur le nom et l\'adresse du site. Vous pouvez le changer mais ce n\'est pas conseillé. ', 'cforms') ?></p>

				<table class="form-table">

                <tr class="ob">
                    <td class="obL"><strong><?php _e('Options de bases:', 'cforms') ?></strong></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_dontclear" name="cforms_dontclear" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_dontclear']) echo "checked=\"checked\""; ?>/><label for="cforms_dontclear"><?php echo sprintf(__('%sNe pas effacer%s les champs après la validation', 'cforms'),'<strong>','</strong>'); ?></label>
		 			</td>
	  			</tr>

				<?php if( $cformsSettings['global']['cforms_showdashboard'] == '1' ) : ?>
					<tr class="ob space10">
						<td class="obL"></td>
						<td class="obR"><input class="allchk" type="checkbox" id="cforms_dashboard" name="cforms_dashboard" <?php if($o=$cformsSettings['form'.$no]['cforms'.$no.'_dashboard']=='1') echo "checked=\"checked\""; ?>/><label for="cforms_dashboard"><?php echo sprintf(__('Afficher les nouvelles entrées sur le %sdashboard%s', 'cforms'),'<strong>','</strong>') ?></label></td>
		  			</tr>
				<?php endif; ?>

				<tr class="ob">
					<td class="obL"></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_hide" name="cforms_hide" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_hide']) echo "checked=\"checked\""; ?>/><label for="cforms_hide"><?php echo sprintf(__('%sCacher le formulaire%s après la validation', 'cforms'),'<strong>','</strong>'); ?></label>
		 			</td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL"><strong><?php _e('Limite d\'inscription', 'cforms'); ?></strong></td>
					<td class="obR"><input type="text" id="cforms_maxentries" name="cforms_maxentries" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_maxentries'])); ?>"/><label for="cforms_maxentries"><?php _e('<u>Nombre total</u> d\'inscriptions acceptées [<strong>vide ou 0 (zéro) = off</strong>]', 'cforms') ?></label></td>
				</tr>

				<tr class="ob">
					<td class="obL" style="padding-top:7px"><strong><?php _e('Date de début', 'cforms'); ?></strong></td>
					<?php $date = explode(' ',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_startdate'])) ); ?>
					<td class="obR">
                    	<input type="text" class="cf_date" id="cforms_startdate" name="cforms_startdate" value="<?php echo $date[0]; ?>"/>
                        <input type="text" id="cforms_starttime" name="cforms_starttime" value="<?php echo $date[1]; ?>"/><a class="cf_timebutt1" href="javascript:void(0);"><img src="<?php echo $cforms_root; ?>/images/clock.gif" alt="" title="<?php _e('Heure.', 'cforms') ?>"/></a>
						<label for="cforms_startdate"><?php
						$dt='x';
                        if( strlen($cformsSettings['form'.$no]['cforms'.$no.'_startdate'])>1 ):
                            $dt = cf_make_time(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_startdate'])) - time();
							if( $dt>0 ):
	                                echo __('Le formulaire sera disponible dans ', 'cforms').sec2hms($dt);
	                            else:
	                                echo __('Le formulaire est actif.', 'cforms');
							endif;
						endif;
                        ?>
                        </label>
                    </td>
				</tr>

				<tr class="ob">
					<td class="obL" style="padding-top:7px"><strong><?php _e('Date de fin', 'cforms'); ?></strong></td>
					<?php $date = explode(' ',stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_enddate'])) ); ?>
					<td class="obR">
                    	<input type="text" class="cf_date" id="cforms_enddate" name="cforms_enddate" value="<?php echo $date[0]; ?>"/>
                        <input type="text" id="cforms_endtime" name="cforms_endtime" value="<?php echo $date[1]; ?>"/><a class="cf_timebutt2" href="javascript:void(0);"><img src="<?php echo $cforms_root; ?>/images/clock.gif" alt="" title="<?php _e('Heure.', 'cforms') ?>"/></a>
						<label for="cforms_startdate"><?php
                        if( $dt=='x' && strlen($cformsSettings['form'.$no]['cforms'.$no.'_enddate'])>1 ):
                            $dt = cf_make_time(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_enddate'])) - time();
							if( $dt>0 ):
	                                echo __('Le formulaire sera fermé dans ', 'cforms').sec2hms($dt);
	                            else:
	                                echo __('Le formulaire n\'est plus disponible.', 'cforms');
							endif;
						endif;
                        ?></label>
                    </td>
				</tr>

				<tr class="ob">
	            	<td class="obL"><label for="cforms_limitagemin"><strong><?php _e('Age minimum', 'cforms'); ?></strong></label></td>
	                <td class="obR"><input type="text" id="cforms_limitagemin" name="cforms_limitagemin" value="<?php echo ($cformsSettings['form'.$no]['cforms'.$no.'_limitagemin']);  ?>"/> ans (<u>chiffre entier positif uniquement</u>)</td>
				</tr>
				<tr class="ob">
	            	<td class="obL"><label for="cforms_limitagemax"><strong><?php _e('Age maximum', 'cforms'); ?></strong></label></td>
	                <td class="obR"><input type="text" id="cforms_limitagemax" name="cforms_limitagemax" value="<?php echo ($cformsSettings['form'.$no]['cforms'.$no.'_limitagemax']);  ?>"/> ans (<u>chiffre entier positif uniquement</u>)</td>
				</tr>
				<tr class="ob">
	            	<td class="obL"><label for="cforms_limitage"><strong><?php _e('Age calculé à partir de', 'cforms'); ?></strong></label></td>
	                <td class="obR"><input class="cf_date" type="text" id="cforms_limitage" name="cforms_limitage" value="<?php echo ($cformsSettings['form'.$no]['cforms'.$no.'_limitage']);  ?>"/></td>
				</tr>
				
				<?php if( $cformsSettings['form'.$no]['cforms'.$no.'_maxentries'] <> '' || $cformsSettings['form'.$no]['cforms'.$no.'_startdate'] <> '' || $cformsSettings['form'.$no]['cforms'.$no.'_enddate'] <> '' ) : ?>
				<tr class="ob">
	            	<td class="obL"><label for="cforms_limittxt"><strong><?php _e('Message de limitation', 'cforms'); ?></strong></label></td>
	                <td class="obR"><table><tr><td><textarea class="resizable" rows="80px" cols="200px" name="cforms_limittxt" id="cforms_limittxt"><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_limittxt'])); ?></textarea></td></tr></table></td>
				</tr>
				<?php endif; ?>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_redirect"><?php _e('Options de<br /><strong>redirection</strong>:', 'cforms'); ?></label></td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_redirect" name="cforms_redirect" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_redirect']) echo "checked=\"checked\""; ?>/><label for="cforms_redirect"><?php _e('Page de confirmation alternative (redirection)', 'cforms'); ?></label><br />
						<input name="cforms_redirect_page" id="cforms_redirect_page" value="<?php echo ($cformsSettings['form'.$no]['cforms'.$no.'_redirect_page']);  ?>" />
		 			</td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

			</table>
			</div>
		</fieldset>
	

		<fieldset class="cformsoptions" id="emailoptions">
			<div class="cflegend op-closed" id="p3" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Paramètres e-mail d\'administration', 'cforms')?>
            </div>

			<div class="cf-content" id="o3">
				<p><?php _e('Les e-mails peuvent généralement être envoyés en texte ou en HTML. La partie TXT est <strong>requise</strong>, celle HTML est <strong>optionelle</strong>.', 'cforms'); ?></p>
				<p><?php echo sprintf(__('Ci-dessous vous trouverez les paramètres pour <strong>la partie TXT</strong> ainsi que la <strong>partie HTML (optionelle)</strong> du message. Les deux zones acceptent les <strong>variables prédéfinies</strong> ou <strong>les données des champs du formualaire</strong>. <a href="%s" %s>Regardez la documentation sur la page d\'aide</a> (avec des exemples HTML).', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></p>

				<table class="form-table">
                <tr class="ob space15">
                    <td class="obL"></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_emailoff" name="cforms_emailoff" <?php if($cformsSettings['form'.$no]['cforms'.$no.'_emailoff']=='1') echo "checked=\"checked\""; ?>/><label for="cforms_emailoff"><?php echo sprintf(__('%sDesactiver%s l\'e-mail admin', 'cforms'),'<strong>','</strong>') ?></label></td>
                </tr>
				</table>

				<table class="form-table<?php if( $cformsSettings['form'.$no]['cforms'.$no.'_emailoff']=='1' ) echo " hidden"; ?>">
                <tr class="ob space15">
					<td class="obL"><label for="cforms_fromemail"><strong><?php _e('FROM: e-mail adresse', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_fromemail" id="cforms_fromemail" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_fromemail'])); ?>" /></td>
				</tr>

				<tr class="ob space15">
					<td class="obL"><label for="cforms_email"><strong><?php _e('Adresse e-mail de l\'administrateur', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_email" id="cforms_email" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_email'])); ?>" /></td>
				</tr>

				<tr class="ob">
					<td class="obL"><label for="cforms_bcc"><strong><?php _e('Adresse(s) CCI', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_bcc" id="cforms_bcc" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_bcc'])); ?>" />(copie carbone invisible)</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_subject"><strong><?php _e('Objet de l\'e-mail', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_subject" id="cforms_subject" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_subject'])); ?>" /> <?php echo sprintf(__('<a href="%s" %s>Variables</a> autorisées.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
				</tr>

                <tr class="ob">
                    <td class="obL"></td>
                    <td class="obR">
						<?php $p = ((int)$cformsSettings['form'.$no]['cforms'.$no.'_emailpriority']>0)?(int)$cformsSettings['form'.$no]['cforms'.$no.'_emailpriority']:3; ?>
						<select name="emailprio" id="emailprio"><?php for ($i=1;$i<=5;$i++) echo '<option'.(($i==$p)?' selected="selected"':'').'>' .$i. '</option>'; ?></select>
                        <label for="emailprio"><?php echo sprintf(__('%sPriorité%s de l\'e-mail (1 = Haute, 3 = Normale, 5 = Basse)', 'cforms'),'<strong>','</strong>') ?></label>
                    </td>
                </tr>

				<tr class="ob space20">
					<td class="obL" style="padding-bottom:0">&nbsp;</td>				
					<td class="obR" style="padding-bottom:0">
						<input type="submit" class="allbuttons" name="cforms_resetAdminMsg" id="cforms_resetAdminMsg" value="<?php _e('Restaurer le message par défaut.', 'cforms') ?>" onclick="javascript:document.mainform.action='#emailoptions';" />
		 			</td>
				</tr>
				
				<tr class="ob">
					<td class="obL" style="padding-bottom:0">
						<label for="cforms_header"><?php _e('<strong>TXT message</strong><br />(En-tête)', 'cforms') ?></label>
					</td>
					<td class="obR" style="padding-bottom:0">
                    	<table><tr>
						<td><textarea class="resizable" rows="80px" cols="200px" name="cforms_header" id="cforms_header" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_header'])); ?></textarea></td>
						<td><?php echo sprintf(__('<a href="%s" %s>Variables</a> autorisées.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
                    	</tr></table>
		 			</td>
				</tr>
				<tr class="ob">
					<td class="obL" style="padding-top:0"><?php _e('(Pied)','cforms')?></td>
					<td class="obR" style="padding-top:0"><input class="allchk" type="checkbox" id="cforms_formdata_txt" name="cforms_formdata_txt" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],0,1)=='1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_txt"><?php _e('<strong>Inclure les données</strong>  du formulaire à la fin de l\'e-mail', 'cforms') ?></label></td>
				</tr>
				<tr class="ob">
					<td class="obL" style="padding-top:0">&nbsp;</td>
					<td class="obR" style="padding-top:0"><input type="text" name="cforms_space" id="cforms_space" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_space'])); ?>" /><label for="cforms_space"><?php _e('(# caractères) : espace entre label et données, pour la version TXT seulement', 'cforms') ?></label></td>
				</tr>

				<tr class="ob space20">
					<td class="obL"><label for="cforms_admin_html"><strong><?php _e('Autoriser HTML', 'cforms') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_admin_html" name="cforms_admin_html" <?php if($o=substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],2,1)=='1') echo "checked=\"checked\""; ?>/></td>
				</tr>

				<tr class="ob <?php if( !$o=='1' ) echo "hidden"; ?>">
					<td class="obL" style="padding-bottom:0"><label for="cforms_header_html"><?php _e('<strong>Message HTML</strong><br />(En-tête)', 'cforms') ?></label></td>
					<td class="obR" style="padding-bottom:0">
                    	<table><tr>
						<td><textarea class="resizable" rows="80px" cols="200px" name="cforms_header_html" id="cforms_header_html" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_header_html'])); ?></textarea></td>
						<td><?php echo sprintf(__('<a href="%s" %s>Variables</a> autorisées.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
                    	</tr></table>
		 			</td>
				</tr>
				<tr class="ob <?php if( !$o=='1' ) echo "hidden"; ?>">
					<td class="obL" style="padding-top:0"><?php _e('(Pied)','cforms')?></td>
					<td class="obR" style="padding-top:0"><input class="allchk" type="checkbox" id="cforms_formdata_html" name="cforms_formdata_html" <?php if(substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],1,1)=='1') echo "checked=\"checked\""; ?>/><label for="cforms_formdata_html"><?php _e('<strong>Inclure les données</strong>  du formulaire à la fin de l\'e-mail', 'cforms') ?></label></td>
				</tr>
				
				</table>
			</div>
		</fieldset>


		<fieldset class="cformsoptions <?php if( !$ccboxused ) echo "hidden"; ?>" id="cc">
			<div class="cflegend op-closed" id="p4" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Paramètres CC', 'cforms')?>
            </div>

			<div class="cf-content" id="o4">
				<p><?php _e('This is the subject of the CC email that goes out the user submitting the form and as such requires the <strong>CC:</strong> field in your form definition above.', 'cforms') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL"><label for="cforms_ccsubject"><strong><?php _e('Subject CC', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_ccsubject" id="cforms_ccsubject" value="<?php $t=explode('$#$',$cformsSettings['form'.$no]['cforms'.$no.'_csubject']); echo stripslashes(htmlspecialchars($t[1])); ?>" /> <?php echo sprintf(__('<a href="%s" %s>Variables</a> allowed.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
				</tr>
				</table>

			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="autoconf">
			<div class="cflegend op-closed" id="p5" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Message de confirmation', 'cforms')?>
            </div>

			<div class="cf-content" id="o5">
				<p><?php _e('Paramètres du message de confirmation envoyé après la validation du formulaire. (Pour un formulaire en plusieurs parties, il est conseillé de n\'activer cette option que sur le dernier formulaire)', 'cforms') ?></p>

				<table class="form-table">
				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR">
						<input class="allchk" type="checkbox" id="cforms_confirm" name="cforms_confirm" <?php if($o=$cformsSettings['form'.$no]['cforms'.$no.'_confirm']=="1") echo "checked=\"checked\""; ?>/><label for="cforms_confirm"><strong><?php _e('Activer la confirmation', 'cforms') ?></strong></label><br />
						<a class="infobutton" href="#" name="it8"><?php _e('Note', 'cforms'); ?></a>
		 			</td>
				</tr>
				
				<tr id="it8" class="infotxt"><td>&nbsp;</td><td class="ex"><?php _e('<strong>Attention:</strong> il doit y avoir au moins un champ e-mail dans le formulaire (ou le formulaire parent). S\'il y en a plusieurs, seul la première adresse est utilisée', 'cforms') ?></td></tr>

                <?php if( $o=="1" ) :?>
				<tr class="ob">
					<td class="obL"><label for="cforms_csubject"><strong><?php _e('Objet', 'cforms') ?></strong></label></td>
					<td class="obR"><input type="text" name="cforms_csubject" id="cforms_csubject" value="<?php $t=explode('$#$',$cformsSettings['form'.$no]['cforms'.$no.'_csubject']); echo stripslashes(htmlspecialchars($t[0])); ?>" /> <?php echo sprintf(__('<a href="%s" %s>Variables</a> autorisées.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
				</tr>
				<tr class="ob space20">
					<td class="obL" style="padding-bottom:0">&nbsp;</td>				
					<td class="obR" style="padding-bottom:0">
						<input type="submit" class="allbuttons" name="cforms_resetAutoCMsg" id="cforms_resetAutoCMsg" value="<?php _e('Message par défaut', 'cforms') ?>" onclick="javascript:document.mainform.action='#autoconf';"/>
		 			</td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cmsg"><strong><?php _e('Message TXT', 'cforms') ?></strong></label></td>
					<td class="obR">
                    	<table><tr>
						<td><textarea class="resizable" rows="80px" cols="200px" name="cforms_cmsg" id="cforms_cmsg" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_cmsg'])); ?></textarea></td>
						<td><?php echo sprintf(__('<a href="%s" %s>Variables</a> autorisées.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
                    	</tr></table>
		 			</td>
				</tr>
				<tr class="ob space15">
					<td class="obL"><label for="cforms_user_html"><strong><?php _e('Autoriser HTML', 'cforms') ?></strong></label></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_user_html" name="cforms_user_html" <?php if($o2=substr($cformsSettings['form'.$no]['cforms'.$no.'_formdata'],3,1)=='1') echo "checked=\"checked\""; ?>/></td>
				</tr>
				<tr class="ob">
					<td class="obL"><label for="cforms_cmsg_html"><strong><?php _e('Message HTML', 'cforms') ?></strong></label></td>
					<td class="obR">
                    	<table><tr>
						<td><textarea class="resizable" rows="80px" cols="200px" name="cforms_cmsg_html" id="cforms_cmsg_html" ><?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_cmsg_html'])); ?></textarea></td>
						<td><?php echo sprintf(__('<a href="%s" %s>Variables</a> autorisées.', 'cforms'),'?page='. $plugindir.'/cforms-help.php#variables','onclick="setshow(23)"'); ?></td>
                    	</tr></table>
		 			</td>
				</tr>

                <?php endif; ?>

				</table>
			</div>
		</fieldset>


		<fieldset class="cformsoptions" id="multipage">
			<div class="cflegend op-closed" id="p29" title="<?php _e('Ouvrir/Fermer', 'cforms') ?>">
            	<a class="helptop" href="#top"><?php _e('top', 'cforms'); ?></a><div class="blindplus"></div><?php _e('Formulaire multi-pages/multi-parties', 'cforms')?>
            </div>

			<div class="cf-content" id="o29">
				<p><?php _e('Si l\'option est activée, de nouveaux paramètres vont être affichés.', 'cforms'); ?> <label for="cforms_mp_form"><?php _e('Ce formulaire fait partie d\'une serie de formulaires:', 'cforms') ?></label> <input class="allchk" type="checkbox" id="cforms_mp_form" name="cforms_mp_form" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form']=='1' ) echo "checked=\"checked\""; ?>/></p>

				<?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_form'] ) : ?>

				<table class="form-table">
				<tr class="ob">
					<td class="obL"><strong><?php _e('Email &amp; Tracking', 'cforms') ?></strong></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_email" name="cforms_mp_email" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_email']=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_mp_email"><?php _e('Supprime l\'admin e-mail et le tracking pour ce formulaire', 'cforms') ?></label></td>
				</tr>

				<tr class="ob">
					<td class="obL"><strong><?php _e('Premier', 'cforms') ?></strong></td>
					<td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_first" name="cforms_mp_first" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_first']=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_mp_first"><?php _e('Ce formulaire est le premier d\'une serie', 'cforms') ?></label></td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

                <tr class="ob">
					<td class="obL"><strong><?php _e('Bouton annuler', 'cforms') ?></strong></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_reset" name="cforms_mp_reset" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_reset']=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_mp_reset"><?php _e('Ajoute un bouton <i>annuler</i> à ce formulaire.', 'cforms') ?></label></td>
				</tr>

				<tr class="ob">
					<td class="obL"><strong><?php _e('Texte', 'cforms') ?></strong></td>
					<td class="obR"><input type="text" id="cforms_mp_resettext" name="cforms_mp_resettext" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_resettext'])); ?>"/><label for="cforms_mp_resettext"><?php _e('Texte du bouton <i>annuler</i>', 'cforms') ?></label></td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

                <tr class="ob">
					<td class="obL"><strong><?php _e('Bouton retour', 'cforms') ?></strong></td>
                    <td class="obR"><input class="allchk" type="checkbox" id="cforms_mp_back" name="cforms_mp_back" <?php if( $cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_back']=='1' ) echo "checked=\"checked\""; ?>/><label for="cforms_mp_back"><?php _e('Ajoute un bouton <i>retour</i> (retourne au formulaire précédent)', 'cforms') ?></label></td>
				</tr>

				<tr class="ob">
					<td class="obL"><strong><?php _e('Texte', 'cforms') ?></strong></td>
					<td class="obR"><input type="text" id="cforms_mp_backtext" name="cforms_mp_backtext" value="<?php echo stripslashes(htmlspecialchars($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_backtext'])); ?>"/><label for="cforms_mp_backtext"><?php _e('Texte du bouton <i>retour<i/>', 'cforms') ?></label></td>
				</tr>

				<tr class="obSEP"><td colspan="2"></td></tr>

				<tr class="ob">
					<td class="obL">&nbsp;</td>
					<td class="obR">
					<?php
	                    $formlistbox = ' <select id="picknextform" name="cforms_mp_next"'. ($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_last']=='1'?' disabled="disabled"':'') .'>';
	                    for ($i=1; $i<=$FORMCOUNT; $i++){
	                        $j   = ( $i > 1 )?$i:'';
	                        $sel = ($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next']==$cformsSettings['form'.$j]['cforms'.$j.'_fname'])?' selected="selected"':'';
	                        $formlistbox .= '<option '.$sel.'>'.$cformsSettings['form'.$j]['cforms'.$j.'_fname'].'</option>';
	                    }
                        $formlistbox .= '<option style="background:#F2D7E0;" value="-1" '.(($cformsSettings['form'.$no]['cforms'.$no.'_mp']['mp_next']=='-1')?' selected="selected"':'').'>'.__('* stop here (last form) *', 'cforms').'</option>';
                        $formlistbox .= '</select>';
                        echo $formlistbox;
                    ?>
                        <?php _e('Choissisez le formulaire suivant', 'cforms') ?>
		 			</td>
				</tr>
				</table>
				<?php endif; ?>
			</div>
		</fieldset>


	    <div class="cf_actions" id="cf_actions" style="display:none;">
			<input id="cfbar-addbutton" class="allbuttons addbutton" type="submit" name="addbutton" value=""/>
			<input id="cfbar-dupbutton" class="allbuttons dupbutton" type="submit" name="dupbutton" value=""/>
			<input id="cfbar-delbutton" class="allbuttons deleteall" type="submit" name="delbutton" value=""/>
			<input id="preset" type="button" class="jqModalInstall allbuttons" name="<?php echo $cforms_root; ?>/js/include/" value=""/>
			<input id="backup" type="button" class="jqModalBackup allbuttons" name="backup"  value=""/>
			<input id="cfbar-SubmitOptions" type="submit" name="SubmitOptions" class="allbuttons updbutton formupd" value="" />
	    </div>

		</form>

	<?php cforms_footer(); ?>
</div>

<?php

add_action('admin_footer', 'insert_cfmodal');
function insert_cfmodal(){
	global $cforms_root,$noDISP;
?>
	<div class="jqmWindow" id="cf_editbox">
		<div class="cf_ed_header jqDrag"><?php _e('Paramètres du champ','cforms'); ?></div>
		<div class="cf_ed_main">
			<div id="cf_target"></div>
			<div class="controls"><a href="#" id="ok" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_ok.gif" alt="<?php _e('OK', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><a href="#" id="cancel" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Annuler', 'cforms') ?>"/></a></div>
		</div>
	</div>
	<div class="jqmWindow" id="cf_installbox">
		<div class="cf_ed_header jqDrag"><?php _e('cforms Out-Of-The-Box Form Repository','cforms'); ?></div>
		<div class="cf_ed_main">
			<form action="" name="installpreset" method="post">
				<div id="cf_installtarget"></div>
				<div class="controls"><a href="#" id="okInstall" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_ok.gif" alt="<?php _e('Install', 'cforms') ?>" title="<?php _e('OK', 'cforms') ?>"/></a><a href="#" id="cancelInstall" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></div>
				<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>"/>
			</form>
		</div>
	</div>
	<div class="jqmWindow" id="cf_backupbox">
		<div class="cf_ed_header jqDrag"><?php _e('Backup &amp; Restore Form Settings','cforms'); ?></div>
		<div class="cf_ed_main_backup">
			<form enctype="multipart/form-data" action="" name="backupform" method="post">
				<div class="controls">

	                <input type="submit" id="savecformsdata" name="savecformsdata" class="allbuttons backupbutton"  value="<?php _e('Backup current form settings', 'cforms'); ?>" onclick="javascript:jQuery('#cf_backupbox').jqmHide();" /><br />
	                <label for="upload"><?php _e(' or restore previously saved settings:', 'cforms'); ?></label>
	                <input type="file" id="upload" name="importall" size="25" />
	                <input type="submit" name="uploadcformsdata" class="allbuttons restorebutton" value="<?php _e('Restore from file', 'cforms'); ?>" onclick="javascript:jQuery('#cf_backupbox').jqmHide();" />

                    <p class="cancel"><a href="#" id="cancel" class="jqmClose"><img src="<?php echo $cforms_root; ?>/images/dialog_cancel.gif" alt="<?php _e('Cancel', 'cforms') ?>" title="<?php _e('Cancel', 'cforms') ?>"/></a></p>

        	    </div>
				<input type="hidden" name="noSub" value="<?php echo $noDISP; ?>"/>
			</form>
		</div>
	</div>
<?php
}
?>