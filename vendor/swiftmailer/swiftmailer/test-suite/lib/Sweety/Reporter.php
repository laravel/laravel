<?php

/**
 * Interface for sending output to the client.
 * @package Sweety
 * @author Chris Corbyn
 */
interface Sweety_Reporter
{
  
  /**
   * Get the reporter used to report on this specific test case.
   * @param string $testCase
   * @return Sweety_Reporter
   */
  public function getReporterFor($testCase);
  
  /**
   * Returns true if start() has been invoked.
   * @return boolean
   */
  public function isStarted();
  
  /**
   * Start reporting.
   */
  public function start();
  
  /**
   * Report a skipped test case.
   * @param string $message
   * @param string $path
   */
  public function reportSkip($message, $path);
  
  /**
   * Report a passing assertion.
   * @param string $message
   * @param string $path
   */
  public function reportPass($message, $path);
  
  /**
   * Report a failing assertion.
   * @param string $message
   * @param string $path
   */
  public function reportFail($message, $path);
  
  /**
   * Report an unexpected exception.
   * @param string $message
   * @param string $path
   */
  public function reportException($message, $path);
  
  /**
   * Report output from something like a dump().
   * @param string $output
   * @param string $path
   */
  public function reportOutput($output, $path);
  
  /**
   * End reporting.
   */
  public function finish();
  
}
