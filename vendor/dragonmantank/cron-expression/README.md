PHP Cron Expression Parser
==========================

[![Latest Stable Version](https://poser.pugx.org/dragonmantank/cron-expression/v)](https://packagist.org/packages/dragonmantank/cron-expression) [![Total Downloads](https://poser.pugx.org/dragonmantank/cron-expression/downloads)](https://packagist.org/packages/dragonmantank/cron-expression) [![Tests](https://github.com/dragonmantank/cron-expression/actions/workflows/tests.yml/badge.svg)](https://github.com/dragonmantank/cron-expression/actions/workflows/tests.yml) [![StyleCI](https://github.styleci.io/repos/103715337/shield?branch=master)](https://github.styleci.io/repos/103715337)

The PHP cron expression parser can parse a CRON expression, determine if it is
due to run, calculate the next run date of the expression, and calculate the previous
run date of the expression.  You can calculate dates far into the future or past by
skipping **n** number of matching dates.

The parser can handle increments of ranges (e.g. */12, 2-59/3), intervals (e.g. 0-9),
lists (e.g. 1,2,3), **W** to find the nearest weekday for a given day of the month, **L** to
find the last day of the month, **L** to find the last given weekday of a month, and hash
(#) to find the nth weekday of a given month.

More information about this fork can be found in the blog post [here](http://ctankersley.com/2017/10/12/cron-expression-update/). tl;dr - v2.0.0 is a major breaking change, and @dragonmantank can better take care of the project in a separate fork.

Installing
==========

Add the dependency to your project:

```bash
composer require dragonmantank/cron-expression
```

Usage
=====
```php
<?php

require_once '/vendor/autoload.php';

// Works with predefined scheduling definitions
$cron = new Cron\CronExpression('@daily');
$cron->isDue();
echo $cron->getNextRunDate()->format('Y-m-d H:i:s');
echo $cron->getPreviousRunDate()->format('Y-m-d H:i:s');

// Works with complex expressions
$cron = new Cron\CronExpression('3-59/15 6-12 */15 1 2-5');
echo $cron->getNextRunDate()->format('Y-m-d H:i:s');

// Calculate a run date two iterations into the future
$cron = new Cron\CronExpression('@daily');
echo $cron->getNextRunDate(null, 2)->format('Y-m-d H:i:s');

// Calculate a run date relative to a specific time
$cron = new Cron\CronExpression('@monthly');
echo $cron->getNextRunDate('2010-01-12 00:00:00')->format('Y-m-d H:i:s');
```

CRON Expressions
================

A CRON expression is a string representing the schedule for a particular command to execute.  The parts of a CRON schedule are as follows:

```
*   *   *   *   *
-   -   -   -   -
|   |   |   |   |
|   |   |   |   |
|   |   |   |   +----- day of week (0-7) (Sunday = 0 or 7) (or SUN-SAT)
|   |   |   +--------- month (1-12) (or JAN-DEC)
|   |   +------------- day of month (1-31)
|   +----------------- hour (0-23)
+--------------------- minute (0-59)
```

Each part of expression can also use wildcard, lists, ranges and steps:

- wildcard - match always
	- `* * * * *` - At every minute.
	- day of week and day of month also support `?`, an alias to `*`
- lists - match list of values, ranges and steps
	- e.g. `15,30 * * * *` - At minute 15 and 30.
- ranges - match values in range
	- e.g. `1-9 * * * *` - At every minute from 1 through 9.
- steps - match every nth value in range
	- e.g. `*/5 * * * *` - At every 5th minute.
	- e.g. `0-30/5 * * * *` - At every 5th minute from 0 through 30.
- combinations
	- e.g. `0-14,30-44 * * * *` - At every minute from 0 through 14 and every minute from 30 through 44.

You can also use macro instead of an expression:

- `@yearly`, `@annually` - At 00:00 on 1st of January. (same as `0 0 1 1 *`)
- `@monthly` - At 00:00 on day-of-month 1. (same as `0 0 1 * *`)
- `@weekly` - At 00:00 on Sunday. (same as `0 0 * * 0`)
- `@daily`, `@midnight` - At 00:00. (same as `0 0 * * *`)
- `@hourly` - At minute 0. (same as `0 * * * *`)

Day of month extra features:

- nearest weekday - weekday (Monday-Friday) nearest to the given day
	- e.g. `* * 15W * *` - At every minute on a weekday nearest to the 15th.
	- If you were to specify `15W` as the value, the meaning is: "the nearest weekday to the 15th of the month"
	  So if the 15th is a Saturday, the trigger will fire on Friday the 14th.
	  If the 15th is a Sunday, the trigger will fire on Monday the 16th.
	  If the 15th is a Tuesday, then it will fire on Tuesday the 15th.
	- However, if you specify `1W` as the value for day-of-month,
	  and the 1st is a Saturday, the trigger will fire on Monday the 3rd,
	  as it will not 'jump' over the boundary of a month's days.
- last day of the month
	- e.g. `* * L * *` - At every minute on a last day-of-month.
- last weekday of the month
	- e.g. `* * LW * *` - At every minute on a last weekday.

Day of week extra features:

- nth day
	- e.g. `* * * * 7#4` - At every minute on 4th Sunday.
	- 1-5
	- Every day of week repeats 4-5 times a month. To target the last one, use "last day" feature instead.
- last day
	- e.g. `* * * * 7L` - At every minute on the last Sunday.

Requirements
============

- PHP 7.2+
- PHPUnit is required to run the unit tests
- Composer is required to run the unit tests

Projects that Use cron-expression
=================================
* Part of the [Laravel Framework](https://github.com/laravel/framework/)
* Available as a [Symfony Bundle - setono/cron-expression-bundle](https://github.com/Setono/CronExpressionBundle)
* Framework agnostic, PHP-based job scheduler - [Crunz](https://github.com/crunzphp/crunz)
* Framework agnostic job scheduler - with locks, parallelism, per-second scheduling and more - [orisai/scheduler](https://github.com/orisai/scheduler)
* Explain expression in English (and other languages) with [orisai/cron-expression-explainer](https://github.com/orisai/cron-expression-explainer)
