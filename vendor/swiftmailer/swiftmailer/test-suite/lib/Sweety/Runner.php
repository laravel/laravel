<?php

require_once 'Sweety/TestLocator.php';
require_once 'Sweety/Reporter.php';

/**
 * Provides the interface for a remote test runner.
 * @author Chris Corbyn
 * @package Sweety
 */
interface Sweety_Runner
{
  
  /** Format for reporting in text mode */
  const REPORT_TEXT = 'text';
  
  /** Format for reporting in XML mode */
  const REPORT_XML = 'xml';
  
  /** Format for reporting in HTML mode */
  const REPORT_HTML = 'html';
  
  /**
   * Provide a regular expression to filter away some classes.
   * @param string $ignoredClassRegex
   */
  public function setIgnoredClassRegex($ignoredClassRegex);
  
  /**
   * Set the reporter used for showing results/progress.
   * @param Sweety_Reporter $reporter
   */
  public function setReporter(Sweety_Reporter $reporter);
  
  /**
   * Register a new test locator instance.
   * @param Sweety_TestLocator $locator
   */
  public function registerTestLocator(Sweety_TestLocator $locator);
  
  /**
   * Run all tests in the provided directories.
   * @param string[] $directories
   * @return int
   */
  public function runAllTests($dirs = array());
  
  /**
   * Run a single test case in isolation using the provided report format.
   * @param string $testCase name
   * @param string Report format
   * @return int
   */
  public function runTestCase($testName, $format = self::REPORT_TEXT);
  
}
