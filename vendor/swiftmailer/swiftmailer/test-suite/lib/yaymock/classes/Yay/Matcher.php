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
 * The Matcher interface for comparing arguments.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
interface Yay_Matcher
{
  
  /**
   * Compare the $argument with whatever is expected to match it.
   * @param mixed $argument
   * @return boolean
   */
  public function matches(&$argument);
  
  /**
   * Returns true if the argument doesn't need to be present.
   * @return boolean
   */
  public function isOptional();
  
  /**
   * Writes the match description as a string following $format.
   * $format is a sprintf() string with %s, $s as $matcherName, $value respectively.
   * @param string $format
   * @return string
   */
  public function describeMatch($format);
  
}
