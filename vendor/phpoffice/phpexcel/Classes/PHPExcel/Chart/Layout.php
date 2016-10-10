<?php
/**
 * PHPExcel
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
 * @category    PHPExcel
 * @package        PHPExcel_Chart
 * @copyright    Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license        http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version        ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Chart_Layout
 *
 * @category    PHPExcel
 * @package        PHPExcel_Chart
 * @copyright    Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Chart_Layout
{
    /**
     * layoutTarget
     *
     * @var string
     */
    private $layoutTarget;

    /**
     * X Mode
     *
     * @var string
     */
    private $xMode;

    /**
     * Y Mode
     *
     * @var string
     */
    private $yMode;

    /**
     * X-Position
     *
     * @var float
     */
    private $xPos;

    /**
     * Y-Position
     *
     * @var float
     */
    private $yPos;

    /**
     * width
     *
     * @var float
     */
    private $width;

    /**
     * height
     *
     * @var float
     */
    private $height;

    /**
     * show legend key
     * Specifies that legend keys should be shown in data labels
     *
     * @var boolean
     */
    private $showLegendKey;

    /**
     * show value
     * Specifies that the value should be shown in a data label.
     *
     * @var boolean
     */
    private $showVal;

    /**
     * show category name
     * Specifies that the category name should be shown in the data label.
     *
     * @var boolean
     */
    private $showCatName;

    /**
     * show data series name
     * Specifies that the series name should be shown in the data label.
     *
     * @var boolean
     */
    private $showSerName;

    /**
     * show percentage
     * Specifies that the percentage should be shown in the data label.
     *
     * @var boolean
     */
    private $showPercent;

    /**
     * show bubble size
     *
     * @var boolean
     */
    private $showBubbleSize;

    /**
     * show leader lines
     * Specifies that leader lines should be shown for the data label.
     *
     * @var boolean
     */
    private $showLeaderLines;


    /**
     * Create a new PHPExcel_Chart_Layout
     */
    public function __construct($layout = array())
    {
        if (isset($layout['layoutTarget'])) {
            $this->layoutTarget = $layout['layoutTarget'];
        }
        if (isset($layout['xMode'])) {
            $this->xMode = $layout['xMode'];
        }
        if (isset($layout['yMode'])) {
            $this->yMode = $layout['yMode'];
        }
        if (isset($layout['x'])) {
            $this->xPos = (float) $layout['x'];
        }
        if (isset($layout['y'])) {
            $this->yPos = (float) $layout['y'];
        }
        if (isset($layout['w'])) {
            $this->width = (float) $layout['w'];
        }
        if (isset($layout['h'])) {
            $this->height = (float) $layout['h'];
        }
    }

    /**
     * Get Layout Target
     *
     * @return string
     */
    public function getLayoutTarget()
    {
        return $this->layoutTarget;
    }

    /**
     * Set Layout Target
     *
     * @param Layout Target $value
     * @return PHPExcel_Chart_Layout
     */
    public function setLayoutTarget($value)
    {
        $this->layoutTarget = $value;
        return $this;
    }

    /**
     * Get X-Mode
     *
     * @return string
     */
    public function getXMode()
    {
        return $this->xMode;
    }

    /**
     * Set X-Mode
     *
     * @param X-Mode $value
     * @return PHPExcel_Chart_Layout
     */
    public function setXMode($value)
    {
        $this->xMode = $value;
        return $this;
    }

    /**
     * Get Y-Mode
     *
     * @return string
     */
    public function getYMode()
    {
        return $this->yMode;
    }

    /**
     * Set Y-Mode
     *
     * @param Y-Mode $value
     * @return PHPExcel_Chart_Layout
     */
    public function setYMode($value)
    {
        $this->yMode = $value;
        return $this;
    }

    /**
     * Get X-Position
     *
     * @return number
     */
    public function getXPosition()
    {
        return $this->xPos;
    }

    /**
     * Set X-Position
     *
     * @param X-Position $value
     * @return PHPExcel_Chart_Layout
     */
    public function setXPosition($value)
    {
        $this->xPos = $value;
        return $this;
    }

    /**
     * Get Y-Position
     *
     * @return number
     */
    public function getYPosition()
    {
        return $this->yPos;
    }

    /**
     * Set Y-Position
     *
     * @param Y-Position $value
     * @return PHPExcel_Chart_Layout
     */
    public function setYPosition($value)
    {
        $this->yPos = $value;
        return $this;
    }

    /**
     * Get Width
     *
     * @return number
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set Width
     *
     * @param Width $value
     * @return PHPExcel_Chart_Layout
     */
    public function setWidth($value)
    {
        $this->width = $value;
        return $this;
    }

    /**
     * Get Height
     *
     * @return number
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set Height
     *
     * @param Height $value
     * @return PHPExcel_Chart_Layout
     */
    public function setHeight($value)
    {
        $this->height = $value;
        return $this;
    }


    /**
     * Get show legend key
     *
     * @return boolean
     */
    public function getShowLegendKey()
    {
        return $this->showLegendKey;
    }

    /**
     * Set show legend key
     * Specifies that legend keys should be shown in data labels.
     *
     * @param boolean $value        Show legend key
     * @return PHPExcel_Chart_Layout
     */
    public function setShowLegendKey($value)
    {
        $this->showLegendKey = $value;
        return $this;
    }

    /**
     * Get show value
     *
     * @return boolean
     */
    public function getShowVal()
    {
        return $this->showVal;
    }

    /**
     * Set show val
     * Specifies that the value should be shown in data labels.
     *
     * @param boolean $value        Show val
     * @return PHPExcel_Chart_Layout
     */
    public function setShowVal($value)
    {
        $this->showVal = $value;
        return $this;
    }

    /**
     * Get show category name
     *
     * @return boolean
     */
    public function getShowCatName()
    {
        return $this->showCatName;
    }

    /**
     * Set show cat name
     * Specifies that the category name should be shown in data labels.
     *
     * @param boolean $value        Show cat name
     * @return PHPExcel_Chart_Layout
     */
    public function setShowCatName($value)
    {
        $this->showCatName = $value;
        return $this;
    }

    /**
     * Get show data series name
     *
     * @return boolean
     */
    public function getShowSerName()
    {
        return $this->showSerName;
    }

    /**
     * Set show ser name
     * Specifies that the series name should be shown in data labels.
     *
     * @param boolean $value        Show series name
     * @return PHPExcel_Chart_Layout
     */
    public function setShowSerName($value)
    {
        $this->showSerName = $value;
        return $this;
    }

    /**
     * Get show percentage
     *
     * @return boolean
     */
    public function getShowPercent()
    {
        return $this->showPercent;
    }

    /**
     * Set show percentage
     * Specifies that the percentage should be shown in data labels.
     *
     * @param boolean $value        Show percentage
     * @return PHPExcel_Chart_Layout
     */
    public function setShowPercent($value)
    {
        $this->showPercent = $value;
        return $this;
    }

    /**
     * Get show bubble size
     *
     * @return boolean
     */
    public function getShowBubbleSize()
    {
        return $this->showBubbleSize;
    }

    /**
     * Set show bubble size
     * Specifies that the bubble size should be shown in data labels.
     *
     * @param boolean $value        Show bubble size
     * @return PHPExcel_Chart_Layout
     */
    public function setShowBubbleSize($value)
    {
        $this->showBubbleSize = $value;
        return $this;
    }

    /**
     * Get show leader lines
     *
     * @return boolean
     */
    public function getShowLeaderLines()
    {
        return $this->showLeaderLines;
    }

    /**
     * Set show leader lines
     * Specifies that leader lines should be shown in data labels.
     *
     * @param boolean $value        Show leader lines
     * @return PHPExcel_Chart_Layout
     */
    public function setShowLeaderLines($value)
    {
        $this->showLeaderLines = $value;
        return $this;
    }
}
