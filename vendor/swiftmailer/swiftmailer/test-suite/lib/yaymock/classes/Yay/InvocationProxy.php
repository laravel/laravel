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
 
//require 'Yay/ExpectationProvider.php';
//require 'Yay/InvocationRecorder.php';
//require 'Yay/MockObject.php';
//require 'Yay/SimpleInvocation.php';

/**
 * Proxies Invocations on a Mock object to the recorder.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_InvocationProxy implements Yay_ExpectationProvider
{
  
  /**
   * The InvocationRecorder which is using this InvocationProxy.
   * @var Yay_InvocationRecorder
   * @access private
   */
  private $_recorder;
  
  /**
   * The Mock object where Invocations are recorded from.
   * @var Yay_MockObject
   * @access private
   */
  private $_mock;
  
  /**
   * Create a new InvocationProxy for $recorder and $mock.
   * @param Yay_InvocationRecorder $recorder
   * @param Yay_MockObject $mock
   */
  public function __construct(Yay_InvocationRecorder $recorder, Yay_MockObject $mock)
  {
    $this->_recorder = $recorder;
    $this->_mock = $mock;
  }
  
  /**
   * Direct all invocations to the recorder.
   * @param string $method
   * @param array $args
   * @return Yay_InvocationRecorder
   */
  public function __call($method, $args)
  {
    if (is_callable(array($this->_mock, $method)))
    {
      $invocation = new Yay_SimpleInvocation($this->_mock, $method, $args);
      $this->_recorder->recordInvocation($invocation);
      return $this->_recorder;
    }
    elseif (is_callable(array($this->_recorder, $method)))
    {
      return call_user_func_array(array($this->_recorder, $method), $args);
    }
    else
    {
      throw new BadMethodCallException('Mock method ' . $method . ' does not exist');
    }
  }
  
  /**
   * Returns the Expectation list.
   * @return array of Yay_Expectation
   */
  public function getExpectations()
  {
    return $this->__call(__FUNCTION__, array());
  }
  
}
