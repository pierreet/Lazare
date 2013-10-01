<?php
/**
 * 	Create a xls
 * 
 * @version 1.0
 * @package wp-lazare
 * @subpackage librairies
 */

/**
 * Create a xls
 * @package wp-lazare
 * @subpackage librairies
 */
class wplazare_excelator {
	function __construct() {
		require_once(WPLAZARE_EXCEL_PLUGIN_DIR . 'PHPExcel.php');
	}

	/**
	 *	Get the pdf from the html template
	 *	replace text following $balises_replace
	 *	$balises_replace = array( array( "balise" => "BALISE TO REPLACE", "new_text" => "NEW TEXT TO PASTE"), etc )
	 *
	 *	@return string The pdf file path+name
	 */
	function getAnnuaire($locataires) {
		date_default_timezone_set('Europe/London');

		define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

		$objPHPExcel = new PHPExcel();

		$objPHPExcel->getProperties()->setCreator("Charles Dupoiron")
				->setLastModifiedBy("Charles Dupoiron")
				->setTitle("Annuaire Lazare")->setSubject("Annuaire Lazare")
				->setDescription("Annuaire Lazare en cours.")
				->setKeywords("Annuaire Lazare")
				->setCategory("Annuaire Lazare");

		$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Appartement')
				->setCellValue('B1', 'Prénom')->setCellValue('C1', 'Nom')
				->setCellValue('D1', 'Téléphone')->setCellValue('E1', 'Email')
				->setCellValue('F1', 'Date de naissance');
				
		$sheet = $objPHPExcel->getActiveSheet();

		$i = 2;
		foreach ($locataires as $result) {
			$sheet->setCellValue('A' . $i,
							wplazare_apparts::getAdresseComplete(
									$result->appart_id));

			$user_id = $result->locataire_id;

			$sheet->setCellValue('B' . $i,
							ucfirst(get_user_meta($user_id, 'first_name', true)));

			$sheet->setCellValue('C' . $i,
							ucfirst(get_user_meta($user_id, 'last_name', true)));
			$sheet->setCellValue('D' . $i,
							str_replace('&nbsp;', ' ',
									wplazare_tools::addSpaceOnPhone(
											get_cimyFieldValue($user_id, 'TEL'))));
			$sheet->setCellValue('E' . $i,
							get_user_by('id', $user_id)->user_email);
			$sheet->setCellValue('F' . $i,
							get_cimyFieldValue($user_id, 'DATE_DE_NAISSANCE'));
			$i++;
		}

		$sheet->getColumnDimension('A')->setAutoSize(true);
		$sheet->getColumnDimension('B')->setAutoSize(true);
		$sheet->getColumnDimension('C')->setAutoSize(true);
		$sheet->getColumnDimension('D')->setAutoSize(true);
		$sheet->getColumnDimension('E')->setAutoSize(true);
		$sheet->getColumnDimension('F')->setAutoSize(true);

		$sheet->setTitle('Annuaire Lazare');

		// Set active sheet index to the first sheet, so Excel opens this as the first sheet
		$objPHPExcel->setActiveSheetIndex(0);

		$callStartTime = microtime(true);

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter
				->save(
						str_replace('excelator.class.php',
								'annuaire_lazare.xlsx', __FILE__));
		$callEndTime = microtime(true);
		$callTime = $callEndTime - $callStartTime;
	}

    /**
     *	Get the pdf from the html template
     *	replace text following $balises_replace
     *	$balises_replace = array( array( "balise" => "BALISE TO REPLACE", "new_text" => "NEW TEXT TO PASTE"), etc )
     *
     *	@return string The pdf file path+name
     */
    function getDons($dons,$title) {
        date_default_timezone_set('Europe/London');

        define('EOL', (PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Charles Dupoiron")
            ->setLastModifiedBy("Charles Dupoiron")
            ->setTitle("Don Lazare - ".$title)->setSubject("Dons")
            ->setDescription("Dons ".$title)
            ->setKeywords("Dons ".$title)
            ->setCategory("Dons");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Date')
            ->setCellValue('B1', 'Nom')->setCellValue('C1', 'Montant')
            ->setCellValue('D1', 'Type')->setCellValue('E1', 'Numero recu fiscal')
            ->setCellValue('F1', 'Don/Charge')->setCellValue('G1', 'Statut')
            ->setCellValue('H1', 'Ville');

        $sheet = $objPHPExcel->getActiveSheet();

        $i = 2;
        foreach ($dons as $element) {
            $sheet->setCellValue('A' . $i,$element->creation_date);

            if($element->location_id)
            {
                $location = wplazare_locations::getElement($element->location_id);
                $locataire_id = $location->user;
                $full_name = wplazare_tools::getUserName($locataire_id);
            }
            else
                $full_name = $element->user_firstname." ".$element->user_lastname;

            $full_name = (trim($full_name) != '') ? $full_name : __('Non renseigné', 'wplazare');

            $sheet->setCellValue('B' . $i, $full_name );

            $sheet->setCellValue('C' . $i,
                ($element->order_amount / 100));

            $sheet->setCellValue('D' . $i,
                utf8_encode(html_entity_decode(__($element->payment_type, 'wplazare'))));
            $sheet->setCellValue('E' . $i,
                $element->order_reference);

            $reason = is_null($element->location_id) ? __('Don', 'wplazare') : __('Charge', 'wplazare');
            $sheet->setCellValue('F' . $i,
                $reason);

            $sheet->setCellValue('G' . $i,
                utf8_encode(html_entity_decode(__($element->order_status, 'wplazare'))));

            if($element->location_id)
            {
                $location = wplazare_locations::getElement($element->location_id);
                $appartement_id = $location->appartement;
                $appart = wplazare_apparts::getElement($appartement_id);
                if(isset($appart->ville))
                    $city = $appart->ville;
                else
                    $city = '';
            }
            else
                $city = $element->user_ville;
            $city = ($city != '') ? $city : __('Non renseigné', 'wplazare');
            $sheet->setCellValue('H' . $i,
                $city);
            $i++;
        }

        $sheet->getColumnDimension('A')->setAutoSize(true);
        $sheet->getColumnDimension('B')->setAutoSize(true);
        $sheet->getColumnDimension('C')->setAutoSize(true);
        $sheet->getColumnDimension('D')->setAutoSize(true);
        $sheet->getColumnDimension('E')->setAutoSize(true);
        $sheet->getColumnDimension('F')->setAutoSize(true);
        $sheet->getColumnDimension('G')->setAutoSize(true);
        $sheet->getColumnDimension('H')->setAutoSize(true);

        $sheet->setTitle('Don Lazare '.$title);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        //$callStartTime = microtime(true);

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter
            ->save(
                str_replace('excelator.class.php',
                    'don_lazare.xlsx', __FILE__));
        //$callEndTime = microtime(true);
        //$callTime = $callEndTime - $callStartTime;
    }

}
