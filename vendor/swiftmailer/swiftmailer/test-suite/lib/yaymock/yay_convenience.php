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
 * Provides non-namespaced classes and functions for brevity.
 * Including this script is entirely optional.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */

// Classes

/**
 * Allow occurences of Yay_Expectations::create() to be replaced with Expectations::create().
 */
class Expectations extends Yay_Expectations { }

/**
 * Allows occurences of new Yay_Mockery() to be replaced with new Mockery().
 */
class Mockery extends Yay_Mockery { }

//Argument matchers

/**
 * Allows Yay::optional() to be called as optional().
 */
function optional($value = null)
{
  return Yay::optional($value);
}

/**
 * Allows Yay::any() to be called as any().
 */
function any($type = null)
{
  return Yay::any($type);
}

/**
 * Allows Yay::none() to be called as none().
 */
function none($type = null)
{
  return Yay::none($type);
}

/**
 * Allows Yay::identical() to be called as identical().
 */
function identical($value)
{
  return Yay::identical($value);
}

/**
 * Allows Yay::notIdentical() to be called as notIdentical().
 */
function notIdentical($value)
{
  return Yay::notIdentical($value);
}

/**
 * Allows Yay::equal() to be called as equal().
 */
function equal($value)
{
  return Yay::equal($value);
}

/**
 * Allows Yay::notEqual() to be called as notEqual().
 */
function notEqual($value)
{
  return Yay::notEqual($value);
}

/**
 * Allows Yay::pattern() to be called as pattern().
 */
function pattern($pattern)
{
  return Yay::pattern($pattern);
}

/**
 * Allows Yay::noPattern() to be called as noPattern().
 */
function noPattern($pattern)
{
  return Yay::noPattern($pattern);
}

/**
 * Allows Yay::bounds() to be called as bounds().
 */
function bounds($a, $b)
{
  return Yay::bounds($a, $b);
}

/**
 * Allows Yay::outside() to be called as outside().
 */
function outside($a, $b)
{
  return Yay::outside($a, $b);
}

/**
 * Allows Yay::reference() to be called as reference().
 */
function reference(&$ref)
{
  return Yay::reference($ref);
}

/**
 * Allows Yay::noReference() to be called as noReference().
 */
function noReference(&$ref)
{
  return Yay::noReference($ref);
}

//Actions

/**
 * Allows Yay::returnValue() to be called as returnValue().
 */
function returnValue($value)
{
  return Yay::returnValue($value);
}

/**
 * Allows Yay::returnReference() to be called as returnReference().
 */
function returnReference(&$ref)
{
  return Yay::returnReference($ref);
}

/**
 * Allows Yay::throwException() to be called as throwException().
 */
function throwException(Exception $e)
{
  return Yay::throwException($e);
}

/**
 * Allows Yay::call() to be called as call().
 */
function call($callback)
{
  return Yay::call($callback);
}
