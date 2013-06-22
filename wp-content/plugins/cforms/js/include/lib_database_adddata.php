<?php

### supporting WP2.6 wp-load & custom wp-content / plugin dir
if ( file_exists('../../abspath.php') )
	include_once('../../abspath.php');
else
	$abspath='../../../../../';

if ( file_exists( $abspath . 'wp-load.php') )
	require_once( $abspath . 'wp-load.php' );
else
	require_once( $abspath . 'wp-config.php' );

if( !current_user_can('track_cforms') )
	wp_die("access restricted.");

global $wpdb;

$wpdb->cformssubmissions	= $wpdb->prefix . 'cformssubmissions';
$wpdb->cformsdata       	= $wpdb->prefix . 'cformsdata';

### new global settings container, will eventually be the only one!
//$cformsSettings = get_option('cforms_settings');

	$sid   = $_POST['sub_id'];

if(!isset($_POST['add_document'])){

	$newName = addslashes($_POST['field_name']);
	$newVal = addslashes($_POST['field_val']);

		$sql="INSERT INTO {$wpdb->cformsdata} (sub_id,field_name,field_val) VALUES ('$sid','$newName','$newVal')";
		$entries = $wpdb->query($sql);
	
	if($newName=='Rendez-vous fixé le'){
		$sql="UPDATE {$wpdb->cformssubmissions} SET state = \"RDV fixé\"  WHERE id = ".$sid;
		$entries = $wpdb->query($sql);
	}else if($newName=='Réponse'){
		$sql="UPDATE {$wpdb->cformssubmissions} SET state = \"".$newVal."\"  WHERE id = ".$sid;
		$entries = $wpdb->query($sql);
	}else if($newName=='Désistement'){
		$sql="UPDATE {$wpdb->cformssubmissions} SET state = \"".$newName."\"  WHERE id = ".$sid;
		$entries = $wpdb->query($sql);
	}
		

}else{
	$docs = explode(";", $cformsSettings['global']['ged']);
	
	$sql="UPDATE {$wpdb->cformssubmissions} SET state = \"Attente des documents\"  WHERE id = ".$sid;
	$entries = $wpdb->query($sql);
	
	foreach($docs as $doc){
		if(!empty($doc)){
			$newName = $doc;
			$newVal = 'false';
			// $sql .= " ('$sid','$newName','$newVal'),";
			$data = array('sub_id' => $sid,
							'field_name' => $newName,
							'field_val' => 'false');
			$wpdb->insert( "{$wpdb->cformsdata}", $data );
		}
	}
}	
	//debug
	echo $wpdb->print_error().' '.$wpdb->last_query;
?>