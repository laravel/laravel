<?php

/**
 * PHPExcel_Writer_Excel5_BIFFwriter
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
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */

// Original file header of PEAR::Spreadsheet_Excel_Writer_BIFFwriter (used as the base for this class):
// -----------------------------------------------------------------------------------------
// *  Module written/ported by Xavier Noguer <xnoguer@rezebra.com>
// *
// *  The majority of this is _NOT_ my code.  I simply ported it from the
// *  PERL Spreadsheet::WriteExcel module.
// *
// *  The author of the Spreadsheet::WriteExcel module is John McNamara
// *  <jmcnamara@cpan.org>
// *
// *  I _DO_ maintain this code, and John McNamara has nothing to do with the
// *  porting of this code to PHP.  Any questions directly related to this
// *  class library should be directed to me.
// *
// *  License Information:
// *
// *    Spreadsheet_Excel_Writer:  A library for generating Excel Spreadsheets
// *    Copyright (c) 2002-2003 Xavier Noguer xnoguer@rezebra.com
// *
// *    This library is free software; you can redistribute it and/or
// *    modify it under the terms of the GNU Lesser General Public
// *    License as published by the Free Software Foundation; either
// *    version 2.1 of the License, or (at your option) any later version.
// *
// *    This library is distributed in the hope that it will be useful,
// *    but WITHOUT ANY WARRANTY; without even the implied warranty of
// *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// *    Lesser General Public License for more details.
// *
// *    You should have received a copy of the GNU Lesser General Public
// *    License along with this library; if not, write to the Free Software
// *    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
// */
class PHPExcel_Writer_Excel5_BIFFwriter
{
    /**
     * The byte order of this architecture. 0 => little endian, 1 => big endian
     * @var integer
     */
    private static $byteOrder;

    /**
     * The string containing the data of the BIFF stream
     * @var string
     */
    public $_data;

    /**
     * The size of the data in bytes. Should be the same as strlen($this->_data)
     * @var integer
     */
    public $_datasize;

    /**
     * The maximum length for a BIFF record (excluding record header and length field). See addContinue()
     * @var integer
     * @see addContinue()
     */
    private $limit    = 8224;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_data       = '';
        $this->_datasize   = 0;
//        $this->limit      = 8224;
    }

    /**
     * Determine the byte order and store it as class data to avoid
     * recalculating it for each call to new().
     *
     * @return int
     */
    public static function getByteOrder()
    {
        if (!isset(self::$byteOrder)) {
            // Check if "pack" gives the required IEEE 64bit float
            $teststr = pack("d", 1.2345);
            $number  = pack("C8", 0x8D, 0x97, 0x6E, 0x12, 0x83, 0xC0, 0xF3, 0x3F);
            if ($number == $teststr) {
                $byte_order = 0;    // Little Endian
            } elseif ($number == strrev($teststr)) {
                $byte_order = 1;    // Big Endian
            } else {
                // Give up. I'll fix this in a later version.
                throw new PHPExcel_Writer_Exception("Required floating point format not supported on this platform.");
            }
            self::$byteOrder = $byte_order;
        }

        return self::$byteOrder;
    }

    /**
     * General storage function
     *
     * @param string $data binary data to append
     * @access private
     */
    protected function append($data)
    {
        if (strlen($data) - 4 > $this->limit) {
            $data = $this->addContinue($data);
        }
        $this->_data     .= $data;
        $this->_datasize += strlen($data);
    }

    /**
     * General storage function like append, but returns string instead of modifying $this->_data
     *
     * @param string $data binary data to write
     * @return string
     */
    public function writeData($data)
    {
        if (strlen($data) - 4 > $this->limit) {
            $data = $this->addContinue($data);
        }
        $this->_datasize += strlen($data);

        return $data;
    }

    /**
     * Writes Excel BOF record to indicate the beginning of a stream or
     * sub-stream in the BIFF file.
     *
     * @param  integer $type Type of BIFF file to write: 0x0005 Workbook,
     *                       0x0010 Worksheet.
     * @access private
     */
    protected function storeBof($type)
    {
        $record  = 0x0809;            // Record identifier    (BIFF5-BIFF8)
        $length  = 0x0010;

        // by inspection of real files, MS Office Excel 2007 writes the following
        $unknown = pack("VV", 0x000100D1, 0x00000406);

        $build   = 0x0DBB;            //    Excel 97
        $year    = 0x07CC;            //    Excel 97

        $version = 0x0600;            //    BIFF8

        $header  = pack("vv", $record, $length);
        $data    = pack("vvvv", $version, $type, $build, $year);
        $this->append($header . $data . $unknown);
    }

    /**
     * Writes Excel EOF record to indicate the end of a BIFF stream.
     *
     * @access private
     */
    protected function storeEof()
    {
        $record    = 0x000A;   // Record identifier
        $length    = 0x0000;   // Number of bytes to follow

        $header    = pack("vv", $record, $length);
        $this->append($header);
    }

    /**
     * Writes Excel EOF record to indicate the end of a BIFF stream.
     *
     * @access private
     */
    public function writeEof()
    {
        $record    = 0x000A;   // Record identifier
        $length    = 0x0000;   // Number of bytes to follow
        $header    = pack("vv", $record, $length);
        return $this->writeData($header);
    }

    /**
     * Excel limits the size of BIFF records. In Excel 5 the limit is 2084 bytes. In
     * Excel 97 the limit is 8228 bytes. Records that are longer than these limits
     * must be split up into CONTINUE blocks.
     *
     * This function takes a long BIFF record and inserts CONTINUE records as
     * necessary.
     *
     * @param  string  $data The original binary data to be written
     * @return string        A very convenient string of continue blocks
     * @access private
     */
    private function addContinue($data)
    {
        $limit  = $this->limit;
        $record = 0x003C;         // Record identifier

        // The first 2080/8224 bytes remain intact. However, we have to change
        // the length field of the record.
        $tmp = substr($data, 0, 2) . pack("v", $limit) . substr($data, 4, $limit);

        $header = pack("vv", $record, $limit);  // Headers for continue records

        // Retrieve chunks of 2080/8224 bytes +4 for the header.
        $data_length = strlen($data);
        for ($i = $limit + 4; $i < ($data_length - $limit); $i += $limit) {
            $tmp .= $header;
            $tmp .= substr($data, $i, $limit);
        }

        // Retrieve the last chunk of data
        $header  = pack("vv", $record, strlen($data) - $i);
        $tmp    .= $header;
        $tmp    .= substr($data, $i, strlen($data) - $i);

        return $tmp;
    }
}
