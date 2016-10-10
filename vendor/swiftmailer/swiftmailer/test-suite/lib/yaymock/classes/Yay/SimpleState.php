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
 
//require 'Yay/State.php';
//require 'Yay/States.php';
//require 'Yay/SimpleStatePredicate.php';

/**
 * A State from a State machine.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_SimpleState extends Yay_SimpleStatePredicate implements Yay_State
{
  
  /**
   * Create a new State for $stateMachine to be $stateName.
   * @param Yay_States $stateMachine
   * @param string $stateName
   */
  public function __construct(Yay_States $stateMachine, $stateName)
  {
    parent::__construct($stateMachine, $stateName, true);
  }
  
  /**
   * Make this State active.
   */
  public function activate()
  {
    $this->_stateMachine->become($this->_stateName);
  }
  
}
