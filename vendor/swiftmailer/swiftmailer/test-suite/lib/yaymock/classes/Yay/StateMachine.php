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
//require 'Yay/SimpleState.php';
//require 'Yay/StatePredicate.php';
//require 'Yay/SimpleStatePredicate.php';

/**
 * A basic state machine.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_StateMachine implements Yay_States
{
  
  /**
   * The name of this state machine.
   * @var string
   * @access private
   */
  private $_name;
  
  /**
   * The current state.
   * @var string
   * @access private
   */
  private $_state;
  
  /**
   * Create a new State machine with $name.
   * @param string $name
   */
  public function __construct($name)
  {
    $this->_name = $name;
  }
  
  /**
   * Set the initial state of this state machine.
   * @param string $stateName
   * @return Yay_States
   */
  public function startsAs($stateName)
  {
    $this->become($stateName);
    return $this;
  }
  
  /**
   * Get the state which puts the state machine into the named state.
   * @param string $stateName
   * @return Yay_State
   */
  public function is($stateName)
  {
    return new Yay_SimpleState($this, $stateName);
  }
  
  /**
   * Get the predicate which indicates the state machine is NOT in the named state.
   * @param string $stateName
   * @return Yay_StatePredicate
   */
  public function isNot($stateName)
  {
    return new Yay_SimpleStatePredicate($this, $stateName, false);
  }
  
  /**
   * Become the named state.
   * @param string $stateName
   */
  public function become($stateName)
  {
    $this->_state = $stateName;
  }
  
  /**
   * Get the name of the current state.
   * @return string
   */
  public function getCurrentState()
  {
    return $this->_state;
  }
  
  /**
   * Write a description of this self describing object to Description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description)
  {
    $description->appendText(sprintf(' %s', $this->_name));
  }
  
}
