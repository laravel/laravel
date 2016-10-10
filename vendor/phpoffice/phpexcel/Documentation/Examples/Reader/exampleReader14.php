<?php

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reader Example #15</title>

</head>
<body>

<h1>PHPExcel Reader Example #14</h1>
<h2>Reading a Large CSV file in "Chunks" to split across multiple Worksheets</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';


$inputFileType = 'CSV';
$inputFileName = './sampleData/example2.csv';

/**  Define a Read Filter class implementing PHPExcel_Reader_IReadFilter  */
class chunkReadFilter implements PHPExcel_Reader_IReadFilter
{
	private $_startRow = 0;

	private $_endRow = 0;

	/**  Set the list of rows that we want to read  */
	public function setRows($startRow, $chunkSize) {
		$this->_startRow	= $startRow;
		$this->_endRow		= $startRow + $chunkSize;
	}

	public function readCell($column, $row, $worksheetName = '') {
		//  Only read the heading row, and the rows that are configured in $this->_startRow and $this->_endRow
		if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) {
			return true;
		}
		return false;
	}
}


echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />';
/**  Create a new Reader of the type defined in $inputFileType  **/
$objReader = PHPExcel_IOFactory::createReader($inputFileType);


echo '<hr />';


/**  Define how many rows we want to read for each "chunk"  **/
$chunkSize = 100;
/**  Create a new Instance of our Read Filter  **/
$chunkFilter = new chunkReadFilter();

/**  Tell the Reader that we want to use the Read Filter that we've Instantiated  **/
/**    and that we want to store it in contiguous rows/columns  **/
$objReader->setReadFilter($chunkFilter)
		  ->setContiguous(true);


/**  Instantiate a new PHPExcel object manually  **/
$objPHPExcel = new PHPExcel();

/**  Set a sheet index  **/
$sheet = 0;
/**  Loop to read our worksheet in "chunk size" blocks  **/
/**  $startRow is set to 2 initially because we always read the headings in row #1  **/
for ($startRow = 2; $startRow <= 240; $startRow += $chunkSize) {
	echo 'Loading WorkSheet #',($sheet+1),' using configurable filter for headings row 1 and for rows ',$startRow,' to ',($startRow+$chunkSize-1),'<br />';
	/**  Tell the Read Filter, the limits on which rows we want to read this iteration  **/
	$chunkFilter->setRows($startRow,$chunkSize);

    /**  Increment the worksheet index pointer for the Reader  **/
    $objReader->setSheetIndex($sheet);
	/**  Load only the rows that match our filter into a new worksheet in the PHPExcel Object  **/
    $objReader->loadIntoExisting($inputFileName,$objPHPExcel);
    /**  Set the worksheet title (to reference the "sheet" of data that we've loaded)  **/
    /**    and increment the sheet index as well  **/
    $objPHPExcel->getActiveSheet()->setTitle('Country Data #'.(++$sheet));
}


echo '<hr />';

echo $objPHPExcel->getSheetCount(),' worksheet',(($objPHPExcel->getSheetCount() == 1) ? '' : 's'),' loaded<br /><br />';
$loadedSheetNames = $objPHPExcel->getSheetNames();
foreach($loadedSheetNames as $sheetIndex => $loadedSheetName) {
	echo '<b>Worksheet #',$sheetIndex,' -> ',$loadedSheetName,'</b><br />';
	$objPHPExcel->setActiveSheetIndexByName($loadedSheetName);
	$sheetData = $objPHPExcel->getActiveSheet()->toArray(null,false,false,true);
	var_dump($sheetData);
	echo '<br />';
}

?>
<body>
</html>