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

$cacheMethod = PHPExcel_CachedObjectStorageFactory::cache_to_sqlite;
if (PHPExcel_Settings::setCacheStorageMethod($cacheMethod)) {
    echo date('H:i:s') , " Enable Cell Caching using " , $cacheMethod , " method" , EOL;
} else {
    echo date('H:i:s') , " Unable to set Cell Caching using " , $cacheMethod , " method, reverting to memory" , EOL;
}


// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();

// Set document properties
echo date('H:i:s') , " Set properties" , EOL;
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


// Create a first sheet
echo date('H:i:s') , " Add data" , EOL;
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', "Firstname");
$objPHPExcel->getActiveSheet()->setCellValue('B1', "Lastname");
$objPHPExcel->getActiveSheet()->setCellValue('C1', "Phone");
$objPHPExcel->getActiveSheet()->setCellValue('D1', "Fax");
$objPHPExcel->getActiveSheet()->setCellValue('E1', "Is Client ?");


// Hide "Phone" and "fax" column
echo date('H:i:s') , " Hide 'Phone' and 'fax' columns" , EOL;
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setVisible(false);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setVisible(false);


// Set outline levels
echo date('H:i:s') , " Set outline levels" , EOL;
$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setOutlineLevel(1)
                                                       ->setVisible(false)
                                                       ->setCollapsed(true);

// Freeze panes
echo date('H:i:s') , " Freeze panes" , EOL;
$objPHPExcel->getActiveSheet()->freezePane('A2');


// Rows to repeat at top
echo date('H:i:s') , " Rows to repeat at top" , EOL;
$objPHPExcel->getActiveSheet()->getPageSetup()->setRowsToRepeatAtTopByStartAndEnd(1, 1);


// Add data
for ($i = 2; $i <= 5000; $i++) {
	$objPHPExcel->getActiveSheet()->setCellValue('A' . $i, "FName $i")
	                              ->setCellValue('B' . $i, "LName $i")
	                              ->setCellValue('C' . $i, "PhoneNo $i")
	                              ->setCellValue('D' . $i, "FaxNo $i")
	                              ->setCellValue('E' . $i, true);
}


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing file" , EOL;
echo 'File has been created in ' , getcwd() , EOL;
