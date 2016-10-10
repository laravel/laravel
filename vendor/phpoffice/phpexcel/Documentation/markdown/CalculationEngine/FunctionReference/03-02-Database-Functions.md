# Calculation Engine - Formula Function Reference

## Function Reference

### Database Functions

#### DAVERAGE

The DAVERAGE function returns the average value of the cells in a column of a list or database that match conditions you specify.

##### Syntax

```
DAVERAGE (database, field, criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria** The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**float** The average value of the matching cells.

This is the statistical mean.

##### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DAVERAGE(A4:E10,"Yield",A1:B2)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();
// $retVal = 12
```

##### Notes

There are no additional notes on this function

#### DCOUNT

The DCOUNT function returns the count of cells that contain a number in a column of a list or database matching conditions that you specify.

##### Syntax

```
DCOUNT(database, [field], criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria** The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**float**  The count of the matching cells.

##### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DCOUNT(A4:E10,"Height",A1:B3)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();

// $retVal = 3
```

##### Notes

In MS Excel, The field argument is optional. If field is omitted, DCOUNT counts all records in the database that match the criteria. This logic has not yet been implemented in PHPExcel.

#### DCOUNTA

The DCOUNT function returns the count of cells that aren’t blank in a column of a list or database and that match conditions that you specify.

##### Syntax

```
DCOUNTA(database, [field], criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria** The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**float** The count of the matching cells.

##### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DCOUNTA(A4:E10,"Yield",A1:A3)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();

// $retVal = 5
```

##### Notes

In MS Excel, The field argument is optional. If field is omitted, DCOUNTA counts all records in the database that match the criteria. This logic has not yet been implemented in PHPExcel.

#### DGET

The DGET function extracts a single value from a column of a list or database that matches conditions that you specify.

##### Syntax

```
DGET(database, field, criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria** The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**mixed** The value from the selected column of the matching row.

#### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=GET(A4:E10,"Age",A1:F2)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();
// $retVal = 14
```

##### Notes

There are no additional notes on this function

#### DMAX

The DMAX function returns the largest number in a column of a list or database that matches conditions you specify.

##### Syntax

```
DMAX(database, field, criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria** The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**float** The maximum value of the matching cells.

##### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DMAX(A4:E10,"Profit",A1:B2)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();
// $retVal = 105
```

##### Notes

There are no additional notes on this function

#### DMIN

The DMIN function returns the smallest number in a column of a list or database that matches conditions you specify.

##### Syntax

```
DMIN(database, field, criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria**  The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**float** The minimum value of the matching cells.

##### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DMIN(A4:E10,"Yield",A1:A3)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();
// $retVal = 6
```

##### Notes

There are no additional notes on this function

#### DPRODUCT

The DPRODUCT function multiplies the values in a column of a list or database that match conditions that you specify.

##### Syntax

```
DPRODUCT(database, field, criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria** The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**float** The product of the matching cells.

##### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DPRODUCT(A4:E10,"Yield",A1:B2)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();
// $retVal = 140
```

##### Notes

There are no additional notes on this function

#### DSTDEV

The DSTDEV function estimates the standard deviation of a population based on a sample by using the numbers in a column of a list or database that match conditions that you specify.

##### Syntax

```
DSTDEV(database, field, criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria** The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**float** The estimated standard deviation of the matching cells.

##### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DSTDEV(A4:E10,"Yield",A1:A3)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();
// $retVal = 2.97
```

##### Notes

There are no additional notes on this function

#### DSTDEVP

The DSTDEVP function calculates the standard deviation of a population based on the entire population by using the numbers in a column of a list or database that match conditions that you specify.

##### Syntax

```
DSTDEVP(database, field, criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria** The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**float** The estimated standard deviation of the matching cells.

##### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DSTDEVP(A4:E10,"Yield",A1:A3)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();
// $retVal = 2.65
```

##### Notes

There are no additional notes on this function

#### DSUM

The DSUM function adds the numbers in a column of a list or database that matches conditions you specify.

##### Syntax

```
DSUM(database, field, criteria)
```

##### Parameters

**database** The range of cells that makes up the list or database.  

A database is a list of related data in which rows of related information are records, and columns of data are fields. The first row of the list contains labels for each column.

**field** Indicates which column of the database is used in the function.  

Enter the column label as a string (enclosed between double quotation marks), such as "Age" or "Yield," or as a number (without quotation marks) that represents the position of the column within the list: 1 for the first column, 2 for the second column, and so on.

**criteria** The range of cells that contains the conditions you specify.  

You can use any range for the criteria argument, as long as it includes at least one column label and at least one cell below the column label in which you specify a condition for the column.

##### Return Value

**float** The total value of the matching cells.

##### Examples

```php
$database = array( 
    array( 'Tree',  'Height', 'Age', 'Yield', 'Profit' ),
    array( 'Apple',  18,       20,    14,      105.00  ),
    array( 'Pear',   12,       12,    10,       96.00  ),
    array( 'Cherry', 13,       14,     9,      105.00  ),
    array( 'Apple',  14,       15,    10,       75.00  ),
    array( 'Pear',    9,        8,     8,       76.80  ),
    array( 'Apple',   8,        9,     6,       45.00  ),
);

$criteria = array( 
    array( 'Tree',      'Height', 'Age', 'Yield', 'Profit', 'Height' ),
    array( '="=Apple"', '>10',    NULL,  NULL,    NULL,     '<16'    ),
    array( '="=Pear"',  NULL,     NULL,  NULL,    NULL,     NULL     ),
);

$worksheet->fromArray( $criteria, NULL, 'A1' )
    ->fromArray( $database, NULL, 'A4' );

$worksheet->setCellValue('A12', '=DMIN(A4:E10,"Profit",A1:A2)');

$retVal = $worksheet->getCell('A12')->getCalculatedValue();
// $retVal = 225
```

##### Notes

There are no additional notes on this function

#### DVAR

Not yet documented.

#### DVARP

Not yet documented.

