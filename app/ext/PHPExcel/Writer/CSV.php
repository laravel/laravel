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
 * @package	PHPExcel_Writer_CSV
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Writer_CSV
 *
 * @category   PHPExcel
 * @package	PHPExcel_Writer_CSV
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_CSV extends PHPExcel_Writer_Abstract implements PHPExcel_Writer_IWriter {
	/**
	 * PHPExcel object
	 *
	 * @var PHPExcel
	 */
	private $_phpExcel;

	/**
	 * Delimiter
	 *
	 * @var string
	 */
	private $_delimiter	= ',';

	/**
	 * Enclosure
	 *
	 * @var string
	 */
	private $_enclosure	= '"';

	/**
	 * Line ending
	 *
	 * @var string
	 */
	private $_lineEnding	= PHP_EOL;

	/**
	 * Sheet index to write
	 *
	 * @var int
	 */
	private $_sheetIndex	= 0;

	/**
	 * Whether to write a BOM (for UTF8).
	 *
	 * @var boolean
	 */
	private $_useBOM = false;

	/**
	 * Whether to write a fully Excel compatible CSV file.
	 *
	 * @var boolean
	 */
	private $_excelCompatibility = false;

	/**
	 * Create a new PHPExcel_Writer_CSV
	 *
	 * @param	PHPExcel	$phpExcel	PHPExcel object
	 */
	public function __construct(PHPExcel $phpExcel) {
		$this->_phpExcel	= $phpExcel;
	}

	/**
	 * Save PHPExcel to file
	 *
	 * @param	string		$pFilename
	 * @throws	PHPExcel_Writer_Exception
	 */
	public function save($pFilename = null) {
		// Fetch sheet
		$sheet = $this->_phpExcel->getSheet($this->_sheetIndex);

		$saveDebugLog = PHPExcel_Calculation::getInstance($this->_phpExcel)->getDebugLog()->getWriteDebugLog();
		PHPExcel_Calculation::getInstance($this->_phpExcel)->getDebugLog()->setWriteDebugLog(FALSE);
		$saveArrayReturnType = PHPExcel_Calculation::getArrayReturnType();
		PHPExcel_Calculation::setArrayReturnType(PHPExcel_Calculation::RETURN_ARRAY_AS_VALUE);

		// Open file
		$fileHandle = fopen($pFilename, 'wb+');
		if ($fileHandle === false) {
			throw new PHPExcel_Writer_Exception("Could not open file $pFilename for writing.");
		}

		if ($this->_excelCompatibility) {
			fwrite($fileHandle, "\xEF\xBB\xBF");	//	Enforce UTF-8 BOM Header
			$this->setEnclosure('"');				//	Set enclosure to "
			$this->setDelimiter(";");			    //	Set delimiter to a semi-colon
            $this->setLineEnding("\r\n");
			fwrite($fileHandle, 'sep=' . $this->getDelimiter() . $this->_lineEnding);
		} elseif ($this->_useBOM) {
			// Write the UTF-8 BOM code if required
			fwrite($fileHandle, "\xEF\xBB\xBF");
		}

		//	Identify the range that we need to extract from the worksheet
		$maxCol = $sheet->getHighestDataColumn();
		$maxRow = $sheet->getHighestDataRow();

		// Write rows to file
		for($row = 1; $row <= $maxRow; ++$row) {
			// Convert the row to an array...
			$cellsArray = $sheet->rangeToArray('A'.$row.':'.$maxCol.$row,'', $this->_preCalculateFormulas);
			// ... and write to the file
			$this->_writeLine($fileHandle, $cellsArray[0]);
		}

		// Close file
		fclose($fileHandle);

		PHPExcel_Calculation::setArrayReturnType($saveArrayReturnType);
		PHPExcel_Calculation::getInstance($this->_phpExcel)->getDebugLog()->setWriteDebugLog($saveDebugLog);
	}

	/**
	 * Get delimiter
	 *
	 * @return string
	 */
	public function getDelimiter() {
		return $this->_delimiter;
	}

	/**
	 * Set delimiter
	 *
	 * @param	string	$pValue		Delimiter, defaults to ,
	 * @return PHPExcel_Writer_CSV
	 */
	public function setDelimiter($pValue = ',') {
		$this->_delimiter = $pValue;
		return $this;
	}

	/**
	 * Get enclosure
	 *
	 * @return string
	 */
	public function getEnclosure() {
		return $this->_enclosure;
	}

	/**
	 * Set enclosure
	 *
	 * @param	string	$pValue		Enclosure, defaults to "
	 * @return PHPExcel_Writer_CSV
	 */
	public function setEnclosure($pValue = '"') {
		if ($pValue == '') {
			$pValue = null;
		}
		$this->_enclosure = $pValue;
		return $this;
	}

	/**
	 * Get line ending
	 *
	 * @return string
	 */
	public function getLineEnding() {
		return $this->_lineEnding;
	}

	/**
	 * Set line ending
	 *
	 * @param	string	$pValue		Line ending, defaults to OS line ending (PHP_EOL)
	 * @return PHPExcel_Writer_CSV
	 */
	public function setLineEnding($pValue = PHP_EOL) {
		$this->_lineEnding = $pValue;
		return $this;
	}

	/**
	 * Get whether BOM should be used
	 *
	 * @return boolean
	 */
	public function getUseBOM() {
		return $this->_useBOM;
	}

	/**
	 * Set whether BOM should be used
	 *
	 * @param	boolean	$pValue		Use UTF-8 byte-order mark? Defaults to false
	 * @return PHPExcel_Writer_CSV
	 */
	public function setUseBOM($pValue = false) {
		$this->_useBOM = $pValue;
		return $this;
	}

	/**
	 * Get whether the file should be saved with full Excel Compatibility
	 *
	 * @return boolean
	 */
	public function getExcelCompatibility() {
		return $this->_excelCompatibility;
	}

	/**
	 * Set whether the file should be saved with full Excel Compatibility
	 *
	 * @param	boolean	$pValue		Set the file to be written as a fully Excel compatible csv file
	 *								Note that this overrides other settings such as useBOM, enclosure and delimiter
	 * @return PHPExcel_Writer_CSV
	 */
	public function setExcelCompatibility($pValue = false) {
		$this->_excelCompatibility = $pValue;
		return $this;
	}

	/**
	 * Get sheet index
	 *
	 * @return int
	 */
	public function getSheetIndex() {
		return $this->_sheetIndex;
	}

	/**
	 * Set sheet index
	 *
	 * @param	int		$pValue		Sheet index
	 * @return PHPExcel_Writer_CSV
	 */
	public function setSheetIndex($pValue = 0) {
		$this->_sheetIndex = $pValue;
		return $this;
	}

	/**
	 * Write line to CSV file
	 *
	 * @param	mixed	$pFileHandle	PHP filehandle
	 * @param	array	$pValues		Array containing values in a row
	 * @throws	PHPExcel_Writer_Exception
	 */
	private function _writeLine($pFileHandle = null, $pValues = null) {
		if (is_array($pValues)) {
			// No leading delimiter
			$writeDelimiter = false;

			// Build the line
			$line = '';

			foreach ($pValues as $element) {
				// Escape enclosures
				$element = str_replace($this->_enclosure, $this->_enclosure . $this->_enclosure, $element);

				// Add delimiter
				if ($writeDelimiter) {
					$line .= $this->_delimiter;
				} else {
					$writeDelimiter = true;
				}

				// Add enclosed string
				$line .= $this->_enclosure . $element . $this->_enclosure;
			}

			// Add line ending
			$line .= $this->_lineEnding;

			// Write to file
            fwrite($pFileHandle, $line);
		} else {
			throw new PHPExcel_Writer_Exception("Invalid data row passed to CSV writer.");
		}
	}

}
