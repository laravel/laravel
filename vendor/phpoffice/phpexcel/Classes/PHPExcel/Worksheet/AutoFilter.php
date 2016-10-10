<?php

/**
 * PHPExcel_Worksheet_AutoFilter
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
class PHPExcel_Worksheet_AutoFilter
{
    /**
     * Autofilter Worksheet
     *
     * @var PHPExcel_Worksheet
     */
    private $workSheet;


    /**
     * Autofilter Range
     *
     * @var string
     */
    private $range = '';


    /**
     * Autofilter Column Ruleset
     *
     * @var array of PHPExcel_Worksheet_AutoFilter_Column
     */
    private $columns = array();


    /**
     * Create a new PHPExcel_Worksheet_AutoFilter
     *
     *    @param    string        $pRange        Cell range (i.e. A1:E10)
     * @param PHPExcel_Worksheet $pSheet
     */
    public function __construct($pRange = '', PHPExcel_Worksheet $pSheet = null)
    {
        $this->range = $pRange;
        $this->workSheet = $pSheet;
    }

    /**
     * Get AutoFilter Parent Worksheet
     *
     * @return PHPExcel_Worksheet
     */
    public function getParent()
    {
        return $this->workSheet;
    }

    /**
     * Set AutoFilter Parent Worksheet
     *
     * @param PHPExcel_Worksheet $pSheet
     * @return PHPExcel_Worksheet_AutoFilter
     */
    public function setParent(PHPExcel_Worksheet $pSheet = null)
    {
        $this->workSheet = $pSheet;

        return $this;
    }

    /**
     * Get AutoFilter Range
     *
     * @return string
     */
    public function getRange()
    {
        return $this->range;
    }

    /**
     *    Set AutoFilter Range
     *
     *    @param    string        $pRange        Cell range (i.e. A1:E10)
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter
     */
    public function setRange($pRange = '')
    {
        // Uppercase coordinate
        $cellAddress = explode('!', strtoupper($pRange));
        if (count($cellAddress) > 1) {
            list($worksheet, $pRange) = $cellAddress;
        }

        if (strpos($pRange, ':') !== false) {
            $this->range = $pRange;
        } elseif (empty($pRange)) {
            $this->range = '';
        } else {
            throw new PHPExcel_Exception('Autofilter must be set on a range of cells.');
        }

        if (empty($pRange)) {
            //    Discard all column rules
            $this->columns = array();
        } else {
            //    Discard any column rules that are no longer valid within this range
            list($rangeStart, $rangeEnd) = PHPExcel_Cell::rangeBoundaries($this->range);
            foreach ($this->columns as $key => $value) {
                $colIndex = PHPExcel_Cell::columnIndexFromString($key);
                if (($rangeStart[0] > $colIndex) || ($rangeEnd[0] < $colIndex)) {
                    unset($this->columns[$key]);
                }
            }
        }

        return $this;
    }

    /**
     * Get all AutoFilter Columns
     *
     * @throws    PHPExcel_Exception
     * @return array of PHPExcel_Worksheet_AutoFilter_Column
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * Validate that the specified column is in the AutoFilter range
     *
     * @param    string    $column            Column name (e.g. A)
     * @throws    PHPExcel_Exception
     * @return    integer    The column offset within the autofilter range
     */
    public function testColumnInRange($column)
    {
        if (empty($this->range)) {
            throw new PHPExcel_Exception("No autofilter range is defined.");
        }

        $columnIndex = PHPExcel_Cell::columnIndexFromString($column);
        list($rangeStart, $rangeEnd) = PHPExcel_Cell::rangeBoundaries($this->range);
        if (($rangeStart[0] > $columnIndex) || ($rangeEnd[0] < $columnIndex)) {
            throw new PHPExcel_Exception("Column is outside of current autofilter range.");
        }

        return $columnIndex - $rangeStart[0];
    }

    /**
     * Get a specified AutoFilter Column Offset within the defined AutoFilter range
     *
     * @param    string    $pColumn        Column name (e.g. A)
     * @throws    PHPExcel_Exception
     * @return integer    The offset of the specified column within the autofilter range
     */
    public function getColumnOffset($pColumn)
    {
        return $this->testColumnInRange($pColumn);
    }

    /**
     * Get a specified AutoFilter Column
     *
     * @param    string    $pColumn        Column name (e.g. A)
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function getColumn($pColumn)
    {
        $this->testColumnInRange($pColumn);

        if (!isset($this->columns[$pColumn])) {
            $this->columns[$pColumn] = new PHPExcel_Worksheet_AutoFilter_Column($pColumn, $this);
        }

        return $this->columns[$pColumn];
    }

    /**
     * Get a specified AutoFilter Column by it's offset
     *
     * @param    integer    $pColumnOffset        Column offset within range (starting from 0)
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function getColumnByOffset($pColumnOffset = 0)
    {
        list($rangeStart, $rangeEnd) = PHPExcel_Cell::rangeBoundaries($this->range);
        $pColumn = PHPExcel_Cell::stringFromColumnIndex($rangeStart[0] + $pColumnOffset - 1);

        return $this->getColumn($pColumn);
    }

    /**
     *    Set AutoFilter
     *
     *    @param    PHPExcel_Worksheet_AutoFilter_Column|string        $pColumn
     *            A simple string containing a Column ID like 'A' is permitted
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter
     */
    public function setColumn($pColumn)
    {
        if ((is_string($pColumn)) && (!empty($pColumn))) {
            $column = $pColumn;
        } elseif (is_object($pColumn) && ($pColumn instanceof PHPExcel_Worksheet_AutoFilter_Column)) {
            $column = $pColumn->getColumnIndex();
        } else {
            throw new PHPExcel_Exception("Column is not within the autofilter range.");
        }
        $this->testColumnInRange($column);

        if (is_string($pColumn)) {
            $this->columns[$pColumn] = new PHPExcel_Worksheet_AutoFilter_Column($pColumn, $this);
        } elseif (is_object($pColumn) && ($pColumn instanceof PHPExcel_Worksheet_AutoFilter_Column)) {
            $pColumn->setParent($this);
            $this->columns[$column] = $pColumn;
        }
        ksort($this->columns);

        return $this;
    }

    /**
     * Clear a specified AutoFilter Column
     *
     * @param    string  $pColumn    Column name (e.g. A)
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet_AutoFilter
     */
    public function clearColumn($pColumn)
    {
        $this->testColumnInRange($pColumn);

        if (isset($this->columns[$pColumn])) {
            unset($this->columns[$pColumn]);
        }

        return $this;
    }

    /**
     *    Shift an AutoFilter Column Rule to a different column
     *
     *    Note: This method bypasses validation of the destination column to ensure it is within this AutoFilter range.
     *        Nor does it verify whether any column rule already exists at $toColumn, but will simply overrideany existing value.
     *        Use with caution.
     *
     *    @param    string    $fromColumn        Column name (e.g. A)
     *    @param    string    $toColumn        Column name (e.g. B)
     *    @return PHPExcel_Worksheet_AutoFilter
     */
    public function shiftColumn($fromColumn = null, $toColumn = null)
    {
        $fromColumn = strtoupper($fromColumn);
        $toColumn = strtoupper($toColumn);

        if (($fromColumn !== null) && (isset($this->columns[$fromColumn])) && ($toColumn !== null)) {
            $this->columns[$fromColumn]->setParent();
            $this->columns[$fromColumn]->setColumnIndex($toColumn);
            $this->columns[$toColumn] = $this->columns[$fromColumn];
            $this->columns[$toColumn]->setParent($this);
            unset($this->columns[$fromColumn]);

            ksort($this->columns);
        }

        return $this;
    }


    /**
     *    Test if cell value is in the defined set of values
     *
     *    @param    mixed        $cellValue
     *    @param    mixed[]        $dataSet
     *    @return boolean
     */
    private static function filterTestInSimpleDataSet($cellValue, $dataSet)
    {
        $dataSetValues = $dataSet['filterValues'];
        $blanks = $dataSet['blanks'];
        if (($cellValue == '') || ($cellValue === null)) {
            return $blanks;
        }
        return in_array($cellValue, $dataSetValues);
    }

    /**
     *    Test if cell value is in the defined set of Excel date values
     *
     *    @param    mixed        $cellValue
     *    @param    mixed[]        $dataSet
     *    @return boolean
     */
    private static function filterTestInDateGroupSet($cellValue, $dataSet)
    {
        $dateSet = $dataSet['filterValues'];
        $blanks = $dataSet['blanks'];
        if (($cellValue == '') || ($cellValue === null)) {
            return $blanks;
        }

        if (is_numeric($cellValue)) {
            $dateValue = PHPExcel_Shared_Date::ExcelToPHP($cellValue);
            if ($cellValue < 1) {
                //    Just the time part
                $dtVal = date('His', $dateValue);
                $dateSet = $dateSet['time'];
            } elseif ($cellValue == floor($cellValue)) {
                //    Just the date part
                $dtVal = date('Ymd', $dateValue);
                $dateSet = $dateSet['date'];
            } else {
                //    date and time parts
                $dtVal = date('YmdHis', $dateValue);
                $dateSet = $dateSet['dateTime'];
            }
            foreach ($dateSet as $dateValue) {
                //    Use of substr to extract value at the appropriate group level
                if (substr($dtVal, 0, strlen($dateValue)) == $dateValue) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     *    Test if cell value is within a set of values defined by a ruleset
     *
     *    @param    mixed        $cellValue
     *    @param    mixed[]        $ruleSet
     *    @return boolean
     */
    private static function filterTestInCustomDataSet($cellValue, $ruleSet)
    {
        $dataSet = $ruleSet['filterRules'];
        $join = $ruleSet['join'];
        $customRuleForBlanks = isset($ruleSet['customRuleForBlanks']) ? $ruleSet['customRuleForBlanks'] : false;

        if (!$customRuleForBlanks) {
            //    Blank cells are always ignored, so return a FALSE
            if (($cellValue == '') || ($cellValue === null)) {
                return false;
            }
        }
        $returnVal = ($join == PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_AND);
        foreach ($dataSet as $rule) {
            if (is_numeric($rule['value'])) {
                //    Numeric values are tested using the appropriate operator
                switch ($rule['operator']) {
                    case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL:
                        $retVal    = ($cellValue == $rule['value']);
                        break;
                    case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL:
                        $retVal    = ($cellValue != $rule['value']);
                        break;
                    case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN:
                        $retVal    = ($cellValue > $rule['value']);
                        break;
                    case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL:
                        $retVal    = ($cellValue >= $rule['value']);
                        break;
                    case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN:
                        $retVal    = ($cellValue < $rule['value']);
                        break;
                    case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL:
                        $retVal    = ($cellValue <= $rule['value']);
                        break;
                }
            } elseif ($rule['value'] == '') {
                switch ($rule['operator']) {
                    case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_EQUAL:
                        $retVal    = (($cellValue == '') || ($cellValue === null));
                        break;
                    case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_NOTEQUAL:
                        $retVal    = (($cellValue != '') && ($cellValue !== null));
                        break;
                    default:
                        $retVal    = true;
                        break;
                }
            } else {
                //    String values are always tested for equality, factoring in for wildcards (hence a regexp test)
                $retVal    = preg_match('/^'.$rule['value'].'$/i', $cellValue);
            }
            //    If there are multiple conditions, then we need to test both using the appropriate join operator
            switch ($join) {
                case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_OR:
                    $returnVal = $returnVal || $retVal;
                    //    Break as soon as we have a TRUE match for OR joins,
                    //        to avoid unnecessary additional code execution
                    if ($returnVal) {
                        return $returnVal;
                    }
                    break;
                case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_AND:
                    $returnVal = $returnVal && $retVal;
                    break;
            }
        }

        return $returnVal;
    }

    /**
     *    Test if cell date value is matches a set of values defined by a set of months
     *
     *    @param    mixed        $cellValue
     *    @param    mixed[]        $monthSet
     *    @return boolean
     */
    private static function filterTestInPeriodDateSet($cellValue, $monthSet)
    {
        //    Blank cells are always ignored, so return a FALSE
        if (($cellValue == '') || ($cellValue === null)) {
            return false;
        }

        if (is_numeric($cellValue)) {
            $dateValue = date('m', PHPExcel_Shared_Date::ExcelToPHP($cellValue));
            if (in_array($dateValue, $monthSet)) {
                return true;
            }
        }

        return false;
    }

    /**
     *    Search/Replace arrays to convert Excel wildcard syntax to a regexp syntax for preg_matching
     *
     *    @var    array
     */
    private static $fromReplace = array('\*', '\?', '~~', '~.*', '~.?');
    private static $toReplace   = array('.*', '.',  '~',  '\*',  '\?');


    /**
     *    Convert a dynamic rule daterange to a custom filter range expression for ease of calculation
     *
     *    @param    string                                        $dynamicRuleType
     *    @param    PHPExcel_Worksheet_AutoFilter_Column        &$filterColumn
     *    @return mixed[]
     */
    private function dynamicFilterDateRange($dynamicRuleType, &$filterColumn)
    {
        $rDateType = PHPExcel_Calculation_Functions::getReturnDateType();
        PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_PHP_NUMERIC);
        $val = $maxVal = null;

        $ruleValues = array();
        $baseDate = PHPExcel_Calculation_DateTime::DATENOW();
        //    Calculate start/end dates for the required date range based on current date
        switch ($dynamicRuleType) {
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK:
                $baseDate = strtotime('-7 days', $baseDate);
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK:
                $baseDate = strtotime('-7 days', $baseDate);
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH:
                $baseDate = strtotime('-1 month', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH:
                $baseDate = strtotime('+1 month', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER:
                $baseDate = strtotime('-3 month', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER:
                $baseDate = strtotime('+3 month', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR:
                $baseDate = strtotime('-1 year', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR:
                $baseDate = strtotime('+1 year', gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));
                break;
        }

        switch ($dynamicRuleType) {
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_TODAY:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW:
                $maxVal = (int) PHPExcel_Shared_Date::PHPtoExcel(strtotime('+1 day', $baseDate));
                $val = (int) PHPExcel_Shared_Date::PHPToExcel($baseDate);
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE:
                $maxVal = (int) PHPExcel_Shared_Date::PHPtoExcel(strtotime('+1 day', $baseDate));
                $val = (int) PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, 1, 1, date('Y', $baseDate)));
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISYEAR:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR:
                $maxVal = (int) PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, 31, 12, date('Y', $baseDate)));
                ++$maxVal;
                $val = (int) PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, 1, 1, date('Y', $baseDate)));
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISQUARTER:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER:
                $thisMonth = date('m', $baseDate);
                $thisQuarter = floor(--$thisMonth / 3);
                $maxVal = (int) PHPExcel_Shared_Date::PHPtoExcel(gmmktime(0, 0, 0, date('t', $baseDate), (1+$thisQuarter)*3, date('Y', $baseDate)));
                ++$maxVal;
                $val = (int) PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, 1, 1+$thisQuarter*3, date('Y', $baseDate)));
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISMONTH:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH:
                $maxVal = (int) PHPExcel_Shared_Date::PHPtoExcel(gmmktime(0, 0, 0, date('t', $baseDate), date('m', $baseDate), date('Y', $baseDate)));
                ++$maxVal;
                $val = (int) PHPExcel_Shared_Date::PHPToExcel(gmmktime(0, 0, 0, 1, date('m', $baseDate), date('Y', $baseDate)));
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_THISWEEK:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK:
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK:
                $dayOfWeek = date('w', $baseDate);
                $val = (int) PHPExcel_Shared_Date::PHPToExcel($baseDate) - $dayOfWeek;
                $maxVal = $val + 7;
                break;
        }

        switch ($dynamicRuleType) {
            //    Adjust Today dates for Yesterday and Tomorrow
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY:
                --$maxVal;
                --$val;
                break;
            case PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW:
                ++$maxVal;
                ++$val;
                break;
        }

        //    Set the filter column rule attributes ready for writing
        $filterColumn->setAttributes(array('val' => $val, 'maxVal' => $maxVal));

        //    Set the rules for identifying rows for hide/show
        $ruleValues[] = array('operator' => PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL, 'value' => $val);
        $ruleValues[] = array('operator' => PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN, 'value' => $maxVal);
        PHPExcel_Calculation_Functions::setReturnDateType($rDateType);

        return array('method' => 'filterTestInCustomDataSet', 'arguments' => array('filterRules' => $ruleValues, 'join' => PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_AND));
    }

    private function calculateTopTenValue($columnID, $startRow, $endRow, $ruleType, $ruleValue)
    {
        $range = $columnID.$startRow.':'.$columnID.$endRow;
        $dataValues = PHPExcel_Calculation_Functions::flattenArray($this->workSheet->rangeToArray($range, null, true, false));

        $dataValues = array_filter($dataValues);
        if ($ruleType == PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP) {
            rsort($dataValues);
        } else {
            sort($dataValues);
        }

        return array_pop(array_slice($dataValues, 0, $ruleValue));
    }

    /**
     *    Apply the AutoFilter rules to the AutoFilter Range
     *
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter
     */
    public function showHideRows()
    {
        list($rangeStart, $rangeEnd) = PHPExcel_Cell::rangeBoundaries($this->range);

        //    The heading row should always be visible
//        echo 'AutoFilter Heading Row ', $rangeStart[1],' is always SHOWN',PHP_EOL;
        $this->workSheet->getRowDimension($rangeStart[1])->setVisible(true);

        $columnFilterTests = array();
        foreach ($this->columns as $columnID => $filterColumn) {
            $rules = $filterColumn->getRules();
            switch ($filterColumn->getFilterType()) {
                case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_FILTER:
                    $ruleValues = array();
                    //    Build a list of the filter value selections
                    foreach ($rules as $rule) {
                        $ruleType = $rule->getRuleType();
                        $ruleValues[] = $rule->getValue();
                    }
                    //    Test if we want to include blanks in our filter criteria
                    $blanks = false;
                    $ruleDataSet = array_filter($ruleValues);
                    if (count($ruleValues) != count($ruleDataSet)) {
                        $blanks = true;
                    }
                    if ($ruleType == PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_FILTER) {
                        //    Filter on absolute values
                        $columnFilterTests[$columnID] = array(
                            'method' => 'filterTestInSimpleDataSet',
                            'arguments' => array('filterValues' => $ruleDataSet, 'blanks' => $blanks)
                        );
                    } else {
                        //    Filter on date group values
                        $arguments = array(
                            'date' => array(),
                            'time' => array(),
                            'dateTime' => array(),
                        );
                        foreach ($ruleDataSet as $ruleValue) {
                            $date = $time = '';
                            if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_YEAR])) &&
                                ($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_YEAR] !== '')) {
                                $date .= sprintf('%04d', $ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_YEAR]);
                            }
                            if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH])) &&
                                ($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH] != '')) {
                                $date .= sprintf('%02d', $ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MONTH]);
                            }
                            if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_DAY])) &&
                                ($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_DAY] !== '')) {
                                $date .= sprintf('%02d', $ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_DAY]);
                            }
                            if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_HOUR])) &&
                                ($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_HOUR] !== '')) {
                                $time .= sprintf('%02d', $ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_HOUR]);
                            }
                            if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE])) &&
                                ($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE] !== '')) {
                                $time .= sprintf('%02d', $ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE]);
                            }
                            if ((isset($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_SECOND])) &&
                                ($ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_SECOND] !== '')) {
                                $time .= sprintf('%02d', $ruleValue[PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DATEGROUP_SECOND]);
                            }
                            $dateTime = $date . $time;
                            $arguments['date'][] = $date;
                            $arguments['time'][] = $time;
                            $arguments['dateTime'][] = $dateTime;
                        }
                        //    Remove empty elements
                        $arguments['date'] = array_filter($arguments['date']);
                        $arguments['time'] = array_filter($arguments['time']);
                        $arguments['dateTime'] = array_filter($arguments['dateTime']);
                        $columnFilterTests[$columnID] = array(
                            'method' => 'filterTestInDateGroupSet',
                            'arguments' => array('filterValues' => $arguments, 'blanks' => $blanks)
                        );
                    }
                    break;
                case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_CUSTOMFILTER:
                    $customRuleForBlanks = false;
                    $ruleValues = array();
                    //    Build a list of the filter value selections
                    foreach ($rules as $rule) {
                        $ruleType = $rule->getRuleType();
                        $ruleValue = $rule->getValue();
                        if (!is_numeric($ruleValue)) {
                            //    Convert to a regexp allowing for regexp reserved characters, wildcards and escaped wildcards
                            $ruleValue = preg_quote($ruleValue);
                            $ruleValue = str_replace(self::$fromReplace, self::$toReplace, $ruleValue);
                            if (trim($ruleValue) == '') {
                                $customRuleForBlanks = true;
                                $ruleValue = trim($ruleValue);
                            }
                        }
                        $ruleValues[] = array('operator' => $rule->getOperator(), 'value' => $ruleValue);
                    }
                    $join = $filterColumn->getJoin();
                    $columnFilterTests[$columnID] = array(
                        'method' => 'filterTestInCustomDataSet',
                        'arguments' => array('filterRules' => $ruleValues, 'join' => $join, 'customRuleForBlanks' => $customRuleForBlanks)
                    );
                    break;
                case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_DYNAMICFILTER:
                    $ruleValues = array();
                    foreach ($rules as $rule) {
                        //    We should only ever have one Dynamic Filter Rule anyway
                        $dynamicRuleType = $rule->getGrouping();
                        if (($dynamicRuleType == PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE) ||
                            ($dynamicRuleType == PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_BELOWAVERAGE)) {
                            //    Number (Average) based
                            //    Calculate the average
                            $averageFormula = '=AVERAGE('.$columnID.($rangeStart[1]+1).':'.$columnID.$rangeEnd[1].')';
                            $average = PHPExcel_Calculation::getInstance()->calculateFormula($averageFormula, null, $this->workSheet->getCell('A1'));
                            //    Set above/below rule based on greaterThan or LessTan
                            $operator = ($dynamicRuleType === PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE)
                                ? PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_GREATERTHAN
                                : PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_LESSTHAN;
                            $ruleValues[] = array('operator' => $operator,
                                                   'value' => $average
                                                 );
                            $columnFilterTests[$columnID] = array(
                                'method' => 'filterTestInCustomDataSet',
                                'arguments' => array('filterRules' => $ruleValues, 'join' => PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_OR)
                            );
                        } else {
                            //    Date based
                            if ($dynamicRuleType{0} == 'M' || $dynamicRuleType{0} == 'Q') {
                                //    Month or Quarter
                                sscanf($dynamicRuleType, '%[A-Z]%d', $periodType, $period);
                                if ($periodType == 'M') {
                                    $ruleValues = array($period);
                                } else {
                                    --$period;
                                    $periodEnd = (1+$period)*3;
                                    $periodStart = 1+$period*3;
                                    $ruleValues = range($periodStart, $periodEnd);
                                }
                                $columnFilterTests[$columnID] = array(
                                    'method' => 'filterTestInPeriodDateSet',
                                    'arguments' => $ruleValues
                                );
                                $filterColumn->setAttributes(array());
                            } else {
                                //    Date Range
                                $columnFilterTests[$columnID] = $this->dynamicFilterDateRange($dynamicRuleType, $filterColumn);
                                break;
                            }
                        }
                    }
                    break;
                case PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_FILTERTYPE_TOPTENFILTER:
                    $ruleValues = array();
                    $dataRowCount = $rangeEnd[1] - $rangeStart[1];
                    foreach ($rules as $rule) {
                        //    We should only ever have one Dynamic Filter Rule anyway
                        $toptenRuleType = $rule->getGrouping();
                        $ruleValue = $rule->getValue();
                        $ruleOperator = $rule->getOperator();
                    }
                    if ($ruleOperator === PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT) {
                        $ruleValue = floor($ruleValue * ($dataRowCount / 100));
                    }
                    if ($ruleValue < 1) {
                        $ruleValue = 1;
                    }
                    if ($ruleValue > 500) {
                        $ruleValue = 500;
                    }

                    $maxVal = $this->calculateTopTenValue($columnID, $rangeStart[1]+1, $rangeEnd[1], $toptenRuleType, $ruleValue);

                    $operator = ($toptenRuleType == PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP)
                        ? PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL
                        : PHPExcel_Worksheet_AutoFilter_Column_Rule::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL;
                    $ruleValues[] = array('operator' => $operator, 'value' => $maxVal);
                    $columnFilterTests[$columnID] = array(
                        'method' => 'filterTestInCustomDataSet',
                        'arguments' => array('filterRules' => $ruleValues, 'join' => PHPExcel_Worksheet_AutoFilter_Column::AUTOFILTER_COLUMN_JOIN_OR)
                    );
                    $filterColumn->setAttributes(array('maxVal' => $maxVal));
                    break;
            }
        }

//        echo 'Column Filter Test CRITERIA',PHP_EOL;
//        var_dump($columnFilterTests);
//
        //    Execute the column tests for each row in the autoFilter range to determine show/hide,
        for ($row = $rangeStart[1]+1; $row <= $rangeEnd[1]; ++$row) {
//            echo 'Testing Row = ', $row,PHP_EOL;
            $result = true;
            foreach ($columnFilterTests as $columnID => $columnFilterTest) {
//                echo 'Testing cell ', $columnID.$row,PHP_EOL;
                $cellValue = $this->workSheet->getCell($columnID.$row)->getCalculatedValue();
//                echo 'Value is ', $cellValue,PHP_EOL;
                //    Execute the filter test
                $result = $result &&
                    call_user_func_array(
                        array('PHPExcel_Worksheet_AutoFilter', $columnFilterTest['method']),
                        array($cellValue, $columnFilterTest['arguments'])
                    );
//                echo (($result) ? 'VALID' : 'INVALID'),PHP_EOL;
                //    If filter test has resulted in FALSE, exit the loop straightaway rather than running any more tests
                if (!$result) {
                    break;
                }
            }
            //    Set show/hide for the row based on the result of the autoFilter result
//            echo (($result) ? 'SHOW' : 'HIDE'),PHP_EOL;
            $this->workSheet->getRowDimension($row)->setVisible($result);
        }

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
                if ($key == 'workSheet') {
                    //    Detach from worksheet
                    $this->{$key} = null;
                } else {
                    $this->{$key} = clone $value;
                }
            } elseif ((is_array($value)) && ($key == 'columns')) {
                //    The columns array of PHPExcel_Worksheet_AutoFilter objects
                $this->{$key} = array();
                foreach ($value as $k => $v) {
                    $this->{$key}[$k] = clone $v;
                    // attach the new cloned Column to this new cloned Autofilter object
                    $this->{$key}[$k]->setParent($this);
                }
            } else {
                $this->{$key} = $value;
            }
        }
    }

    /**
     * toString method replicates previous behavior by returning the range if object is
     *    referenced as a property of its parent.
     */
    public function __toString()
    {
        return (string) $this->range;
    }
}
