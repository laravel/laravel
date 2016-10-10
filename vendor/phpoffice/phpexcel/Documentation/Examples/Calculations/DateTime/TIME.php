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

<h1>TIME</h1>
<h2>Returns the serial number of a particular time.</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../../Classes/');

/** Include PHPExcel */
include 'PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$worksheet = $objPHPExcel->getActiveSheet();

// Add some data
$testDates = array(	array(3,15),		array(13,15),	array(15,15,15),	array(3,15,30),
					array(15,15,15),	array(5),		array(9,15,0),		array(9,15,-1),
					array(13,-14,-15),	array(0,0,-1)
				  );
$testDateCount = count($testDates);

$worksheet->fromArray($testDates,NULL,'A1',true);

for ($row = 1; $row <= $testDateCount; ++$row) {
	$worksheet->setCellValue('D'.$row, '=TIME(A'.$row.',B'.$row.',C'.$row.')');
	$worksheet->setCellValue('E'.$row, '=D'.$row);
}
$worksheet->getStyle('E1:E'.$testDateCount)
          ->getNumberFormat()
          ->setFormatCode('hh:mm:ss');


echo '<hr />';


// Test the formulae
?>
<table border="1" cellspacing="0">
	<tr>
		<th colspan="3">Date Value</th>
		<th rowspan="2" valign="bottom">Formula</th>
		<th rowspan="2" valign="bottom">Excel TimeStamp</th>
		<th rowspan="2" valign="bottom">Formatted TimeStamp</th>
	</tr>
	<tr>
		<th>Hour</th>
		<th>Minute</th>
		<th>Second</th>
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