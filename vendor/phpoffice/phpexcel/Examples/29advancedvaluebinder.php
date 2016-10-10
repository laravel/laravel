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

/** PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


// Set timezone
echo date('H:i:s') , " Set timezone" , EOL;
date_default_timezone_set('UTC');

// Set value binder
echo date('H:i:s') , " Set value binder" , EOL;
PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() );

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

// Set default font
echo date('H:i:s') , " Set default font" , EOL;
$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setName('Arial');
$objPHPExcel->getActiveSheet()->getDefaultStyle()->getFont()->setSize(10);

// Set column widths
echo date('H:i:s') , " Set column widths" , EOL;
$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(14);

// Add some data, resembling some different data types
echo date('H:i:s') , " Add some data" , EOL;
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'String value:')
                              ->setCellValue('B1', 'Mark Baker');

$objPHPExcel->getActiveSheet()->setCellValue('A2', 'Numeric value #1:')
                              ->setCellValue('B2', 12345);

$objPHPExcel->getActiveSheet()->setCellValue('A3', 'Numeric value #2:')
                              ->setCellValue('B3', -12.345);

$objPHPExcel->getActiveSheet()->setCellValue('A4', 'Numeric value #3:')
                              ->setCellValue('B4', .12345);

$objPHPExcel->getActiveSheet()->setCellValue('A5', 'Numeric value #4:')
                              ->setCellValue('B5', '12345');

$objPHPExcel->getActiveSheet()->setCellValue('A6', 'Numeric value #5:')
                              ->setCellValue('B6', '1.2345');

$objPHPExcel->getActiveSheet()->setCellValue('A7', 'Numeric value #6:')
                              ->setCellValue('B7', '.12345');

$objPHPExcel->getActiveSheet()->setCellValue('A8', 'Numeric value #7:')
                              ->setCellValue('B8', '1.234e-5');

$objPHPExcel->getActiveSheet()->setCellValue('A9', 'Numeric value #8:')
                              ->setCellValue('B9', '-1.234e+5');

$objPHPExcel->getActiveSheet()->setCellValue('A10', 'Boolean value:')
                              ->setCellValue('B10', 'TRUE');

$objPHPExcel->getActiveSheet()->setCellValue('A11', 'Percentage value #1:')
                              ->setCellValue('B11', '10%');

$objPHPExcel->getActiveSheet()->setCellValue('A12', 'Percentage value #2:')
                              ->setCellValue('B12', '12.5%');

$objPHPExcel->getActiveSheet()->setCellValue('A13', 'Fraction value #1:')
                              ->setCellValue('B13', '-1/2');

$objPHPExcel->getActiveSheet()->setCellValue('A14', 'Fraction value #2:')
                              ->setCellValue('B14', '3 1/2');

$objPHPExcel->getActiveSheet()->setCellValue('A15', 'Fraction value #3:')
                              ->setCellValue('B15', '-12 3/4');

$objPHPExcel->getActiveSheet()->setCellValue('A16', 'Fraction value #4:')
                              ->setCellValue('B16', '13/4');

$objPHPExcel->getActiveSheet()->setCellValue('A17', 'Currency value #1:')
                              ->setCellValue('B17', '$12345');

$objPHPExcel->getActiveSheet()->setCellValue('A18', 'Currency value #2:')
                              ->setCellValue('B18', '$12345.67');

$objPHPExcel->getActiveSheet()->setCellValue('A19', 'Currency value #3:')
                              ->setCellValue('B19', '$12,345.67');

$objPHPExcel->getActiveSheet()->setCellValue('A20', 'Date value #1:')
                              ->setCellValue('B20', '21 December 1983');

$objPHPExcel->getActiveSheet()->setCellValue('A21', 'Date value #2:')
                              ->setCellValue('B21', '19-Dec-1960');

$objPHPExcel->getActiveSheet()->setCellValue('A22', 'Date value #3:')
                              ->setCellValue('B22', '07/12/1982');

$objPHPExcel->getActiveSheet()->setCellValue('A23', 'Date value #4:')
                              ->setCellValue('B23', '24-11-1950');

$objPHPExcel->getActiveSheet()->setCellValue('A24', 'Date value #5:')
                              ->setCellValue('B24', '17-Mar');

$objPHPExcel->getActiveSheet()->setCellValue('A25', 'Time value #1:')
                              ->setCellValue('B25', '01:30');

$objPHPExcel->getActiveSheet()->setCellValue('A26', 'Time value #2:')
                              ->setCellValue('B26', '01:30:15');

$objPHPExcel->getActiveSheet()->setCellValue('A27', 'Date/Time value:')
                              ->setCellValue('B27', '19-Dec-1960 01:30');

$objPHPExcel->getActiveSheet()->setCellValue('A28', 'Formula:')
                              ->setCellValue('B28', '=SUM(B2:B9)');

// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('Advanced value binder');


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;
// Save Excel5 file
echo date('H:i:s') , " Write to Excel5 format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing file" , EOL;
echo 'File has been created in ' , getcwd() , EOL;
