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
 * PHPExcel_Shared_Escher_DggContainer_BstoreContainer
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_Escher
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_Escher_DggContainer_BstoreContainer
{
	/**
	 * BLIP Store Entries. Each of them holds one BLIP (Big Large Image or Picture)
	 *
	 * @var array
	 */
	private $_BSECollection = array();

	/**
	 * Add a BLIP Store Entry
	 *
	 * @param PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE $BSE
	 */
	public function addBSE($BSE)
	{
		$this->_BSECollection[] = $BSE;
		$BSE->setParent($this);
	}

	/**
	 * Get the collection of BLIP Store Entries
	 *
	 * @return PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE[]
	 */
	public function getBSECollection()
	{
		return $this->_BSECollection;
	}

}
