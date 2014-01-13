<?php
/**
 * Generate document
 *
 * @version 1.0
 * @package wp-lazare
 * @subpackage librairies
 */

/**
 * Generate document
 * @package wp-lazare
 * @subpackage librairies
 */
class wplazare_documents
{
    /**
     *	Get the url listing slug of the current class
     *
     *	@return string The table of the class
     */
    function getCurrentPageCode()
    {
        return 'wplazare_documents';
    }
    /**
     *	Get the url listing slug of the current class
     *
     *	@return string The table of the class
     */
    function getPageIcon()
    {
        return '';
    }
    /**
     *	Get the url listing slug of the current class
     *
     *	@return string The table of the class
     */
    function getListingSlug()
    {
        return WPLAZARE_URL_SLUG_DOCUMENTS_LISTING;
    }
    /**
     *	Get the url edition slug of the current class
     *
     *	@return string The table of the class
     */
    function getEditionSlug()
    {
        return WPLAZARE_URL_SLUG_DOCUMENTS_EDITION;
    }

    /**
     *	Define the title of the page
     *
     *	@return string $title The title of the page looking at the environnement
     */
    function pageTitle()
    {
        return 'Documents';
    }

    /**
     *	Define the different message and action after an action is send through the element interface
     */
    function elementAction()
    {
        return ;
    }

    function getPageFormButton($export =''){
        return "";
    }

    /**
     *	Return the list page content, containing the table that present the item list
     *
     *	@return string $listItemOutput The html code that output the item list
     */
    function elementList()
    {
        $formAction = admin_url('admin.php?page=' . wplazare_documents::getEditionSlug().'&action=export');
        $selectForm='<form  method="post" enctype="multipart/form-data" action="'.$formAction.'">
		Partie1:<br/> <textarea rows="4" cols="50" name="ligne1"/></textarea><br/>
		Partie2:<br/> <textarea rows="4" cols="50" name="ligne2"/></textarea><br/><br/>
		<input type="submit" class="button-primary" value="Générer PDF"/></form>
		';

        return $selectForm;
    }

    function export() {

        $template_name = "attestation_assurance";

        $pdfator = new wplazare_pdfator();

        $ligne1 = isset($_REQUEST['ligne1']) ? wplazare_tools::varSanitizer($_REQUEST['ligne1']) : '';
        $ligne2 = isset($_REQUEST['ligne2']) ? wplazare_tools::varSanitizer($_REQUEST['ligne2']) : '';

        $balises_replace = array(
            array("balise" => "{LIGNE1}", "new_text" => nl2br($ligne1)),
            array("balise" => "{LIGNE2}", "new_text" => nl2br($ligne2)),
            array("balise" => "{PATH}", "new_text" => plugins_url( '/wplazare/includes/librairies/html2pdf/templates/') )
        );

        $file_path = $pdfator->getPdf($template_name, $balises_replace);

        if ($file_path != '') {

            $return = '<h3>Attestation assurance</h3>';

            $return .= '<div>'
                . 'L\'attestation d\'assurance a bien &eacute;t&eacute; g&eacute;n&eacute;r&eacute;e:<br/>'
                . '<a class="pdf" href="'
                . plugins_url(
                    '/wplazare/includes/librairies/html2pdf/output/'
                    . basename($file_path)) . '">Attestation assurance</a>';

            '</div>';

        }
        else{
            return "Erreur lors de la g&eacute;n&eacute;ration du PDF.";
        }

        return $return;

    }

}