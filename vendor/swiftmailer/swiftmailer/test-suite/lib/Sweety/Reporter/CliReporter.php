<?php

require_once 'Sweety/Reporter.php';
require_once 'Sweety/Reporter/CliTestCaseReporter.php';

/**
 * The reporter used in command line reporting.
 * @package Sweety
 * @author Chris Corbyn
 */
class Sweety_Reporter_CliReporter implements Sweety_Reporter
{
  
  /**
   * True if this repoter is running.
   * @var boolean
   * @access private
   */
  private $_started = false;
  
  /**
   * The name to show this report as.
   * @var string
   * @access private
   */
  private $_name;
  
  /**
   * Aggregate scores from tests run.
   * @var int[]
   */
  private $_aggregates = array();
  
  /**
   * Creates a new CliReporter.
   */
  public function __construct($name)
  {
    $this->_name = $name;
    $this->_aggregates = array(
      'cases' => 0,
      'run' => 0,
      'passes' => 0,
      'fails' => 0,
      'exceptions' => 0
      );
  }
  
  /**
   * Used so test case reporters can notify this reporter when they've completed.
   * @param string $testCase
   */
  public function notifyEnded($testCase)
  {
    $this->_aggregates['run']++;
  }
  
  /**
   * Get the reporter used to report on this specific test case.
   * @param string $testCase
   * @return Sweety_Reporter
   */
  public function getReporterFor($testCase)
  {
    $this->_aggregates['cases']++;
    
    $reporter = new Sweety_Reporter_CliTestCaseReporter($testCase, $this);
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
    echo $this->_name . PHP_EOL;
  }
  
  /**
   * Report a skipped test case.
   * @param string $message
   * @param string $path
   */
  public function reportSkip($message, $path)
  {
    echo "  \033[34m\033[1m\033[4mSkip\033[0m:";
    $messageLines = explode(PHP_EOL, wordwrap($message, 74, PHP_EOL));
    foreach ($messageLines as $line)
    {
      echo '  ' . $line . PHP_EOL;
    }
    echo '    in: ' . $path . PHP_EOL;
  }
  
  /**
   * Report a passing assertion.
   * @param string $message
   * @param string $path
   */
  public function reportPass($message, $path)
  {
    $this->_aggregates['passes']++;
  }
  
  /**
   * Report a failing assertion.
   * @param string $message
   * @param string $path
   */
  public function reportFail($message, $path)
  {
    $this->_aggregates['fails']++;
    
    echo "\033[31m" . $this->_aggregates['fails'] . ') ';
    echo $message . "\033[0m" . PHP_EOL;
    echo '    in: ' . $path . PHP_EOL;
  }
  
  /**
   * Report an unexpected exception.
   * @param string $message
   * @param string $path
   */
  public function reportException($message, $path)
  {
    $this->_aggregates['exceptions']++;
    
    echo "\033[31m\033[1mException" . $this->_aggregates['exceptions'] . "\033[0m!" . PHP_EOL;
    echo "\033[1m" . $message . "\033[0m" . PHP_EOL;
    echo '    in ' . $path . PHP_EOL;
  }
  
  /**
   * Report output from something like a dump().
   * @param string $output
   * @param string $path
   */
  public function reportOutput($output, $path)
  {
    if (preg_match('/^\{image @ (.*?)\}$/D', $output, $matches))
    {
      echo "  \033[33mSmoke Test\033[0m" . PHP_EOL;
      echo '  Compare email sent with image @ ' . $matches[1] . PHP_EOL;
    }
    else
    {
      echo '--------------------' . PHP_EOL;
      echo $output . PHP_EOL;
      echo '--------------------' . PHP_EOL;
    }
  }
  
  /**
   * End reporting.
   */
  public function finish()
  {
    $this->_started = false;
    
    $incomplete = $this->_aggregates['cases'] - $this->_aggregates['run'];
    
    if ($incomplete)
    {
      echo '**********************' . PHP_EOL;
      echo $incomplete . ' test case(s) did not complete.' . PHP_EOL .
        'This may be because invalid XML was output during the test run' . PHP_EOL .
        'and/or because an error occured.' . PHP_EOL .
        'Try running the tests separately for more detail.' . PHP_EOL;
      echo '**********************' . PHP_EOL;
    }
    
    $success = (!$this->_aggregates['fails'] && !$this->_aggregates['exceptions']
      && $this->_aggregates['cases'] == $this->_aggregates['run']);
    
    if ($success)
    {
      echo "\033[32m\033[1mOK\033[0m" . PHP_EOL;
    }
    else
    {
      echo "\033[31m\033[1mFAILURES!!!\033[0m" . PHP_EOL;
    }
    
    echo 'Test cases run: ';
    echo $this->_aggregates['run'] . '/' . $this->_aggregates['cases'] . ', ';
    echo 'Passes: ' . $this->_aggregates['passes'] . ', ';
    echo 'Failures: ' . $this->_aggregates['fails'] . ', ';
    echo 'Exceptions: '. $this->_aggregates['exceptions'] . PHP_EOL;
    
    exit((int) !$success);
  }
  
}
