<?php

error_reporting(E_ALL);
set_time_limit(0);

date_default_timezone_set('Europe/London');

?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PHPExcel Reading WorkBook Data Example #03</title>

</head>
<body>

<h1>PHPExcel Reading WorkBook Data Example #03</h1>
<h2>Read Custom Property Values for a WorkBook</h2>
<?php

/** Include path **/
set_include_path(get_include_path() . PATH_SEPARATOR . '../../../Classes/');

/** PHPExcel_IOFactory */
include 'PHPExcel/IOFactory.php';


$inputFileType = 'Excel2007';
$inputFileName = './sampleData/example1.xlsx';

/**  Create a new Reader of the type defined in $inputFileType  **/
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
/**  Load $inputFileName to a PHPExcel Object  **/
$objPHPExcel = $objReader->load($inputFileName);


echo '<hr />';

/**  Read an array list of any custom properties for this document  **/
$customPropertyList = $objPHPExcel->getProperties()->getCustomProperties();

echo '<b>Custom Properties: </b><br />';
/**  Loop through the list of custom properties  **/
foreach($customPropertyList as $customPropertyName) {
	echo '<b>',$customPropertyName,': </b>';
	/**  Retrieve the property value  **/
	$propertyValue = $objPHPExcel->getProperties()->getCustomPropertyValue($customPropertyName);
	/**  Retrieve the property type  **/
	$propertyType = $objPHPExcel->getProperties()->getCustomPropertyType($customPropertyName);

	/**  Manipulate properties as appropriate for display purposes  **/
	switch($propertyType) {
		case 'i' :	//	integer
			$propertyType = 'integer number';
			break;
		case 'f' :	//	float
			$propertyType = 'floating point number';
			break;
		case 's' :	//	string
			$propertyType = 'string';
			break;
		case 'd' :	//	date
			$propertyValue = date('l, d<\s\up>S</\s\up> F Y g:i A',$propertyValue);
			$propertyType = 'date';
			break;
		case 'b' :	//	boolean
			$propertyValue = ($propertyValue) ? 'TRUE' : 'FALSE';
			$propertyType = 'boolean';
			break;
	}

	echo $propertyValue,' (',$propertyType,')<br />';
}



?>
<body>
</html>
