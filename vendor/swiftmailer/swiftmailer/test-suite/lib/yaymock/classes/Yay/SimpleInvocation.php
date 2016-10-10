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
 
//require 'Yay/Invocation.php';
//require 'Yay/MockGenerator.php';

/**
 * The standard implementation of the Invocation interface.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_SimpleInvocation implements Yay_Invocation
{
  
  /**
   * The Object on which the Inovation occurred.
   * @var object
   * @access private
   */
  private $_object;
  
  /**
   * The method name invoked.
   * @var string
   * @access private
   */
  private $_method;
  
  /**
   * The arguments in the Invocation.
   * @var array
   * @access private
   */
  private $_arguments;
  
  /**
   * Create a new SimpleInvocation with the given details.
   * @param object $object
   * @param string $method
   * @param array &$arguments
   */
  public function __construct($object, $method, array &$arguments)
  {
    $this->_object = $object;
    //Massage __call() overloading so the interface is tested correctly
    if ($method == '__call')
    {
      $method = array_shift($arguments);
      $args =& array_shift($arguments);
      $arguments =& $args;
    }
    $this->_method = $method;
    $this->_arguments =& $arguments;
  }
  
  /**
   * Get the object which this Invocation occured on.
   * @return object
   */
  public function getObject()
  {
    return $this->_object;
  }
  
  /**
   * Get the method name of the invoked method.
   * @return string
   */
  public function getMethod()
  {
    return $this->_method;
  }
  
  /**
   * Get the argument list in the Invocation.
   * @return array
   */
  public function &getArguments()
  {
    return $this->_arguments;
  }
  
  /**
   * Describe this Invocation to $description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description)
  {
    $description->appendText(sprintf(' of %s;', $this->_getInvocationSignature()));
  }
  
  // -- Private methods
  
  private function _getInvocationSignature()
  {
    $class = Yay_MockGenerator::getInstance()
      ->reverseNamingScheme(get_class($this->_object));
    if (!empty($this->_arguments))
    {
      $args = array();
      foreach ($this->_arguments as $arg)
      {
        $args[] = $this->_describeArgument($arg, '%s [%s]');
      }
      $params = implode(', ', $args);
    }
    else
    {
      $params = '';
    }
    return sprintf('%s::%s(%s)', $class, $this->_method, $params);
  }
  
  private function _describeArgument($arg, $format)
  {
    $description = '';
    if (is_int($arg))
    {
      $description = sprintf($format, 'int', $arg);
    }
    elseif (is_float($arg))
    {
      $description = sprintf($format, 'float', preg_replace('/^(.{8}).+/', '$1..', $arg));
    }
    elseif (is_numeric($arg))
    {
      $description = sprintf($format, 'number', preg_replace('/^(.{8}).+/', '$1..', $arg));
    }
    elseif (is_string($arg))
    {
      $description = sprintf($format, 'string', preg_replace('/^(.{8}).+/', '$1..', $arg));
    }
    elseif (is_object($arg))
    {
      $description = sprintf($format, 'object', get_class($arg));
    }
    elseif (is_array($arg))
    {
      $description = sprintf($format, 'array', count($arg) . ' items');
    }
    else
    {
      $description = sprintf($format, gettype($arg), preg_replace('/^(.{8}).+/', '$1..', (string) $arg));
    }
    return $description;
  }
  
}
