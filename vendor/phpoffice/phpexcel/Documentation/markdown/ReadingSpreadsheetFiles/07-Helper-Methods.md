# PHPExcel User Documentation – Reading Spreadsheet Files


## Helper Methods

You can retrieve a list of worksheet names contained in a file without loading the whole file by using the Reader’s `listWorksheetNames()` method; similarly, a `listWorksheetInfo()` method will retrieve the dimensions of worksheet in a file without needing to load and parse the whole file.

### listWorksheetNames

The `listWorksheetNames()` method returns a simple array listing each worksheet name within the workbook:

```php
$objReader = PHPExcel_IOFactory::createReader($inputFileType);

$worksheetNames = $objReader->listWorksheetNames($inputFileName);

echo '<h3>Worksheet Names</h3>';
echo '<ol>';
foreach ($worksheetNames as $worksheetName) {
    echo '<li>', $worksheetName, '</li>';
}
echo '</ol>';
```
 > See Examples/Reader/exampleReader18.php for a working example of this code.

### listWorksheetInfo

The `listWorksheetInfo()` method returns a nested array, with each entry listing the name and dimensions for a worksheet:

```php
$objReader = PHPExcel_IOFactory::createReader($inputFileType);

$worksheetData = $objReader->listWorksheetInfo($inputFileName);

echo '<h3>Worksheet Information</h3>';
echo '<ol>';
foreach ($worksheetData as $worksheet) {
    echo '<li>', $worksheet['worksheetName'], '<br />';
    echo 'Rows: ', $worksheet['totalRows'],
         ' Columns: ', $worksheet['totalColumns'], '<br />';
    echo 'Cell Range: A1:',
    $worksheet['lastColumnLetter'], $worksheet['totalRows'];
    echo '</li>';
}
echo '</ol>';
```
 > See Examples/Reader/exampleReader19.php for a working example of this code.
