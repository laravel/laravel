<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #18</title>

</head>
<body>

<h1>PHPExcel Reader Example #18</h1>
<h2>Reading list of WorkSheets without loading entire file</h2>
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

echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' information using IOFactory with a defined reader type of ',$inputFileType,'<br />';

$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$worksheetNames = $objReader->listWorksheetNames($inputFileName);

echo '<h3>Worksheet Names</h3>';
echo '<ol>';
foreach ($worksheetNames as $worksheetName) {
	echo '<li>', $worksheetName, '</li>';
}
echo '</ol>';

?>
<body>
</html>