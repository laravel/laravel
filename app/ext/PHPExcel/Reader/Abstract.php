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
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Reader_Abstract
 *
 * @category	PHPExcel
 * @package	PHPExcel_Reader
 * @copyright	Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
abstract class PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
	/**
	 * Read data only?
	 * Identifies whether the Reader should only read data values for cells, and ignore any formatting information;
	 *		or whether it should read both data and formatting
	 *
	 * @var	boolean
	 */
	protected $_readDataOnly = FALSE;

	/**
	 * Read charts that are defined in the workbook?
	 * Identifies whether the Reader should read the definitions for any charts that exist in the workbook;
	 *
	 * @var	boolean
	 */
	protected $_includeCharts = FALSE;

	/**
	 * Restrict which sheets should be loaded?
	 * This property holds an array of worksheet names to be loaded. If null, then all worksheets will be loaded.
	 *
	 * @var array of string
	 */
	protected $_loadSheetsOnly = NULL;

	/**
	 * PHPExcel_Reader_IReadFilter instance
	 *
	 * @var PHPExcel_Reader_IReadFilter
	 */
	protected $_readFilter = NULL;

	protected $_fileHandle = NULL;


	/**
	 * Read data only?
	 *		If this is true, then the Reader will only read data values for cells, it will not read any formatting information.
	 *		If false (the default) it will read data and formatting.
	 *
	 * @return	boolean
	 */
	public function getReadDataOnly() {
		return $this->_readDataOnly;
	}

	/**
	 * Set read data only
	 *		Set to true, to advise the Reader only to read data values for cells, and to ignore any formatting information.
	 *		Set to false (the default) to advise the Reader to read both data and formatting for cells.
	 *
	 * @param	boolean	$pValue
	 *
	 * @return	PHPExcel_Reader_IReader
	 */
	public function setReadDataOnly($pValue = FALSE) {
		$this->_readDataOnly = $pValue;
		return $this;
	}

	/**
	 * Read charts in workbook?
	 *		If this is true, then the Reader will include any charts that exist in the workbook.
	 *      Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
	 *		If false (the default) it will ignore any charts defined in the workbook file.
	 *
	 * @return	boolean
	 */
	public function getIncludeCharts() {
		return $this->_includeCharts;
	}

	/**
	 * Set read charts in workbook
	 *		Set to true, to advise the Reader to include any charts that exist in the workbook.
	 *      Note that a ReadDataOnly value of false overrides, and charts won't be read regardless of the IncludeCharts value.
	 *		Set to false (the default) to discard charts.
	 *
	 * @param	boolean	$pValue
	 *
	 * @return	PHPExcel_Reader_IReader
	 */
	public function setIncludeCharts($pValue = FALSE) {
		$this->_includeCharts = (boolean) $pValue;
		return $this;
	}

	/**
	 * Get which sheets to load
	 * Returns either an array of worksheet names (the list of worksheets that should be loaded), or a null
	 *		indicating that all worksheets in the workbook should be loaded.
	 *
	 * @return mixed
	 */
	public function getLoadSheetsOnly()
	{
		return $this->_loadSheetsOnly;
	}

	/**
	 * Set which sheets to load
	 *
	 * @param mixed $value
	 *		This should be either an array of worksheet names to be loaded, or a string containing a single worksheet name.
	 *		If NULL, then it tells the Reader to read all worksheets in the workbook
	 *
	 * @return PHPExcel_Reader_IReader
	 */
	public function setLoadSheetsOnly($value = NULL)
	{
		$this->_loadSheetsOnly = is_array($value) ?
			$value : array($value);
		return $this;
	}

	/**
	 * Set all sheets to load
	 *		Tells the Reader to load all worksheets from the workbook.
	 *
	 * @return PHPExcel_Reader_IReader
	 */
	public function setLoadAllSheets()
	{
		$this->_loadSheetsOnly = NULL;
		return $this;
	}

	/**
	 * Read filter
	 *
	 * @return PHPExcel_Reader_IReadFilter
	 */
	public function getReadFilter() {
		return $this->_readFilter;
	}

	/**
	 * Set read filter
	 *
	 * @param PHPExcel_Reader_IReadFilter $pValue
	 * @return PHPExcel_Reader_IReader
	 */
	public function setReadFilter(PHPExcel_Reader_IReadFilter $pValue) {
		$this->_readFilter = $pValue;
		return $this;
	}

	/**
	 * Open file for reading
	 *
	 * @param string $pFilename
	 * @throws	PHPExcel_Reader_Exception
	 * @return resource
	 */
	protected function _openFile($pFilename)
	{
		// Check if file exists
		if (!file_exists($pFilename) || !is_readable($pFilename)) {
			throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
		}

		// Open file
		$this->_fileHandle = fopen($pFilename, 'r');
		if ($this->_fileHandle === FALSE) {
			throw new PHPExcel_Reader_Exception("Could not open file " . $pFilename . " for reading.");
		}
	}

	/**
	 * Can the current PHPExcel_Reader_IReader read the file?
	 *
	 * @param 	string 		$pFilename
	 * @return boolean
	 * @throws PHPExcel_Reader_Exception
	 */
	public function canRead($pFilename)
	{
		// Check if file exists
		try {
			$this->_openFile($pFilename);
		} catch (Exception $e) {
			return FALSE;
		}

		$readable = $this->_isValidFormat();
		fclose ($this->_fileHandle);
		return $readable;
	}

}
