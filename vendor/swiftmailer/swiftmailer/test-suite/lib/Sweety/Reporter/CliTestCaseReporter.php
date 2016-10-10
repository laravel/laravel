<?php

require_once 'Sweety/Reporter.php';
require_once 'Sweety/Reporter/CliReporter.php';

/**
 * A command line reporter for an individual test case.
 * @package Sweety
 * @author Chris Corbyn
 */
class Sweety_Reporter_CliTestCaseReporter implements Sweety_Reporter
{
  
  /**
   * True if this reporter is running.
   * @var boolean
   * @access private
   */
  private $_started = false;
  
  /**
   * The name of the test case being reported on.
   * @var string
   * @access private
   */
  private $_testCase;
  
  /**
   * The parent reporter this was spawned from.
   * @var Sweety_Reporter_CliReporter
   * @access private
   */
  private $_parent;
  
  /**
   * Aggregate totals for this test case.
   * @var int[]
   * @access private
   */
  private $_aggregates = array();
  
  /**
   * Creates a new CliTestCaseReporter.
   * @param string $testCase
   * @param Sweety_Reporter_CliReporter The parent reporter this was spawned from.
   */
  public function __construct($testCase, Sweety_Reporter_CliReporter $parent)
  {
    $this->_parent = $parent;
    $this->_testCase = $testCase;
    $this->_aggregates = array(
      'passes' => 0,
      'fails' => 0,
      'exceptions' => 0
      );
  }
  
  /**
   * Get the reporter used to report on this specific test case.
   * This method is stubbed only to return itself.
   * @param string $testCase
   * @return Sweety_Reporter
   */
  public function getReporterFor($testCase)
  {
    return $this;
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
    $this->_parent->reportSkip($message, $path);
  }
  
  /**
   * Report a passing assertion.
   * @param string $message
   * @param string $path
   */
  public function reportPass($message, $path)
  {
    $this->_aggregates['passes']++;
    $this->_parent->reportPass($message, $path);
  }
  
  /**
   * Report a failing assertion.
   * @param string $message
   * @param string $path
   */
  public function reportFail($message, $path)
  {
    $this->_aggregates['fails']++;
    $this->_parent->reportFail($message, $path);
  }
  
  /**
   * Report an unexpected exception.
   * @param string $message
   * @param string $path
   */
  public function reportException($message, $path)
  {
    $this->_aggregates['exceptions']++;
    $this->_parent->reportException($message, $path);
  }
  
  /**
   * Report output from something like a dump().
   * @param string $output
   * @param string $path
   */
  public function reportOutput($output, $path)
  {
    $this->_parent->reportOutput($output, $path);
  }
  
  /**
   * End reporting.
   */
  public function finish()
  {
    $this->_started = false;
    
    if (!$this->_aggregates['fails'] && !$this->_aggregates['exceptions'])
    {
      echo '  >> ' . $this->_testCase . ' ';
      echo "\033[32mOK\033[0m" . PHP_EOL;
    }
    else
    {
      echo '  !! ' . $this->_testCase . ' ';
      echo "\033[31mFAILED\033[0m" . PHP_EOL;
    }
    
    $this->_parent->notifyEnded($this->_testCase);
  }
  
}
