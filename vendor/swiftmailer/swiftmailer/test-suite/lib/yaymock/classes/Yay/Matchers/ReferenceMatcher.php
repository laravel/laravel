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
 * Compares values to see if they reference one another.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Matchers_ReferenceMatcher implements Yay_Matcher
{
  
  /**
   * The expected reference.
   * @var mixed
   * @access private
   */
  private $_ref;
  
  /**
   * The desired return value.
   * @var boolean
   * @access private
   */
  private $_result;
  
  /**
   * Create a new IdenticalMatcher expecting $expected.
   * @param mixed $expected
   * @param boolean $result to be expected
   */
  public function __construct(&$ref, $result = true)
  {
    $this->_ref =& $ref;
    $this->_result = $result;
  }
  
  /**
   * Compare $ref with the expected reference and return true if it is the same reference.
   * @param mixed $ref
   * @return boolean
   */
  public function matches(&$ref)
  {
    if (is_object($ref))
    {
      $isRef = ($this->_ref === $ref);
    }
    else
    {
      if ($this->_ref === $ref)
      {
        $copy = $ref;
        $randomString = uniqid('yay');
        $ref = $randomString;
        $isRef = ($this->_ref === $ref);
        $ref = $copy;
      }
      else
      {
        $isRef = false;
      }
    }
    
    return (($this->_result && $isRef) || (!$this->_result && !$isRef));
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
    return '[reference]';
  }
  
}