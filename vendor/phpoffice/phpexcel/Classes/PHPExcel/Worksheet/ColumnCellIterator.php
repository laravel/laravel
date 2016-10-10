<?php

/**
 * PHPExcel_Worksheet_ColumnCellIterator
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
class PHPExcel_Worksheet_ColumnCellIterator extends PHPExcel_Worksheet_CellIterator implements Iterator
{
    /**
     * Column index
     *
     * @var string
     */
    protected $columnIndex;

    /**
     * Start position
     *
     * @var int
     */
    protected $startRow = 1;

    /**
     * End position
     *
     * @var int
     */
    protected $endRow = 1;

    /**
     * Create a new row iterator
     *
     * @param    PHPExcel_Worksheet    $subject        The worksheet to iterate over
     * @param   string              $columnIndex    The column that we want to iterate
     * @param    integer                $startRow        The row number at which to start iterating
     * @param    integer                $endRow            Optionally, the row number at which to stop iterating
     */
    public function __construct(PHPExcel_Worksheet $subject = null, $columnIndex = 'A', $startRow = 1, $endRow = null)
    {
        // Set subject
        $this->subject = $subject;
        $this->columnIndex = PHPExcel_Cell::columnIndexFromString($columnIndex) - 1;
        $this->resetEnd($endRow);
        $this->resetStart($startRow);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->subject);
    }

    /**
     * (Re)Set the start row and the current row pointer
     *
     * @param integer    $startRow    The row number at which to start iterating
     * @return PHPExcel_Worksheet_ColumnCellIterator
     * @throws PHPExcel_Exception
     */
    public function resetStart($startRow = 1)
    {
        $this->startRow = $startRow;
        $this->adjustForExistingOnlyRange();
        $this->seek($startRow);

        return $this;
    }

    /**
     * (Re)Set the end row
     *
     * @param integer    $endRow    The row number at which to stop iterating
     * @return PHPExcel_Worksheet_ColumnCellIterator
     * @throws PHPExcel_Exception
     */
    public function resetEnd($endRow = null)
    {
        $this->endRow = ($endRow) ? $endRow : $this->subject->getHighestRow();
        $this->adjustForExistingOnlyRange();

        return $this;
    }

    /**
     * Set the row pointer to the selected row
     *
     * @param integer    $row    The row number to set the current pointer at
     * @return PHPExcel_Worksheet_ColumnCellIterator
     * @throws PHPExcel_Exception
     */
    public function seek($row = 1)
    {
        if (($row < $this->startRow) || ($row > $this->endRow)) {
            throw new PHPExcel_Exception("Row $row is out of range ({$this->startRow} - {$this->endRow})");
        } elseif ($this->onlyExistingCells && !($this->subject->cellExistsByColumnAndRow($this->columnIndex, $row))) {
            throw new PHPExcel_Exception('In "IterateOnlyExistingCells" mode and Cell does not exist');
        }
        $this->position = $row;

        return $this;
    }

    /**
     * Rewind the iterator to the starting row
     */
    public function rewind()
    {
        $this->position = $this->startRow;
    }

    /**
     * Return the current cell in this worksheet column
     *
     * @return PHPExcel_Worksheet_Row
     */
    public function current()
    {
        return $this->subject->getCellByColumnAndRow($this->columnIndex, $this->position);
    }

    /**
     * Return the current iterator key
     *
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * Set the iterator to its next value
     */
    public function next()
    {
        do {
            ++$this->position;
        } while (($this->onlyExistingCells) &&
            (!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->position)) &&
            ($this->position <= $this->endRow));
    }

    /**
     * Set the iterator to its previous value
     */
    public function prev()
    {
        if ($this->position <= $this->startRow) {
            throw new PHPExcel_Exception("Row is already at the beginning of range ({$this->startRow} - {$this->endRow})");
        }

        do {
            --$this->position;
        } while (($this->onlyExistingCells) &&
            (!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->position)) &&
            ($this->position >= $this->startRow));
    }

    /**
     * Indicate if more rows exist in the worksheet range of rows that we're iterating
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->position <= $this->endRow;
    }

    /**
     * Validate start/end values for "IterateOnlyExistingCells" mode, and adjust if necessary
     *
     * @throws PHPExcel_Exception
     */
    protected function adjustForExistingOnlyRange()
    {
        if ($this->onlyExistingCells) {
            while ((!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->startRow)) &&
                ($this->startRow <= $this->endRow)) {
                ++$this->startRow;
            }
            if ($this->startRow > $this->endRow) {
                throw new PHPExcel_Exception('No cells exist within the specified range');
            }
            while ((!$this->subject->cellExistsByColumnAndRow($this->columnIndex, $this->endRow)) &&
                ($this->endRow >= $this->startRow)) {
                --$this->endRow;
            }
            if ($this->endRow < $this->startRow) {
                throw new PHPExcel_Exception('No cells exist within the specified range');
            }
        }
    }
}
