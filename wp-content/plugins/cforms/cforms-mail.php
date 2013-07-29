<?php

###
### please see cforms.php for more information
###

$CFfunctionsC = dirname(dirname(__FILE__)).$cformsSettings['global']['cforms_IIS'].'cforms-custom'.$cformsSettings['global']['cforms_IIS'].'my-functions.php';
$CFfunctions = dirname(__FILE__).$cformsSettings['global']['cforms_IIS'].'my-functions.php';
if ( file_exists($CFfunctionsC) )
    include_once($CFfunctionsC);
else if ( file_exists($CFfunctions) )
    include_once($CFfunctions);

require_once (dirname(__FILE__) . '/lib_validate.php');
	
// print_r($_SERVER);
include_once('lib_aux.php');

function isOk($var){
	return (isset($var) && !empty($var));
}


?>

<div class="wrap" id="top">

    <div id="icon-cforms-mail" class="icon32"><br/></div><h2><?php _e('Envoi d\'e-mail','cforms')?></h2>

<?php

if(isOk($_POST['a']) && isOk($_POST['content']) && cforms_is_email($_POST['a']) ){

	$to = $_POST['a'];
	$frommail = check_cust_vars(stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fromemail']),$track,$no);
	$replyto = preg_replace( array('/;|#|\|/'), array(','), stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_email']) );
	$subject = stripslashes($_POST['obj']);
	$body = stripslashes(nl2br($_POST['content']));
	$bcc = $_POST['cci'];
	$cc = $_POST['cc'];	
	
	$mail = new cf_mail($no,$frommail,$to,$frommail);
	$mail->subj  = $subject;
	$mail->char_set = 'utf-8';
	$mail->add_bcc($bcc);
	$mail->add_cc($cc);
	$mail->body = $body;
	$mail->is_html(true);
	
	
	if ( $smtpsettings[0]=='1' ){
		$sent = cforms_phpmailer( $no, $frommail, $replyto, $to, $subject, $body, '', '', '', 'ac' );
	}else{
		if ( $mail->html_show_ac ) {
			$mail->is_html(true);
			$mail->body     =  $cformsSettings['global']['cforms_style_doctype'] .$mail->eol."<html xmlns=\"http://www.w3.org/1999/xhtml\">".$mail->eol."<head><title></title></head>".$mail->eol."<body {$cformsSettings['global']['cforms_style']['body']}>".$htmlmessage.( $mail->f_html?$mail->eol.$htmlformdata:'').$mail->eol."</body></html>".$mail->eol;
			$mail->body_alt  =  $body . ($mail->f_txt?$mail->eol.$formdata:'');
		}
		else
			$mail->body     =  $body . ($mail->f_txt?$mail->eol.$formdata:'');

		$sent = $mail->send();
	}
	
	
	// if($mail->send()){
	if($sent == 1){
		echo '<div id="message" class="updated fade">
				<p>
					<strong>Le message a bien été envoyé !</strong>
				</p>
			</div>';
		$_POST = array();
		$_REQUEST = array();
	}else{
			echo '<div id="message" class="error fade">
			<p>
				<strong>Le message n\'a pas été envoyé ! '.$sent.'</strong>
			</p>
		</div>';	
	}

}else if(isOk($_POST['a']) && !cforms_is_email($_POST['a'])){

	echo '<div id="message" class="error fade">
			<p>
				<strong>Veuillez donner une adresse e-mail valide.</strong>
			</p>
		</div>';
	
}else if(isset($_POST['content'])){

	echo '<div id="message" class="error fade">
			<p>
				<strong>Veuillez remplir tous les champs obligatoires.</strong>
			</p>
		</div>';

}

?>
	<form action="<?php echo $_SERVER['REQUEST_URI']; ?>" class="cform " method="post" id="cformemail" >	
		<table class="cf-ol">
			<tr  class="">
				<td><label for="a"><span>À :</span></label></td>
				<td><input type="text" name="a" id="a" class="single fldrequired" size="30" value="<?php echo (isOk($_POST['a']))?$_POST['a']:(isset($_REQUEST['mail'])? rawurldecode($_REQUEST['mail']):NULL); ?>"/>&nbsp;<span class="reqtxt">(obligatoire)</span></td>
			</tr>
			<tr  class="">
				<td><label for="cc"><span>Cc :</span></label></td>
				<td><input type="text" name="cc" id="cc" class="single " value="<?php echo (isOk($_POST['cc']))?$_POST['cc']:NULL; ?>" size="30"/></td>
			</tr>
			<tr  class="">
				<td><label for="cci"><span>Cci :</span></label></td>
				<td><input type="text" name="cci" id="cci" class="single " value="<?php echo (isOk($_POST['cci']))?$_POST['cci']:NULL; ?>" size="30"/></td>
			</tr>
			<tr class="">
				<td><label for="obj"><span>Objet :</span></label></td>
				<td><input type="text" name="obj" id="obj" class="single " size="50" value="<?php echo (isOk($_POST['obj']))?stripslashes($_POST['obj']):(isOk($_REQUEST['obj']))?stripslashes($_REQUEST['obj']):NULL; ?>"/></td>
			</tr>
			<tr  class="">
				<td><label for="content"><span>Message :</span></label></td>
				<td>
				<?php $content = (isOk($_POST['content']))?htmlspecialchars_decode(stripslashes($_POST['content'])):(isset($_REQUEST['body'])?htmlspecialchars_decode(stripslashes(rawurldecode($_REQUEST['body']))):NULL);
				 wp_editor( $content,'content', $settings = array('editor_class' => 'content resizable processed fldrequired'));  ?>
				 &nbsp;<span class="reqtxt">(obligatoire)</span>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><p class="cf-sb">
					<input type="submit" name="sendbutton" id="sendbutton" class="sendbutton" value="Envoyer"/>
				</p></td>
			</tr>
		</table>		
	</form>
	
</div>