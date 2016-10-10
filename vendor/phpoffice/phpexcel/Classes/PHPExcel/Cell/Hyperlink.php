<?php

/**
 * PHPExcel_Cell_Hyperlink
 *
 * Copyright (c) 2006 - 2015 PHPExcel
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
 * @package    PHPExcel_Cell
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Cell_Hyperlink
{
    /**
     * URL to link the cell to
     *
     * @var string
     */
    private $url;

    /**
     * Tooltip to display on the hyperlink
     *
     * @var string
     */
    private $tooltip;

    /**
     * Create a new PHPExcel_Cell_Hyperlink
     *
     * @param  string  $pUrl      Url to link the cell to
     * @param  string  $pTooltip  Tooltip to display on the hyperlink
     */
    public function __construct($pUrl = '', $pTooltip = '')
    {
        // Initialise member variables
        $this->url     = $pUrl;
        $this->tooltip = $pTooltip;
    }

    /**
     * Get URL
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set URL
     *
     * @param  string    $value
     * @return PHPExcel_Cell_Hyperlink
     */
    public function setUrl($value = '')
    {
        $this->url = $value;
        return $this;
    }

    /**
     * Get tooltip
     *
     * @return string
     */
    public function getTooltip()
    {
        return $this->tooltip;
    }

    /**
     * Set tooltip
     *
     * @param  string    $value
     * @return PHPExcel_Cell_Hyperlink
     */
    public function setTooltip($value = '')
    {
        $this->tooltip = $value;
        return $this;
    }

    /**
     * Is this hyperlink internal? (to another worksheet)
     *
     * @return boolean
     */
    public function isInternal()
    {
        return strpos($this->url, 'sheet://') !== false;
    }

    /**
     * Get hash code
     *
     * @return string    Hash code
     */
    public function getHashCode()
    {
        return md5(
            $this->url .
            $this->tooltip .
            __CLASS__
        );
    }
}
