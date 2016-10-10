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
 * @package    PHPExcel_Reader_Excel2007
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Reader_Excel2007_Theme
 *
 * @category   PHPExcel
 * @package    PHPExcel_Reader_Excel2007
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Reader_Excel2007_Theme
{
	/**
	 * Theme Name
	 *
	 * @var string
	 */
	private $_themeName;

	/**
	 * Colour Scheme Name
	 *
	 * @var string
	 */
	private $_colourSchemeName;

	/**
	 * Colour Map indexed by position
	 *
	 * @var array of string
	 */
	private $_colourMapValues;


	/**
	 * Colour Map
	 *
	 * @var array of string
	 */
	private $_colourMap;


    /**
     * Create a new PHPExcel_Theme
	 *
     */
    public function __construct($themeName,$colourSchemeName,$colourMap)
    {
		// Initialise values
    	$this->_themeName			= $themeName;
		$this->_colourSchemeName	= $colourSchemeName;
		$this->_colourMap			= $colourMap;
    }

	/**
	 * Get Theme Name
	 *
	 * @return string
	 */
	public function getThemeName()
	{
		return $this->_themeName;
	}

    /**
     * Get colour Scheme Name
     *
     * @return string
     */
    public function getColourSchemeName() {
		return $this->_colourSchemeName;
    }

    /**
     * Get colour Map Value by Position
     *
     * @return string
     */
    public function getColourByIndex($index=0) {
    	if (isset($this->_colourMap[$index])) {
			return $this->_colourMap[$index];
		}
		return null;
    }

	/**
	 * Implement PHP __clone to create a deep clone, not just a shallow copy.
	 */
	public function __clone() {
		$vars = get_object_vars($this);
		foreach ($vars as $key => $value) {
			if ((is_object($value)) && ($key != '_parent')) {
				$this->$key = clone $value;
			} else {
				$this->$key = $value;
			}
		}
	}
}
