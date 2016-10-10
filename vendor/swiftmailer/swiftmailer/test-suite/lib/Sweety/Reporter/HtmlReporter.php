<?php

require_once 'Sweety/Reporter.php';
require_once 'Sweety/Reporter/HtmlTestCaseReporter.php';

/**
 * The reporter used for the Html (non JS) backend.
 * @package Sweety
 * @author Chris Corbyn
 */
class Sweety_Reporter_HtmlReporter implements Sweety_Reporter
{
  
  /**
   * Template data.
   * @var mixed[]
   */
  public $_tplVars;
  
  /**
   * True if this repoter is running.
   * @var boolean
   * @access private
   */
  private $_started = false;
  
  /**
   * Set template vars.
   * @param mixed[]
   */
  public function setTemplateVars(&$vars)
  {
    $this->_tplVars =& $vars;
  }
  
  /**
   * Used so test case reporters can notify this reporter when they've completed.
   * @param string $testCase
   */
  public function notifyEnded($testCase)
  {
    $this->_tplVars['runTests'][] = $testCase;
    $this->_tplVars['runCount']++;
  }
  
  /**
   * Get the reporter used to report on this specific test case.
   * @param string $testCase
   * @return Sweety_Reporter
   */
  public function getReporterFor($testCase)
  {
    $this->_tplVars['caseCount']++;
    
    $reporter = new Sweety_Reporter_HtmlTestCaseReporter($testCase, $this);
    $reporter->setTemplateVars($this->_tplVars);
    return $reporter;
  }
  
  /**
   * Returns true if start() has been invoked.
   * @return boolean
   */
  public function isStarted()
  {
    return $this->_started;
  }
  
  /**
   * Start reporting.
   */
  public function start()
  {
    $this->_started = true;
  }
  
  /**
   * Report a skipped test case.
   * @param string $message
   * @param string $path
   */
  public function reportSkip($message, $path)
  {
    $this->_tplVars['messages'][] = array(
      'type' => 'skip',
      'path' => $path,
      'text' => $message);
  }
  
  /**
   * Report a passing assertion.
   * @param string $message
   * @param string $path
   */
  public function reportPass($message, $path)
  {
    $this->_tplVars['passCount']++;
  }
  
  /**
   * Report a failing assertion.
   * @param string $message
   * @param string $path
   */
  public function reportFail($message, $path)
  {
    $this->_tplVars['failCount']++;
    $this->_tplVars['messages'][] = array(
      'type' => 'fail',
      'path' => $path,
      'text' => $message);
  }
  
  /**
   * Report an unexpected exception.
   * @param string $message
   * @param string $path
   */
  public function reportException($message, $path)
  {
    $this->_tplVars['exceptionCount']++;
    $this->_tplVars['messages'][] = array(
      'type' => 'exception',
      'path' => $path,
      'text' => $message);
  }
  
  /**
   * Report output from something like a dump().
   * @param string $output
   * @param string $path
   */
  public function reportOutput($output, $path)
  {
    $this->_tplVars['messages'][] = array(
      'type' => 'output',
      'path' => $path,
      'text' => $output);
  }
  
  /**
   * End reporting.
   */
  public function finish()
  {
    $this->_started = false;
    
    if (!$this->_tplVars['failCount'] && !$this->_tplVars['exceptionCount']
      && $this->_tplVars['caseCount'] == $this->_tplVars['runCount'])
    {
      $this->_tplVars['result'] = 'pass';
    }
    else
    {
      $this->_tplVars['result'] = 'fail';
    }
    
    $incomplete = $this->_tplVars['caseCount'] - $this->_tplVars['runCount'];
    
    if (0 < $incomplete)
    {
      $this->_tplVars['messages'][] = array(
        'type' => 'internal',
        'path' => '',
        'text' => $incomplete . ' test case(s) did not complete.' .
        ' This may be because invalid XML was output during the test run' .
        ' and/or because an error occured.' .
        ' Incomplete test cases are shown in yellow.  Click the HTML link ' .
        'next to the test for more detail.'
        );
    }
  }
  
}
