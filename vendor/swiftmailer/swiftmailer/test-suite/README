Sweety SimpleTest Suite
-----------------------

Sweety is a wrapper around SimpleTest's XML reporting capabilities which
makes unit tests easier to manage and friendlier to run.

Tests are run in a grouped fashion, but each individual test runs in its own
environment and own memory space either via forking new PHP processes, or by
making new HTTP requests.

Sweety works with any vanilla version of SimpleTest since the XmlReporter was
added.

Tests can be run on command line, in an AJAX equipped web browser*, or in a 
web browser with javascript turned off.

 * Sweety has been tested with success in the following browsers:

   - Mozilla Firefox 2.0
   - Safari 3-beta
   - Internet Explorer 7
   - Opera 9


Configuring Sweety:
--------------------

All Sweety configuration is contained inside the config.php file, defined as
constants for the mostpart.

Make sure you at least indicate a path to a directory containing SimpleTest,
and also change the SWEETY_INCLUDE_PATH and SWEETY_TEST_PATH to fit your needs.

Paths are provided using the directory separator for your OS.  Use the PHP
constant PATH_SEPARATOR if you need to run in different environments.

If you have test cases in directories /webdev/tests/unit and
/webdev/tests/integration your SWEETY_TEST_PATH should look like:

define('SWEETY_TEST_PATH', '/webdev/tests/unit' . PATH_SEPARATOR .
  '/webdev/tests/integration');
  
If you want to run Sweety on the command line you'll need to specify the path
to your PHP executable (typically /usr/bin/php).  Sweety needs to be able to
fork new processes using this executable.


What to do if your naming scheme doesn't use PEAR conventions:
--------------------------------------------------------------

By default Sweety looks for classes using PEAR naming conventions.  If you use
some other naming convention you need to tell Sweety how to find your test cases.

This is a two step process:

1)  Write a new Sweety_TestLocator -- don't worry, it's easy!

   Refer to the interface in lib/Sweety/TestLocator.php for guidance on what
   your TestLocator needs to include (just two methods for searching and including).
   
2) Add it to your config.php.

  Once you've written a new TestLocator which works for your naming scheme,
  change the config value SWEETY_TEST_LOCATOR to the name of your new class, then
  include the class file somewhere inside the config.php.  If you use multiple class
  naming conventions, list your TestLocators as a comma separated string.


Making tests appear in Sweety's interface:
-------------------------------------------

No really, you just edit the configuration and they'll show up if a TestLocator
can find them ;)


Running sweety on the command line:
-----------------------------------

Interacting with Sweety on the command line you'll get almost as much detail
as you do in a web browser, although the formatting obviously isn't so pretty!

All operations are handled by the file named run.php in the sweety installation
directory.

 -bash$  php run.php  #runs all tests
 -bash$  php run.php Name_Of_TestClass  #runs a single test case
 -bash$  php run.php Name_Of_TestClass  xml  #runs a single test case in XML

 

Runing Sweety with AJAX:
-------------------------

Open up an AJAX equipped web browsers (preferably supporting DOM 3 XPath, but at
least support basic DOM).  Navigate to the index.php file at the installation
directory of Sweety.  You'll see the screen is divided into two sections, left
and right.  On the left there's a list of test cases which you can click to run.
On the right you get all the verbose output from running the tests.

Clicking the "Run Tests" button will run all tests you can currently see in
the list.  As each test runs, a request is sent to the web server to get
SimpleTest to run your test case.  If the test passes the test case will turn
green, if it fails it will turn red.  Tests go yellow until a final conclusion
is drawn.

If you need to stop the tests at any time just click the button again (it
should say "Stop Tests" whilst the tests run).

Whilst the tests run, the large bar to the right of the screen will tally
up aggregated results and will eventually go either green or red indicating
a pass or failure.  Failed assertion messages will appear in the page just like
they do with the HtmlReporter of SimpleTest.

Clicking a single test case will run just that test in isolation.  If you want
(or need?) to run the test with SimpleTest's HtmlReporter just click the
HTML Icon (little world image) next to the test case.  Tests can also be run in
XML if needed.

Above the list of tests there's a filter box which can be directly typed into.
Typing in here narrows down the list of testcases to show only the ones which
match the search query.

Refreshing the page with your browser's refresh button will reset the test suite.

When you change your code, you DO NOT need to refesh your browser window.  Just
click the "Run Tests" button, or click the individual test to refresh the results.

If you add a new test case you will need to refresh your browser window however.


Running Sweety without JavaScript:
----------------------------------

If your web browser has JavaScript disabled you can still use the HTML version
of the test suite.  Open up the index.php file in your web browser.

You'll see the screen is divided into two sections, left
and right.  On the left there's a list of test cases with checkboxes next to them
which you can check to run.  On the right you get all the verbose output from
running the tests.

Clicking the "Run Tests" button will run all the currently selected test cases.
This could take a long time depending upon how many tests you have to run,
but once complete you'll see the page again where the tests you selected will
either be red or green indicating a pass or failure.  The bar at the right of
the screen will contain aggregate results for all the tests and will be either
red or green to indicate an overall pass or failure.

Assertion messages appear in the page just like with SimpleTest's HtmlReporter.

If you want to run just a single test case, click the "Run" icon at the right
of the test (little running man image).

You can run tests with SimpleTest's HtmlReporter by clicking the HTML icon
(little world image) next to the test case.  Tests can be run in XML if needed
too.

Enjoy!

