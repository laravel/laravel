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
 
//require 'Yay/Matcher.php';
//require 'Yay/Matchers/IdenticalMatcher.php';

/**
 * Wraps Matchers and makes them Optional.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Matchers_OptionalMatcher implements Yay_Matcher
{
  
  /**
   * A matcher to delegate to.
   * @var Yay_Matcher
   * @access private
   */
  private $_matcher;
  
  /**
   * Create a new OptionalMatcher, optionally wrapping $value.
   * @param mixed $value, optional
   */
  public function __construct($value = null)
  {
    if (isset($value))
    {
      if ($value instanceof Yay_Matcher)
      {
        $this->_matcher = $value;
      }
      else
      {
        $this->_matcher = new Yay_Matchers_IdenticalMatcher($value);
      }
    }
  }
  
  /**
   * Returns true if no matcher set, otherwise it delegates to the given Matcher.
   * @param mixed $value
   * @return boolean
   */
  public function matches(&$value)
  {
    if (isset($this->_matcher))
    {
      $matches = $this->_matcher->matches($value);
    }
    else
    {
      $matches = true;
    }
    return $matches;
  }
  
  /**
   * Returns true if the argument doesn't need to be present.
   * @return boolean
   */
  public function isOptional()
  {
    return true;
  }
  
  /**
   * Writes the match description as a string following $format.
   * $format is a sprintf() string with %s, $s as $matcherName, $value respectively.
   * @param string $format
   * @return string
   */
  public function describeMatch($format)
  {
    $name = 'optional';
    if (isset($this->_matcher))
    {
      $value = $this->_matcher->describeMatch($format);
    }
    else
    {
      $value = '*';
    }
    return sprintf($format, $name, $value);
  }
  
}