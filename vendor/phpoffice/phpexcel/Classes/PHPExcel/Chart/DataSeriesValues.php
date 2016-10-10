<?php

/**
 * PHPExcel_Chart_DataSeriesValues
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
class PHPExcel_Chart_DataSeriesValues
{

    const DATASERIES_TYPE_STRING    = 'String';
    const DATASERIES_TYPE_NUMBER    = 'Number';

    private static $dataTypeValues = array(
        self::DATASERIES_TYPE_STRING,
        self::DATASERIES_TYPE_NUMBER,
    );

    /**
     * Series Data Type
     *
     * @var    string
     */
    private $dataType;

    /**
     * Series Data Source
     *
     * @var    string
     */
    private $dataSource;

    /**
     * Format Code
     *
     * @var    string
     */
    private $formatCode;

    /**
     * Series Point Marker
     *
     * @var    string
     */
    private $pointMarker;

    /**
     * Point Count (The number of datapoints in the dataseries)
     *
     * @var    integer
     */
    private $pointCount = 0;

    /**
     * Data Values
     *
     * @var    array of mixed
     */
    private $dataValues = array();

    /**
     * Create a new PHPExcel_Chart_DataSeriesValues object
     */
    public function __construct($dataType = self::DATASERIES_TYPE_NUMBER, $dataSource = null, $formatCode = null, $pointCount = 0, $dataValues = array(), $marker = null)
    {
        $this->setDataType($dataType);
        $this->dataSource = $dataSource;
        $this->formatCode = $formatCode;
        $this->pointCount = $pointCount;
        $this->dataValues = $dataValues;
        $this->pointMarker = $marker;
    }

    /**
     * Get Series Data Type
     *
     * @return    string
     */
    public function getDataType()
    {
        return $this->dataType;
    }

    /**
     * Set Series Data Type
     *
     * @param    string    $dataType    Datatype of this data series
     *                                Typical values are:
     *                                    PHPExcel_Chart_DataSeriesValues::DATASERIES_TYPE_STRING
     *                                        Normally used for axis point values
     *                                    PHPExcel_Chart_DataSeriesValues::DATASERIES_TYPE_NUMBER
     *                                        Normally used for chart data values
     * @return    PHPExcel_Chart_DataSeriesValues
     */
    public function setDataType($dataType = self::DATASERIES_TYPE_NUMBER)
    {
        if (!in_array($dataType, self::$dataTypeValues)) {
            throw new PHPExcel_Chart_Exception('Invalid datatype for chart data series values');
        }
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * Get Series Data Source (formula)
     *
     * @return    string
     */
    public function getDataSource()
    {
        return $this->dataSource;
    }

    /**
     * Set Series Data Source (formula)
     *
     * @param    string    $dataSource
     * @return    PHPExcel_Chart_DataSeriesValues
     */
    public function setDataSource($dataSource = null, $refreshDataValues = true)
    {
        $this->dataSource = $dataSource;

        if ($refreshDataValues) {
            //    TO DO
        }

        return $this;
    }

    /**
     * Get Point Marker
     *
     * @return string
     */
    public function getPointMarker()
    {
        return $this->pointMarker;
    }

    /**
     * Set Point Marker
     *
     * @param    string    $marker
     * @return    PHPExcel_Chart_DataSeriesValues
     */
    public function setPointMarker($marker = null)
    {
        $this->pointMarker = $marker;

        return $this;
    }

    /**
     * Get Series Format Code
     *
     * @return    string
     */
    public function getFormatCode()
    {
        return $this->formatCode;
    }

    /**
     * Set Series Format Code
     *
     * @param    string    $formatCode
     * @return    PHPExcel_Chart_DataSeriesValues
     */
    public function setFormatCode($formatCode = null)
    {
        $this->formatCode = $formatCode;

        return $this;
    }

    /**
     * Get Series Point Count
     *
     * @return    integer
     */
    public function getPointCount()
    {
        return $this->pointCount;
    }

    /**
     * Identify if the Data Series is a multi-level or a simple series
     *
     * @return    boolean
     */
    public function isMultiLevelSeries()
    {
        if (count($this->dataValues) > 0) {
            return is_array($this->dataValues[0]);
        }
        return null;
    }

    /**
     * Return the level count of a multi-level Data Series
     *
     * @return    boolean
     */
    public function multiLevelCount()
    {
        $levelCount = 0;
        foreach ($this->dataValues as $dataValueSet) {
            $levelCount = max($levelCount, count($dataValueSet));
        }
        return $levelCount;
    }

    /**
     * Get Series Data Values
     *
     * @return    array of mixed
     */
    public function getDataValues()
    {
        return $this->dataValues;
    }

    /**
     * Get the first Series Data value
     *
     * @return    mixed
     */
    public function getDataValue()
    {
        $count = count($this->dataValues);
        if ($count == 0) {
            return null;
        } elseif ($count == 1) {
            return $this->dataValues[0];
        }
        return $this->dataValues;
    }

    /**
     * Set Series Data Values
     *
     * @param    array    $dataValues
     * @param    boolean    $refreshDataSource
     *                    TRUE - refresh the value of dataSource based on the values of $dataValues
     *                    FALSE - don't change the value of dataSource
     * @return    PHPExcel_Chart_DataSeriesValues
     */
    public function setDataValues($dataValues = array(), $refreshDataSource = true)
    {
        $this->dataValues = PHPExcel_Calculation_Functions::flattenArray($dataValues);
        $this->pointCount = count($dataValues);

        if ($refreshDataSource) {
            //    TO DO
        }

        return $this;
    }

    private function stripNulls($var)
    {
        return $var !== null;
    }

    public function refresh(PHPExcel_Worksheet $worksheet, $flatten = true)
    {
        if ($this->dataSource !== null) {
            $calcEngine = PHPExcel_Calculation::getInstance($worksheet->getParent());
            $newDataValues = PHPExcel_Calculation::unwrapResult(
                $calcEngine->_calculateFormulaValue(
                    '='.$this->dataSource,
                    null,
                    $worksheet->getCell('A1')
                )
            );
            if ($flatten) {
                $this->dataValues = PHPExcel_Calculation_Functions::flattenArray($newDataValues);
                foreach ($this->dataValues as &$dataValue) {
                    if ((!empty($dataValue)) && ($dataValue[0] == '#')) {
                        $dataValue = 0.0;
                    }
                }
                unset($dataValue);
            } else {
                $cellRange = explode('!', $this->dataSource);
                if (count($cellRange) > 1) {
                    list(, $cellRange) = $cellRange;
                }

                $dimensions = PHPExcel_Cell::rangeDimension(str_replace('$', '', $cellRange));
                if (($dimensions[0] == 1) || ($dimensions[1] == 1)) {
                    $this->dataValues = PHPExcel_Calculation_Functions::flattenArray($newDataValues);
                } else {
                    $newArray = array_values(array_shift($newDataValues));
                    foreach ($newArray as $i => $newDataSet) {
                        $newArray[$i] = array($newDataSet);
                    }

                    foreach ($newDataValues as $newDataSet) {
                        $i = 0;
                        foreach ($newDataSet as $newDataVal) {
                            array_unshift($newArray[$i++], $newDataVal);
                        }
                    }
                    $this->dataValues = $newArray;
                }
            }
            $this->pointCount = count($this->dataValues);
        }
    }
}
