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
 
//require 'Yay/Expectations.php';
//require 'Yay/Matchers/OptionalMatcher.php';
//require 'Yay/Matchers/AnyMatcher.php';
//require 'Yay/Matchers/IdenticalMatcher.php';
//require 'Yay/Matchers/EqualMatcher.php';
//require 'Yay/Matchers/PatternMatcher.php';
//require 'Yay/Matchers/ReferenceMatcher.php';
//require 'Yay/Matchers/BoundsMatcher.php';
//require 'Yay/Actions/ReturnValueAction.php';
//require 'Yay/Actions/ReturnReferenceAction.php';
//require 'Yay/Actions/ThrowAction.php';
//require 'Yay/Actions/CallbackAction.php';

/**
 * A convenience factory class.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay
{
  
  /**
   * The classpath used for autoloading.
   * @var string
   * @access private
   */
  private static $CLASSPATH = '.';
  
  // -- Expectations
  
  /**
   * Create a new Expectations builder instance.
   * @return Yay_Expectations
   */
  public static function expectations()
  {
    return new Yay_Expectations();
  }
  
  // -- Matchers
  
  /**
   * Create a new Optional matcher, optionally wrapping $value.
   * @param string $value, optional
   * @return Yay_Matchers_OptionalMatcher
   */
  public static function optional($value = null)
  {
    return new Yay_Matchers_OptionalMatcher($value);
  }
  
  /**
   * Create a new Any matcher, optionally constrained to $type.
   * @param string $type, optional
   * @return Yay_Matchers_AnyMatcher
   */
  public static function any($type = null)
  {
    return new Yay_Matchers_AnyMatcher($type, true);
  }
  
  /**
   * Create a negated Any matcher, optionally constrained to $type.
   * @param string $type, optional
   * @return Yay_Matchers_AnyMatcher
   */
  public static function none($type = null)
  {
    return new Yay_Matchers_AnyMatcher($type, false);
  }
  
  /**
   * Create a new Identical matcher for $value.
   * @param mixed $value
   * @return Yay_Matchers_IdenticalMatcher
   */
  public static function identical($value)
  {
    return new Yay_Matchers_IdenticalMatcher($value, true);
  }
  
  /**
   * Create a negated Identical matcher for $value.
   * @param mixed $value
   * @return Yay_Matchers_IdenticalMatcher
   */
  public static function notIdentical($value)
  {
    return new Yay_Matchers_IdenticalMatcher($value, false);
  }
  
  /**
   * Create a new Equal matcher for $value.
   * @param mixed $value
   * @return Yay_Matchers_EqualMatcher
   */
  public static function equal($value)
  {
    return new Yay_Matchers_EqualMatcher($value, true);
  }
  
  /**
   * Create a negated Equal matcher for $value.
   * @param mixed $value
   * @return Yay_Matchers_EqualMatcher
   */
  public static function notEqual($value)
  {
    return new Yay_Matchers_EqualMatcher($value, false);
  }
  
  /**
   * Create a new Pattern matcher for $pattern.
   * @param string $pattern
   * @return Yay_Matchers_IsAMatcher
   */
  public static function pattern($pattern)
  {
    return new Yay_Matchers_PatternMatcher($pattern, true);
  }
  
  /**
   * Create a negated Pattern matcher for $pattern.
   * @param string $pattern
   * @return Yay_Matchers_IsAMatcher
   */
  public static function noPattern($pattern)
  {
    return new Yay_Matchers_PatternMatcher($pattern, false);
  }
  
  /**
   * Create a new Reference matcher for $ref.
   * @param mixed $ref
   * @return Yay_Matchers_ReferenceMatcher
   */
  public static function reference(&$ref)
  {
    return new Yay_Matchers_ReferenceMatcher($ref, true);
  }
  
  /**
   * Create a negated Reference matcher for $ref.
   * @param mixed $ref
   * @return Yay_Matchers_ReferenceMatcher
   */
  public static function noReference(&$ref)
  {
    return new Yay_Matchers_ReferenceMatcher($ref, false);
  }
  
  /**
   * Create a new Bounds matcher for boundaries between $lower and $upper.
   * @param mixed $lower
   * @param mixed $upper
   * @return Yay_Matchers_BoundsMatcher
   */
  public static function bounds($lower, $upper)
  {
    return new Yay_Matchers_BoundsMatcher($lower, $upper, true);
  }
  
  /**
   * Create a negated Bounds matcher for boundaries outside $lower and $upper.
   * @param mixed $lower
   * @param mixed $upper
   * @return Yay_Matchers_BoundsMatcher
   */
  public static function outside($lower, $upper)
  {
    return new Yay_Matchers_BoundsMatcher($lower, $upper, false);
  }
  
  // -- Actions
  
  /**
   * Create a new ReturnValueAction with $value.
   * @param mixed $value
   * @return Yay_Actions_ReturnValueAction
   */
  public static function returnValue($value)
  {
    return new Yay_Actions_ReturnValueAction($value);
  }
  
  /**
   * Create a new ReturnReferenceAction with &$ref.
   * @param mixed $ref
   * @return Yay_Actions_ReturnReferenceAction
   */
  public static function returnReference(&$ref)
  {
    return new Yay_Actions_ReturnReferenceAction($ref);
  }
  
  /**
   * Create a new ThrowAction with $e.
   * @param Exception $ref
   * @return Yay_Actions_ThrowAction
   */
  public static function throwException(Exception $e)
  {
    return new Yay_Actions_ThrowAction($e);
  }
  
  /**
   * Create a new CallbackAction with $callback.
   * @param callback $callback
   * @return Yay_Actions_CallbackAction
   */
  public static function call($callback)
  {
    return new Yay_Actions_CallbackAction($callback);
  }

  /**
   * Set the classpath for autoloading.
   * @param string $path
   */
  public static function setClassPath($path)
  {
    self::$CLASSPATH = $path;
  }

  /**
   * Static autoloader registered in bootstrap file.
   * @param string $class
   */
  public static function autoload($class)
  {
    if (substr($class, 0, 3) != 'Yay')
    {
      return;
    }
    $file = str_replace('_', '/', $class) . '.php';
    $path = self::$CLASSPATH . '/' . $file;
    if (is_file($path))
    {
      require_once $path;
    }
  }
  
}
