# PHPExcel User Documentation – Reading Spreadsheet Files


## Loading a Spreadsheet File

The simplest way to load a workbook file is to let PHPExcel's IO Factory identify the file type and load it, calling the static load() method of the PHPExcel_IOFactory class.

```php
$inputFileName = './sampleData/example1.xls';

/** Load $inputFileName to a PHPExcel Object  **/
$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
```
 > See Examples/Reader/exampleReader01.php for a working example of this code.

The load() method will attempt to identify the file type, and instantiate a loader for that file type; using it to load the file and store the data and any formatting in a PHPExcel object.

The method makes an initial guess at the loader to instantiate based on the file extension; but will test the file before actually executing the load: so if (for example) the file is actually a CSV file or contains HTML markup, but that has been given a .xls extension (quite a common practise), it will reject the Excel5 loader that it would normally use for a .xls file; and test the file using the other loaders until it finds the appropriate loader, and then use that to read the file.

While easy to implement in your code, and you don't need to worry about the file type; this isn't the most efficient method to load a file; and it lacks the flexibility to configure the loader in any way before actually reading the file into a PHPExcel object.

