<?php

/** PHPExcel root directory */
if (!defined('PHPEXCEL_ROOT')) {
    /**
     * @ignore
     */
    define('PHPEXCEL_ROOT', dirname(__FILE__) . '/../../');
    require(PHPEXCEL_ROOT . 'PHPExcel/Autoloader.php');
}

/**
 * PHPExcel_Reader_CSV
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
 * @package    PHPExcel_Reader
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Reader_CSV extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    /**
     * Input encoding
     *
     * @access    private
     * @var    string
     */
    private $inputEncoding = 'UTF-8';

    /**
     * Delimiter
     *
     * @access    private
     * @var string
     */
    private $delimiter = ',';

    /**
     * Enclosure
     *
     * @access    private
     * @var    string
     */
    private $enclosure = '"';

    /**
     * Sheet index to read
     *
     * @access    private
     * @var    int
     */
    private $sheetIndex = 0;

    /**
     * Load rows contiguously
     *
     * @access    private
     * @var    int
     */
    private $contiguous = false;

    /**
     * Row counter for loading rows contiguously
     *
     * @var    int
     */
    private $contiguousRow = -1;


    /**
     * Create a new PHPExcel_Reader_CSV
     */
    public function __construct()
    {
        $this->readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }

    /**
     * Validate that the current file is a CSV file
     *
     * @return boolean
     */
    protected function isValidFormat()
    {
        return true;
    }

    /**
     * Set input encoding
     *
     * @param string $pValue Input encoding
     */
    public function setInputEncoding($pValue = 'UTF-8')
    {
        $this->inputEncoding = $pValue;
        return $this;
    }

    /**
     * Get input encoding
     *
     * @return string
     */
    public function getInputEncoding()
    {
        return $this->inputEncoding;
    }

    /**
     * Move filepointer past any BOM marker
     *
     */
    protected function skipBOM()
    {
        rewind($this->fileHandle);

        switch ($this->inputEncoding) {
            case 'UTF-8':
                fgets($this->fileHandle, 4) == "\xEF\xBB\xBF" ?
                    fseek($this->fileHandle, 3) : fseek($this->fileHandle, 0);
                break;
            case 'UTF-16LE':
                fgets($this->fileHandle, 3) == "\xFF\xFE" ?
                    fseek($this->fileHandle, 2) : fseek($this->fileHandle, 0);
                break;
            case 'UTF-16BE':
                fgets($this->fileHandle, 3) == "\xFE\xFF" ?
                    fseek($this->fileHandle, 2) : fseek($this->fileHandle, 0);
                break;
            case 'UTF-32LE':
                fgets($this->fileHandle, 5) == "\xFF\xFE\x00\x00" ?
                    fseek($this->fileHandle, 4) : fseek($this->fileHandle, 0);
                break;
            case 'UTF-32BE':
                fgets($this->fileHandle, 5) == "\x00\x00\xFE\xFF" ?
                    fseek($this->fileHandle, 4) : fseek($this->fileHandle, 0);
                break;
            default:
                break;
        }
    }

    /**
     * Identify any separator that is explicitly set in the file
     *
     */
    protected function checkSeparator()
    {
        $line = fgets($this->fileHandle);
        if ($line === false) {
            return;
        }

        if ((strlen(trim($line)) == 5) && (strpos($line, 'sep=') !== 0)) {
            return $this->skipBOM();
        }
        $this->delimiter = substr($line, 4, 1);
    }

    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns)
     *
     * @param     string         $pFilename
     * @throws    PHPExcel_Reader_Exception
     */
    public function listWorksheetInfo($pFilename)
    {
        // Open file
        $this->openFile($pFilename);
        if (!$this->isValidFormat()) {
            fclose($this->fileHandle);
            throw new PHPExcel_Reader_Exception($pFilename . " is an Invalid Spreadsheet file.");
        }
        $fileHandle = $this->fileHandle;

        // Skip BOM, if any
        $this->skipBOM();
        $this->checkSeparator();

        $escapeEnclosures = array( "\\" . $this->enclosure, $this->enclosure . $this->enclosure );

        $worksheetInfo = array();
        $worksheetInfo[0]['worksheetName'] = 'Worksheet';
        $worksheetInfo[0]['lastColumnLetter'] = 'A';
        $worksheetInfo[0]['lastColumnIndex'] = 0;
        $worksheetInfo[0]['totalRows'] = 0;
        $worksheetInfo[0]['totalColumns'] = 0;

        // Loop through each line of the file in turn
        while (($rowData = fgetcsv($fileHandle, 0, $this->delimiter, $this->enclosure)) !== false) {
            $worksheetInfo[0]['totalRows']++;
            $worksheetInfo[0]['lastColumnIndex'] = max($worksheetInfo[0]['lastColumnIndex'], count($rowData) - 1);
        }

        $worksheetInfo[0]['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($worksheetInfo[0]['lastColumnIndex']);
        $worksheetInfo[0]['totalColumns'] = $worksheetInfo[0]['lastColumnIndex'] + 1;

        // Close file
        fclose($fileHandle);

        return $worksheetInfo;
    }

    /**
     * Loads PHPExcel from file
     *
     * @param     string         $pFilename
     * @return PHPExcel
     * @throws PHPExcel_Reader_Exception
     */
    public function load($pFilename)
    {
        // Create new PHPExcel
        $objPHPExcel = new PHPExcel();

        // Load into this instance
        return $this->loadIntoExisting($pFilename, $objPHPExcel);
    }

    /**
     * Loads PHPExcel from file into PHPExcel instance
     *
     * @param     string         $pFilename
     * @param    PHPExcel    $objPHPExcel
     * @return     PHPExcel
     * @throws     PHPExcel_Reader_Exception
     */
    public function loadIntoExisting($pFilename, PHPExcel $objPHPExcel)
    {
        $lineEnding = ini_get('auto_detect_line_endings');
        ini_set('auto_detect_line_endings', true);

        // Open file
        $this->openFile($pFilename);
        if (!$this->isValidFormat()) {
            fclose($this->fileHandle);
            throw new PHPExcel_Reader_Exception($pFilename . " is an Invalid Spreadsheet file.");
        }
        $fileHandle = $this->fileHandle;

        // Skip BOM, if any
        $this->skipBOM();
        $this->checkSeparator();

        // Create new PHPExcel object
        while ($objPHPExcel->getSheetCount() <= $this->sheetIndex) {
            $objPHPExcel->createSheet();
        }
        $sheet = $objPHPExcel->setActiveSheetIndex($this->sheetIndex);

        $escapeEnclosures = array( "\\" . $this->enclosure,
                                   $this->enclosure . $this->enclosure
                                 );

        // Set our starting row based on whether we're in contiguous mode or not
        $currentRow = 1;
        if ($this->contiguous) {
            $currentRow = ($this->contiguousRow == -1) ? $sheet->getHighestRow(): $this->contiguousRow;
        }

        // Loop through each line of the file in turn
        while (($rowData = fgetcsv($fileHandle, 0, $this->delimiter, $this->enclosure)) !== false) {
            $columnLetter = 'A';
            foreach ($rowData as $rowDatum) {
                if ($rowDatum != '' && $this->readFilter->readCell($columnLetter, $currentRow)) {
                    // Unescape enclosures
                    $rowDatum = str_replace($escapeEnclosures, $this->enclosure, $rowDatum);

                    // Convert encoding if necessary
                    if ($this->inputEncoding !== 'UTF-8') {
                        $rowDatum = PHPExcel_Shared_String::ConvertEncoding($rowDatum, 'UTF-8', $this->inputEncoding);
                    }

                    // Set cell value
                    $sheet->getCell($columnLetter . $currentRow)->setValue($rowDatum);
                }
                ++$columnLetter;
            }
            ++$currentRow;
        }

        // Close file
        fclose($fileHandle);

        if ($this->contiguous) {
            $this->contiguousRow = $currentRow;
        }

        ini_set('auto_detect_line_endings', $lineEnding);

        // Return
        return $objPHPExcel;
    }

    /**
     * Get delimiter
     *
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * Set delimiter
     *
     * @param    string    $pValue        Delimiter, defaults to ,
     * @return    PHPExcel_Reader_CSV
     */
    public function setDelimiter($pValue = ',')
    {
        $this->delimiter = $pValue;
        return $this;
    }

    /**
     * Get enclosure
     *
     * @return string
     */
    public function getEnclosure()
    {
        return $this->enclosure;
    }

    /**
     * Set enclosure
     *
     * @param    string    $pValue        Enclosure, defaults to "
     * @return PHPExcel_Reader_CSV
     */
    public function setEnclosure($pValue = '"')
    {
        if ($pValue == '') {
            $pValue = '"';
        }
        $this->enclosure = $pValue;
        return $this;
    }

    /**
     * Get sheet index
     *
     * @return integer
     */
    public function getSheetIndex()
    {
        return $this->sheetIndex;
    }

    /**
     * Set sheet index
     *
     * @param    integer        $pValue        Sheet index
     * @return PHPExcel_Reader_CSV
     */
    public function setSheetIndex($pValue = 0)
    {
        $this->sheetIndex = $pValue;
        return $this;
    }

    /**
     * Set Contiguous
     *
     * @param boolean $contiguous
     */
    public function setContiguous($contiguous = false)
    {
        $this->contiguous = (bool) $contiguous;
        if (!$contiguous) {
            $this->contiguousRow = -1;
        }

        return $this;
    }

    /**
     * Get Contiguous
     *
     * @return boolean
     */
    public function getContiguous()
    {
        return $this->contiguous;
    }
}
