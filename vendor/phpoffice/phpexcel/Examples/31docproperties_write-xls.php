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


$inputFileType = 'Excel5';
$inputFileName = 'templates/31docproperties.xls';


echo date('H:i:s') , " Load Tests from $inputFileType file" , EOL;
$callStartTime = microtime(true);

$objPHPExcelReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objPHPExcelReader->load($inputFileName);

$callEndTime = microtime(true);
$callTime = $callEndTime - $callStartTime;
echo 'Call time to read Workbook was ' , sprintf('%.4f',$callTime) , " seconds" , EOL;
// Echo memory usage
echo date('H:i:s') , ' Current memory usage: ' , (memory_get_usage(true) / 1024 / 1024) , " MB" , EOL;


echo date('H:i:s') , " Adjust properties" , EOL;
$objPHPExcel->getProperties()->setTitle("Office 95 XLS Test Document")
							 ->setSubject("Office 95 XLS Test Document")
							 ->setDescription("Test XLS document, generated using PHPExcel")
							 ->setKeywords("office 95 biff php");


// Save Excel 95 file
echo date('H:i:s') , " Write to Excel5 format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
$objWriter->save(str_replace('.php', '.xls', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xls', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " . (memory_get_peak_usage(true) / 1024 / 1024) . " MB" , EOL;


echo EOL;
// Reread File
echo date('H:i:s') , " Reread Excel5 file" , EOL;
$objPHPExcelRead = PHPExcel_IOFactory::load(str_replace('.php', '.xls', __FILE__));

// Set properties
echo date('H:i:s') , " Get properties" , EOL;

echo 'Core Properties:' , EOL;
echo '    Created by - ' , $objPHPExcel->getProperties()->getCreator() , EOL;
echo '    Created on - ' , date('d-M-Y',$objPHPExcel->getProperties()->getCreated()) , ' at ' ,
                       date('H:i:s',$objPHPExcel->getProperties()->getCreated()) , EOL;
echo '    Last Modified by - ' , $objPHPExcel->getProperties()->getLastModifiedBy() , EOL;
echo '    Last Modified on - ' , date('d-M-Y',$objPHPExcel->getProperties()->getModified()) , ' at ' ,
                             date('H:i:s',$objPHPExcel->getProperties()->getModified()) , EOL;
echo '    Title - ' , $objPHPExcel->getProperties()->getTitle() , EOL;
echo '    Subject - ' , $objPHPExcel->getProperties()->getSubject() , EOL;
echo '    Description - ' , $objPHPExcel->getProperties()->getDescription() , EOL;
echo '    Keywords: - ' , $objPHPExcel->getProperties()->getKeywords() , EOL;


echo 'Extended (Application) Properties:' , EOL;
echo '    Category - ' , $objPHPExcel->getProperties()->getCategory() , EOL;
echo '    Company - ' , $objPHPExcel->getProperties()->getCompany() , EOL;
echo '    Manager - ' , $objPHPExcel->getProperties()->getManager() , EOL;


echo 'Custom Properties:' , EOL;
$customProperties = $objPHPExcel->getProperties()->getCustomProperties();
foreach($customProperties as $customProperty) {
	$propertyValue = $objPHPExcel->getProperties()->getCustomPropertyValue($customProperty);
	$propertyType = $objPHPExcel->getProperties()->getCustomPropertyType($customProperty);
	echo '    ' , $customProperty , ' - (' , $propertyType , ') - ';
	if ($propertyType == PHPExcel_DocumentProperties::PROPERTY_TYPE_DATE) {
		echo date('d-M-Y H:i:s',$propertyValue) , EOL;
	} elseif ($propertyType == PHPExcel_DocumentProperties::PROPERTY_TYPE_BOOLEAN) {
		echo (($propertyValue) ? 'TRUE' : 'FALSE') , EOL;
	} else {
		echo $propertyValue , EOL;
	}
}

// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) . " MB" , EOL;
