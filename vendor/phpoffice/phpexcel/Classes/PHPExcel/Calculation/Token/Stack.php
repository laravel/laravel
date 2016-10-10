<?php

/**
 * PHPExcel_Calculation_Token_Stack
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
class PHPExcel_Calculation_Token_Stack
{
    /**
     *  The parser stack for formulae
     *
     *  @var mixed[]
     */
    private $stack = array();

    /**
     *  Count of entries in the parser stack
     *
     *  @var integer
     */
    private $count = 0;

    /**
     * Return the number of entries on the stack
     *
     * @return  integer
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * Push a new entry onto the stack
     *
     * @param  mixed  $type
     * @param  mixed  $value
     * @param  mixed  $reference
     */
    public function push($type, $value, $reference = null)
    {
        $this->stack[$this->count++] = array(
            'type'      => $type,
            'value'     => $value,
            'reference' => $reference
        );
        if ($type == 'Function') {
            $localeFunction = PHPExcel_Calculation::localeFunc($value);
            if ($localeFunction != $value) {
                $this->stack[($this->count - 1)]['localeValue'] = $localeFunction;
            }
        }
    }

    /**
     * Pop the last entry from the stack
     *
     * @return  mixed
     */
    public function pop()
    {
        if ($this->count > 0) {
            return $this->stack[--$this->count];
        }
        return null;
    }

    /**
     * Return an entry from the stack without removing it
     *
     * @param   integer  $n  number indicating how far back in the stack we want to look
     * @return  mixed
     */
    public function last($n = 1)
    {
        if ($this->count - $n < 0) {
            return null;
        }
        return $this->stack[$this->count - $n];
    }

    /**
     * Clear the stack
     */
    public function clear()
    {
        $this->stack = array();
        $this->count = 0;
    }
}
