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
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.4.5, 2007-08-23
 */


/**
 * PHPExcel_Style_Protection
 *
 * @category   PHPExcel
 * @package    PHPExcel_Style
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Style_Protection extends PHPExcel_Style_Supervisor implements PHPExcel_IComparable
{
	/** Protection styles */
	const PROTECTION_INHERIT		= 'inherit';
	const PROTECTION_PROTECTED		= 'protected';
	const PROTECTION_UNPROTECTED	= 'unprotected';

	/**
	 * Locked
	 *
	 * @var string
	 */
	protected $_locked;

	/**
	 * Hidden
	 *
	 * @var string
	 */
	protected $_hidden;

	/**
     * Create a new PHPExcel_Style_Protection
	 *
	 * @param	boolean	$isSupervisor	Flag indicating if this is a supervisor or not
	 *									Leave this value at default unless you understand exactly what
	 *										its ramifications are
	 * @param	boolean	$isConditional	Flag indicating if this is a conditional style or not
	 *									Leave this value at default unless you understand exactly what
	 *										its ramifications are
     */
    public function __construct($isSupervisor = FALSE, $isConditional = FALSE)
    {
    	// Supervisor?
		parent::__construct($isSupervisor);

    	// Initialise values
		if (!$isConditional) {
	    	$this->_locked			= self::PROTECTION_INHERIT;
	    	$this->_hidden			= self::PROTECTION_INHERIT;
		}
    }

	/**
	 * Get the shared style component for the currently active cell in currently active sheet.
	 * Only used for style supervisor
	 *
	 * @return PHPExcel_Style_Protection
	 */
	public function getSharedComponent()
	{
		return $this->_parent->getSharedComponent()->getProtection();
	}

	/**
	 * Build style array from subcomponents
	 *
	 * @param array $array
	 * @return array
	 */
	public function getStyleArray($array)
	{
		return array('protection' => $array);
	}

    /**
     * Apply styles from array
     *
     * <code>
     * $objPHPExcel->getActiveSheet()->getStyle('B2')->getLocked()->applyFromArray(
     *		array(
     *			'locked' => TRUE,
     *			'hidden' => FALSE
     *		)
     * );
     * </code>
     *
     * @param	array	$pStyles	Array containing style information
     * @throws	PHPExcel_Exception
     * @return PHPExcel_Style_Protection
     */
	public function applyFromArray($pStyles = NULL) {
		if (is_array($pStyles)) {
			if ($this->_isSupervisor) {
				$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($this->getStyleArray($pStyles));
			} else {
				if (isset($pStyles['locked'])) {
					$this->setLocked($pStyles['locked']);
				}
				if (isset($pStyles['hidden'])) {
					$this->setHidden($pStyles['hidden']);
				}
			}
		} else {
			throw new PHPExcel_Exception("Invalid style array passed.");
		}
		return $this;
	}

    /**
     * Get locked
     *
     * @return string
     */
    public function getLocked() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getLocked();
		}
    	return $this->_locked;
    }

    /**
     * Set locked
     *
     * @param string $pValue
     * @return PHPExcel_Style_Protection
     */
    public function setLocked($pValue = self::PROTECTION_INHERIT) {
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('locked' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_locked = $pValue;
		}
		return $this;
    }

    /**
     * Get hidden
     *
     * @return string
     */
    public function getHidden() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHidden();
		}
    	return $this->_hidden;
    }

    /**
     * Set hidden
     *
     * @param string $pValue
     * @return PHPExcel_Style_Protection
     */
    public function setHidden($pValue = self::PROTECTION_INHERIT) {
		if ($this->_isSupervisor) {
			$styleArray = $this->getStyleArray(array('hidden' => $pValue));
			$this->getActiveSheet()->getStyle($this->getSelectedCells())->applyFromArray($styleArray);
		} else {
			$this->_hidden = $pValue;
		}
		return $this;
    }

	/**
	 * Get hash code
	 *
	 * @return string	Hash code
	 */
	public function getHashCode() {
		if ($this->_isSupervisor) {
			return $this->getSharedComponent()->getHashCode();
		}
    	return md5(
    		  $this->_locked
    		. $this->_hidden
    		. __CLASS__
    	);
    }

}
