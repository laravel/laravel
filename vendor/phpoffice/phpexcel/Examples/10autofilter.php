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

// Create new PHPExcel object
echo date('H:i:s').' Create new PHPExcel object'.EOL;
$objPHPExcel = new PHPExcel();

// Set document properties
echo date('H:i:s').' Set document properties'.EOL;
$objPHPExcel->getProperties()->setCreator('Maarten Balliauw')
							 ->setLastModifiedBy('Maarten Balliauw')
							 ->setTitle('PHPExcel Test Document')
							 ->setSubject('PHPExcel Test Document')
							 ->setDescription('Test document for PHPExcel, generated using PHP classes.')
							 ->setKeywords('office PHPExcel php')
							 ->setCategory('Test result file');

// Create the worksheet
echo date('H:i:s').' Add data'.EOL;
$objPHPExcel->setActiveSheetIndex(0);
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Year')
                              ->setCellValue('B1', 'Quarter')
                              ->setCellValue('C1', 'Country')
                              ->setCellValue('D1', 'Sales');

$dataArray = array(array('2010',	'Q1',	'United States',	790),
                   array('2010',	'Q2',	'United States',	730),
                   array('2010',	'Q3',	'United States',	860),
                   array('2010',	'Q4',	'United States',	850),
                   array('2011',	'Q1',	'United States',	800),
                   array('2011',	'Q2',	'United States',	700),
                   array('2011',	'Q3',	'United States',	900),
                   array('2011',	'Q4',	'United States',	950),
                   array('2010',	'Q1',	'Belgium',			380),
                   array('2010',	'Q2',	'Belgium',			390),
                   array('2010',	'Q3',	'Belgium',			420),
                   array('2010',	'Q4',	'Belgium',			460),
                   array('2011',	'Q1',	'Belgium',			400),
                   array('2011',	'Q2',	'Belgium',			350),
                   array('2011',	'Q3',	'Belgium',			450),
                   array('2011',	'Q4',	'Belgium',			500),
                   array('2010',	'Q1',	'UK',				690),
                   array('2010',	'Q2',	'UK',				610),
                   array('2010',	'Q3',	'UK',				620),
                   array('2010',	'Q4',	'UK',				600),
                   array('2011',	'Q1',	'UK',				720),
                   array('2011',	'Q2',	'UK',				650),
                   array('2011',	'Q3',	'UK',				580),
                   array('2011',	'Q4',	'UK',				510),
                   array('2010',	'Q1',	'France',			510),
                   array('2010',	'Q2',	'France',			490),
                   array('2010',	'Q3',	'France',			460),
                   array('2010',	'Q4',	'France', 			590),
                   array('2011',	'Q1',	'France',			620),
                   array('2011',	'Q2',	'France',			650),
                   array('2011',	'Q3',	'France',			415),
                   array('2011',	'Q4',	'France', 			570),
                   array('2010',	'Q1',	'Germany',			720),
                   array('2010',	'Q2',	'Germany',			680),
                   array('2010',	'Q3',	'Germany',			640),
                   array('2010',	'Q4',	'Germany',			660),
                   array('2011',	'Q1',	'Germany',			680),
                   array('2011',	'Q2',	'Germany',			620),
                   array('2011',	'Q3',	'Germany',			710),
                   array('2011',	'Q4',	'Germany',			690),
                   array('2010',	'Q1',	'Spain',			510),
                   array('2010',	'Q2',	'Spain',			490),
                   array('2010',	'Q3',	'Spain',			470),
                   array('2010',	'Q4',	'Spain',			420),
                   array('2011',	'Q1',	'Spain',			460),
                   array('2011',	'Q2',	'Spain',			390),
                   array('2011',	'Q3',	'Spain',			430),
                   array('2011',	'Q4',	'Spain',			415),
                   array('2010',	'Q1',	'Italy',			440),
                   array('2010',	'Q2',	'Italy',			410),
                   array('2010',	'Q3',	'Italy',			420),
                   array('2010',	'Q4',	'Italy',			450),
                   array('2011',	'Q1',	'Italy',			430),
                   array('2011',	'Q2',	'Italy',			370),
                   array('2011',	'Q3',	'Italy',			350),
                   array('2011',	'Q4',	'Italy',			335),
                  );
$objPHPExcel->getActiveSheet()->fromArray($dataArray, NULL, 'A2');

// Set title row bold
echo date('H:i:s').' Set title row bold'.EOL;
$objPHPExcel->getActiveSheet()->getStyle('A1:D1')->getFont()->setBold(true);

// Set autofilter
echo date('H:i:s').' Set autofilter'.EOL;
// Always include the complete filter range!
// Excel does support setting only the caption
// row, but that's not a best practise...
$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

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
echo date('H:i:s').' Peak memory usage: '.(memory_get_peak_usage(true) / 1024 / 1024).' MB'.EOL;

// Echo done
echo date('H:i:s').' Done writing files'.EOL;
echo 'Files have been created in ' , getcwd() , EOL;
