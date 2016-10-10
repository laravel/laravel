<?php

require_once dirname(__FILE__) . '/config.php';

require_once SWEETY_SIMPLETEST_PATH . '/unit_tester.php';
require_once SWEETY_SIMPLETEST_PATH . '/mock_objects.php';
require_once SWEETY_SIMPLETEST_PATH . '/reporter.php';
require_once SWEETY_SIMPLETEST_PATH . '/xml.php';

require_once 'Sweety/Runner.php';
require_once 'Sweety/Runner/HtmlRunner.php';
require_once 'Sweety/Reporter/HtmlReporter.php';

$runner = new Sweety_Runner_HtmlRunner(
  explode(PATH_SEPARATOR, SWEETY_TEST_PATH),
  SWEETY_UI_TEMPLATE,
  SWEETY_SUITE_NAME
);

$runner->setReporter(new Sweety_Reporter_HtmlReporter());

$runner->setIgnoredClassRegex(SWEETY_IGNORED_CLASSES);

$locators = preg_split('/\s*,\s*/', SWEETY_TEST_LOCATOR);
foreach ($locators as $locator)
{
  $runner->registerTestLocator(new $locator());
}

if (isset($_GET['test']))
{
  $testName = $_GET['test'];
  $format = isset($_GET['format']) ? $_GET['format'] : Sweety_Runner::REPORT_HTML;
  
  $runner->runTestCase($testName, $format);
}
else
{
  $runner->runAllTests();
}
