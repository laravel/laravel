<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #04</title>

</head>
<body>

<h1>PHPExcel Reader Example #04</h1>
<h2>Simple File Reader using the PHPExcel_IOFactory to Identify a Reader to Use</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';


$inputFileName = './sampleData/example1.xls';

$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
echo 'File ',pathinfo($inputFileName,PATHINFO_BASENAME),' has been identified as an ',$inputFileType,' file<br />';

echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory with the identified reader type<br />';
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
$objPHPExcel = $objReader->load($inputFileName);


echo '<hr />';

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
var_dump($sheetData);


?>
<body>
</html>