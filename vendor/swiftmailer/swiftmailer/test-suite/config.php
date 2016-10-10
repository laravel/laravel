<?php

//Error reporting settings
error_reporting(E_ALL | E_STRICT); ini_set('display_errors', true);

if (defined('E_DEPRECATED'))
{
    if (version_compare(phpversion(), '5.5', '>=')) {
        error_reporting(~E_DEPRECATED);
    } else {
        error_reporting(error_reporting() | E_DEPRECATED);
    }
}

//E_STRICT compliance -- If you change this timezone some tests may fail -
// This only affects the tests, you need to ensure PHP is correctly set up in
// your own code
date_default_timezone_set('Australia/ACT');

//Time limit to process all tests
set_time_limit(30);

//The path to the PHP command line executable (auto-detected if none set)
define('SWEETY_PHP_EXE', '');
//The path to this file
define('SWEETY_HOME', dirname(__FILE__));
//The path to the libs being tested
define('SWEETY_INCLUDE_PATH',
  SWEETY_HOME . '/../lib/classes' . PATH_SEPARATOR .
  SWEETY_HOME . '/../lib' . PATH_SEPARATOR .
  SWEETY_HOME . '/../tests/helpers'
  );
//The path to the main test suite
define('SWEETY_LIB_PATH', SWEETY_HOME . '/lib');
//The path to simpletest
define('SWEETY_SIMPLETEST_PATH', SWEETY_LIB_PATH . '/simpletest');
//The path to any testing directories
define('SWEETY_TEST_PATH',
  SWEETY_HOME . '/../tests/unit' .
  PATH_SEPARATOR . SWEETY_HOME . '/../tests/acceptance' .
  PATH_SEPARATOR . SWEETY_HOME . '/../tests/smoke' .
  PATH_SEPARATOR . SWEETY_HOME . '/../tests/bug'
  );
//Test locator strategies, separated by commas
define('SWEETY_TEST_LOCATOR', 'Sweety_TestLocator_PearStyleLocator');
//A pattern used for filtering out certain class names expected to be tests
define('SWEETY_IGNORED_CLASSES', '/(^|_)Abstract/');
//The name which appears at the top of the test suite
define('SWEETY_SUITE_NAME', 'Swift Mailer 4 Test Suite');
//The path to the template which renders the view
define('SWEETY_UI_TEMPLATE', SWEETY_HOME . '/templates/sweety/suite-ui.tpl.php');

//Most likely you won't want to modify the include_path
set_include_path(
  dirname(__FILE__) . '/../lib' . PATH_SEPARATOR .
  SWEETY_LIB_PATH . PATH_SEPARATOR .
  SWEETY_INCLUDE_PATH . PATH_SEPARATOR .
  SWEETY_TEST_PATH
);

//Load in any dependencies
require_once 'Sweety/TestLocator/PearStyleLocator.php';
require_once 'swift_required.php';

//Force init to be required
require_once 'swift_init.php';

//Load in some swift specific testig config
require_once SWEETY_HOME . '/../tests/acceptance.conf.php';
require_once SWEETY_HOME . '/../tests/smoke.conf.php';
require_once SWEETY_HOME . '/lib/yaymock/yay_mock.php';
require_once SWEETY_HOME . '/lib/yaymock/yay_convenience.php';
