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
 
//require 'Yay/MockGenerator.php';
//require 'Yay/SimpleInvocation.php';
//require 'Yay/SimpleDescription.php';
//require 'Yay/InvocationHandler.php';
//require 'Yay/MockObject.php';
//require 'Yay/ExpectationProvider.php';
//require 'Yay/NotSatisfiedException.php';
//require 'Yay/StateMachine.php';
//require 'Yay/SimpleSequence.php';

/**
 * The main Yay context.
 * Handles the generation of MockObjects and the Invocation of methods.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Mockery implements Yay_InvocationHandler
{
  
  /**
   * The Expectation stack which is being checked.
   * @var array
   * @access private
   */
  private $_expectations = array();
  
  /**
   * Invocations which are not expected by any Expectations get caught here.
   * @var array
   * @access private
   */
  private $_unexpectedInvocations = array();
  
  /**
   * A mock class generator.
   * @var Yay_MockGenerator
   * @access private
   */
  private $_generator;
  
  /**
   * Create a new Mockery.
   */
  public function __construct()
  {
    $this->_generator = Yay_MockGenerator::getInstance();
  }
  
  /**
   * Create a MockObject matching $typeHint.
   * If the $typeHint is an interface the Mock will implement the interface
   * and maintain the method signatures from that interface.
   * If the $typeHint is a class name the Mock will extend the class overriding
   * all public methods (HOWEVER, if the class contains final methods it is not
   * possible to override all methods and hence, the mock will have no specific
   * type.
   * @param string $typeHint
   * @return Yay_MockObject
   */
  public function mock($typeHint)
  {
    $className = $this->_generator->generateMock($typeHint);
    $reflector = new ReflectionClass($className);
    return $reflector->newInstance($this);
  }
  
  /**
   * Specify an Expectation (or Expectations) to check.
   * @param Yay_ExpectationProvider $provider
   */
  public function checking(Yay_ExpectationProvider $provider)
  {
    foreach ($provider->getExpectations() as $expectation)
    {
      $this->_expectations[] = $expectation;
    }
  }
  
  /**
   * Get a state machine named $name.
   * @param string $name
   * @return Yay_States
   */
  public function states($name)
  {
    return new Yay_StateMachine($name);
  }
  
  /**
   * Create a new Sequence named $name.
   * @param string $name
   * @return Yay_Sequence
   */
  public function sequence($name)
  {
    return new Yay_SimpleSequence($name);
  }
  
  /**
   * Used by YayMock internally (ignore this method!).
   */
  public function &handleInvocation(Yay_Invocation $invocation)
  {
    $ret = null;
    $expected = false;
    foreach ($this->_expectations as $expectation)
    {
      if ($expectation->isExpected($invocation))
      {
        $expected = true;
        if ($action = $expectation->getAction($invocation))
        {
          $ret =& $action->invoke($invocation);
        }
        break;
      }
    }
    if (!$expected)
    {
      $this->_unexpectedInvocations[] = $invocation;
    }
    return $ret;
  }
  
  /**
   * Assert that all Expectations are satisfied.
   * Throws an Exception of type Yay_NotSatisfiedException if any Expecations
   * are not satisfied.
   * @throws Yay_NotSatisfiedException
   */
  public function assertIsSatisfied()
  {
    $description = new Yay_SimpleDescription();
    $satisfied = true;
    foreach ($this->_unexpectedInvocations as $invocation)
    {
      $description->appendText('Unexpected invocation');
      $invocation->describeTo($description);
      $description->appendText(PHP_EOL);
      $satisfied = false;
    }
    if (!$satisfied)
    {
      $description->appendText(PHP_EOL);
    }
    foreach ($this->_expectations as $expectation)
    {
      if (!$expectation->isSatisfied())
      {
        $description->appendText('* ');
        $satisfied = false;
      }
      $expectation->describeTo($description);
      $description->appendText(PHP_EOL);
    }
    if (!$satisfied)
    {
      throw new Yay_NotSatisfiedException(
        'Not all expectations were satisfied or a method was invoked unexpectedly.' .
        PHP_EOL . PHP_EOL . $description->toString() . PHP_EOL
        );
    }
  }
  
}
