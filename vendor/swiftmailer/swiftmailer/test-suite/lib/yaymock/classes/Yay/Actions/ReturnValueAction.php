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
 
//require 'Yay/Action.php';

/**
 * An Action which returns a specified value.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Actions_ReturnValueAction implements Yay_Action
{
  
  /**
   * The value to return.
   * @var mixed
   * @access private
   */
  private $_value;
  
  /**
   * Create a new ReturnValueAction for $value.
   * @param mixed $value
   */
  public function __construct($value)
  {
    $this->_value = $value;
  }
  
  /**
   * Mimmick the method Invocation and return a value.
   * @param Yay_Invocation $invocation
   * @return mixed
   */
  public function &invoke(Yay_Invocation $invocation)
  {
    $value = $this->_value;
    return $value;
  }
  
  /**
   * Describe this Expectation to $description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description)
  {
    $description->appendText(sprintf(' Returns %s;', $this->_describeValue('%s [%s]')));
  }
  
  private function _describeValue($format)
  {
    $description = '';
    $value = $this->_value;
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
