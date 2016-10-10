# PHPExcel AutoFilter Reference 


## Autofilter Expressions

PHPEXcel 1.7.8 introduced the ability to actually create, read and write filter expressions; initially only for Excel2007 files, but later releases will extend this to other formats.

To apply a filter expression to an autoFilter range, you first need to identify which column you're going to be applying this filter to.

```php
$autoFilter = $objPHPExcel->getActiveSheet()->getAutoFilter();
$columnFilter = $autoFilter->getColumn('C');
```

This returns an autoFilter column object, and you can then apply filter expressions to that column.

There are a number of different types of autofilter expressions. The most commonly used are:

 - Simple Filters
 - DateGroup Filters
 - Custom filters
 - Dynamic Filters
 - Top Ten Filters

These different types are mutually exclusive within any single column. You should not mix the different types of filter in the same column. PHPExcel will not actively prevent you from doing this, but the results are unpredictable.

Other filter expression types (such as cell colour filters) are not yet supported.
