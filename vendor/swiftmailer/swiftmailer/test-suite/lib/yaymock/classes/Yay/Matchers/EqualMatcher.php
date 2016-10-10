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
 
//require 'Yay/Matchers/IdenticalMatcher.php';

/**
 * Compares values for equality.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Matchers_EqualMatcher extends Yay_Matchers_IdenticalMatcher
{
  
  /**
   * Create a new EqualMatcher expecting $expected.
   * @param mixed $expected
   * @param boolean $result to be expected
   */
  public function __construct($expected, $result = true)
  {
    parent::__construct($expected, $result);
  }
  
  /**
   * Compare $value with the expected value and return true if it is equal.
   * @param mixed $value
   * @return boolean
   */
  public function matches(&$value)
  {
    $return =  (($this->_expected == $value) && ($value == $this->_expected));
    return (($this->_result && $return) || (!$this->_result && !$return));
  }
  
}