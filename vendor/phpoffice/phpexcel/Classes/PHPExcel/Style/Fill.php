<?php

/**
 * PHPExcel_Style_Fill
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
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Style_Fill extends PHPExcel_Style_Supervisor implements PHPExcel_IComparable
{
    /* Fill types */
    const FILL_NONE                    = 'none';
    const FILL_SOLID                   = 'solid';
    const FILL_GRADIENT_LINEAR         = 'linear';
    const FILL_GRADIENT_PATH           = 'path';
    const FILL_PATTERN_DARKDOWN        = 'darkDown';
    const FILL_PATTERN_DARKGRAY        = 'darkGray';
    const FILL_PATTERN_DARKGRID        = 'darkGrid';
    const FILL_PATTERN_DARKHORIZONTAL  = 'darkHorizontal';
    const FILL_PATTERN_DARKTRELLIS     = 'darkTrellis';
    const FILL_PATTERN_DARKUP          = 'darkUp';
    const FILL_PATTERN_DARKVERTICAL    = 'darkVertical';
    const FILL_PATTERN_GRAY0625        = 'gray0625';
    const FILL_PATTERN_GRAY125         = 'gray125';
    const FILL_PATTERN_LIGHTDOWN       = 'lightDown';
    const FILL_PATTERN_LIGHTGRAY       = 'lightGray';
    const FILL_PATTERN_LIGHTGRID       = 'lightGrid';
    const FILL_PATTERN_LIGHTHORIZONTAL = 'lightHorizontal';
    const FILL_PATTERN_LIGHTTRELLIS    = 'lightTrellis';
    const FILL_PATTERN_LIGHTUP         = 'lightUp';
    const FILL_PATTERN_LIGHTVERTICAL   = 'lightVertical';
    const FILL_PATTERN_MEDIUMGRAY      = 'mediumGray';

    /**
     * Fill type
     *
     * @var string
     */
    protected $fillType = PHPExcel_Style_Fill::FILL_NONE;

    /**
     * Rotation
     *
     * @var double
     */
    protected $rotation = 0;

    /**
     * Start color
     *
     * @var PHPExcel_Style_Color
     */
    protected $startColor;

    /**
     * End color
     *
     * @var PHPExcel_Style_Color
     */
    protected $endColor;

    /**
     * Create a new PHPExcel_Style_Fill
     *
     * @param    boolean    $isSupervisor    Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     * @param    boolean    $isConditional    Flag indicating if this is a conditional style or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct($isSupervisor = false, $isConditional = false)
    {
        // Supervisor?
        parent::__construct($isSupervisor);

        // Initialise values
        if ($isConditional) {
            $this->fillType = null;
        }
        $this->startColor = new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_WHITE, $isSupervisor, $isConditional);
        $this->endColor = new PHPExcel_Style_Color(PHPExcel_Style_Color::COLOR_BLACK, $isSupervisor, $isConditional);

        // bind parent if we are a supervisor
        if ($isSupervisor) {
            $this->startColor->bindParent($this, 'startColor');
            $this->endColor->bindParent($this, 'endColor');
        }
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor
     *
     * @return PHPExcel_Style_Fill
     */
    public function getSharedComponent()
    {
        return $this->parent->getSharedComponent()->getFill();
    }

    /**
     * Build style array from subcomponents
     *
     * @param array $array
     * @return array
     */
    public function getStyleArray($array)
    {
        return array('fill' => $array);
    }

    /**
     * Apply styles from array
     *
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getFill()->applyFromArray(
     *        array(
     *            'type'       => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
     *            'rotation'   => 0,
     *            'startcolor' => array(
     *                'rgb' => '000000'
     *            ),
     *            'endcolor'   => array(
     *                'argb' => 'FFFFFFFF'
     *            )
     *        )
     * );
     * </code>
     *
     * @param    array    $pStyles    Array containing style information
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Style_Fill
     */
    public function applyFromArray($pStyles = null)
    {
        if (is_array($pStyles)) {
            if ($this->isSupervisor) {
                $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
            } else {
                if (array_key_exists('type', $pStyles)) {
                    $this->setFillType($pStyles['type']);
                }
                if (array_key_exists('rotation', $pStyles)) {
                    $this->setRotation($pStyles['rotation']);
                }
                if (array_key_exists('startcolor', $pStyles)) {
                    $this->getStartColor()->applyFromArray($pStyles['startcolor']);
                }
                if (array_key_exists('endcolor', $pStyles)) {
                    $this->getEndColor()->applyFromArray($pStyles['endcolor']);
                }
                if (array_key_exists('color', $pStyles)) {
                    $this->getStartColor()->applyFromArray($pStyles['color']);
                }
            }
        } else {
            throw new PHPExcel_Exception("Invalid style array passed.");
        }
        return $this;
    }

    /**
     * Get Fill Type
     *
     * @return string
     */
    public function getFillType()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getFillType();
        }
        return $this->fillType;
    }

    /**
     * Set Fill Type
     *
     * @param string $pValue    PHPExcel_Style_Fill fill type
     * @return PHPExcel_Style_Fill
     */
    public function setFillType($pValue = PHPExcel_Style_Fill::FILL_NONE)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(array('type' => $pValue));
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->fillType = $pValue;
        }
        return $this;
    }

    /**
     * Get Rotation
     *
     * @return double
     */
    public function getRotation()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getRotation();
        }
        return $this->rotation;
    }

    /**
     * Set Rotation
     *
     * @param double $pValue
     * @return PHPExcel_Style_Fill
     */
    public function setRotation($pValue = 0)
    {
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(array('rotation' => $pValue));
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->rotation = $pValue;
        }
        return $this;
    }

    /**
     * Get Start Color
     *
     * @return PHPExcel_Style_Color
     */
    public function getStartColor()
    {
        return $this->startColor;
    }

    /**
     * Set Start Color
     *
     * @param    PHPExcel_Style_Color $pValue
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Style_Fill
     */
    public function setStartColor(PHPExcel_Style_Color $pValue = null)
    {
        // make sure parameter is a real color and not a supervisor
        $color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

        if ($this->isSupervisor) {
            $styleArray = $this->getStartColor()->getStyleArray(array('argb' => $color->getARGB()));
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->startColor = $color;
        }
        return $this;
    }

    /**
     * Get End Color
     *
     * @return PHPExcel_Style_Color
     */
    public function getEndColor()
    {
        return $this->endColor;
    }

    /**
     * Set End Color
     *
     * @param    PHPExcel_Style_Color $pValue
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Style_Fill
     */
    public function setEndColor(PHPExcel_Style_Color $pValue = null)
    {
        // make sure parameter is a real color and not a supervisor
        $color = $pValue->getIsSupervisor() ? $pValue->getSharedComponent() : $pValue;

        if ($this->isSupervisor) {
            $styleArray = $this->getEndColor()->getStyleArray(array('argb' => $color->getARGB()));
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->endColor = $color;
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
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getHashCode();
        }
        return md5(
            $this->getFillType() .
            $this->getRotation() .
            $this->getStartColor()->getHashCode() .
            $this->getEndColor()->getHashCode() .
            __CLASS__
        );
    }
}
