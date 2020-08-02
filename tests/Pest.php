<?php

/*
|--------------------------------------------------------------------------
| Bind Traits And Classes
|--------------------------------------------------------------------------
|
| The `uses` function lets you recursively bind traits and classes to 
| your test files. By default, test files are already bound to the
| PHPUnit test case class. Of course, feel free to modify this.
|
*/

uses(Tests\TestCase::class)->in('Feature');
