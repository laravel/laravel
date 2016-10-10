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
 
//require 'Yay/MockObject.php';
//require 'Yay/Invocation.php';
//require 'Yay/Action.php';
//require 'Yay/SelfDescribing.php';
//require 'Yay/State.php';
//require 'Yay/StatePredicate.php';
//require 'Yay/Sequence.php';

/**
 * An Invocation expectation.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
interface Yay_Expectation extends Yay_SelfDescribing
{
  
  /**
   * Specify the MockObject which the Invocation will occur.
   * This method should return the mock object in record mode.
   * @param Yay_MockObject $mock
   * @return Yay_MockObject
   */
  public function of(Yay_MockObject $mock);
  
  /**
   * Notify the Expectation of an Invocation and check if it matches.
   * @param Yay_Invocation $invocation
   * @return boolean
   */
  public function isExpected(Yay_Invocation $invocation);
  
  /**
   * Specify the Action to run if a match occurs.
   * @param Yay_Action $action
   */
  public function will(Yay_Action $action);
  
  /**
   * Only be expected when in the given State predicate.
   * @param Yay_StatePredicate $predicate
   */
  public function when(Yay_StatePredicate $predicate);
  
  /**
   * Activate the given $state if a match occurs.
   * @param Yay_State $state
   */
  public function then(Yay_State $state);
  
  /**
   * Constrain this expectation to be valid only if invoked in the given sequence.
   * @param Yay_Sequence $sequence
   */
  public function inSequence(Yay_Sequence $sequence);
  
  /**
   * Test if all conditions of the Invocation are satisfied.
   * @return boolean
   */
  public function isSatisfied();
  
  /**
   * Get the Action for the given Invocation.
   * This may have been specified by a will() clause.
   * @param Yay_Invocation $invocation
   * @return Yay_Action
   */
  public function getAction(Yay_Invocation $invocation);
  
}
