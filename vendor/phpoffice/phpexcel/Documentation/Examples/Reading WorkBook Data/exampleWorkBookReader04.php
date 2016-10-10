<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reading WorkBook Data Example #04</title>

</head>
<body>

<h1>PHPExcel Reading WorkBook Data Example #04</h1>
<h2>Get a List of the Worksheets in a WorkBook</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';


$inputFileType = 'Excel5';
$inputFileName = './sampleData/example2.xls';

/**  Create a new Reader of the type defined in $inputFileType  **/
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
/**  Load $inputFileName to a PHPExcel Object  **/
$objPHPExcel = $objReader->load($inputFileName);


echo '<hr />';

echo 'Reading the number of Worksheets in the WorkBook<br />';
/**  Use the PHPExcel object's getSheetCount() method to get a count of the number of WorkSheets in the WorkBook  */
$sheetCount = $objPHPExcel->getSheetCount();
echo 'There ',(($sheetCount == 1) ? 'is' : 'are'),' ',$sheetCount,' WorkSheet',(($sheetCount == 1) ? '' : 's'),' in the WorkBook<br /><br />';

echo 'Reading the names of Worksheets in the WorkBook<br />';
/**  Use the PHPExcel object's getSheetNames() method to get an array listing the names/titles of the WorkSheets in the WorkBook  */
$sheetNames = $objPHPExcel->getSheetNames();
foreach($sheetNames as $sheetIndex => $sheetName) {
	echo 'WorkSheet #',$sheetIndex,' is named "',$sheetName,'"<br />';
}


?>
<body>
</html>
