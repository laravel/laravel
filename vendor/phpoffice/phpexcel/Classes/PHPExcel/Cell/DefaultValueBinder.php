<?php

/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}

/**
 * PHPExcel_Cell_DefaultValueBinder
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
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Cell_DefaultValueBinder implements PHPExcel_Cell_IValueBinder
{
    /**
     * Bind value to a cell
     *
     * @param  PHPExcel_Cell  $cell   Cell to bind value to
     * @param  mixed          $value  Value to bind in cell
     * @return boolean
     */
    public function bindValue(PHPExcel_Cell $cell, $value = null)
    {
        // sanitize UTF-8 strings
        if (is_string($value)) {
            $value = PHPExcel_Shared_String::SanitizeUTF8($value);
        } elseif (is_object($value)) {
            // Handle any objects that might be injected
            if ($value instanceof DateTime) {
                $value = $value->format('Y-m-d H:i:s');
            } elseif (!($value instanceof PHPExcel_RichText)) {
                $value = (string) $value;
            }
        }

        // Set value explicit
        $cell->setValueExplicit($value, self::dataTypeForValue($value));

        // Done!
        return true;
    }

    /**
     * DataType for value
     *
     * @param   mixed  $pValue
     * @return  string
     */
    public static function dataTypeForValue($pValue = null)
    {
        // Match the value against a few data types
        if ($pValue === null) {
            return PHPExcel_Cell_DataType::TYPE_NULL;
        } elseif ($pValue === '') {
            return PHPExcel_Cell_DataType::TYPE_STRING;
        } elseif ($pValue instanceof PHPExcel_RichText) {
            return PHPExcel_Cell_DataType::TYPE_INLINE;
        } elseif ($pValue{0} === '=' && strlen($pValue) > 1) {
            return PHPExcel_Cell_DataType::TYPE_FORMULA;
        } elseif (is_bool($pValue)) {
            return PHPExcel_Cell_DataType::TYPE_BOOL;
        } elseif (is_float($pValue) || is_int($pValue)) {
            return PHPExcel_Cell_DataType::TYPE_NUMERIC;
        } elseif (preg_match('/^[\+\-]?([0-9]+\\.?[0-9]*|[0-9]*\\.?[0-9]+)([Ee][\-\+]?[0-2]?\d{1,3})?$/', $pValue)) {
            $tValue = ltrim($pValue, '+-');
            if (is_string($pValue) && $tValue{0} === '0' && strlen($tValue) > 1 && $tValue{1} !== '.') {
                return PHPExcel_Cell_DataType::TYPE_STRING;
            } elseif ((strpos($pValue, '.') === false) && ($pValue > PHP_INT_MAX)) {
                return PHPExcel_Cell_DataType::TYPE_STRING;
            }
            return PHPExcel_Cell_DataType::TYPE_NUMERIC;
        } elseif (is_string($pValue) && array_key_exists($pValue, PHPExcel_Cell_DataType::getErrorCodes())) {
            return PHPExcel_Cell_DataType::TYPE_ERROR;
        }

        return PHPExcel_Cell_DataType::TYPE_STRING;
    }
}
