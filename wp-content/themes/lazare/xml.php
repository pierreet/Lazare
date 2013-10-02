<?php Header("Content-type: text/xml"); 

$dom = new DOMDocument("1.0", 'UTF-8');

require_once('../../../wp-load.php');

$root = $dom->createElement("images");
$dom->appendChild($root);

$headers = get_posts( array( 'post_type' => 'attachment', 'meta_key' => '_wp_attachment_is_custom_header', 'orderby' => 'rand', 'nopaging' => true ) );
if ( empty( $headers ) )
	echo '<url>'.get_header_image().'</url>';

foreach ( (array) $headers as $header ) {
	$url = esc_url_raw( $header->guid );
	$item = $dom->createElement("url");
	$root->appendChild($item);
	$text = $dom->createTextNode($url);
	$item->appendChild($text);
}

echo $dom->saveXML();
?>