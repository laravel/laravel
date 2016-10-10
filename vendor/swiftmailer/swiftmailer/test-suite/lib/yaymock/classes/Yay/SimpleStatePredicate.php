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
 
//require 'Yay/StatePredicate.php';
//require 'Yay/States.php';

/**
 * An expectation about what State a state machine is in.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_SimpleStatePredicate implements Yay_StatePredicate
{
  
  /**
   * The state machine which this predicate checks.
   * @var Yay_States
   * @access private
   */
  protected $_stateMachine;
  
  /**
   * The state name to check for in the state machine.
   * @var string
   * @access private
   */
  protected $_stateName;
  
  /**
   * True if the state is wanted, false otherwise.
   * @var boolean
   * @access private
   */
  private $_is = true;
  
  /**
   * Create a new StatePredicate.
   * @param Yay_States $stateMachine
   * @param string $stateName to expect
   * @param boolean $is (negation point)
   */
  public function __construct(Yay_States $stateMachine, $stateName, $is = true)
  {
    $this->_stateMachine = $stateMachine;
    $this->_stateName = $stateName;
    $this->_is = $is;
  }
  
  /**
   * Return true if the state machine is in this state.
   * @return boolean
   */
  public function isActive()
  {
    return (($this->_is && $this->_stateMachine->getCurrentState() == $this->_stateName)
     || (!$this->_is && $this->_stateMachine->getCurrentState() != $this->_stateName));
  }
  
  /**
   * Write a description of this self describing object to Description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description)
  {
    $this->_stateMachine->describeTo($description);
    $description->appendText(sprintf(
      ' %s %s;',
      ($this->_is ? 'is' : 'is not'),
      $this->_stateName
      ));
  }
  
}
