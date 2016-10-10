<?php

/**
 * PHPExcel_Worksheet_HeaderFooterDrawing
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
class PHPExcel_Worksheet_HeaderFooterDrawing extends PHPExcel_Worksheet_Drawing implements PHPExcel_IComparable
{
    /**
     * Path
     *
     * @var string
     */
    private $path;

    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Offset X
     *
     * @var int
     */
    protected $offsetX;

    /**
     * Offset Y
     *
     * @var int
     */
    protected $offsetY;

    /**
     * Width
     *
     * @var int
     */
    protected $width;

    /**
     * Height
     *
     * @var int
     */
    protected $height;

    /**
     * Proportional resize
     *
     * @var boolean
     */
    protected $resizeProportional;

    /**
     * Create a new PHPExcel_Worksheet_HeaderFooterDrawing
     */
    public function __construct()
    {
        // Initialise values
        $this->path                = '';
        $this->name                = '';
        $this->offsetX             = 0;
        $this->offsetY             = 0;
        $this->width               = 0;
        $this->height              = 0;
        $this->resizeProportional  = true;
    }

    /**
     * Get Name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set Name
     *
     * @param string $pValue
     * @return PHPExcel_Worksheet_HeaderFooterDrawing
     */
    public function setName($pValue = '')
    {
        $this->name = $pValue;
        return $this;
    }

    /**
     * Get OffsetX
     *
     * @return int
     */
    public function getOffsetX()
    {
        return $this->offsetX;
    }

    /**
     * Set OffsetX
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_HeaderFooterDrawing
     */
    public function setOffsetX($pValue = 0)
    {
        $this->offsetX = $pValue;
        return $this;
    }

    /**
     * Get OffsetY
     *
     * @return int
     */
    public function getOffsetY()
    {
        return $this->offsetY;
    }

    /**
     * Set OffsetY
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_HeaderFooterDrawing
     */
    public function setOffsetY($pValue = 0)
    {
        $this->offsetY = $pValue;
        return $this;
    }

    /**
     * Get Width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set Width
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_HeaderFooterDrawing
     */
    public function setWidth($pValue = 0)
    {
        // Resize proportional?
        if ($this->resizeProportional && $pValue != 0) {
            $ratio = $this->width / $this->height;
            $this->height = round($ratio * $pValue);
        }

        // Set width
        $this->width = $pValue;

        return $this;
    }

    /**
     * Get Height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set Height
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_HeaderFooterDrawing
     */
    public function setHeight($pValue = 0)
    {
        // Resize proportional?
        if ($this->resizeProportional && $pValue != 0) {
            $ratio = $this->width / $this->height;
            $this->width = round($ratio * $pValue);
        }

        // Set height
        $this->height = $pValue;

        return $this;
    }

    /**
     * Set width and height with proportional resize
     * Example:
     * <code>
     * $objDrawing->setResizeProportional(true);
     * $objDrawing->setWidthAndHeight(160,120);
     * </code>
     *
     * @author Vincent@luo MSN:kele_100@hotmail.com
     * @param int $width
     * @param int $height
     * @return PHPExcel_Worksheet_HeaderFooterDrawing
     */
    public function setWidthAndHeight($width = 0, $height = 0)
    {
        $xratio = $width / $this->width;
        $yratio = $height / $this->height;
        if ($this->resizeProportional && !($width == 0 || $height == 0)) {
            if (($xratio * $this->height) < $height) {
                $this->height = ceil($xratio * $this->height);
                $this->width  = $width;
            } else {
                $this->width    = ceil($yratio * $this->width);
                $this->height    = $height;
            }
        }
        return $this;
    }

    /**
     * Get ResizeProportional
     *
     * @return boolean
     */
    public function getResizeProportional()
    {
        return $this->resizeProportional;
    }

    /**
     * Set ResizeProportional
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_HeaderFooterDrawing
     */
    public function setResizeProportional($pValue = true)
    {
        $this->resizeProportional = $pValue;
        return $this;
    }

    /**
     * Get Filename
     *
     * @return string
     */
    public function getFilename()
    {
        return basename($this->path);
    }

    /**
     * Get Extension
     *
     * @return string
     */
    public function getExtension()
    {
        $parts = explode(".", basename($this->path));
        return end($parts);
    }

    /**
     * Get Path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set Path
     *
     * @param     string         $pValue            File path
     * @param     boolean        $pVerifyFile    Verify file
     * @throws     PHPExcel_Exception
     * @return PHPExcel_Worksheet_HeaderFooterDrawing
     */
    public function setPath($pValue = '', $pVerifyFile = true)
    {
        if ($pVerifyFile) {
            if (file_exists($pValue)) {
                $this->path = $pValue;

                if ($this->width == 0 && $this->height == 0) {
                    // Get width/height
                    list($this->width, $this->height) = getimagesize($pValue);
                }
            } else {
                throw new PHPExcel_Exception("File $pValue not found!");
            }
        } else {
            $this->path = $pValue;
        }
        return $this;
    }

    /**
     * Get hash code
     *
     * @return string    Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->path .
            $this->name .
            $this->offsetX .
            $this->offsetY .
            $this->width .
            $this->height .
            __CLASS__
        );
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
