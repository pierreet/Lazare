<?php
//--------------------------------------------------------------------------
// anniv_notif.php
// envoie par mail la liste des utlisateurs dont l'anniversaire est le lendemain
//
// expression régulière pour la date d'anniversaire en jj/mm/aaaa : /([0-9]{2})\/([0-9]{2})\/([0-9]{4})/
//
//--------------------------------------------------------------------------
/*
	// test de l'expression régulière :
	$anniv = "13/06/1983";
	$regex = "/([0-9]{2})\/([0-9]{2})\/([0-9]{4})/";
	$test = preg_match($regex, $anniv);
	$test = ($test == "") ? 0 : $test;
	echo ("))".$test."((");
*/

require_once('./admin.php');

//--------------------------------------------------------------------------
// Générer la liste des utilisteurs
//--------------------------------------------------------------------------

$blogusers = get_users('');
//print_r($blogusers);

$date_obj = date("d/m", (current_time("timestamp", 0)+86400) ); 		// date de demain au format jj/mm (utilise le fuseau horaire configuré dans wordpress)

$anniv_users = array();
foreach ($blogusers as $user) {
	$anniv = get_cimyFieldValue($user->ID, 'DATE_DE_NAISSANCE');
	//$anniv = "05/09/1983";		// pour le debug brutal
	if( substr($anniv,0,5) === $date_obj )
		$anniv_users[] = $user->user_nicename;							// ajoute l'utilisateur dont ce sera l'anniv au tableau $anniv_users
}

//-------------
// pour le debug
print_r($anniv_users);
echo("<br /><br />");
//-------------

//--------------------------------------------------------------------------
// Envoyer le mail
//--------------------------------------------------------------------------

if( !empty($anniv_users) )
{
	$sender = "charlesdupoiron@yahoo.fr";
	$destinataire = "charlesdupoiron@yahoo.fr";
	
	$emailObj = "Liste des anniversaires demain";
	$emailMsg = "Demain, nous fêterons les anniversaires des membres suivant : <br />";
	foreach ($anniv_users as $user) {
		$emailMsg .= "- ".$user."<br />";
	}
	$emailMsg .= "<br />Et ça, c'est génial.";

	$emailHeader = "From: $sender\r\n";
	$emailHeader .= "Reply-To: $sender\r\n";
	$emailHeader .= "Return-Path: $sender\r\n";
	$emailHeader .= "MIME-Version: 1.0\r\n";
	$emailHeader .= "Content-type: text/html; charset=utf-8\r\n";
		
	//echo $emailObj;
	//echo $$emailMsg;
	//echo $emailHeader;

	if (!$result = mail($destinataire, $emailObj, $emailMsg, $emailHeader) )
	{	
		echo utf8_decode("Erreur a l'envoi des email !");					// pour le debug
	} else {
		echo utf8_decode("$result email a été envoyé à $destinataire");		// pour le debug
	}
}
?>