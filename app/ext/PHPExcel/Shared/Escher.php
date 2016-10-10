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
 * PHPExcel_Shared_Escher
 *
 * @category   PHPExcel
 * @package    PHPExcel_Shared_Escher
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Shared_Escher
{
	/**
	 * Drawing Group Container
	 *
	 * @var PHPExcel_Shared_Escher_DggContainer
	 */
	private $_dggContainer;

	/**
	 * Drawing Container
	 *
	 * @var PHPExcel_Shared_Escher_DgContainer
	 */
	private $_dgContainer;

	/**
	 * Get Drawing Group Container
	 *
	 * @return PHPExcel_Shared_Escher_DgContainer
	 */
	public function getDggContainer()
	{
		return $this->_dggContainer;
	}

	/**
	 * Set Drawing Group Container
	 *
	 * @param PHPExcel_Shared_Escher_DggContainer $dggContainer
	 */
	public function setDggContainer($dggContainer)
	{
		return $this->_dggContainer = $dggContainer;
	}

	/**
	 * Get Drawing Container
	 *
	 * @return PHPExcel_Shared_Escher_DgContainer
	 */
	public function getDgContainer()
	{
		return $this->_dgContainer;
	}

	/**
	 * Set Drawing Container
	 *
	 * @param PHPExcel_Shared_Escher_DgContainer $dgContainer
	 */
	public function setDgContainer($dgContainer)
	{
		return $this->_dgContainer = $dgContainer;
	}

}
