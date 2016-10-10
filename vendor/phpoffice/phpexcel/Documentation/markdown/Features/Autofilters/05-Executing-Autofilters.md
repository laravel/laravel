# PHPExcel AutoFilter Reference 


## Executing an AutoFilter

When an autofilter is applied in MS Excel, it sets the row hidden/visible flags for each row of the autofilter area based on the selected criteria, so that only those rows that match the filter criteria are displayed.

PHPExcel will not execute the equivalent function automatically when you set or change a filter expression, but only when the file is saved.

### Applying the Filter

If you wish to execute your filter from within a script, you need to do this manually. You can do this using the autofilters showHideRows() method.

```php
$autoFilter = $objPHPExcel->getActiveSheet()->getAutoFilter();
$autoFilter->showHideRows();
```

This will set all rows that match the filter criteria to visible, while hiding all other rows within the autofilter area.

### Displaying Filtered Rows

Simply looping through the rows in an autofilter area will still access ever row, whether it matches the filter criteria or not. To selectively access only the filtered rows, you need to test each row’s visibility settings.

```php
foreach ($objPHPExcel->getActiveSheet()->getRowIterator() as $row) {
    if ($objPHPExcel->getActiveSheet()
        ->getRowDimension($row->getRowIndex())->getVisible()) {
        echo '    Row number - ' , $row->getRowIndex() , ' ';
        echo $objPHPExcel->getActiveSheet()
            ->getCell(
                'C'.$row->getRowIndex()
            )
            ->getValue(), ' ';
        echo $objPHPExcel->getActiveSheet()
            ->getCell(
                'D'.$row->getRowIndex()
            )->getFormattedValue(), ' ';
        echo EOL;
    }
}
```
