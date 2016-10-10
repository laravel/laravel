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
 * @package    PHPExcel_Worksheet_Drawing
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Worksheet_Drawing
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet_Drawing
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet_Drawing extends PHPExcel_Worksheet_BaseDrawing implements PHPExcel_IComparable
{
	/**
	 * Path
	 *
	 * @var string
	 */
	private $_path;

    /**
     * Create a new PHPExcel_Worksheet_Drawing
     */
    public function __construct()
    {
    	// Initialise values
    	$this->_path				= '';

    	// Initialize parent
    	parent::__construct();
    }

    /**
     * Get Filename
     *
     * @return string
     */
    public function getFilename() {
    	return basename($this->_path);
    }

    /**
     * Get indexed filename (using image index)
     *
     * @return string
     */
    public function getIndexedFilename() {
    	$fileName = $this->getFilename();
    	$fileName = str_replace(' ', '_', $fileName);
    	return str_replace('.' . $this->getExtension(), '', $fileName) . $this->getImageIndex() . '.' . $this->getExtension();
    }

    /**
     * Get Extension
     *
     * @return string
     */
    public function getExtension() {
    	$exploded = explode(".", basename($this->_path));
    	return $exploded[count($exploded) - 1];
    }

    /**
     * Get Path
     *
     * @return string
     */
    public function getPath() {
    	return $this->_path;
    }

    /**
     * Set Path
     *
     * @param 	string 		$pValue			File path
     * @param 	boolean		$pVerifyFile	Verify file
     * @throws 	PHPExcel_Exception
     * @return PHPExcel_Worksheet_Drawing
     */
    public function setPath($pValue = '', $pVerifyFile = true) {
    	if ($pVerifyFile) {
	    	if (file_exists($pValue)) {
	    		$this->_path = $pValue;

	    		if ($this->_width == 0 && $this->_height == 0) {
	    			// Get width/height
	    			list($this->_width, $this->_height) = getimagesize($pValue);
	    		}
	    	} else {
	    		throw new PHPExcel_Exception("File $pValue not found!");
	    	}
    	} else {
    		$this->_path = $pValue;
    	}
    	return $this;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
    	return md5(
    		  $this->_path
    		. parent::getHashCode()
    		. __CLASS__
    	);
    }

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if (is_object($value)) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}
