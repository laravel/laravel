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
 
//require 'Yay/Mockery.php';
//require 'Yay/Expectation.php';
//require 'Yay/ExpectationProvider.php';
//require 'Yay/Invocation.php';
//require 'Yay/InvocationRecorder.php';
//require 'Yay/InvocationProxy.php';
//require 'Yay/Action.php';
//require 'Yay/Matcher.php';
//require 'Yay/Matchers/IdenticalMatcher.php';
//require 'Yay/State.php';
//require 'Yay/StatePredicate.php';
//require 'Yay/Sequence.php';
//require 'Yay/Description.php';
//require 'Yay/MockGenerator.php';

/**
 * A base Expectation which other Expectations extend.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
abstract class Yay_Expectations_AbstractExpectation
  implements Yay_Expectation, Yay_InvocationRecorder
{
  
  /**
   * The object to expect Invocations from.
   * @var Yay_MockObject
   * @access private
   */
  private $_object;
  
  /**
   * The method name to expect Invocations on.
   * @var string
   * @access private
   */
  private $_method;
  
  /**
   * The argument Matchers.
   * @var array
   * @access private
   */
  private $_matchers = array();
  
  /**
   * The Action to use if matched.
   * @var Yay_Action
   * @access private
   */
  private $_action;
  
  /**
   * A state predicate to check for.
   * @var Yay_StatePredicate
   * @access private
   */
  private $_statePredicate;
  
  /**
   * A state to effect if matched.
   * @var Yay_State
   * @access private
   */
  private $_state;
  
  /**
   * The ID wanted to be valid in the Sequence.
   * @var int
   * @access private
   */
  private $_wantedSequenceId;
  
  /**
   * The Sequence to check for validity (if any).
   * @var Yay_Sequence
   * @access private
   */
  private $_sequence;
  
  /**
   * Invoked when the expectation matches so any counters can be incremented
   * for example.
   * @param Yay_Invocation $invocation
   */
  abstract public function notifyMatchedInvocation(Yay_Invocation $invocation);
  
  /**
   * Describe the boundaries of how many invocations can occur.
   * @param Yay_Description $description
   */
  abstract public function describeBounds(Yay_Description $description);
  
  /**
   * Describe the current status of this expectation.
   * @param Yay_Description $description
   */
  abstract public function describeSatisfaction(Yay_Description $description);
  
  /**
   * Specify the MockObject which the Invocation will occur.
   * This method returns the mock object in record mode.
   * @param Yay_MockObject $mock
   * @return Yay_InvocationProxy
   */
  public function of(Yay_MockObject $mock)
  {
    $this->_object = $mock;
    return new Yay_InvocationProxy($this, $mock);
  }
  
  /**
   * Notify the Expectation of an Invocation and check if it matches.
   * @param Yay_Invocation $invocation
   * @return boolean
   */
  public function isExpected(Yay_Invocation $invocation)
  {
    $matches = true;
    $object = $invocation->getObject();
    if ($object === $this->_object)
    {
      if (isset($this->_statePredicate))
      {
        $matches = $this->_statePredicate->isActive();
      }
      
      if ($matches && isset($this->_method))
      {
        if ($this->_method == $invocation->getMethod())
        {
          $args =& $invocation->getArguments();
          foreach ($this->_matchers as $i => $m)
          {
            if (!array_key_exists($i, $args))
            {
              if ($m->isOptional())
              {
                break;
              }
              else
              {
                $matches = false;
                break;
              }
            }
            else
            {
              if (!$m->matches($args[$i]))
              {
                $matches = false;
                break;
              }
            }
          }
        }
        else
        {
          $matches = false;
        }
      }
      
      if ($matches && isset($this->_sequence))
      {
        $matches = $this->_sequence->isInSequence($this->_wantedSequenceId);
      }
    }
    else
    {
      $matches = false;
    }
    
    if ($matches)
    {
      $this->notifyMatchedInvocation($invocation);
    }
    
    return $matches;
  }
  
  /**
   * Specify the Action to run if a match occurs.
   * @param Yay_Action $action
   */
  public function will(Yay_Action $action)
  {
    $this->_action = $action;
    return $this;
  }
  
  /**
   * Only be expected when in the given State predicate.
   * @param Yay_StatePredicate $predicate
   */
  public function when(Yay_StatePredicate $predicate)
  {
    $this->_statePredicate = $predicate;
    return $this;
  }
  
  /**
   * Activate the given $state if a match occurs.
   * @param Yay_State $state
   */
  public function then(Yay_State $state)
  {
    $this->_state = $state;
    return $this;
  }
  
  /**
   * Constrain this expectation to be valid only if invoked in the given sequence.
   * @param Yay_Sequence $sequence
   */
  public function inSequence(Yay_Sequence $sequence)
  {
    $this->_wantedSequenceId = $sequence->requestSequenceId();
    $this->_sequence = $sequence;
    return $this;
  }
  
  /**
   * Get the Action for the given Invocation.
   * This may have been specified by a will() clause.
   * @param Yay_Invocation $invocation
   * @return Yay_Action
   */
  public function getAction(Yay_Invocation $invocation)
  {
    if (isset($this->_state))
    {
      $this->_state->activate();
    }
    return $this->_action;
  }
  
  /**
   * Record any Invocations on the MockObject whilst it's in record mode.
   * @param Yay_Invocation $invocation
   */
  public function recordInvocation(Yay_Invocation $invocation)
  {
    $this->_method = $invocation->getMethod();
    $matchers =& $invocation->getArguments();
    foreach ($matchers as $matcher)
    {
      if ($matcher instanceof Yay_Matcher)
      {
        $this->_matchers[] = $matcher;
      }
      else
      {
        $this->_matchers[] = new Yay_Matchers_IdenticalMatcher($matcher);
      }
    }
  }
  
  /**
   * Returns the Expectations.
   * @return array of Yay_Expectation
   */
  public function getExpectations()
  {
    return array($this);
  }
  
  /**
   * Describe this Expectation to $description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description)
  {
    $this->describeBounds($description);
    
    $description->appendText(sprintf(' of %s;', $this->_getInvocationSignature()));
    
    if (isset($this->_sequence))
    {
      $description->appendText(' in');
      $this->_sequence->describeTo($description);
    }
    
    if (isset($this->_statePredicate))
    {
      $description->appendText(' when');
      $this->_statePredicate->describeTo($description);
    }
    
    if (isset($this->_action))
    {
      $this->_action->describeTo($description);
    }
    
    $this->describeSatisfaction($description);
  }
  
  // -- Private methods
  
  private function _getInvocationSignature()
  {
    $class = Yay_MockGenerator::getInstance()
      ->reverseNamingScheme(get_class($this->_object));
    if (isset($this->_method))
    {
      $method = $this->_method;
    }
    else
    {
      $method = '<any method>';
    }
    if (!empty($this->_matchers))
    {
      $args = array();
      foreach ($this->_matchers as $matcher)
      {
        $args[] = $matcher->describeMatch('%s [%s]');
      }
      $params = implode(', ', $args);
    }
    else
    {
      $params = '';
    }
    return sprintf('%s::%s(%s)', $class, $method, $params);
  }
  
}
