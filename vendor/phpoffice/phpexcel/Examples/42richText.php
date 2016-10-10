<?php
/**
 * PHPExcel
 *
 * Copyright (C) 2006 - 2014 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
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
							 ->setTitle("PHPExcel Test Document")
							 ->setSubject("PHPExcel Test Document")
							 ->setDescription("Test document for PHPExcel, generated using PHP classes.")
							 ->setKeywords("office PHPExcel php")
							 ->setCategory("Test result file");


// Add some data
echo date('H:i:s') , " Add some data" , EOL;

$html1 = '<font color="#0000ff">
<h1 align="center">My very first example of rich text<br />generated from html markup</h1>
<p>
<font size="14" COLOR="rgb(0,255,128)">
<b>This block</b> contains an <i>italicized</i> word;
while this block uses an <u>underline</u>.
</font>
</p>
<p align="right"><font size="9" color="red">
I want to eat <ins><del>healthy food</del> <strong>pizza</strong></ins>.
</font>
';

$html2 = '<p>
<font color="#ff0000">
    100&deg;C is a hot temperature
</font>
<br>
<font color="#0080ff">
    10&deg;F is cold
</font>
</p>';

$html3 = '2<sup>3</sup> equals 8';

$html4 = 'H<sub>2</sub>SO<sub>4</sub> is the chemical formula for Sulphuric acid';

$html5 = '<strong>bold</strong>, <em>italic</em>, <strong><em>bold+italic</em></strong>';


$wizard = new PHPExcel_Helper_HTML;
$richText = $wizard->toRichTextObject($html1);

$objPHPExcel->setActiveSheetIndex(0)
    ->setCellValue('A1', $richText);

$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(48);
$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);
$objPHPExcel->getActiveSheet()->getStyle('A1')
    ->getAlignment()
    ->setWrapText(true);

$richText = $wizard->toRichTextObject($html2);

$objPHPExcel->getActiveSheet()
    ->setCellValue('A2', $richText);

$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(-1);
$objPHPExcel->getActiveSheet()->getStyle('A2')
    ->getAlignment()
    ->setWrapText(true);

$objPHPExcel->getActiveSheet()
    ->setCellValue('A3', $wizard->toRichTextObject($html3));

$objPHPExcel->getActiveSheet()
    ->setCellValue('A4', $wizard->toRichTextObject($html4));

$objPHPExcel->getActiveSheet()
    ->setCellValue('A5', $wizard->toRichTextObject($html5));


// Rename worksheet
echo date('H:i:s') , " Rename worksheet" , EOL;
$objPHPExcel->getActiveSheet()->setTitle('Simple');


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
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
