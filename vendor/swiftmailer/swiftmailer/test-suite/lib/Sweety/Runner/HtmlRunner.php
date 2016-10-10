<?php

require_once 'Sweety/Runner/AbstractTestRunner.php';

/**
 * Runs SimpleTest cases as a group through a JS enabled browser.
 * @package Sweety
 * @author Chris Corbyn
 */
class Sweety_Runner_HtmlRunner extends Sweety_Runner_AbstractTestRunner
{
  
  /**
   * The path to a valid template file.
   * @var string
   */
  private $_template;
  
  /**
   * The name of the test suite.
   * @var string
   */
  private $_name;
  
  /**
   * Creates a new HtmlRunner scanning the given directories.
   * @param string[] $dirs
   * @param string $template to load
   * @param string $name of the test suite
   */
  public function __construct(array $dirs, $template, $name)
  {
      $this->_dirs = $dirs;
      $this->_template = $template;
      $this->_name = $name;
  }
  
   /**
   * Runs all test cases found under the given directories.
   * @param string[] $directories to scan for test cases
   * @param string To be prepended to class names
   * @return int
   */
  public function runAllTests($dirs = array())
  {
    //We don't want test output to be cached
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
      
    if (empty($dirs))
    {
      $dirs = $this->_dirs;
    }
    
    $testCases = $this->findTests($dirs);
    foreach ($testCases as $k => $testCase)
    {
      if (preg_match($this->getIgnoredClassRegex(), $testCase))
      {
        unset($testCases[$k]);
      }
    }
    
    usort($testCases, array($this, '_sort'));
    
    $vars = array(
      //String
      'testCases' => $testCases,
      //String
      'suiteName' => $this->_name,
       // testCase => pass | fail | running
      'runTests' => array(),
      //Integer
      'caseCount' => 0,
      //Integer
      'runCount' => 0,
      //Integer
      'passCount' => 0,
      //Integer
      'failCount' => 0,
      //Integer
      'exceptionCount' => 0,
      // type => pass | fail | exception | output | internal, path => testCase, text => ...
      'messages' => array(),
      // pass | fail
      'result' => 'idle'
    );
    
    if (isset($_REQUEST['runtests']))
    {
      $reporter = $this->getReporter();
      $reporter->setTemplateVars($vars);
      
      if (!$reporter->isStarted())
      {
        $reporter->start();
      }
      
      $this->_runTestList((array)$_REQUEST['runtests'], $reporter);
      
      $reporter->finish();
    }
    else
    {
      foreach ($testCases as $testCase)
      {
        $vars['runTests'][$testCase] = 'idle'; //Show all checked by default
      }
    }
    
    $this->_render($vars);
  }
  
  /**
   * Run tests in the given array using the REST API (kind of).
   * @param string[] $tests
   * @param Sweety_Reporter $reporter
   * @return int
   */
  protected function _runTestList(array $tests, Sweety_Reporter $reporter)
  {
    $protocol = !empty($_SERVER['HTTPS']) ? 'https://' : 'http://';
    
    //Most likely a HTTP/1.0 server not supporting HOST header
    $server = !empty($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '127.0.0.1';
    
    $path = '/';
    if (!empty($_SERVER['REQUEST_URI']))
    {
      $path = preg_replace('/\?.*$/sD', '', $_SERVER['REQUEST_URI']);
    }
    
    $baseUrl = $protocol . $server . $path;
    
    foreach ($tests as $testCase)
    {
      $url = $baseUrl . '?test=' . $testCase . '&format=xml';
      $xml = file_get_contents($url);
      $this->parseXml($xml, $testCase);
    }
    
    return 0;
  }
  
  /**
   * Renders the view for the suite.
   * @param string[] $templateVars
   */
  protected function _render($vars = array())
  {
    foreach ($vars as $k => $v)
    {
      $$k = $v;
    }
    
    require_once $this->_template;
  }
  
}
