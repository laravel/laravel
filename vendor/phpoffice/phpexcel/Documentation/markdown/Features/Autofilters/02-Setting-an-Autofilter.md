# PHPExcel AutoFilter Reference 


## Setting an AutoFilter area on a worksheet

To set an autoFilter on a range of cells.

```php
$objPHPExcel->getActiveSheet()->setAutoFilter('A1:E20');
```

The first row in an autofilter range will be the heading row, which displays the autoFilter dropdown icons. It is not part of the actual autoFiltered data. All subsequent rows are the autoFiltered data. So an AutoFilter range should always contain the heading row and one or more data rows (one data row is pretty meaningless, but PHPExcel won't actually stop you specifying a meaningless range: it's up to you as the developer to avoid such errors.

If you want to set the whole worksheet as an autofilter region

```php
$objPHPExcel->getActiveSheet()->setAutoFilter(
    $objPHPExcel->getActiveSheet()
        ->calculateWorksheetDimension()
);
```

This enables filtering, but does not actually apply any filters.

