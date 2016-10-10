<?php

/**
 * PHPExcel_Worksheet_Dimension
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
abstract class PHPExcel_Worksheet_Dimension
{
    /**
     * Visible?
     *
     * @var bool
     */
    private $visible = true;

    /**
     * Outline level
     *
     * @var int
     */
    private $outlineLevel = 0;

    /**
     * Collapsed
     *
     * @var bool
     */
    private $collapsed = false;

    /**
     * Index to cellXf. Null value means row has no explicit cellXf format.
     *
     * @var int|null
     */
    private $xfIndex;

    /**
     * Create a new PHPExcel_Worksheet_Dimension
     *
     * @param int $pIndex Numeric row index
     */
    public function __construct($initialValue = null)
    {
        // set dimension as unformatted by default
        $this->xfIndex = $initialValue;
    }

    /**
     * Get Visible
     *
     * @return bool
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set Visible
     *
     * @param bool $pValue
     * @return PHPExcel_Worksheet_Dimension
     */
    public function setVisible($pValue = true)
    {
        $this->visible = $pValue;
        return $this;
    }

    /**
     * Get Outline Level
     *
     * @return int
     */
    public function getOutlineLevel()
    {
        return $this->outlineLevel;
    }

    /**
     * Set Outline Level
     *
     * Value must be between 0 and 7
     *
     * @param int $pValue
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet_Dimension
     */
    public function setOutlineLevel($pValue)
    {
        if ($pValue < 0 || $pValue > 7) {
            throw new PHPExcel_Exception("Outline level must range between 0 and 7.");
        }

        $this->outlineLevel = $pValue;
        return $this;
    }

    /**
     * Get Collapsed
     *
     * @return bool
     */
    public function getCollapsed()
    {
        return $this->collapsed;
    }

    /**
     * Set Collapsed
     *
     * @param bool $pValue
     * @return PHPExcel_Worksheet_Dimension
     */
    public function setCollapsed($pValue = true)
    {
        $this->collapsed = $pValue;
        return $this;
    }

    /**
     * Get index to cellXf
     *
     * @return int
     */
    public function getXfIndex()
    {
        return $this->xfIndex;
    }

    /**
     * Set index to cellXf
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_Dimension
     */
    public function setXfIndex($pValue = 0)
    {
        $this->xfIndex = $pValue;
        return $this;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if (is_object($value)) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
    }
}
