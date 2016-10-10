# PHPExcel Developer Documentation


## Creating a spreadsheet

### The PHPExcel class

The PHPExcel class is the core of PHPExcel. It contains references to the contained worksheets, document security settings and document meta data.

To simplify the PHPExcel concept: the PHPExcel class represents your workbook.

Typically, you will create a workbook in one of two ways, either by loading it from a spreadsheet file, or creating it manually. A third option, though less commonly used, is cloning an existing workbook that has been created using one of the previous two methods.

#### Loading a Workbook from a file

Details of the different spreadsheet formats supported, and the options available to read them into a PHPExcel object are described fully in the PHPExcel User Documentation - Reading Spreadsheet Files document.

```php
$inputFileName = './sampleData/example1.xls';

/** Load $inputFileName to a PHPExcel Object **/
$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
```

#### Creating a new workbook

If you want to create a new workbook, rather than load one from file, then you simply need to instantiate it as a new PHPExcel object.

```php
/** Create a new PHPExcel Object **/
$objPHPExcel = new PHPExcel();
```

A new workbook will always be created with a single worksheet.
