<?php

require_once 'Sweety/Runner.php';
require_once 'Sweety/TestLocator.php';
require_once 'Sweety/Reporter.php';

/**
 * Base functionality for the Sweety_Runner.
 * @package Sweety
 * @author Chris Corbyn
 */
abstract class Sweety_Runner_AbstractTestRunner implements Sweety_Runner
{
 
 /**
  * The reporter used for showing progress.
  * @var Sweety_Reporter
  * @access private
  */
 private $_reporter;
 
  /**
   * TestLocator strategies.
   * @var Sweety_TestLocator[]
   * @access private
   */
  private $_testLocators = array();
   
  /**
   * Regular expression for classes which should be ignored.
   * @var string
   * @access private
   */
  private $_ignoredClassRegex = '/^$/D';
  
  /**
   * Set the reporter used for showing results.
   * @param Sweety_Reporter $reporter
   */
  public function setReporter(Sweety_Reporter $reporter)
  {
    $this->_reporter = $reporter;
  }
  
  /**
   * Get the reporter used for showing results.
   * @return Sweety_Reporter
   */
  public function getReporter()
  {
    return $this->_reporter;
  }
  
  /**
   * Register a test locator instance.
   * @param Sweety_TestLocator $locator
   */
  public function registerTestLocator(Sweety_TestLocator $locator)
  {
    $this->_testLocators[] = $locator;
  }
  
  /**
   * Set the regular expression used to filter out certain class names.
   * @param string $ignoredClassRegex
   */
  public function setIgnoredClassRegex($ignoredClassRegex)
  {
    $this->_ignoredClassRegex = $ignoredClassRegex;
  }
  
  /**
   * Get the filtering regular expression for ignoring certain classes.
   * @return string
   */
  public function getIgnoredClassRegex()
  {
    return $this->_ignoredClassRegex;
  }
   
  /**
   * Run a single test case with the given name, using the provided output format.
   * @param string $testName
   * @param string $format (xml, text or html)
   * @return int
   */
  public function runTestCase($testName, $format = Sweety_Runner::REPORT_TEXT)
  {
    foreach ($this->_testLocators as $locator)
    {
      if ($locator->includeTest($testName))
      {
        break;
      }
    }
    
    $testClass = new ReflectionClass($testName);
    if ($testClass->getConstructor())
    {
      //We don't want test output to be cached
      if (!SimpleReporter::inCli())
      {
        header("Cache-Control: no-cache, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
      }
      
      switch ($format)
      {
        case Sweety_Runner::REPORT_HTML:
          $reporter = new HtmlReporter();
          break;
        case Sweety_Runner::REPORT_XML:
          if (!SimpleReporter::inCli())
          {
            header("Content-Type: text/xml"); //Sigh! SimpleTest (skip() issues).
          }
          $reporter = new XmlReporter();
          break;
        case Sweety_Runner::REPORT_TEXT:
        default:
          $reporter = new TextReporter();
          break;
      }
      $test = $testClass->newInstance();
      return $test->run($reporter) ? 0 : 1;
    }
    
    return 1;
  }
  
  /**
   * Use strategies to find tests which are runnable.
   * @param string[] $dirs
   * @return string[]
   */
  protected function findTests($dirs = array())
  {
    $tests = array();
    foreach ($this->_testLocators as $locator)
    {
      $tests += $locator->getTests($dirs);
    }
    return $tests;
  }
  
  /**
   * Parse an XML response from a test case an report to the reporter.
   * @param string $xml
   * @param string $testCase
   */
  protected function parseXml($xml, $testCase)
  {
    $reporter = $this->_reporter->getReporterFor($testCase);
    if (!$reporter->isStarted())
    {
      $reporter->start();
    }
    
    $xml = str_replace("\0", '?', trim($xml));
    $xml = preg_replace_callback('/[^\x01-\x7F]/', array($this, 'preg_print_escape'), $xml); //Do something better?
    if (!empty($xml))
    {
      $document = @simplexml_load_string($xml);
      if ($document)
      {
        $this->_parseDocument($document, $testCase, $reporter);
        $reporter->finish();
        return;
      }
    }
    
    $reporter->reportException(
      'Invalid XML response: ' .
      trim(strip_tags(
        preg_replace('/^\s*<\?xml.+<\/(?:name|pass|fail|exception)>/s', '', $xml)
        )),
      $testCase
      );
  }
  
  private function preg_print_escape( $matches )
  {
      return sprintf("&#%d;", ord($matches[0]));
  }
  
  /**
   * Parse formatted test output.
   * @param SimpleXMLElement The node containing the output
   * @param string $path to this test method
   * @access private
   */
  private function _parseFormatted(SimpleXMLElement $formatted, $path = '',
    Sweety_Reporter $reporter)
  {
    $reporter->reportOutput((string)$formatted, $path);
  }
  
  /**
   * Parse test output.
   * @param SimpleXMLElement The node containing the output
   * @param string $path to this test method
   * @access private
   */
  private function _parseMessage(SimpleXMLElement $message, $path = '',
    Sweety_Reporter $reporter)
  {
    $reporter->reportOutput((string)$message, $path);
  }
  
  /**
   * Parse a test failure.
   * @param SimpleXMLElement The node containing the fail
   * @param string $path to this test method
   * @access private
   */
  private function _parseFailure(SimpleXMLElement $failure, $path = '',
    Sweety_Reporter $reporter)
  {
    $reporter->reportFail((string)$failure, $path);
  }
  
  /**
   * Parse an exception.
   * @param SimpleXMLElement The node containing the exception
   * @param string $path to this test method
   * @access private
   */
  private function _parseException(SimpleXMLElement $exception, $path = '',
    Sweety_Reporter $reporter)
  {
    $reporter->reportException((string)$exception, $path);
  }
  
  /**
   * Parse a pass.
   * @param SimpleXMLElement The node containing this pass.
   * @param string $path to this test method
   * @access private
   */
  private function _parsePass(SimpleXMLElement $pass, $path = '',
    Sweety_Reporter $reporter)
  {
    $reporter->reportPass((string)$pass, $path);
  }
  
  /**
   * Parse a single test case.
   * @param SimpleXMLElement The node containing the test case
   * @param string $path to this test case
   * @access private
   */
  private function _parseTestCase(SimpleXMLElement $testCase, $path = '',
    Sweety_Reporter $reporter)
  { 
    foreach ($testCase->xpath('./test') as $testMethod)
    {
      $testMethodName = (string) $this->_firstNodeValue($testMethod->xpath('./name'));
      
      foreach ($testMethod->xpath('./formatted') as $formatted)
      {
        $this->_parseFormatted(
          $formatted, $path . ' -> ' . $testMethodName, $reporter);
      }
      
      foreach ($testMethod->xpath('./message') as $message)
      {
        $this->_parseMessage(
          $message, $path . ' -> ' . $testMethodName, $reporter);
      }
      
      foreach ($testMethod->xpath('./fail') as $failure)
      {
        $this->_parseFailure(
          $failure, $path . ' -> ' . $testMethodName, $reporter);
      }
      
      foreach ($testMethod->xpath('./exception') as $exception)
      {
        $this->_parseException(
          $exception, $path . ' -> ' . $testMethodName, $reporter);
      }
      
      foreach ($testMethod->xpath('./pass') as $pass)
      {
        $this->_parsePass($pass, $path . ' -> ' . $testMethodName, $reporter);
      }
      
      $stdout = trim((string) $testMethod);
      if ($stdout)
      {
        $reporter->reportOutput($stdout, $path . ' -> ' . $testMethodName);
      }
    }
  }
  
  /**
   * Parse the results of all tests.
   * @param SimpleXMLElement The node containing the tests
   * @param string $path to the tests
   * @access private
   */
  private function _parseResults(SimpleXMLElement $document, $path = '',
    Sweety_Reporter $reporter)
  {
    $groups = $document->xpath('./group');
    if (!empty($groups))
    { 
      foreach ($groups as $group)
      { 
        $groupName = (string) $this->_firstNodeValue($group->xpath('./name'));
        $this->_parseResults($group, $path . ' -> ' . $groupName, $reporter);
      }
    }
    else
    {
      foreach ($document->xpath('./case') as $testCase)
      {
        $this->_parseTestCase($testCase, $path, $reporter);
      }
    }
  }
  
  /**
   * Parse the entire SimpleTest XML document from a test case.
   * @param SimpleXMLElement $document to parse
   * @param string $path to the test
   * @access private
   */
  private function _parseDocument(SimpleXMLElement $document, $path = '',
    Sweety_Reporter $reporter)
  {
    if ($everything = $this->_firstNodeValue($document->xpath('/run')))
    {
      $this->_parseResults($everything, $path, $reporter);
    }
    elseif ($skip = $this->_firstNodeValue($document->xpath('/skip')))
    {
      $reporter->reportSkip((string) $skip, $path);
    }
  }
  
  protected function _sort($a, $b)
  {
    $apkg = preg_replace('/_[^_]+$/D', '', $a);
    $bpkg = preg_replace('/_[^_]+$/D', '', $b);
    if ($apkg == $bpkg)
    {
      if ($a == $b)
      {
        return 0;
      }
      else
      {
        return ($a > $b) ? 1 : -1;
      }
    }
    else
    {
      return ($apkg > $bpkg) ? 1 : -1;
    }
  }
  
  private function _firstNodeValue($nodeSet)
  {
    $first = array_shift($nodeSet);
    return $first;
  }
  
}
