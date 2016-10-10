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
 * Compares values for value and type.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Matchers_IdenticalMatcher implements Yay_Matcher
{
  
  /**
   * The expected value.
   * @var mixed
   * @access protected
   */
  protected $_expected;
  
  /**
   * The expected return value.
   * @var boolean
   * @access protected
   */
  protected $_result;
  
  /**
   * Create a new IdenticalMatcher expecting $expected.
   * @param mixed $expected
   * @param boolean $result to be expected
   */
  public function __construct($expected, $result = true)
  {
    $this->_expected = $expected;
    $this->_result = $result;
  }
  
  /**
   * Compare $value with the expected value and return true if it matches in
   * type and in value.
   * @param mixed $value
   * @return boolean
   */
  public function matches(&$value)
  {
    $return = (($this->_expected === $value) && ($value === $this->_expected));
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
    $description = '';
    $value = $this->_expected;
    if (is_int($value))
    {
      $description = sprintf($format, 'int', $value);
    }
    elseif (is_float($value))
    {
      $description = sprintf($format, 'float', preg_replace('/^(.{8}).+/', '$1..', $value));
    }
    elseif (is_numeric($value))
    {
      $description = sprintf($format, 'number', preg_replace('/^(.{8}).+/', '$1..', $value));
    }
    elseif (is_string($value))
    {
      $description = sprintf($format, 'string', preg_replace('/^(.{8}).+/', '$1..', $value));
    }
    elseif (is_object($value))
    {
      $description = sprintf($format, 'object', get_class($value));
    }
    elseif (is_array($value))
    {
      $description = sprintf($format, 'array', count($value) . ' items');
    }
    else
    {
      $description = sprintf($format, gettype($value), preg_replace('/^(.{8}).+/', '$1..', (string) $value));
    }
    return $description;
  }
  
}