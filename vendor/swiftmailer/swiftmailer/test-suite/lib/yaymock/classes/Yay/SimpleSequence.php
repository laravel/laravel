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
 * Provides a means for Expectations to verify they are called in the correct order.
 * This allows Invocations to be forced in a particular order.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_SimpleSequence implements Yay_Sequence
{

  /**
   * The name of this sequence.
   * @var string
   * @access private
   */
  private $_name;

  /**
   * The list of sequence IDs expected.
   * @var array
   * @access private
   */
  private $_sequenceIds = array();

  /**
   * An internal sequence counter.
   * @var int
   * @access private
   */
  private $_counter = 0;

  /**
   * The current position in the sequence.
   * @var int
   * @access private
   */
  private $_currentId = null;

  /**
   * Create a new Sequence with $name.
   * @param string $name
   */
  public function __construct($name)
  {
    $this->_name = $name;
  }

  /**
   * Ask for a new Sequence Id and register the new sequence.
   * @return integer $id
   */
  public function requestSequenceId()
  {
    $id = $this->_counter++;
    $this->_sequenceIds[] = $id;
    return $id;
  }

  /**
   * Check if the sequence has progressed far enough for this sequence ID to be used.
   * @param integer $id
   * @return boolean
   */
  public function isInSequence($sequenceId)
  {
    if ($this->_currentId === $sequenceId)
    {
      $inSequence = true;
    }
    elseif (current($this->_sequenceIds) === $sequenceId)
    {
      $this->_currentId = array_shift($this->_sequenceIds);
      $inSequence = true;
    }
    else
    {
      $inSequence = false;
    }
    return $inSequence;
  }

  /**
   * Write a description of this self describing object to Description.
   * @param Yay_Description $description
   */
  public function describeTo(Yay_Description $description)
  {
    $description->appendText(sprintf(' sequence %s;', $this->_name));
  }

}
