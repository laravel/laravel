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
 
//require 'Yay/Description.php';

/**
 * Components implementing this can describe what they do to a Description instance.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
interface Yay_SelfDescribing
{
  
  /**
   * Write a description of this self describing object to Description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description);
  
}
