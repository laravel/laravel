<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2014 PHPExcel
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
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


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
 * PHPExcel_Calculation_FormulaParser
 *
 * @category   PHPExcel
 * @package    PHPExcel_Calculation
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_FormulaParser {
	/* Character constants */
	const QUOTE_DOUBLE  = '"';
	const QUOTE_SINGLE  = '\'';
	const BRACKET_CLOSE = ']';
	const BRACKET_OPEN  = '[';
	const BRACE_OPEN    = '{';
	const BRACE_CLOSE   = '}';
	const PAREN_OPEN    = '(';
	const PAREN_CLOSE   = ')';
	const SEMICOLON     = ';';
	const WHITESPACE    = ' ';
	const COMMA         = ',';
	const ERROR_START   = '#';

	const OPERATORS_SN 			= "+-";
	const OPERATORS_INFIX 		= "+-*/^&=><";
	const OPERATORS_POSTFIX 	= "%";

	/**
	 * Formula
	 *
	 * @var string
	 */
	private $_formula;

	/**
	 * Tokens
	 *
	 * @var PHPExcel_Calculation_FormulaToken[]
	 */
	private $_tokens = array();

    /**
     * Create a new PHPExcel_Calculation_FormulaParser
     *
     * @param 	string		$pFormula	Formula to parse
     * @throws 	PHPExcel_Calculation_Exception
     */
    public function __construct($pFormula = '')
    {
    	// Check parameters
    	if (is_null($pFormula)) {
    		throw new PHPExcel_Calculation_Exception("Invalid parameter passed: formula");
    	}

    	// Initialise values
    	$this->_formula = trim($pFormula);
    	// Parse!
    	$this->_parseToTokens();
    }

    /**
     * Get Formula
     *
     * @return string
     */
    public function getFormula() {
    	return $this->_formula;
    }

    /**
     * Get Token
     *
     * @param 	int		$pId	Token id
     * @return	string
     * @throws  PHPExcel_Calculation_Exception
     */
    public function getToken($pId = 0) {
    	if (isset($this->_tokens[$pId])) {
    		return $this->_tokens[$pId];
    	} else {
    		throw new PHPExcel_Calculation_Exception("Token with id $pId does not exist.");
    	}
    }

    /**
     * Get Token count
     *
     * @return string
     */
    public function getTokenCount() {
    	return count($this->_tokens);
    }

    /**
     * Get Tokens
     *
     * @return PHPExcel_Calculation_FormulaToken[]
     */
    public function getTokens() {
    	return $this->_tokens;
    }

    /**
     * Parse to tokens
     */
    private function _parseToTokens() {
		// No attempt is made to verify formulas; assumes formulas are derived from Excel, where
		// they can only exist if valid; stack overflows/underflows sunk as nulls without exceptions.

		// Check if the formula has a valid starting =
		$formulaLength = strlen($this->_formula);
		if ($formulaLength < 2 || $this->_formula{0} != '=') return;

		// Helper variables
		$tokens1	= $tokens2 	= $stack = array();
		$inString	= $inPath 	= $inRange 	= $inError = false;
		$token		= $previousToken	= $nextToken	= null;

		$index	= 1;
		$value	= '';

		$ERRORS 			= array("#NULL!", "#DIV/0!", "#VALUE!", "#REF!", "#NAME?", "#NUM!", "#N/A");
		$COMPARATORS_MULTI 	= array(">=", "<=", "<>");

		while ($index < $formulaLength) {
			// state-dependent character evaluation (order is important)

			// double-quoted strings
			// embeds are doubled
			// end marks token
			if ($inString) {
				if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::QUOTE_DOUBLE) {
					if ((($index + 2) <= $formulaLength) && ($this->_formula{$index + 1} == PHPExcel_Calculation_FormulaParser::QUOTE_DOUBLE)) {
						$value .= PHPExcel_Calculation_FormulaParser::QUOTE_DOUBLE;
						++$index;
					} else {
						$inString = false;
						$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_TEXT);
						$value = "";
					}
				} else {
					$value .= $this->_formula{$index};
				}
				++$index;
				continue;
			}

			// single-quoted strings (links)
			// embeds are double
			// end does not mark a token
			if ($inPath) {
				if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::QUOTE_SINGLE) {
					if ((($index + 2) <= $formulaLength) && ($this->_formula{$index + 1} == PHPExcel_Calculation_FormulaParser::QUOTE_SINGLE)) {
						$value .= PHPExcel_Calculation_FormulaParser::QUOTE_SINGLE;
						++$index;
					} else {
						$inPath = false;
					}
				} else {
					$value .= $this->_formula{$index};
				}
				++$index;
				continue;
			}

			// bracked strings (R1C1 range index or linked workbook name)
			// no embeds (changed to "()" by Excel)
			// end does not mark a token
			if ($inRange) {
				if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::BRACKET_CLOSE) {
					$inRange = false;
				}
				$value .= $this->_formula{$index};
				++$index;
				continue;
			}

			// error values
			// end marks a token, determined from absolute list of values
			if ($inError) {
				$value .= $this->_formula{$index};
				++$index;
				if (in_array($value, $ERRORS)) {
					$inError = false;
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_ERROR);
					$value = "";
				}
				continue;
			}

			// scientific notation check
			if (strpos(PHPExcel_Calculation_FormulaParser::OPERATORS_SN, $this->_formula{$index}) !== false) {
				if (strlen($value) > 1) {
					if (preg_match("/^[1-9]{1}(\.[0-9]+)?E{1}$/", $this->_formula{$index}) != 0) {
						$value .= $this->_formula{$index};
						++$index;
						continue;
					}
				}
			}

			// independent character evaluation (order not important)

			// establish state-dependent character evaluations
			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::QUOTE_DOUBLE) {
				if (strlen($value > 0)) {  // unexpected
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_UNKNOWN);
					$value = "";
				}
				$inString = true;
				++$index;
				continue;
 			}

			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::QUOTE_SINGLE) {
				if (strlen($value) > 0) { // unexpected
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_UNKNOWN);
					$value = "";
				}
				$inPath = true;
				++$index;
				continue;
			}

			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::BRACKET_OPEN) {
				$inRange = true;
				$value .= PHPExcel_Calculation_FormulaParser::BRACKET_OPEN;
				++$index;
				continue;
			}

			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::ERROR_START) {
				if (strlen($value) > 0) { // unexpected
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_UNKNOWN);
					$value = "";
				}
				$inError = true;
				$value .= PHPExcel_Calculation_FormulaParser::ERROR_START;
				++$index;
				continue;
			}

			// mark start and end of arrays and array rows
			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::BRACE_OPEN) {
				if (strlen($value) > 0) { // unexpected
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_UNKNOWN);
					$value = "";
				}

				$tmp = new PHPExcel_Calculation_FormulaToken("ARRAY", PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_START);
				$tokens1[] = $tmp;
				$stack[] = clone $tmp;

				$tmp = new PHPExcel_Calculation_FormulaToken("ARRAYROW", PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_START);
				$tokens1[] = $tmp;
				$stack[] = clone $tmp;

				++$index;
				continue;
			}

			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::SEMICOLON) {
				if (strlen($value) > 0) {
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND);
					$value = "";
				}

				$tmp = array_pop($stack);
				$tmp->setValue("");
				$tmp->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP);
				$tokens1[] = $tmp;

				$tmp = new PHPExcel_Calculation_FormulaToken(",", PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_ARGUMENT);
				$tokens1[] = $tmp;

				$tmp = new PHPExcel_Calculation_FormulaToken("ARRAYROW", PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_START);
				$tokens1[] = $tmp;
				$stack[] = clone $tmp;

				++$index;
				continue;
			}

			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::BRACE_CLOSE) {
				if (strlen($value) > 0) {
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND);
					$value = "";
				}

				$tmp = array_pop($stack);
				$tmp->setValue("");
				$tmp->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP);
				$tokens1[] = $tmp;

				$tmp = array_pop($stack);
				$tmp->setValue("");
				$tmp->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP);
				$tokens1[] = $tmp;

				++$index;
				continue;
			}

			// trim white-space
			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::WHITESPACE) {
				if (strlen($value) > 0) {
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND);
					$value = "";
				}
				$tokens1[] = new PHPExcel_Calculation_FormulaToken("", PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_WHITESPACE);
				++$index;
				while (($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::WHITESPACE) && ($index < $formulaLength)) {
					++$index;
				}
				continue;
			}

			// multi-character comparators
			if (($index + 2) <= $formulaLength) {
				if (in_array(substr($this->_formula, $index, 2), $COMPARATORS_MULTI)) {
					if (strlen($value) > 0) {
						$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND);
						$value = "";
					}
					$tokens1[] = new PHPExcel_Calculation_FormulaToken(substr($this->_formula, $index, 2), PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORINFIX, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_LOGICAL);
					$index += 2;
					continue;
				}
			}

			// standard infix operators
			if (strpos(PHPExcel_Calculation_FormulaParser::OPERATORS_INFIX, $this->_formula{$index}) !== false) {
				if (strlen($value) > 0) {
					$tokens1[] =new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND);
					$value = "";
				}
				$tokens1[] = new PHPExcel_Calculation_FormulaToken($this->_formula{$index}, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORINFIX);
				++$index;
				continue;
			}

			// standard postfix operators (only one)
			if (strpos(PHPExcel_Calculation_FormulaParser::OPERATORS_POSTFIX, $this->_formula{$index}) !== false) {
				if (strlen($value) > 0) {
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND);
					$value = "";
				}
				$tokens1[] = new PHPExcel_Calculation_FormulaToken($this->_formula{$index}, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORPOSTFIX);
				++$index;
				continue;
			}

			// start subexpression or function
			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::PAREN_OPEN) {
				if (strlen($value) > 0) {
					$tmp = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_START);
					$tokens1[] = $tmp;
					$stack[] = clone $tmp;
					$value = "";
				} else {
					$tmp = new PHPExcel_Calculation_FormulaToken("", PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_SUBEXPRESSION, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_START);
					$tokens1[] = $tmp;
					$stack[] = clone $tmp;
				}
				++$index;
				continue;
			}

			// function, subexpression, or array parameters, or operand unions
			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::COMMA) {
				if (strlen($value) > 0) {
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND);
					$value = "";
				}

				$tmp = array_pop($stack);
				$tmp->setValue("");
				$tmp->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP);
				$stack[] = $tmp;

				if ($tmp->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION) {
					$tokens1[] = new PHPExcel_Calculation_FormulaToken(",", PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORINFIX, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_UNION);
				} else {
					$tokens1[] = new PHPExcel_Calculation_FormulaToken(",", PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_ARGUMENT);
				}
				++$index;
				continue;
			}

			// stop subexpression
			if ($this->_formula{$index} == PHPExcel_Calculation_FormulaParser::PAREN_CLOSE) {
				if (strlen($value) > 0) {
					$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND);
					$value = "";
				}

				$tmp = array_pop($stack);
				$tmp->setValue("");
				$tmp->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP);
				$tokens1[] = $tmp;

				++$index;
				continue;
			}

        	// token accumulation
			$value .= $this->_formula{$index};
			++$index;
		}

		// dump remaining accumulation
		if (strlen($value) > 0) {
			$tokens1[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND);
		}

		// move tokenList to new set, excluding unnecessary white-space tokens and converting necessary ones to intersections
		$tokenCount = count($tokens1);
		for ($i = 0; $i < $tokenCount; ++$i) {
			$token = $tokens1[$i];
			if (isset($tokens1[$i - 1])) {
				$previousToken = $tokens1[$i - 1];
			} else {
				$previousToken = null;
			}
			if (isset($tokens1[$i + 1])) {
				$nextToken = $tokens1[$i + 1];
			} else {
				$nextToken = null;
			}

			if (is_null($token)) {
				continue;
			}

			if ($token->getTokenType() != PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_WHITESPACE) {
				$tokens2[] = $token;
				continue;
			}

			if (is_null($previousToken)) {
				continue;
			}

			if (! (
					(($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION) && ($previousToken->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP)) ||
					(($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_SUBEXPRESSION) && ($previousToken->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP)) ||
					($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND)
				  ) ) {
				continue;
			}

			if (is_null($nextToken)) {
				continue;
			}

			if (! (
					(($nextToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION) && ($nextToken->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_START)) ||
					(($nextToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_SUBEXPRESSION) && ($nextToken->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_START)) ||
					($nextToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND)
				  ) ) {
				continue;
			}

			$tokens2[] = new PHPExcel_Calculation_FormulaToken($value, PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORINFIX, PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_INTERSECTION);
		}

		// move tokens to final list, switching infix "-" operators to prefix when appropriate, switching infix "+" operators
		// to noop when appropriate, identifying operand and infix-operator subtypes, and pulling "@" from function names
		$this->_tokens = array();

		$tokenCount = count($tokens2);
		for ($i = 0; $i < $tokenCount; ++$i) {
			$token = $tokens2[$i];
			if (isset($tokens2[$i - 1])) {
				$previousToken = $tokens2[$i - 1];
			} else {
				$previousToken = null;
			}
			if (isset($tokens2[$i + 1])) {
				$nextToken = $tokens2[$i + 1];
			} else {
				$nextToken = null;
			}

			if (is_null($token)) {
				continue;
			}

			if ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORINFIX && $token->getValue() == "-") {
				if ($i == 0) {
					$token->setTokenType(PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORPREFIX);
				} else if (
							(($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION) && ($previousToken->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP)) ||
							(($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_SUBEXPRESSION) && ($previousToken->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP)) ||
							($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORPOSTFIX) ||
							($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND)
						) {
					$token->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_MATH);
				} else {
					$token->setTokenType(PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORPREFIX);
				}

				$this->_tokens[] = $token;
				continue;
			}

			if ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORINFIX && $token->getValue() == "+") {
				if ($i == 0) {
					continue;
				} else if (
							(($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION) && ($previousToken->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP)) ||
							(($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_SUBEXPRESSION) && ($previousToken->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_STOP)) ||
							($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORPOSTFIX) ||
							($previousToken->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND)
						) {
					$token->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_MATH);
				} else {
					continue;
				}

				$this->_tokens[] = $token;
				continue;
			}

			if ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERATORINFIX && $token->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_NOTHING) {
				if (strpos("<>=", substr($token->getValue(), 0, 1)) !== false) {
					$token->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_LOGICAL);
				} else if ($token->getValue() == "&") {
					$token->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_CONCATENATION);
				} else {
					$token->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_MATH);
				}

				$this->_tokens[] = $token;
				continue;
			}

			if ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_OPERAND && $token->getTokenSubType() == PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_NOTHING) {
				if (!is_numeric($token->getValue())) {
					if (strtoupper($token->getValue()) == "TRUE" || strtoupper($token->getValue() == "FALSE")) {
						$token->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_LOGICAL);
					} else {
						$token->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_RANGE);
					}
				} else {
					$token->setTokenSubType(PHPExcel_Calculation_FormulaToken::TOKEN_SUBTYPE_NUMBER);
				}

				$this->_tokens[] = $token;
				continue;
			}

			if ($token->getTokenType() == PHPExcel_Calculation_FormulaToken::TOKEN_TYPE_FUNCTION) {
				if (strlen($token->getValue() > 0)) {
					if (substr($token->getValue(), 0, 1) == "@") {
						$token->setValue(substr($token->getValue(), 1));
					}
				}
			}

        	$this->_tokens[] = $token;
		}
    }
}
