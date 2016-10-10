<?php

/**
 *    PHPExcel_Cell
 *
 *    Copyright (c) 2006 - 2015 PHPExcel
 *
 *    This library is free software; you can redistribute it and/or
 *    modify it under the terms of the GNU Lesser General Public
 *    License as published by the Free Software Foundation; either
 *    version 2.1 of the License, or (at your option) any later version.
 *
 *    This library is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 *    Lesser General Public License for more details.
 *
 *    You should have received a copy of the GNU Lesser General Public
 *    License along with this library; if not, write to the Free Software
 *    Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 *    @category    PHPExcel
 *    @package    PHPExcel_Cell
 *    @copyright    Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 *    @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 *    @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Cell
{
    /**
     *  Default range variable constant
     *
     *  @var  string
     */
    const DEFAULT_RANGE = 'A1:A1';

    /**
     *    Value binder to use
     *
     *    @var    PHPExcel_Cell_IValueBinder
     */
    private static $valueBinder;

    /**
     *    Value of the cell
     *
     *    @var    mixed
     */
    private $value;

    /**
     *    Calculated value of the cell (used for caching)
     *    This returns the value last calculated by MS Excel or whichever spreadsheet program was used to
     *        create the original spreadsheet file.
     *    Note that this value is not guaranteed to reflect the actual calculated value because it is
     *        possible that auto-calculation was disabled in the original spreadsheet, and underlying data
     *        values used by the formula have changed since it was last calculated.
     *
     *    @var mixed
     */
    private $calculatedValue;

    /**
     *    Type of the cell data
     *
     *    @var    string
     */
    private $dataType;

    /**
     *    Parent worksheet
     *
     *    @var    PHPExcel_CachedObjectStorage_CacheBase
     */
    private $parent;

    /**
     *    Index to cellXf
     *
     *    @var    int
     */
    private $xfIndex = 0;

    /**
     *    Attributes of the formula
     *
     */
    private $formulaAttributes;


    /**
     *    Send notification to the cache controller
     *
     *    @return void
     **/
    public function notifyCacheController()
    {
        $this->parent->updateCacheData($this);

        return $this;
    }

    public function detach()
    {
        $this->parent = null;
    }

    public function attach(PHPExcel_CachedObjectStorage_CacheBase $parent)
    {
        $this->parent = $parent;
    }


    /**
     *    Create a new Cell
     *
     *    @param    mixed                $pValue
     *    @param    string                $pDataType
     *    @param    PHPExcel_Worksheet    $pSheet
     *    @throws    PHPExcel_Exception
     */
    public function __construct($pValue = null, $pDataType = null, PHPExcel_Worksheet $pSheet = null)
    {
        // Initialise cell value
        $this->value = $pValue;

        // Set worksheet cache
        $this->parent = $pSheet->getCellCacheController();

        // Set datatype?
        if ($pDataType !== null) {
            if ($pDataType == PHPExcel_Cell_DataType::TYPE_STRING2) {
                $pDataType = PHPExcel_Cell_DataType::TYPE_STRING;
            }
            $this->dataType = $pDataType;
        } elseif (!self::getValueBinder()->bindValue($this, $pValue)) {
            throw new PHPExcel_Exception("Value could not be bound to cell.");
        }
    }

    /**
     *    Get cell coordinate column
     *
     *    @return    string
     */
    public function getColumn()
    {
        return $this->parent->getCurrentColumn();
    }

    /**
     *    Get cell coordinate row
     *
     *    @return    int
     */
    public function getRow()
    {
        return $this->parent->getCurrentRow();
    }

    /**
     *    Get cell coordinate
     *
     *    @return    string
     */
    public function getCoordinate()
    {
        return $this->parent->getCurrentAddress();
    }

    /**
     *    Get cell value
     *
     *    @return    mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *    Get cell value with formatting
     *
     *    @return    string
     */
    public function getFormattedValue()
    {
        return (string) PHPExcel_Style_NumberFormat::toFormattedString(
            $this->getCalculatedValue(),
            $this->getStyle()
                ->getNumberFormat()->getFormatCode()
        );
    }

    /**
     *    Set cell value
     *
     *    Sets the value for a cell, automatically determining the datatype using the value binder
     *
     *    @param    mixed    $pValue                    Value
     *    @return    PHPExcel_Cell
     *    @throws    PHPExcel_Exception
     */
    public function setValue($pValue = null)
    {
        if (!self::getValueBinder()->bindValue($this, $pValue)) {
            throw new PHPExcel_Exception("Value could not be bound to cell.");
        }
        return $this;
    }

    /**
     *    Set the value for a cell, with the explicit data type passed to the method (bypassing any use of the value binder)
     *
     *    @param    mixed    $pValue            Value
     *    @param    string    $pDataType        Explicit data type
     *    @return    PHPExcel_Cell
     *    @throws    PHPExcel_Exception
     */
    public function setValueExplicit($pValue = null, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
    {
        // set the value according to data type
        switch ($pDataType) {
            case PHPExcel_Cell_DataType::TYPE_NULL:
                $this->value = $pValue;
                break;
            case PHPExcel_Cell_DataType::TYPE_STRING2:
                $pDataType = PHPExcel_Cell_DataType::TYPE_STRING;
                // no break
            case PHPExcel_Cell_DataType::TYPE_STRING:
                // Synonym for string
            case PHPExcel_Cell_DataType::TYPE_INLINE:
                // Rich text
                $this->value = PHPExcel_Cell_DataType::checkString($pValue);
                break;
            case PHPExcel_Cell_DataType::TYPE_NUMERIC:
                $this->value = (float) $pValue;
                break;
            case PHPExcel_Cell_DataType::TYPE_FORMULA:
                $this->value = (string) $pValue;
                break;
            case PHPExcel_Cell_DataType::TYPE_BOOL:
                $this->value = (bool) $pValue;
                break;
            case PHPExcel_Cell_DataType::TYPE_ERROR:
                $this->value = PHPExcel_Cell_DataType::checkErrorCode($pValue);
                break;
            default:
                throw new PHPExcel_Exception('Invalid datatype: ' . $pDataType);
                break;
        }

        // set the datatype
        $this->dataType = $pDataType;

        return $this->notifyCacheController();
    }

    /**
     *    Get calculated cell value
     *
     *    @deprecated        Since version 1.7.8 for planned changes to cell for array formula handling
     *
     *    @param    boolean $resetLog  Whether the calculation engine logger should be reset or not
     *    @return    mixed
     *    @throws    PHPExcel_Exception
     */
    public function getCalculatedValue($resetLog = true)
    {
//echo 'Cell '.$this->getCoordinate().' value is a '.$this->dataType.' with a value of '.$this->getValue().PHP_EOL;
        if ($this->dataType == PHPExcel_Cell_DataType::TYPE_FORMULA) {
            try {
//echo 'Cell value for '.$this->getCoordinate().' is a formula: Calculating value'.PHP_EOL;
                $result = PHPExcel_Calculation::getInstance(
                    $this->getWorksheet()->getParent()
                )->calculateCellValue($this, $resetLog);
//echo $this->getCoordinate().' calculation result is '.$result.PHP_EOL;
                //    We don't yet handle array returns
                if (is_array($result)) {
                    while (is_array($result)) {
                        $result = array_pop($result);
                    }
                }
            } catch (PHPExcel_Exception $ex) {
                if (($ex->getMessage() === 'Unable to access External Workbook') && ($this->calculatedValue !== null)) {
//echo 'Returning fallback value of '.$this->calculatedValue.' for cell '.$this->getCoordinate().PHP_EOL;
                    return $this->calculatedValue; // Fallback for calculations referencing external files.
                }
//echo 'Calculation Exception: '.$ex->getMessage().PHP_EOL;
                $result = '#N/A';
                throw new PHPExcel_Calculation_Exception(
                    $this->getWorksheet()->getTitle().'!'.$this->getCoordinate().' -> '.$ex->getMessage()
                );
            }

            if ($result === '#Not Yet Implemented') {
//echo 'Returning fallback value of '.$this->calculatedValue.' for cell '.$this->getCoordinate().PHP_EOL;
                return $this->calculatedValue; // Fallback if calculation engine does not support the formula.
            }
//echo 'Returning calculated value of '.$result.' for cell '.$this->getCoordinate().PHP_EOL;
            return $result;
        } elseif ($this->value instanceof PHPExcel_RichText) {
//        echo 'Cell value for '.$this->getCoordinate().' is rich text: Returning data value of '.$this->value.'<br />';
            return $this->value->getPlainText();
        }
//        echo 'Cell value for '.$this->getCoordinate().' is not a formula: Returning data value of '.$this->value.'<br />';
        return $this->value;
    }

    /**
     *    Set old calculated value (cached)
     *
     *    @param    mixed $pValue    Value
     *    @return    PHPExcel_Cell
     */
    public function setCalculatedValue($pValue = null)
    {
        if ($pValue !== null) {
            $this->calculatedValue = (is_numeric($pValue)) ? (float) $pValue : $pValue;
        }

        return $this->notifyCacheController();
    }

    /**
     *    Get old calculated value (cached)
     *    This returns the value last calculated by MS Excel or whichever spreadsheet program was used to
     *        create the original spreadsheet file.
     *    Note that this value is not guaranteed to refelect the actual calculated value because it is
     *        possible that auto-calculation was disabled in the original spreadsheet, and underlying data
     *        values used by the formula have changed since it was last calculated.
     *
     *    @return    mixed
     */
    public function getOldCalculatedValue()
    {
        return $this->calculatedValue;
    }

    /**
     *    Get cell data type
     *
     *    @return string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     *    Set cell data type
     *
     *    @param    string $pDataType
     *    @return    PHPExcel_Cell
     */
    public function setDataType($pDataType = PHPExcel_Cell_DataType::TYPE_STRING)
    {
        if ($pDataType == PHPExcel_Cell_DataType::TYPE_STRING2) {
            $pDataType = PHPExcel_Cell_DataType::TYPE_STRING;
        }
        $this->dataType = $pDataType;

        return $this->notifyCacheController();
    }

    /**
     *  Identify if the cell contains a formula
     *
     *  @return boolean
     */
    public function isFormula()
    {
        return $this->dataType == PHPExcel_Cell_DataType::TYPE_FORMULA;
    }

    /**
     *    Does this cell contain Data validation rules?
     *
     *    @return    boolean
     *    @throws    PHPExcel_Exception
     */
    public function hasDataValidation()
    {
        if (!isset($this->parent)) {
            throw new PHPExcel_Exception('Cannot check for data validation when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->dataValidationExists($this->getCoordinate());
    }

    /**
     *    Get Data validation rules
     *
     *    @return    PHPExcel_Cell_DataValidation
     *    @throws    PHPExcel_Exception
     */
    public function getDataValidation()
    {
        if (!isset($this->parent)) {
            throw new PHPExcel_Exception('Cannot get data validation for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getDataValidation($this->getCoordinate());
    }

    /**
     *    Set Data validation rules
     *
     *    @param    PHPExcel_Cell_DataValidation    $pDataValidation
     *    @return    PHPExcel_Cell
     *    @throws    PHPExcel_Exception
     */
    public function setDataValidation(PHPExcel_Cell_DataValidation $pDataValidation = null)
    {
        if (!isset($this->parent)) {
            throw new PHPExcel_Exception('Cannot set data validation for cell that is not bound to a worksheet');
        }

        $this->getWorksheet()->setDataValidation($this->getCoordinate(), $pDataValidation);

        return $this->notifyCacheController();
    }

    /**
     *    Does this cell contain a Hyperlink?
     *
     *    @return boolean
     *    @throws    PHPExcel_Exception
     */
    public function hasHyperlink()
    {
        if (!isset($this->parent)) {
            throw new PHPExcel_Exception('Cannot check for hyperlink when cell is not bound to a worksheet');
        }

        return $this->getWorksheet()->hyperlinkExists($this->getCoordinate());
    }

    /**
     *    Get Hyperlink
     *
     *    @return    PHPExcel_Cell_Hyperlink
     *    @throws    PHPExcel_Exception
     */
    public function getHyperlink()
    {
        if (!isset($this->parent)) {
            throw new PHPExcel_Exception('Cannot get hyperlink for cell that is not bound to a worksheet');
        }

        return $this->getWorksheet()->getHyperlink($this->getCoordinate());
    }

    /**
     *    Set Hyperlink
     *
     *    @param    PHPExcel_Cell_Hyperlink    $pHyperlink
     *    @return    PHPExcel_Cell
     *    @throws    PHPExcel_Exception
     */
    public function setHyperlink(PHPExcel_Cell_Hyperlink $pHyperlink = null)
    {
        if (!isset($this->parent)) {
            throw new PHPExcel_Exception('Cannot set hyperlink for cell that is not bound to a worksheet');
        }

        $this->getWorksheet()->setHyperlink($this->getCoordinate(), $pHyperlink);

        return $this->notifyCacheController();
    }

    /**
     *    Get parent worksheet
     *
     *    @return PHPExcel_CachedObjectStorage_CacheBase
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     *    Get parent worksheet
     *
     *    @return PHPExcel_Worksheet
     */
    public function getWorksheet()
    {
        return $this->parent->getParent();
    }

    /**
     *    Is this cell in a merge range
     *
     *    @return boolean
     */
    public function isInMergeRange()
    {
        return (boolean) $this->getMergeRange();
    }

    /**
     *    Is this cell the master (top left cell) in a merge range (that holds the actual data value)
     *
     *    @return boolean
     */
    public function isMergeRangeValueCell()
    {
        if ($mergeRange = $this->getMergeRange()) {
            $mergeRange = PHPExcel_Cell::splitRange($mergeRange);
            list($startCell) = $mergeRange[0];
            if ($this->getCoordinate() === $startCell) {
                return true;
            }
        }
        return false;
    }

    /**
     *    If this cell is in a merge range, then return the range
     *
     *    @return string
     */
    public function getMergeRange()
    {
        foreach ($this->getWorksheet()->getMergeCells() as $mergeRange) {
            if ($this->isInRange($mergeRange)) {
                return $mergeRange;
            }
        }
        return false;
    }

    /**
     *    Get cell style
     *
     *    @return    PHPExcel_Style
     */
    public function getStyle()
    {
        return $this->getWorksheet()->getStyle($this->getCoordinate());
    }

    /**
     *    Re-bind parent
     *
     *    @param    PHPExcel_Worksheet $parent
     *    @return    PHPExcel_Cell
     */
    public function rebindParent(PHPExcel_Worksheet $parent)
    {
        $this->parent = $parent->getCellCacheController();

        return $this->notifyCacheController();
    }

    /**
     *    Is cell in a specific range?
     *
     *    @param    string    $pRange        Cell range (e.g. A1:A1)
     *    @return    boolean
     */
    public function isInRange($pRange = 'A1:A1')
    {
        list($rangeStart, $rangeEnd) = self::rangeBoundaries($pRange);

        // Translate properties
        $myColumn = self::columnIndexFromString($this->getColumn());
        $myRow    = $this->getRow();

        // Verify if cell is in range
        return (($rangeStart[0] <= $myColumn) && ($rangeEnd[0] >= $myColumn) &&
                ($rangeStart[1] <= $myRow) && ($rangeEnd[1] >= $myRow)
               );
    }

    /**
     *    Coordinate from string
     *
     *    @param    string    $pCoordinateString
     *    @return    array    Array containing column and row (indexes 0 and 1)
     *    @throws    PHPExcel_Exception
     */
    public static function coordinateFromString($pCoordinateString = 'A1')
    {
        if (preg_match("/^([$]?[A-Z]{1,3})([$]?\d{1,7})$/", $pCoordinateString, $matches)) {
            return array($matches[1],$matches[2]);
        } elseif ((strpos($pCoordinateString, ':') !== false) || (strpos($pCoordinateString, ',') !== false)) {
            throw new PHPExcel_Exception('Cell coordinate string can not be a range of cells');
        } elseif ($pCoordinateString == '') {
            throw new PHPExcel_Exception('Cell coordinate can not be zero-length string');
        }

        throw new PHPExcel_Exception('Invalid cell coordinate '.$pCoordinateString);
    }

    /**
     *    Make string row, column or cell coordinate absolute
     *
     *    @param    string    $pCoordinateString        e.g. 'A' or '1' or 'A1'
     *                    Note that this value can be a row or column reference as well as a cell reference
     *    @return    string    Absolute coordinate        e.g. '$A' or '$1' or '$A$1'
     *    @throws    PHPExcel_Exception
     */
    public static function absoluteReference($pCoordinateString = 'A1')
    {
        if (strpos($pCoordinateString, ':') === false && strpos($pCoordinateString, ',') === false) {
            // Split out any worksheet name from the reference
            $worksheet = '';
            $cellAddress = explode('!', $pCoordinateString);
            if (count($cellAddress) > 1) {
                list($worksheet, $pCoordinateString) = $cellAddress;
            }
            if ($worksheet > '') {
                $worksheet .= '!';
            }

            // Create absolute coordinate
            if (ctype_digit($pCoordinateString)) {
                return $worksheet . '$' . $pCoordinateString;
            } elseif (ctype_alpha($pCoordinateString)) {
                return $worksheet . '$' . strtoupper($pCoordinateString);
            }
            return $worksheet . self::absoluteCoordinate($pCoordinateString);
        }

        throw new PHPExcel_Exception('Cell coordinate string can not be a range of cells');
    }

    /**
     *    Make string coordinate absolute
     *
     *    @param    string    $pCoordinateString        e.g. 'A1'
     *    @return    string    Absolute coordinate        e.g. '$A$1'
     *    @throws    PHPExcel_Exception
     */
    public static function absoluteCoordinate($pCoordinateString = 'A1')
    {
        if (strpos($pCoordinateString, ':') === false && strpos($pCoordinateString, ',') === false) {
            // Split out any worksheet name from the coordinate
            $worksheet = '';
            $cellAddress = explode('!', $pCoordinateString);
            if (count($cellAddress) > 1) {
                list($worksheet, $pCoordinateString) = $cellAddress;
            }
            if ($worksheet > '') {
                $worksheet .= '!';
            }

            // Create absolute coordinate
            list($column, $row) = self::coordinateFromString($pCoordinateString);
            $column = ltrim($column, '$');
            $row = ltrim($row, '$');
            return $worksheet . '$' . $column . '$' . $row;
        }

        throw new PHPExcel_Exception('Cell coordinate string can not be a range of cells');
    }

    /**
     *    Split range into coordinate strings
     *
     *    @param    string    $pRange        e.g. 'B4:D9' or 'B4:D9,H2:O11' or 'B4'
     *    @return    array    Array containg one or more arrays containing one or two coordinate strings
     *                                e.g. array('B4','D9') or array(array('B4','D9'),array('H2','O11'))
     *                                        or array('B4')
     */
    public static function splitRange($pRange = 'A1:A1')
    {
        // Ensure $pRange is a valid range
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }

        $exploded = explode(',', $pRange);
        $counter = count($exploded);
        for ($i = 0; $i < $counter; ++$i) {
            $exploded[$i] = explode(':', $exploded[$i]);
        }
        return $exploded;
    }

    /**
     *    Build range from coordinate strings
     *
     *    @param    array    $pRange    Array containg one or more arrays containing one or two coordinate strings
     *    @return    string    String representation of $pRange
     *    @throws    PHPExcel_Exception
     */
    public static function buildRange($pRange)
    {
        // Verify range
        if (!is_array($pRange) || empty($pRange) || !is_array($pRange[0])) {
            throw new PHPExcel_Exception('Range does not contain any information');
        }

        // Build range
        $imploded = array();
        $counter = count($pRange);
        for ($i = 0; $i < $counter; ++$i) {
            $pRange[$i] = implode(':', $pRange[$i]);
        }
        $imploded = implode(',', $pRange);

        return $imploded;
    }

    /**
     *    Calculate range boundaries
     *
     *    @param    string    $pRange        Cell range (e.g. A1:A1)
     *    @return    array    Range coordinates array(Start Cell, End Cell)
     *                    where Start Cell and End Cell are arrays (Column Number, Row Number)
     */
    public static function rangeBoundaries($pRange = 'A1:A1')
    {
        // Ensure $pRange is a valid range
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }

        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        // Extract range
        if (strpos($pRange, ':') === false) {
            $rangeA = $rangeB = $pRange;
        } else {
            list($rangeA, $rangeB) = explode(':', $pRange);
        }

        // Calculate range outer borders
        $rangeStart = self::coordinateFromString($rangeA);
        $rangeEnd    = self::coordinateFromString($rangeB);

        // Translate column into index
        $rangeStart[0]    = self::columnIndexFromString($rangeStart[0]);
        $rangeEnd[0]    = self::columnIndexFromString($rangeEnd[0]);

        return array($rangeStart, $rangeEnd);
    }

    /**
     *    Calculate range dimension
     *
     *    @param    string    $pRange        Cell range (e.g. A1:A1)
     *    @return    array    Range dimension (width, height)
     */
    public static function rangeDimension($pRange = 'A1:A1')
    {
        // Calculate range outer borders
        list($rangeStart, $rangeEnd) = self::rangeBoundaries($pRange);

        return array( ($rangeEnd[0] - $rangeStart[0] + 1), ($rangeEnd[1] - $rangeStart[1] + 1) );
    }

    /**
     *    Calculate range boundaries
     *
     *    @param    string    $pRange        Cell range (e.g. A1:A1)
     *    @return    array    Range coordinates array(Start Cell, End Cell)
     *                    where Start Cell and End Cell are arrays (Column ID, Row Number)
     */
    public static function getRangeBoundaries($pRange = 'A1:A1')
    {
        // Ensure $pRange is a valid range
        if (empty($pRange)) {
            $pRange = self::DEFAULT_RANGE;
        }

        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        // Extract range
        if (strpos($pRange, ':') === false) {
            $rangeA = $rangeB = $pRange;
        } else {
            list($rangeA, $rangeB) = explode(':', $pRange);
        }

        return array( self::coordinateFromString($rangeA), self::coordinateFromString($rangeB));
    }

    /**
     *    Column index from string
     *
     *    @param    string $pString
     *    @return    int Column index (base 1 !!!)
     */
    public static function columnIndexFromString($pString = 'A')
    {
        //    Using a lookup cache adds a slight memory overhead, but boosts speed
        //    caching using a static within the method is faster than a class static,
        //        though it's additional memory overhead
        static $_indexCache = array();

        if (isset($_indexCache[$pString])) {
            return $_indexCache[$pString];
        }
        //    It's surprising how costly the strtoupper() and ord() calls actually are, so we use a lookup array rather than use ord()
        //        and make it case insensitive to get rid of the strtoupper() as well. Because it's a static, there's no significant
        //        memory overhead either
        static $_columnLookup = array(
            'A' => 1, 'B' => 2, 'C' => 3, 'D' => 4, 'E' => 5, 'F' => 6, 'G' => 7, 'H' => 8, 'I' => 9, 'J' => 10, 'K' => 11, 'L' => 12, 'M' => 13,
            'N' => 14, 'O' => 15, 'P' => 16, 'Q' => 17, 'R' => 18, 'S' => 19, 'T' => 20, 'U' => 21, 'V' => 22, 'W' => 23, 'X' => 24, 'Y' => 25, 'Z' => 26,
            'a' => 1, 'b' => 2, 'c' => 3, 'd' => 4, 'e' => 5, 'f' => 6, 'g' => 7, 'h' => 8, 'i' => 9, 'j' => 10, 'k' => 11, 'l' => 12, 'm' => 13,
            'n' => 14, 'o' => 15, 'p' => 16, 'q' => 17, 'r' => 18, 's' => 19, 't' => 20, 'u' => 21, 'v' => 22, 'w' => 23, 'x' => 24, 'y' => 25, 'z' => 26
        );

        //    We also use the language construct isset() rather than the more costly strlen() function to match the length of $pString
        //        for improved performance
        if (isset($pString{0})) {
            if (!isset($pString{1})) {
                $_indexCache[$pString] = $_columnLookup[$pString];
                return $_indexCache[$pString];
            } elseif (!isset($pString{2})) {
                $_indexCache[$pString] = $_columnLookup[$pString{0}] * 26 + $_columnLookup[$pString{1}];
                return $_indexCache[$pString];
            } elseif (!isset($pString{3})) {
                $_indexCache[$pString] = $_columnLookup[$pString{0}] * 676 + $_columnLookup[$pString{1}] * 26 + $_columnLookup[$pString{2}];
                return $_indexCache[$pString];
            }
        }
        throw new PHPExcel_Exception("Column string index can not be " . ((isset($pString{0})) ? "longer than 3 characters" : "empty"));
    }

    /**
     *    String from columnindex
     *
     *    @param    int $pColumnIndex Column index (base 0 !!!)
     *    @return    string
     */
    public static function stringFromColumnIndex($pColumnIndex = 0)
    {
        //    Using a lookup cache adds a slight memory overhead, but boosts speed
        //    caching using a static within the method is faster than a class static,
        //        though it's additional memory overhead
        static $_indexCache = array();

        if (!isset($_indexCache[$pColumnIndex])) {
            // Determine column string
            if ($pColumnIndex < 26) {
                $_indexCache[$pColumnIndex] = chr(65 + $pColumnIndex);
            } elseif ($pColumnIndex < 702) {
                $_indexCache[$pColumnIndex] = chr(64 + ($pColumnIndex / 26)) .
                                              chr(65 + $pColumnIndex % 26);
            } else {
                $_indexCache[$pColumnIndex] = chr(64 + (($pColumnIndex - 26) / 676)) .
                                              chr(65 + ((($pColumnIndex - 26) % 676) / 26)) .
                                              chr(65 + $pColumnIndex % 26);
            }
        }
        return $_indexCache[$pColumnIndex];
    }

    /**
     *    Extract all cell references in range
     *
     *    @param    string    $pRange        Range (e.g. A1 or A1:C10 or A1:E10 A20:E25)
     *    @return    array    Array containing single cell references
     */
    public static function extractAllCellReferencesInRange($pRange = 'A1')
    {
        // Returnvalue
        $returnValue = array();

        // Explode spaces
        $cellBlocks = explode(' ', str_replace('$', '', strtoupper($pRange)));
        foreach ($cellBlocks as $cellBlock) {
            // Single cell?
            if (strpos($cellBlock, ':') === false && strpos($cellBlock, ',') === false) {
                $returnValue[] = $cellBlock;
                continue;
            }

            // Range...
            $ranges = self::splitRange($cellBlock);
            foreach ($ranges as $range) {
                // Single cell?
                if (!isset($range[1])) {
                    $returnValue[] = $range[0];
                    continue;
                }

                // Range...
                list($rangeStart, $rangeEnd)    = $range;
                sscanf($rangeStart, '%[A-Z]%d', $startCol, $startRow);
                sscanf($rangeEnd, '%[A-Z]%d', $endCol, $endRow);
                ++$endCol;

                // Current data
                $currentCol = $startCol;
                $currentRow = $startRow;

                // Loop cells
                while ($currentCol != $endCol) {
                    while ($currentRow <= $endRow) {
                        $returnValue[] = $currentCol.$currentRow;
                        ++$currentRow;
                    }
                    ++$currentCol;
                    $currentRow = $startRow;
                }
            }
        }

        //    Sort the result by column and row
        $sortKeys = array();
        foreach (array_unique($returnValue) as $coord) {
            sscanf($coord, '%[A-Z]%d', $column, $row);
            $sortKeys[sprintf('%3s%09d', $column, $row)] = $coord;
        }
        ksort($sortKeys);

        // Return value
        return array_values($sortKeys);
    }

    /**
     * Compare 2 cells
     *
     * @param    PHPExcel_Cell    $a    Cell a
     * @param    PHPExcel_Cell    $b    Cell b
     * @return    int        Result of comparison (always -1 or 1, never zero!)
     */
    public static function compareCells(PHPExcel_Cell $a, PHPExcel_Cell $b)
    {
        if ($a->getRow() < $b->getRow()) {
            return -1;
        } elseif ($a->getRow() > $b->getRow()) {
            return 1;
        } elseif (self::columnIndexFromString($a->getColumn()) < self::columnIndexFromString($b->getColumn())) {
            return -1;
        } else {
            return 1;
        }
    }

    /**
     * Get value binder to use
     *
     * @return PHPExcel_Cell_IValueBinder
     */
    public static function getValueBinder()
    {
        if (self::$valueBinder === null) {
            self::$valueBinder = new PHPExcel_Cell_DefaultValueBinder();
        }

        return self::$valueBinder;
    }

    /**
     * Set value binder to use
     *
     * @param PHPExcel_Cell_IValueBinder $binder
     * @throws PHPExcel_Exception
     */
    public static function setValueBinder(PHPExcel_Cell_IValueBinder $binder = null)
    {
        if ($binder === null) {
            throw new PHPExcel_Exception("A PHPExcel_Cell_IValueBinder is required for PHPExcel to function correctly.");
        }

        self::$valueBinder = $binder;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
    public function __clone()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $value) {
            if ((is_object($value)) && ($key != 'parent')) {
                $this->$key = clone $value;
            } else {
                $this->$key = $value;
            }
        }
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
     * @return PHPExcel_Cell
     */
    public function setXfIndex($pValue = 0)
    {
        $this->xfIndex = $pValue;

        return $this->notifyCacheController();
    }

    /**
     *    @deprecated        Since version 1.7.8 for planned changes to cell for array formula handling
     */
    public function setFormulaAttributes($pAttributes)
    {
        $this->formulaAttributes = $pAttributes;
        return $this;
    }

    /**
     *    @deprecated        Since version 1.7.8 for planned changes to cell for array formula handling
     */
    public function getFormulaAttributes()
    {
        return $this->formulaAttributes;
    }

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->getValue();
    }
}
