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

//require 'Yay/Expectations/AbstractExpectation.php';
//require 'Yay/Invocation.php';

/**
 * An Expectation which allows a boundary for a number of matching Invocations.
 * @author Chris Corbyn <chris@w3style.co.uk>
 * @package Yay
 */
class Yay_Expectations_BetweenExpectation
  extends Yay_Expectations_AbstractExpectation
{

  /**
   * The minimum Invocation count.
   * @var int
   * @access private
   */
  private $_min = 0;

  /**
   * The maximum Invocation count.
   * @var int
   * @access private
   */
  private $_max = 0;

  /**
   * The number of matched Invocations.
   * @var int
   * @access private
   */
  private $_matched = 0;

  /**
   * Create a new BetweenExpectation expecting between $min and $max Invocations.
   * @param integer $n
   */
  public function __construct($min, $max)
  {
    $this->_min = $min;
    $this->_max = $max;
  }

  /**
   * Test if this Invocation is one that was expected by this Expectation.
   * @param Yay_Invocation $invocation
   * @return boolean
   */
  public function isExpected(Yay_Invocation $invocation)
  {
    return parent::isExpected($invocation) && ($this->_matched <= $this->_max);
  }

  /**
   * Test if all conditions of the Invocation are satisfied.
   * @return boolean
   */
  public function isSatisfied()
  {
    return ($this->_matched >= $this->_min);
  }

  /**
   * Increment the match counter by 1.
   * @param Yay_Invocation $invocation
   */
  public function notifyMatchedInvocation(Yay_Invocation $invocation)
  {
    $this->_matched++;
  }

  /**
   * Describe the boundaries of how many invocations can occur.
   * @param Yay_Description $description
   */
  public function describeBounds(Yay_Description $description)
  {
    $description->appendText(sprintf('Between %d and %d', $this->_min, $this->_max));
  }

  /**
   * Describe the current status of this expectation.
   * @param Yay_Description $description
   */
  public function describeSatisfaction(Yay_Description $description)
  {
    if ($this->_matched >= $this->_min)
    {
      $description->appendText(' already');
    }
    $description->appendText(
      sprintf(
        ' occurred %d times',
        (($this->_matched < $this->_max)
          ? $this->_matched
          : $this->_max)
      ));
  }

}
