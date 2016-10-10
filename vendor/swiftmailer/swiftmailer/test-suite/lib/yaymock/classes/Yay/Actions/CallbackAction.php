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
//require 'Yay/Description.php';

/**
 * An Action which delegates to a callback.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Actions_CallbackAction implements Yay_Action
{
  
  /**
   * The callback to invoke.
   * @var callback
   * @access private
   */
  private $_callback;
  
  /**
   * Create a new CallbackAction for $callback.
   * @param callback $callback
   */
  public function __construct($callback)
  {
    $this->_callback = $callback;
  }
  
  /**
   * Mimmick the method Invocation and return a value.
   * @param Yay_Invocation $invocation
   * @return mixed
   */
  public function &invoke(Yay_Invocation $invocation)
  {
    $ret = call_user_func($this->_callback, $invocation);
    return $ret;
  }
  
  /**
   * Describe this Expectation to $description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description)
  {
    $description->appendText(' Runs a callback;');
  }
  
}
