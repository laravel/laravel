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
$objPHPExcel->getActiveSheet()->setCellValue('A1', 'Financial Year')
                              ->setCellValue('B1', 'Financial Period')
                              ->setCellValue('C1', 'Country')
                              ->setCellValue('D1', 'Date')
                              ->setCellValue('E1', 'Sales Value')
                              ->setCellValue('F1', 'Expenditure')
                              ;
$startYear = $endYear = $currentYear = date('Y');
$startYear--;
$endYear++;

$years = range($startYear,$endYear);
$periods = range(1,12);
$countries = array(	'United States',	'UK',		'France',	'Germany',
					'Italy',			'Spain',	'Portugal',	'Japan'
				  );

$row = 2;
foreach($years as $year) {
	foreach($periods as $period) {
		foreach($countries as $country) {
			$endDays = date('t',mktime(0,0,0,$period,1,$year));
			for($i = 1; $i <= $endDays; ++$i) {
				$eDate = PHPExcel_Shared_Date::FormattedPHPToExcel(
					$year,
					$period,
					$i
				);
				$value = rand(500,1000) * (1 + rand(-0.25,+0.25));
				$salesValue = $invoiceValue = NULL;
				$incomeOrExpenditure = rand(-1,1);
				if ($incomeOrExpenditure == -1) {
					$expenditure = rand(-500,-1000) * (1 + rand(-0.25,+0.25));
					$income = NULL;
				} elseif ($incomeOrExpenditure == 1) {
					$expenditure = rand(-500,-1000) * (1 + rand(-0.25,+0.25));
					$income = rand(500,1000) * (1 + rand(-0.25,+0.25));;
				} else {
					$expenditure = NULL;
					$income = rand(500,1000) * (1 + rand(-0.25,+0.25));;
				}
				$dataArray = array(	$year,
									$period,
									$country,
									$eDate,
									$income,
									$expenditure,
								  );
				$objPHPExcel->getActiveSheet()->fromArray($dataArray, NULL, 'A'.$row++);
			}
		}
	}
}
$row--;


// Set styling
echo date('H:i:s').' Set styling'.EOL;
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFont()->setBold(true);
$objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getAlignment()->setWrapText(TRUE);
$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(12.5);
$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10.5);
$objPHPExcel->getActiveSheet()->getStyle('D2:D'.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
$objPHPExcel->getActiveSheet()->getStyle('E2:F'.$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_USD_SIMPLE);
$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(14);
$objPHPExcel->getActiveSheet()->freezePane('A2');



// Set autofilter range
echo date('H:i:s').' Set autofilter range'.EOL;
// Always include the complete filter range!
// Excel does support setting only the caption
// row, but that's not a best practise...
$objPHPExcel->getActiveSheet()->setAutoFilter($objPHPExcel->getActiveSheet()->calculateWorksheetDimension());

// Set active filters
$autoFilter = $objPHPExcel->getActiveSheet()->getAutoFilter();
echo date('H:i:s').' Set active filters'.EOL;
// Filter the Country column on a filter value of countries beginning with the letter U (or Japan)
//     We use * as a wildcard, so specify as U* and using a wildcard requires customFilter
$autoFilter->getColumn('C')
    ->setFilterType(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER)
    ->createRule()
		->setRule(
			PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
			'u*'
		)
		->setRuleType(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
$autoFilter->getColumn('C')
    ->createRule()
		->setRule(
			PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
			'japan'
		)
		->setRuleType(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_CUSTOMFILTER);
// Filter the Date column on a filter value of the first day of every period of the current year
//	We us a dateGroup ruletype for this, although it is still a standard filter
foreach($periods as $period) {
	$endDate = date('t',mktime(0,0,0,$period,1,$currentYear));

	$autoFilter->getColumn('D')
	    ->setFilterType(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_FILTER)
	    ->createRule()
			->setRule(
				PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
				array(
					'year' => $currentYear,
					'month' => $period,
					'day' => $endDate
				)
			)
			->setRuleType(PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP);
}
// Display only sales values that are blank
//     Standard filter, operator equals, and value of NULL
$autoFilter->getColumn('E')
    ->setFilterType(PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_FILTER)
    ->createRule()
		->setRule(
			PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL,
			''
		);


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
