<?php

/*
PARTLY BASED ON:
    Copyright (c) 2007 E. W. Bachtal, Inc.

    Permission is hereby granted, free of charge, to any person obtaining a copy of this software
    and associated documentation files (the "Software"), to deal in the Software without restriction,
    including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense,
    and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so,
    subject to the following conditions:

      The above copyright notice and this permission notice shall be included in all copies or substantial
      portions of the Software.

    The software is provided "as is", without warranty of any kind, express or implied, including but not
    limited to the warranties of merchantability, fitness for a particular purpose and noninfringement. In
    no event shall the authors or copyright holders be liable for any claim, damages or other liability,
    whether in an action of contract, tort or otherwise, arising from, out of or in connection with the
    software or the use or other dealings in the software.

    http://ewbi.blogs.com/develops/2007/03/excel_formula_p.html
    http://ewbi.blogs.com/develops/2004/12/excel_formula_p.html
*/

/**
 * PHPExcel_Calculation_FormulaToken
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
 * @package    PHPExcel_Calculation
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */


class PHPExcel_Calculation_FormulaToken
{
    /* Token types */
    const TOKEN_TYPE_NOOP            = 'Noop';
    const TOKEN_TYPE_OPERAND         = 'Operand';
    const TOKEN_TYPE_FUNCTION        = 'Function';
    const TOKEN_TYPE_SUBEXPRESSION   = 'Subexpression';
    const TOKEN_TYPE_ARGUMENT        = 'Argument';
    const TOKEN_TYPE_OPERATORPREFIX  = 'OperatorPrefix';
    const TOKEN_TYPE_OPERATORINFIX   = 'OperatorInfix';
    const TOKEN_TYPE_OPERATORPOSTFIX = 'OperatorPostfix';
    const TOKEN_TYPE_WHITESPACE      = 'Whitespace';
    const TOKEN_TYPE_UNKNOWN         = 'Unknown';

    /* Token subtypes */
    const TOKEN_SUBTYPE_NOTHING       = 'Nothing';
    const TOKEN_SUBTYPE_START         = 'Start';
    const TOKEN_SUBTYPE_STOP          = 'Stop';
    const TOKEN_SUBTYPE_TEXT          = 'Text';
    const TOKEN_SUBTYPE_NUMBER        = 'Number';
    const TOKEN_SUBTYPE_LOGICAL       = 'Logical';
    const TOKEN_SUBTYPE_ERROR         = 'Error';
    const TOKEN_SUBTYPE_RANGE         = 'Range';
    const TOKEN_SUBTYPE_MATH          = 'Math';
    const TOKEN_SUBTYPE_CONCATENATION = 'Concatenation';
    const TOKEN_SUBTYPE_INTERSECTION  = 'Intersection';
    const TOKEN_SUBTYPE_UNION         = 'Union';

    /**
     * Value
     *
     * @var string
     */
    private $value;

    /**
     * Token Type (represented by TOKEN_TYPE_*)
     *
     * @var string
     */
    private $tokenType;

    /**
     * Token SubType (represented by TOKEN_SUBTYPE_*)
     *
     * @var string
     */
    private $tokenSubType;

    /**
     * Create a new PHPExcel_Calculation_FormulaToken
     *
     * @param string    $pValue
     * @param string    $pTokenType     Token type (represented by TOKEN_TYPE_*)
     * @param string    $pTokenSubType     Token Subtype (represented by TOKEN_SUBTYPE_*)
     */
    public function __construct($pValue, $pTokenType = PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_UNKNOWN, $pTokenSubType = PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_NOTHING)
    {
        // Initialise values
        $this->value       = $pValue;
        $this->tokenType    = $pTokenType;
        $this->tokenSubType = $pTokenSubType;
    }

    /**
     * Get Value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set Value
     *
     * @param string    $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Get Token Type (represented by TOKEN_TYPE_*)
     *
     * @return string
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * Set Token Type
     *
     * @param string    $value
     */
    public function setTokenType($value = PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_UNKNOWN)
    {
        $this->tokenType = $value;
    }

    /**
     * Get Token SubType (represented by TOKEN_SUBTYPE_*)
     *
     * @return string
     */
    public function getTokenSubType()
    {
        return $this->tokenSubType;
    }

    /**
     * Set Token SubType
     *
     * @param string    $value
     */
    public function setTokenSubType($value = PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_NOTHING)
    {
        $this->tokenSubType = $value;
    }
}
