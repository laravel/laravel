<?php

/*
 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.
 
 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>.
 
 */
 
//require 'Yay/Matcher.php';

/**
 * Compares values against a PCRE pattern.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Matchers_PatternMatcher implements Yay_Matcher
{
  
  /**
   * The expected pattern.
   * @var string
   * @access private
   */
  private $_pattern;
  
  /**
   * The desired return value.
   * @var boolean
   * @access private
   */
  private $_result;
  
  /**
   * Create a new PatternMatcher expecting $pattern.
   * @param string $pattern
   * @param boolean $result to be expected
   */
  public function __construct($pattern, $result = true)
  {
    $this->_pattern = $pattern;
    $this->_result = $result;
  }
  
  /**
   * Compare $value with the expected pattern and return true if it matches.
   * @param string $value
   * @return boolean
   */
  public function matches(&$value)
  {
    $return = (
      (is_string($value) || is_numeric($value))
        && preg_match($this->_pattern, $value)
      );
    return (($this->_result && $return) || (!$this->_result && !$return));
  }
  
  /**
   * Returns true if the argument doesn't need to be present.
   * @return boolean
   */
  public function isOptional()
  {
    return false;
  }
  
  /**
   * Writes the match description as a string following $format.
   * $format is a sprintf() string with %s, $s as $matcherName, $value respectively.
   * @param string $format
   * @return string
   */
  public function describeMatch($format)
  {
    return sprintf($format, 'pattern', $this->_pattern);
  }
  
}