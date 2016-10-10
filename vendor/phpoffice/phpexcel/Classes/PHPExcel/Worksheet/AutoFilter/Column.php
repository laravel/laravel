<?php

/**
 * PHPExcel_Worksheet_AutoFilter_Column
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
class PHPExcel_Worksheet_AutoFilter_Column
{
    const AUTOFILTER_FILTERTYPE_FILTER         = 'filters';
    const AUTOFILTER_FILTERTYPE_CUSTOMFILTER   = 'customFilters';
    //    Supports no more than 2 rules, with an And/Or join criteria
    //        if more than 1 rule is defined
    const AUTOFILTER_FILTERTYPE_DYNAMICFILTER  = 'dynamicFilter';
    //    Even though the filter rule is constant, the filtered data can vary
    //        e.g. filtered by date = TODAY
    const AUTOFILTER_FILTERTYPE_TOPTENFILTER   = 'top10';

    /**
     * Types of autofilter rules
     *
     * @var string[]
     */
    private static $filterTypes = array(
        //    Currently we're not handling
        //        colorFilter
        //        extLst
        //        iconFilter
        self::AUTOFILTER_FILTERTYPE_FILTER,
        self::AUTOFILTER_FILTERTYPE_CUSTOMFILTER,
        self::AUTOFILTER_FILTERTYPE_DYNAMICFILTER,
        self::AUTOFILTER_FILTERTYPE_TOPTENFILTER,
    );

    /* Multiple Rule Connections */
    const AUTOFILTER_COLUMN_JOIN_AND = 'and';
    const AUTOFILTER_COLUMN_JOIN_OR  = 'or';

    /**
     * Join options for autofilter rules
     *
     * @var string[]
     */
    private static $ruleJoins = array(
        self::AUTOFILTER_COLUMN_JOIN_AND,
        self::AUTOFILTER_COLUMN_JOIN_OR,
    );

    /**
     * Autofilter
     *
     * @var PHPExcel_Worksheet_AutoFilter
     */
    private $parent;


    /**
     * Autofilter Column Index
     *
     * @var string
     */
    private $columnIndex = '';


    /**
     * Autofilter Column Filter Type
     *
     * @var string
     */
    private $filterType = self::AUTOFILTER_FILTERTYPE_FILTER;


    /**
     * Autofilter Multiple Rules And/Or
     *
     * @var string
     */
    private $join = self::AUTOFILTER_COLUMN_JOIN_OR;


    /**
     * Autofilter Column Rules
     *
     * @var array of PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    private $ruleset = array();


    /**
     * Autofilter Column Dynamic Attributes
     *
     * @var array of mixed
     */
    private $attributes = array();


    /**
     * Create a new PHPExcel_Worksheet_AutoFilter_Column
     *
     *    @param    string                           $pColumn        Column (e.g. A)
     *    @param    PHPExcel_Worksheet_AutoFilter  $pParent        Autofilter for this column
     */
    public function __construct($pColumn, PHPExcel_Worksheet_AutoFilter $pParent = null)
    {
        $this->columnIndex = $pColumn;
        $this->parent = $pParent;
    }

    /**
     * Get AutoFilter Column Index
     *
     * @return string
     */
    public function getColumnIndex()
    {
        return $this->columnIndex;
    }

    /**
     *    Set AutoFilter Column Index
     *
     *    @param    string        $pColumn        Column (e.g. A)
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function setColumnIndex($pColumn)
    {
        // Uppercase coordinate
        $pColumn = strtoupper($pColumn);
        if ($this->parent !== null) {
            $this->parent->testColumnInRange($pColumn);
        }

        $this->columnIndex = $pColumn;

        return $this;
    }

    /**
     * Get this Column's AutoFilter Parent
     *
     * @return PHPExcel_Worksheet_AutoFilter
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set this Column's AutoFilter Parent
     *
     * @param PHPExcel_Worksheet_AutoFilter
     * @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function setParent(PHPExcel_Worksheet_AutoFilter $pParent = null)
    {
        $this->parent = $pParent;

        return $this;
    }

    /**
     * Get AutoFilter Type
     *
     * @return string
     */
    public function getFilterType()
    {
        return $this->filterType;
    }

    /**
     *    Set AutoFilter Type
     *
     *    @param    string        $pFilterType
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function setFilterType($pFilterType = self::AUTOFILTER_FILTERTYPE_FILTER)
    {
        if (!in_array($pFilterType, self::$filterTypes)) {
            throw new PHPExcel_Exception('Invalid filter type for column AutoFilter.');
        }

        $this->filterType = $pFilterType;

        return $this;
    }

    /**
     * Get AutoFilter Multiple Rules And/Or Join
     *
     * @return string
     */
    public function getJoin()
    {
        return $this->join;
    }

    /**
     *    Set AutoFilter Multiple Rules And/Or
     *
     *    @param    string        $pJoin        And/Or
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function setJoin($pJoin = self::AUTOFILTER_COLUMN_JOIN_OR)
    {
        // Lowercase And/Or
        $pJoin = strtolower($pJoin);
        if (!in_array($pJoin, self::$ruleJoins)) {
            throw new PHPExcel_Exception('Invalid rule connection for column AutoFilter.');
        }

        $this->join = $pJoin;

        return $this;
    }

    /**
     *    Set AutoFilter Attributes
     *
     *    @param    string[]        $pAttributes
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function setAttributes($pAttributes = array())
    {
        $this->attributes = $pAttributes;

        return $this;
    }

    /**
     *    Set An AutoFilter Attribute
     *
     *    @param    string        $pName        Attribute Name
     *    @param    string        $pValue        Attribute Value
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet_AutoFilter_Column
     */
    public function setAttribute($pName, $pValue)
    {
        $this->attributes[$pName] = $pValue;

        return $this;
    }

    /**
     * Get AutoFilter Column Attributes
     *
     * @return string
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Get specific AutoFilter Column Attribute
     *
     *    @param    string        $pName        Attribute Name
     * @return string
     */
    public function getAttribute($pName)
    {
        if (isset($this->attributes[$pName])) {
            return $this->attributes[$pName];
        }
        return null;
    }

    /**
     * Get all AutoFilter Column Rules
     *
     * @throws    PHPExcel_Exception
     * @return array of PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    public function getRules()
    {
        return $this->ruleset;
    }

    /**
     * Get a specified AutoFilter Column Rule
     *
     * @param    integer    $pIndex        Rule index in the ruleset array
     * @return    PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    public function getRule($pIndex)
    {
        if (!isset($this->ruleset[$pIndex])) {
            $this->ruleset[$pIndex] = new PHPExcel_Worksheet_AutoFilter_Column_Rule($this);
        }
        return $this->ruleset[$pIndex];
    }

    /**
     * Create a new AutoFilter Column Rule in the ruleset
     *
     * @return    PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    public function createRule()
    {
        $this->ruleset[] = new PHPExcel_Worksheet_AutoFilter_Column_Rule($this);

        return end($this->ruleset);
    }

    /**
     * Add a new AutoFilter Column Rule to the ruleset
     *
     * @param    PHPExcel_Worksheet_AutoFilter_Column_Rule    $pRule
     * @param    boolean    $returnRule     Flag indicating whether the rule object or the column object should be returned
     * @return    PHPExcel_Worksheet_AutoFilter_Column|PHPExcel_Worksheet_AutoFilter_Column_Rule
     */
    public function addRule(PHPExcel_Worksheet_AutoFilter_Column_Rule $pRule, $returnRule = true)
    {
        $pRule->setParent($this);
        $this->ruleset[] = $pRule;

        return ($returnRule) ? $pRule : $this;
    }

    /**
     * Delete a specified AutoFilter Column Rule
     *    If the number of rules is reduced to 1, then we reset And/Or logic to Or
     *
     * @param    integer    $pIndex        Rule index in the ruleset array
     * @return    PHPExcel_Worksheet_AutoFilter_Column
     */
    public function deleteRule($pIndex)
    {
        if (isset($this->ruleset[$pIndex])) {
            unset($this->ruleset[$pIndex]);
            //    If we've just deleted down to a single rule, then reset And/Or joining to Or
            if (count($this->ruleset) <= 1) {
                $this->setJoin(self::AUTOFILTER_COLUMN_JOIN_OR);
            }
        }

        return $this;
    }

    /**
     * Delete all AutoFilter Column Rules
     *
     * @return    PHPExcel_Worksheet_AutoFilter_Column
     */
    public function clearRules()
    {
        $this->ruleset = array();
        $this->setJoin(self::AUTOFILTER_COLUMN_JOIN_OR);

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
                    //    Detach from autofilter parent
                    $this->$key = null;
                } else {
                    $this->$key = clone $value;
                }
            } elseif ((is_array($value)) && ($key == 'ruleset')) {
                //    The columns array of PHPExcel_Worksheet_AutoFilter objects
                $this->$key = array();
                foreach ($value as $k => $v) {
                    $this->$key[$k] = clone $v;
                    // attach the new cloned Rule to this new cloned Autofilter Cloned object
                    $this->$key[$k]->setParent($this);
                }
            } else {
                $this->$key = $value;
            }
        }
    }
}
