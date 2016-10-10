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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301 USA
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license		http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version		1.8.0, 2014-03-02
 */


/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
	/**
	 * @ignore
	 */
	define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
	require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}


/**
 * PHPExcel_Calculation_LookupRef
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_LookupRef {


	/**
	 * CELL_ADDRESS
	 *
	 * Creates a cell address as text, given specified row and column numbers.
	 *
	 * Excel Function:
	 *		=ADDRESS(row, column, [relativity], [referenceStyle], [sheetText])
	 *
	 * @param	row				Row number to use in the cell reference
	 * @param	column			Column number to use in the cell reference
	 * @param	relativity		Flag indicating the type of reference to return
	 *								1 or omitted	Absolute
	 *								2				Absolute row; relative column
	 *								3				Relative row; absolute column
	 *								4				Relative
	 * @param	referenceStyle	A logical value that specifies the A1 or R1C1 reference style.
	 *								TRUE or omitted		CELL_ADDRESS returns an A1-style reference
	 *								FALSE				CELL_ADDRESS returns an R1C1-style reference
	 * @param	sheetText		Optional Name of worksheet to use
	 * @return	string
	 */
	public static function CELL_ADDRESS($row, $column, $relativity=1, $referenceStyle=True, $sheetText='') {
		$row		= PHPExcel_Calculation_Functions::flattenSingleValue($row);
		$column		= PHPExcel_Calculation_Functions::flattenSingleValue($column);
		$relativity	= PHPExcel_Calculation_Functions::flattenSingleValue($relativity);
		$sheetText	= PHPExcel_Calculation_Functions::flattenSingleValue($sheetText);

		if (($row < 1) || ($column < 1)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if ($sheetText > '') {
			if (strpos($sheetText,' ') !== False) { $sheetText = "'".$sheetText."'"; }
			$sheetText .='!';
		}
		if ((!is_bool($referenceStyle)) || $referenceStyle) {
			$rowRelative = $columnRelative = '$';
			$column = PHPExcel_Cell::stringFromColumnIndex($column-1);
			if (($relativity == 2) || ($relativity == 4)) { $columnRelative = ''; }
			if (($relativity == 3) || ($relativity == 4)) { $rowRelative = ''; }
			return $sheetText.$columnRelative.$column.$rowRelative.$row;
		} else {
			if (($relativity == 2) || ($relativity == 4)) { $column = '['.$column.']'; }
			if (($relativity == 3) || ($relativity == 4)) { $row = '['.$row.']'; }
			return $sheetText.'R'.$row.'C'.$column;
		}
	}	//	function CELL_ADDRESS()


	/**
	 * COLUMN
	 *
	 * Returns the column number of the given cell reference
	 * If the cell reference is a range of cells, COLUMN returns the column numbers of each column in the reference as a horizontal array.
	 * If cell reference is omitted, and the function is being called through the calculation engine, then it is assumed to be the
	 *		reference of the cell in which the COLUMN function appears; otherwise this function returns 0.
	 *
	 * Excel Function:
	 *		=COLUMN([cellAddress])
	 *
	 * @param	cellAddress		A reference to a range of cells for which you want the column numbers
	 * @return	integer or array of integer
	 */
	public static function COLUMN($cellAddress=Null) {
		if (is_null($cellAddress) || trim($cellAddress) === '') { return 0; }

		if (is_array($cellAddress)) {
			foreach($cellAddress as $columnKey => $value) {
				$columnKey = preg_replace('/[^a-z]/i','',$columnKey);
				return (integer) PHPExcel_Cell::columnIndexFromString($columnKey);
			}
		} else {
			if (strpos($cellAddress,'!') !== false) {
				list($sheet,$cellAddress) = explode('!',$cellAddress);
			}
			if (strpos($cellAddress,':') !== false) {
				list($startAddress,$endAddress) = explode(':',$cellAddress);
				$startAddress = preg_replace('/[^a-z]/i','',$startAddress);
				$endAddress = preg_replace('/[^a-z]/i','',$endAddress);
				$returnValue = array();
				do {
					$returnValue[] = (integer) PHPExcel_Cell::columnIndexFromString($startAddress);
				} while ($startAddress++ != $endAddress);
				return $returnValue;
			} else {
				$cellAddress = preg_replace('/[^a-z]/i','',$cellAddress);
				return (integer) PHPExcel_Cell::columnIndexFromString($cellAddress);
			}
		}
	}	//	function COLUMN()


	/**
	 * COLUMNS
	 *
	 * Returns the number of columns in an array or reference.
	 *
	 * Excel Function:
	 *		=COLUMNS(cellAddress)
	 *
	 * @param	cellAddress		An array or array formula, or a reference to a range of cells for which you want the number of columns
	 * @return	integer			The number of columns in cellAddress
	 */
	public static function COLUMNS($cellAddress=Null) {
		if (is_null($cellAddress) || $cellAddress === '') {
			return 1;
		} elseif (!is_array($cellAddress)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		$x = array_keys($cellAddress);
		$x = array_shift($x);
		$isMatrix = (is_numeric($x));
		list($columns,$rows) = PHPExcel_Calculation::_getMatrixDimensions($cellAddress);

		if ($isMatrix) {
			return $rows;
		} else {
			return $columns;
		}
	}	//	function COLUMNS()


	/**
	 * ROW
	 *
	 * Returns the row number of the given cell reference
	 * If the cell reference is a range of cells, ROW returns the row numbers of each row in the reference as a vertical array.
	 * If cell reference is omitted, and the function is being called through the calculation engine, then it is assumed to be the
	 *		reference of the cell in which the ROW function appears; otherwise this function returns 0.
	 *
	 * Excel Function:
	 *		=ROW([cellAddress])
	 *
	 * @param	cellAddress		A reference to a range of cells for which you want the row numbers
	 * @return	integer or array of integer
	 */
	public static function ROW($cellAddress=Null) {
		if (is_null($cellAddress) || trim($cellAddress) === '') { return 0; }

		if (is_array($cellAddress)) {
			foreach($cellAddress as $columnKey => $rowValue) {
				foreach($rowValue as $rowKey => $cellValue) {
					return (integer) preg_replace('/[^0-9]/i','',$rowKey);
				}
			}
		} else {
			if (strpos($cellAddress,'!') !== false) {
				list($sheet,$cellAddress) = explode('!',$cellAddress);
			}
			if (strpos($cellAddress,':') !== false) {
				list($startAddress,$endAddress) = explode(':',$cellAddress);
				$startAddress = preg_replace('/[^0-9]/','',$startAddress);
				$endAddress = preg_replace('/[^0-9]/','',$endAddress);
				$returnValue = array();
				do {
					$returnValue[][] = (integer) $startAddress;
				} while ($startAddress++ != $endAddress);
				return $returnValue;
			} else {
				list($cellAddress) = explode(':',$cellAddress);
				return (integer) preg_replace('/[^0-9]/','',$cellAddress);
			}
		}
	}	//	function ROW()


	/**
	 * ROWS
	 *
	 * Returns the number of rows in an array or reference.
	 *
	 * Excel Function:
	 *		=ROWS(cellAddress)
	 *
	 * @param	cellAddress		An array or array formula, or a reference to a range of cells for which you want the number of rows
	 * @return	integer			The number of rows in cellAddress
	 */
	public static function ROWS($cellAddress=Null) {
		if (is_null($cellAddress) || $cellAddress === '') {
			return 1;
		} elseif (!is_array($cellAddress)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		$i = array_keys($cellAddress);
		$isMatrix = (is_numeric(array_shift($i)));
		list($columns,$rows) = PHPExcel_Calculation::_getMatrixDimensions($cellAddress);

		if ($isMatrix) {
			return $columns;
		} else {
			return $rows;
		}
	}	//	function ROWS()


	/**
	 * HYPERLINK
	 *
	 * Excel Function:
	 *		=HYPERLINK(linkURL,displayName)
	 *
	 * @access	public
	 * @category Logical Functions
	 * @param	string			$linkURL		Value to check, is also the value returned when no error
	 * @param	string			$displayName	Value to return when testValue is an error condition
	 * @param	PHPExcel_Cell	$pCell			The cell to set the hyperlink in
	 * @return	mixed	The value of $displayName (or $linkURL if $displayName was blank)
	 */
	public static function HYPERLINK($linkURL = '', $displayName = null, PHPExcel_Cell $pCell = null) {
		$args = func_get_args();
		$pCell = array_pop($args);

		$linkURL		= (is_null($linkURL))		? '' :	PHPExcel_Calculation_Functions::flattenSingleValue($linkURL);
		$displayName	= (is_null($displayName))	? '' :	PHPExcel_Calculation_Functions::flattenSingleValue($displayName);

		if ((!is_object($pCell)) || (trim($linkURL) == '')) {
			return PHPExcel_Calculation_Functions::REF();
		}

		if ((is_object($displayName)) || trim($displayName) == '') {
			$displayName = $linkURL;
		}

		$pCell->getHyperlink()->setUrl($linkURL);

		return $displayName;
	}	//	function HYPERLINK()


	/**
	 * INDIRECT
	 *
	 * Returns the reference specified by a text string.
	 * References are immediately evaluated to display their contents.
	 *
	 * Excel Function:
	 *		=INDIRECT(cellAddress)
	 *
	 * NOTE - INDIRECT() does not yet support the optional a1 parameter introduced in Excel 2010
	 *
	 * @param	cellAddress		$cellAddress	The cell address of the current cell (containing this formula)
	 * @param	PHPExcel_Cell	$pCell			The current cell (containing this formula)
	 * @return	mixed			The cells referenced by cellAddress
	 *
	 * @todo	Support for the optional a1 parameter introduced in Excel 2010
	 *
	 */
	public static function INDIRECT($cellAddress = NULL, PHPExcel_Cell $pCell = NULL) {
		$cellAddress	= PHPExcel_Calculation_Functions::flattenSingleValue($cellAddress);
		if (is_null($cellAddress) || $cellAddress === '') {
			return PHPExcel_Calculation_Functions::REF();
		}

		$cellAddress1 = $cellAddress;
		$cellAddress2 = NULL;
		if (strpos($cellAddress,':') !== false) {
			list($cellAddress1,$cellAddress2) = explode(':',$cellAddress);
		}

		if ((!preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF.'$/i', $cellAddress1, $matches)) ||
			((!is_null($cellAddress2)) && (!preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF.'$/i', $cellAddress2, $matches)))) {
			if (!preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_NAMEDRANGE.'$/i', $cellAddress1, $matches)) {
				return PHPExcel_Calculation_Functions::REF();
			}

			if (strpos($cellAddress,'!') !== FALSE) {
				list($sheetName, $cellAddress) = explode('!',$cellAddress);
				$sheetName = trim($sheetName, "'");
				$pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
			} else {
				$pSheet = $pCell->getWorksheet();
			}

			return PHPExcel_Calculation::getInstance()->extractNamedRange($cellAddress, $pSheet, FALSE);
		}

		if (strpos($cellAddress,'!') !== FALSE) {
			list($sheetName,$cellAddress) = explode('!',$cellAddress);
			$sheetName = trim($sheetName, "'");
			$pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
		} else {
			$pSheet = $pCell->getWorksheet();
		}

		return PHPExcel_Calculation::getInstance()->extractCellRange($cellAddress, $pSheet, FALSE);
	}	//	function INDIRECT()


	/**
	 * OFFSET
	 *
	 * Returns a reference to a range that is a specified number of rows and columns from a cell or range of cells.
	 * The reference that is returned can be a single cell or a range of cells. You can specify the number of rows and
	 * the number of columns to be returned.
	 *
	 * Excel Function:
	 *		=OFFSET(cellAddress, rows, cols, [height], [width])
	 *
	 * @param	cellAddress		The reference from which you want to base the offset. Reference must refer to a cell or
	 *								range of adjacent cells; otherwise, OFFSET returns the #VALUE! error value.
	 * @param	rows			The number of rows, up or down, that you want the upper-left cell to refer to.
	 *								Using 5 as the rows argument specifies that the upper-left cell in the reference is
	 *								five rows below reference. Rows can be positive (which means below the starting reference)
	 *								or negative (which means above the starting reference).
	 * @param	cols			The number of columns, to the left or right, that you want the upper-left cell of the result
	 *								to refer to. Using 5 as the cols argument specifies that the upper-left cell in the
	 *								reference is five columns to the right of reference. Cols can be positive (which means
	 *								to the right of the starting reference) or negative (which means to the left of the
	 *								starting reference).
	 * @param	height			The height, in number of rows, that you want the returned reference to be. Height must be a positive number.
	 * @param	width			The width, in number of columns, that you want the returned reference to be. Width must be a positive number.
	 * @return	string			A reference to a cell or range of cells
	 */
	public static function OFFSET($cellAddress=Null,$rows=0,$columns=0,$height=null,$width=null) {
		$rows		= PHPExcel_Calculation_Functions::flattenSingleValue($rows);
		$columns	= PHPExcel_Calculation_Functions::flattenSingleValue($columns);
		$height		= PHPExcel_Calculation_Functions::flattenSingleValue($height);
		$width		= PHPExcel_Calculation_Functions::flattenSingleValue($width);
		if ($cellAddress == Null) {
			return 0;
		}

		$args = func_get_args();
		$pCell = array_pop($args);
		if (!is_object($pCell)) {
			return PHPExcel_Calculation_Functions::REF();
		}

		$sheetName = NULL;
		if (strpos($cellAddress,"!")) {
			list($sheetName,$cellAddress) = explode("!",$cellAddress);
			$sheetName = trim($sheetName, "'");
		}
		if (strpos($cellAddress,":")) {
			list($startCell,$endCell) = explode(":",$cellAddress);
		} else {
			$startCell = $endCell = $cellAddress;
		}
		list($startCellColumn,$startCellRow) = PHPExcel_Cell::coordinateFromString($startCell);
		list($endCellColumn,$endCellRow) = PHPExcel_Cell::coordinateFromString($endCell);

		$startCellRow += $rows;
		$startCellColumn = PHPExcel_Cell::columnIndexFromString($startCellColumn) - 1;
		$startCellColumn += $columns;

		if (($startCellRow <= 0) || ($startCellColumn < 0)) {
			return PHPExcel_Calculation_Functions::REF();
		}
		$endCellColumn = PHPExcel_Cell::columnIndexFromString($endCellColumn) - 1;
		if (($width != null) && (!is_object($width))) {
			$endCellColumn = $startCellColumn + $width - 1;
		} else {
			$endCellColumn += $columns;
		}
		$startCellColumn = PHPExcel_Cell::stringFromColumnIndex($startCellColumn);

		if (($height != null) && (!is_object($height))) {
			$endCellRow = $startCellRow + $height - 1;
		} else {
			$endCellRow += $rows;
		}

		if (($endCellRow <= 0) || ($endCellColumn < 0)) {
			return PHPExcel_Calculation_Functions::REF();
		}
		$endCellColumn = PHPExcel_Cell::stringFromColumnIndex($endCellColumn);

		$cellAddress = $startCellColumn.$startCellRow;
		if (($startCellColumn != $endCellColumn) || ($startCellRow != $endCellRow)) {
			$cellAddress .= ':'.$endCellColumn.$endCellRow;
		}

		if ($sheetName !== NULL) {
			$pSheet = $pCell->getWorksheet()->getParent()->getSheetByName($sheetName);
		} else {
			$pSheet = $pCell->getWorksheet();
		}

		return PHPExcel_Calculation::getInstance()->extractCellRange($cellAddress, $pSheet, False);
	}	//	function OFFSET()


	/**
	 * CHOOSE
	 *
	 * Uses lookup_value to return a value from the list of value arguments.
	 * Use CHOOSE to select one of up to 254 values based on the lookup_value.
	 *
	 * Excel Function:
	 *		=CHOOSE(index_num, value1, [value2], ...)
	 *
	 * @param	index_num		Specifies which value argument is selected.
	 *							Index_num must be a number between 1 and 254, or a formula or reference to a cell containing a number
	 *								between 1 and 254.
	 * @param	value1...		Value1 is required, subsequent values are optional.
	 *							Between 1 to 254 value arguments from which CHOOSE selects a value or an action to perform based on
	 *								index_num. The arguments can be numbers, cell references, defined names, formulas, functions, or
	 *								text.
	 * @return	mixed			The selected value
	 */
	public static function CHOOSE() {
		$chooseArgs = func_get_args();
		$chosenEntry = PHPExcel_Calculation_Functions::flattenArray(array_shift($chooseArgs));
		$entryCount = count($chooseArgs) - 1;

		if(is_array($chosenEntry)) {
			$chosenEntry = array_shift($chosenEntry);
		}
		if ((is_numeric($chosenEntry)) && (!is_bool($chosenEntry))) {
			--$chosenEntry;
		} else {
			return PHPExcel_Calculation_Functions::VALUE();
		}
		$chosenEntry = floor($chosenEntry);
		if (($chosenEntry < 0) || ($chosenEntry > $entryCount)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (is_array($chooseArgs[$chosenEntry])) {
			return PHPExcel_Calculation_Functions::flattenArray($chooseArgs[$chosenEntry]);
		} else {
			return $chooseArgs[$chosenEntry];
		}
	}	//	function CHOOSE()


	/**
	 * MATCH
	 *
	 * The MATCH function searches for a specified item in a range of cells
	 *
	 * Excel Function:
	 *		=MATCH(lookup_value, lookup_array, [match_type])
	 *
	 * @param	lookup_value	The value that you want to match in lookup_array
	 * @param	lookup_array	The range of cells being searched
	 * @param	match_type		The number -1, 0, or 1. -1 means above, 0 means exact match, 1 means below. If match_type is 1 or -1, the list has to be ordered.
	 * @return	integer			The relative position of the found item
	 */
	public static function MATCH($lookup_value, $lookup_array, $match_type=1) {
		$lookup_array = PHPExcel_Calculation_Functions::flattenArray($lookup_array);
		$lookup_value = PHPExcel_Calculation_Functions::flattenSingleValue($lookup_value);
		$match_type	= (is_null($match_type)) ? 1 : (int) PHPExcel_Calculation_Functions::flattenSingleValue($match_type);
		//	MATCH is not case sensitive
		$lookup_value = strtolower($lookup_value);

		//	lookup_value type has to be number, text, or logical values
		if ((!is_numeric($lookup_value)) && (!is_string($lookup_value)) && (!is_bool($lookup_value))) {
			return PHPExcel_Calculation_Functions::NA();
		}

		//	match_type is 0, 1 or -1
		if (($match_type !== 0) && ($match_type !== -1) && ($match_type !== 1)) {
			return PHPExcel_Calculation_Functions::NA();
		}

		//	lookup_array should not be empty
		$lookupArraySize = count($lookup_array);
		if ($lookupArraySize <= 0) {
			return PHPExcel_Calculation_Functions::NA();
		}

		//	lookup_array should contain only number, text, or logical values, or empty (null) cells
		foreach($lookup_array as $i => $lookupArrayValue) {
			//	check the type of the value
			if ((!is_numeric($lookupArrayValue)) && (!is_string($lookupArrayValue)) &&
				(!is_bool($lookupArrayValue)) && (!is_null($lookupArrayValue))) {
				return PHPExcel_Calculation_Functions::NA();
			}
			//	convert strings to lowercase for case-insensitive testing
			if (is_string($lookupArrayValue)) {
				$lookup_array[$i] = strtolower($lookupArrayValue);
			}
			if ((is_null($lookupArrayValue)) && (($match_type == 1) || ($match_type == -1))) {
				$lookup_array = array_slice($lookup_array,0,$i-1);
			}
		}

		// if match_type is 1 or -1, the list has to be ordered
		if ($match_type == 1) {
			asort($lookup_array);
			$keySet = array_keys($lookup_array);
		} elseif($match_type == -1) {
			arsort($lookup_array);
			$keySet = array_keys($lookup_array);
		}

		// **
		// find the match
		// **
		// loop on the cells
//		var_dump($lookup_array);
//		echo '<br />';
		foreach($lookup_array as $i => $lookupArrayValue) {
			if (($match_type == 0) && ($lookupArrayValue == $lookup_value)) {
				//	exact match
				return ++$i;
			} elseif (($match_type == -1) && ($lookupArrayValue <= $lookup_value)) {
//				echo '$i = '.$i.' => ';
//				var_dump($lookupArrayValue);
//				echo '<br />';
//				echo 'Keyset = ';
//				var_dump($keySet);
//				echo '<br />';
				$i = array_search($i,$keySet);
//				echo '$i='.$i.'<br />';
				// if match_type is -1 <=> find the smallest value that is greater than or equal to lookup_value
				if ($i < 1){
					// 1st cell was allready smaller than the lookup_value
					break;
				} else {
					// the previous cell was the match
					return $keySet[$i-1]+1;
				}
			} elseif (($match_type == 1) && ($lookupArrayValue >= $lookup_value)) {
//				echo '$i = '.$i.' => ';
//				var_dump($lookupArrayValue);
//				echo '<br />';
//				echo 'Keyset = ';
//				var_dump($keySet);
//				echo '<br />';
				$i = array_search($i,$keySet);
//				echo '$i='.$i.'<br />';
				// if match_type is 1 <=> find the largest value that is less than or equal to lookup_value
				if ($i < 1){
					// 1st cell was allready bigger than the lookup_value
					break;
				} else {
					// the previous cell was the match
					return $keySet[$i-1]+1;
				}
			}
		}

		//	unsuccessful in finding a match, return #N/A error value
		return PHPExcel_Calculation_Functions::NA();
	}	//	function MATCH()


	/**
	 * INDEX
	 *
	 * Uses an index to choose a value from a reference or array
	 *
	 * Excel Function:
	 *		=INDEX(range_array, row_num, [column_num])
	 *
	 * @param	range_array		A range of cells or an array constant
	 * @param	row_num			The row in array from which to return a value. If row_num is omitted, column_num is required.
	 * @param	column_num		The column in array from which to return a value. If column_num is omitted, row_num is required.
	 * @return	mixed			the value of a specified cell or array of cells
	 */
	public static function INDEX($arrayValues,$rowNum = 0,$columnNum = 0) {

		if (($rowNum < 0) || ($columnNum < 0)) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		if (!is_array($arrayValues)) {
			return PHPExcel_Calculation_Functions::REF();
		}

		$rowKeys = array_keys($arrayValues);
		$columnKeys = @array_keys($arrayValues[$rowKeys[0]]);

		if ($columnNum > count($columnKeys)) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($columnNum == 0) {
			if ($rowNum == 0) {
				return $arrayValues;
			}
			$rowNum = $rowKeys[--$rowNum];
			$returnArray = array();
			foreach($arrayValues as $arrayColumn) {
				if (is_array($arrayColumn)) {
					if (isset($arrayColumn[$rowNum])) {
						$returnArray[] = $arrayColumn[$rowNum];
					} else {
						return $arrayValues[$rowNum];
					}
				} else {
					return $arrayValues[$rowNum];
				}
			}
			return $returnArray;
		}
		$columnNum = $columnKeys[--$columnNum];
		if ($rowNum > count($rowKeys)) {
			return PHPExcel_Calculation_Functions::VALUE();
		} elseif ($rowNum == 0) {
			return $arrayValues[$columnNum];
		}
		$rowNum = $rowKeys[--$rowNum];

		return $arrayValues[$rowNum][$columnNum];
	}	//	function INDEX()


	/**
	 * TRANSPOSE
	 *
	 * @param	array	$matrixData	A matrix of values
	 * @return	array
	 *
	 * Unlike the Excel TRANSPOSE function, which will only work on a single row or column, this function will transpose a full matrix.
	 */
	public static function TRANSPOSE($matrixData) {
		$returnMatrix = array();
		if (!is_array($matrixData)) { $matrixData = array(array($matrixData)); }

		$column = 0;
		foreach($matrixData as $matrixRow) {
			$row = 0;
			foreach($matrixRow as $matrixCell) {
				$returnMatrix[$row][$column] = $matrixCell;
				++$row;
			}
			++$column;
		}
		return $returnMatrix;
	}	//	function TRANSPOSE()


	private static function _vlookupSort($a,$b) {
		$f = array_keys($a);
		$firstColumn = array_shift($f);
		if (strtolower($a[$firstColumn]) == strtolower($b[$firstColumn])) {
			return 0;
		}
		return (strtolower($a[$firstColumn]) < strtolower($b[$firstColumn])) ? -1 : 1;
	}	//	function _vlookupSort()


	/**
	* VLOOKUP
	* The VLOOKUP function searches for value in the left-most column of lookup_array and returns the value in the same row based on the index_number.
	* @param	lookup_value	The value that you want to match in lookup_array
	* @param	lookup_array	The range of cells being searched
	* @param	index_number	The column number in table_array from which the matching value must be returned. The first column is 1.
	* @param	not_exact_match	Determines if you are looking for an exact match based on lookup_value.
	* @return	mixed			The value of the found cell
	*/
	public static function VLOOKUP($lookup_value, $lookup_array, $index_number, $not_exact_match=true) {
		$lookup_value	= PHPExcel_Calculation_Functions::flattenSingleValue($lookup_value);
		$index_number	= PHPExcel_Calculation_Functions::flattenSingleValue($index_number);
		$not_exact_match	= PHPExcel_Calculation_Functions::flattenSingleValue($not_exact_match);

		// index_number must be greater than or equal to 1
		if ($index_number < 1) {
			return PHPExcel_Calculation_Functions::VALUE();
		}

		// index_number must be less than or equal to the number of columns in lookup_array
		if ((!is_array($lookup_array)) || (empty($lookup_array))) {
			return PHPExcel_Calculation_Functions::REF();
		} else {
			$f = array_keys($lookup_array);
			$firstRow = array_pop($f);
			if ((!is_array($lookup_array[$firstRow])) || ($index_number > count($lookup_array[$firstRow]))) {
				return PHPExcel_Calculation_Functions::REF();
			} else {
				$columnKeys = array_keys($lookup_array[$firstRow]);
				$returnColumn = $columnKeys[--$index_number];
				$firstColumn = array_shift($columnKeys);
			}
		}

		if (!$not_exact_match) {
			uasort($lookup_array,array('self','_vlookupSort'));
		}

		$rowNumber = $rowValue = False;
		foreach($lookup_array as $rowKey => $rowData) {
			if ((is_numeric($lookup_value) && is_numeric($rowData[$firstColumn]) && ($rowData[$firstColumn] > $lookup_value)) ||
				(!is_numeric($lookup_value) && !is_numeric($rowData[$firstColumn]) && (strtolower($rowData[$firstColumn]) > strtolower($lookup_value)))) {
				break;
			}
			$rowNumber = $rowKey;
			$rowValue = $rowData[$firstColumn];
		}

		if ($rowNumber !== false) {
			if ((!$not_exact_match) && ($rowValue != $lookup_value)) {
				//	if an exact match is required, we have what we need to return an appropriate response
				return PHPExcel_Calculation_Functions::NA();
			} else {
				//	otherwise return the appropriate value
				$result = $lookup_array[$rowNumber][$returnColumn];
				if ((is_numeric($lookup_value) && is_numeric($result)) ||
					(!is_numeric($lookup_value) && !is_numeric($result))) {
					return $result;
				}
			}
		}

		return PHPExcel_Calculation_Functions::NA();
	}	//	function VLOOKUP()


/**
    * HLOOKUP
    * The HLOOKUP function searches for value in the top-most row of lookup_array and returns the value in the same column based on the index_number.
    * @param    lookup_value    The value that you want to match in lookup_array
    * @param    lookup_array    The range of cells being searched
    * @param    index_number    The row number in table_array from which the matching value must be returned. The first row is 1.
    * @param    not_exact_match Determines if you are looking for an exact match based on lookup_value.
    * @return   mixed           The value of the found cell
    */
    public static function HLOOKUP($lookup_value, $lookup_array, $index_number, $not_exact_match=true) {
        $lookup_value   = PHPExcel_Calculation_Functions::flattenSingleValue($lookup_value);
        $index_number   = PHPExcel_Calculation_Functions::flattenSingleValue($index_number);
        $not_exact_match    = PHPExcel_Calculation_Functions::flattenSingleValue($not_exact_match);

        // index_number must be greater than or equal to 1
        if ($index_number < 1) {
            return PHPExcel_Calculation_Functions::VALUE();
        }

        // index_number must be less than or equal to the number of columns in lookup_array
        if ((!is_array($lookup_array)) || (empty($lookup_array))) {
            return PHPExcel_Calculation_Functions::REF();
        } else {
            $f = array_keys($lookup_array);
            $firstRow = array_pop($f);
            if ((!is_array($lookup_array[$firstRow])) || ($index_number > count($lookup_array[$firstRow]))) {
                return PHPExcel_Calculation_Functions::REF();
            } else {
                $columnKeys = array_keys($lookup_array[$firstRow]);
                                $firstkey = $f[0] - 1;
                $returnColumn = $firstkey + $index_number;
                $firstColumn = array_shift($f);
            }
        }

        if (!$not_exact_match) {
            $firstRowH = asort($lookup_array[$firstColumn]);
        }

        $rowNumber = $rowValue = False;
        foreach($lookup_array[$firstColumn] as $rowKey => $rowData) {
			if ((is_numeric($lookup_value) && is_numeric($rowData) && ($rowData > $lookup_value)) ||
				(!is_numeric($lookup_value) && !is_numeric($rowData) && (strtolower($rowData) > strtolower($lookup_value)))) {
                break;
            }
            $rowNumber = $rowKey;
            $rowValue = $rowData;
        }

        if ($rowNumber !== false) {
            if ((!$not_exact_match) && ($rowValue != $lookup_value)) {
                //  if an exact match is required, we have what we need to return an appropriate response
                return PHPExcel_Calculation_Functions::NA();
            } else {
                //  otherwise return the appropriate value
                $result = $lookup_array[$returnColumn][$rowNumber];
				return $result;
            }
        }

        return PHPExcel_Calculation_Functions::NA();
    }   //  function HLOOKUP()


	/**
	 * LOOKUP
	 * The LOOKUP function searches for value either from a one-row or one-column range or from an array.
	 * @param	lookup_value	The value that you want to match in lookup_array
	 * @param	lookup_vector	The range of cells being searched
	 * @param	result_vector	The column from which the matching value must be returned
	 * @return	mixed			The value of the found cell
	 */
	public static function LOOKUP($lookup_value, $lookup_vector, $result_vector=null) {
		$lookup_value	= PHPExcel_Calculation_Functions::flattenSingleValue($lookup_value);

		if (!is_array($lookup_vector)) {
			return PHPExcel_Calculation_Functions::NA();
		}
		$lookupRows = count($lookup_vector);
		$l = array_keys($lookup_vector);
		$l = array_shift($l);
		$lookupColumns = count($lookup_vector[$l]);
		if ((($lookupRows == 1) && ($lookupColumns > 1)) || (($lookupRows == 2) && ($lookupColumns != 2))) {
			$lookup_vector = self::TRANSPOSE($lookup_vector);
			$lookupRows = count($lookup_vector);
			$l = array_keys($lookup_vector);
			$lookupColumns = count($lookup_vector[array_shift($l)]);
		}

		if (is_null($result_vector)) {
			$result_vector = $lookup_vector;
		}
		$resultRows = count($result_vector);
		$l = array_keys($result_vector);
		$l = array_shift($l);
		$resultColumns = count($result_vector[$l]);
		if ((($resultRows == 1) && ($resultColumns > 1)) || (($resultRows == 2) && ($resultColumns != 2))) {
			$result_vector = self::TRANSPOSE($result_vector);
			$resultRows = count($result_vector);
			$r = array_keys($result_vector);
			$resultColumns = count($result_vector[array_shift($r)]);
		}

		if ($lookupRows == 2) {
			$result_vector = array_pop($lookup_vector);
			$lookup_vector = array_shift($lookup_vector);
		}
		if ($lookupColumns != 2) {
			foreach($lookup_vector as &$value) {
				if (is_array($value)) {
					$k = array_keys($value);
					$key1 = $key2 = array_shift($k);
					$key2++;
					$dataValue1 = $value[$key1];
				} else {
					$key1 = 0;
					$key2 = 1;
					$dataValue1 = $value;
				}
				$dataValue2 = array_shift($result_vector);
				if (is_array($dataValue2)) {
					$dataValue2 = array_shift($dataValue2);
				}
				$value = array($key1 => $dataValue1, $key2 => $dataValue2);
			}
			unset($value);
		}

		return self::VLOOKUP($lookup_value,$lookup_vector,2);
 	}	//	function LOOKUP()

}	//	class PHPExcel_Calculation_LookupRef
