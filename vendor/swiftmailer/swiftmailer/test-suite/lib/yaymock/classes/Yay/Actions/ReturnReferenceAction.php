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
 * An Action which returns a reference.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Actions_ReturnReferenceAction implements Yay_Action
{
  
  /**
   * The reference to return.
   * @var mixed
   * @access private
   */
  private $_ref;
  
  /**
   * Create a new ReturnReferenceAction for &$ref.
   * @param mixed $ref
   */
  public function __construct(&$ref)
  {
    $this->_ref =& $ref;
  }
  
  /**
   * Mimmick the method Invocation and return the reference.
   * @param Yay_Invocation $invocation
   * @return mixed
   */
  public function &invoke(Yay_Invocation $invocation)
  {
    return $this->_ref;
  }
  
  /**
   * Describe this Expectation to $description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description)
  {
    $description->appendText(' Returns a reference;');
  }
  
}
