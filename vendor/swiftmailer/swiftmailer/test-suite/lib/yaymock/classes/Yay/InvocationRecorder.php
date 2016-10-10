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
//require 'Yay/ExpectationProvider.php';

/**
 * Listens for Invocations and provides expectations based on them.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
interface Yay_InvocationRecorder extends Yay_ExpectationProvider
{
  
  /**
   * Record the given $invocation.
   * @param Yay_Invocation $invocation
   */
  public function recordInvocation(Yay_Invocation $invocation);
  
}