# PHPExcel Developer Documentation

## Worksheets

A worksheet is a collection of cells, formulae, images, graphs, etc. It holds all data necessary to represent a spreadsheet worksheet.

When you load a workbook from a spreadsheet file, it will be loaded with all its existing worksheets (unless you specified that only certain sheets should be loaded). When you load from non-spreadsheet files (such as a CSV or HTML file) or from spreadsheet formats that don't identify worksheets by name (such as SYLK), then a single worksheet called "WorkSheet1" will be created containing the data from that file.

When you instantiate a new workbook, PHPExcel will create it with a single worksheet called "WorkSheet1"ù.

The `getSheetCount()` method will tell you the number of worksheets in the workbook; while the `getSheetNames()` method will return a list of all worksheets in the workbook, indexed by the order in which their "tabs" would appear when opened in MS Excel (or other appropriate Spreadsheet program).

Individual worksheets can be accessed by name, or by their index position in the workbook. The index position represents the order that each worksheet "tab" is shown when the workbook is opened in MS Excel (or other appropriate Spreadsheet program). To access a sheet by its index, use the `getSheet()` method.

```php
// Get the second sheet in the workbook
// Note that sheets are indexed from 0
$objPHPExcel->getSheet(1);
```

If you don't specify a sheet index, then the first worksheet will be returned.

Methods also exist allowing you to reorder the worksheets in the workbook.

To access a sheet by name, use the `getSheetByName()` method, specifying the name of the worksheet that you want to access.

```php
// Retrieve the worksheet called 'Worksheet 1'
$objPHPExcel->getSheetByName('Worksheet 1');
```

Alternatively, one worksheet is always the currently active worksheet, and you can access that directly. The currently active worksheet is the one that will be active when the workbook is opened in MS Excel (or other appropriate Spreadsheet program).

```php
// Retrieve the current active worksheet
$objPHPExcel->getActiveSheet();
```

You can change the currently active sheet by index or by name using the `setActiveSheetIndex()` and `setActiveSheetIndexByName()` methods.

### Adding a new Worksheet

You can add a new worksheet to the workbook using the `createSheet()` method of the PHPExcel object. By default, this will be created as a new "lastù" sheet; but you can also specify an index position as an argument, and the worksheet will be inserted at that position, shuffling all subsequent worksheets in the collection down a place.

```php
$objPHPExcel->createSheet();
```

A new worksheet created using this method will be called "Worksheet\<n\>"ù where "\<n\>"ù is the lowest number possible to guarantee that the title is unique.

Alternatively, you can instantiate a new worksheet (setting the title to whatever you choose) and then insert it into your workbook using the addSheet() method.

```php
// Create a new worksheet called "My Data"
$myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'My Data');

// Attach the "My Data" worksheet as the first worksheet in the PHPExcel object
$objPHPExcel->addSheet($myWorkSheet, 0);
```

If you don't specify an index position as the second argument, then the new worksheet will be added after the last existing worksheet.

### Copying Worksheets

Sheets within the same workbook can be copied by creating a clone of the worksheet you wish to copy, and then using the addSheet() method to insert the clone into the workbook.

```php
$objClonedWorksheet = clone $objPHPExcel->getSheetByName('Worksheet 1');
$objClonedWorksheet->setTitle('Copy of Worksheet 1')
$objPHPExcel->addSheet($objClonedWorksheet);
```

You can also copy worksheets from one workbook to another, though this is more complex as PHPExcel also has to replicate the styling between the two workbooks. The addExternalSheet() method is provided for this purpose.

```
$objClonedWorksheet = clone $objPHPExcel1->getSheetByName('Worksheet 1');
$objPHPExcel->addExternalSheet($objClonedWorksheet);
```

In both cases, it is the developer's responsibility to ensure that worksheet names are not duplicated. PHPExcel will throw an exception if you attempt to copy worksheets that will result in a duplicate name.

### Removing a Worksheet

You can delete a worksheet from a workbook, identified by its index position, using the removeSheetByIndex() method

```php
$sheetIndex = $objPHPExcel->getIndex(
    $objPHPExcel->getSheetByName('Worksheet 1')
);
$objPHPExcel->removeSheetByIndex($sheetIndex);
```

If the currently active worksheet is deleted, then the sheet at the previous index position will become the currently active sheet.

