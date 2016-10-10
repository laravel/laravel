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

//require 'Yay/Expectation.php';
//require 'Yay/InvocationRecorder.php';
//require 'Yay/InvocationProxy.php';
//require 'Yay/Invocation.php';
//require 'Yay/State.php';
//require 'Yay/StatePredicate.php';
//require 'Yay/Sequence.php';
//require 'Yay/Expectations/ExactlyExpectation.php';
//require 'Yay/Expectations/AtLeastExpectation.php';
//require 'Yay/Expectations/AtMostExpectation.php';
//require 'Yay/Expectations/BetweenExpectation.php';
//require 'Yay/Action.php';
//require 'Yay/Actions/ReturnValueAction.php';
//require 'Yay/Actions/ReturnReferenceAction.php';
//require 'Yay/Actions/ThrowAction.php';
//require 'Yay/Actions/CallbackAction.php';

/**
 * A group of expectations which can be specified in a fluid manner.
 * Generally speaking this is where all expectations should be made for the sake
 * of abstraction.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Expectations implements Yay_InvocationRecorder
{

  /**
   * The Expectation stack.
   * @var array
   * @access private
   */
  private $_expectations = array();

  /**
   * The current Expectation to proxy any recording to.
   * @var Yay_Expectation
   * @access private
   */
  private $_currentEndpoint;

  /**
   * Create a new instance of Expectations.
   * @return Yay_Expectations
   */
  final public static function create()
  {
    return new self();
  }

  /**
   * Expect one Invocation on the $mock object.
   * Returns the mock object in record mode.
   * @param Yay_MockObject $mock
   * @return Yay_Expectations
   */
  public function one(Yay_MockObject $mock)
  {
    return $this->exactly(1)->of($mock);
  }

  /**
   * Expect exactly $n Invocations on a mock object specified with a following
   * of() clause.
   * Example: <code> Expectations::create()->exactly(2)->of($mock); </code>
   * @param integer $n
   * @return Yay_Expectations
   */
  public function exactly($n)
  {
    return $this->_setEndpoint(new Yay_Expectations_ExactlyExpectation($n));
  }

  /**
   * Expect at least $n Invocations on a mock object specified with a following
   * of() clause.
   * Example: <code> Expectations::create()->atLeast(2)->of($mock); </code>
   * @param integer $n
   * @return Yay_Expectations
   */
  public function atLeast($n)
  {
    return $this->_setEndpoint(new Yay_Expectations_AtLeastExpectation($n));
  }

  /**
   * Expect at most $n Invocations on a mock object specified with a following
   * of() clause.
   * Example: <code> Expectations::create()->atMost(2)->of($mock); </code>
   * @param integer $n
   * @return Yay_Expectations
   */
  public function atMost($n)
  {
    return $this->_setEndpoint(new Yay_Expectations_AtMostExpectation($n));
  }

  /**
   * Expect at between $min and $max Invocations on a mock object specified
   * with a following of() clause.
   * Example: <code> Expectations::create()->atLeast(2)->of($mock); </code>
   * @param integer $n
   * @return Yay_Expectations
   */
  public function between($min, $max)
  {
    return $this->_setEndpoint(new Yay_Expectations_BetweenExpectation($min, $max));
  }

  /**
   * Ignore Invocations on the $mock object specified.
   * @param Yay_MockObject $mock
   * @return Yay_Expectations
   */
  public function ignoring(Yay_MockObject $mock)
  {
    return $this->atLeast(0)->of($mock);
  }

  /**
   * Allow Invocations on the $mock object specified.
   * This does exactly the same thing as ignoring() but it allows a semantically
   * different meaning in the test case.
   * @param Yay_MockObject $mock
   * @return Yay_Expectations
   */
  public function allowing(Yay_MockObject $mock)
  {
    return $this->ignoring($mock);
  }

  /**
   * Deny Invocations on the $mock object specified.
   * @param Yay_MockObject $mock
   * @return Yay_Expectations
   */
  public function never(Yay_MockObject $mock)
  {
    return $this->exactly(0)->of($mock);
  }

  /**
   * Specify the MockObject which the Invocation will occur.
   * This method returns the mock object in record mode.
   * @param Yay_MockObject $mock
   * @return Yay_InvocationProxy
   */
  public function of(Yay_MockObject $mock)
  {
    $this->_getEndpoint()->of($mock);
    return new Yay_InvocationProxy($this, $mock);
  }

  /**
   * Specify the Action to run if a match occurs.
   * @param Yay_Action $action
   */
  public function will(Yay_Action $action)
  {
    $this->_getEndpoint()->will($action);
    return $this;
  }

  /**
   * Only be expected when in the given State predicate.
   * @param Yay_StatePredicate $predicate
   */
  public function when(Yay_StatePredicate $predicate)
  {
    $this->_getEndpoint()->when($predicate);
    return $this;
  }

  /**
   * Activate the given $state if a match occurs.
   * @param Yay_State $state
   */
  public function then(Yay_State $state)
  {
    $this->_getEndpoint()->then($state);
    return $this;
  }

  /**
   * Constrain the current expectation to occur in the given sequence.
   * @param Yay_Sequence $seq
   */
  public function inSequence(Yay_Sequence $seq)
  {
    $this->_getEndpoint()->inSequence($seq);
    return $this;
  }

  /**
   * A wrapper for will(Yay::returnValue($value)).
   * @param mixed $value
   */
  public function returns($value)
  {
    $this->_getEndpoint()->will(new Yay_Actions_ReturnValueAction($value));
    return $this;
  }

  /**
   * A wrapper for will(Yay::returnReference($ref)).
   * @param mixed $ref
   */
  public function returnsReference(&$ref)
  {
    $this->_getEndpoint()->will(new Yay_Actions_ReturnReferenceAction($ref));
    return $this;
  }

  /**
   * A wrapper for will(Yay::throwException($e)).
   * @param Exception $e
   */
  public function throws(Exception $e)
  {
    $this->_getEndpoint()->will(new Yay_Actions_ThrowAction($e));
    return $this;
  }

  /**
   * A wrapper for will(Yay::call($callback)).
   * @param callback $callback
   */
  public function calls($callback)
  {
    $this->_getEndpoint()->will(new Yay_Actions_CallbackAction($callback));
    return $this;
  }

  /**
   * Record any Invocations on the MockObject whilst it's in record mode.
   * @param Yay_Invocation $invocation
   */
  public function recordInvocation(Yay_Invocation $invocation)
  {
    $this->_getEndpoint()->recordInvocation($invocation);
  }

  /**
   * Returns the Expectation stack.
   * @return Yay_Expectation
   */
  public function getExpectations()
  {
    return $this->_expectations;
  }

  // -- Private methods

  /**
   * Apply a new Expectation to the stack and tag it as the endpoint for recording.
   * @param Yay_Expectation $expectation
   * @return Yay_Expectations
   * @access private
   */
  private function _setEndpoint(Yay_Expectation $expectation)
  {
    $this->_expectations[] = $expectation;
    $this->_currentEndpoint = $expectation;
    return $this;
  }

  /**
   * Gets the current endpoint (current expectation).
   * @return Yay_Expectation
   * @access private
   */
  private function _getEndpoint()
  {
    if (!isset($this->_currentEndpoint))
    {
      throw new BadMethodCallException(
        'No cardinality clause has yet been made.  First call one(), atLeast(), ' .
        'atMost(), exactly(), between(), ignoring(), allowing() or never() ' .
        'before performing this operation.'
        );
    }
    else
    {
      return $this->_currentEndpoint;
    }
  }

}
