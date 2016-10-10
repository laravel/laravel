<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2015 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    ##VERSION##, ##DATE##
 */

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


//	Change these values to select the PDF Rendering library that you wish to use
//		and its directory location on your server
//$rendererName = PHPExcel_Settings::PDF_RENDERER_TCPDF;
//$rendererName = PHPExcel_Settings::PDF_RENDERER_MPDF;
$rendererName = PHPExcel_Settings::PDF_RENDERER_DOMPDF;
//$rendererLibrary = 'tcPDF5.9';
//$rendererLibrary = 'mPDF5.4';
$rendererLibrary = 'domPDF0.6.0beta3';
$rendererLibraryPath = '/php/libraries/PDF/' . $rendererLibrary;


// Read from Excel2007 (.xlsx) template
echo date('H:i:s') , " Load Excel2007 template file" , EOL;
$objReader = PHPExcel_IOFactory::createReader('Excel2007');
$objPHPExcel = $objReader->load("templates/26template.xlsx");

/** at this point, we could do some manipulations with the template, but we skip this step */

// Export to Excel2007 (.xlsx)
echo date('H:i:s') , " Write to Excel5 format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;

// Export to Excel5 (.xls)
echo date('H:i:s') , " Write to Excel5 format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;

// Export to HTML (.html)
echo date('H:i:s') , " Write to HTML format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'HTML');
$objWriter->save(str_replace('.php', '.htm', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.htm', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;

// Export to PDF (.pdf)
echo date('H:i:s') , " Write to PDF format" , EOL;
try {
	if (!PHPExcel_Settings::setPdfRenderer(
		$rendererName,
		$rendererLibraryPath
	)) {
		echo (
			'NOTICE: Please set the $rendererName and $rendererLibraryPath values' .
			EOL .
			'at the top of this script as appropriate for your directory structure' .
			EOL
		);
	} else {
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'PDF');
		$objWriter->save(str_replace('.php', '.pdf', __FILE__));
		echo date('H:i:s') , " File written to " , str_replace('.php', '.pdf', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
	}
} catch (Exception $e) {
	echo date('H:i:s') , ' EXCEPTION: ', $e->getMessage() , EOL;
}

// Remove first two rows with field headers before exporting to CSV
echo date('H:i:s') , " Removing first two heading rows for CSV export" , EOL;
$objWorksheet = $objPHPExcel->getActiveSheet();
$objWorksheet->removeRow(1, 2);

// Export to CSV (.csv)
echo date('H:i:s') , " Write to CSV format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
$objWriter->save(str_replace('.php', '.csv', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.csv', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;

// Export to CSV with BOM (.csv)
echo date('H:i:s') , " Write to CSV format (with BOM)" , EOL;
$objWriter->setUseBOM(true);
$objWriter->save(str_replace('.php', '-bom.csv', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '-bom.csv', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
