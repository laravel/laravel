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
 
/**
 * Generates the code for a Mock object.
 * This lives as a singleton for a few reasons.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_MockGenerator
{
  
  /** The name of the Mock object interface */
  const MOCK_INTERFACE = 'Yay_MockObject';
  
  /** Prefixed to types to create a Mock name */
  const MOCK_PREFIX = 'Yay_MockObjects_';
  
  /** Singleton instance */
  private static $_instance = null;
  
  /**
   * The path a template which draws a Mock.
   * @var string
   * @access private
   */
  private $_template;
  
  /**
   * A map of mocked type hints to their concrete class names.
   * @var array
   * @access private
   */
  private $_mocked = array();
  
  /**
   * Constructor cannot be used.
   */
  private function __construct()
  {
  }
  
  /**
   * Get a singleton instance of this MockGenerator.
   * @return Yay_MockGenerator
   */
  public static function getInstance()
  {
    if (is_null(self::$_instance))
    {
      self::$_instance = new self();
    }
    return self::$_instance;
  }
  
  /**
   * Set the path to a template which can draw a mock class.
   * @param string $path
   */
  public function setMockTemplate($path)
  {
    $this->_template = $path;
  }
  
  /**
   * Produce class code for a MockObject of $typeHint and return its concrete name.
   * @param string $typeHint
   * @return string
   */
  public function generateMock($typeHint)
  {
    if (!$className = $this->_getConcreteMockName($typeHint))
    {
      $className = $this->_materializeMockCode($typeHint);
    }
    return $className;
  }
  
  /**
   * Use a fixed naming scheme to make a mock class name from $typeHint.
   * @param string $typeHint
   * @return string
   */
  public function applyNamingScheme($typeHint)
  {
    return self::MOCK_PREFIX . $typeHint;
  }
  
  /**
   * Remove any adjustments that were made to an original type hint.
   * @param string $typeHint
   * @return string
   */
  public function reverseNamingScheme($typeHint)
  {
    $len = strlen(self::MOCK_PREFIX);
    if (substr($typeHint, 0, $len) == self::MOCK_PREFIX)
    {
      $typeHint = substr($typeHint, $len);
    }
    return $typeHint;
  }
  
  // -- Private methods
  
  /**
   * Try to lookup a mocked concrete class name for $typeHint.
   * @param string $typeHint
   * @return string
   * @access private
   */
  private function _getConcreteMockName($typeHint)
  {
    if (array_key_exists($typeHint, $this->_mocked))
    {
      return $this->_mocked[$typeHint];
    }
  }
  
  /**
   * Produce the mock object code and return its name.
   * @param string $typeHint
   * @return string
   * @access private
   */
  private function _materializeMockCode($typeHint)
  {
    $reflector = new ReflectionClass($typeHint);
    $mockData = array(
      'className' => $this->applyNamingScheme($typeHint),
      'extends' => $this->_getSuperclass($reflector),
      'interfaces' => $this->_getInterfaces($reflector),
      'methods' => $this->_getMethods($reflector)
      );
    
    extract($mockData);
    $code = include($this->_template);
    eval($code);
    
    $this->_mocked[$typeHint] = $mockData['className'];
    return $mockData['className'];
  }
  
  /**
   * Get all known interfaces for $reflector.
   * @param ReflectionClass $reflector
   * @return array
   * @access private
   */
  private function _getInterfaces(ReflectionClass $reflector)
  {
    $interfaces = array();
    if ($reflector->isInterface())
    {
      if ($reflector->getName() != self::MOCK_INTERFACE)
      {
        $interfaces[] = $reflector->getName();
      }
    }
    else
    {
      foreach ($reflector->getInterfaces() as $interfaceReflector)
      {
        if ($interfaceReflector->getName() != self::MOCK_INTERFACE)
        {
          $interfaces[] = $interfaceReflector->getName();
        }
      }
    }
    return $interfaces;
  }
  
  /**
   * Get the superclass this mock object needs to extend.
   * @param ReflectionClass $reflector
   * @return string
   * @access private
   */
  private function _getSuperclass(ReflectionClass $reflector)
  {
    if ($this->_canExtend($reflector))
    {
      $superclass = $reflector->getName();
    }
    else
    {
      $superclass = '';
    }
    return $superclass;
  }
  
  /**
   * Get all methods from $reflector.
   * @param ReflectionClass $reflector
   * @return array
   * @access private
   */
  private function _getMethods(ReflectionClass $reflector)
  {
    $methods = array();
    foreach ($reflector->getMethods() as $reflectionMethod)
    {
      if ($reflectionMethod->isConstructor()
        || $reflectionMethod->getName() == '__clone')
      {
        continue;
      }
      if ($reflectionMethod->isPublic() || $reflectionMethod->isProtected())
      {
        $methods[] = array(
          'name' => $reflectionMethod->getName(),
          'access' => $reflectionMethod->isPublic() ? 'public' : 'protected',
          'modifiers' => $reflectionMethod->isStatic() ? 'static' : '',
          'returnReference' => $reflectionMethod->returnsReference(),
          'parameters' => $this->_getParameters($reflectionMethod)
          );
      }
    }
    return $methods;
  }
  
  /**
   * Get all parameters for $method.
   * @param ReflectionMethod $method
   * @return array
   * @access private
   */
  private function _getParameters(ReflectionMethod $method)
  {
    $parameters = array();
    foreach ($method->getParameters() as $reflectionParameter)
    {
      $hint = '';
      if ($reflectionParameter->isArray())
      {
        $hint = 'array';
      }
      elseif ($c = $reflectionParameter->getClass())
      {
        $hint = $c->getName();
      }
      $parameters[] = array(
        'hint' => $hint,
        'byReference' => $reflectionParameter->isPassedByReference(),
        'optional' => $reflectionParameter->isOptional()
        );
    }
    return $parameters;
  }
  
  /**
   * Determine if the reflector for the given class is safe to extend.
   * @param ReflectionClass $reflector
   * @return boolean
   * @access private
   */
  private function _canExtend(ReflectionClass $reflector)
  {
    $canExtend = true;
    $warning = false;
    if ($reflector->isInterface())
    {
      $canExtend = false;
    }
    else
    {
      if ($constructor = $reflector->getConstructor())
      {
        if ($constructor->isPrivate() || $constructor->isFinal())
        {
          $canExtend = false;
          $warning = 'has a private or final constructor';
        }
      }
      elseif ($reflector->isFinal())
      {
        $canExtend = false;
        $warning = 'is declared final';
      }
      else
      {
        foreach ($reflector->getMethods() as $method)
        {
          if (($method->isPublic() || $method->isProtected()) && $method->isFinal())
          {
            $canExtend = false;
            $warning = 'contains final methods';
          }
        }
      }
    }
    if ($warning)
    {
      trigger_error(
        sprintf('The type [%s] to be mocked %s.' .
          ' Mocking classes which cannot be fully overridden results' .
          ' in a loss of class type. It is safe to supress this warning if you' .
          ' are aware of the conflict.',
          $reflector->getName(),
          $warning
          ),
        E_USER_WARNING
        );
    }
    return $canExtend;
  }
  
}
