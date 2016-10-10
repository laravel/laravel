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
 * PHPExcel_Calculation_Database
 *
 * @category	PHPExcel
 * @package		PHPExcel_Calculation
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Calculation_Database {


	/**
	 * __fieldExtract
	 *
	 * Extracts the column ID to use for the data field.
	 *
	 * @access	private
	 * @param	mixed[]		$database		The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	mixed		$field			Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @return	string|NULL
	 *
	 */
	private static function __fieldExtract($database,$field) {
		$field = strtoupper(PHPExcel_Calculation_Functions::flattenSingleValue($field));
		$fieldNames = array_map('strtoupper',array_shift($database));

		if (is_numeric($field)) {
			$keys = array_keys($fieldNames);
			return $keys[$field-1];
		}
		$key = array_search($field,$fieldNames);
		return ($key) ? $key : NULL;
	}

	/**
	 * __filter
	 *
	 * Parses the selection criteria, extracts the database rows that match those criteria, and
	 * returns that subset of rows.
	 *
	 * @access	private
	 * @param	mixed[]		$database		The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	mixed[]		$criteria		The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	array of mixed
	 *
	 */
	private static function __filter($database,$criteria) {
		$fieldNames = array_shift($database);
		$criteriaNames = array_shift($criteria);

		//	Convert the criteria into a set of AND/OR conditions with [:placeholders]
		$testConditions = $testValues = array();
		$testConditionsCount = 0;
		foreach($criteriaNames as $key => $criteriaName) {
			$testCondition = array();
			$testConditionCount = 0;
			foreach($criteria as $row => $criterion) {
				if ($criterion[$key] > '') {
					$testCondition[] = '[:'.$criteriaName.']'.PHPExcel_Calculation_Functions::_ifCondition($criterion[$key]);
					$testConditionCount++;
				}
			}
			if ($testConditionCount > 1) {
				$testConditions[] = 'OR('.implode(',',$testCondition).')';
				$testConditionsCount++;
			} elseif($testConditionCount == 1) {
				$testConditions[] = $testCondition[0];
				$testConditionsCount++;
			}
		}

		if ($testConditionsCount > 1) {
			$testConditionSet = 'AND('.implode(',',$testConditions).')';
		} elseif($testConditionsCount == 1) {
			$testConditionSet = $testConditions[0];
		}

		//	Loop through each row of the database
		foreach($database as $dataRow => $dataValues) {
			//	Substitute actual values from the database row for our [:placeholders]
			$testConditionList = $testConditionSet;
			foreach($criteriaNames as $key => $criteriaName) {
				$k = array_search($criteriaName,$fieldNames);
				if (isset($dataValues[$k])) {
					$dataValue = $dataValues[$k];
					$dataValue = (is_string($dataValue)) ? PHPExcel_Calculation::_wrapResult(strtoupper($dataValue)) : $dataValue;
					$testConditionList = str_replace('[:'.$criteriaName.']',$dataValue,$testConditionList);
				}
			}
			//	evaluate the criteria against the row data
			$result = PHPExcel_Calculation::getInstance()->_calculateFormulaValue('='.$testConditionList);
			//	If the row failed to meet the criteria, remove it from the database
			if (!$result) {
				unset($database[$dataRow]);
			}
		}

		return $database;
	}


	/**
	 * DAVERAGE
	 *
	 * Averages the values in a column of a list or database that match conditions you specify.
	 *
	 * Excel Function:
	 *		DAVERAGE(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	float
	 *
	 */
	public static function DAVERAGE($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}
		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::AVERAGE($colData);
	}	//	function DAVERAGE()


	/**
	 * DCOUNT
	 *
	 * Counts the cells that contain numbers in a column of a list or database that match conditions
	 * that you specify.
	 *
	 * Excel Function:
	 *		DCOUNT(database,[field],criteria)
	 *
	 * Excel Function:
	 *		DAVERAGE(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	integer
	 *
	 * @TODO	The field argument is optional. If field is omitted, DCOUNT counts all records in the
	 *			database that match the criteria.
	 *
	 */
	public static function DCOUNT($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::COUNT($colData);
	}	//	function DCOUNT()


	/**
	 * DCOUNTA
	 *
	 * Counts the nonblank cells in a column of a list or database that match conditions that you specify.
	 *
	 * Excel Function:
	 *		DCOUNTA(database,[field],criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	integer
	 *
	 * @TODO	The field argument is optional. If field is omitted, DCOUNTA counts all records in the
	 *			database that match the criteria.
	 *
	 */
	public static function DCOUNTA($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::COUNTA($colData);
	}	//	function DCOUNTA()


	/**
	 * DGET
	 *
	 * Extracts a single value from a column of a list or database that matches conditions that you
	 * specify.
	 *
	 * Excel Function:
	 *		DGET(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	mixed
	 *
	 */
	public static function DGET($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		if (count($colData) > 1) {
			return PHPExcel_Calculation_Functions::NaN();
		}

		return $colData[0];
	}	//	function DGET()


	/**
	 * DMAX
	 *
	 * Returns the largest number in a column of a list or database that matches conditions you that
	 * specify.
	 *
	 * Excel Function:
	 *		DMAX(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	float
	 *
	 */
	public static function DMAX($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::MAX($colData);
	}	//	function DMAX()


	/**
	 * DMIN
	 *
	 * Returns the smallest number in a column of a list or database that matches conditions you that
	 * specify.
	 *
	 * Excel Function:
	 *		DMIN(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	float
	 *
	 */
	public static function DMIN($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::MIN($colData);
	}	//	function DMIN()


	/**
	 * DPRODUCT
	 *
	 * Multiplies the values in a column of a list or database that match conditions that you specify.
	 *
	 * Excel Function:
	 *		DPRODUCT(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	float
	 *
	 */
	public static function DPRODUCT($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_MathTrig::PRODUCT($colData);
	}	//	function DPRODUCT()


	/**
	 * DSTDEV
	 *
	 * Estimates the standard deviation of a population based on a sample by using the numbers in a
	 * column of a list or database that match conditions that you specify.
	 *
	 * Excel Function:
	 *		DSTDEV(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	float
	 *
	 */
	public static function DSTDEV($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::STDEV($colData);
	}	//	function DSTDEV()


	/**
	 * DSTDEVP
	 *
	 * Calculates the standard deviation of a population based on the entire population by using the
	 * numbers in a column of a list or database that match conditions that you specify.
	 *
	 * Excel Function:
	 *		DSTDEVP(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	float
	 *
	 */
	public static function DSTDEVP($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::STDEVP($colData);
	}	//	function DSTDEVP()


	/**
	 * DSUM
	 *
	 * Adds the numbers in a column of a list or database that match conditions that you specify.
	 *
	 * Excel Function:
	 *		DSUM(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	float
	 *
	 */
	public static function DSUM($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_MathTrig::SUM($colData);
	}	//	function DSUM()


	/**
	 * DVAR
	 *
	 * Estimates the variance of a population based on a sample by using the numbers in a column
	 * of a list or database that match conditions that you specify.
	 *
	 * Excel Function:
	 *		DVAR(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	float
	 *
	 */
	public static function DVAR($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::VARFunc($colData);
	}	//	function DVAR()


	/**
	 * DVARP
	 *
	 * Calculates the variance of a population based on the entire population by using the numbers
	 * in a column of a list or database that match conditions that you specify.
	 *
	 * Excel Function:
	 *		DVARP(database,field,criteria)
	 *
	 * @access	public
	 * @category Database Functions
	 * @param	mixed[]			$database	The range of cells that makes up the list or database.
	 *										A database is a list of related data in which rows of related
	 *										information are records, and columns of data are fields. The
	 *										first row of the list contains labels for each column.
	 * @param	string|integer	$field		Indicates which column is used in the function. Enter the
	 *										column label enclosed between double quotation marks, such as
	 *										"Age" or "Yield," or a number (without quotation marks) that
	 *										represents the position of the column within the list: 1 for
	 *										the first column, 2 for the second column, and so on.
	 * @param	mixed[]			$criteria	The range of cells that contains the conditions you specify.
	 *										You can use any range for the criteria argument, as long as it
	 *										includes at least one column label and at least one cell below
	 *										the column label in which you specify a condition for the
	 *										column.
	 * @return	float
	 *
	 */
	public static function DVARP($database,$field,$criteria) {
		$field = self::__fieldExtract($database,$field);
		if (is_null($field)) {
			return NULL;
		}

		//	reduce the database to a set of rows that match all the criteria
		$database = self::__filter($database,$criteria);
		//	extract an array of values for the requested column
		$colData = array();
		foreach($database as $row) {
			$colData[] = $row[$field];
		}

		// Return
		return PHPExcel_Calculation_Statistical::VARP($colData);
	}	//	function DVARP()


}	//	class PHPExcel_Calculation_Database
