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
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Worksheet_PageSetup
 *
 * <code>
 * Paper size taken from Office Open XML Part 4 - Markup Language Reference, page 1988:
 *
 * 1 = Letter paper (8.5 in. by 11 in.)
 * 2 = Letter small paper (8.5 in. by 11 in.)
 * 3 = Tabloid paper (11 in. by 17 in.)
 * 4 = Ledger paper (17 in. by 11 in.)
 * 5 = Legal paper (8.5 in. by 14 in.)
 * 6 = Statement paper (5.5 in. by 8.5 in.)
 * 7 = Executive paper (7.25 in. by 10.5 in.)
 * 8 = A3 paper (297 mm by 420 mm)
 * 9 = A4 paper (210 mm by 297 mm)
 * 10 = A4 small paper (210 mm by 297 mm)
 * 11 = A5 paper (148 mm by 210 mm)
 * 12 = B4 paper (250 mm by 353 mm)
 * 13 = B5 paper (176 mm by 250 mm)
 * 14 = Folio paper (8.5 in. by 13 in.)
 * 15 = Quarto paper (215 mm by 275 mm)
 * 16 = Standard paper (10 in. by 14 in.)
 * 17 = Standard paper (11 in. by 17 in.)
 * 18 = Note paper (8.5 in. by 11 in.)
 * 19 = #9 envelope (3.875 in. by 8.875 in.)
 * 20 = #10 envelope (4.125 in. by 9.5 in.)
 * 21 = #11 envelope (4.5 in. by 10.375 in.)
 * 22 = #12 envelope (4.75 in. by 11 in.)
 * 23 = #14 envelope (5 in. by 11.5 in.)
 * 24 = C paper (17 in. by 22 in.)
 * 25 = D paper (22 in. by 34 in.)
 * 26 = E paper (34 in. by 44 in.)
 * 27 = DL envelope (110 mm by 220 mm)
 * 28 = C5 envelope (162 mm by 229 mm)
 * 29 = C3 envelope (324 mm by 458 mm)
 * 30 = C4 envelope (229 mm by 324 mm)
 * 31 = C6 envelope (114 mm by 162 mm)
 * 32 = C65 envelope (114 mm by 229 mm)
 * 33 = B4 envelope (250 mm by 353 mm)
 * 34 = B5 envelope (176 mm by 250 mm)
 * 35 = B6 envelope (176 mm by 125 mm)
 * 36 = Italy envelope (110 mm by 230 mm)
 * 37 = Monarch envelope (3.875 in. by 7.5 in.).
 * 38 = 6 3/4 envelope (3.625 in. by 6.5 in.)
 * 39 = US standard fanfold (14.875 in. by 11 in.)
 * 40 = German standard fanfold (8.5 in. by 12 in.)
 * 41 = German legal fanfold (8.5 in. by 13 in.)
 * 42 = ISO B4 (250 mm by 353 mm)
 * 43 = Japanese double postcard (200 mm by 148 mm)
 * 44 = Standard paper (9 in. by 11 in.)
 * 45 = Standard paper (10 in. by 11 in.)
 * 46 = Standard paper (15 in. by 11 in.)
 * 47 = Invite envelope (220 mm by 220 mm)
 * 50 = Letter extra paper (9.275 in. by 12 in.)
 * 51 = Legal extra paper (9.275 in. by 15 in.)
 * 52 = Tabloid extra paper (11.69 in. by 18 in.)
 * 53 = A4 extra paper (236 mm by 322 mm)
 * 54 = Letter transverse paper (8.275 in. by 11 in.)
 * 55 = A4 transverse paper (210 mm by 297 mm)
 * 56 = Letter extra transverse paper (9.275 in. by 12 in.)
 * 57 = SuperA/SuperA/A4 paper (227 mm by 356 mm)
 * 58 = SuperB/SuperB/A3 paper (305 mm by 487 mm)
 * 59 = Letter plus paper (8.5 in. by 12.69 in.)
 * 60 = A4 plus paper (210 mm by 330 mm)
 * 61 = A5 transverse paper (148 mm by 210 mm)
 * 62 = JIS B5 transverse paper (182 mm by 257 mm)
 * 63 = A3 extra paper (322 mm by 445 mm)
 * 64 = A5 extra paper (174 mm by 235 mm)
 * 65 = ISO B5 extra paper (201 mm by 276 mm)
 * 66 = A2 paper (420 mm by 594 mm)
 * 67 = A3 transverse paper (297 mm by 420 mm)
 * 68 = A3 extra transverse paper (322 mm by 445 mm)
 * </code>
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_PageSetup
{
    /* Paper size */
    const PAPERSIZE_LETTER                          = 1;
    const PAPERSIZE_LETTER_SMALL                    = 2;
    const PAPERSIZE_TABLOID                         = 3;
    const PAPERSIZE_LEDGER                          = 4;
    const PAPERSIZE_LEGAL                           = 5;
    const PAPERSIZE_STATEMENT                       = 6;
    const PAPERSIZE_EXECUTIVE                       = 7;
    const PAPERSIZE_A3                              = 8;
    const PAPERSIZE_A4                              = 9;
    const PAPERSIZE_A4_SMALL                        = 10;
    const PAPERSIZE_A5                              = 11;
    const PAPERSIZE_B4                              = 12;
    const PAPERSIZE_B5                              = 13;
    const PAPERSIZE_FOLIO                           = 14;
    const PAPERSIZE_QUARTO                          = 15;
    const PAPERSIZE_STANDARD_1                      = 16;
    const PAPERSIZE_STANDARD_2                      = 17;
    const PAPERSIZE_NOTE                            = 18;
    const PAPERSIZE_NO9_ENVELOPE                    = 19;
    const PAPERSIZE_NO10_ENVELOPE                   = 20;
    const PAPERSIZE_NO11_ENVELOPE                   = 21;
    const PAPERSIZE_NO12_ENVELOPE                   = 22;
    const PAPERSIZE_NO14_ENVELOPE                   = 23;
    const PAPERSIZE_C                               = 24;
    const PAPERSIZE_D                               = 25;
    const PAPERSIZE_E                               = 26;
    const PAPERSIZE_DL_ENVELOPE                     = 27;
    const PAPERSIZE_C5_ENVELOPE                     = 28;
    const PAPERSIZE_C3_ENVELOPE                     = 29;
    const PAPERSIZE_C4_ENVELOPE                     = 30;
    const PAPERSIZE_C6_ENVELOPE                     = 31;
    const PAPERSIZE_C65_ENVELOPE                    = 32;
    const PAPERSIZE_B4_ENVELOPE                     = 33;
    const PAPERSIZE_B5_ENVELOPE                     = 34;
    const PAPERSIZE_B6_ENVELOPE                     = 35;
    const PAPERSIZE_ITALY_ENVELOPE                  = 36;
    const PAPERSIZE_MONARCH_ENVELOPE                = 37;
    const PAPERSIZE_6_3_4_ENVELOPE                  = 38;
    const PAPERSIZE_US_STANDARD_FANFOLD             = 39;
    const PAPERSIZE_GERMAN_STANDARD_FANFOLD         = 40;
    const PAPERSIZE_GERMAN_LEGAL_FANFOLD            = 41;
    const PAPERSIZE_ISO_B4                          = 42;
    const PAPERSIZE_JAPANESE_DOUBLE_POSTCARD        = 43;
    const PAPERSIZE_STANDARD_PAPER_1                = 44;
    const PAPERSIZE_STANDARD_PAPER_2                = 45;
    const PAPERSIZE_STANDARD_PAPER_3                = 46;
    const PAPERSIZE_INVITE_ENVELOPE                 = 47;
    const PAPERSIZE_LETTER_EXTRA_PAPER              = 48;
    const PAPERSIZE_LEGAL_EXTRA_PAPER               = 49;
    const PAPERSIZE_TABLOID_EXTRA_PAPER             = 50;
    const PAPERSIZE_A4_EXTRA_PAPER                  = 51;
    const PAPERSIZE_LETTER_TRANSVERSE_PAPER         = 52;
    const PAPERSIZE_A4_TRANSVERSE_PAPER             = 53;
    const PAPERSIZE_LETTER_EXTRA_TRANSVERSE_PAPER   = 54;
    const PAPERSIZE_SUPERA_SUPERA_A4_PAPER          = 55;
    const PAPERSIZE_SUPERB_SUPERB_A3_PAPER          = 56;
    const PAPERSIZE_LETTER_PLUS_PAPER               = 57;
    const PAPERSIZE_A4_PLUS_PAPER                   = 58;
    const PAPERSIZE_A5_TRANSVERSE_PAPER             = 59;
    const PAPERSIZE_JIS_B5_TRANSVERSE_PAPER         = 60;
    const PAPERSIZE_A3_EXTRA_PAPER                  = 61;
    const PAPERSIZE_A5_EXTRA_PAPER                  = 62;
    const PAPERSIZE_ISO_B5_EXTRA_PAPER              = 63;
    const PAPERSIZE_A2_PAPER                        = 64;
    const PAPERSIZE_A3_TRANSVERSE_PAPER             = 65;
    const PAPERSIZE_A3_EXTRA_TRANSVERSE_PAPER       = 66;

    /* Page orientation */
    const ORIENTATION_DEFAULT                       = 'default';
    const ORIENTATION_LANDSCAPE                     = 'landscape';
    const ORIENTATION_PORTRAIT                      = 'portrait';

    /* Print Range Set Method */
    const SETPRINTRANGE_OVERWRITE                   = 'O';
    const SETPRINTRANGE_INSERT                      = 'I';


    /**
     * Paper size
     *
     * @var int
     */
    private $paperSize = PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER;

    /**
     * Orientation
     *
     * @var string
     */
    private $orientation = PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT;

    /**
     * Scale (Print Scale)
     *
     * Print scaling. Valid values range from 10 to 400
     * This setting is overridden when fitToWidth and/or fitToHeight are in use
     *
     * @var int?
     */
    private $scale = 100;

    /**
      * Fit To Page
      * Whether scale or fitToWith / fitToHeight applies
      *
      * @var boolean
      */
    private $fitToPage = false;

    /**
      * Fit To Height
      * Number of vertical pages to fit on
      *
      * @var int?
      */
    private $fitToHeight    = 1;

    /**
      * Fit To Width
      * Number of horizontal pages to fit on
      *
      * @var int?
      */
    private $fitToWidth    = 1;

    /**
     * Columns to repeat at left
     *
     * @var array Containing start column and end column, empty array if option unset
     */
    private $columnsToRepeatAtLeft = array('', '');

    /**
     * Rows to repeat at top
     *
     * @var array Containing start row number and end row number, empty array if option unset
     */
    private $rowsToRepeatAtTop = array(0, 0);

    /**
     * Center page horizontally
     *
     * @var boolean
     */
    private $horizontalCentered = false;

    /**
     * Center page vertically
     *
     * @var boolean
     */
    private $verticalCentered = false;

    /**
     * Print area
     *
     * @var string
     */
    private $printArea = null;

    /**
     * First page number
     *
     * @var int
     */
    private $firstPageNumber = null;

    /**
     * Create a new PHPExcel_Worksheet_PageSetup
     */
    public function __construct()
    {
    }

    /**
     * Get Paper Size
     *
     * @return int
     */
    public function getPaperSize()
    {
        return $this->paperSize;
    }

    /**
     * Set Paper Size
     *
     * @param int $pValue
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setPaperSize($pValue = PHPExcel_Worksheet_PageSetup::PAPERSIZE_LETTER)
    {
        $this->paperSize = $pValue;
        return $this;
    }

    /**
     * Get Orientation
     *
     * @return string
     */
    public function getOrientation()
    {
        return $this->orientation;
    }

    /**
     * Set Orientation
     *
     * @param string $pValue
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setOrientation($pValue = PHPExcel_Worksheet_PageSetup::ORIENTATION_DEFAULT)
    {
        $this->orientation = $pValue;
        return $this;
    }

    /**
     * Get Scale
     *
     * @return int?
     */
    public function getScale()
    {
        return $this->scale;
    }

    /**
     * Set Scale
     *
     * Print scaling. Valid values range from 10 to 400
     * This setting is overridden when fitToWidth and/or fitToHeight are in use
     *
     * @param     int?    $pValue
     * @param boolean    $pUpdate    Update fitToPage so scaling applies rather than fitToHeight / fitToWidth
     * @return PHPExcel_Worksheet_PageSetup
     * @throws     PHPExcel_Exception
     */
    public function setScale($pValue = 100, $pUpdate = true)
    {
        // Microsoft Office Excel 2007 only allows setting a scale between 10 and 400 via the user interface,
        // but it is apparently still able to handle any scale >= 0, where 0 results in 100
        if (($pValue >= 0) || is_null($pValue)) {
            $this->scale = $pValue;
            if ($pUpdate) {
                $this->fitToPage = false;
            }
        } else {
            throw new PHPExcel_Exception("Scale must not be negative");
        }
        return $this;
    }

    /**
     * Get Fit To Page
     *
     * @return boolean
     */
    public function getFitToPage()
    {
        return $this->fitToPage;
    }

    /**
     * Set Fit To Page
     *
     * @param boolean $pValue
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setFitToPage($pValue = true)
    {
        $this->fitToPage = $pValue;
        return $this;
    }

    /**
     * Get Fit To Height
     *
     * @return int?
     */
    public function getFitToHeight()
    {
        return $this->fitToHeight;
    }

    /**
     * Set Fit To Height
     *
     * @param int? $pValue
     * @param boolean $pUpdate Update fitToPage so it applies rather than scaling
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setFitToHeight($pValue = 1, $pUpdate = true)
    {
        $this->fitToHeight = $pValue;
        if ($pUpdate) {
            $this->fitToPage = true;
        }
        return $this;
    }

    /**
     * Get Fit To Width
     *
     * @return int?
     */
    public function getFitToWidth()
    {
        return $this->fitToWidth;
    }

    /**
     * Set Fit To Width
     *
     * @param int? $pValue
     * @param boolean $pUpdate Update fitToPage so it applies rather than scaling
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setFitToWidth($pValue = 1, $pUpdate = true)
    {
        $this->fitToWidth = $pValue;
        if ($pUpdate) {
            $this->fitToPage = true;
        }
        return $this;
    }

    /**
     * Is Columns to repeat at left set?
     *
     * @return boolean
     */
    public function isColumnsToRepeatAtLeftSet()
    {
        if (is_array($this->columnsToRepeatAtLeft)) {
            if ($this->columnsToRepeatAtLeft[0] != '' && $this->columnsToRepeatAtLeft[1] != '') {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Columns to repeat at left
     *
     * @return array Containing start column and end column, empty array if option unset
     */
    public function getColumnsToRepeatAtLeft()
    {
        return $this->columnsToRepeatAtLeft;
    }

    /**
     * Set Columns to repeat at left
     *
     * @param array $pValue Containing start column and end column, empty array if option unset
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setColumnsToRepeatAtLeft($pValue = null)
    {
        if (is_array($pValue)) {
            $this->columnsToRepeatAtLeft = $pValue;
        }
        return $this;
    }

    /**
     * Set Columns to repeat at left by start and end
     *
     * @param string $pStart
     * @param string $pEnd
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setColumnsToRepeatAtLeftByStartAndEnd($pStart = 'A', $pEnd = 'A')
    {
        $this->columnsToRepeatAtLeft = array($pStart, $pEnd);
        return $this;
    }

    /**
     * Is Rows to repeat at top set?
     *
     * @return boolean
     */
    public function isRowsToRepeatAtTopSet()
    {
        if (is_array($this->rowsToRepeatAtTop)) {
            if ($this->rowsToRepeatAtTop[0] != 0 && $this->rowsToRepeatAtTop[1] != 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Rows to repeat at top
     *
     * @return array Containing start column and end column, empty array if option unset
     */
    public function getRowsToRepeatAtTop()
    {
        return $this->rowsToRepeatAtTop;
    }

    /**
     * Set Rows to repeat at top
     *
     * @param array    $pValue    Containing start column and end column, empty array if option unset
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setRowsToRepeatAtTop($pValue = null)
    {
        if (is_array($pValue)) {
            $this->rowsToRepeatAtTop = $pValue;
        }
        return $this;
    }

    /**
     * Set Rows to repeat at top by start and end
     *
     * @param int $pStart
     * @param int $pEnd
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setRowsToRepeatAtTopByStartAndEnd($pStart = 1, $pEnd = 1)
    {
        $this->rowsToRepeatAtTop = array($pStart, $pEnd);
        return $this;
    }

    /**
     * Get center page horizontally
     *
     * @return bool
     */
    public function getHorizontalCentered()
    {
        return $this->horizontalCentered;
    }

    /**
     * Set center page horizontally
     *
     * @param bool $value
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setHorizontalCentered($value = false)
    {
        $this->horizontalCentered = $value;
        return $this;
    }

    /**
     * Get center page vertically
     *
     * @return bool
     */
    public function getVerticalCentered()
    {
        return $this->verticalCentered;
    }

    /**
     * Set center page vertically
     *
     * @param bool $value
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function setVerticalCentered($value = false)
    {
        $this->verticalCentered = $value;
        return $this;
    }

    /**
     *    Get print area
     *
     * @param    int        $index    Identifier for a specific print area range if several ranges have been set
     *                            Default behaviour, or a index value of 0, will return all ranges as a comma-separated string
     *                            Otherwise, the specific range identified by the value of $index will be returned
     *                            Print areas are numbered from 1
     * @throws    PHPExcel_Exception
     * @return    string
     */
    public function getPrintArea($index = 0)
    {
        if ($index == 0) {
            return $this->printArea;
        }
        $printAreas = explode(',', $this->printArea);
        if (isset($printAreas[$index-1])) {
            return $printAreas[$index-1];
        }
        throw new PHPExcel_Exception("Requested Print Area does not exist");
    }

    /**
     * Is print area set?
     *
     * @param    int        $index    Identifier for a specific print area range if several ranges have been set
     *                            Default behaviour, or an index value of 0, will identify whether any print range is set
     *                            Otherwise, existence of the range identified by the value of $index will be returned
     *                            Print areas are numbered from 1
     * @return    boolean
     */
    public function isPrintAreaSet($index = 0)
    {
        if ($index == 0) {
            return !is_null($this->printArea);
        }
        $printAreas = explode(',', $this->printArea);
        return isset($printAreas[$index-1]);
    }

    /**
     * Clear a print area
     *
     * @param    int        $index    Identifier for a specific print area range if several ranges have been set
     *                            Default behaviour, or an index value of 0, will clear all print ranges that are set
     *                            Otherwise, the range identified by the value of $index will be removed from the series
     *                            Print areas are numbered from 1
     * @return    PHPExcel_Worksheet_PageSetup
     */
    public function clearPrintArea($index = 0)
    {
        if ($index == 0) {
            $this->printArea = null;
        } else {
            $printAreas = explode(',', $this->printArea);
            if (isset($printAreas[$index-1])) {
                unset($printAreas[$index-1]);
                $this->printArea = implode(',', $printAreas);
            }
        }

        return $this;
    }

    /**
     * Set print area. e.g. 'A1:D10' or 'A1:D10,G5:M20'
     *
     * @param    string    $value
     * @param    int        $index    Identifier for a specific print area range allowing several ranges to be set
     *                            When the method is "O"verwrite, then a positive integer index will overwrite that indexed
     *                                entry in the print areas list; a negative index value will identify which entry to
     *                                overwrite working bacward through the print area to the list, with the last entry as -1.
     *                                Specifying an index value of 0, will overwrite <b>all</b> existing print ranges.
     *                            When the method is "I"nsert, then a positive index will insert after that indexed entry in
     *                                the print areas list, while a negative index will insert before the indexed entry.
     *                                Specifying an index value of 0, will always append the new print range at the end of the
     *                                list.
     *                            Print areas are numbered from 1
     * @param    string    $method    Determines the method used when setting multiple print areas
     *                            Default behaviour, or the "O" method, overwrites existing print area
     *                            The "I" method, inserts the new print area before any specified index, or at the end of the list
     * @return    PHPExcel_Worksheet_PageSetup
     * @throws    PHPExcel_Exception
     */
    public function setPrintArea($value, $index = 0, $method = self::SETPRINTRANGE_OVERWRITE)
    {
        if (strpos($value, '!') !== false) {
            throw new PHPExcel_Exception('Cell coordinate must not specify a worksheet.');
        } elseif (strpos($value, ':') === false) {
            throw new PHPExcel_Exception('Cell coordinate must be a range of cells.');
        } elseif (strpos($value, '$') !== false) {
            throw new PHPExcel_Exception('Cell coordinate must not be absolute.');
        }
        $value = strtoupper($value);

        if ($method == self::SETPRINTRANGE_OVERWRITE) {
            if ($index == 0) {
                $this->printArea = $value;
            } else {
                $printAreas = explode(',', $this->printArea);
                if ($index < 0) {
                    $index = count($printAreas) - abs($index) + 1;
                }
                if (($index <= 0) || ($index > count($printAreas))) {
                    throw new PHPExcel_Exception('Invalid index for setting print range.');
                }
                $printAreas[$index-1] = $value;
                $this->printArea = implode(',', $printAreas);
            }
        } elseif ($method == self::SETPRINTRANGE_INSERT) {
            if ($index == 0) {
                $this->printArea .= ($this->printArea == '') ? $value : ','.$value;
            } else {
                $printAreas = explode(',', $this->printArea);
                if ($index < 0) {
                    $index = abs($index) - 1;
                }
                if ($index > count($printAreas)) {
                    throw new PHPExcel_Exception('Invalid index for setting print range.');
                }
                $printAreas = array_merge(array_slice($printAreas, 0, $index), array($value), array_slice($printAreas, $index));
                $this->printArea = implode(',', $printAreas);
            }
        } else {
            throw new PHPExcel_Exception('Invalid method for setting print range.');
        }

        return $this;
    }

    /**
     * Add a new print area (e.g. 'A1:D10' or 'A1:D10,G5:M20') to the list of print areas
     *
     * @param    string    $value
     * @param    int        $index    Identifier for a specific print area range allowing several ranges to be set
     *                            A positive index will insert after that indexed entry in the print areas list, while a
     *                                negative index will insert before the indexed entry.
     *                                Specifying an index value of 0, will always append the new print range at the end of the
     *                                list.
     *                            Print areas are numbered from 1
     * @return    PHPExcel_Worksheet_PageSetup
     * @throws    PHPExcel_Exception
     */
    public function addPrintArea($value, $index = -1)
    {
        return $this->setPrintArea($value, $index, self::SETPRINTRANGE_INSERT);
    }

    /**
     * Set print area
     *
     * @param    int        $column1    Column 1
     * @param    int        $row1        Row 1
     * @param    int        $column2    Column 2
     * @param    int        $row2        Row 2
     * @param    int        $index        Identifier for a specific print area range allowing several ranges to be set
     *                                When the method is "O"verwrite, then a positive integer index will overwrite that indexed
     *                                    entry in the print areas list; a negative index value will identify which entry to
     *                                    overwrite working bacward through the print area to the list, with the last entry as -1.
     *                                    Specifying an index value of 0, will overwrite <b>all</b> existing print ranges.
     *                                When the method is "I"nsert, then a positive index will insert after that indexed entry in
     *                                    the print areas list, while a negative index will insert before the indexed entry.
     *                                    Specifying an index value of 0, will always append the new print range at the end of the
     *                                    list.
     *                                Print areas are numbered from 1
     * @param    string    $method        Determines the method used when setting multiple print areas
     *                                Default behaviour, or the "O" method, overwrites existing print area
     *                                The "I" method, inserts the new print area before any specified index, or at the end of the list
     * @return    PHPExcel_Worksheet_PageSetup
     * @throws    PHPExcel_Exception
     */
    public function setPrintAreaByColumnAndRow($column1, $row1, $column2, $row2, $index = 0, $method = self::SETPRINTRANGE_OVERWRITE)
    {
        return $this->setPrintArea(
            PHPExcel_Cell::stringFromColumnIndex($column1) . $row1 . ':' . PHPExcel_Cell::stringFromColumnIndex($column2) . $row2,
            $index,
            $method
        );
    }

    /**
     * Add a new print area to the list of print areas
     *
     * @param    int        $column1    Start Column for the print area
     * @param    int        $row1        Start Row for the print area
     * @param    int        $column2    End Column for the print area
     * @param    int        $row2        End Row for the print area
     * @param    int        $index        Identifier for a specific print area range allowing several ranges to be set
     *                                A positive index will insert after that indexed entry in the print areas list, while a
     *                                    negative index will insert before the indexed entry.
     *                                    Specifying an index value of 0, will always append the new print range at the end of the
     *                                    list.
     *                                Print areas are numbered from 1
     * @return    PHPExcel_Worksheet_PageSetup
     * @throws    PHPExcel_Exception
     */
    public function addPrintAreaByColumnAndRow($column1, $row1, $column2, $row2, $index = -1)
    {
        return $this->setPrintArea(
            PHPExcel_Cell::stringFromColumnIndex($column1) . $row1 . ':' . PHPExcel_Cell::stringFromColumnIndex($column2) . $row2,
            $index,
            self::SETPRINTRANGE_INSERT
        );
    }

    /**
     * Get first page number
     *
     * @return int
     */
    public function getFirstPageNumber()
    {
        return $this->firstPageNumber;
    }

    /**
     * Set first page number
     *
     * @param int $value
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function setFirstPageNumber($value = null)
    {
        $this->firstPageNumber = $value;
        return $this;
    }

    /**
     * Reset first page number
     *
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function resetFirstPageNumber()
    {
        return $this->setFirstPageNumber(null);
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
