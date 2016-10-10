<?php

/**
 * PHPExcel_CalcEngine_CyclicReferenceStack
 *
 * Copyright (c) 2006 - 2015 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_CalcEngine_CyclicReferenceStack
{
    /**
     *  The call stack for calculated cells
     *
     *  @var mixed[]
     */
    private $stack = array();

    /**
     * Return the number of entries on the stack
     *
     * @return  integer
     */
    public function count()
    {
        return count($this->stack);
    }

    /**
     * Push a new entry onto the stack
     *
     * @param  mixed  $value
     */
    public function push($value)
    {
        $this->stack[$value] = $value;
    }

    /**
     * Pop the last entry from the stack
     *
     * @return  mixed
     */
    public function pop()
    {
        return array_pop($this->stack);
    }

    /**
     * Test to see if a specified entry exists on the stack
     *
     * @param  mixed  $value  The value to test
     */
    public function onStack($value)
    {
        return isset($this->stack[$value]);
    }

    /**
     * Clear the stack
     */
    public function clear()
    {
        $this->stack = array();
    }

    /**
     * Return an array of all entries on the stack
     *
     * @return  mixed[]
     */
    public function showStack()
    {
        return $this->stack;
    }
}
