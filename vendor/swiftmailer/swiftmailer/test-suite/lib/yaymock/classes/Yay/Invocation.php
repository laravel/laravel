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
 
//require 'Yay/SelfDescribing.php';

/**
 * A representation of a Method invocation.
 * This is a container for the object the method was invoked on, the method-
 * name and the arguments in the invocation.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
interface Yay_Invocation extends Yay_SelfDescribing
{
  
  /**
   * Get the object which this Invocation occured on.
   * @return object
   */
  public function getObject();
  
  /**
   * Get the method name of the invoked method.
   * @return string
   */
  public function getMethod();
  
  /**
   * Get the argument list in the Invocation.
   * @return array
   */
  public function &getArguments();
  
}
