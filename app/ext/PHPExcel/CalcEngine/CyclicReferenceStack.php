<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2014 PHPExcel
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category   PHPExcel
 * @package    PHPExcel_Calculation
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	1.8.0, 2014-03-02
 */


/**
 * PHPExcel_CalcEngine_CyclicReferenceStack
 *
 * @category	PHPExcel_CalcEngine_CyclicReferenceStack
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_CalcEngine_CyclicReferenceStack {

	/**
	 *  The call stack for calculated cells
	 *
	 *  @var mixed[]
	 */
	private $_stack = array();


	/**
	 * Return the number of entries on the stack
	 *
	 * @return  integer
	 */
	public function count() {
		return count($this->_stack);
	}

	/**
	 * Push a new entry onto the stack
	 *
	 * @param  mixed  $value
	 */
	public function push($value) {
		$this->_stack[] = $value;
	}	//	function push()

	/**
	 * Pop the last entry from the stack
	 *
	 * @return  mixed
	 */
	public function pop() {
		return array_pop($this->_stack);
	}	//	function pop()

	/**
	 * Test to see if a specified entry exists on the stack
	 *
	 * @param  mixed  $value  The value to test
	 */
	public function onStack($value) {
		return in_array($value, $this->_stack);
	}

	/**
	 * Clear the stack
	 */
	public function clear() {
		$this->_stack = array();
	}	//	function push()

	/**
	 * Return an array of all entries on the stack
	 *
	 * @return  mixed[]
	 */
	public function showStack() {
		return $this->_stack;
	}

}	//	class PHPExcel_CalcEngine_CyclicReferenceStack
