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
 * An Action which throws an Exception.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Actions_ThrowAction implements Yay_Action
{
  
  /**
   * The Exception to throw.
   * @var Exception
   * @access private
   */
  private $_e;
  
  /**
   * Create a new ThrowAction for $e.
   * @param Exception $e
   */
  public function __construct(Exception $e)
  {
    $this->_e = $e;
  }
  
  /**
   * Mimmick the method Invocation and throw an Exception.
   * @param Yay_Invocation $invocation
   * @throws Exception
   */
  public function &invoke(Yay_Invocation $invocation)
  {
    throw $this->_e;
  }
  
  /**
   * Describe this Expectation to $description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description)
  {
    $description->appendText(
      sprintf(' Throws %s;', get_class($this->_e))
      );
  }
  
}
