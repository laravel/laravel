<?php

/**
 * PHPExcel_Worksheet_ColumnDimension
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
class PHPExcel_Worksheet_ColumnDimension extends PHPExcel_Worksheet_Dimension
{
    /**
     * Column index
     *
     * @var int
     */
    private $columnIndex;

    /**
     * Column width
     *
     * When this is set to a negative value, the column width should be ignored by IWriter
     *
     * @var double
     */
    private $width = -1;

    /**
     * Auto size?
     *
     * @var bool
     */
    private $autoSize = false;

    /**
     * Create a new PHPExcel_Worksheet_ColumnDimension
     *
     * @param string $pIndex Character column index
     */
    public function __construct($pIndex = 'A')
    {
        // Initialise values
        $this->columnIndex = $pIndex;

        // set dimension as unformatted by default
        parent::__construct(0);
    }

    /**
     * Get ColumnIndex
     *
     * @return string
     */
    public function getColumnIndex()
    {
        return $this->columnIndex;
    }

    /**
     * Set ColumnIndex
     *
     * @param string $pValue
     * @return PHPExcel_Worksheet_ColumnDimension
     */
    public function setColumnIndex($pValue)
    {
        $this->columnIndex = $pValue;
        return $this;
    }

    /**
     * Get Width
     *
     * @return double
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set Width
     *
     * @param double $pValue
     * @return PHPExcel_Worksheet_ColumnDimension
     */
    public function setWidth($pValue = -1)
    {
        $this->width = $pValue;
        return $this;
    }

    /**
     * Get Auto Size
     *
     * @return bool
     */
    public function getAutoSize()
    {
        return $this->autoSize;
    }

    /**
     * Set Auto Size
     *
     * @param bool $pValue
     * @return PHPExcel_Worksheet_ColumnDimension
     */
    public function setAutoSize($pValue = false)
    {
        $this->autoSize = $pValue;
        return $this;
    }
}
