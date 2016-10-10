# Calculation Engine - Formula Function Reference

## Function Reference

### Handling Date and Time Values

#### Excel functions that return a Date and Time value

Any of the Date and Time functions that return a date value in Excel can return either an Excel timestamp or a PHP timestamp or date object.

It is possible for scripts to change the data type used for returning date values by calling the PHPExcel_Calculation_Functions::setReturnDateType() method:

```php
PHPExcel_Calculation_Functions::setReturnDateType($returnDateType);
```

where the following constants can be used for $returnDateType

 - PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC
 - PHPExcel_Calculation_Functions::RETURNDATE_PHP_OBJECT
 - PHPExcel_Calculation_Functions::RETURNDATE_EXCEL

The method will return a Boolean True on success, False on failure (e.g. if an invalid value is passed in for the return date type).

The PHPExcel_Calculation_Functions::getReturnDateType() method can be used to determine the current value of this setting:

```php
$returnDateType = PHPExcel_Calculation_Functions::getReturnDateType();
```

The default is RETURNDATE_PHP_NUMERIC.

##### PHP Timestamps

If RETURNDATE_PHP_NUMERIC is set for the Return Date Type, then any date value returned to the calling script by any access to the Date and Time functions in Excel will be an integer value that represents the number of seconds from the PHP/Unix base date. The PHP/Unix base date (0) is 00:00 UST on 1st January 1970. This value can be positive or negative: so a value of -3600 would be 23:00 hrs on 31st December 1969; while a value of +3600 would be 01:00 hrs on 1st January 1970. This gives PHP a date range of between 14th December 1901 and 19th January 2038.

##### PHP DateTime Objects

If the Return Date Type is set for RETURNDATE_PHP_NUMERIC, then any date value returned to the calling script by any access to the Date and Time functions in Excel will be a PHP date/time object.

##### Excel Timestamps

If RETURNDATE_EXCEL is set for the Return Date Type, then the returned date value by any access to the Date and Time functions in Excel will be a floating point value that represents a number of days from the Excel base date. The Excel base date is determined by which calendar Excel uses: the Windows 1900 or the Mac 1904 calendar. 1st January 1900 is the base date for the Windows 1900 calendar while 1st January 1904 is the base date for the Mac 1904 calendar.

It is possible for scripts to change the calendar used for calculating Excel date values by calling the PHPExcel_Shared_Date::setExcelCalendar() method:

```php
PHPExcel_Shared_Date::setExcelCalendar($baseDate);
```

where the following constants can be used for $baseDate

 - PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900
 - PHPExcel_Shared_Date::CALENDAR_MAC_1904

The method will return a Boolean True on success, False on failure (e.g. if an invalid value is passed in).

The PHPExcel_Shared_Date::getExcelCalendar() method can be used to determine the current value of this setting:

```php
$baseDate = PHPExcel_Shared_Date::getExcelCalendar();
```
The default is CALENDAR_WINDOWS_1900.

##### Functions that return a Date/Time Value

 - DATE
 - DATEVALUE
 - EDATE
 - EOMONTH
 - NOW
 - TIME
 - TIMEVALUE
 - TODAY

#### Excel functions that accept Date and Time values as parameters

Date values passed in as parameters to a function can be an Excel timestamp or a PHP timestamp; or date object; or a string containing a date value (e.g. '1-Jan-2009'). PHPExcel will attempt to identify their type based on the PHP datatype:

An integer numeric value will be treated as a PHP/Unix timestamp. A real (floating point) numeric value will be treated as an Excel date/timestamp. Any PHP DateTime object will be treated as a DateTime object. Any string value (even one containing straight numeric data) will be converted to a date/time object for validation as a date value based on the server locale settings, so passing through an ambiguous value of '07/08/2008' will be treated as 7th August 2008 if your server settings are UK, but as 8th July 2008 if your server settings are US. However, if you pass through a value such as '31/12/2008' that would be considered an error by a US-based server, but which is not ambiguous, then PHPExcel will attempt to correct this to 31st December 2008. If the content of the string doesn’t match any of the formats recognised by the php date/time object implementation of strtotime() (which can handle a wider range of formats than the normal strtotime() function), then the function will return a '#VALUE' error. However, Excel recommends that you should always use date/timestamps for your date functions, and the recommendation for PHPExcel is the same: avoid strings because the result is not predictable.

The same principle applies when data is being written to Excel. Cells containing date actual values (rather than Excel functions that return a date value) are always written as Excel dates, converting where necessary. If a cell formatted as a date contains an integer or date/time object value, then it is converted to an Excel value for writing: if a cell formatted as a date contains a real value, then no conversion is required. Note that string values are written as strings rather than converted to Excel date timestamp values.

##### Functions that expect a Date/Time Value

 - DATEDIF
 - DAY
 - DAYS360
 - EDATE
 - EOMONTH
 - HOUR
 - MINUTE
 - MONTH
 - NETWORKDAYS
 - SECOND
 - WEEKDAY
 - WEEKNUM
 - WORKDAY
 - YEAR
 - YEARFRAC

#### Helper Methods

In addition to the setExcelCalendar() and getExcelCalendar() methods, a number of other methods are available in the PHPExcel_Shared_Date class that can help when working with dates:

##### PHPExcel_Shared_Date::ExcelToPHP($excelDate)

Converts a date/time from an Excel date timestamp to return a PHP serialized date/timestamp.

Note that this method does not trap for Excel dates that fall outside of the valid range for a PHP date timestamp.

##### PHPExcel_Shared_Date::ExcelToPHPObject($excelDate)

Converts a date from an Excel date/timestamp to return a PHP DateTime object.

##### PHPExcel_Shared_Date::PHPToExcel($PHPDate)

Converts a PHP serialized date/timestamp or a PHP DateTime object to return an Excel date timestamp.

##### PHPExcel_Shared_Date::FormattedPHPToExcel($year, $month, $day, $hours=0, $minutes=0, $seconds=0)

Takes year, month and day values (and optional hour, minute and second values) and returns an Excel date timestamp value.

