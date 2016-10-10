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
 * Allows anything to match.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Matchers_AnyMatcher implements Yay_Matcher
{
  
  /**
   * A type to compare with.
   * @var string
   * @access private
   */
  private $_type;
  
  /**
   * The desired result.
   * @var boolean
   * @access private
   */
  private $_result;
  
  /**
   * Create a new AnyMatcher, optionally constrained only to objects of $type.
   * @param string $type, optional
   * @param boolean $result
   */
  public function __construct($type = null, $result = true)
  {
    $this->_type = $type;
    $this->_result = $result;
  }
  
  /**
   * Always returns true where no type is given, and where the type matches otherwise.
   * @param mixed $value
   * @return boolean
   */
  public function matches(&$value)
  {
    $return = (is_null($this->_type) || ($value instanceof $this->_type));
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
    return 'ANYTHING';
  }
  
}