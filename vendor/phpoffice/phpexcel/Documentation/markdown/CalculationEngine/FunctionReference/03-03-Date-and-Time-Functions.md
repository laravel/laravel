# Calculation Engine - Formula Function Reference

## Function Reference

### Date and Time Functions

Excel provides a number of functions for the manipulation of dates and times, and calculations based on date/time values. it is worth spending some time reading the section titled "Date and Time Values" on passing date parameters and returning date values to understand how PHPExcel reconciles the differences between dates and times in Excel and in PHP.

#### DATE

The DATE function returns an Excel timestamp or a PHP timestamp or date object representing the date that is referenced by the parameters.

##### Syntax

```
DATE(year, month, day)
```

##### Parameters

**year** The year number.  

If this value is between 0 (zero) and 1899 inclusive (for the Windows 1900 calendar), or between 4 and 1903 inclusive (for the Mac 1904), then PHPExcel adds it to the Calendar base year, so a value of 108 will interpret the year as 2008 when using the Windows 1900 calendar, or 2012 when using the Mac 1904 calendar.

**month** The month number.  

If this value is greater than 12, the DATE function adds that number of months to the first month in the year specified. For example, DATE(2008,14,2) returns a value representing February 2, 2009.

If the value of __month__ is less than 1, then that value will be adjusted by -1, and that will then be subtracted from the first month of the year specified. For example, DATE(2008,0,2) returns a value representing December 2, 2007; while DATE(2008,-1,2) returns a value representing November 2, 2007.

**day** The day number.  

If this value is greater than the number of days in the month (and year) specified, the DATE function adds that number of days to the first day in the month. For example, DATE(2008,1,35) returns a value representing February 4, 2008.

If the value of __day__ is less than 1, then that value will be adjusted by -1, and that will then be subtracted from the first month of the year specified. For example, DATE(2008,3,0) returns a value representing February 29, 2008; while DATE(2008,3,-2) returns a value representing February 27, 2008.

##### Return Value

**mixed** A date/time stamp that corresponds to the given date.  

This could be a PHP timestamp value (integer), a PHP date/time object, or an Excel timestamp value (real), depending on the value of PHPExcel_Calculation_Functions::getReturnDateType().

##### Examples

```php
$worksheet->setCellValue('A1', 'Year')
    ->setCellValue('A2', 'Month')
    ->setCellValue('A3', 'Day');

$worksheet->setCellValue('B1', 2008)
    ->setCellValue('B2', 12)
    ->setCellValue('B3', 31);

$worksheet->setCellValue('D1', '=DATE(B1,B2,B3)');

$retVal = $worksheet->getCell('D1')->getCalculatedValue();
// $retVal = 1230681600
```

```php
// We're going to be calling the same cell calculation multiple times,
//    and expecting different return values, so disable calculation cacheing
PHPExcel_Calculation::getInstance()->setCalculationCacheEnabled(FALSE);

$saveFormat = PHPExcel_Calculation_Functions::getReturnDateType();

PHPExcel_Calculation_Functions::setReturnDateType(
    PHPExcel_Calculation_Functions::RETURNDATE_EXCEL
);

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATE'),
    array(2008, 12, 31)
);
// $retVal = 39813.0

PHPExcel_Calculation_Functions::setReturnDateType(
    PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC
);

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATE'),
    array(2008, 12, 31)
);
// $retVal = 1230681600

PHPExcel_Calculation_Functions::setReturnDateType($saveFormat);
```

##### Notes

There are no additional notes on this function

#### DATEDIF

The DATEDIF function computes the difference between two dates in a variety of different intervals, such number of years, months, or days.

##### Syntax

```
DATEDIF(date1, date2 [, unit])
```

##### Parameters

**date1** First Date.  

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

**date2** Second Date.  

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

**unit** The interval type to use for the calculation  

This is a string, comprising one of the values listed below:

Unit | Meaning                         | Description
-----|---------------------------------|--------------------------------
m    | Months                          | Complete calendar months between the dates.
d    | Days                            | Number of days between the dates.
y    | Years                           | Complete calendar years between the dates.
ym   | Months Excluding Years          | Complete calendar months between the dates as if they were of the same year.
yd   | Days Excluding Years            | Complete calendar days between the dates as if they were of the same year.
md   | Days Excluding Years And Months | Complete calendar days between the dates as if they were of the same month and same year.

The unit value is not case sensitive, and defaults to "d".

##### Return Value

**integer** An integer value that reflects the difference between the two dates.  

This could be the number of full days, months or years between the two dates, depending on the interval unit value passed into the function as the third parameter.

##### Examples

```php
$worksheet->setCellValue('A1', 'Year')
    ->setCellValue('A2', 'Month')
    ->setCellValue('A3', 'Day');

$worksheet->setCellValue('B1', 2001)
    ->setCellValue('C1', 2009)
    ->setCellValue('B2', 7)
    ->setCellValue('C2', 12)
    ->setCellValue('B3', 1)
    ->setCellValue('C3', 31);

$worksheet->setCellValue('D1', '=DATEDIF(DATE(B1,B2,B3),DATE(C1,C2,C3),"d")')
    ->setCellValue('D2', '=DATEDIF(DATE(B1,B2,B3),DATE(C1,C2,C3),"m")')
    ->setCellValue('D3', '=DATEDIF(DATE(B1,B2,B3),DATE(C1,C2,C3),"y")')
    ->setCellValue('D4', '=DATEDIF(DATE(B1,B2,B3),DATE(C1,C2,C3),"ym")')
    ->setCellValue('D5', '=DATEDIF(DATE(B1,B2,B3),DATE(C1,C2,C3),"yd")')
    ->setCellValue('D6', '=DATEDIF(DATE(B1,B2,B3),DATE(C1,C2,C3),"md")');

$retVal = $worksheet->getCell('D1')->getCalculatedValue();
// $retVal = 3105

$retVal = $worksheet->getCell('D2')->getCalculatedValue();
// $retVal = 101

$retVal = $worksheet->getCell('D3')->getCalculatedValue();
// $retVal = 8

$retVal = $worksheet->getCell('D4')->getCalculatedValue();
// $retVal = 5

$retVal = $worksheet->getCell('D5')->getCalculatedValue();
// $retVal = 183

$retVal = $worksheet->getCell('D6')->getCalculatedValue();
// $retVal = 30
```

```php
$date1 = 1193317015; // PHP timestamp for 25-Oct-2007
$date2 = 1449579415; // PHP timestamp for 8-Dec-2015

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATEDIF'),
    array($date1, $date2, 'd')
);
// $retVal = 2966

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATEDIF'),
    array($date1, $date2, 'm')
);
// $retVal = 97

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATEDIF'),
    array($date1, $date2, 'y')
);
// $retVal = 8

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATEDIF'),
    array($date1, $date2, 'ym')
);
// $retVal = 1

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATEDIF'),
    array($date1, $date2, 'yd')
);
// $retVal = 44

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATEDIF'),
    array($date1, $date2, 'md')
);
// $retVal = 13
```

##### Notes

If Date1 is later than Date2, DATEDIF will return a #NUM! error.

#### DATEVALUE

The DATEVALUE function returns the date represented by a date formatted as a text string. Use DATEVALUE to convert a date represented by text to a serial number.

##### Syntax

```
DATEVALUE(dateString)
```

##### Parameters

**date** Date String.  

A string, representing a date value.

##### Return Value

**mixed** A date/time stamp that corresponds to the given date.  

This could be a PHP timestamp value (integer), a PHP date/time object, or an Excel timestamp value (real), depending on the value of PHPExcel_Calculation_Functions::getReturnDateType().

##### Examples

```php
$worksheet->setCellValue('A1', 'Date String');
    ->setCellValue('A2', '31-Dec-2008')
    ->setCellValue('A3', '31/12/2008')
    ->setCellValue('A4', '12-31-2008');

$worksheet->setCellValue('B2', '=DATEVALUE(A2)')
    ->setCellValue('B3', '=DATEVALUE(A3)')
    ->setCellValue('B4', '=DATEVALUE(A4)');

PHPExcel_Calculation_Functions::setReturnDateType(
    PHPExcel_Calculation_Functions::RETURNDATE_EXCEL
);

$retVal = $worksheet->getCell('B2')->getCalculatedValue();

$retVal = $worksheet->getCell('B3')->getCalculatedValue();

$retVal = $worksheet->getCell('B4')->getCalculatedValue();
// $retVal = 39813.0 for all cases
```

```php
// We're going to be calling the same cell calculation multiple times,
//    and expecting different return values, so disable calculation cacheing
PHPExcel_Calculation::getInstance()->setCalculationCacheEnabled(FALSE);

$saveFormat = PHPExcel_Calculation_Functions::getReturnDateType();

PHPExcel_Calculation_Functions::setReturnDateType(
    PHPExcel_Calculation_Functions::RETURNDATE_EXCEL
);

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATEVALUE'),
    array('31-Dec-2008')
);
// $retVal = 39813.0

PHPExcel_Calculation_Functions::setReturnDateType(
    PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC
);

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DATEVALUE'),
    array('31-Dec-2008')
);
// $retVal = 1230681600

PHPExcel_Calculation_Functions::setReturnDateType($saveFormat);
```

##### Notes

DATEVALUE uses the php date/time object implementation of strtotime() (which can handle a wider range of formats than the normal strtotime() function), and it is also called for any date parameter passed to other date functions (such as DATEDIF) when the parameter value is a string.

__WARNING:-__ PHPExcel accepts a wider range of date formats than MS Excel, so it is entirely possible that Excel will return a #VALUE! error when passed a date string that it can’t interpret, while PHPExcel is able to translate that same string into a correct date value.

Care should be taken in workbooks that use string formatted dates in calculations when writing to Excel5 or Excel2007.

#### DAY

The DAY function returns the day of a date. The day is given as an integer ranging from 1 to 31.

##### Syntax

```
DAY(datetime)
```

##### Parameters

**datetime** Date. 

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

##### Return Value

**integer** An integer value that reflects the day of the month.

This is an integer ranging from 1 to 31.

##### Examples

```php
$worksheet->setCellValue('A1', 'Date String')
    ->setCellValue('A2', '31-Dec-2008')
    ->setCellValue('A3', '14-Feb-2008');

$worksheet->setCellValue('B2', '=DAY(A2)')
    ->setCellValue('B3', '=DAY(A3)');

$retVal = $worksheet->getCell('B2')->getCalculatedValue();
// $retVal = 31

$retVal = $worksheet->getCell('B3')->getCalculatedValue();
// $retVal = 14
```

```php
$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DAYOFMONTH'),
    array('25-Dec-2008')
);
// $retVal = 25
```

##### Notes

Note that the PHPExcel function is PHPExcel_Calculation_Functions::DAYOFMONTH() when the method is called statically.

#### DAYS360

The DAYS360 function computes the difference between two dates based on a 360 day year (12 equal periods of 30 days each) used by some accounting systems.

##### Syntax

```
DAYS360(date1, date2 [, method])
```

#### Parameters

**date1** First Date.  

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

**date2** Second Date.  

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

**method** A boolean flag (TRUE or FALSE)

This is a flag that determines which method to use in the calculation, based on the values listed below:

    method | Description
    -------|------------
    FALSE  | U.S. (NASD) method. If the starting date is the last day of a month, it becomes equal to the 30th of the same month. If the ending date is the last day of a month and the starting date is earlier than the 30th of a month, the ending date becomes equal to the 1st of the next month; otherwise the ending date becomes equal to the 30th of the same month.
    TRUE   | European method. Starting dates and ending dates that occur on the 31st of a month become equal to the 30th of the same month.

The method value defaults to FALSE.

##### Return Value

**integer** An integer value that reflects the difference between the two dates.

This is the number of full days between the two dates, based on a 360 day year.

##### Examples

```php
$worksheet->setCellValue('B1', 'Start Date')
    ->setCellValue('C1', 'End Date')
    ->setCellValue('A2', 'Year')
    ->setCellValue('A3', 'Month')
    ->setCellValue('A4', 'Day');

$worksheet->setCellValue('B2', 2003)
    ->setCellValue('B3', 2)
    ->setCellValue('B4', 3);

$worksheet->setCellValue('C2', 2007)
    ->setCellValue('C3', 5)
    ->setCellValue('C4', 31);

$worksheet->setCellValue('E2', '=DAYS360(DATE(B2,B3,B4),DATE(C2,C3,C4))')
    ->setCellValue('E4', '=DAYS360(DATE(B2,B3,B4),DATE(C2,C3,C4),FALSE)');

$retVal = $worksheet->getCell('E2')->getCalculatedValue();
// $retVal = 1558

$retVal = $worksheet->getCell('E4')->getCalculatedValue();
// $retVal = 1557
```

```php
$date1 = 37655.0; // Excel timestamp for 25-Oct-2007
$date2 = 39233.0; // Excel timestamp for 8-Dec-2015

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DAYS360'),
    array($date1, $date2)
);
// $retVal = 1558

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DAYS360'),
    array($date1, $date2, TRUE)
);
// $retVal = 1557
```

##### Notes

__WARNING:-__ This function does not currently work with the Excel5 Writer when a PHP Boolean is used for the third (optional) parameter (as shown in the example above), and the writer will generate and error. It will work if a numeric 0 or 1 is used for the method parameter; or if the Excel TRUE() and FALSE() functions are used instead.

#### EDATE

The EDATE function returns an Excel timestamp or a PHP timestamp or date object representing the date that is the indicated number of months before or after a specified date (the start_date). Use EDATE to calculate maturity dates or due dates that fall on the same day of the month as the date of issue.

##### Syntax

```
EDATE(baseDate, months)
```

##### Parameters

**baseDate** Start Date.

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

**months** Number of months to add.

An integer value indicating the number of months before or after baseDate. A positive value for months yields a future date; a negative value yields a past date.

##### Return Value

**mixed** A date/time stamp that corresponds to the basedate + months.

This could be a PHP timestamp value (integer), a PHP date/time object, or an Excel timestamp value (real), depending on the value of PHPExcel_Calculation_Functions::getReturnDateType().

##### Examples

```php
$worksheet->setCellValue('A1', 'Date String')
    ->setCellValue('A2', '1-Jan-2008')
    ->setCellValue('A3', '29-Feb-2008');

$worksheet->setCellValue('B2', '=EDATE(A2,5)')
    ->setCellValue('B3', '=EDATE(A3,-12)');

PHPExcel_Calculation_Functions::setReturnDateType(
    PHPExcel_Calculation_Functions::RETURNDATE_EXCEL
);

$retVal = $worksheet->getCell('B2')->getCalculatedValue();
// $retVal = 39600.0 (1-Jun-2008)

$retVal = $worksheet->getCell('B3')->getCalculatedValue();
// $retVal = 39141.0 (28-Feb-2007)
```

```php
PHPExcel_Calculation_Functions::setReturnDateType(
    PHPExcel_Calculation_Functions::RETURNDATE_EXCEL
);

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'EDATE'),
    array('31-Oct-2008',25)
);
// $retVal = 40512.0 (30-Nov-2010)
```

###### Notes

__WARNING:-__ This function is currently not supported by the Excel5 Writer because it is not a standard function within Excel 5, but an add-in from the Analysis ToolPak.

#### EOMONTH

The EOMONTH function returns an Excel timestamp or a PHP timestamp or date object representing the date of the last day of the month that is the indicated number of months before or after a specified date (the start_date). Use EOMONTH to calculate maturity dates or due dates that fall on the last day of the month.

##### Syntax

```
EOMONTH(baseDate, months)
```

##### Parameters

**baseDate** Start Date.

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

**months** Number of months to add.

An integer value indicating the number of months before or after baseDate. A positive value for months yields a future date; a negative value yields a past date.

##### Return Value

**mixed** A date/time stamp that corresponds to the last day of basedate + months.

This could be a PHP timestamp value (integer), a PHP date/time object, or an Excel timestamp value (real), depending on the value of PHPExcel_Calculation_Functions::getReturnDateType().

##### Examples

```php
$worksheet->setCellValue('A1', 'Date String')
    ->setCellValue('A2', '1-Jan-2000')
    ->setCellValue('A3', '14-Feb-2009');

$worksheet->setCellValue('B2', '=EOMONTH(A2,5)')
    ->setCellValue('B3', '=EOMONTH(A3,-12)');

PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);

$retVal = $worksheet->getCell('B2')->getCalculatedValue();
// $retVal = 39629.0 (30-Jun-2008)

$retVal = $worksheet->getCell('B3')->getCalculatedValue();
// $retVal = 39507.0 (29-Feb-2008)
```

```php
PHPExcel_Calculation_Functions::setReturnDateType(
    PHPExcel_Calculation_Functions::RETURNDATE_EXCEL
);

$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'EOMONTH'),
    array('31-Oct-2008',13)
);
// $retVal = 40147.0 (30-Nov-2010)
```

##### Notes

__WARNING:-__ This function is currently not supported by the Excel5 Writer because it is not a standard function within Excel 5, but an add-in from the Analysis ToolPak.

#### HOUR

The HOUR function returns the hour of a time value. The hour is given as an integer, ranging from 0 (12:00 A.M.) to 23 (11:00 P.M.).

##### Syntax

```
HOUR(datetime)
```

##### Parameters

**datetime** Time.

An Excel date/time value, PHP date timestamp, PHP date object, or a date/time represented as a string.

##### Return Value

**integer** An integer value that reflects the hour of the day.

This is an integer ranging from 0 to 23.

##### Examples

```php
$worksheet->setCellValue('A1', 'Time String')
    ->setCellValue('A2', '31-Dec-2008 17:30')
    ->setCellValue('A3', '14-Feb-2008 4:20 AM')
    ->setCellValue('A4', '14-Feb-2008 4:20 PM');

$worksheet->setCellValue('B2', '=HOUR(A2)')
    ->setCellValue('B3', '=HOUR(A3)')
    ->setCellValue('B4', '=HOUR(A4)');

$retVal = $worksheet->getCell('B2')->getCalculatedValue();
// $retVal = 17

$retVal = $worksheet->getCell('B3')->getCalculatedValue();
// $retVal = 4

$retVal = $worksheet->getCell('B4')->getCalculatedValue();
// $retVal = 16
```

```php
$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'HOUROFDAY'),
    array('09:30')
);
// $retVal = 9
```

##### Notes

Note that the PHPExcel function is PHPExcel_Calculation_Functions::HOUROFDAY() when the method is called statically.

#### MINUTE

The MINUTE function returns the minutes of a time value. The minute is given as an integer, ranging from 0 to 59.

##### Syntax

```
MINUTE(datetime)
```

##### Parameters

**datetime** Time.

An Excel date/time value, PHP date timestamp, PHP date object, or a date/time represented as a string.

##### Return Value

**integer** An integer value that reflects the minutes within the hour.

This is an integer ranging from 0 to 59.

##### Examples

```php
$worksheet->setCellValue('A1', 'Time String')
    ->setCellValue('A2', '31-Dec-2008 17:30')
    ->setCellValue('A3', '14-Feb-2008 4:20 AM')
    ->setCellValue('A4', '14-Feb-2008 4:45 PM');

$worksheet->setCellValue('B2', '=MINUTE(A2)')
    ->setCellValue('B3', '=MINUTE(A3)')
    ->setCellValue('B4', '=MINUTE(A4)');

$retVal = $worksheet->getCell('B2')->getCalculatedValue();
// $retVal = 30

$retVal = $worksheet->getCell('B3')->getCalculatedValue();
// $retVal = 20

$retVal = $worksheet->getCell('B4')->getCalculatedValue();
// $retVal = 45
```

```php
$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'MINUTEOFHOUR'),
    array('09:30')
);
// $retVal = 30
```

##### Notes

Note that the PHPExcel function is PHPExcel_Calculation_Functions::MINUTEOFHOUR() when the method is called statically.

#### MONTH

The MONTH function returns the month of a date. The month is given as an integer ranging from 1 to 12.

##### Syntax

```
MONTH(datetime)
```

##### Parameters

**datetime** Date.

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

##### Return Value

**integer** An integer value that reflects the month of the year.

This is an integer ranging from 1 to 12.

##### Examples

```php
$worksheet->setCellValue('A1', 'Date String');
$worksheet->setCellValue('A2', '31-Dec-2008');
$worksheet->setCellValue('A3', '14-Feb-2008');

$worksheet->setCellValue('B2', '=MONTH(A2)');
$worksheet->setCellValue('B3', '=MONTH(A3)');

$retVal = $worksheet->getCell('B2')->getCalculatedValue();
// $retVal = 12

$retVal = $worksheet->getCell('B3')->getCalculatedValue();
// $retVal = 2
```

```php
$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'MONTHOFYEAR'),
    array('14-July-2008')
);
// $retVal = 7
```

#### Notes

Note that the PHPExcel function is PHPExcel_Calculation_Functions::MONTHOFYEAR() when the method is called statically.

#### NETWORKDAYS

The NETWORKDAYS function returns the number of whole working days between a *start date* and an *end date*. Working days exclude weekends and any dates identified in *holidays*. Use NETWORKDAYS to calculate employee benefits that accrue based on the number of days worked during a specific term.

##### Syntax

```
NETWORKDAYS(startDate, endDate [, holidays])
```

##### Parameters

**startDate** Start Date of the period.

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

**endDate** End Date of the period.

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

**holidays** Optional array of Holiday dates.

An optional range of one or more dates to exclude from the working calendar, such as state and federal holidays and floating holidays.

The list can be either a range of cells that contains the dates or an array constant of Excel date values, PHP date timestamps, PHP date objects, or dates represented as strings.

##### Return Value

**integer** Number of working days.

The number of working days between startDate and endDate.

##### Examples

```php
```

```php
```

##### Notes

There are no additional notes on this function

#### NOW

The NOW function returns the current date and time.

##### Syntax

```
NOW()
```

##### Parameters

There are now parameters for the NOW() function.

##### Return Value

**mixed** A date/time stamp that corresponds to the current date and time.

This could be a PHP timestamp value (integer), a PHP date/time object, or an Excel timestamp value (real), depending on the value of PHPExcel_Calculation_Functions::getReturnDateType().

##### Examples

```php
```

```php
```

##### Notes

Note that the PHPExcel function is PHPExcel_Calculation_Functions::DATETIMENOW() when the method is called statically.

#### SECOND

The SECOND function returns the seconds of a time value. The second is given as an integer, ranging from 0 to 59.

##### Syntax

```
SECOND(datetime)
```

##### Parameters

**datetime** Time.

An Excel date/time value, PHP date timestamp, PHP date object, or a date/time represented as a string.

##### Return Value

**integer** An integer value that reflects the seconds within the minute.

This is an integer ranging from 0 to 59.

##### Examples

```php
$worksheet->setCellValue('A1', 'Time String')
    ->setCellValue('A2', '31-Dec-2008 17:30:20')
    ->setCellValue('A3', '14-Feb-2008 4:20 AM')
    ->setCellValue('A4', '14-Feb-2008 4:45:59 PM');

$worksheet->setCellValue('B2', '=SECOND(A2)')
    ->setCellValue('B3', '=SECOND(A3)');
    ->setCellValue('B4', '=SECOND(A4)');

$retVal = $worksheet->getCell('B2')->getCalculatedValue();
// $retVal = 20

$retVal = $worksheet->getCell('B3')->getCalculatedValue();
// $retVal = 0

$retVal = $worksheet->getCell('B4')->getCalculatedValue();
// $retVal = 59
```

```php
$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'SECONDOFMINUTE'),
    array('09:30:17')
);
// $retVal = 17
```

##### Notes

Note that the PHPExcel function is PHPExcel_Calculation_Functions::SECONDOFMINUTE() when the method is called statically.

#### TIME

Not yet documented.

#### TIMEVALUE

Not yet documented.

#### TODAY

Not yet documented.

#### WEEKDAY

The WEEKDAY function returns the day of the week for a given date. The day is given as an integer ranging from 1 to 7, although this can be modified to return a value between 0 and 6.

##### Syntax

```
WEEKDAY(datetime [, method])
```

##### Parameters

**datetime** Date.

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

**method** An integer flag (values 0, 1 or 2)

This is a flag that determines which method to use in the calculation, based on the values listed below:

    method | Description
    :-----:|------------------------------------------
    0      | Returns 1 (Sunday) through 7 (Saturday).
    1      | Returns 1 (Monday) through 7 (Sunday).
    2      | Returns 0 (Monday) through 6 (Sunday).

The method value defaults to 1.

##### Return Value

**integer** An integer value that reflects the day of the week.

This is an integer ranging from 1 to 7, or 0 to 6, depending on the value of method.

##### Examples

```php
$worksheet->setCellValue('A1', 'Date String')
    ->setCellValue('A2', '31-Dec-2008')
    ->setCellValue('A3', '14-Feb-2008');

$worksheet->setCellValue('B2', '=WEEKDAY(A2)')
    ->setCellValue('B3', '=WEEKDAY(A3,0)')
    ->setCellValue('B4', '=WEEKDAY(A3,2)');

$retVal = $worksheet->getCell('B2')->getCalculatedValue();
// $retVal = 12

$retVal = $worksheet->getCell('B3')->getCalculatedValue();
// $retVal = 2

$retVal = $worksheet->getCell('B4')->getCalculatedValue();
// $retVal = 2
```

```php
$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'DAYOFWEEK'),
    array('14-July-2008')
);
// $retVal = 7
```

##### Notes

Note that the PHPExcel function is PHPExcel_Calculation_Functions::DAYOFWEEK() when the method is called statically.

#### WEEKNUM

Not yet documented.

#### WORKDAY

Not yet documented.

#### YEAR

The YEAR function returns the year of a date.

##### Syntax

```
YEAR(datetime)
```

##### Parameters

**datetime** Date.

An Excel date value, PHP date timestamp, PHP date object, or a date represented as a string.

##### Return Value

**integer** An integer value that reflects the month of the year.

This is an integer year value.

##### Examples

```php
$worksheet->setCellValue('A1', 'Date String')
    ->setCellValue('A2', '17-Jul-1982')
    ->setCellValue('A3', '16-Apr-2009');

$worksheet->setCellValue('B2', '=YEAR(A2)')
    ->setCellValue('B3', '=YEAR(A3)');

$retVal = $worksheet->getCell('B2')->getCalculatedValue();
// $retVal = 1982

$retVal = $worksheet->getCell('B3')->getCalculatedValue();
// $retVal = 2009
```

```php
$retVal = call_user_func_array(
    array('PHPExcel_Calculation_Functions', 'YEAR'),
    array('14-July-2001')
);
// $retVal = 2001
```

##### Notes

There are no additional notes on this function

### YEARFRAC

Not yet documented.

