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

<h1>DATEVALUE</h1>
<h2>Converts a date in the form of text to a serial number.</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../../Classes/');

/** Include PHPExcel */
include 'PHPExcel.php';


// Create new PHPExcel object
$objPHPExcel = new PHPExcel();
$worksheet = $objPHPExcel->getActiveSheet();

// Add some data
$testDates = array(	'26 March 2012',	'29 Feb 2012',	'April 1, 2012',	'25/12/2012',
					'2012-Oct-31',		'5th November',	'January 1st',		'April 2012',
					'17-03',			'03-2012',		'29 Feb 2011',		'03-05-07',
					'03-MAY-07',		'03-13-07',
				  );
$testDateCount = count($testDates);

for($row = 1; $row <= $testDateCount; ++$row) {
	$worksheet->setCellValue('A'.$row, $testDates[$row-1]);
	$worksheet->setCellValue('B'.$row, '=DATEVALUE(A'.$row.')');
	$worksheet->setCellValue('C'.$row, '=B'.$row);
}

$worksheet->getStyle('C1:C'.$testDateCount)
          ->getNumberFormat()
          ->setFormatCode('yyyy-mmm-dd');


echo '<hr />';


// Test the formulae
?>
<p><strong>Warning: </strong>The PHPExcel DATEVALUE() function accepts a wider range of date formats than MS Excel's DATEFORMAT() function.</p>
<table border="1" cellspacing="0">
	<tr>
		<th>Date String</th>
		<th>Formula</th>
		<th>Excel DateStamp</th>
		<th>Formatted DateStamp</th>
	</tr>
	<?php
	for ($row = 1; $row <= $testDateCount; ++$row) {
		echo '<tr>';
		    echo '<td>' , $worksheet->getCell('A'.$row)->getFormattedValue() , '</td>';
			echo '<td>' , $worksheet->getCell('B'.$row)->getValue() , '</td>';
			echo '<td>' , $worksheet->getCell('B'.$row)->getFormattedValue() , '</td>';
			echo '<td>' , $worksheet->getCell('C'.$row)->getFormattedValue() , '</td>';
		echo '</tr>';
	}
	?>
</table>