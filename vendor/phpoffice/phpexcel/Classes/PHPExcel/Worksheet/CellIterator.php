<?php

/**
 * PHPExcel_Worksheet_CellIterator
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
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
abstract class PHPExcel_Worksheet_CellIterator
{
    /**
     * PHPExcel_Worksheet to iterate
     *
     * @var PHPExcel_Worksheet
     */
    protected $subject;

    /**
     * Current iterator position
     *
     * @var mixed
     */
    protected $position = null;

    /**
     * Iterate only existing cells
     *
     * @var boolean
     */
    protected $onlyExistingCells = false;

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->subject);
    }

    /**
     * Get loop only existing cells
     *
     * @return boolean
     */
    public function getIterateOnlyExistingCells()
    {
        return $this->onlyExistingCells;
    }

    /**
     * Validate start/end values for "IterateOnlyExistingCells" mode, and adjust if necessary
     *
     * @throws PHPExcel_Exception
     */
    abstract protected function adjustForExistingOnlyRange();

    /**
     * Set the iterator to loop only existing cells
     *
     * @param    boolean        $value
     * @throws PHPExcel_Exception
     */
    public function setIterateOnlyExistingCells($value = true)
    {
        $this->onlyExistingCells = (boolean) $value;

        $this->adjustForExistingOnlyRange();
    }
}
