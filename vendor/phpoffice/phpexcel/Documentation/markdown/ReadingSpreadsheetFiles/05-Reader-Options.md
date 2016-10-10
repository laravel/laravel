# PHPExcel User Documentation – Reading Spreadsheet Files

## Spreadsheet Reader Options

Once you have created a reader object for the workbook that you want to load, you have the opportunity to set additional options before executing the load() method.

### Reading Only Data from a Spreadsheet File

If you're only interested in the cell values in a workbook, but don't need any of the cell formatting information, then you can set the reader to read only the data values and any formulae from each cell using the setReadDataOnly() method.

```php
$inputFileType = 'Excel5';
$inputFileName = './sampleData/example1.xls';

/**  Create a new Reader of the type defined in $inputFileType  **/
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
/**  Advise the Reader that we only want to load cell data  **/
$objReader->setReadDataOnly(true);
/**  Load $inputFileName to a PHPExcel Object  **/
$objPHPExcel = $objReader->load($inputFileName);
```
 > See Examples/Reader/exampleReader05.php for a working example of this code.

It is important to note that Workbooks (and PHPExcel) store dates and times as simple numeric values: they can only be distinguished from other numeric values by the format mask that is applied to that cell. When setting read data only to true, PHPExcel doesn't read the cell format masks, so it is not possible to differentiate between dates/times and numbers.

The Gnumeric loader has been written to read the format masks for date values even when read data only has been set to true, so it can differentiate between dates/times and numbers; but this change hasn't yet been implemented for the other readers.

Reading Only Data from a Spreadsheet File applies to Readers:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |  
----------|:---:|--------|:---:|--------------|:---:|  
Excel2007 | YES | Excel5 | YES | Excel2003XML | YES |  
OOCalc    | YES | SYLK   | NO  | Gnumeric     | YES |  
CSV       | NO  | HTML   | NO

### Reading Only Named WorkSheets from a File

If your workbook contains a number of worksheets, but you are only interested in reading some of those, then you can use the setLoadSheetsOnly() method to identify those sheets you are interested in reading.

To read a single sheet, you can pass that sheet name as a parameter to the setLoadSheetsOnly() method.

```php
$inputFileType = 'Excel5'; 
$inputFileName = './sampleData/example1.xls'; 
$sheetname = 'Data Sheet #2'; 

/**  Create a new Reader of the type defined in $inputFileType  **/ 
$objReader = PHPExcel_IOFactory::createReader($inputFileType); 
/**  Advise the Reader of which WorkSheets we want to load  **/ 
$objReader->setLoadSheetsOnly($sheetname); 
/**  Load $inputFileName to a PHPExcel Object  **/ 
$objPHPExcel = $objReader->load($inputFileName); 
```
 > See Examples/Reader/exampleReader07.php for a working example of this code.

If you want to read more than just a single sheet, you can pass a list of sheet names as an array parameter to the setLoadSheetsOnly() method.

```php
$inputFileType = 'Excel5'; 
$inputFileName = './sampleData/example1.xls'; 
$sheetnames = array('Data Sheet #1','Data Sheet #3'); 

/**  Create a new Reader of the type defined in $inputFileType  **/ 
$objReader = PHPExcel_IOFactory::createReader($inputFileType); 
/**  Advise the Reader of which WorkSheets we want to load  **/ 
$objReader->setLoadSheetsOnly($sheetnames); 
/**  Load $inputFileName to a PHPExcel Object  **/ 
$objPHPExcel = $objReader->load($inputFileName);
```
 > See Examples/Reader/exampleReader08.php for a working example of this code.

To reset this option to the default, you can call the setLoadAllSheets() method.

```php
$inputFileType = 'Excel5';
$inputFileName = './sampleData/example1.xls';

/**  Create a new Reader of the type defined in $inputFileType  **/
$objReader = PHPExcel_IOFactory::createReader($inputFileType);
/**  Advise the Reader to load all Worksheets  **/
$objReader->setLoadAllSheets();
/**  Load $inputFileName to a PHPExcel Object  **/
$objPHPExcel = $objReader->load($inputFileName);
```
 > See Examples/Reader/exampleReader06.php for a working example of this code.

Reading Only Named WorkSheets from a File applies to Readers:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |  
----------|:---:|--------|:---:|--------------|:---:|  
Excel2007 | YES | Excel5 | YES | Excel2003XML | YES |  
OOCalc    | YES | SYLK   | NO  | Gnumeric     | YES |  
CSV       | NO  | HTML   | NO

### Reading Only Specific Columns and Rows from a File (Read Filters)

If you are only interested in reading part of a worksheet, then you can write a filter class that identifies whether or not individual cells should be read by the loader. A read filter must implement the PHPExcel_Reader_IReadFilter interface, and contain a readCell() method that accepts arguments of $column, $row and $worksheetName, and return a boolean true or false that indicates whether a workbook cell identified by those arguments should be read or not.

```php
$inputFileType = 'Excel5'; 
$inputFileName = './sampleData/example1.xls'; 
$sheetname = 'Data Sheet #3'; 


/**  Define a Read Filter class implementing PHPExcel_Reader_IReadFilter  */ 
class MyReadFilter implements PHPExcel_Reader_IReadFilter 
{ 
    public function readCell($column, $row, $worksheetName = '') { 
        //  Read rows 1 to 7 and columns A to E only 
        if ($row >= 1 && $row <= 7) { 
            if (in_array($column,range('A','E'))) { 
                return true; 
            } 
        } 
        return false; 
    } 
} 

/**  Create an Instance of our Read Filter  **/ 
$filterSubset = new MyReadFilter(); 

/**  Create a new Reader of the type defined in $inputFileType  **/ 
$objReader = PHPExcel_IOFactory::createReader($inputFileType); 
/**  Tell the Reader that we want to use the Read Filter  **/ 
$objReader->setReadFilter($filterSubset); 
/**  Load only the rows and columns that match our filter to PHPExcel  **/ 
$objPHPExcel = $objReader->load($inputFileName); 
```
 > See Examples/Reader/exampleReader09.php for a working example of this code.

This example is not particularly useful, because it can only be used in a very specific circumstance (when you only want cells in the range A1:E7 from your worksheet. A generic Read Filter would probably be more useful:

```php
/**  Define a Read Filter class implementing PHPExcel_Reader_IReadFilter  */ 
class MyReadFilter implements PHPExcel_Reader_IReadFilter 
{ 
    private $_startRow = 0; 
    private $_endRow   = 0; 
    private $_columns  = array(); 

    /**  Get the list of rows and columns to read  */ 
    public function __construct($startRow, $endRow, $columns) { 
        $this->_startRow = $startRow; 
        $this->_endRow   = $endRow; 
        $this->_columns  = $columns; 
    } 

    public function readCell($column, $row, $worksheetName = '') { 
        //  Only read the rows and columns that were configured 
        if ($row >= $this->_startRow && $row <= $this->_endRow) { 
            if (in_array($column,$this->_columns)) { 
                return true; 
            } 
        } 
        return false; 
    } 
} 

/**  Create an Instance of our Read Filter, passing in the cell range  **/ 
$filterSubset = new MyReadFilter(9,15,range('G','K'));
```
 > See Examples/Reader/exampleReader10.php for a working example of this code.

This can be particularly useful for conserving memory, by allowing you to read and process a large workbook in “chunks”: an example of this usage might be when transferring data from an Excel worksheet to a database.

```php
$inputFileType = 'Excel5'; 
$inputFileName = './sampleData/example2.xls'; 


/**  Define a Read Filter class implementing PHPExcel_Reader_IReadFilter  */ 
class chunkReadFilter implements PHPExcel_Reader_IReadFilter 
{ 
    private $_startRow = 0; 
    private $_endRow   = 0; 

    /**  Set the list of rows that we want to read  */ 
    public function setRows($startRow, $chunkSize) { 
        $this->_startRow = $startRow; 
        $this->_endRow   = $startRow + $chunkSize; 
    } 

    public function readCell($column, $row, $worksheetName = '') { 
        //  Only read the heading row, and the configured rows 
        if (($row == 1) || ($row >= $this->_startRow && $row < $this->_endRow)) { 
            return true; 
        } 
        return false; 
    } 
} 


/**  Create a new Reader of the type defined in $inputFileType  **/ 
$objReader = PHPExcel_IOFactory::createReader($inputFileType); 


/**  Define how many rows we want to read for each "chunk"  **/ 
$chunkSize = 2048; 
/**  Create a new Instance of our Read Filter  **/ 
$chunkFilter = new chunkReadFilter(); 

/**  Tell the Reader that we want to use the Read Filter  **/ 
$objReader->setReadFilter($chunkFilter); 

/**  Loop to read our worksheet in "chunk size" blocks  **/ 
for ($startRow = 2; $startRow <= 65536; $startRow += $chunkSize) { 
    /**  Tell the Read Filter which rows we want this iteration  **/ 
    $chunkFilter->setRows($startRow,$chunkSize); 
    /**  Load only the rows that match our filter  **/ 
    $objPHPExcel = $objReader->load($inputFileName); 
    //    Do some processing here 
} 
```
 > See Examples/Reader/exampleReader12.php for a working example of this code.

Using Read Filters applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |  
----------|:---:|--------|:---:|--------------|:---:|  
Excel2007 | YES | Excel5 | YES | Excel2003XML | YES |  
OOCalc    | YES | SYLK   | NO  | Gnumeric     | YES |  
CSV       | YES | HTML   | NO

### Combining Multiple Files into a Single PHPExcel Object

While you can limit the number of worksheets that are read from a workbook file using the setLoadSheetsOnly() method, certain readers also allow you to combine several individual "sheets" from different files into a single PHPExcel object, where each individual file is a single worksheet within that workbook. For each file that you read, you need to indicate which worksheet index it should be loaded into using the setSheetIndex() method of the $objReader, then use the loadIntoExisting() method rather than the load() method to actually read the file into that worksheet.

```php
$inputFileType = 'CSV'; 
$inputFileNames = array('./sampleData/example1.csv',
    './sampleData/example2.csv'
    './sampleData/example3.csv'
); 

/**  Create a new Reader of the type defined in $inputFileType  **/ 
$objReader = PHPExcel_IOFactory::createReader($inputFileType); 


/**  Extract the first named file from the array list  **/ 
$inputFileName = array_shift($inputFileNames); 
/**  Load the initial file to the first worksheet in a PHPExcel Object  **/ 
$objPHPExcel = $objReader->load($inputFileName); 
/**  Set the worksheet title (to the filename that we've loaded)  **/ 
$objPHPExcel->getActiveSheet()
    ->setTitle(pathinfo($inputFileName,PATHINFO_BASENAME)); 


/**  Loop through all the remaining files in the list  **/ 
foreach($inputFileNames as $sheet => $inputFileName) { 
    /**  Increment the worksheet index pointer for the Reader  **/ 
    $objReader->setSheetIndex($sheet+1); 
    /**  Load the current file into a new worksheet in PHPExcel  **/ 
    $objReader->loadIntoExisting($inputFileName,$objPHPExcel); 
    /**  Set the worksheet title (to the filename that we've loaded)  **/ 
    $objPHPExcel->getActiveSheet()
        ->setTitle(pathinfo($inputFileName,PATHINFO_BASENAME)); 
} 
```
 > See Examples/Reader/exampleReader13.php for a working example of this code.

Note that using the same sheet index for multiple sheets won't append files into the same sheet, but overwrite the results of the previous load. You cannot load multiple CSV files into the same worksheet.

Combining Multiple Files into a Single PHPExcel Object applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |  
----------|:---:|--------|:---:|--------------|:---:|  
Excel2007 | NO  | Excel5 | NO  | Excel2003XML | NO  |  
OOCalc    | NO  | SYLK   | YES | Gnumeric     | NO  |  
CSV       | YES | HTML   | NO

###  Combining Read Filters with the setSheetIndex() method to split a large CSV file across multiple Worksheets

An Excel5 BIFF .xls file is limited to 65536 rows in a worksheet, while the Excel2007 Microsoft Office Open XML SpreadsheetML .xlsx file is limited to 1,048,576 rows in a worksheet; but a CSV file is not limited other than by available disk space. This means that we wouldn’t ordinarily be able to read all the rows from a very large CSV file that exceeded those limits, and save it as an Excel5 or Excel2007 file. However, by using Read Filters to read the CSV file in “chunks” (using the chunkReadFilter Class that we defined in section  REF _Ref275604563 \r \p 5.3 above), and the setSheetIndex() method of the $objReader, we can split the CSV file across several individual worksheets.

```php
$inputFileType = 'CSV'; 
$inputFileName = './sampleData/example2.csv'; 


echo 'Loading file ',pathinfo($inputFileName,PATHINFO_BASENAME),' using IOFactory with a defined reader type of ',$inputFileType,'<br />'; 
/**  Create a new Reader of the type defined in $inputFileType  **/ 
$objReader = PHPExcel_IOFactory::createReader($inputFileType); 


/**  Define how many rows we want to read for each "chunk"  **/ 
$chunkSize = 65530; 
/**  Create a new Instance of our Read Filter  **/ 
$chunkFilter = new chunkReadFilter(); 

/**  Tell the Reader that we want to use the Read Filter  **/ 
/**    and that we want to store it in contiguous rows/columns  **/

$objReader->setReadFilter($chunkFilter)
    ->setContiguous(true);

/**  Instantiate a new PHPExcel object manually  **/ 
$objPHPExcel = new PHPExcel(); 

/**  Set a sheet index  **/ 
$sheet = 0; 
/**  Loop to read our worksheet in "chunk size" blocks  **/ 
/**  $startRow is set to 2 initially because we always read the headings in row #1  **/ 
for ($startRow = 2; $startRow <= 1000000; $startRow += $chunkSize) { 
    /**  Tell the Read Filter which rows we want to read this loop  **/ 
    $chunkFilter->setRows($startRow,$chunkSize); 

    /**  Increment the worksheet index pointer for the Reader  **/ 
    $objReader->setSheetIndex($sheet); 
    /**  Load only the rows that match our filter into a new worksheet  **/ 
    $objReader->loadIntoExisting($inputFileName,$objPHPExcel); 
    /**  Set the worksheet title for the sheet that we've justloaded)  **/
    /**    and increment the sheet index as well  **/ 
    $objPHPExcel->getActiveSheet()->setTitle('Country Data #'.(++$sheet)); 
} 
```
 > See Examples/Reader/exampleReader14.php for a working example of this code.

This code will read 65,530 rows at a time from the CSV file that we’re loading, and store each "chunk" in a new worksheet.

The setContiguous() method for the Reader is important here. It is applicable only when working with a Read Filter, and identifies whether or not the cells should be stored by their position within the CSV file, or their position relative to the filter.

For example, if the filter returned true for cells in the range B2:C3, then with setContiguous set to false (the default) these would be loaded as B2:C3 in the PHPExcel object; but with setContiguous set to true, they would be loaded as A1:B2.

Splitting a single loaded file across multiple worksheets applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |  
----------|:---:|--------|:---:|--------------|:---:|  
Excel2007 | NO  | Excel5 | NO  | Excel2003XML | NO  |  
OOCalc    | NO  | SYLK   | NO  | Gnumeric     | NO  |  
CSV       | YES | HTML   | NO

### Pipe or Tab Separated Value Files

The CSV loader defaults to loading a file where comma is used as the separator, but you can modify this to load tab- or pipe-separated value files using the setDelimiter() method.

```php
$inputFileType = 'CSV'; 
$inputFileName = './sampleData/example1.tsv'; 

/**  Create a new Reader of the type defined in $inputFileType  **/ $objReader = PHPExcel_IOFactory::createReader($inputFileType); 
/**  Set the delimiter to a TAB character  **/ 
$objReader->setDelimiter("\t"); 
//    $objReader->setDelimiter('|');

/**  Load the file to a PHPExcel Object  **/ 
$objPHPExcel = $objReader->load($inputFileName);
```
 > See Examples/Reader/exampleReader15.php for a working example of this code.

In addition to the delimiter, you can also use the following methods to set other attributes for the data load:

setEnclosure() | default is "  
setLineEnding() | default is PHP_EOL  
setInputEncoding() | default is UTF-8  

Setting CSV delimiter applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |  
----------|:---:|--------|:---:|--------------|:---:|  
Excel2007 | NO  | Excel5 | NO  | Excel2003XML | NO  |  
OOCalc    | NO  | SYLK   | NO  | Gnumeric     | NO  |  
CSV       | YES | HTML   | NO

### A Brief Word about the Advanced Value Binder

When loading data from a file that contains no formatting information, such as a CSV file, then data is read either as strings or numbers (float or integer). This means that PHPExcel does not automatically recognise dates/times (such as "16-Apr-2009" or "13:30"), booleans ("TRUE" or "FALSE"), percentages ("75%"), hyperlinks ("http://www.phpexcel.net"), etc as anything other than simple strings. However, you can apply additional processing that is executed against these values during the load process within a Value Binder.

A Value Binder is a class that implement the PHPExcel_Cell_IValueBinder interface. It must contain a bindValue() method that accepts a PHPExcel_Cell and a value as arguments, and return a boolean true or false that indicates whether the workbook cell has been populated with the value or not. The Advanced Value Binder implements such a class: amongst other tests, it identifies a string comprising "TRUE" or "FALSE" (based on locale settings) and sets it to a boolean; or a number in scientific format (e.g. "1.234e-5") and converts it to a float; or dates and times, converting them to their Excel timestamp value – before storing the value in the cell object. It also sets formatting for strings that are identified as dates, times or percentages. It could easily be extended to provide additional handling (including text or cell formatting) when it encountered a hyperlink, or HTML markup within a CSV file.

So using a Value Binder allows a great deal more flexibility in the loader logic when reading unformatted text files.

```php
/**  Tell PHPExcel that we want to use the Advanced Value Binder  **/ 
PHPExcel_Cell::setValueBinder( new PHPExcel_Cell_AdvancedValueBinder() ); 

$inputFileType = 'CSV'; 
$inputFileName = './sampleData/example1.tsv'; 

$objReader = PHPExcel_IOFactory::createReader($inputFileType); 
$objReader->setDelimiter("\t"); 
$objPHPExcel = $objReader->load($inputFileName);
```
 > See Examples/Reader/exampleReader15.php for a working example of this code.

Loading using a Value Binder applies to:

Reader    | Y/N |Reader  | Y/N |Reader        | Y/N |  
----------|:---:|--------|:---:|--------------|:---:|  
Excel2007 | NO  | Excel5 | NO  | Excel2003XML | NO  |  
OOCalc    | NO  | SYLK   | NO  | Gnumeric     | NO  |  
CSV       | YES | HTML   | YES

