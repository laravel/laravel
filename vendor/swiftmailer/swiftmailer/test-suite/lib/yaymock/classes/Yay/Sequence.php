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
 * Provides a means for Expectations to verify they are called in the correct order.
 * This allows Invocations to be forced in a particular order.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
interface Yay_Sequence extends Yay_SelfDescribing
{

  /**
   * Ask for a new Sequence Id and register the new sequence.
   * @return integer $id
   */
  public function requestSequenceId();

  /**
   * Check if the sequence has progressed far enough for this sequence ID to be used.
   * @param integer $id
   * @return boolean
   */
  public function isInSequence($sequenceId);

}
