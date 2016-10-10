<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP Version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2002 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 2.02 of the PHP license,      |
// | that is bundled with this package in the file LICENSE, and is        |
// | available at through the world-wide-web at                           |
// | http://www.php.net/license/2_02.txt.                                 |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Author: Xavier Noguer <xnoguer@php.net>                              |
// | Based on OLE::Storage_Lite by Kawai, Takanori                        |
// +----------------------------------------------------------------------+
//
// $Id: Root.php,v 1.9 2005/04/23 21:53:49 dufuz Exp $


/**
* Class for creating Root PPS's for OLE containers
*
* @author   Xavier Noguer <xnoguer@php.net>
* @category PHPExcel
* @package  PHPExcel_Shared_OLE
*/
class PHPExcel_Shared_OLE_PPS_Root extends PHPExcel_Shared_OLE_PPS
{

    /**
     * Directory for temporary files
     * @var string
     */
    protected $tempDirectory = null;

    /**
     * @param integer $time_1st A timestamp
     * @param integer $time_2nd A timestamp
     */
    public function __construct($time_1st, $time_2nd, $raChild)
    {
        $this->_tempDir = PHPExcel_Shared_File::sys_get_temp_dir();

        parent::__construct(null, PHPExcel_Shared_OLE::Asc2Ucs('Root Entry'), PHPExcel_Shared_OLE::OLE_PPS_TYPE_ROOT, null, null, null, $time_1st, $time_2nd, null, $raChild);
    }

    /**
    * Method for saving the whole OLE container (including files).
    * In fact, if called with an empty argument (or '-'), it saves to a
    * temporary file and then outputs it's contents to stdout.
    * If a resource pointer to a stream created by fopen() is passed
    * it will be used, but you have to close such stream by yourself.
    *
    * @param string|resource $filename The name of the file or stream where to save the OLE container.
    * @access public
    * @return mixed true on success
    */
    public function save($filename)
    {
        // Initial Setting for saving
        $this->_BIG_BLOCK_SIZE  = pow(
            2,
            (isset($this->_BIG_BLOCK_SIZE))? self::adjust2($this->_BIG_BLOCK_SIZE) : 9
        );
        $this->_SMALL_BLOCK_SIZE= pow(
            2,
            (isset($this->_SMALL_BLOCK_SIZE))?  self::adjust2($this->_SMALL_BLOCK_SIZE) : 6
        );

        if (is_resource($filename)) {
            $this->_FILEH_ = $filename;
        } elseif ($filename == '-' || $filename == '') {
            if ($this->tempDirectory === null) {
                $this->tempDirectory = PHPExcel_Shared_File::sys_get_temp_dir();
            }
            $this->_tmp_filename = tempnam($this->tempDirectory, "OLE_PPS_Root");
            $this->_FILEH_ = fopen($this->_tmp_filename, "w+b");
            if ($this->_FILEH_ == false) {
                throw new PHPExcel_Writer_Exception("Can't create temporary file.");
            }
        } else {
            $this->_FILEH_ = fopen($filename, "wb");
        }
        if ($this->_FILEH_ == false) {
            throw new PHPExcel_Writer_Exception("Can't open $filename. It may be in use or protected.");
        }
        // Make an array of PPS's (for Save)
        $aList = array();
        PHPExcel_Shared_OLE_PPS::_savePpsSetPnt($aList, array($this));
        // calculate values for header
        list($iSBDcnt, $iBBcnt, $iPPScnt) = $this->_calcSize($aList); //, $rhInfo);
        // Save Header
        $this->_saveHeader($iSBDcnt, $iBBcnt, $iPPScnt);

        // Make Small Data string (write SBD)
        $this->_data = $this->_makeSmallData($aList);

        // Write BB
        $this->_saveBigData($iSBDcnt, $aList);
        // Write PPS
        $this->_savePps($aList);
        // Write Big Block Depot and BDList and Adding Header informations
        $this->_saveBbd($iSBDcnt, $iBBcnt, $iPPScnt);

        if (!is_resource($filename)) {
            fclose($this->_FILEH_);
        }

        return true;
    }

    /**
    * Calculate some numbers
    *
    * @access public
    * @param array $raList Reference to an array of PPS's
    * @return array The array of numbers
    */
    public function _calcSize(&$raList)
    {
        // Calculate Basic Setting
        list($iSBDcnt, $iBBcnt, $iPPScnt) = array(0,0,0);
        $iSmallLen = 0;
        $iSBcnt = 0;
        $iCount = count($raList);
        for ($i = 0; $i < $iCount; ++$i) {
            if ($raList[$i]->Type == PHPExcel_Shared_OLE::OLE_PPS_TYPE_FILE) {
                $raList[$i]->Size = $raList[$i]->_DataLen();
                if ($raList[$i]->Size < PHPExcel_Shared_OLE::OLE_DATA_SIZE_SMALL) {
                    $iSBcnt += floor($raList[$i]->Size / $this->_SMALL_BLOCK_SIZE)
                                  + (($raList[$i]->Size % $this->_SMALL_BLOCK_SIZE)? 1: 0);
                } else {
                    $iBBcnt += (floor($raList[$i]->Size / $this->_BIG_BLOCK_SIZE) +
                        (($raList[$i]->Size % $this->_BIG_BLOCK_SIZE)? 1: 0));
                }
            }
        }
        $iSmallLen = $iSBcnt * $this->_SMALL_BLOCK_SIZE;
        $iSlCnt = floor($this->_BIG_BLOCK_SIZE / PHPExcel_Shared_OLE::OLE_LONG_INT_SIZE);
        $iSBDcnt = floor($iSBcnt / $iSlCnt) + (($iSBcnt % $iSlCnt)? 1:0);
        $iBBcnt +=  (floor($iSmallLen / $this->_BIG_BLOCK_SIZE) +
                      (( $iSmallLen % $this->_BIG_BLOCK_SIZE)? 1: 0));
        $iCnt = count($raList);
        $iBdCnt = $this->_BIG_BLOCK_SIZE / PHPExcel_Shared_OLE::OLE_PPS_SIZE;
        $iPPScnt = (floor($iCnt/$iBdCnt) + (($iCnt % $iBdCnt)? 1: 0));

        return array($iSBDcnt, $iBBcnt, $iPPScnt);
    }

    /**
    * Helper function for caculating a magic value for block sizes
    *
    * @access public
    * @param integer $i2 The argument
    * @see save()
    * @return integer
    */
    private static function adjust2($i2)
    {
        $iWk = log($i2)/log(2);
        return ($iWk > floor($iWk))? floor($iWk)+1:$iWk;
    }

    /**
    * Save OLE header
    *
    * @access public
    * @param integer $iSBDcnt
    * @param integer $iBBcnt
    * @param integer $iPPScnt
    */
    public function _saveHeader($iSBDcnt, $iBBcnt, $iPPScnt)
    {
        $FILE = $this->_FILEH_;

        // Calculate Basic Setting
        $iBlCnt = $this->_BIG_BLOCK_SIZE / PHPExcel_Shared_OLE::OLE_LONG_INT_SIZE;
        $i1stBdL = ($this->_BIG_BLOCK_SIZE - 0x4C) / PHPExcel_Shared_OLE::OLE_LONG_INT_SIZE;

        $iBdExL = 0;
        $iAll = $iBBcnt + $iPPScnt + $iSBDcnt;
        $iAllW = $iAll;
        $iBdCntW = floor($iAllW / $iBlCnt) + (($iAllW % $iBlCnt)? 1: 0);
        $iBdCnt = floor(($iAll + $iBdCntW) / $iBlCnt) + ((($iAllW+$iBdCntW) % $iBlCnt)? 1: 0);

        // Calculate BD count
        if ($iBdCnt > $i1stBdL) {
            while (1) {
                ++$iBdExL;
                ++$iAllW;
                $iBdCntW = floor($iAllW / $iBlCnt) + (($iAllW % $iBlCnt)? 1: 0);
                $iBdCnt = floor(($iAllW + $iBdCntW) / $iBlCnt) + ((($iAllW+$iBdCntW) % $iBlCnt)? 1: 0);
                if ($iBdCnt <= ($iBdExL*$iBlCnt+ $i1stBdL)) {
                    break;
                }
            }
        }

        // Save Header
        fwrite(
            $FILE,
            "\xD0\xCF\x11\xE0\xA1\xB1\x1A\xE1"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x00"
            . pack("v", 0x3b)
            . pack("v", 0x03)
            . pack("v", -2)
            . pack("v", 9)
            . pack("v", 6)
            . pack("v", 0)
            . "\x00\x00\x00\x00"
            . "\x00\x00\x00\x00"
            . pack("V", $iBdCnt)
            . pack("V", $iBBcnt+$iSBDcnt) //ROOT START
            . pack("V", 0)
            . pack("V", 0x1000)
            . pack("V", $iSBDcnt ? 0 : -2) //Small Block Depot
            . pack("V", $iSBDcnt)
        );
        // Extra BDList Start, Count
        if ($iBdCnt < $i1stBdL) {
            fwrite(
                $FILE,
                pack("V", -2) // Extra BDList Start
                . pack("V", 0)// Extra BDList Count
            );
        } else {
            fwrite($FILE, pack("V", $iAll+$iBdCnt) . pack("V", $iBdExL));
        }

        // BDList
        for ($i = 0; $i < $i1stBdL && $i < $iBdCnt; ++$i) {
            fwrite($FILE, pack("V", $iAll+$i));
        }
        if ($i < $i1stBdL) {
            $jB = $i1stBdL - $i;
            for ($j = 0; $j < $jB; ++$j) {
                fwrite($FILE, (pack("V", -1)));
            }
        }
    }

    /**
    * Saving big data (PPS's with data bigger than PHPExcel_Shared_OLE::OLE_DATA_SIZE_SMALL)
    *
    * @access public
    * @param integer $iStBlk
    * @param array &$raList Reference to array of PPS's
    */
    public function _saveBigData($iStBlk, &$raList)
    {
        $FILE = $this->_FILEH_;

        // cycle through PPS's
        $iCount = count($raList);
        for ($i = 0; $i < $iCount; ++$i) {
            if ($raList[$i]->Type != PHPExcel_Shared_OLE::OLE_PPS_TYPE_DIR) {
                $raList[$i]->Size = $raList[$i]->_DataLen();
                if (($raList[$i]->Size >= PHPExcel_Shared_OLE::OLE_DATA_SIZE_SMALL) || (($raList[$i]->Type == PHPExcel_Shared_OLE::OLE_PPS_TYPE_ROOT) && isset($raList[$i]->_data))) {
                    // Write Data
                    //if (isset($raList[$i]->_PPS_FILE)) {
                    //    $iLen = 0;
                    //    fseek($raList[$i]->_PPS_FILE, 0); // To The Top
                    //    while ($sBuff = fread($raList[$i]->_PPS_FILE, 4096)) {
                    //        $iLen += strlen($sBuff);
                    //        fwrite($FILE, $sBuff);
                    //    }
                    //} else {
                        fwrite($FILE, $raList[$i]->_data);
                    //}

                    if ($raList[$i]->Size % $this->_BIG_BLOCK_SIZE) {
                        fwrite($FILE, str_repeat("\x00", $this->_BIG_BLOCK_SIZE - ($raList[$i]->Size % $this->_BIG_BLOCK_SIZE)));
                    }
                    // Set For PPS
                    $raList[$i]->_StartBlock = $iStBlk;
                    $iStBlk +=
                            (floor($raList[$i]->Size / $this->_BIG_BLOCK_SIZE) +
                                (($raList[$i]->Size % $this->_BIG_BLOCK_SIZE)? 1: 0));
                }
                // Close file for each PPS, and unlink it
                //if (isset($raList[$i]->_PPS_FILE)) {
                //    fclose($raList[$i]->_PPS_FILE);
                //    $raList[$i]->_PPS_FILE = null;
                //    unlink($raList[$i]->_tmp_filename);
                //}
            }
        }
    }

    /**
    * get small data (PPS's with data smaller than PHPExcel_Shared_OLE::OLE_DATA_SIZE_SMALL)
    *
    * @access public
    * @param array &$raList Reference to array of PPS's
    */
    public function _makeSmallData(&$raList)
    {
        $sRes = '';
        $FILE = $this->_FILEH_;
        $iSmBlk = 0;

        $iCount = count($raList);
        for ($i = 0; $i < $iCount; ++$i) {
            // Make SBD, small data string
            if ($raList[$i]->Type == PHPExcel_Shared_OLE::OLE_PPS_TYPE_FILE) {
                if ($raList[$i]->Size <= 0) {
                    continue;
                }
                if ($raList[$i]->Size < PHPExcel_Shared_OLE::OLE_DATA_SIZE_SMALL) {
                    $iSmbCnt = floor($raList[$i]->Size / $this->_SMALL_BLOCK_SIZE)
                                  + (($raList[$i]->Size % $this->_SMALL_BLOCK_SIZE)? 1: 0);
                    // Add to SBD
                    $jB = $iSmbCnt - 1;
                    for ($j = 0; $j < $jB; ++$j) {
                        fwrite($FILE, pack("V", $j+$iSmBlk+1));
                    }
                    fwrite($FILE, pack("V", -2));

                    //// Add to Data String(this will be written for RootEntry)
                    //if ($raList[$i]->_PPS_FILE) {
                    //    fseek($raList[$i]->_PPS_FILE, 0); // To The Top
                    //    while ($sBuff = fread($raList[$i]->_PPS_FILE, 4096)) {
                    //        $sRes .= $sBuff;
                    //    }
                    //} else {
                        $sRes .= $raList[$i]->_data;
                    //}
                    if ($raList[$i]->Size % $this->_SMALL_BLOCK_SIZE) {
                        $sRes .= str_repeat("\x00", $this->_SMALL_BLOCK_SIZE - ($raList[$i]->Size % $this->_SMALL_BLOCK_SIZE));
                    }
                    // Set for PPS
                    $raList[$i]->_StartBlock = $iSmBlk;
                    $iSmBlk += $iSmbCnt;
                }
            }
        }
        $iSbCnt = floor($this->_BIG_BLOCK_SIZE / PHPExcel_Shared_OLE::OLE_LONG_INT_SIZE);
        if ($iSmBlk % $iSbCnt) {
            $iB = $iSbCnt - ($iSmBlk % $iSbCnt);
            for ($i = 0; $i < $iB; ++$i) {
                fwrite($FILE, pack("V", -1));
            }
        }
        return $sRes;
    }

    /**
    * Saves all the PPS's WKs
    *
    * @access public
    * @param array $raList Reference to an array with all PPS's
    */
    public function _savePps(&$raList)
    {
        // Save each PPS WK
        $iC = count($raList);
        for ($i = 0; $i < $iC; ++$i) {
            fwrite($this->_FILEH_, $raList[$i]->_getPpsWk());
        }
        // Adjust for Block
        $iCnt = count($raList);
        $iBCnt = $this->_BIG_BLOCK_SIZE / PHPExcel_Shared_OLE::OLE_PPS_SIZE;
        if ($iCnt % $iBCnt) {
            fwrite($this->_FILEH_, str_repeat("\x00", ($iBCnt - ($iCnt % $iBCnt)) * PHPExcel_Shared_OLE::OLE_PPS_SIZE));
        }
    }

    /**
    * Saving Big Block Depot
    *
    * @access public
    * @param integer $iSbdSize
    * @param integer $iBsize
    * @param integer $iPpsCnt
    */
    public function _saveBbd($iSbdSize, $iBsize, $iPpsCnt)
    {
        $FILE = $this->_FILEH_;
        // Calculate Basic Setting
        $iBbCnt = $this->_BIG_BLOCK_SIZE / PHPExcel_Shared_OLE::OLE_LONG_INT_SIZE;
        $i1stBdL = ($this->_BIG_BLOCK_SIZE - 0x4C) / PHPExcel_Shared_OLE::OLE_LONG_INT_SIZE;

        $iBdExL = 0;
        $iAll = $iBsize + $iPpsCnt + $iSbdSize;
        $iAllW = $iAll;
        $iBdCntW = floor($iAllW / $iBbCnt) + (($iAllW % $iBbCnt)? 1: 0);
        $iBdCnt = floor(($iAll + $iBdCntW) / $iBbCnt) + ((($iAllW+$iBdCntW) % $iBbCnt)? 1: 0);
        // Calculate BD count
        if ($iBdCnt >$i1stBdL) {
            while (1) {
                ++$iBdExL;
                ++$iAllW;
                $iBdCntW = floor($iAllW / $iBbCnt) + (($iAllW % $iBbCnt)? 1: 0);
                $iBdCnt = floor(($iAllW + $iBdCntW) / $iBbCnt) + ((($iAllW+$iBdCntW) % $iBbCnt)? 1: 0);
                if ($iBdCnt <= ($iBdExL*$iBbCnt+ $i1stBdL)) {
                    break;
                }
            }
        }

        // Making BD
        // Set for SBD
        if ($iSbdSize > 0) {
            for ($i = 0; $i < ($iSbdSize - 1); ++$i) {
                fwrite($FILE, pack("V", $i+1));
            }
            fwrite($FILE, pack("V", -2));
        }
        // Set for B
        for ($i = 0; $i < ($iBsize - 1); ++$i) {
            fwrite($FILE, pack("V", $i+$iSbdSize+1));
        }
        fwrite($FILE, pack("V", -2));

        // Set for PPS
        for ($i = 0; $i < ($iPpsCnt - 1); ++$i) {
            fwrite($FILE, pack("V", $i+$iSbdSize+$iBsize+1));
        }
        fwrite($FILE, pack("V", -2));
        // Set for BBD itself ( 0xFFFFFFFD : BBD)
        for ($i = 0; $i < $iBdCnt; ++$i) {
            fwrite($FILE, pack("V", 0xFFFFFFFD));
        }
        // Set for ExtraBDList
        for ($i = 0; $i < $iBdExL; ++$i) {
            fwrite($FILE, pack("V", 0xFFFFFFFC));
        }
        // Adjust for Block
        if (($iAllW + $iBdCnt) % $iBbCnt) {
            $iBlock = ($iBbCnt - (($iAllW + $iBdCnt) % $iBbCnt));
            for ($i = 0; $i < $iBlock; ++$i) {
                fwrite($FILE, pack("V", -1));
            }
        }
        // Extra BDList
        if ($iBdCnt > $i1stBdL) {
            $iN=0;
            $iNb=0;
            for ($i = $i1stBdL; $i < $iBdCnt; $i++, ++$iN) {
                if ($iN >= ($iBbCnt - 1)) {
                    $iN = 0;
                    ++$iNb;
                    fwrite($FILE, pack("V", $iAll+$iBdCnt+$iNb));
                }
                fwrite($FILE, pack("V", $iBsize+$iSbdSize+$iPpsCnt+$i));
            }
            if (($iBdCnt-$i1stBdL) % ($iBbCnt-1)) {
                $iB = ($iBbCnt - 1) - (($iBdCnt - $i1stBdL) % ($iBbCnt - 1));
                for ($i = 0; $i < $iB; ++$i) {
                    fwrite($FILE, pack("V", -1));
                }
            }
            fwrite($FILE, pack("V", -2));
        }
    }
}
