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

date_default_timezone_set('Europe/London');
mt_srand(1234567890);

/** Include PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


// List functions
echo date('H:i:s') , " List implemented functions" , EOL;
$objCalc = PHPExcel_Calculation::getInstance();
print_r($objCalc->listFunctionNames());

// Create new PHPExcel object
echo date('H:i:s') , " Create new PHPExcel object" , EOL;
$objPHPExcel = new PHPExcel();

// Add some data, we will use some formulas here
echo date('H:i:s') , " Add some data and formulas" , EOL;
$objPHPExcel->getActiveSheet()->setCellValue('A14', 'Count:')
                              ->setCellValue('A15', 'Sum:')
                              ->setCellValue('A16', 'Max:')
                              ->setCellValue('A17', 'Min:')
                              ->setCellValue('A18', 'Average:')
                              ->setCellValue('A19', 'Median:')
                              ->setCellValue('A20', 'Mode:');

$objPHPExcel->getActiveSheet()->setCellValue('A22', 'CountA:')
                              ->setCellValue('A23', 'MaxA:')
                              ->setCellValue('A24', 'MinA:');

$objPHPExcel->getActiveSheet()->setCellValue('A26', 'StDev:')
                              ->setCellValue('A27', 'StDevA:')
                              ->setCellValue('A28', 'StDevP:')
                              ->setCellValue('A29', 'StDevPA:');

$objPHPExcel->getActiveSheet()->setCellValue('A31', 'DevSq:')
                              ->setCellValue('A32', 'Var:')
                              ->setCellValue('A33', 'VarA:')
                              ->setCellValue('A34', 'VarP:')
                              ->setCellValue('A35', 'VarPA:');

$objPHPExcel->getActiveSheet()->setCellValue('A37', 'Date:');


$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Range 1')
                              ->setCellValue('B2', 2)
                              ->setCellValue('B3', 8)
                              ->setCellValue('B4', 10)
                              ->setCellValue('B5', True)
                              ->setCellValue('B6', False)
                              ->setCellValue('B7', 'Text String')
                              ->setCellValue('B9', '22')
                              ->setCellValue('B10', 4)
                              ->setCellValue('B11', 6)
                              ->setCellValue('B12', 12);

$objPHPExcel->getActiveSheet()->setCellValue('B14', '=COUNT(B2:B12)')
                              ->setCellValue('B15', '=SUM(B2:B12)')
                              ->setCellValue('B16', '=MAX(B2:B12)')
                              ->setCellValue('B17', '=MIN(B2:B12)')
                              ->setCellValue('B18', '=AVERAGE(B2:B12)')
                              ->setCellValue('B19', '=MEDIAN(B2:B12)')
                              ->setCellValue('B20', '=MODE(B2:B12)');

$objPHPExcel->getActiveSheet()->setCellValue('B22', '=COUNTA(B2:B12)')
                              ->setCellValue('B23', '=MAXA(B2:B12)')
                              ->setCellValue('B24', '=MINA(B2:B12)');

$objPHPExcel->getActiveSheet()->setCellValue('B26', '=STDEV(B2:B12)')
                              ->setCellValue('B27', '=STDEVA(B2:B12)')
                              ->setCellValue('B28', '=STDEVP(B2:B12)')
                              ->setCellValue('B29', '=STDEVPA(B2:B12)');

$objPHPExcel->getActiveSheet()->setCellValue('B31', '=DEVSQ(B2:B12)')
                              ->setCellValue('B32', '=VAR(B2:B12)')
                              ->setCellValue('B33', '=VARA(B2:B12)')
                              ->setCellValue('B34', '=VARP(B2:B12)')
                              ->setCellValue('B35', '=VARPA(B2:B12)');

$objPHPExcel->getActiveSheet()->setCellValue('B37', '=DATE(2007, 12, 21)')
                              ->setCellValue('B38', '=DATEDIF( DATE(2007, 12, 21), DATE(2007, 12, 22), "D" )')
                              ->setCellValue('B39', '=DATEVALUE("01-Feb-2006 10:06 AM")')
                              ->setCellValue('B40', '=DAY( DATE(2006, 1, 2) )')
                              ->setCellValue('B41', '=DAYS360( DATE(2002, 2, 3), DATE(2005, 5, 31) )');


$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Range 2')
                              ->setCellValue('C2', 1)
                              ->setCellValue('C3', 2)
                              ->setCellValue('C4', 2)
                              ->setCellValue('C5', 3)
                              ->setCellValue('C6', 3)
                              ->setCellValue('C7', 3)
                              ->setCellValue('C8', '0')
                              ->setCellValue('C9', 4)
                              ->setCellValue('C10', 4)
                              ->setCellValue('C11', 4)
                              ->setCellValue('C12', 4);

$objPHPExcel->getActiveSheet()->setCellValue('C14', '=COUNT(C2:C12)')
                              ->setCellValue('C15', '=SUM(C2:C12)')
                              ->setCellValue('C16', '=MAX(C2:C12)')
                              ->setCellValue('C17', '=MIN(C2:C12)')
                              ->setCellValue('C18', '=AVERAGE(C2:C12)')
                              ->setCellValue('C19', '=MEDIAN(C2:C12)')
                              ->setCellValue('C20', '=MODE(C2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('C22', '=COUNTA(C2:C12)')
                              ->setCellValue('C23', '=MAXA(C2:C12)')
                              ->setCellValue('C24', '=MINA(C2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('C26', '=STDEV(C2:C12)')
                              ->setCellValue('C27', '=STDEVA(C2:C12)')
                              ->setCellValue('C28', '=STDEVP(C2:C12)')
                              ->setCellValue('C29', '=STDEVPA(C2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('C31', '=DEVSQ(C2:C12)')
                              ->setCellValue('C32', '=VAR(C2:C12)')
                              ->setCellValue('C33', '=VARA(C2:C12)')
                              ->setCellValue('C34', '=VARP(C2:C12)')
                              ->setCellValue('C35', '=VARPA(C2:C12)');


$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Range 3')
                              ->setCellValue('D2', 2)
                              ->setCellValue('D3', 3)
                              ->setCellValue('D4', 4);

$objPHPExcel->getActiveSheet()->setCellValue('D14', '=((D2 * D3) + D4) & " should be 10"');

$objPHPExcel->getActiveSheet()->setCellValue('E12', 'Other functions')
                              ->setCellValue('E14', '=PI()')
                              ->setCellValue('E15', '=RAND()')
                              ->setCellValue('E16', '=RANDBETWEEN(5, 10)');

$objPHPExcel->getActiveSheet()->setCellValue('E17', 'Count of both ranges:')
                              ->setCellValue('F17', '=COUNT(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E18', 'Total of both ranges:')
                              ->setCellValue('F18', '=SUM(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E19', 'Maximum of both ranges:')
                              ->setCellValue('F19', '=MAX(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E20', 'Minimum of both ranges:')
                              ->setCellValue('F20', '=MIN(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E21', 'Average of both ranges:')
                              ->setCellValue('F21', '=AVERAGE(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E22', 'Median of both ranges:')
                              ->setCellValue('F22', '=MEDIAN(B2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('E23', 'Mode of both ranges:')
                              ->setCellValue('F23', '=MODE(B2:C12)');


// Calculated data
echo date('H:i:s') , " Calculated data" , EOL;
for ($col = 'B'; $col != 'G'; ++$col) {
    for($row = 14; $row <= 41; ++$row) {
        if ((!is_null($formula = $objPHPExcel->getActiveSheet()->getCell($col.$row)->getValue())) &&
			($formula[0] == '=')) {
            echo 'Value of ' , $col , $row , ' [' , $formula , ']: ' ,
                               $objPHPExcel->getActiveSheet()->getCell($col.$row)->getCalculatedValue() . EOL;
        }
    }
}


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


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing file" , EOL;
echo 'File has been created in ' , getcwd() , EOL;
