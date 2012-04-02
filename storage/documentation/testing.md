# Unit Testing

## Contents

- [The Basics](#the-basics)
- [Creating Test Classes](#creating-test-classes)
- [Running Tests](#running-tests)
- [Calling Controllers From Tests](#calling-controllers-from-tests)

<a name="the-basics"></a>
## The Basics

Unit Testing allows you to test your code and verify that it is working correctly. In fact, many advocate that you should even write your tests before you write your code! Laravel provides beautiful integration with the popular [PHPUnit](http://www.phpunit.de/manual/current/en/) testing library, making it easy to get started writing your tests. In fact, the Laravel framework itself has hundreds of unit tests!

<a name="creating-test-classes"></a>
## Creating Test Classes

All of your application's tests live in the **application/tests** directory. In this directory, you will find a basic **example.test.php** file. Pop it open and look at the class it contains:

	<?php

	class TestExample extends PHPUnit_Framework_TestCase {

		/**
		 * Test that a given condition is met.
		 *
		 * @return void
		 */
		public function testSomethingIsTrue()
		{
			$this->assertTrue(true);
		}

	}

Take special note of the **.test.php** file suffix. This tells Laravel that it should include this class as a test case when running your test. Any files in the test directory that are not named with this suffix will not be considered a test case.

If you are writing tests for a bundle, just place them in a **tests** directory within the bundle. Laravel will take care of the rest!

For more information regarding creating test cases, check out the [PHPUnit documentation](http://www.phpunit.de/manual/current/en/).

<a name="running-tests"></a>
## Running Tests

To run your tests, you can use Laravel's Artisan command-line utility:

#### Running the application's tests via the Artisan CLI:

	php artisan test

#### Running the unit tests for a bundle:

	php artisan test bundle-name

<a name="#calling-controllers-from-tests"></a>
## Calling Controllers From Tests

Here's an example of how you can call your controllers from your tests:

#### Calling a controller from a test:

	$response = Controller::call('home@index', $parameters);

#### Resolving an instance of a controller from a test:

	$controller = Controller::resolve('application', 'home@index');

> **Note:** The controller's action filters will still run when using Controller::call to execute controller actions.