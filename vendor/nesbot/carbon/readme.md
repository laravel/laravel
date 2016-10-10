# Carbon

[![Latest Stable Version](https://poser.pugx.org/nesbot/carbon/v/stable.png)](https://packagist.org/packages/nesbot/carbon) [![Total Downloads](https://poser.pugx.org/nesbot/carbon/downloads.png)](https://packagist.org/packages/nesbot/carbon) [![Build Status](https://secure.travis-ci.org/briannesbitt/Carbon.png)](http://travis-ci.org/briannesbitt/Carbon)

A simple PHP API extension for DateTime. [http://carbon.nesbot.com](http://carbon.nesbot.com)

```php
printf("Right now is %s", Carbon::now()->toDateTimeString());
printf("Right now in Vancouver is %s", Carbon::now('America/Vancouver'));  //implicit __toString()
$tomorrow = Carbon::now()->addDay();
$lastWeek = Carbon::now()->subWeek();
$nextSummerOlympics = Carbon::createFromDate(2012)->addYears(4);

$officialDate = Carbon::now()->toRfc2822String();

$howOldAmI = Carbon::createFromDate(1975, 5, 21)->age;

$noonTodayLondonTime = Carbon::createFromTime(12, 0, 0, 'Europe/London');

$worldWillEnd = Carbon::createFromDate(2012, 12, 21, 'GMT');

// Don't really want to die so mock now
Carbon::setTestNow(Carbon::createFromDate(2000, 1, 1));

// comparisons are always done in UTC
if (Carbon::now()->gte($worldWillEnd)) {
   die();
}

// Phew! Return to normal behaviour
Carbon::setTestNow();

if (Carbon::now()->isWeekend()) {
   echo 'Party!';
}
echo Carbon::now()->subMinutes(2)->diffForHumans(); // '2 minutes ago'

// ... but also does 'from now', 'after' and 'before'
// rolling up to seconds, minutes, hours, days, months, years

$daysSinceEpoch = Carbon::createFromTimeStamp(0)->diffInDays();
```

## Installation

### With Composer

```
$ composer require nesbot/carbon
```

```json
{
    "require": {
        "nesbot/carbon": "~1.14"
    }
}
```

```php
<?php
require 'vendor/autoload.php';

use Carbon\Carbon;

printf("Now: %s", Carbon::now());
```

<a name="install-nocomposer"/>
### Without Composer

Why are you not using [composer](http://getcomposer.org/)? Download [Carbon.php](https://github.com/briannesbitt/Carbon/blob/master/src/Carbon/Carbon.php) from the repo and save the file into your project path somewhere.

```php
<?php
require 'path/to/Carbon.php';

use Carbon\Carbon;

printf("Now: %s", Carbon::now());
```
