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

}
