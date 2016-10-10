<?php

/**
 * Interface for any strategy for finding test cases.
 * @package Sweety
 * @author Chris Corbyn
 */
interface Sweety_TestLocator
{
  
  /**
   * Returns an array of all test cases found under the given directories.
   * @param string[] $dirs
   * @return string[]
   */
  public function getTests($dirs = array());
  
  /**
   * Loads the test case of the given name.
   * @param string $testCase
   * @return boolean
   */
  public function includeTest($testCase);
  
}
