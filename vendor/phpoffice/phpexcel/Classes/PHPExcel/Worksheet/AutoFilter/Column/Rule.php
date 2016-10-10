<?php

/**
 * PHPExcel_Worksheet_AutoFilter_Column_Rule
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
 * @package        PHPExcel_Worksheet
 * @copyright    Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license        http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version        ##VERSION##, ##DATE##
 */
class PHPExcel_Worksheet_AutoFilter_Column_Rule
{
    const AUTOFILTER_RULETYPE_FILTER        = 'filter';
    const AUTOFILTER_RULETYPE_DATEGROUP     = 'dateGroupItem';
    const AUTOFILTER_RULETYPE_CUSTOMFILTER  = 'customFilter';
    const AUTOFILTER_RULETYPE_DYNAMICFILTER = 'dynamicFilter';
    const AUTOFILTER_RULETYPE_TOPTENFILTER  = 'top10Filter';

    private static $ruleTypes = array(
        //    Currently we're not handling
        //        colorFilter
        //        extLst
        //        iconFilter
        self::AUTOFILTER_RULETYPE_FILTER,
        self::AUTOFILTER_RULETYPE_DATEGROUP,
        self::AUTOFILTER_RULETYPE_CUSTOMFILTER,
        self::AUTOFILTER_RULETYPE_DYNAMICFILTER,
        self::AUTOFILTER_RULETYPE_TOPTENFILTER,
    );

    const AUTOFILTER_RULETYPE_DATEGROUP_YEAR   = 'year';
    const AUTOFILTER_RULETYPE_DATEGROUP_MONTH  = 'month';
    const AUTOFILTER_RULETYPE_DATEGROUP_DAY    = 'day';
    const AUTOFILTER_RULETYPE_DATEGROUP_HOUR   = 'hour';
    const AUTOFILTER_RULETYPE_DATEGROUP_MINUTE = 'minute';
    const AUTOFILTER_RULETYPE_DATEGROUP_SECOND = 'second';

    private static $dateTimeGroups = array(
        self::AUTOFILTER_RULETYPE_DATEGROUP_YEAR,
        self::AUTOFILTER_RULETYPE_DATEGROUP_MONTH,
        self::AUTOFILTER_RULETYPE_DATEGROUP_DAY,
        self::AUTOFILTER_RULETYPE_DATEGROUP_HOUR,
        self::AUTOFILTER_RULETYPE_DATEGROUP_MINUTE,
        self::AUTOFILTER_RULETYPE_DATEGROUP_SECOND,
    );

    const AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY    = 'yesterday';
    const AUTOFILTER_RULETYPE_DYNAMIC_TODAY        = 'today';
    const AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW     = 'tomorrow';
    const AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE   = 'yearToDate';
    const AUTOFILTER_RULETYPE_DYNAMIC_THISYEAR     = 'thisYear';
    const AUTOFILTER_RULETYPE_DYNAMIC_THISQUARTER  = 'thisQuarter';
    const AUTOFILTER_RULETYPE_DYNAMIC_THISMONTH    = 'thisMonth';
    const AUTOFILTER_RULETYPE_DYNAMIC_THISWEEK     = 'thisWeek';
    const AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR     = 'lastYear';
    const AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER  = 'lastQuarter';
    const AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH    = 'lastMonth';
    const AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK     = 'lastWeek';
    const AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR     = 'nextYear';
    const AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER  = 'nextQuarter';
    const AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH    = 'nextMonth';
    const AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK     = 'nextWeek';
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_1      = 'M1';
    const AUTOFILTER_RULETYPE_DYNAMIC_JANUARY      = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_1;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_2      = 'M2';
    const AUTOFILTER_RULETYPE_DYNAMIC_FEBRUARY     = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_2;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_3      = 'M3';
    const AUTOFILTER_RULETYPE_DYNAMIC_MARCH        = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_3;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_4      = 'M4';
    const AUTOFILTER_RULETYPE_DYNAMIC_APRIL        = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_4;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_5      = 'M5';
    const AUTOFILTER_RULETYPE_DYNAMIC_MAY          = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_5;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_6      = 'M6';
    const AUTOFILTER_RULETYPE_DYNAMIC_JUNE         = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_6;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_7      = 'M7';
    const AUTOFILTER_RULETYPE_DYNAMIC_JULY         = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_7;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_8      = 'M8';
    const AUTOFILTER_RULETYPE_DYNAMIC_AUGUST       = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_8;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_9      = 'M9';
    const AUTOFILTER_RULETYPE_DYNAMIC_SEPTEMBER    = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_9;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_10     = 'M10';
    const AUTOFILTER_RULETYPE_DYNAMIC_OCTOBER      = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_10;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_11     = 'M11';
    const AUTOFILTER_RULETYPE_DYNAMIC_NOVEMBER     = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_11;
    const AUTOFILTER_RULETYPE_DYNAMIC_MONTH_12     = 'M12';
    const AUTOFILTER_RULETYPE_DYNAMIC_DECEMBER     = self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_12;
    const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_1    = 'Q1';
    const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_2    = 'Q2';
    const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_3    = 'Q3';
    const AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_4    = 'Q4';
    const AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE = 'aboveAverage';
    const AUTOFILTER_RULETYPE_DYNAMIC_BELOWAVERAGE = 'belowAverage';

    private static $dynamicTypes = array(
        self::AUTOFILTER_RULETYPE_DYNAMIC_YESTERDAY,
        self::AUTOFILTER_RULETYPE_DYNAMIC_TODAY,
        self::AUTOFILTER_RULETYPE_DYNAMIC_TOMORROW,
        self::AUTOFILTER_RULETYPE_DYNAMIC_YEARTODATE,
        self::AUTOFILTER_RULETYPE_DYNAMIC_THISYEAR,
        self::AUTOFILTER_RULETYPE_DYNAMIC_THISQUARTER,
        self::AUTOFILTER_RULETYPE_DYNAMIC_THISMONTH,
        self::AUTOFILTER_RULETYPE_DYNAMIC_THISWEEK,
        self::AUTOFILTER_RULETYPE_DYNAMIC_LASTYEAR,
        self::AUTOFILTER_RULETYPE_DYNAMIC_LASTQUARTER,
        self::AUTOFILTER_RULETYPE_DYNAMIC_LASTMONTH,
        self::AUTOFILTER_RULETYPE_DYNAMIC_LASTWEEK,
        self::AUTOFILTER_RULETYPE_DYNAMIC_NEXTYEAR,
        self::AUTOFILTER_RULETYPE_DYNAMIC_NEXTQUARTER,
        self::AUTOFILTER_RULETYPE_DYNAMIC_NEXTMONTH,
        self::AUTOFILTER_RULETYPE_DYNAMIC_NEXTWEEK,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_1,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_2,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_3,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_4,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_5,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_6,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_7,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_8,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_9,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_10,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_11,
        self::AUTOFILTER_RULETYPE_DYNAMIC_MONTH_12,
        self::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_1,
        self::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_2,
        self::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_3,
        self::AUTOFILTER_RULETYPE_DYNAMIC_QUARTER_4,
        self::AUTOFILTER_RULETYPE_DYNAMIC_ABOVEAVERAGE,
        self::AUTOFILTER_RULETYPE_DYNAMIC_BELOWAVERAGE,
    );

    /*
     *    The only valid filter rule operators for filter and customFilter types are:
     *        <xsd:enumeration value="equal"/>
     *        <xsd:enumeration value="lessThan"/>
     *        <xsd:enumeration value="lessThanOrEqual"/>
     *        <xsd:enumeration value="notEqual"/>
     *        <xsd:enumeration value="greaterThanOrEqual"/>
     *        <xsd:enumeration value="greaterThan"/>
     */
    const AUTOFILTER_COLUMN_RULE_EQUAL              = 'equal';
    const AUTOFILTER_COLUMN_RULE_NOTEQUAL           = 'notEqual';
    const AUTOFILTER_COLUMN_RULE_GREATERTHAN        = 'greaterThan';
    const AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL = 'greaterThanOrEqual';
    const AUTOFILTER_COLUMN_RULE_LESSTHAN           = 'lessThan';
    const AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL    = 'lessThanOrEqual';

    private static $operators = array(
        self::AUTOFILTER_COLUMN_RULE_EQUAL,
        self::AUTOFILTER_COLUMN_RULE_NOTEQUAL,
        self::AUTOFILTER_COLUMN_RULE_GREATERTHAN,
        self::AUTOFILTER_COLUMN_RULE_GREATERTHANOREQUAL,
        self::AUTOFILTER_COLUMN_RULE_LESSTHAN,
        self::AUTOFILTER_COLUMN_RULE_LESSTHANOREQUAL,
    );

    const AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE = 'byValue';
    const AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT  = 'byPercent';

    private static $topTenValue = array(
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_BY_VALUE,
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_PERCENT,
    );

    const AUTOFILTER_COLUMN_RULE_TOPTEN_TOP    = 'top';
    const AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM = 'bottom';

    private static $topTenType = array(
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_TOP,
        self::AUTOFILTER_COLUMN_RULE_TOPTEN_BOTTOM,
    );


    /* Rule Operators (Numeric, Boolean etc) */
//    const AUTOFILTER_COLUMN_RULE_BETWEEN            = 'between';        //    greaterThanOrEqual 1 && lessThanOrEqual 2
    /* Rule Operators (Numeric Special) which are translated to standard numeric operators with calculated values */
//    const AUTOFILTER_COLUMN_RULE_TOPTEN                = 'topTen';            //    greaterThan calculated value
//    const AUTOFILTER_COLUMN_RULE_TOPTENPERCENT        = 'topTenPercent';    //    greaterThan calculated value
//    const AUTOFILTER_COLUMN_RULE_ABOVEAVERAGE        = 'aboveAverage';    //    Value is calculated as the average
//    const AUTOFILTER_COLUMN_RULE_BELOWAVERAGE        = 'belowAverage';    //    Value is calculated as the average
    /* Rule Operators (String) which are set as wild-carded values */
//    const AUTOFILTER_COLUMN_RULE_BEGINSWITH            = 'beginsWith';            // A*
//    const AUTOFILTER_COLUMN_RULE_ENDSWITH            = 'endsWith';            // *Z
//    const AUTOFILTER_COLUMN_RULE_CONTAINS            = 'contains';            // *B*
//    const AUTOFILTER_COLUMN_RULE_DOESNTCONTAIN        = 'notEqual';            //    notEqual *B*
    /* Rule Operators (Date Special) which are translated to standard numeric operators with calculated values */
//    const AUTOFILTER_COLUMN_RULE_BEFORE                = 'lessThan';
//    const AUTOFILTER_COLUMN_RULE_AFTER                = 'greaterThan';
//    const AUTOFILTER_COLUMN_RULE_YESTERDAY            = 'yesterday';
//    const AUTOFILTER_COLUMN_RULE_TODAY                = 'today';
//    const AUTOFILTER_COLUMN_RULE_TOMORROW            = 'tomorrow';
//    const AUTOFILTER_COLUMN_RULE_LASTWEEK            = 'lastWeek';
//    const AUTOFILTER_COLUMN_RULE_THISWEEK            = 'thisWeek';
//    const AUTOFILTER_COLUMN_RULE_NEXTWEEK            = 'nextWeek';
//    const AUTOFILTER_COLUMN_RULE_LASTMONTH            = 'lastMonth';
//    const AUTOFILTER_COLUMN_RULE_THISMONTH            = 'thisMonth';
//    const AUTOFILTER_COLUMN_RULE_NEXTMONTH            = 'nextMonth';
//    const AUTOFILTER_COLUMN_RULE_LASTQUARTER        = 'lastQuarter';
//    const AUTOFILTER_COLUMN_RULE_THISQUARTER        = 'thisQuarter';
//    const AUTOFILTER_COLUMN_RULE_NEXTQUARTER        = 'nextQuarter';
//    const AUTOFILTER_COLUMN_RULE_LASTYEAR            = 'lastYear';
//    const AUTOFILTER_COLUMN_RULE_THISYEAR            = 'thisYear';
//    const AUTOFILTER_COLUMN_RULE_NEXTYEAR            = 'nextYear';
//    const AUTOFILTER_COLUMN_RULE_YEARTODATE            = 'yearToDate';            //    <dynamicFilter val="40909" type="yearToDate" maxVal="41113"/>
//    const AUTOFILTER_COLUMN_RULE_ALLDATESINMONTH    = 'allDatesInMonth';    //    <dynamicFilter type="M2"/> for Month/February
//    const AUTOFILTER_COLUMN_RULE_ALLDATESINQUARTER    = 'allDatesInQuarter';    //    <dynamicFilter type="Q2"/> for Quarter 2

    /**
     * Autofilter Column
     *
     * @var PHPExcel_Worksheet_AutoFilter_Column
     */
    private $parent = null;


    /**
     * Autofilter Rule Type
     *
     * @var string
     */
    private $ruleType = self::AUTOFILTER_RULETYPE_FILTER;


    /**
     * Autofilter Rule Value
     *
     * @var string
     */
    private $value = '';

    /**
     * Autofilter Rule Operator
     *
     * @var string
     */
    private $operator = self::AUTOFILTER_COLUMN_RULE_EQUAL;

    /**
     * DateTimeGrouping Group Value
     *
     * @var string
     */
    private $grouping = '';


    /**
     * Create a new PHPExcel_Worksheet_AutoFilter_Column_Rule
     *
     * @param PHPExcel_Worksheet_AutoFilter_Column $pParent
     */
    public function __construct(PHPExcel_Worksheet_AutoFilter_Column $pParent = null)
    {
        $this->parent = $pParent;
    }

    /**
     * Get AutoFilter Rule Type
     *
     * @return string
     */
    public function getRuleType()
    {
        return $this->ruleType;
    }

    /**
     *    Set AutoFilter Rule Type
     *
     *    @param    string        $pRuleType
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function setRuleType($pRuleType = self::AUTOFILTER_RULETYPE_FILTER)
    {
        if (!in_array($pRuleType, self::$ruleTypes)) {
            throw new PHPExcel_Exception('Invalid rule type for column AutoFilter Rule.');
        }

        $this->ruleType = $pRuleType;

        return $this;
    }

    /**
     * Get AutoFilter Rule Value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     *    Set AutoFilter Rule Value
     *
     *    @param    string|string[]        $pValue
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    public function setValue($pValue = '')
    {
        if (is_array($pValue)) {
            $grouping = -1;
            foreach ($pValue as $key => $value) {
                //    Validate array entries
                if (!in_array($key, self::$dateTimeGroups)) {
                    //    Remove any invalid entries from the value array
                    unset($pValue[$key]);
                } else {
                    //    Work out what the dateTime grouping will be
                    $grouping = max($grouping, array_search($key, self::$dateTimeGroups));
                }
            }
            if (count($pValue) == 0) {
                throw new PHPExcel_Exception('Invalid rule value for column AutoFilter Rule.');
            }
            //    Set the dateTime grouping that we've anticipated
            $this->setGrouping(self::$dateTimeGroups[$grouping]);
        }
        $this->value = $pValue;

        return $this;
    }

    /**
     * Get AutoFilter Rule Operator
     *
     * @return string
     */
    public function getOperator()
    {
        return $this->operator;
    }

    /**
     *    Set AutoFilter Rule Operator
     *
     *    @param    string        $pOperator
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    public function setOperator($pOperator = self::AUTOFILTER_COLUMN_RULE_EQUAL)
    {
        if (empty($pOperator)) {
            $pOperator = self::AUTOFILTER_COLUMN_RULE_EQUAL;
        }
        if ((!in_array($pOperator, self::$operators)) &&
            (!in_array($pOperator, self::$topTenValue))) {
            throw new PHPExcel_Exception('Invalid operator for column AutoFilter Rule.');
        }
        $this->operator = $pOperator;

        return $this;
    }

    /**
     * Get AutoFilter Rule Grouping
     *
     * @return string
     */
    public function getGrouping()
    {
        return $this->grouping;
    }

    /**
     *    Set AutoFilter Rule Grouping
     *
     *    @param    string        $pGrouping
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    public function setGrouping($pGrouping = null)
    {
        if (($pGrouping !== null) &&
            (!in_array($pGrouping, self::$dateTimeGroups)) &&
            (!in_array($pGrouping, self::$dynamicTypes)) &&
            (!in_array($pGrouping, self::$topTenType))) {
            throw new PHPExcel_Exception('Invalid rule type for column AutoFilter Rule.');
        }
        $this->grouping = $pGrouping;

        return $this;
    }

    /**
     *    Set AutoFilter Rule
     *
     *    @param    string                $pOperator
     *    @param    string|string[]        $pValue
     *    @param    string                $pGrouping
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    public function setRule($pOperator = self::AUTOFILTER_COLUMN_RULE_EQUAL, $pValue = '', $pGrouping = null)
    {
        $this->setOperator($pOperator);
        $this->setValue($pValue);
        //    Only set grouping if it's been passed in as a user-supplied argument,
        //        otherwise we're calculating it when we setValue() and don't want to overwrite that
        //        If the user supplies an argumnet for grouping, then on their own head be it
        if ($pGrouping !== null) {
            $this->setGrouping($pGrouping);
        }

        return $this;
    }

    /**
     * Get this Rule's AutoFilter Column Parent
     *
     * @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set this Rule's AutoFilter Column Parent
     *
     * @param PHPExcel_Worksheet_AutoFilter_Column
     * @return PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    public function setParent(PHPExcel_Worksheet_AutoFilter_Column $pParent = null)
    {
        $this->parent = $pParent;

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
                if ($key == 'parent') {
                    //    Detach from autofilter column parent
                    $this->$key = null;
                } else {
                    $this->$key = clone $value;
                }
            } else {
                $this->$key = $value;
            }
        }
    }
}
