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
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();

// Set document properties
echo date('H:i:s') , " Set document properties" , EOL;
$objPHPExcel->getProperties()->setCreator("Maarten Balliauw")
							 ->setLastModifiedBy("Maarten Balliauw")
							 ->setTitle("Office 2007 XLSX Test Document")
							 ->setSubject("Office 2007 XLSX Test Document")
							 ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
							 ->setKeywords("office 2007 openxml php")
							 ->setCategory("Test result file");


// Add some data, we will use some formulas here
echo date('H:i:s') , " Add some data" , EOL;
$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Sum:');

$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Range #1')
                              ->setCellValue('B2', 3)
                              ->setCellValue('B3', 7)
                              ->setCellValue('B4', 13)
                              ->setCellValue('B5', '=SUM(B2:B4)');
echo date('H:i:s') , " Sum of Range #1 is " ,
                     $objPHPExcel->getActiveSheet()->getCell('B5')->getCalculatedValue() , EOL;

$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Range #2')
                              ->setCellValue('C2', 5)
                              ->setCellValue('C3', 11)
                              ->setCellValue('C4', 17)
                              ->setCellValue('C5', '=SUM(C2:C4)');
echo date('H:i:s') , " Sum of Range #2 is " ,
                     $objPHPExcel->getActiveSheet()->getCell('C5')->getCalculatedValue() , EOL;

$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Total of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('B7', '=SUM(B5:C5)');
echo date('H:i:s') , " Sum of both Ranges is " ,
                     $objPHPExcel->getActiveSheet()->getCell('B7')->getCalculatedValue() , EOL;

$objPHPExcel->getActiveSheet()->setCellValue('A8', 'Minimum of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('B8', '=MIN(B2:C4)');
echo date('H:i:s') , " Minimum value in either Range is " ,
                     $objPHPExcel->getActiveSheet()->getCell('B8')->getCalculatedValue() , EOL;

$objPHPExcel->getActiveSheet()->setCellValue('A9', 'Maximum of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('B9', '=MAX(B2:C4)');
echo date('H:i:s') , " Maximum value in either Range is " ,
                     $objPHPExcel->getActiveSheet()->getCell('B9')->getCalculatedValue() , EOL;

$objPHPExcel->getActiveSheet()->setCellValue('A10', 'Average of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('B10', '=AVERAGE(B2:C4)');
echo date('H:i:s') , " Average value of both Ranges is " ,
                     $objPHPExcel->getActiveSheet()->getCell('B10')->getCalculatedValue() , EOL;


// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('Formulas');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');

//
//  If we set Pre Calculated Formulas to true then PHPExcel will calculate all formulae in the
//    workbook before saving. This adds time and memory overhead, and can cause some problems with formulae
//    using functions or features (such as array formulae) that aren't yet supported by the calculation engine
//  If the value is false (the default) for the Excel2007 Writer, then MS Excel (or the application used to
//    open the file) will need to recalculate values itself to guarantee that the correct results are available.
//
//$objWriter->setPreCalculateFormulas(true);
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// Save Excel 95 file
echo date('H:i:s') , " Write to Excel5 format" , EOL;
$callStartTime = microtime(true);

$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));
$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;

echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
echo 'Call time to write Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
