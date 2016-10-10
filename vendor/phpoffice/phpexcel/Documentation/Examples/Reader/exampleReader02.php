<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');


/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #02</title>

</head>
<body>

<h1>PHPExcel Reader Example #02</h1>
<h2>Simple File Reader using a Specified Reader</h2>
<?php

$inputFileName = './sampleData/example1.xls';

echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using PHPExcel_Reader_Excel5<br />';
$objReader = new PHPExcel_Reader_Excel5();
//	$objReader = new PHPExcel_Reader_Excel2007();
//	$objReader = new PHPExcel_Reader_Excel2003XML();
//	$objReader = new PHPExcel_Reader_OOCalc();
//	$objReader = new PHPExcel_Reader_SYLK();
//	$objReader = new PHPExcel_Reader_Gnumeric();
//	$objReader = new PHPExcel_Reader_CSV();
$objPHPExcel = $objReader->load($inputFileName);


echo '<hr />';

$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
var_dump($sheetData);


?>
<body>
</html>