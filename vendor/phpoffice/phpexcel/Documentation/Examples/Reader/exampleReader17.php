<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #17</title>

</head>
<body>

<h1>PHPExcel Reader Example #17</h1>
<h2>Simple File Reader Loading Several Named WorkSheets</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';


$inputFileType = 'Excel5';
//	$inputFileType = 'Excel2007';
//	$inputFileType = 'Excel2003XML';
//	$inputFileType = 'OOCalc';
//	$inputFileType = 'Gnumeric';
$inputFileName = './sampleData/example1.xls';

echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
$objReader = PHPExcel_IOFactory::createReader($inputFileType);


/**  Read the list of Worksheet Names from the Workbook file  **/
echo 'Read the list of Worksheets in the WorkBook<br />';
$worksheetNames = $objReader->listWorksheetNames($inputFileName);

echo 'There are ',count($worksheetNames),' worksheet',((count($worksheetNames) == 1) ? '' : 's'),' in the workbook<br /><br />';
foreach($worksheetNames as $worksheetName) {
	echo $worksheetName,'<br />';
}


?>
<body>
</html>