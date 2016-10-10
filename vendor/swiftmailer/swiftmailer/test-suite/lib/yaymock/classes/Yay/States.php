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
 
//require 'Yay/SelfDescribing.php';

/**
 * A basic state machine.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
interface Yay_States extends Yay_SelfDescribing
{
  
  /**
   * Set the initial state of this state machine.
   * @param string $stateName
   * @return Yay_States
   */
  public function startsAs($stateName);
  
  /**
   * Get the state which puts the state machine into the named state.
   * @param string $stateName
   * @return Yay_State
   */
  public function is($stateName);
  
  /**
   * Get the predicate which indicates the state machine is NOT in the named state.
   * @param string $stateName
   * @return Yay_StatePredicate
   */
  public function isNot($stateName);
  
  /**
   * Become the named state.
   * @param string $stateName
   */
  public function become($stateName);
  
  /**
   * Get the name of the current state.
   * @return string
   */
  public function getCurrentState();
  
}
