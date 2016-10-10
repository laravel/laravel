# PHPExcel User Documentation – Reading Spreadsheet Files


## Creating a Reader and Loading a Spreadsheet File

If you know the file type of the spreadsheet file that you need to load, you can instantiate a new reader object for that file type, then use the reader's load() method to read the file to a PHPExcel object. It is possible to instantiate the reader objects for each of the different supported filetype by name. However, you may get unpredictable results if the file isn't of the right type (e.g. it is a CSV with an extension of .xls), although this type of exception should normally be trapped.

```php
$inputFileName = './sampleData/example1.xls';

/** Create a new Excel5 Reader  **/
$objReader = new PHPExcel_Reader_Excel5();
//    $objReader = new PHPExcel_Reader_Excel2007();
//    $objReader = new PHPExcel_Reader_Excel2003XML();
//    $objReader = new PHPExcel_Reader_OOCalc();
//    $objReader = new PHPExcel_Reader_SYLK();
//    $objReader = new PHPExcel_Reader_Gnumeric();
//    $objReader = new PHPExcel_Reader_CSV();
/** Load $inputFileName to a PHPExcel Object  **/
$objPHPExcel = $objReader->load($inputFileName);
```
 > See Examples/Reader/exampleReader02.php for a working example of this code.

Alternatively, you can use the IO Factory's createReader() method to instantiate the reader object for you, simply telling it the file type of the reader that you want instantiating.

```php
$inputFileType = 'Excel5';
//    $inputFileType = 'Excel2007';
//    $inputFileType = 'Excel2003XML';
//    $inputFileType = 'OOCalc';
//    $inputFileType = 'SYLK';
//    $inputFileType = 'Gnumeric';
//    $inputFileType = 'CSV';
$inputFileName = './sampleData/example1.xls';

/**  Create a new Reader of the type defined in $inputFileType  **/
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
/**  Load $inputFileName to a PHPExcel Object  **/
$objPHPExcel = $objReader->load($inputFileName);
```
 > See Examples/Reader/exampleReader03.php for a working example of this code.

If you're uncertain of the filetype, you can use the IO Factory's identify() method to identify the reader that you need, before using the createReader() method to instantiate the reader object.

```php
$inputFileName = './sampleData/example1.xls';

/**  Identify the type of $inputFileName  **/
$inputFileType = PHPExcel_IOFactory::identify($inputFileName);
/**  Create a new Reader of the type that has been identified  **/
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
/**  Load $inputFileName to a PHPExcel Object  **/
$objPHPExcel = $objReader->load($inputFileName);
```
 > See Examples/Reader/exampleReader04.php for a working example of this code.

