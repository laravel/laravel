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
$objPHPExcel->getProperties()
    ->setCreator("PHPOffice")
	->setLastModifiedBy("PHPOffice")
	->setTitle("PHPExcel Test Document")
	->setSubject("PHPExcel Test Document")
	->setDescription("Test document for PHPExcel, generated using PHP classes.")
	->setKeywords("Office PHPExcel php")
	->setCategory("Test result file");


function transpose($value) {
    return array($value);
}

// Add some data
$continentColumn = 'D';
$column = 'F';

// Set data for dropdowns
foreach(glob('./data/continents/*') as $key => $filename) {
    $continent = pathinfo($filename, PATHINFO_FILENAME);
    echo "Loading $continent", EOL;
    $continent = str_replace(' ','_',$continent);
    $countries = file($filename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $countryCount = count($countries);

    // Transpose $countries from a row to a column array
    $countries = array_map('transpose', $countries);
    $objPHPExcel->getActiveSheet()
        ->fromArray($countries, null, $column . '1');
    $objPHPExcel->addNamedRange(
        new PHPExcel_NamedRange(
            $continent, 
            $objPHPExcel->getActiveSheet(), $column . '1:' . $column . $countryCount
        )
    );
    $objPHPExcel->getActiveSheet()
        ->getColumnDimension($column)
        ->setVisible(false);

    $objPHPExcel->getActiveSheet()
        ->setCellValue($continentColumn . ($key+1), $continent);

    ++$column;
}

// Hide the dropdown data
$objPHPExcel->getActiveSheet()
    ->getColumnDimension($continentColumn)
    ->setVisible(false);

$objPHPExcel->addNamedRange(
    new PHPExcel_NamedRange(
        'Continents', 
        $objPHPExcel->getActiveSheet(), $continentColumn . '1:' . $continentColumn . ($key+1)
    )
);


// Set selection cells
$objPHPExcel->getActiveSheet()
    ->setCellValue('A1', 'Continent:');
$objPHPExcel->getActiveSheet()
    ->setCellValue('B1', 'Select continent');
$objPHPExcel->getActiveSheet()
    ->setCellValue('B3', '=' . $column . 1);
$objPHPExcel->getActiveSheet()
    ->setCellValue('B3', 'Select country');
$objPHPExcel->getActiveSheet()
    ->getStyle('A1:A3')
    ->getFont()->setBold(true);

// Set linked validators
$objValidation = $objPHPExcel->getActiveSheet()
    ->getCell('B1')
    ->getDataValidation();
$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST )
    ->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION )
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setErrorTitle('Input error')
    ->setError('Continent is not in the list.')
    ->setPromptTitle('Pick from the list')
    ->setPrompt('Please pick a continent from the drop-down list.')
    ->setFormula1('=Continents');

$objPHPExcel->getActiveSheet()
    ->setCellValue('A3', 'Country:');
$objPHPExcel->getActiveSheet()
    ->getStyle('A3')
    ->getFont()->setBold(true);

$objValidation = $objPHPExcel->getActiveSheet()
    ->getCell('B3')
    ->getDataValidation();
$objValidation->setType( PHPExcel_Cell_DataValidation::TYPE_LIST )
    ->setErrorStyle( PHPExcel_Cell_DataValidation::STYLE_INFORMATION )
    ->setAllowBlank(false)
    ->setShowInputMessage(true)
    ->setShowErrorMessage(true)
    ->setShowDropDown(true)
    ->setErrorTitle('Input error')
    ->setError('Country is not in the list.')
    ->setPromptTitle('Pick from the list')
    ->setPrompt('Please pick a country from the drop-down list.')
    ->setFormula1('=INDIRECT($B$1)');


$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(12);
$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(30);


// Set active sheet index to the first sheet, so Excel opens this as the first sheet
$objPHPExcel->setActiveSheetIndex(0);

// Save Excel 2007 file
// This linked validation list method only seems to work for Excel2007, not for Excel5
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->save(str_replace('.php', '.xlsx', __FILE__));
echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;

// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing files" , EOL;
echo 'Files have been created in ' , getcwd() , EOL;
