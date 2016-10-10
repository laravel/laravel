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
 * @package	PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license	http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version	1.8.0, 2014-03-02
 */

if (!defined('DATE_W3C')) {
  define('DATE_W3C', 'Y-m-d\TH:i:sP');
}

if (!defined('DEBUGMODE_ENABLED')) {
  define('DEBUGMODE_ENABLED', false);
}


/**
 * PHPExcel_Shared_XMLWriter
 *
 * @category   PHPExcel
 * @package	PHPExcel_Shared
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_XMLWriter extends XMLWriter {
	/** Temporary storage method */
	const STORAGE_MEMORY	= 1;
	const STORAGE_DISK		= 2;

	/**
	 * Temporary filename
	 *
	 * @var string
	 */
	private $_tempFileName = '';

	/**
	 * Create a new PHPExcel_Shared_XMLWriter instance
	 *
	 * @param int		$pTemporaryStorage			Temporary storage location
	 * @param string	$pTemporaryStorageFolder	Temporary storage folder
	 */
	public function __construct($pTemporaryStorage = self::STORAGE_MEMORY, $pTemporaryStorageFolder = NULL) {
		// Open temporary storage
		if ($pTemporaryStorage == self::STORAGE_MEMORY) {
			$this->openMemory();
		} else {
			// Create temporary filename
			if ($pTemporaryStorageFolder === NULL)
				$pTemporaryStorageFolder = PHPExcel_Shared_File::sys_get_temp_dir();
			$this->_tempFileName = @tempnam($pTemporaryStorageFolder, 'xml');

			// Open storage
			if ($this->openUri($this->_tempFileName) === false) {
				// Fallback to memory...
				$this->openMemory();
			}
		}

		// Set default values
		if (DEBUGMODE_ENABLED) {
			$this->setIndent(true);
		}
	}

	/**
	 * Destructor
	 */
	public function __destruct() {
		// Unlink temporary files
		if ($this->_tempFileName != '') {
			@unlink($this->_tempFileName);
		}
	}

	/**
	 * Get written data
	 *
	 * @return $data
	 */
	public function getData() {
		if ($this->_tempFileName == '') {
			return $this->outputMemory(true);
		} else {
			$this->flush();
			return file_get_contents($this->_tempFileName);
		}
	}

	/**
	 * Fallback method for writeRaw, introduced in PHP 5.2
	 *
	 * @param string $text
	 * @return string
	 */
	public function writeRawData($text)
	{
		if (is_array($text)) {
			$text = implode("\n",$text);
		}

		if (method_exists($this, 'writeRaw')) {
			return $this->writeRaw(htmlspecialchars($text));
		}

		return $this->text($text);
	}
}
