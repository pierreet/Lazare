<?php
/**
* 	Create a pdf from an html template
* 
* @version 1.0
* @package wp-lazare
* @subpackage librairies
*/

/**
* Create a pdf from an html template
* @package wp-lazare
* @subpackage librairies
*/
class wplazare_pdfator
{
    function __construct() {
		require_once(WPLAZARE_HTML2PDF_PLUGIN_DIR.'html2pdf.class.php');
    }
	
	/**
	*	Get the pdf from the html template
	*	replace text following $balises_replace
	*	$balises_replace = array( array( "balise" => "BALISE TO REPLACE", "new_text" => "NEW TEXT TO PASTE"), etc )
	*
	*	@return string The pdf file path+name
	*/
	function getPdf($template_name, $balises_replace)
	{	
		//$template_name ="attestation_domicile";
		$return_value = "";
		
		$html_template = WPLAZARE_HTML2PDF_PLUGIN_DIR."templates/".$template_name.".html";
		if( @file_exists($html_template) )
		{
			$content = file_get_contents($html_template);
			
			if( is_array($balises_replace) )
			{
				foreach($balises_replace as $balise_replace)
				{
					$content = str_replace ( $balise_replace["balise"] , $balise_replace["new_text"] , $content );	
				}
			}			
			$output_filename = WPLAZARE_HTML2PDF_PLUGIN_DIR."output/".$template_name."-".date("Y-m").".pdf";
            if(file_exists($output_filename)){
                unlink($output_filename);
            }
			$html2pdf = new HTML2PDF('P','A4','fr');
			$html2pdf->WriteHTML($content);
			$html2pdf->Output($output_filename, 'F');
			if( @file_exists($output_filename) )
				$return_value = $output_filename;
		}
		return $return_value;
	}
	
	function clearTempPdf($template_name)
	{
		$file_path = WPLAZARE_HTML2PDF_PLUGIN_DIR."output/".$template_name.".pdf";
		if( @file_exists($file_path) )
		{
			unlink($file_path);
		}
		return 1;
	}	
	
}