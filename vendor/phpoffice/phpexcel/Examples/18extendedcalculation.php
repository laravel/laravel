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

/** PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


// List functions
echo date('H:i:s') . " List implemented functions\n";
$objCalc = PHPExcel_Calculation::getInstance();
print_r($objCalc->listFunctionNames());

// Create new PHPExcel object
echo date('H:i:s') . " Create new PHPExcel object\n";
$objPHPExcel = new PHPExcel();

// Add some data, we will use some formulas here
echo date('H:i:s') . " Add some data\n";
$objPHPExcel->getActiveSheet()->setCellValue('A14', 'Count:');

$objPHPExcel->getActiveSheet()->setCellValue('B1', 'Range 1');
$objPHPExcel->getActiveSheet()->setCellValue('B2', 2);
$objPHPExcel->getActiveSheet()->setCellValue('B3', 8);
$objPHPExcel->getActiveSheet()->setCellValue('B4', 10);
$objPHPExcel->getActiveSheet()->setCellValue('B5', True);
$objPHPExcel->getActiveSheet()->setCellValue('B6', False);
$objPHPExcel->getActiveSheet()->setCellValue('B7', 'Text String');
$objPHPExcel->getActiveSheet()->setCellValue('B9', '22');
$objPHPExcel->getActiveSheet()->setCellValue('B10', 4);
$objPHPExcel->getActiveSheet()->setCellValue('B11', 6);
$objPHPExcel->getActiveSheet()->setCellValue('B12', 12);

$objPHPExcel->getActiveSheet()->setCellValue('B14', '=COUNT(B2:B12)');

$objPHPExcel->getActiveSheet()->setCellValue('C1', 'Range 2');
$objPHPExcel->getActiveSheet()->setCellValue('C2', 1);
$objPHPExcel->getActiveSheet()->setCellValue('C3', 2);
$objPHPExcel->getActiveSheet()->setCellValue('C4', 2);
$objPHPExcel->getActiveSheet()->setCellValue('C5', 3);
$objPHPExcel->getActiveSheet()->setCellValue('C6', 3);
$objPHPExcel->getActiveSheet()->setCellValue('C7', 3);
$objPHPExcel->getActiveSheet()->setCellValue('C8', '0');
$objPHPExcel->getActiveSheet()->setCellValue('C9', 4);
$objPHPExcel->getActiveSheet()->setCellValue('C10', 4);
$objPHPExcel->getActiveSheet()->setCellValue('C11', 4);
$objPHPExcel->getActiveSheet()->setCellValue('C12', 4);

$objPHPExcel->getActiveSheet()->setCellValue('C14', '=COUNT(C2:C12)');

$objPHPExcel->getActiveSheet()->setCellValue('D1', 'Range 3');
$objPHPExcel->getActiveSheet()->setCellValue('D2', 2);
$objPHPExcel->getActiveSheet()->setCellValue('D3', 3);
$objPHPExcel->getActiveSheet()->setCellValue('D4', 4);

$objPHPExcel->getActiveSheet()->setCellValue('D5', '=((D2 * D3) + D4) & " should be 10"');

$objPHPExcel->getActiveSheet()->setCellValue('E1', 'Other functions');
$objPHPExcel->getActiveSheet()->setCellValue('E2', '=PI()');
$objPHPExcel->getActiveSheet()->setCellValue('E3', '=RAND()');
$objPHPExcel->getActiveSheet()->setCellValue('E4', '=RANDBETWEEN(5, 10)');

$objPHPExcel->getActiveSheet()->setCellValue('E14', 'Count of both ranges:');
$objPHPExcel->getActiveSheet()->setCellValue('F14', '=COUNT(B2:C12)');

// Calculated data
echo date('H:i:s') . " Calculated data\n";
echo 'Value of B14 [=COUNT(B2:B12)]: ' . $objPHPExcel->getActiveSheet()->getCell('B14')->getCalculatedValue() . "\r\n";


// Echo memory peak usage
echo date('H:i:s') . " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB\r\n";

// Echo done
echo date('H:i:s') . " Done" , EOL;
