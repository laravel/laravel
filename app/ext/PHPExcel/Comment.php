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
 * PHPExcel_Comment
 *
 * @category   PHPExcel
 * @package    PHPExcel
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Comment implements PHPExcel_IComparable
{
    /**
     * Author
     *
     * @var string
     */
    private $_author;

    /**
     * Rich text comment
     *
     * @var PHPExcel_RichText
     */
    private $_text;

    /**
     * Comment width (CSS style, i.e. XXpx or YYpt)
     *
     * @var string
     */
    private $_width = '96pt';

    /**
     * Left margin (CSS style, i.e. XXpx or YYpt)
     *
     * @var string
     */
    private $_marginLeft = '59.25pt';

    /**
     * Top margin (CSS style, i.e. XXpx or YYpt)
     *
     * @var string
     */
    private $_marginTop = '1.5pt';

    /**
     * Visible
     *
     * @var boolean
     */
    private $_visible = false;

    /**
     * Comment height (CSS style, i.e. XXpx or YYpt)
     *
     * @var string
     */
    private $_height = '55.5pt';

    /**
     * Comment fill color
     *
     * @var PHPExcel_Style_Color
     */
    private $_fillColor;

    /**
     * Alignment
     *
     * @var string
     */
    private $_alignment;

    /**
     * Create a new PHPExcel_Comment
     *
     * @throws PHPExcel_Exception
     */
    public function __construct()
    {
        // Initialise variables
        $this->_author		= 'Author';
        $this->_text		= new PHPExcel_RichText();
        $this->_fillColor	= new PHPExcel_Style_Color('FFFFFFE1');
        $this->_alignment	= PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
    }

    /**
     * Get Author
     *
     * @return string
     */
    public function getAuthor() {
        return $this->_author;
    }

    /**
     * Set Author
     *
     * @param string $pValue
     * @return PHPExcel_Comment
     */
    public function setAuthor($pValue = '') {
        $this->_author = $pValue;
        return $this;
    }

    /**
     * Get Rich text comment
     *
     * @return PHPExcel_RichText
     */
    public function getText() {
        return $this->_text;
    }

    /**
     * Set Rich text comment
     *
     * @param PHPExcel_RichText $pValue
     * @return PHPExcel_Comment
     */
    public function setText(PHPExcel_RichText $pValue) {
        $this->_text = $pValue;
        return $this;
    }

    /**
     * Get comment width (CSS style, i.e. XXpx or YYpt)
     *
     * @return string
     */
    public function getWidth() {
        return $this->_width;
    }

    /**
     * Set comment width (CSS style, i.e. XXpx or YYpt)
     *
     * @param string $value
     * @return PHPExcel_Comment
     */
    public function setWidth($value = '96pt') {
        $this->_width = $value;
        return $this;
    }

    /**
     * Get comment height (CSS style, i.e. XXpx or YYpt)
     *
     * @return string
     */
    public function getHeight() {
        return $this->_height;
    }

    /**
     * Set comment height (CSS style, i.e. XXpx or YYpt)
     *
     * @param string $value
     * @return PHPExcel_Comment
     */
    public function setHeight($value = '55.5pt') {
        $this->_height = $value;
        return $this;
    }

    /**
     * Get left margin (CSS style, i.e. XXpx or YYpt)
     *
     * @return string
     */
    public function getMarginLeft() {
        return $this->_marginLeft;
    }

    /**
     * Set left margin (CSS style, i.e. XXpx or YYpt)
     *
     * @param string $value
     * @return PHPExcel_Comment
     */
    public function setMarginLeft($value = '59.25pt') {
        $this->_marginLeft = $value;
        return $this;
    }

    /**
     * Get top margin (CSS style, i.e. XXpx or YYpt)
     *
     * @return string
     */
    public function getMarginTop() {
        return $this->_marginTop;
    }

    /**
     * Set top margin (CSS style, i.e. XXpx or YYpt)
     *
     * @param string $value
     * @return PHPExcel_Comment
     */
    public function setMarginTop($value = '1.5pt') {
        $this->_marginTop = $value;
        return $this;
    }

    /**
     * Is the comment visible by default?
     *
     * @return boolean
     */
    public function getVisible() {
        return $this->_visible;
    }

    /**
     * Set comment default visibility
     *
     * @param boolean $value
     * @return PHPExcel_Comment
     */
    public function setVisible($value = false) {
        $this->_visible = $value;
        return $this;
    }

    /**
     * Get fill color
     *
     * @return PHPExcel_Style_Color
     */
    public function getFillColor() {
        return $this->_fillColor;
    }

    /**
     * Set Alignment
     *
     * @param string $pValue
     * @return PHPExcel_Comment
     */
    public function setAlignment($pValue = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL) {
        $this->_alignment = $pValue;
        return $this;
    }

    /**
     * Get Alignment
     *
     * @return string
     */
    public function getAlignment() {
        return $this->_alignment;
    }

    /**
     * Get hash code
     *
     * @return string    Hash code
     */
    public function getHashCode() {
        return md5(
              $this->_author
            . $this->_text->getHashCode()
            . $this->_width
            . $this->_height
            . $this->_marginLeft
            . $this->_marginTop
            . ($this->_visible ? 1 : 0)
            . $this->_fillColor->getHashCode()
            . $this->_alignment
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

    /**
     * Convert to string
     *
     * @return string
     */
    public function __toString() {
        return $this->_text->getPlainText();
    }

}
