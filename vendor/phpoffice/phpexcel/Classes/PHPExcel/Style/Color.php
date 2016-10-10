<?php

/**
 * PHPExcel_Style_Color
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
 * @copyright Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version ##VERSION##, ##DATE##
 */
class PHPExcel_Style_Color extends PHPExcel_Style_Supervisor implements PHPExcel_IComparable
{
    /* Colors */
    const COLOR_BLACK      = 'FF000000';
    const COLOR_WHITE      = 'FFFFFFFF';
    const COLOR_RED        = 'FFFF0000';
    const COLOR_DARKRED    = 'FF800000';
    const COLOR_BLUE       = 'FF0000FF';
    const COLOR_DARKBLUE   = 'FF000080';
    const COLOR_GREEN      = 'FF00FF00';
    const COLOR_DARKGREEN  = 'FF008000';
    const COLOR_YELLOW     = 'FFFFFF00';
    const COLOR_DARKYELLOW = 'FF808000';

    /**
     * Indexed colors array
     *
     * @var array
     */
    protected static $indexedColors;

    /**
     * ARGB - Alpha RGB
     *
     * @var string
     */
    protected $argb = null;

    /**
     * Parent property name
     *
     * @var string
     */
    protected $parentPropertyName;


    /**
     * Create a new PHPExcel_Style_Color
     *
     * @param    string    $pARGB            ARGB value for the colour
     * @param    boolean    $isSupervisor    Flag indicating if this is a supervisor or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     * @param    boolean    $isConditional    Flag indicating if this is a conditional style or not
     *                                    Leave this value at default unless you understand exactly what
     *                                        its ramifications are
     */
    public function __construct($pARGB = PHPExcel_Style_Color::COLOR_BLACK, $isSupervisor = false, $isConditional = false)
    {
        //    Supervisor?
        parent::__construct($isSupervisor);

        //    Initialise values
        if (!$isConditional) {
            $this->argb = $pARGB;
        }
    }

    /**
     * Bind parent. Only used for supervisor
     *
     * @param mixed $parent
     * @param string $parentPropertyName
     * @return PHPExcel_Style_Color
     */
    public function bindParent($parent, $parentPropertyName = null)
    {
        $this->parent = $parent;
        $this->parentPropertyName = $parentPropertyName;
        return $this;
    }

    /**
     * Get the shared style component for the currently active cell in currently active sheet.
     * Only used for style supervisor
     *
     * @return PHPExcel_Style_Color
     */
    public function getSharedComponent()
    {
        switch ($this->parentPropertyName) {
            case 'endColor':
                return $this->parent->getSharedComponent()->getEndColor();
            case 'color':
                return $this->parent->getSharedComponent()->getColor();
            case 'startColor':
                return $this->parent->getSharedComponent()->getStartColor();
        }
    }

    /**
     * Build style array from subcomponents
     *
     * @param array $array
     * @return array
     */
    public function getStyleArray($array)
    {
        switch ($this->parentPropertyName) {
            case 'endColor':
                $key = 'endcolor';
                break;
            case 'color':
                $key = 'color';
                break;
            case 'startColor':
                $key = 'startcolor';
                break;

        }
        return $this->parent->getStyleArray(array($key => $array));
    }

    /**
     * Apply styles from array
     *
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->getColor()->applyFromArray( array('rgb' => '808080') );
     * </code>
     *
     * @param    array    $pStyles    Array containing style information
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Style_Color
     */
    public function applyFromArray($pStyles = null)
    {
        if (is_array($pStyles)) {
            if ($this->isSupervisor) {
                $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
            } else {
                if (array_key_exists('rgb', $pStyles)) {
                    $this->setRGB($pStyles['rgb']);
                }
                if (array_key_exists('argb', $pStyles)) {
                    $this->setARGB($pStyles['argb']);
                }
            }
        } else {
            throw new PHPExcel_Exception("Invalid style array passed.");
        }
        return $this;
    }

    /**
     * Get ARGB
     *
     * @return string
     */
    public function getARGB()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getARGB();
        }
        return $this->argb;
    }

    /**
     * Set ARGB
     *
     * @param string $pValue
     * @return PHPExcel_Style_Color
     */
    public function setARGB($pValue = PHPExcel_Style_Color::COLOR_BLACK)
    {
        if ($pValue == '') {
            $pValue = PHPExcel_Style_Color::COLOR_BLACK;
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(array('argb' => $pValue));
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->argb = $pValue;
        }
        return $this;
    }

    /**
     * Get RGB
     *
     * @return string
     */
    public function getRGB()
    {
        if ($this->isSupervisor) {
            return $this->getSharedComponent()->getRGB();
        }
        return substr($this->argb, 2);
    }

    /**
     * Set RGB
     *
     * @param    string    $pValue    RGB value
     * @return PHPExcel_Style_Color
     */
    public function setRGB($pValue = '000000')
    {
        if ($pValue == '') {
            $pValue = '000000';
        }
        if ($this->isSupervisor) {
            $styleArray = $this->getStyleArray(array('argb' => 'FF' . $pValue));
            $this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
        } else {
            $this->argb = 'FF' . $pValue;
        }
        return $this;
    }

    /**
     * Get a specified colour component of an RGB value
     *
     * @private
     * @param    string        $RGB        The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param    int            $offset        Position within the RGB value to extract
     * @param    boolean        $hex        Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     * @return    string        The extracted colour component
     */
    private static function getColourComponent($RGB, $offset, $hex = true)
    {
        $colour = substr($RGB, $offset, 2);
        if (!$hex) {
            $colour = hexdec($colour);
        }
        return $colour;
    }

    /**
     * Get the red colour component of an RGB value
     *
     * @param    string        $RGB        The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param    boolean        $hex        Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     * @return    string        The red colour component
     */
    public static function getRed($RGB, $hex = true)
    {
        return self::getColourComponent($RGB, strlen($RGB) - 6, $hex);
    }

    /**
     * Get the green colour component of an RGB value
     *
     * @param    string        $RGB        The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param    boolean        $hex        Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     * @return    string        The green colour component
     */
    public static function getGreen($RGB, $hex = true)
    {
        return self::getColourComponent($RGB, strlen($RGB) - 4, $hex);
    }

    /**
     * Get the blue colour component of an RGB value
     *
     * @param    string        $RGB        The colour as an RGB value (e.g. FF00CCCC or CCDDEE
     * @param    boolean        $hex        Flag indicating whether the component should be returned as a hex or a
     *                                    decimal value
     * @return    string        The blue colour component
     */
    public static function getBlue($RGB, $hex = true)
    {
        return self::getColourComponent($RGB, strlen($RGB) - 2, $hex);
    }

    /**
     * Adjust the brightness of a color
     *
     * @param    string        $hex    The colour as an RGBA or RGB value (e.g. FF00CCCC or CCDDEE)
     * @param    float        $adjustPercentage    The percentage by which to adjust the colour as a float from -1 to 1
     * @return    string        The adjusted colour as an RGBA or RGB value (e.g. FF00CCCC or CCDDEE)
     */
    public static function changeBrightness($hex, $adjustPercentage)
    {
        $rgba = (strlen($hex) == 8);

        $red = self::getRed($hex, false);
        $green = self::getGreen($hex, false);
        $blue = self::getBlue($hex, false);
        if ($adjustPercentage > 0) {
            $red += (255 - $red) * $adjustPercentage;
            $green += (255 - $green) * $adjustPercentage;
            $blue += (255 - $blue) * $adjustPercentage;
        } else {
            $red += $red * $adjustPercentage;
            $green += $green * $adjustPercentage;
            $blue += $blue * $adjustPercentage;
        }

        if ($red < 0) {
            $red = 0;
        } elseif ($red > 255) {
            $red = 255;
        }
        if ($green < 0) {
            $green = 0;
        } elseif ($green > 255) {
            $green = 255;
        }
        if ($blue < 0) {
            $blue = 0;
        } elseif ($blue > 255) {
            $blue = 255;
        }

        $rgb = strtoupper(
            str_pad(dechex($red), 2, '0', 0) .
            str_pad(dechex($green), 2, '0', 0) .
            str_pad(dechex($blue), 2, '0', 0)
        );
        return (($rgba) ? 'FF' : '') . $rgb;
    }

    /**
     * Get indexed color
     *
     * @param    int            $pIndex            Index entry point into the colour array
     * @param    boolean        $background        Flag to indicate whether default background or foreground colour
     *                                            should be returned if the indexed colour doesn't exist
     * @return    PHPExcel_Style_Color
     */
    public static function indexedColor($pIndex, $background = false)
    {
        // Clean parameter
        $pIndex = intval($pIndex);

        // Indexed colors
        if (is_null(self::$indexedColors)) {
            self::$indexedColors = array(
                    1    => 'FF000000',    //    System Colour #1 - Black
                    2    => 'FFFFFFFF',    //    System Colour #2 - White
                    3    => 'FFFF0000',    //    System Colour #3 - Red
                    4    => 'FF00FF00',    //    System Colour #4 - Green
                    5    => 'FF0000FF',    //    System Colour #5 - Blue
                    6    => 'FFFFFF00',    //    System Colour #6 - Yellow
                    7    => 'FFFF00FF',    //    System Colour #7- Magenta
                    8    => 'FF00FFFF',    //    System Colour #8- Cyan
                    9    => 'FF800000',    //    Standard Colour #9
                    10    => 'FF008000',    //    Standard Colour #10
                    11    => 'FF000080',    //    Standard Colour #11
                    12    => 'FF808000',    //    Standard Colour #12
                    13    => 'FF800080',    //    Standard Colour #13
                    14    => 'FF008080',    //    Standard Colour #14
                    15    => 'FFC0C0C0',    //    Standard Colour #15
                    16    => 'FF808080',    //    Standard Colour #16
                    17    => 'FF9999FF',    //    Chart Fill Colour #17
                    18    => 'FF993366',    //    Chart Fill Colour #18
                    19    => 'FFFFFFCC',    //    Chart Fill Colour #19
                    20    => 'FFCCFFFF',    //    Chart Fill Colour #20
                    21    => 'FF660066',    //    Chart Fill Colour #21
                    22    => 'FFFF8080',    //    Chart Fill Colour #22
                    23    => 'FF0066CC',    //    Chart Fill Colour #23
                    24    => 'FFCCCCFF',    //    Chart Fill Colour #24
                    25    => 'FF000080',    //    Chart Line Colour #25
                    26    => 'FFFF00FF',    //    Chart Line Colour #26
                    27    => 'FFFFFF00',    //    Chart Line Colour #27
                    28    => 'FF00FFFF',    //    Chart Line Colour #28
                    29    => 'FF800080',    //    Chart Line Colour #29
                    30    => 'FF800000',    //    Chart Line Colour #30
                    31    => 'FF008080',    //    Chart Line Colour #31
                    32    => 'FF0000FF',    //    Chart Line Colour #32
                    33    => 'FF00CCFF',    //    Standard Colour #33
                    34    => 'FFCCFFFF',    //    Standard Colour #34
                    35    => 'FFCCFFCC',    //    Standard Colour #35
                    36    => 'FFFFFF99',    //    Standard Colour #36
                    37    => 'FF99CCFF',    //    Standard Colour #37
                    38    => 'FFFF99CC',    //    Standard Colour #38
                    39    => 'FFCC99FF',    //    Standard Colour #39
                    40    => 'FFFFCC99',    //    Standard Colour #40
                    41    => 'FF3366FF',    //    Standard Colour #41
                    42    => 'FF33CCCC',    //    Standard Colour #42
                    43    => 'FF99CC00',    //    Standard Colour #43
                    44    => 'FFFFCC00',    //    Standard Colour #44
                    45    => 'FFFF9900',    //    Standard Colour #45
                    46    => 'FFFF6600',    //    Standard Colour #46
                    47    => 'FF666699',    //    Standard Colour #47
                    48    => 'FF969696',    //    Standard Colour #48
                    49    => 'FF003366',    //    Standard Colour #49
                    50    => 'FF339966',    //    Standard Colour #50
                    51    => 'FF003300',    //    Standard Colour #51
                    52    => 'FF333300',    //    Standard Colour #52
                    53    => 'FF993300',    //    Standard Colour #53
                    54    => 'FF993366',    //    Standard Colour #54
                    55    => 'FF333399',    //    Standard Colour #55
                    56    => 'FF333333'    //    Standard Colour #56
                );
        }

        if (array_key_exists($pIndex, self::$indexedColors)) {
            return new PHPExcel_Style_Color(self::$indexedColors[$pIndex]);
        }

        if ($background) {
            return new PHPExcel_Style_Color(self::COLOR_WHITE);
        }
        return new PHPExcel_Style_Color(self::COLOR_BLACK);
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
            $this->argb .
            __CLASS__
        );
    }
}
