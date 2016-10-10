<?php

require_once 'Sweety/Runner/AbstractTestRunner.php';

/**
 * Runs SimpleTest cases as a group via the command line.
 * @package Sweety
 * @author Chris Corbyn
 */
class Sweety_Runner_CliRunner extends Sweety_Runner_AbstractTestRunner
{
  
  /**
   * Directories to scan for test cases.
   * @var string[]
   * @access private
   */
  private $_dirs = array();
  
  /**
   * The command to invoke when running test cases.
   * @var string
   * @access private
   */
  private $_command;
  
  /**
   * Creates a new CliRunner scanning the given directories, using the given
   * command and having the given name.
   * @param string[] $dirs
   * @param string $command
   * @param string $name
   */
  public function __construct(array $dirs, $command)
  {
    $this->_dirs = $dirs;
    $this->_command = $command;
  }
  
  /**
   * Runs all test cases found under the given directories.
   * @param string[] $directories to scan for test cases
   * @param string To be prepended to class names
   * @return int
   */
  public function runAllTests($dirs = array())
  {
    if (empty($dirs))
    {
      $dirs = $this->_dirs;
    }
    
    $reporter = $this->getReporter();
    
    if (!$reporter->isStarted())
    {
      $reporter->start();
    }
    
    $tests = $this->findTests($dirs);
    usort($tests, array($this, '_sort'));
    
    global $argv;
    
    if (!empty($argv[1]))
    {
      if (substr($argv[1], 0, 1) == '!')
      {
        $argv[1] = substr($argv[1], 1);
        foreach ($tests as $index => $name)
        {
          if (@preg_match($argv[1] . 'i', $name))
          {
            unset($tests[$index]);
          }
        }
      }
      else
      {
        foreach ($tests as $index => $name)
        {
          if (!@preg_match($argv[1] . 'i', $name))
          {
            unset($tests[$index]);
          }
        }
      }
    }
    
    $ret = $this->_runTestList($tests);
    
    $reporter->finish();
    
    return $ret;
  }
  
  /**
   * Run all possible tests from the given list.
   * @param string[] $tests
   * @return int
   */
  protected function _runTestList(array $tests)
  {
    foreach ($tests as $testCase)
    {     
      if (preg_match($this->getIgnoredClassRegex(), $testCase))
      {
        continue;
      }
      
      $command = $this->_command;
      $command .= ' ' . $testCase;
      $command .= ' ' . Sweety_Runner::REPORT_XML;
      
      exec($command, $output, $status);
      
      $xml = implode(PHP_EOL, $output);
      
      $this->parseXml($xml, $testCase);
      
      unset($status);
      unset($output);
    }
    
    return 0;
  }
  
}
