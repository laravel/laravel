<?php

/**
 * PHPExcel_Worksheet_Row
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
class PHPExcel_Worksheet_Row
{
    /**
     * PHPExcel_Worksheet
     *
     * @var PHPExcel_Worksheet
     */
    private $parent;

    /**
     * Row index
     *
     * @var int
     */
    private $rowIndex = 0;

    /**
     * Create a new row
     *
     * @param PHPExcel_Worksheet         $parent
     * @param int                        $rowIndex
     */
    public function __construct(PHPExcel_Worksheet $parent = null, $rowIndex = 1)
    {
        // Set parent and row index
        $this->parent   = $parent;
        $this->rowIndex = $rowIndex;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->parent);
    }

    /**
     * Get row index
     *
     * @return int
     */
    public function getRowIndex()
    {
        return $this->rowIndex;
    }

    /**
     * Get cell iterator
     *
     * @param    string                $startColumn    The column address at which to start iterating
     * @param    string                $endColumn        Optionally, the column address at which to stop iterating
     * @return PHPExcel_Worksheet_CellIterator
     */
    public function getCellIterator($startColumn = 'A', $endColumn = null)
    {
        return new PHPExcel_Worksheet_RowCellIterator($this->parent, $this->rowIndex, $startColumn, $endColumn);
    }
}
