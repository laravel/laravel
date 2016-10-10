<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');


?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Calculation Examples</title>

</head>
<body>

<h1>DATE</h1>
<h2>Returns the serial number of a particular date.</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../../Classes/');

/** Include PHPExcel */
include 'PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$worksheet = $objPHPExcel->getActiveSheet();

// Add some data
$testDates = array(	array(2012,3,26),	array(2012,2,29),	array(2012,4,1),	array(2012,12,25),
					array(2012,10,31),	array(2012,11,5),	array(2012,1,1),	array(2012,3,17),
					array(2011,2,29),	array(7,5,3),		array(2012,13,1),	array(2012,11,45),
					array(2012,0,0),	array(2012,1,0),	array(2012,0,1),
					array(2012,-2,2),	array(2012,2,-2),	array(2012,-2,-2),
				  );
$testDateCount = count($testDates);

$worksheet->fromArray($testDates,NULL,'A1',true);

for ($row = 1; $row <= $testDateCount; ++$row) {
	$worksheet->setCellValue('D'.$row, '=DATE(A'.$row.',B'.$row.',C'.$row.')');
	$worksheet->setCellValue('E'.$row, '=D'.$row);
}
$worksheet->getStyle('E1:E'.$testDateCount)
          ->getNumberFormat()
          ->setFormatCode('yyyy-mmm-dd');


echo '<hr />';


// Test the formulae
?>
<table border="1" cellspacing="0">
	<tr>
		<th colspan="3">Date Value</th>
		<th rowspan="2" valign="bottom">Formula</th>
		<th rowspan="2" valign="bottom">Excel DateStamp</th>
		<th rowspan="2" valign="bottom">Formatted DateStamp</th>
	</tr>
	<tr>
		<th>Year</th>
		<th>Month</th>
		<th>Day</th>
	<tr>
	<?php
	for ($row = 1; $row <= $testDateCount; ++$row) {
		echo '<tr>';
		    echo '<td>' , $worksheet->getCell('A'.$row)->getFormattedValue() , '</td>';
			echo '<td>' , $worksheet->getCell('B'.$row)->getFormattedValue() , '</td>';
			echo '<td>' , $worksheet->getCell('C'.$row)->getFormattedValue() , '</td>';
			echo '<td>' , $worksheet->getCell('D'.$row)->getValue() , '</td>';
			echo '<td>' , $worksheet->getCell('D'.$row)->getFormattedValue() , '</td>';
			echo '<td>' , $worksheet->getCell('E'.$row)->getFormattedValue() , '</td>';
		echo '</tr>';
	}
	?>
</table>