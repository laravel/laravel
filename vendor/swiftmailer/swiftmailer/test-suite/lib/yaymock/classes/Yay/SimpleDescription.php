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
 * A basic Description container for error messages.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_SimpleDescription implements Yay_Description
{
  
  /**
   * An internal text buffer.
   * @var string
   * @access private
   */
  private $_text = '';
  
  /**
   * Append an existing Description to this Description.
   * @param Yay_Description
   */
  public function appendDescription(Yay_Description $description)
  {
    $this->_text .= $description->toString();
  }
  
  /**
   * Append text content to this Description.
   * @param string $text
   */
  public function appendText($text)
  {
    $this->_text .= $text;
  }
  
  /**
   * Get this description back as a formatted string.
   * @return string
   */
  public function toString()
  {
    return $this->_text;
  }
  
}
