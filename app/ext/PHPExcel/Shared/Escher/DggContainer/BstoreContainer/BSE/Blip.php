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
 * @package    PHPExcel_Shared_Escher
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */

/**
 * PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_Escher
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip
{
	/**
	 * The parent BSE
	 *
	 * @var PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE
	 */
	private $_parent;

	/**
	 * Raw image data
	 *
	 * @var string
	 */
	private $_data;

	/**
	 * Get the raw image data
	 *
	 * @return string
	 */
	public function getData()
	{
		return $this->_data;
	}

	/**
	 * Set the raw image data
	 *
	 * @param string
	 */
	public function setData($data)
	{
		$this->_data = $data;
	}

	/**
	 * Set parent BSE
	 *
	 * @param PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE $parent
	 */
	public function setParent($parent)
	{
		$this->_parent = $parent;
	}

	/**
	 * Get parent BSE
	 *
	 * @return PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE $parent
	 */
	public function getParent()
	{
		return $this->_parent;
	}

}
