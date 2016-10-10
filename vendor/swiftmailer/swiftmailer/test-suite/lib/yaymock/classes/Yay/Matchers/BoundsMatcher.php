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
 * Compares values to test if they are within given boundaries.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Matchers_BoundsMatcher implements Yay_Matcher
{
  
  /**
   * The upper bound.
   * @var mixed
   * @access private
   */
  private $_upper;
  
  /**
   * The lower bound.
   * @var mixed
   * @access private
   */
  private $_lower;
  
  /**
   * The desired result.
   * @var boolean
   * @access private
   */
  private $_result;
  
  /**
   * Create a new BoundsMatcher between $lower and $upper.
   * @param mixed $lower
   * @param mixed $upper
   * @param boolean $result which is wanted
   */
  public function __construct($lower, $upper, $result = true)
  {
    $this->_upper = $upper;
    $this->_lower = $lower;
    $this->_result = $result;
  }
  
  /**
   * Compare $value with the boundaries and return true if it is within them.
   * @param mixed $value
   * @return boolean
   */
  public function matches(&$value)
  {
    $return = ($value <= $this->_upper && $value >= $this->_lower);
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
    return sprintf($format, 'between', $this->_min . ' and ' . $this->_max);
  }
  
}