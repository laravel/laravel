<?php

/** Error reporting */
error_reporting(E_ALL);
ini_set('display_errors', TRUE);
ini_set('display_startup_errors', TRUE);
date_default_timezone_set('Europe/London');

define('EOL',(PHP_SAPI == 'cli') ? PHP_EOL : '<br />');

date_default_timezone_set('Europe/London');

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

/** PHPExcel */
require_once dirname(__FILE__) . '/../Classes/PHPExcel.php';


$objPHPExcel = new PHPExcel();
$objWorksheet = $objPHPExcel->getActiveSheet();
$objWorksheet->fromArray(
	array(
		array('Counts', 		'Max', 		'Min', 		'Min Threshold', 	'Max Threshold'	),
		array(10,		 		10, 		5, 			0, 					50				),
		array(30,		 		20, 		10, 		0,	 				50				),
		array(20,		 		30, 		15, 		0,	 				50				),
		array(40,		 		10, 		0, 			0, 					50				),
		array(100,		 		40, 		5, 			0, 					50				),
	), null, 'A1', true
);
$objWorksheet->getStyle('B2:E6')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);


//	Set the Labels for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesLabels = array(
	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$B$1', NULL, 1), //Max / Open
	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$C$1', NULL, 1), //Min / Close
	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$D$1', NULL, 1), //Min Threshold / Min
	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$E$1', NULL, 1), //Max Threshold / Max
);
//	Set the X-Axis Labels
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$xAxisTickValues = array(
	new PHPExcel_Chart_DataSeriesValues('String', 'Worksheet!$A$2:$A$6', NULL, 5),	//	Counts
);
//	Set the Data values for each data series we want to plot
//		Datatype
//		Cell reference for data
//		Format Code
//		Number of datapoints in series
//		Data values
//		Data Marker
$dataSeriesValues = array(
	new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$B$2:$B$6', NULL, 5),
	new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$C$2:$C$6', NULL, 5),
	new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$D$2:$D$6', NULL, 5),
	new PHPExcel_Chart_DataSeriesValues('Number', 'Worksheet!$E$2:$E$6', NULL, 5),
);

//	Build the dataseries
$series = new PHPExcel_Chart_DataSeries(
	PHPExcel_Chart_DataSeries::TYPE_STOCKCHART,	// plotType
	null,										// plotGrouping - if we set this to not null, then xlsx throws error
	range(0, count($dataSeriesValues)-1),		// plotOrder
	$dataSeriesLabels,							// plotLabel
	$xAxisTickValues,							// plotCategory
	$dataSeriesValues							// plotValues
);

//	Set the series in the plot area
$plotArea = new PHPExcel_Chart_PlotArea(NULL, array($series));
//	Set the chart legend
$legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_RIGHT, NULL, false);

$title = new PHPExcel_Chart_Title('Test Stock Chart');
$xAxisLabel = new PHPExcel_Chart_Title('Counts');
$yAxisLabel = new PHPExcel_Chart_Title('Values');

//	Create the chart
$chart = new PHPExcel_Chart(
	'stock-chart',	// name
	$title,			// title
	$legend,		// legend
	$plotArea,		// plotArea
	true,			// plotVisibleOnly
	0,				// displayBlanksAs
	$xAxisLabel,	// xAxisLabel
	$yAxisLabel		// yAxisLabel
);

//	Set the position where the chart should appear in the worksheet
$chart->setTopLeftPosition('A7');
$chart->setBottomRightPosition('H20');

//	Add the chart to the worksheet
$objWorksheet->addChart($chart);


// Save Excel 2007 file
echo date('H:i:s') , " Write to Excel2007 format" , EOL;
$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
$objWriter->setIncludeCharts(TRUE);
$filename = str_replace('.php', '.xlsx', __FILE__);
if(file_exists($filename)) {
	unlink($filename);
}
$objWriter->save($filename);
echo date('H:i:s') , " File written to " , str_replace('.php', '.xlsx', pathinfo(__FILE__, PATHINFO_BASENAME)) , EOL;


// Echo memory peak usage
echo date('H:i:s') , " Peak memory usage: " , (memory_get_peak_usage(true) / 1024 / 1024) , " MB" , EOL;

// Echo done
echo date('H:i:s') , " Done writing file" , EOL;
echo 'File has been created in ' , getcwd() , EOL;
