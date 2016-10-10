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
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_WorksheetIterator
 *
 * Used to iterate worksheets in PHPExcel
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_WorksheetIterator implements Iterator
{
    /**
     * Spreadsheet to iterate
     *
     * @var PHPExcel
     */
    private $_subject;

    /**
     * Current iterator position
     *
     * @var int
     */
    private $_position = 0;

    /**
     * Create a new worksheet iterator
     *
     * @param PHPExcel         $subject
     */
    public function __construct(PHPExcel $subject = null)
    {
        // Set subject
        $this->_subject = $subject;
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        unset($this->_subject);
    }

    /**
     * Rewind iterator
     */
    public function rewind()
    {
        $this->_position = 0;
    }

    /**
     * Current PHPExcel_Worksheet
     *
     * @return PHPExcel_Worksheet
     */
    public function current()
    {
        return $this->_subject->getSheet($this->_position);
    }

    /**
     * Current key
     *
     * @return int
     */
    public function key()
    {
        return $this->_position;
    }

    /**
     * Next value
     */
    public function next()
    {
        ++$this->_position;
    }

    /**
     * More PHPExcel_Worksheet instances available?
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_position < $this->_subject->getSheetCount();
    }
}
