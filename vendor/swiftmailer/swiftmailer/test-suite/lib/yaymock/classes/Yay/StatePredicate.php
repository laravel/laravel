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
 * An expectation about what State a state machine is in.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
interface Yay_StatePredicate extends Yay_SelfDescribing
{
  
  /**
   * Return true if the state machine is in this state.
   * @return boolean
   */
  public function isActive();
  
}
