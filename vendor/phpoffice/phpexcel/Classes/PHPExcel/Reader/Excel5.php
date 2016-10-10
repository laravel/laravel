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
 * PHPExcel_Reader_Excel5
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
 * @package    PHPExcel_Reader_Excel5
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */

// Original file header of ParseXL (used as the base for this class):
// --------------------------------------------------------------------------------
// Adapted from Excel_Spreadsheet_Reader developed by users bizon153,
// trex005, and mmp11 (SourceForge.net)
// http://sourceforge.net/projects/phpexcelreader/
// Primary changes made by canyoncasa (dvc) for ParseXL 1.00 ...
//     Modelled moreso after Perl Excel Parse/Write modules
//     Added Parse_Excel_Spreadsheet object
//         Reads a whole worksheet or tab as row,column array or as
//         associated hash of indexed rows and named column fields
//     Added variables for worksheet (tab) indexes and names
//     Added an object call for loading individual woorksheets
//     Changed default indexing defaults to 0 based arrays
//     Fixed date/time and percent formats
//     Includes patches found at SourceForge...
//         unicode patch by nobody
//         unpack("d") machine depedency patch by matchy
//         boundsheet utf16 patch by bjaenichen
//     Renamed functions for shorter names
//     General code cleanup and rigor, including <80 column width
//     Included a testcase Excel file and PHP example calls
//     Code works for PHP 5.x

// Primary changes made by canyoncasa (dvc) for ParseXL 1.10 ...
// http://sourceforge.net/tracker/index.php?func=detail&aid=1466964&group_id=99160&atid=623334
//     Decoding of formula conditions, results, and tokens.
//     Support for user-defined named cells added as an array "namedcells"
//         Patch code for user-defined named cells supports single cells only.
//         NOTE: this patch only works for BIFF8 as BIFF5-7 use a different
//         external sheet reference structure
class PHPExcel_Reader_Excel5 extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    // ParseXL definitions
    const XLS_BIFF8                     = 0x0600;
    const XLS_BIFF7                     = 0x0500;
    const XLS_WorkbookGlobals           = 0x0005;
    const XLS_Worksheet                 = 0x0010;

    // record identifiers
    const XLS_TYPE_FORMULA              = 0x0006;
    const XLS_TYPE_EOF                  = 0x000a;
    const XLS_TYPE_PROTECT              = 0x0012;
    const XLS_TYPE_OBJECTPROTECT        = 0x0063;
    const XLS_TYPE_SCENPROTECT          = 0x00dd;
    const XLS_TYPE_PASSWORD             = 0x0013;
    const XLS_TYPE_HEADER               = 0x0014;
    const XLS_TYPE_FOOTER               = 0x0015;
    const XLS_TYPE_EXTERNSHEET          = 0x0017;
    const XLS_TYPE_DEFINEDNAME          = 0x0018;
    const XLS_TYPE_VERTICALPAGEBREAKS   = 0x001a;
    const XLS_TYPE_HORIZONTALPAGEBREAKS = 0x001b;
    const XLS_TYPE_NOTE                 = 0x001c;
    const XLS_TYPE_SELECTION            = 0x001d;
    const XLS_TYPE_DATEMODE             = 0x0022;
    const XLS_TYPE_EXTERNNAME           = 0x0023;
    const XLS_TYPE_LEFTMARGIN           = 0x0026;
    const XLS_TYPE_RIGHTMARGIN          = 0x0027;
    const XLS_TYPE_TOPMARGIN            = 0x0028;
    const XLS_TYPE_BOTTOMMARGIN         = 0x0029;
    const XLS_TYPE_PRINTGRIDLINES       = 0x002b;
    const XLS_TYPE_FILEPASS             = 0x002f;
    const XLS_TYPE_FONT                 = 0x0031;
    const XLS_TYPE_CONTINUE             = 0x003c;
    const XLS_TYPE_PANE                 = 0x0041;
    const XLS_TYPE_CODEPAGE             = 0x0042;
    const XLS_TYPE_DEFCOLWIDTH          = 0x0055;
    const XLS_TYPE_OBJ                  = 0x005d;
    const XLS_TYPE_COLINFO              = 0x007d;
    const XLS_TYPE_IMDATA               = 0x007f;
    const XLS_TYPE_SHEETPR              = 0x0081;
    const XLS_TYPE_HCENTER              = 0x0083;
    const XLS_TYPE_VCENTER              = 0x0084;
    const XLS_TYPE_SHEET                = 0x0085;
    const XLS_TYPE_PALETTE              = 0x0092;
    const XLS_TYPE_SCL                  = 0x00a0;
    const XLS_TYPE_PAGESETUP            = 0x00a1;
    const XLS_TYPE_MULRK                = 0x00bd;
    const XLS_TYPE_MULBLANK             = 0x00be;
    const XLS_TYPE_DBCELL               = 0x00d7;
    const XLS_TYPE_XF                   = 0x00e0;
    const XLS_TYPE_MERGEDCELLS          = 0x00e5;
    const XLS_TYPE_MSODRAWINGGROUP      = 0x00eb;
    const XLS_TYPE_MSODRAWING           = 0x00ec;
    const XLS_TYPE_SST                  = 0x00fc;
    const XLS_TYPE_LABELSST             = 0x00fd;
    const XLS_TYPE_EXTSST               = 0x00ff;
    const XLS_TYPE_EXTERNALBOOK         = 0x01ae;
    const XLS_TYPE_DATAVALIDATIONS      = 0x01b2;
    const XLS_TYPE_TXO                  = 0x01b6;
    const XLS_TYPE_HYPERLINK            = 0x01b8;
    const XLS_TYPE_DATAVALIDATION       = 0x01be;
    const XLS_TYPE_DIMENSION            = 0x0200;
    const XLS_TYPE_BLANK                = 0x0201;
    const XLS_TYPE_NUMBER               = 0x0203;
    const XLS_TYPE_LABEL                = 0x0204;
    const XLS_TYPE_BOOLERR              = 0x0205;
    const XLS_TYPE_STRING               = 0x0207;
    const XLS_TYPE_ROW                  = 0x0208;
    const XLS_TYPE_INDEX                = 0x020b;
    const XLS_TYPE_ARRAY                = 0x0221;
    const XLS_TYPE_DEFAULTROWHEIGHT     = 0x0225;
    const XLS_TYPE_WINDOW2              = 0x023e;
    const XLS_TYPE_RK                   = 0x027e;
    const XLS_TYPE_STYLE                = 0x0293;
    const XLS_TYPE_FORMAT               = 0x041e;
    const XLS_TYPE_SHAREDFMLA           = 0x04bc;
    const XLS_TYPE_BOF                  = 0x0809;
    const XLS_TYPE_SHEETPROTECTION      = 0x0867;
    const XLS_TYPE_RANGEPROTECTION      = 0x0868;
    const XLS_TYPE_SHEETLAYOUT          = 0x0862;
    const XLS_TYPE_XFEXT                = 0x087d;
    const XLS_TYPE_PAGELAYOUTVIEW       = 0x088b;
    const XLS_TYPE_UNKNOWN              = 0xffff;

    // Encryption type
    const MS_BIFF_CRYPTO_NONE           = 0;
    const MS_BIFF_CRYPTO_XOR            = 1;
    const MS_BIFF_CRYPTO_RC4            = 2;
    
    // Size of stream blocks when using RC4 encryption
    const REKEY_BLOCK                   = 0x400;

    /**
     * Summary Information stream data.
     *
     * @var string
     */
    private $summaryInformation;

    /**
     * Extended Summary Information stream data.
     *
     * @var string
     */
    private $documentSummaryInformation;

    /**
     * User-Defined Properties stream data.
     *
     * @var string
     */
    private $userDefinedProperties;

    /**
     * Workbook stream data. (Includes workbook globals substream as well as sheet substreams)
     *
     * @var string
     */
    private $data;

    /**
     * Size in bytes of $this->data
     *
     * @var int
     */
    private $dataSize;

    /**
     * Current position in stream
     *
     * @var integer
     */
    private $pos;

    /**
     * Workbook to be returned by the reader.
     *
     * @var PHPExcel
     */
    private $phpExcel;

    /**
     * Worksheet that is currently being built by the reader.
     *
     * @var PHPExcel_Worksheet
     */
    private $phpSheet;

    /**
     * BIFF version
     *
     * @var int
     */
    private $version;

    /**
     * Codepage set in the Excel file being read. Only important for BIFF5 (Excel 5.0 - Excel 95)
     * For BIFF8 (Excel 97 - Excel 2003) this will always have the value 'UTF-16LE'
     *
     * @var string
     */
    private $codepage;

    /**
     * Shared formats
     *
     * @var array
     */
    private $formats;

    /**
     * Shared fonts
     *
     * @var array
     */
    private $objFonts;

    /**
     * Color palette
     *
     * @var array
     */
    private $palette;

    /**
     * Worksheets
     *
     * @var array
     */
    private $sheets;

    /**
     * External books
     *
     * @var array
     */
    private $externalBooks;

    /**
     * REF structures. Only applies to BIFF8.
     *
     * @var array
     */
    private $ref;

    /**
     * External names
     *
     * @var array
     */
    private $externalNames;

    /**
     * Defined names
     *
     * @var array
     */
    private $definedname;

    /**
     * Shared strings. Only applies to BIFF8.
     *
     * @var array
     */
    private $sst;

    /**
     * Panes are frozen? (in sheet currently being read). See WINDOW2 record.
     *
     * @var boolean
     */
    private $frozen;

    /**
     * Fit printout to number of pages? (in sheet currently being read). See SHEETPR record.
     *
     * @var boolean
     */
    private $isFitToPages;

    /**
     * Objects. One OBJ record contributes with one entry.
     *
     * @var array
     */
    private $objs;

    /**
     * Text Objects. One TXO record corresponds with one entry.
     *
     * @var array
     */
    private $textObjects;

    /**
     * Cell Annotations (BIFF8)
     *
     * @var array
     */
    private $cellNotes;

    /**
     * The combined MSODRAWINGGROUP data
     *
     * @var string
     */
    private $drawingGroupData;

    /**
     * The combined MSODRAWING data (per sheet)
     *
     * @var string
     */
    private $drawingData;

    /**
     * Keep track of XF index
     *
     * @var int
     */
    private $xfIndex;

    /**
     * Mapping of XF index (that is a cell XF) to final index in cellXf collection
     *
     * @var array
     */
    private $mapCellXfIndex;

    /**
     * Mapping of XF index (that is a style XF) to final index in cellStyleXf collection
     *
     * @var array
     */
    private $mapCellStyleXfIndex;

    /**
     * The shared formulas in a sheet. One SHAREDFMLA record contributes with one value.
     *
     * @var array
     */
    private $sharedFormulas;

    /**
     * The shared formula parts in a sheet. One FORMULA record contributes with one value if it
     * refers to a shared formula.
     *
     * @var array
     */
    private $sharedFormulaParts;

    /**
     * The type of encryption in use
     *
     * @var int
     */
    private $encryption = 0;
    
    /**
     * The position in the stream after which contents are encrypted
     *
     * @var int
     */
    private $encryptionStartPos = false;

    /**
     * The current RC4 decryption object
     *
     * @var PHPExcel_Reader_Excel5_RC4
     */
    private $rc4Key = null;

    /**
     * The position in the stream that the RC4 decryption object was left at
     *
     * @var int
     */
    private $rc4Pos = 0;

    /**
     * The current MD5 context state
     *
     * @var string
     */
    private $md5Ctxt = null;

    /**
     * Create a new PHPExcel_Reader_Excel5 instance
     */
    public function __construct()
    {
        $this->readFilter = new PHPExcel_Reader_DefaultReadFilter();
    }

    /**
     * Can the current PHPExcel_Reader_IReader read the file?
     *
     * @param     string         $pFilename
     * @return     boolean
     * @throws PHPExcel_Reader_Exception
     */
    public function canRead($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }

        try {
            // Use ParseXL for the hard work.
            $ole = new PHPExcel_Shared_OLERead();

            // get excel data
            $res = $ole->read($pFilename);
            return true;
        } catch (PHPExcel_Exception $e) {
            return false;
        }
    }

    /**
     * Reads names of the worksheets from a file, without parsing the whole file to a PHPExcel object
     *
     * @param     string         $pFilename
     * @throws     PHPExcel_Reader_Exception
     */
    public function listWorksheetNames($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }

        $worksheetNames = array();

        // Read the OLE file
        $this->loadOLE($pFilename);

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $this->dataSize = strlen($this->data);

        $this->pos        = 0;
        $this->sheets    = array();

        // Parse Workbook Global Substream
        while ($this->pos < $this->dataSize) {
            $code = self::getInt2d($this->data, $this->pos);

            switch ($code) {
                case self::XLS_TYPE_BOF:
                    $this->readBof();
                    break;
                case self::XLS_TYPE_SHEET:
                    $this->readSheet();
                    break;
                case self::XLS_TYPE_EOF:
                    $this->readDefault();
                    break 2;
                default:
                    $this->readDefault();
                    break;
            }
        }

        foreach ($this->sheets as $sheet) {
            if ($sheet['sheetType'] != 0x00) {
                // 0x00: Worksheet, 0x02: Chart, 0x06: Visual Basic module
                continue;
            }

            $worksheetNames[] = $sheet['name'];
        }

        return $worksheetNames;
    }


    /**
     * Return worksheet info (Name, Last Column Letter, Last Column Index, Total Rows, Total Columns)
     *
     * @param   string     $pFilename
     * @throws   PHPExcel_Reader_Exception
     */
    public function listWorksheetInfo($pFilename)
    {
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }

        $worksheetInfo = array();

        // Read the OLE file
        $this->loadOLE($pFilename);

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $this->dataSize = strlen($this->data);

        // initialize
        $this->pos    = 0;
        $this->sheets = array();

        // Parse Workbook Global Substream
        while ($this->pos < $this->dataSize) {
            $code = self::getInt2d($this->data, $this->pos);

            switch ($code) {
                case self::XLS_TYPE_BOF:
                    $this->readBof();
                    break;
                case self::XLS_TYPE_SHEET:
                    $this->readSheet();
                    break;
                case self::XLS_TYPE_EOF:
                    $this->readDefault();
                    break 2;
                default:
                    $this->readDefault();
                    break;
            }
        }

        // Parse the individual sheets
        foreach ($this->sheets as $sheet) {
            if ($sheet['sheetType'] != 0x00) {
                // 0x00: Worksheet
                // 0x02: Chart
                // 0x06: Visual Basic module
                continue;
            }

            $tmpInfo = array();
            $tmpInfo['worksheetName'] = $sheet['name'];
            $tmpInfo['lastColumnLetter'] = 'A';
            $tmpInfo['lastColumnIndex'] = 0;
            $tmpInfo['totalRows'] = 0;
            $tmpInfo['totalColumns'] = 0;

            $this->pos = $sheet['offset'];

            while ($this->pos <= $this->dataSize - 4) {
                $code = self::getInt2d($this->data, $this->pos);

                switch ($code) {
                    case self::XLS_TYPE_RK:
                    case self::XLS_TYPE_LABELSST:
                    case self::XLS_TYPE_NUMBER:
                    case self::XLS_TYPE_FORMULA:
                    case self::XLS_TYPE_BOOLERR:
                    case self::XLS_TYPE_LABEL:
                        $length = self::getInt2d($this->data, $this->pos + 2);
                        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

                        // move stream pointer to next record
                        $this->pos += 4 + $length;

                        $rowIndex = self::getInt2d($recordData, 0) + 1;
                        $columnIndex = self::getInt2d($recordData, 2);

                        $tmpInfo['totalRows'] = max($tmpInfo['totalRows'], $rowIndex);
                        $tmpInfo['lastColumnIndex'] = max($tmpInfo['lastColumnIndex'], $columnIndex);
                        break;
                    case self::XLS_TYPE_BOF:
                        $this->readBof();
                        break;
                    case self::XLS_TYPE_EOF:
                        $this->readDefault();
                        break 2;
                    default:
                        $this->readDefault();
                        break;
                }
            }

            $tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
            $tmpInfo['totalColumns'] = $tmpInfo['lastColumnIndex'] + 1;

            $worksheetInfo[] = $tmpInfo;
        }

        return $worksheetInfo;
    }


    /**
     * Loads PHPExcel from file
     *
     * @param     string         $pFilename
     * @return     PHPExcel
     * @throws     PHPExcel_Reader_Exception
     */
    public function load($pFilename)
    {
        // Read the OLE file
        $this->loadOLE($pFilename);

        // Initialisations
        $this->phpExcel = new PHPExcel;
        $this->phpExcel->removeSheetByIndex(0); // remove 1st sheet
        if (!$this->readDataOnly) {
            $this->phpExcel->removeCellStyleXfByIndex(0); // remove the default style
            $this->phpExcel->removeCellXfByIndex(0); // remove the default style
        }

        // Read the summary information stream (containing meta data)
        $this->readSummaryInformation();

        // Read the Additional document summary information stream (containing application-specific meta data)
        $this->readDocumentSummaryInformation();

        // total byte size of Excel data (workbook global substream + sheet substreams)
        $this->dataSize = strlen($this->data);

        // initialize
        $this->pos                 = 0;
        $this->codepage            = 'CP1252';
        $this->formats             = array();
        $this->objFonts            = array();
        $this->palette             = array();
        $this->sheets              = array();
        $this->externalBooks       = array();
        $this->ref                 = array();
        $this->definedname         = array();
        $this->sst                 = array();
        $this->drawingGroupData    = '';
        $this->xfIndex             = '';
        $this->mapCellXfIndex      = array();
        $this->mapCellStyleXfIndex = array();

        // Parse Workbook Global Substream
        while ($this->pos < $this->dataSize) {
            $code = self::getInt2d($this->data, $this->pos);

            switch ($code) {
                case self::XLS_TYPE_BOF:
                    $this->readBof();
                    break;
                case self::XLS_TYPE_FILEPASS:
                    $this->readFilepass();
                    break;
                case self::XLS_TYPE_CODEPAGE:
                    $this->readCodepage();
                    break;
                case self::XLS_TYPE_DATEMODE:
                    $this->readDateMode();
                    break;
                case self::XLS_TYPE_FONT:
                    $this->readFont();
                    break;
                case self::XLS_TYPE_FORMAT:
                    $this->readFormat();
                    break;
                case self::XLS_TYPE_XF:
                    $this->readXf();
                    break;
                case self::XLS_TYPE_XFEXT:
                    $this->readXfExt();
                    break;
                case self::XLS_TYPE_STYLE:
                    $this->readStyle();
                    break;
                case self::XLS_TYPE_PALETTE:
                    $this->readPalette();
                    break;
                case self::XLS_TYPE_SHEET:
                    $this->readSheet();
                    break;
                case self::XLS_TYPE_EXTERNALBOOK:
                    $this->readExternalBook();
                    break;
                case self::XLS_TYPE_EXTERNNAME:
                    $this->readExternName();
                    break;
                case self::XLS_TYPE_EXTERNSHEET:
                    $this->readExternSheet();
                    break;
                case self::XLS_TYPE_DEFINEDNAME:
                    $this->readDefinedName();
                    break;
                case self::XLS_TYPE_MSODRAWINGGROUP:
                    $this->readMsoDrawingGroup();
                    break;
                case self::XLS_TYPE_SST:
                    $this->readSst();
                    break;
                case self::XLS_TYPE_EOF:
                    $this->readDefault();
                    break 2;
                default:
                    $this->readDefault();
                    break;
            }
        }

        // Resolve indexed colors for font, fill, and border colors
        // Cannot be resolved already in XF record, because PALETTE record comes afterwards
        if (!$this->readDataOnly) {
            foreach ($this->objFonts as $objFont) {
                if (isset($objFont->colorIndex)) {
                    $color = PHPExcel_Reader_Excel5_Color::map($objFont->colorIndex, $this->palette, $this->version);
                    $objFont->getColor()->setRGB($color['rgb']);
                }
            }

            foreach ($this->phpExcel->getCellXfCollection() as $objStyle) {
                // fill start and end color
                $fill = $objStyle->getFill();

                if (isset($fill->startcolorIndex)) {
                    $startColor = PHPExcel_Reader_Excel5_Color::map($fill->startcolorIndex, $this->palette, $this->version);
                    $fill->getStartColor()->setRGB($startColor['rgb']);
                }
                if (isset($fill->endcolorIndex)) {
                    $endColor = PHPExcel_Reader_Excel5_Color::map($fill->endcolorIndex, $this->palette, $this->version);
                    $fill->getEndColor()->setRGB($endColor['rgb']);
                }

                // border colors
                $top      = $objStyle->getBorders()->getTop();
                $right    = $objStyle->getBorders()->getRight();
                $bottom   = $objStyle->getBorders()->getBottom();
                $left     = $objStyle->getBorders()->getLeft();
                $diagonal = $objStyle->getBorders()->getDiagonal();

                if (isset($top->colorIndex)) {
                    $borderTopColor = PHPExcel_Reader_Excel5_Color::map($top->colorIndex, $this->palette, $this->version);
                    $top->getColor()->setRGB($borderTopColor['rgb']);
                }
                if (isset($right->colorIndex)) {
                    $borderRightColor = PHPExcel_Reader_Excel5_Color::map($right->colorIndex, $this->palette, $this->version);
                    $right->getColor()->setRGB($borderRightColor['rgb']);
                }
                if (isset($bottom->colorIndex)) {
                    $borderBottomColor = PHPExcel_Reader_Excel5_Color::map($bottom->colorIndex, $this->palette, $this->version);
                    $bottom->getColor()->setRGB($borderBottomColor['rgb']);
                }
                if (isset($left->colorIndex)) {
                    $borderLeftColor = PHPExcel_Reader_Excel5_Color::map($left->colorIndex, $this->palette, $this->version);
                    $left->getColor()->setRGB($borderLeftColor['rgb']);
                }
                if (isset($diagonal->colorIndex)) {
                    $borderDiagonalColor = PHPExcel_Reader_Excel5_Color::map($diagonal->colorIndex, $this->palette, $this->version);
                    $diagonal->getColor()->setRGB($borderDiagonalColor['rgb']);
                }
            }
        }

        // treat MSODRAWINGGROUP records, workbook-level Escher
        if (!$this->readDataOnly && $this->drawingGroupData) {
            $escherWorkbook = new PHPExcel_Shared_Escher();
            $reader = new PHPExcel_Reader_Excel5_Escher($escherWorkbook);
            $escherWorkbook = $reader->load($this->drawingGroupData);

            // debug Escher stream
            //$debug = new Debug_Escher(new PHPExcel_Shared_Escher());
            //$debug->load($this->drawingGroupData);
        }

        // Parse the individual sheets
        foreach ($this->sheets as $sheet) {
            if ($sheet['sheetType'] != 0x00) {
                // 0x00: Worksheet, 0x02: Chart, 0x06: Visual Basic module
                continue;
            }

            // check if sheet should be skipped
            if (isset($this->loadSheetsOnly) && !in_array($sheet['name'], $this->loadSheetsOnly)) {
                continue;
            }

            // add sheet to PHPExcel object
            $this->phpSheet = $this->phpExcel->createSheet();
            //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in formula
            //        cells... during the load, all formulae should be correct, and we're simply bringing the worksheet
            //        name in line with the formula, not the reverse
            $this->phpSheet->setTitle($sheet['name'], false);
            $this->phpSheet->setSheetState($sheet['sheetState']);

            $this->pos = $sheet['offset'];

            // Initialize isFitToPages. May change after reading SHEETPR record.
            $this->isFitToPages = false;

            // Initialize drawingData
            $this->drawingData = '';

            // Initialize objs
            $this->objs = array();

            // Initialize shared formula parts
            $this->sharedFormulaParts = array();

            // Initialize shared formulas
            $this->sharedFormulas = array();

            // Initialize text objs
            $this->textObjects = array();

            // Initialize cell annotations
            $this->cellNotes = array();
            $this->textObjRef = -1;

            while ($this->pos <= $this->dataSize - 4) {
                $code = self::getInt2d($this->data, $this->pos);

                switch ($code) {
                    case self::XLS_TYPE_BOF:
                        $this->readBof();
                        break;
                    case self::XLS_TYPE_PRINTGRIDLINES:
                        $this->readPrintGridlines();
                        break;
                    case self::XLS_TYPE_DEFAULTROWHEIGHT:
                        $this->readDefaultRowHeight();
                        break;
                    case self::XLS_TYPE_SHEETPR:
                        $this->readSheetPr();
                        break;
                    case self::XLS_TYPE_HORIZONTALPAGEBREAKS:
                        $this->readHorizontalPageBreaks();
                        break;
                    case self::XLS_TYPE_VERTICALPAGEBREAKS:
                        $this->readVerticalPageBreaks();
                        break;
                    case self::XLS_TYPE_HEADER:
                        $this->readHeader();
                        break;
                    case self::XLS_TYPE_FOOTER:
                        $this->readFooter();
                        break;
                    case self::XLS_TYPE_HCENTER:
                        $this->readHcenter();
                        break;
                    case self::XLS_TYPE_VCENTER:
                        $this->readVcenter();
                        break;
                    case self::XLS_TYPE_LEFTMARGIN:
                        $this->readLeftMargin();
                        break;
                    case self::XLS_TYPE_RIGHTMARGIN:
                        $this->readRightMargin();
                        break;
                    case self::XLS_TYPE_TOPMARGIN:
                        $this->readTopMargin();
                        break;
                    case self::XLS_TYPE_BOTTOMMARGIN:
                        $this->readBottomMargin();
                        break;
                    case self::XLS_TYPE_PAGESETUP:
                        $this->readPageSetup();
                        break;
                    case self::XLS_TYPE_PROTECT:
                        $this->readProtect();
                        break;
                    case self::XLS_TYPE_SCENPROTECT:
                        $this->readScenProtect();
                        break;
                    case self::XLS_TYPE_OBJECTPROTECT:
                        $this->readObjectProtect();
                        break;
                    case self::XLS_TYPE_PASSWORD:
                        $this->readPassword();
                        break;
                    case self::XLS_TYPE_DEFCOLWIDTH:
                        $this->readDefColWidth();
                        break;
                    case self::XLS_TYPE_COLINFO:
                        $this->readColInfo();
                        break;
                    case self::XLS_TYPE_DIMENSION:
                        $this->readDefault();
                        break;
                    case self::XLS_TYPE_ROW:
                        $this->readRow();
                        break;
                    case self::XLS_TYPE_DBCELL:
                        $this->readDefault();
                        break;
                    case self::XLS_TYPE_RK:
                        $this->readRk();
                        break;
                    case self::XLS_TYPE_LABELSST:
                        $this->readLabelSst();
                        break;
                    case self::XLS_TYPE_MULRK:
                        $this->readMulRk();
                        break;
                    case self::XLS_TYPE_NUMBER:
                        $this->readNumber();
                        break;
                    case self::XLS_TYPE_FORMULA:
                        $this->readFormula();
                        break;
                    case self::XLS_TYPE_SHAREDFMLA:
                        $this->readSharedFmla();
                        break;
                    case self::XLS_TYPE_BOOLERR:
                        $this->readBoolErr();
                        break;
                    case self::XLS_TYPE_MULBLANK:
                        $this->readMulBlank();
                        break;
                    case self::XLS_TYPE_LABEL:
                        $this->readLabel();
                        break;
                    case self::XLS_TYPE_BLANK:
                        $this->readBlank();
                        break;
                    case self::XLS_TYPE_MSODRAWING:
                        $this->readMsoDrawing();
                        break;
                    case self::XLS_TYPE_OBJ:
                        $this->readObj();
                        break;
                    case self::XLS_TYPE_WINDOW2:
                        $this->readWindow2();
                        break;
                    case self::XLS_TYPE_PAGELAYOUTVIEW:
                        $this->readPageLayoutView();
                        break;
                    case self::XLS_TYPE_SCL:
                        $this->readScl();
                        break;
                    case self::XLS_TYPE_PANE:
                        $this->readPane();
                        break;
                    case self::XLS_TYPE_SELECTION:
                        $this->readSelection();
                        break;
                    case self::XLS_TYPE_MERGEDCELLS:
                        $this->readMergedCells();
                        break;
                    case self::XLS_TYPE_HYPERLINK:
                        $this->readHyperLink();
                        break;
                    case self::XLS_TYPE_DATAVALIDATIONS:
                        $this->readDataValidations();
                        break;
                    case self::XLS_TYPE_DATAVALIDATION:
                        $this->readDataValidation();
                        break;
                    case self::XLS_TYPE_SHEETLAYOUT:
                        $this->readSheetLayout();
                        break;
                    case self::XLS_TYPE_SHEETPROTECTION:
                        $this->readSheetProtection();
                        break;
                    case self::XLS_TYPE_RANGEPROTECTION:
                        $this->readRangeProtection();
                        break;
                    case self::XLS_TYPE_NOTE:
                        $this->readNote();
                        break;
                    //case self::XLS_TYPE_IMDATA:                $this->readImData();                    break;
                    case self::XLS_TYPE_TXO:
                        $this->readTextObject();
                        break;
                    case self::XLS_TYPE_CONTINUE:
                        $this->readContinue();
                        break;
                    case self::XLS_TYPE_EOF:
                        $this->readDefault();
                        break 2;
                    default:
                        $this->readDefault();
                        break;
                }

            }

            // treat MSODRAWING records, sheet-level Escher
            if (!$this->readDataOnly && $this->drawingData) {
                $escherWorksheet = new PHPExcel_Shared_Escher();
                $reader = new PHPExcel_Reader_Excel5_Escher($escherWorksheet);
                $escherWorksheet = $reader->load($this->drawingData);

                // debug Escher stream
                //$debug = new Debug_Escher(new PHPExcel_Shared_Escher());
                //$debug->load($this->drawingData);

                // get all spContainers in one long array, so they can be mapped to OBJ records
                $allSpContainers = $escherWorksheet->getDgContainer()->getSpgrContainer()->getAllSpContainers();
            }

            // treat OBJ records
            foreach ($this->objs as $n => $obj) {
//                echo '<hr /><b>Object</b> reference is ', $n,'<br />';
//                var_dump($obj);
//                echo '<br />';

                // the first shape container never has a corresponding OBJ record, hence $n + 1
                if (isset($allSpContainers[$n + 1]) && is_object($allSpContainers[$n + 1])) {
                    $spContainer = $allSpContainers[$n + 1];

                    // we skip all spContainers that are a part of a group shape since we cannot yet handle those
                    if ($spContainer->getNestingLevel() > 1) {
                        continue;
                    }

                    // calculate the width and height of the shape
                    list($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($spContainer->getStartCoordinates());
                    list($endColumn, $endRow) = PHPExcel_Cell::coordinateFromString($spContainer->getEndCoordinates());

                    $startOffsetX = $spContainer->getStartOffsetX();
                    $startOffsetY = $spContainer->getStartOffsetY();
                    $endOffsetX = $spContainer->getEndOffsetX();
                    $endOffsetY = $spContainer->getEndOffsetY();

                    $width = PHPExcel_Shared_Excel5::getDistanceX($this->phpSheet, $startColumn, $startOffsetX, $endColumn, $endOffsetX);
                    $height = PHPExcel_Shared_Excel5::getDistanceY($this->phpSheet, $startRow, $startOffsetY, $endRow, $endOffsetY);

                    // calculate offsetX and offsetY of the shape
                    $offsetX = $startOffsetX * PHPExcel_Shared_Excel5::sizeCol($this->phpSheet, $startColumn) / 1024;
                    $offsetY = $startOffsetY * PHPExcel_Shared_Excel5::sizeRow($this->phpSheet, $startRow) / 256;

                    switch ($obj['otObjType']) {
                        case 0x19:
                            // Note
//                            echo 'Cell Annotation Object<br />';
//                            echo 'Object ID is ', $obj['idObjID'],'<br />';
                            if (isset($this->cellNotes[$obj['idObjID']])) {
                                $cellNote = $this->cellNotes[$obj['idObjID']];

                                if (isset($this->textObjects[$obj['idObjID']])) {
                                    $textObject = $this->textObjects[$obj['idObjID']];
                                    $this->cellNotes[$obj['idObjID']]['objTextData'] = $textObject;
                                }
                            }
                            break;
                        case 0x08:
//                            echo 'Picture Object<br />';
                            // picture
                            // get index to BSE entry (1-based)
                            $BSEindex = $spContainer->getOPT(0x0104);
                            $BSECollection = $escherWorkbook->getDggContainer()->getBstoreContainer()->getBSECollection();
                            $BSE = $BSECollection[$BSEindex - 1];
                            $blipType = $BSE->getBlipType();

                            // need check because some blip types are not supported by Escher reader such as EMF
                            if ($blip = $BSE->getBlip()) {
                                $ih = imagecreatefromstring($blip->getData());
                                $drawing = new PHPExcel_Worksheet_MemoryDrawing();
                                $drawing->setImageResource($ih);

                                // width, height, offsetX, offsetY
                                $drawing->setResizeProportional(false);
                                $drawing->setWidth($width);
                                $drawing->setHeight($height);
                                $drawing->setOffsetX($offsetX);
                                $drawing->setOffsetY($offsetY);

                                switch ($blipType) {
                                    case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG:
                                        $drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG);
                                        $drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_JPEG);
                                        break;
                                    case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG:
                                        $drawing->setRenderingFunction(PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG);
                                        $drawing->setMimeType(PHPExcel_Worksheet_MemoryDrawing::MIMETYPE_PNG);
                                        break;
                                }

                                $drawing->setWorksheet($this->phpSheet);
                                $drawing->setCoordinates($spContainer->getStartCoordinates());
                            }
                            break;
                        default:
                            // other object type
                            break;
                    }
                }
            }

            // treat SHAREDFMLA records
            if ($this->version == self::XLS_BIFF8) {
                foreach ($this->sharedFormulaParts as $cell => $baseCell) {
                    list($column, $row) = PHPExcel_Cell::coordinateFromString($cell);
                    if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($column, $row, $this->phpSheet->getTitle())) {
                        $formula = $this->getFormulaFromStructure($this->sharedFormulas[$baseCell], $cell);
                        $this->phpSheet->getCell($cell)->setValueExplicit('=' . $formula, PHPExcel_Cell_DataType::TYPE_FORMULA);
                    }
                }
            }

            if (!empty($this->cellNotes)) {
                foreach ($this->cellNotes as $note => $noteDetails) {
                    if (!isset($noteDetails['objTextData'])) {
                        if (isset($this->textObjects[$note])) {
                            $textObject = $this->textObjects[$note];
                            $noteDetails['objTextData'] = $textObject;
                        } else {
                            $noteDetails['objTextData']['text'] = '';
                        }
                    }
//                    echo '<b>Cell annotation ', $note,'</b><br />';
//                    var_dump($noteDetails);
//                    echo '<br />';
                    $cellAddress = str_replace('$', '', $noteDetails['cellRef']);
                    $this->phpSheet->getComment($cellAddress)->setAuthor($noteDetails['author'])->setText($this->parseRichText($noteDetails['objTextData']['text']));
                }
            }
        }

        // add the named ranges (defined names)
        foreach ($this->definedname as $definedName) {
            if ($definedName['isBuiltInName']) {
                switch ($definedName['name']) {
                    case pack('C', 0x06):
                        // print area
                        //    in general, formula looks like this: Foo!$C$7:$J$66,Bar!$A$1:$IV$2
                        $ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?

                        $extractedRanges = array();
                        foreach ($ranges as $range) {
                            // $range should look like one of these
                            //        Foo!$C$7:$J$66
                            //        Bar!$A$1:$IV$2
                            $explodes = explode('!', $range);    // FIXME: what if sheetname contains exclamation mark?
                            $sheetName = trim($explodes[0], "'");
                            if (count($explodes) == 2) {
                                if (strpos($explodes[1], ':') === false) {
                                    $explodes[1] = $explodes[1] . ':' . $explodes[1];
                                }
                                $extractedRanges[] = str_replace('$', '', $explodes[1]); // C7:J66
                            }
                        }
                        if ($docSheet = $this->phpExcel->getSheetByName($sheetName)) {
                            $docSheet->getPageSetup()->setPrintArea(implode(',', $extractedRanges)); // C7:J66,A1:IV2
                        }
                        break;
                    case pack('C', 0x07):
                        // print titles (repeating rows)
                        // Assuming BIFF8, there are 3 cases
                        // 1. repeating rows
                        //        formula looks like this: Sheet!$A$1:$IV$2
                        //        rows 1-2 repeat
                        // 2. repeating columns
                        //        formula looks like this: Sheet!$A$1:$B$65536
                        //        columns A-B repeat
                        // 3. both repeating rows and repeating columns
                        //        formula looks like this: Sheet!$A$1:$B$65536,Sheet!$A$1:$IV$2
                        $ranges = explode(',', $definedName['formula']); // FIXME: what if sheetname contains comma?
                        foreach ($ranges as $range) {
                            // $range should look like this one of these
                            //        Sheet!$A$1:$B$65536
                            //        Sheet!$A$1:$IV$2
                            $explodes = explode('!', $range);
                            if (count($explodes) == 2) {
                                if ($docSheet = $this->phpExcel->getSheetByName($explodes[0])) {
                                    $extractedRange = $explodes[1];
                                    $extractedRange = str_replace('$', '', $extractedRange);

                                    $coordinateStrings = explode(':', $extractedRange);
                                    if (count($coordinateStrings) == 2) {
                                        list($firstColumn, $firstRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[0]);
                                        list($lastColumn, $lastRow) = PHPExcel_Cell::coordinateFromString($coordinateStrings[1]);

                                        if ($firstColumn == 'A' and $lastColumn == 'IV') {
                                            // then we have repeating rows
                                            $docSheet->getPageSetup()->setRowsToRepeatAtTop(array($firstRow, $lastRow));
                                        } elseif ($firstRow == 1 and $lastRow == 65536) {
                                            // then we have repeating columns
                                            $docSheet->getPageSetup()->setColumnsToRepeatAtLeft(array($firstColumn, $lastColumn));
                                        }
                                    }
                                }
                            }
                        }
                        break;
                }
            } else {
                // Extract range
                $explodes = explode('!', $definedName['formula']);

                if (count($explodes) == 2) {
                    if (($docSheet = $this->phpExcel->getSheetByName($explodes[0])) ||
                        ($docSheet = $this->phpExcel->getSheetByName(trim($explodes[0], "'")))) {
                        $extractedRange = $explodes[1];
                        $extractedRange = str_replace('$', '', $extractedRange);

                        $localOnly = ($definedName['scope'] == 0) ? false : true;

                        $scope = ($definedName['scope'] == 0) ? null : $this->phpExcel->getSheetByName($this->sheets[$definedName['scope'] - 1]['name']);

                        $this->phpExcel->addNamedRange(new PHPExcel_NamedRange((string)$definedName['name'], $docSheet, $extractedRange, $localOnly, $scope));
                    }
                } else {
                    //    Named Value
                    //    TODO Provide support for named values
                }
            }
        }
        $this->data = null;

        return $this->phpExcel;
    }
    
    /**
     * Read record data from stream, decrypting as required
     *
     * @param string $data   Data stream to read from
     * @param int    $pos    Position to start reading from
     * @param int    $length Record data length
     *
     * @return string Record data
     */
    private function readRecordData($data, $pos, $len)
    {
        $data = substr($data, $pos, $len);
        
        // File not encrypted, or record before encryption start point
        if ($this->encryption == self::MS_BIFF_CRYPTO_NONE || $pos < $this->encryptionStartPos) {
            return $data;
        }
    
        $recordData = '';
        if ($this->encryption == self::MS_BIFF_CRYPTO_RC4) {
            $oldBlock = floor($this->rc4Pos / self::REKEY_BLOCK);
            $block = floor($pos / self::REKEY_BLOCK);
            $endBlock = floor(($pos + $len) / self::REKEY_BLOCK);

            // Spin an RC4 decryptor to the right spot. If we have a decryptor sitting
            // at a point earlier in the current block, re-use it as we can save some time.
            if ($block != $oldBlock || $pos < $this->rc4Pos || !$this->rc4Key) {
                $this->rc4Key = $this->makeKey($block, $this->md5Ctxt);
                $step = $pos % self::REKEY_BLOCK;
            } else {
                $step = $pos - $this->rc4Pos;
            }
            $this->rc4Key->RC4(str_repeat("\0", $step));

            // Decrypt record data (re-keying at the end of every block)
            while ($block != $endBlock) {
                $step = self::REKEY_BLOCK - ($pos % self::REKEY_BLOCK);
                $recordData .= $this->rc4Key->RC4(substr($data, 0, $step));
                $data = substr($data, $step);
                $pos += $step;
                $len -= $step;
                $block++;
                $this->rc4Key = $this->makeKey($block, $this->md5Ctxt);
            }
            $recordData .= $this->rc4Key->RC4(substr($data, 0, $len));

            // Keep track of the position of this decryptor.
            // We'll try and re-use it later if we can to speed things up
            $this->rc4Pos = $pos + $len;
        } elseif ($this->encryption == self::MS_BIFF_CRYPTO_XOR) {
            throw new PHPExcel_Reader_Exception('XOr encryption not supported');
        }
        return $recordData;
    }

    /**
     * Use OLE reader to extract the relevant data streams from the OLE file
     *
     * @param string $pFilename
     */
    private function loadOLE($pFilename)
    {
        // OLE reader
        $ole = new PHPExcel_Shared_OLERead();
        // get excel data,
        $res = $ole->read($pFilename);
        // Get workbook data: workbook stream + sheet streams
        $this->data = $ole->getStream($ole->wrkbook);
        // Get summary information data
        $this->summaryInformation = $ole->getStream($ole->summaryInformation);
        // Get additional document summary information data
        $this->documentSummaryInformation = $ole->getStream($ole->documentSummaryInformation);
        // Get user-defined property data
//        $this->userDefinedProperties = $ole->getUserDefinedProperties();
    }


    /**
     * Read summary information
     */
    private function readSummaryInformation()
    {
        if (!isset($this->summaryInformation)) {
            return;
        }

        // offset: 0; size: 2; must be 0xFE 0xFF (UTF-16 LE byte order mark)
        // offset: 2; size: 2;
        // offset: 4; size: 2; OS version
        // offset: 6; size: 2; OS indicator
        // offset: 8; size: 16
        // offset: 24; size: 4; section count
        $secCount = self::getInt4d($this->summaryInformation, 24);

        // offset: 28; size: 16; first section's class id: e0 85 9f f2 f9 4f 68 10 ab 91 08 00 2b 27 b3 d9
        // offset: 44; size: 4
        $secOffset = self::getInt4d($this->summaryInformation, 44);

        // section header
        // offset: $secOffset; size: 4; section length
        $secLength = self::getInt4d($this->summaryInformation, $secOffset);

        // offset: $secOffset+4; size: 4; property count
        $countProperties = self::getInt4d($this->summaryInformation, $secOffset+4);

        // initialize code page (used to resolve string values)
        $codePage = 'CP1252';

        // offset: ($secOffset+8); size: var
        // loop through property decarations and properties
        for ($i = 0; $i < $countProperties; ++$i) {
            // offset: ($secOffset+8) + (8 * $i); size: 4; property ID
            $id = self::getInt4d($this->summaryInformation, ($secOffset+8) + (8 * $i));

            // Use value of property id as appropriate
            // offset: ($secOffset+12) + (8 * $i); size: 4; offset from beginning of section (48)
            $offset = self::getInt4d($this->summaryInformation, ($secOffset+12) + (8 * $i));

            $type = self::getInt4d($this->summaryInformation, $secOffset + $offset);

            // initialize property value
            $value = null;

            // extract property value based on property type
            switch ($type) {
                case 0x02: // 2 byte signed integer
                    $value = self::getInt2d($this->summaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x03: // 4 byte signed integer
                    $value = self::getInt4d($this->summaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x13: // 4 byte unsigned integer
                    // not needed yet, fix later if necessary
                    break;
                case 0x1E: // null-terminated string prepended by dword string length
                    $byteLength = self::getInt4d($this->summaryInformation, $secOffset + 4 + $offset);
                    $value = substr($this->summaryInformation, $secOffset + 8 + $offset, $byteLength);
                    $value = PHPExcel_Shared_String::ConvertEncoding($value, 'UTF-8', $codePage);
                    $value = rtrim($value);
                    break;
                case 0x40: // Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                    // PHP-time
                    $value = PHPExcel_Shared_OLE::OLE2LocalDate(substr($this->summaryInformation, $secOffset + 4 + $offset, 8));
                    break;
                case 0x47: // Clipboard format
                    // not needed yet, fix later if necessary
                    break;
            }

            switch ($id) {
                case 0x01:    //    Code Page
                    $codePage = PHPExcel_Shared_CodePage::NumberToName($value);
                    break;
                case 0x02:    //    Title
                    $this->phpExcel->getProperties()->setTitle($value);
                    break;
                case 0x03:    //    Subject
                    $this->phpExcel->getProperties()->setSubject($value);
                    break;
                case 0x04:    //    Author (Creator)
                    $this->phpExcel->getProperties()->setCreator($value);
                    break;
                case 0x05:    //    Keywords
                    $this->phpExcel->getProperties()->setKeywords($value);
                    break;
                case 0x06:    //    Comments (Description)
                    $this->phpExcel->getProperties()->setDescription($value);
                    break;
                case 0x07:    //    Template
                    //    Not supported by PHPExcel
                    break;
                case 0x08:    //    Last Saved By (LastModifiedBy)
                    $this->phpExcel->getProperties()->setLastModifiedBy($value);
                    break;
                case 0x09:    //    Revision
                    //    Not supported by PHPExcel
                    break;
                case 0x0A:    //    Total Editing Time
                    //    Not supported by PHPExcel
                    break;
                case 0x0B:    //    Last Printed
                    //    Not supported by PHPExcel
                    break;
                case 0x0C:    //    Created Date/Time
                    $this->phpExcel->getProperties()->setCreated($value);
                    break;
                case 0x0D:    //    Modified Date/Time
                    $this->phpExcel->getProperties()->setModified($value);
                    break;
                case 0x0E:    //    Number of Pages
                    //    Not supported by PHPExcel
                    break;
                case 0x0F:    //    Number of Words
                    //    Not supported by PHPExcel
                    break;
                case 0x10:    //    Number of Characters
                    //    Not supported by PHPExcel
                    break;
                case 0x11:    //    Thumbnail
                    //    Not supported by PHPExcel
                    break;
                case 0x12:    //    Name of creating application
                    //    Not supported by PHPExcel
                    break;
                case 0x13:    //    Security
                    //    Not supported by PHPExcel
                    break;
            }
        }
    }


    /**
     * Read additional document summary information
     */
    private function readDocumentSummaryInformation()
    {
        if (!isset($this->documentSummaryInformation)) {
            return;
        }

        //    offset: 0;    size: 2;    must be 0xFE 0xFF (UTF-16 LE byte order mark)
        //    offset: 2;    size: 2;
        //    offset: 4;    size: 2;    OS version
        //    offset: 6;    size: 2;    OS indicator
        //    offset: 8;    size: 16
        //    offset: 24;    size: 4;    section count
        $secCount = self::getInt4d($this->documentSummaryInformation, 24);
//        echo '$secCount = ', $secCount,'<br />';

        // offset: 28;    size: 16;    first section's class id: 02 d5 cd d5 9c 2e 1b 10 93 97 08 00 2b 2c f9 ae
        // offset: 44;    size: 4;    first section offset
        $secOffset = self::getInt4d($this->documentSummaryInformation, 44);
//        echo '$secOffset = ', $secOffset,'<br />';

        //    section header
        //    offset: $secOffset;    size: 4;    section length
        $secLength = self::getInt4d($this->documentSummaryInformation, $secOffset);
//        echo '$secLength = ', $secLength,'<br />';

        //    offset: $secOffset+4;    size: 4;    property count
        $countProperties = self::getInt4d($this->documentSummaryInformation, $secOffset+4);
//        echo '$countProperties = ', $countProperties,'<br />';

        // initialize code page (used to resolve string values)
        $codePage = 'CP1252';

        //    offset: ($secOffset+8);    size: var
        //    loop through property decarations and properties
        for ($i = 0; $i < $countProperties; ++$i) {
//            echo 'Property ', $i,'<br />';
            //    offset: ($secOffset+8) + (8 * $i);    size: 4;    property ID
            $id = self::getInt4d($this->documentSummaryInformation, ($secOffset+8) + (8 * $i));
//            echo 'ID is ', $id,'<br />';

            // Use value of property id as appropriate
            // offset: 60 + 8 * $i;    size: 4;    offset from beginning of section (48)
            $offset = self::getInt4d($this->documentSummaryInformation, ($secOffset+12) + (8 * $i));

            $type = self::getInt4d($this->documentSummaryInformation, $secOffset + $offset);
//            echo 'Type is ', $type,', ';

            // initialize property value
            $value = null;

            // extract property value based on property type
            switch ($type) {
                case 0x02:    //    2 byte signed integer
                    $value = self::getInt2d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x03:    //    4 byte signed integer
                    $value = self::getInt4d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    break;
                case 0x0B:  // Boolean
                    $value = self::getInt2d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    $value = ($value == 0 ? false : true);
                    break;
                case 0x13:    //    4 byte unsigned integer
                    // not needed yet, fix later if necessary
                    break;
                case 0x1E:    //    null-terminated string prepended by dword string length
                    $byteLength = self::getInt4d($this->documentSummaryInformation, $secOffset + 4 + $offset);
                    $value = substr($this->documentSummaryInformation, $secOffset + 8 + $offset, $byteLength);
                    $value = PHPExcel_Shared_String::ConvertEncoding($value, 'UTF-8', $codePage);
                    $value = rtrim($value);
                    break;
                case 0x40:    //    Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                    // PHP-Time
                    $value = PHPExcel_Shared_OLE::OLE2LocalDate(substr($this->documentSummaryInformation, $secOffset + 4 + $offset, 8));
                    break;
                case 0x47:    //    Clipboard format
                    // not needed yet, fix later if necessary
                    break;
            }

            switch ($id) {
                case 0x01:    //    Code Page
                    $codePage = PHPExcel_Shared_CodePage::NumberToName($value);
                    break;
                case 0x02:    //    Category
                    $this->phpExcel->getProperties()->setCategory($value);
                    break;
                case 0x03:    //    Presentation Target
                    //    Not supported by PHPExcel
                    break;
                case 0x04:    //    Bytes
                    //    Not supported by PHPExcel
                    break;
                case 0x05:    //    Lines
                    //    Not supported by PHPExcel
                    break;
                case 0x06:    //    Paragraphs
                    //    Not supported by PHPExcel
                    break;
                case 0x07:    //    Slides
                    //    Not supported by PHPExcel
                    break;
                case 0x08:    //    Notes
                    //    Not supported by PHPExcel
                    break;
                case 0x09:    //    Hidden Slides
                    //    Not supported by PHPExcel
                    break;
                case 0x0A:    //    MM Clips
                    //    Not supported by PHPExcel
                    break;
                case 0x0B:    //    Scale Crop
                    //    Not supported by PHPExcel
                    break;
                case 0x0C:    //    Heading Pairs
                    //    Not supported by PHPExcel
                    break;
                case 0x0D:    //    Titles of Parts
                    //    Not supported by PHPExcel
                    break;
                case 0x0E:    //    Manager
                    $this->phpExcel->getProperties()->setManager($value);
                    break;
                case 0x0F:    //    Company
                    $this->phpExcel->getProperties()->setCompany($value);
                    break;
                case 0x10:    //    Links up-to-date
                    //    Not supported by PHPExcel
                    break;
            }
        }
    }


    /**
     * Reads a general type of BIFF record. Does nothing except for moving stream pointer forward to next record.
     */
    private function readDefault()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
//        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;
    }


    /**
     *    The NOTE record specifies a comment associated with a particular cell. In Excel 95 (BIFF7) and earlier versions,
     *        this record stores a note (cell note). This feature was significantly enhanced in Excel 97.
     */
    private function readNote()
    {
//        echo '<b>Read Cell Annotation</b><br />';
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        $cellAddress = $this->readBIFF8CellAddress(substr($recordData, 0, 4));
        if ($this->version == self::XLS_BIFF8) {
            $noteObjID = self::getInt2d($recordData, 6);
            $noteAuthor = self::readUnicodeStringLong(substr($recordData, 8));
            $noteAuthor = $noteAuthor['value'];
//            echo 'Note Address=', $cellAddress,'<br />';
//            echo 'Note Object ID=', $noteObjID,'<br />';
//            echo 'Note Author=', $noteAuthor,'<hr />';
//
            $this->cellNotes[$noteObjID] = array(
                'cellRef'   => $cellAddress,
                'objectID'  => $noteObjID,
                'author'    => $noteAuthor
            );
        } else {
            $extension = false;
            if ($cellAddress == '$B$65536') {
                //    If the address row is -1 and the column is 0, (which translates as $B$65536) then this is a continuation
                //        note from the previous cell annotation. We're not yet handling this, so annotations longer than the
                //        max 2048 bytes will probably throw a wobbly.
                $row = self::getInt2d($recordData, 0);
                $extension = true;
                $cellAddress = array_pop(array_keys($this->phpSheet->getComments()));
            }
//            echo 'Note Address=', $cellAddress,'<br />';

            $cellAddress = str_replace('$', '', $cellAddress);
            $noteLength = self::getInt2d($recordData, 4);
            $noteText = trim(substr($recordData, 6));
//            echo 'Note Length=', $noteLength,'<br />';
//            echo 'Note Text=', $noteText,'<br />';

            if ($extension) {
                //    Concatenate this extension with the currently set comment for the cell
                $comment = $this->phpSheet->getComment($cellAddress);
                $commentText = $comment->getText()->getPlainText();
                $comment->setText($this->parseRichText($commentText.$noteText));
            } else {
                //    Set comment for the cell
                $this->phpSheet->getComment($cellAddress)->setText($this->parseRichText($noteText));
//                                                    ->setAuthor($author)
            }
        }

    }


    /**
     *    The TEXT Object record contains the text associated with a cell annotation.
     */
    private function readTextObject()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // recordData consists of an array of subrecords looking like this:
        //    grbit: 2 bytes; Option Flags
        //    rot: 2 bytes; rotation
        //    cchText: 2 bytes; length of the text (in the first continue record)
        //    cbRuns: 2 bytes; length of the formatting (in the second continue record)
        // followed by the continuation records containing the actual text and formatting
        $grbitOpts  = self::getInt2d($recordData, 0);
        $rot        = self::getInt2d($recordData, 2);
        $cchText    = self::getInt2d($recordData, 10);
        $cbRuns     = self::getInt2d($recordData, 12);
        $text       = $this->getSplicedRecordData();

        $this->textObjects[$this->textObjRef] = array(
            'text'      => substr($text["recordData"], $text["spliceOffsets"][0]+1, $cchText),
            'format'    => substr($text["recordData"], $text["spliceOffsets"][1], $cbRuns),
            'alignment' => $grbitOpts,
            'rotation'  => $rot
        );

//        echo '<b>_readTextObject()</b><br />';
//        var_dump($this->textObjects[$this->textObjRef]);
//        echo '<br />';
    }


    /**
     * Read BOF
     */
    private function readBof()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = substr($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 2; size: 2; type of the following data
        $substreamType = self::getInt2d($recordData, 2);

        switch ($substreamType) {
            case self::XLS_WorkbookGlobals:
                $version = self::getInt2d($recordData, 0);
                if (($version != self::XLS_BIFF8) && ($version != self::XLS_BIFF7)) {
                    throw new PHPExcel_Reader_Exception('Cannot read this Excel file. Version is too old.');
                }
                $this->version = $version;
                break;
            case self::XLS_Worksheet:
                // do not use this version information for anything
                // it is unreliable (OpenOffice doc, 5.8), use only version information from the global stream
                break;
            default:
                // substream, e.g. chart
                // just skip the entire substream
                do {
                    $code = self::getInt2d($this->data, $this->pos);
                    $this->readDefault();
                } while ($code != self::XLS_TYPE_EOF && $this->pos < $this->dataSize);
                break;
        }
    }


    /**
     * FILEPASS
     *
     * This record is part of the File Protection Block. It
     * contains information about the read/write password of the
     * file. All record contents following this record will be
     * encrypted.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     *
     * The decryption functions and objects used from here on in
     * are based on the source of Spreadsheet-ParseExcel:
     * http://search.cpan.org/~jmcnamara/Spreadsheet-ParseExcel/
     */
    private function readFilepass()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);

        if ($length != 54) {
            throw new PHPExcel_Reader_Exception('Unexpected file pass record length');
        }
        
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);
        
        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->verifyPassword('VelvetSweatshop', substr($recordData, 6, 16), substr($recordData, 22, 16), substr($recordData, 38, 16), $this->md5Ctxt)) {
            throw new PHPExcel_Reader_Exception('Decryption password incorrect');
        }
        
        $this->encryption = self::MS_BIFF_CRYPTO_RC4;

        // Decryption required from the record after next onwards
        $this->encryptionStartPos = $this->pos + self::getInt2d($this->data, $this->pos + 2);
    }

    /**
     * Make an RC4 decryptor for the given block
     *
     * @var int    $block      Block for which to create decrypto
     * @var string $valContext MD5 context state
     *
     * @return PHPExcel_Reader_Excel5_RC4
     */
    private function makeKey($block, $valContext)
    {
        $pwarray = str_repeat("\0", 64);

        for ($i = 0; $i < 5; $i++) {
            $pwarray[$i] = $valContext[$i];
        }
        
        $pwarray[5] = chr($block & 0xff);
        $pwarray[6] = chr(($block >> 8) & 0xff);
        $pwarray[7] = chr(($block >> 16) & 0xff);
        $pwarray[8] = chr(($block >> 24) & 0xff);

        $pwarray[9] = "\x80";
        $pwarray[56] = "\x48";

        $md5 = new PHPExcel_Reader_Excel5_MD5();
        $md5->add($pwarray);

        $s = $md5->getContext();
        return new PHPExcel_Reader_Excel5_RC4($s);
    }

    /**
     * Verify RC4 file password
     *
     * @var string $password        Password to check
     * @var string $docid           Document id
     * @var string $salt_data       Salt data
     * @var string $hashedsalt_data Hashed salt data
     * @var string &$valContext     Set to the MD5 context of the value
     *
     * @return bool Success
     */
    private function verifyPassword($password, $docid, $salt_data, $hashedsalt_data, &$valContext)
    {
        $pwarray = str_repeat("\0", 64);

        for ($i = 0; $i < strlen($password); $i++) {
            $o = ord(substr($password, $i, 1));
            $pwarray[2 * $i] = chr($o & 0xff);
            $pwarray[2 * $i + 1] = chr(($o >> 8) & 0xff);
        }
        $pwarray[2 * $i] = chr(0x80);
        $pwarray[56] = chr(($i << 4) & 0xff);

        $md5 = new PHPExcel_Reader_Excel5_MD5();
        $md5->add($pwarray);

        $mdContext1 = $md5->getContext();

        $offset = 0;
        $keyoffset = 0;
        $tocopy = 5;

        $md5->reset();

        while ($offset != 16) {
            if ((64 - $offset) < 5) {
                $tocopy = 64 - $offset;
            }
            for ($i = 0; $i <= $tocopy; $i++) {
                $pwarray[$offset + $i] = $mdContext1[$keyoffset + $i];
            }
            $offset += $tocopy;

            if ($offset == 64) {
                $md5->add($pwarray);
                $keyoffset = $tocopy;
                $tocopy = 5 - $tocopy;
                $offset = 0;
                continue;
            }

            $keyoffset = 0;
            $tocopy = 5;
            for ($i = 0; $i < 16; $i++) {
                $pwarray[$offset + $i] = $docid[$i];
            }
            $offset += 16;
        }

        $pwarray[16] = "\x80";
        for ($i = 0; $i < 47; $i++) {
            $pwarray[17 + $i] = "\0";
        }
        $pwarray[56] = "\x80";
        $pwarray[57] = "\x0a";

        $md5->add($pwarray);
        $valContext = $md5->getContext();

        $key = $this->makeKey(0, $valContext);

        $salt = $key->RC4($salt_data);
        $hashedsalt = $key->RC4($hashedsalt_data);
        
        $salt .= "\x80" . str_repeat("\0", 47);
        $salt[56] = "\x80";

        $md5->reset();
        $md5->add($salt);
        $mdContext2 = $md5->getContext();

        return $mdContext2 == $hashedsalt;
    }

    /**
     * CODEPAGE
     *
     * This record stores the text encoding used to write byte
     * strings, stored as MS Windows code page identifier.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readCodepage()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; code page identifier
        $codepage = self::getInt2d($recordData, 0);

        $this->codepage = PHPExcel_Shared_CodePage::NumberToName($codepage);
    }


    /**
     * DATEMODE
     *
     * This record specifies the base date for displaying date
     * values. All dates are stored as count of days past this
     * base date. In BIFF2-BIFF4 this record is part of the
     * Calculation Settings Block. In BIFF5-BIFF8 it is
     * stored in the Workbook Globals Substream.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readDateMode()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; 0 = base 1900, 1 = base 1904
        PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_WINDOWS_1900);
        if (ord($recordData{0}) == 1) {
            PHPExcel_Shared_Date::setExcelCalendar(PHPExcel_Shared_Date::CALENDAR_MAC_1904);
        }
    }


    /**
     * Read a FONT record
     */
    private function readFont()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            $objFont = new PHPExcel_Style_Font();

            // offset: 0; size: 2; height of the font (in twips = 1/20 of a point)
            $size = self::getInt2d($recordData, 0);
            $objFont->setSize($size / 20);

            // offset: 2; size: 2; option flags
            // bit: 0; mask 0x0001; bold (redundant in BIFF5-BIFF8)
            // bit: 1; mask 0x0002; italic
            $isItalic = (0x0002 & self::getInt2d($recordData, 2)) >> 1;
            if ($isItalic) {
                $objFont->setItalic(true);
            }

            // bit: 2; mask 0x0004; underlined (redundant in BIFF5-BIFF8)
            // bit: 3; mask 0x0008; strike
            $isStrike = (0x0008 & self::getInt2d($recordData, 2)) >> 3;
            if ($isStrike) {
                $objFont->setStrikethrough(true);
            }

            // offset: 4; size: 2; colour index
            $colorIndex = self::getInt2d($recordData, 4);
            $objFont->colorIndex = $colorIndex;

            // offset: 6; size: 2; font weight
            $weight = self::getInt2d($recordData, 6);
            switch ($weight) {
                case 0x02BC:
                    $objFont->setBold(true);
                    break;
            }

            // offset: 8; size: 2; escapement type
            $escapement = self::getInt2d($recordData, 8);
            switch ($escapement) {
                case 0x0001:
                    $objFont->setSuperScript(true);
                    break;
                case 0x0002:
                    $objFont->setSubScript(true);
                    break;
            }

            // offset: 10; size: 1; underline type
            $underlineType = ord($recordData{10});
            switch ($underlineType) {
                case 0x00:
                    break; // no underline
                case 0x01:
                    $objFont->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLE);
                    break;
                case 0x02:
                    $objFont->setUnderline(PHPExcel_Style_Font::UNDERLINE_DOUBLE);
                    break;
                case 0x21:
                    $objFont->setUnderline(PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING);
                    break;
                case 0x22:
                    $objFont->setUnderline(PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING);
                    break;
            }

            // offset: 11; size: 1; font family
            // offset: 12; size: 1; character set
            // offset: 13; size: 1; not used
            // offset: 14; size: var; font name
            if ($this->version == self::XLS_BIFF8) {
                $string = self::readUnicodeStringShort(substr($recordData, 14));
            } else {
                $string = $this->readByteStringShort(substr($recordData, 14));
            }
            $objFont->setName($string['value']);

            $this->objFonts[] = $objFont;
        }
    }


    /**
     * FORMAT
     *
     * This record contains information about a number format.
     * All FORMAT records occur together in a sequential list.
     *
     * In BIFF2-BIFF4 other records referencing a FORMAT record
     * contain a zero-based index into this list. From BIFF5 on
     * the FORMAT record contains the index itself that will be
     * used by other records.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readFormat()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            $indexCode = self::getInt2d($recordData, 0);

            if ($this->version == self::XLS_BIFF8) {
                $string = self::readUnicodeStringLong(substr($recordData, 2));
            } else {
                // BIFF7
                $string = $this->readByteStringShort(substr($recordData, 2));
            }

            $formatString = $string['value'];
            $this->formats[$indexCode] = $formatString;
        }
    }


    /**
     * XF - Extended Format
     *
     * This record contains formatting information for cells, rows, columns or styles.
     * According to http://support.microsoft.com/kb/147732 there are always at least 15 cell style XF
     * and 1 cell XF.
     * Inspection of Excel files generated by MS Office Excel shows that XF records 0-14 are cell style XF
     * and XF record 15 is a cell XF
     * We only read the first cell style XF and skip the remaining cell style XF records
     * We read all cell XF records.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readXf()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        $objStyle = new PHPExcel_Style();

        if (!$this->readDataOnly) {
            // offset:  0; size: 2; Index to FONT record
            if (self::getInt2d($recordData, 0) < 4) {
                $fontIndex = self::getInt2d($recordData, 0);
            } else {
                // this has to do with that index 4 is omitted in all BIFF versions for some strange reason
                // check the OpenOffice documentation of the FONT record
                $fontIndex = self::getInt2d($recordData, 0) - 1;
            }
            $objStyle->setFont($this->objFonts[$fontIndex]);

            // offset:  2; size: 2; Index to FORMAT record
            $numberFormatIndex = self::getInt2d($recordData, 2);
            if (isset($this->formats[$numberFormatIndex])) {
                // then we have user-defined format code
                $numberformat = array('code' => $this->formats[$numberFormatIndex]);
            } elseif (($code = PHPExcel_Style_NumberFormat::builtInFormatCode($numberFormatIndex)) !== '') {
                // then we have built-in format code
                $numberformat = array('code' => $code);
            } else {
                // we set the general format code
                $numberformat = array('code' => 'General');
            }
            $objStyle->getNumberFormat()->setFormatCode($numberformat['code']);

            // offset:  4; size: 2; XF type, cell protection, and parent style XF
            // bit 2-0; mask 0x0007; XF_TYPE_PROT
            $xfTypeProt = self::getInt2d($recordData, 4);
            // bit 0; mask 0x01; 1 = cell is locked
            $isLocked = (0x01 & $xfTypeProt) >> 0;
            $objStyle->getProtection()->setLocked($isLocked ? PHPExcel_Style_Protection::PROTECTION_INHERIT : PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

            // bit 1; mask 0x02; 1 = Formula is hidden
            $isHidden = (0x02 & $xfTypeProt) >> 1;
            $objStyle->getProtection()->setHidden($isHidden ? PHPExcel_Style_Protection::PROTECTION_PROTECTED : PHPExcel_Style_Protection::PROTECTION_UNPROTECTED);

            // bit 2; mask 0x04; 0 = Cell XF, 1 = Cell Style XF
            $isCellStyleXf = (0x04 & $xfTypeProt) >> 2;

            // offset:  6; size: 1; Alignment and text break
            // bit 2-0, mask 0x07; horizontal alignment
            $horAlign = (0x07 & ord($recordData{6})) >> 0;
            switch ($horAlign) {
                case 0:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_GENERAL);
                    break;
                case 1:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
                    break;
                case 2:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
                    break;
                case 3:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
                    break;
                case 4:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_FILL);
                    break;
                case 5:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY);
                    break;
                case 6:
                    $objStyle->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS);
                    break;
            }
            // bit 3, mask 0x08; wrap text
            $wrapText = (0x08 & ord($recordData{6})) >> 3;
            switch ($wrapText) {
                case 0:
                    $objStyle->getAlignment()->setWrapText(false);
                    break;
                case 1:
                    $objStyle->getAlignment()->setWrapText(true);
                    break;
            }
            // bit 6-4, mask 0x70; vertical alignment
            $vertAlign = (0x70 & ord($recordData{6})) >> 4;
            switch ($vertAlign) {
                case 0:
                    $objStyle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_TOP);
                    break;
                case 1:
                    $objStyle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
                    break;
                case 2:
                    $objStyle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_BOTTOM);
                    break;
                case 3:
                    $objStyle->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_JUSTIFY);
                    break;
            }

            if ($this->version == self::XLS_BIFF8) {
                // offset:  7; size: 1; XF_ROTATION: Text rotation angle
                $angle = ord($recordData{7});
                $rotation = 0;
                if ($angle <= 90) {
                    $rotation = $angle;
                } elseif ($angle <= 180) {
                    $rotation = 90 - $angle;
                } elseif ($angle == 255) {
                    $rotation = -165;
                }
                $objStyle->getAlignment()->setTextRotation($rotation);

                // offset:  8; size: 1; Indentation, shrink to cell size, and text direction
                // bit: 3-0; mask: 0x0F; indent level
                $indent = (0x0F & ord($recordData{8})) >> 0;
                $objStyle->getAlignment()->setIndent($indent);

                // bit: 4; mask: 0x10; 1 = shrink content to fit into cell
                $shrinkToFit = (0x10 & ord($recordData{8})) >> 4;
                switch ($shrinkToFit) {
                    case 0:
                        $objStyle->getAlignment()->setShrinkToFit(false);
                        break;
                    case 1:
                        $objStyle->getAlignment()->setShrinkToFit(true);
                        break;
                }

                // offset:  9; size: 1; Flags used for attribute groups

                // offset: 10; size: 4; Cell border lines and background area
                // bit: 3-0; mask: 0x0000000F; left style
                if ($bordersLeftStyle = PHPExcel_Reader_Excel5_Style_Border::lookup((0x0000000F & self::getInt4d($recordData, 10)) >> 0)) {
                    $objStyle->getBorders()->getLeft()->setBorderStyle($bordersLeftStyle);
                }
                // bit: 7-4; mask: 0x000000F0; right style
                if ($bordersRightStyle = PHPExcel_Reader_Excel5_Style_Border::lookup((0x000000F0 & self::getInt4d($recordData, 10)) >> 4)) {
                    $objStyle->getBorders()->getRight()->setBorderStyle($bordersRightStyle);
                }
                // bit: 11-8; mask: 0x00000F00; top style
                if ($bordersTopStyle = PHPExcel_Reader_Excel5_Style_Border::lookup((0x00000F00 & self::getInt4d($recordData, 10)) >> 8)) {
                    $objStyle->getBorders()->getTop()->setBorderStyle($bordersTopStyle);
                }
                // bit: 15-12; mask: 0x0000F000; bottom style
                if ($bordersBottomStyle = PHPExcel_Reader_Excel5_Style_Border::lookup((0x0000F000 & self::getInt4d($recordData, 10)) >> 12)) {
                    $objStyle->getBorders()->getBottom()->setBorderStyle($bordersBottomStyle);
                }
                // bit: 22-16; mask: 0x007F0000; left color
                $objStyle->getBorders()->getLeft()->colorIndex = (0x007F0000 & self::getInt4d($recordData, 10)) >> 16;

                // bit: 29-23; mask: 0x3F800000; right color
                $objStyle->getBorders()->getRight()->colorIndex = (0x3F800000 & self::getInt4d($recordData, 10)) >> 23;

                // bit: 30; mask: 0x40000000; 1 = diagonal line from top left to right bottom
                $diagonalDown = (0x40000000 & self::getInt4d($recordData, 10)) >> 30 ? true : false;

                // bit: 31; mask: 0x80000000; 1 = diagonal line from bottom left to top right
                $diagonalUp = (0x80000000 & self::getInt4d($recordData, 10)) >> 31 ? true : false;

                if ($diagonalUp == false && $diagonalDown == false) {
                    $objStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_NONE);
                } elseif ($diagonalUp == true && $diagonalDown == false) {
                    $objStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_UP);
                } elseif ($diagonalUp == false && $diagonalDown == true) {
                    $objStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_DOWN);
                } elseif ($diagonalUp == true && $diagonalDown == true) {
                    $objStyle->getBorders()->setDiagonalDirection(PHPExcel_Style_Borders::DIAGONAL_BOTH);
                }

                // offset: 14; size: 4;
                // bit: 6-0; mask: 0x0000007F; top color
                $objStyle->getBorders()->getTop()->colorIndex = (0x0000007F & self::getInt4d($recordData, 14)) >> 0;

                // bit: 13-7; mask: 0x00003F80; bottom color
                $objStyle->getBorders()->getBottom()->colorIndex = (0x00003F80 & self::getInt4d($recordData, 14)) >> 7;

                // bit: 20-14; mask: 0x001FC000; diagonal color
                $objStyle->getBorders()->getDiagonal()->colorIndex = (0x001FC000 & self::getInt4d($recordData, 14)) >> 14;

                // bit: 24-21; mask: 0x01E00000; diagonal style
                if ($bordersDiagonalStyle = PHPExcel_Reader_Excel5_Style_Border::lookup((0x01E00000 & self::getInt4d($recordData, 14)) >> 21)) {
                    $objStyle->getBorders()->getDiagonal()->setBorderStyle($bordersDiagonalStyle);
                }

                // bit: 31-26; mask: 0xFC000000 fill pattern
                if ($fillType = PHPExcel_Reader_Excel5_Style_FillPattern::lookup((0xFC000000 & self::getInt4d($recordData, 14)) >> 26)) {
                    $objStyle->getFill()->setFillType($fillType);
                }
                // offset: 18; size: 2; pattern and background colour
                // bit: 6-0; mask: 0x007F; color index for pattern color
                $objStyle->getFill()->startcolorIndex = (0x007F & self::getInt2d($recordData, 18)) >> 0;

                // bit: 13-7; mask: 0x3F80; color index for pattern background
                $objStyle->getFill()->endcolorIndex = (0x3F80 & self::getInt2d($recordData, 18)) >> 7;
            } else {
                // BIFF5

                // offset: 7; size: 1; Text orientation and flags
                $orientationAndFlags = ord($recordData{7});

                // bit: 1-0; mask: 0x03; XF_ORIENTATION: Text orientation
                $xfOrientation = (0x03 & $orientationAndFlags) >> 0;
                switch ($xfOrientation) {
                    case 0:
                        $objStyle->getAlignment()->setTextRotation(0);
                        break;
                    case 1:
                        $objStyle->getAlignment()->setTextRotation(-165);
                        break;
                    case 2:
                        $objStyle->getAlignment()->setTextRotation(90);
                        break;
                    case 3:
                        $objStyle->getAlignment()->setTextRotation(-90);
                        break;
                }

                // offset: 8; size: 4; cell border lines and background area
                $borderAndBackground = self::getInt4d($recordData, 8);

                // bit: 6-0; mask: 0x0000007F; color index for pattern color
                $objStyle->getFill()->startcolorIndex = (0x0000007F & $borderAndBackground) >> 0;

                // bit: 13-7; mask: 0x00003F80; color index for pattern background
                $objStyle->getFill()->endcolorIndex = (0x00003F80 & $borderAndBackground) >> 7;

                // bit: 21-16; mask: 0x003F0000; fill pattern
                $objStyle->getFill()->setFillType(PHPExcel_Reader_Excel5_Style_FillPattern::lookup((0x003F0000 & $borderAndBackground) >> 16));

                // bit: 24-22; mask: 0x01C00000; bottom line style
                $objStyle->getBorders()->getBottom()->setBorderStyle(PHPExcel_Reader_Excel5_Style_Border::lookup((0x01C00000 & $borderAndBackground) >> 22));

                // bit: 31-25; mask: 0xFE000000; bottom line color
                $objStyle->getBorders()->getBottom()->colorIndex = (0xFE000000 & $borderAndBackground) >> 25;

                // offset: 12; size: 4; cell border lines
                $borderLines = self::getInt4d($recordData, 12);

                // bit: 2-0; mask: 0x00000007; top line style
                $objStyle->getBorders()->getTop()->setBorderStyle(PHPExcel_Reader_Excel5_Style_Border::lookup((0x00000007 & $borderLines) >> 0));

                // bit: 5-3; mask: 0x00000038; left line style
                $objStyle->getBorders()->getLeft()->setBorderStyle(PHPExcel_Reader_Excel5_Style_Border::lookup((0x00000038 & $borderLines) >> 3));

                // bit: 8-6; mask: 0x000001C0; right line style
                $objStyle->getBorders()->getRight()->setBorderStyle(PHPExcel_Reader_Excel5_Style_Border::lookup((0x000001C0 & $borderLines) >> 6));

                // bit: 15-9; mask: 0x0000FE00; top line color index
                $objStyle->getBorders()->getTop()->colorIndex = (0x0000FE00 & $borderLines) >> 9;

                // bit: 22-16; mask: 0x007F0000; left line color index
                $objStyle->getBorders()->getLeft()->colorIndex = (0x007F0000 & $borderLines) >> 16;

                // bit: 29-23; mask: 0x3F800000; right line color index
                $objStyle->getBorders()->getRight()->colorIndex = (0x3F800000 & $borderLines) >> 23;
            }

            // add cellStyleXf or cellXf and update mapping
            if ($isCellStyleXf) {
                // we only read one style XF record which is always the first
                if ($this->xfIndex == 0) {
                    $this->phpExcel->addCellStyleXf($objStyle);
                    $this->mapCellStyleXfIndex[$this->xfIndex] = 0;
                }
            } else {
                // we read all cell XF records
                $this->phpExcel->addCellXf($objStyle);
                $this->mapCellXfIndex[$this->xfIndex] = count($this->phpExcel->getCellXfCollection()) - 1;
            }

            // update XF index for when we read next record
            ++$this->xfIndex;
        }
    }


    /**
     *
     */
    private function readXfExt()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; 0x087D = repeated header

            // offset: 2; size: 2

            // offset: 4; size: 8; not used

            // offset: 12; size: 2; record version

            // offset: 14; size: 2; index to XF record which this record modifies
            $ixfe = self::getInt2d($recordData, 14);

            // offset: 16; size: 2; not used

            // offset: 18; size: 2; number of extension properties that follow
            $cexts = self::getInt2d($recordData, 18);

            // start reading the actual extension data
            $offset = 20;
            while ($offset < $length) {
                // extension type
                $extType = self::getInt2d($recordData, $offset);

                // extension length
                $cb = self::getInt2d($recordData, $offset + 2);

                // extension data
                $extData = substr($recordData, $offset + 4, $cb);

                switch ($extType) {
                    case 4:        // fill start color
                        $xclfType  = self::getInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $fill = $this->phpExcel->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getFill();
                                $fill->getStartColor()->setRGB($rgb);
                                unset($fill->startcolorIndex); // normal color index does not apply, discard
                            }
                        }
                        break;
                    case 5:        // fill end color
                        $xclfType  = self::getInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $fill = $this->phpExcel->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getFill();
                                $fill->getEndColor()->setRGB($rgb);
                                unset($fill->endcolorIndex); // normal color index does not apply, discard
                            }
                        }
                        break;
                    case 7:        // border color top
                        $xclfType  = self::getInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $top = $this->phpExcel->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getTop();
                                $top->getColor()->setRGB($rgb);
                                unset($top->colorIndex); // normal color index does not apply, discard
                            }
                        }
                        break;
                    case 8:        // border color bottom
                        $xclfType  = self::getInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $bottom = $this->phpExcel->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getBottom();
                                $bottom->getColor()->setRGB($rgb);
                                unset($bottom->colorIndex); // normal color index does not apply, discard
                            }
                        }
                        break;
                    case 9:        // border color left
                        $xclfType  = self::getInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $left = $this->phpExcel->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getLeft();
                                $left->getColor()->setRGB($rgb);
                                unset($left->colorIndex); // normal color index does not apply, discard
                            }
                        }
                        break;
                    case 10:        // border color right
                        $xclfType  = self::getInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $right = $this->phpExcel->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getRight();
                                $right->getColor()->setRGB($rgb);
                                unset($right->colorIndex); // normal color index does not apply, discard
                            }
                        }
                        break;
                    case 11:        // border color diagonal
                        $xclfType  = self::getInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $diagonal = $this->phpExcel->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getBorders()->getDiagonal();
                                $diagonal->getColor()->setRGB($rgb);
                                unset($diagonal->colorIndex); // normal color index does not apply, discard
                            }
                        }
                        break;
                    case 13:    // font color
                        $xclfType  = self::getInt2d($extData, 0); // color type
                        $xclrValue = substr($extData, 4, 4); // color value (value based on color type)

                        if ($xclfType == 2) {
                            $rgb = sprintf('%02X%02X%02X', ord($xclrValue{0}), ord($xclrValue{1}), ord($xclrValue{2}));

                            // modify the relevant style property
                            if (isset($this->mapCellXfIndex[$ixfe])) {
                                $font = $this->phpExcel->getCellXfByIndex($this->mapCellXfIndex[$ixfe])->getFont();
                                $font->getColor()->setRGB($rgb);
                                unset($font->colorIndex); // normal color index does not apply, discard
                            }
                        }
                        break;
                }

                $offset += $cb;
            }
        }

    }


    /**
     * Read STYLE record
     */
    private function readStyle()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; index to XF record and flag for built-in style
            $ixfe = self::getInt2d($recordData, 0);

            // bit: 11-0; mask 0x0FFF; index to XF record
            $xfIndex = (0x0FFF & $ixfe) >> 0;

            // bit: 15; mask 0x8000; 0 = user-defined style, 1 = built-in style
            $isBuiltIn = (bool) ((0x8000 & $ixfe) >> 15);

            if ($isBuiltIn) {
                // offset: 2; size: 1; identifier for built-in style
                $builtInId = ord($recordData{2});

                switch ($builtInId) {
                    case 0x00:
                        // currently, we are not using this for anything
                        break;
                    default:
                        break;
                }
            } else {
                // user-defined; not supported by PHPExcel
            }
        }
    }


    /**
     * Read PALETTE record
     */
    private function readPalette()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; number of following colors
            $nm = self::getInt2d($recordData, 0);

            // list of RGB colors
            for ($i = 0; $i < $nm; ++$i) {
                $rgb = substr($recordData, 2 + 4 * $i, 4);
                $this->palette[] = self::readRGB($rgb);
            }
        }
    }


    /**
     * SHEET
     *
     * This record is  located in the  Workbook Globals
     * Substream  and represents a sheet inside the workbook.
     * One SHEET record is written for each sheet. It stores the
     * sheet name and a stream offset to the BOF record of the
     * respective Sheet Substream within the Workbook Stream.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readSheet()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // offset: 0; size: 4; absolute stream position of the BOF record of the sheet
        // NOTE: not encrypted
        $rec_offset = self::getInt4d($this->data, $this->pos + 4);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 4; size: 1; sheet state
        switch (ord($recordData{4})) {
            case 0x00:
                $sheetState = PHPExcel_Worksheet::SHEETSTATE_VISIBLE;
                break;
            case 0x01:
                $sheetState = PHPExcel_Worksheet::SHEETSTATE_HIDDEN;
                break;
            case 0x02:
                $sheetState = PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN;
                break;
            default:
                $sheetState = PHPExcel_Worksheet::SHEETSTATE_VISIBLE;
                break;
        }

        // offset: 5; size: 1; sheet type
        $sheetType = ord($recordData{5});

        // offset: 6; size: var; sheet name
        if ($this->version == self::XLS_BIFF8) {
            $string = self::readUnicodeStringShort(substr($recordData, 6));
            $rec_name = $string['value'];
        } elseif ($this->version == self::XLS_BIFF7) {
            $string = $this->readByteStringShort(substr($recordData, 6));
            $rec_name = $string['value'];
        }

        $this->sheets[] = array(
            'name' => $rec_name,
            'offset' => $rec_offset,
            'sheetState' => $sheetState,
            'sheetType' => $sheetType,
        );
    }


    /**
     * Read EXTERNALBOOK record
     */
    private function readExternalBook()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset within record data
        $offset = 0;

        // there are 4 types of records
        if (strlen($recordData) > 4) {
            // external reference
            // offset: 0; size: 2; number of sheet names ($nm)
            $nm = self::getInt2d($recordData, 0);
            $offset += 2;

            // offset: 2; size: var; encoded URL without sheet name (Unicode string, 16-bit length)
            $encodedUrlString = self::readUnicodeStringLong(substr($recordData, 2));
            $offset += $encodedUrlString['size'];

            // offset: var; size: var; list of $nm sheet names (Unicode strings, 16-bit length)
            $externalSheetNames = array();
            for ($i = 0; $i < $nm; ++$i) {
                $externalSheetNameString = self::readUnicodeStringLong(substr($recordData, $offset));
                $externalSheetNames[] = $externalSheetNameString['value'];
                $offset += $externalSheetNameString['size'];
            }

            // store the record data
            $this->externalBooks[] = array(
                'type' => 'external',
                'encodedUrl' => $encodedUrlString['value'],
                'externalSheetNames' => $externalSheetNames,
            );
        } elseif (substr($recordData, 2, 2) == pack('CC', 0x01, 0x04)) {
            // internal reference
            // offset: 0; size: 2; number of sheet in this document
            // offset: 2; size: 2; 0x01 0x04
            $this->externalBooks[] = array(
                'type' => 'internal',
            );
        } elseif (substr($recordData, 0, 4) == pack('vCC', 0x0001, 0x01, 0x3A)) {
            // add-in function
            // offset: 0; size: 2; 0x0001
            $this->externalBooks[] = array(
                'type' => 'addInFunction',
            );
        } elseif (substr($recordData, 0, 2) == pack('v', 0x0000)) {
            // DDE links, OLE links
            // offset: 0; size: 2; 0x0000
            // offset: 2; size: var; encoded source document name
            $this->externalBooks[] = array(
                'type' => 'DDEorOLE',
            );
        }
    }


    /**
     * Read EXTERNNAME record.
     */
    private function readExternName()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // external sheet references provided for named cells
        if ($this->version == self::XLS_BIFF8) {
            // offset: 0; size: 2; options
            $options = self::getInt2d($recordData, 0);

            // offset: 2; size: 2;

            // offset: 4; size: 2; not used

            // offset: 6; size: var
            $nameString = self::readUnicodeStringShort(substr($recordData, 6));

            // offset: var; size: var; formula data
            $offset = 6 + $nameString['size'];
            $formula = $this->getFormulaFromStructure(substr($recordData, $offset));

            $this->externalNames[] = array(
                'name' => $nameString['value'],
                'formula' => $formula,
            );
        }
    }


    /**
     * Read EXTERNSHEET record
     */
    private function readExternSheet()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // external sheet references provided for named cells
        if ($this->version == self::XLS_BIFF8) {
            // offset: 0; size: 2; number of following ref structures
            $nm = self::getInt2d($recordData, 0);
            for ($i = 0; $i < $nm; ++$i) {
                $this->ref[] = array(
                    // offset: 2 + 6 * $i; index to EXTERNALBOOK record
                    'externalBookIndex' => self::getInt2d($recordData, 2 + 6 * $i),
                    // offset: 4 + 6 * $i; index to first sheet in EXTERNALBOOK record
                    'firstSheetIndex' => self::getInt2d($recordData, 4 + 6 * $i),
                    // offset: 6 + 6 * $i; index to last sheet in EXTERNALBOOK record
                    'lastSheetIndex' => self::getInt2d($recordData, 6 + 6 * $i),
                );
            }
        }
    }


    /**
     * DEFINEDNAME
     *
     * This record is part of a Link Table. It contains the name
     * and the token array of an internal defined name. Token
     * arrays of defined names contain tokens with aberrant
     * token classes.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readDefinedName()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8) {
            // retrieves named cells

            // offset: 0; size: 2; option flags
            $opts = self::getInt2d($recordData, 0);

            // bit: 5; mask: 0x0020; 0 = user-defined name, 1 = built-in-name
            $isBuiltInName = (0x0020 & $opts) >> 5;

            // offset: 2; size: 1; keyboard shortcut

            // offset: 3; size: 1; length of the name (character count)
            $nlen = ord($recordData{3});

            // offset: 4; size: 2; size of the formula data (it can happen that this is zero)
            // note: there can also be additional data, this is not included in $flen
            $flen = self::getInt2d($recordData, 4);

            // offset: 8; size: 2; 0=Global name, otherwise index to sheet (1-based)
            $scope = self::getInt2d($recordData, 8);

            // offset: 14; size: var; Name (Unicode string without length field)
            $string = self::readUnicodeString(substr($recordData, 14), $nlen);

            // offset: var; size: $flen; formula data
            $offset = 14 + $string['size'];
            $formulaStructure = pack('v', $flen) . substr($recordData, $offset);

            try {
                $formula = $this->getFormulaFromStructure($formulaStructure);
            } catch (PHPExcel_Exception $e) {
                $formula = '';
            }

            $this->definedname[] = array(
                'isBuiltInName' => $isBuiltInName,
                'name' => $string['value'],
                'formula' => $formula,
                'scope' => $scope,
            );
        }
    }


    /**
     * Read MSODRAWINGGROUP record
     */
    private function readMsoDrawingGroup()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);

        // get spliced record data
        $splicedRecordData = $this->getSplicedRecordData();
        $recordData = $splicedRecordData['recordData'];

        $this->drawingGroupData .= $recordData;
    }


    /**
     * SST - Shared String Table
     *
     * This record contains a list of all strings used anywhere
     * in the workbook. Each string occurs only once. The
     * workbook uses indexes into the list to reference the
     * strings.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     **/
    private function readSst()
    {
        // offset within (spliced) record data
        $pos = 0;

        // get spliced record data
        $splicedRecordData = $this->getSplicedRecordData();

        $recordData = $splicedRecordData['recordData'];
        $spliceOffsets = $splicedRecordData['spliceOffsets'];

        // offset: 0; size: 4; total number of strings in the workbook
        $pos += 4;

        // offset: 4; size: 4; number of following strings ($nm)
        $nm = self::getInt4d($recordData, 4);
        $pos += 4;

        // loop through the Unicode strings (16-bit length)
        for ($i = 0; $i < $nm; ++$i) {
            // number of characters in the Unicode string
            $numChars = self::getInt2d($recordData, $pos);
            $pos += 2;

            // option flags
            $optionFlags = ord($recordData{$pos});
            ++$pos;

            // bit: 0; mask: 0x01; 0 = compressed; 1 = uncompressed
            $isCompressed = (($optionFlags & 0x01) == 0) ;

            // bit: 2; mask: 0x02; 0 = ordinary; 1 = Asian phonetic
            $hasAsian = (($optionFlags & 0x04) != 0);

            // bit: 3; mask: 0x03; 0 = ordinary; 1 = Rich-Text
            $hasRichText = (($optionFlags & 0x08) != 0);

            if ($hasRichText) {
                // number of Rich-Text formatting runs
                $formattingRuns = self::getInt2d($recordData, $pos);
                $pos += 2;
            }

            if ($hasAsian) {
                // size of Asian phonetic setting
                $extendedRunLength = self::getInt4d($recordData, $pos);
                $pos += 4;
            }

            // expected byte length of character array if not split
            $len = ($isCompressed) ? $numChars : $numChars * 2;

            // look up limit position
            foreach ($spliceOffsets as $spliceOffset) {
                // it can happen that the string is empty, therefore we need
                // <= and not just <
                if ($pos <= $spliceOffset) {
                    $limitpos = $spliceOffset;
                    break;
                }
            }

            if ($pos + $len <= $limitpos) {
                // character array is not split between records

                $retstr = substr($recordData, $pos, $len);
                $pos += $len;
            } else {
                // character array is split between records

                // first part of character array
                $retstr = substr($recordData, $pos, $limitpos - $pos);

                $bytesRead = $limitpos - $pos;

                // remaining characters in Unicode string
                $charsLeft = $numChars - (($isCompressed) ? $bytesRead : ($bytesRead / 2));

                $pos = $limitpos;

                // keep reading the characters
                while ($charsLeft > 0) {
                    // look up next limit position, in case the string span more than one continue record
                    foreach ($spliceOffsets as $spliceOffset) {
                        if ($pos < $spliceOffset) {
                            $limitpos = $spliceOffset;
                            break;
                        }
                    }

                    // repeated option flags
                    // OpenOffice.org documentation 5.21
                    $option = ord($recordData{$pos});
                    ++$pos;

                    if ($isCompressed && ($option == 0)) {
                        // 1st fragment compressed
                        // this fragment compressed
                        $len = min($charsLeft, $limitpos - $pos);
                        $retstr .= substr($recordData, $pos, $len);
                        $charsLeft -= $len;
                        $isCompressed = true;
                    } elseif (!$isCompressed && ($option != 0)) {
                        // 1st fragment uncompressed
                        // this fragment uncompressed
                        $len = min($charsLeft * 2, $limitpos - $pos);
                        $retstr .= substr($recordData, $pos, $len);
                        $charsLeft -= $len / 2;
                        $isCompressed = false;
                    } elseif (!$isCompressed && ($option == 0)) {
                        // 1st fragment uncompressed
                        // this fragment compressed
                        $len = min($charsLeft, $limitpos - $pos);
                        for ($j = 0; $j < $len; ++$j) {
                            $retstr .= $recordData{$pos + $j} . chr(0);
                        }
                        $charsLeft -= $len;
                        $isCompressed = false;
                    } else {
                        // 1st fragment compressed
                        // this fragment uncompressed
                        $newstr = '';
                        for ($j = 0; $j < strlen($retstr); ++$j) {
                            $newstr .= $retstr[$j] . chr(0);
                        }
                        $retstr = $newstr;
                        $len = min($charsLeft * 2, $limitpos - $pos);
                        $retstr .= substr($recordData, $pos, $len);
                        $charsLeft -= $len / 2;
                        $isCompressed = false;
                    }

                    $pos += $len;
                }
            }

            // convert to UTF-8
            $retstr = self::encodeUTF16($retstr, $isCompressed);

            // read additional Rich-Text information, if any
            $fmtRuns = array();
            if ($hasRichText) {
                // list of formatting runs
                for ($j = 0; $j < $formattingRuns; ++$j) {
                    // first formatted character; zero-based
                    $charPos = self::getInt2d($recordData, $pos + $j * 4);

                    // index to font record
                    $fontIndex = self::getInt2d($recordData, $pos + 2 + $j * 4);

                    $fmtRuns[] = array(
                        'charPos' => $charPos,
                        'fontIndex' => $fontIndex,
                    );
                }
                $pos += 4 * $formattingRuns;
            }

            // read additional Asian phonetics information, if any
            if ($hasAsian) {
                // For Asian phonetic settings, we skip the extended string data
                $pos += $extendedRunLength;
            }

            // store the shared sting
            $this->sst[] = array(
                'value' => $retstr,
                'fmtRuns' => $fmtRuns,
            );
        }

        // getSplicedRecordData() takes care of moving current position in data stream
    }


    /**
     * Read PRINTGRIDLINES record
     */
    private function readPrintGridlines()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8 && !$this->readDataOnly) {
            // offset: 0; size: 2; 0 = do not print sheet grid lines; 1 = print sheet gridlines
            $printGridlines = (bool) self::getInt2d($recordData, 0);
            $this->phpSheet->setPrintGridlines($printGridlines);
        }
    }


    /**
     * Read DEFAULTROWHEIGHT record
     */
    private function readDefaultRowHeight()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; option flags
        // offset: 2; size: 2; default height for unused rows, (twips 1/20 point)
        $height = self::getInt2d($recordData, 2);
        $this->phpSheet->getDefaultRowDimension()->setRowHeight($height / 20);
    }


    /**
     * Read SHEETPR record
     */
    private function readSheetPr()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2

        // bit: 6; mask: 0x0040; 0 = outline buttons above outline group
        $isSummaryBelow = (0x0040 & self::getInt2d($recordData, 0)) >> 6;
        $this->phpSheet->setShowSummaryBelow($isSummaryBelow);

        // bit: 7; mask: 0x0080; 0 = outline buttons left of outline group
        $isSummaryRight = (0x0080 & self::getInt2d($recordData, 0)) >> 7;
        $this->phpSheet->setShowSummaryRight($isSummaryRight);

        // bit: 8; mask: 0x100; 0 = scale printout in percent, 1 = fit printout to number of pages
        // this corresponds to radio button setting in page setup dialog in Excel
        $this->isFitToPages = (bool) ((0x0100 & self::getInt2d($recordData, 0)) >> 8);
    }


    /**
     * Read HORIZONTALPAGEBREAKS record
     */
    private function readHorizontalPageBreaks()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8 && !$this->readDataOnly) {
            // offset: 0; size: 2; number of the following row index structures
            $nm = self::getInt2d($recordData, 0);

            // offset: 2; size: 6 * $nm; list of $nm row index structures
            for ($i = 0; $i < $nm; ++$i) {
                $r = self::getInt2d($recordData, 2 + 6 * $i);
                $cf = self::getInt2d($recordData, 2 + 6 * $i + 2);
                $cl = self::getInt2d($recordData, 2 + 6 * $i + 4);

                // not sure why two column indexes are necessary?
                $this->phpSheet->setBreakByColumnAndRow($cf, $r, PHPExcel_Worksheet::BREAK_ROW);
            }
        }
    }


    /**
     * Read VERTICALPAGEBREAKS record
     */
    private function readVerticalPageBreaks()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8 && !$this->readDataOnly) {
            // offset: 0; size: 2; number of the following column index structures
            $nm = self::getInt2d($recordData, 0);

            // offset: 2; size: 6 * $nm; list of $nm row index structures
            for ($i = 0; $i < $nm; ++$i) {
                $c = self::getInt2d($recordData, 2 + 6 * $i);
                $rf = self::getInt2d($recordData, 2 + 6 * $i + 2);
                $rl = self::getInt2d($recordData, 2 + 6 * $i + 4);

                // not sure why two row indexes are necessary?
                $this->phpSheet->setBreakByColumnAndRow($c, $rf, PHPExcel_Worksheet::BREAK_COLUMN);
            }
        }
    }


    /**
     * Read HEADER record
     */
    private function readHeader()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: var
            // realized that $recordData can be empty even when record exists
            if ($recordData) {
                if ($this->version == self::XLS_BIFF8) {
                    $string = self::readUnicodeStringLong($recordData);
                } else {
                    $string = $this->readByteStringShort($recordData);
                }

                $this->phpSheet->getHeaderFooter()->setOddHeader($string['value']);
                $this->phpSheet->getHeaderFooter()->setEvenHeader($string['value']);
            }
        }
    }


    /**
     * Read FOOTER record
     */
    private function readFooter()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: var
            // realized that $recordData can be empty even when record exists
            if ($recordData) {
                if ($this->version == self::XLS_BIFF8) {
                    $string = self::readUnicodeStringLong($recordData);
                } else {
                    $string = $this->readByteStringShort($recordData);
                }
                $this->phpSheet->getHeaderFooter()->setOddFooter($string['value']);
                $this->phpSheet->getHeaderFooter()->setEvenFooter($string['value']);
            }
        }
    }


    /**
     * Read HCENTER record
     */
    private function readHcenter()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; 0 = print sheet left aligned, 1 = print sheet centered horizontally
            $isHorizontalCentered = (bool) self::getInt2d($recordData, 0);

            $this->phpSheet->getPageSetup()->setHorizontalCentered($isHorizontalCentered);
        }
    }


    /**
     * Read VCENTER record
     */
    private function readVcenter()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; 0 = print sheet aligned at top page border, 1 = print sheet vertically centered
            $isVerticalCentered = (bool) self::getInt2d($recordData, 0);

            $this->phpSheet->getPageSetup()->setVerticalCentered($isVerticalCentered);
        }
    }


    /**
     * Read LEFTMARGIN record
     */
    private function readLeftMargin()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8
            $this->phpSheet->getPageMargins()->setLeft(self::extractNumber($recordData));
        }
    }


    /**
     * Read RIGHTMARGIN record
     */
    private function readRightMargin()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8
            $this->phpSheet->getPageMargins()->setRight(self::extractNumber($recordData));
        }
    }


    /**
     * Read TOPMARGIN record
     */
    private function readTopMargin()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8
            $this->phpSheet->getPageMargins()->setTop(self::extractNumber($recordData));
        }
    }


    /**
     * Read BOTTOMMARGIN record
     */
    private function readBottomMargin()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8
            $this->phpSheet->getPageMargins()->setBottom(self::extractNumber($recordData));
        }
    }


    /**
     * Read PAGESETUP record
     */
    private function readPageSetup()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; paper size
            $paperSize = self::getInt2d($recordData, 0);

            // offset: 2; size: 2; scaling factor
            $scale = self::getInt2d($recordData, 2);

            // offset: 6; size: 2; fit worksheet width to this number of pages, 0 = use as many as needed
            $fitToWidth = self::getInt2d($recordData, 6);

            // offset: 8; size: 2; fit worksheet height to this number of pages, 0 = use as many as needed
            $fitToHeight = self::getInt2d($recordData, 8);

            // offset: 10; size: 2; option flags

            // bit: 1; mask: 0x0002; 0=landscape, 1=portrait
            $isPortrait = (0x0002 & self::getInt2d($recordData, 10)) >> 1;

            // bit: 2; mask: 0x0004; 1= paper size, scaling factor, paper orient. not init
            // when this bit is set, do not use flags for those properties
            $isNotInit = (0x0004 & self::getInt2d($recordData, 10)) >> 2;

            if (!$isNotInit) {
                $this->phpSheet->getPageSetup()->setPaperSize($paperSize);
                switch ($isPortrait) {
                    case 0:
                        $this->phpSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_LANDSCAPE);
                        break;
                    case 1:
                        $this->phpSheet->getPageSetup()->setOrientation(PHPExcel_Worksheet_PageSetup::ORIENTATION_PORTRAIT);
                        break;
                }

                $this->phpSheet->getPageSetup()->setScale($scale, false);
                $this->phpSheet->getPageSetup()->setFitToPage((bool) $this->isFitToPages);
                $this->phpSheet->getPageSetup()->setFitToWidth($fitToWidth, false);
                $this->phpSheet->getPageSetup()->setFitToHeight($fitToHeight, false);
            }

            // offset: 16; size: 8; header margin (IEEE 754 floating-point value)
            $marginHeader = self::extractNumber(substr($recordData, 16, 8));
            $this->phpSheet->getPageMargins()->setHeader($marginHeader);

            // offset: 24; size: 8; footer margin (IEEE 754 floating-point value)
            $marginFooter = self::extractNumber(substr($recordData, 24, 8));
            $this->phpSheet->getPageMargins()->setFooter($marginFooter);
        }
    }


    /**
     * PROTECT - Sheet protection (BIFF2 through BIFF8)
     *   if this record is omitted, then it also means no sheet protection
     */
    private function readProtect()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // offset: 0; size: 2;

        // bit 0, mask 0x01; 1 = sheet is protected
        $bool = (0x01 & self::getInt2d($recordData, 0)) >> 0;
        $this->phpSheet->getProtection()->setSheet((bool)$bool);
    }


    /**
     * SCENPROTECT
     */
    private function readScenProtect()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // offset: 0; size: 2;

        // bit: 0, mask 0x01; 1 = scenarios are protected
        $bool = (0x01 & self::getInt2d($recordData, 0)) >> 0;

        $this->phpSheet->getProtection()->setScenarios((bool)$bool);
    }


    /**
     * OBJECTPROTECT
     */
    private function readObjectProtect()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // offset: 0; size: 2;

        // bit: 0, mask 0x01; 1 = objects are protected
        $bool = (0x01 & self::getInt2d($recordData, 0)) >> 0;

        $this->phpSheet->getProtection()->setObjects((bool)$bool);
    }


    /**
     * PASSWORD - Sheet protection (hashed) password (BIFF2 through BIFF8)
     */
    private function readPassword()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; 16-bit hash value of password
            $password = strtoupper(dechex(self::getInt2d($recordData, 0))); // the hashed password
            $this->phpSheet->getProtection()->setPassword($password, true);
        }
    }


    /**
     * Read DEFCOLWIDTH record
     */
    private function readDefColWidth()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; default column width
        $width = self::getInt2d($recordData, 0);
        if ($width != 8) {
            $this->phpSheet->getDefaultColumnDimension()->setWidth($width);
        }
    }


    /**
     * Read COLINFO record
     */
    private function readColInfo()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; index to first column in range
            $fc = self::getInt2d($recordData, 0); // first column index

            // offset: 2; size: 2; index to last column in range
            $lc = self::getInt2d($recordData, 2); // first column index

            // offset: 4; size: 2; width of the column in 1/256 of the width of the zero character
            $width = self::getInt2d($recordData, 4);

            // offset: 6; size: 2; index to XF record for default column formatting
            $xfIndex = self::getInt2d($recordData, 6);

            // offset: 8; size: 2; option flags
            // bit: 0; mask: 0x0001; 1= columns are hidden
            $isHidden = (0x0001 & self::getInt2d($recordData, 8)) >> 0;

            // bit: 10-8; mask: 0x0700; outline level of the columns (0 = no outline)
            $level = (0x0700 & self::getInt2d($recordData, 8)) >> 8;

            // bit: 12; mask: 0x1000; 1 = collapsed
            $isCollapsed = (0x1000 & self::getInt2d($recordData, 8)) >> 12;

            // offset: 10; size: 2; not used

            for ($i = $fc; $i <= $lc; ++$i) {
                if ($lc == 255 || $lc == 256) {
                    $this->phpSheet->getDefaultColumnDimension()->setWidth($width / 256);
                    break;
                }
                $this->phpSheet->getColumnDimensionByColumn($i)->setWidth($width / 256);
                $this->phpSheet->getColumnDimensionByColumn($i)->setVisible(!$isHidden);
                $this->phpSheet->getColumnDimensionByColumn($i)->setOutlineLevel($level);
                $this->phpSheet->getColumnDimensionByColumn($i)->setCollapsed($isCollapsed);
                $this->phpSheet->getColumnDimensionByColumn($i)->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }
        }
    }


    /**
     * ROW
     *
     * This record contains the properties of a single row in a
     * sheet. Rows and cells in a sheet are divided into blocks
     * of 32 rows.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readRow()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; index of this row
            $r = self::getInt2d($recordData, 0);

            // offset: 2; size: 2; index to column of the first cell which is described by a cell record

            // offset: 4; size: 2; index to column of the last cell which is described by a cell record, increased by 1

            // offset: 6; size: 2;

            // bit: 14-0; mask: 0x7FFF; height of the row, in twips = 1/20 of a point
            $height = (0x7FFF & self::getInt2d($recordData, 6)) >> 0;

            // bit: 15: mask: 0x8000; 0 = row has custom height; 1= row has default height
            $useDefaultHeight = (0x8000 & self::getInt2d($recordData, 6)) >> 15;

            if (!$useDefaultHeight) {
                $this->phpSheet->getRowDimension($r + 1)->setRowHeight($height / 20);
            }

            // offset: 8; size: 2; not used

            // offset: 10; size: 2; not used in BIFF5-BIFF8

            // offset: 12; size: 4; option flags and default row formatting

            // bit: 2-0: mask: 0x00000007; outline level of the row
            $level = (0x00000007 & self::getInt4d($recordData, 12)) >> 0;
            $this->phpSheet->getRowDimension($r + 1)->setOutlineLevel($level);

            // bit: 4; mask: 0x00000010; 1 = outline group start or ends here... and is collapsed
            $isCollapsed = (0x00000010 & self::getInt4d($recordData, 12)) >> 4;
            $this->phpSheet->getRowDimension($r + 1)->setCollapsed($isCollapsed);

            // bit: 5; mask: 0x00000020; 1 = row is hidden
            $isHidden = (0x00000020 & self::getInt4d($recordData, 12)) >> 5;
            $this->phpSheet->getRowDimension($r + 1)->setVisible(!$isHidden);

            // bit: 7; mask: 0x00000080; 1 = row has explicit format
            $hasExplicitFormat = (0x00000080 & self::getInt4d($recordData, 12)) >> 7;

            // bit: 27-16; mask: 0x0FFF0000; only applies when hasExplicitFormat = 1; index to XF record
            $xfIndex = (0x0FFF0000 & self::getInt4d($recordData, 12)) >> 16;

            if ($hasExplicitFormat) {
                $this->phpSheet->getRowDimension($r + 1)->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }
        }
    }


    /**
     * Read RK record
     * This record represents a cell that contains an RK value
     * (encoded integer or floating-point value). If a
     * floating-point value cannot be encoded to an RK value,
     * a NUMBER record will be written. This record replaces the
     * record INTEGER written in BIFF2.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readRk()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; index to column
        $column = self::getInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);

        // Read cell?
        if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; index to XF record
            $xfIndex = self::getInt2d($recordData, 4);

            // offset: 6; size: 4; RK value
            $rknum = self::getInt4d($recordData, 6);
            $numValue = self::getIEEE754($rknum);

            $cell = $this->phpSheet->getCell($columnString . ($row + 1));
            if (!$this->readDataOnly) {
                // add style information
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }

            // add cell
            $cell->setValueExplicit($numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
        }
    }


    /**
     * Read LABELSST record
     * This record represents a cell that contains a string. It
     * replaces the LABEL record and RSTRING record used in
     * BIFF2-BIFF5.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readLabelSst()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; index to column
        $column = self::getInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);

        $emptyCell = true;
        // Read cell?
        if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; index to XF record
            $xfIndex = self::getInt2d($recordData, 4);

            // offset: 6; size: 4; index to SST record
            $index = self::getInt4d($recordData, 6);

            // add cell
            if (($fmtRuns = $this->sst[$index]['fmtRuns']) && !$this->readDataOnly) {
                // then we should treat as rich text
                $richText = new PHPExcel_RichText();
                $charPos = 0;
                $sstCount = count($this->sst[$index]['fmtRuns']);
                for ($i = 0; $i <= $sstCount; ++$i) {
                    if (isset($fmtRuns[$i])) {
                        $text = PHPExcel_Shared_String::Substring($this->sst[$index]['value'], $charPos, $fmtRuns[$i]['charPos'] - $charPos);
                        $charPos = $fmtRuns[$i]['charPos'];
                    } else {
                        $text = PHPExcel_Shared_String::Substring($this->sst[$index]['value'], $charPos, PHPExcel_Shared_String::CountCharacters($this->sst[$index]['value']));
                    }

                    if (PHPExcel_Shared_String::CountCharacters($text) > 0) {
                        if ($i == 0) { // first text run, no style
                            $richText->createText($text);
                        } else {
                            $textRun = $richText->createTextRun($text);
                            if (isset($fmtRuns[$i - 1])) {
                                if ($fmtRuns[$i - 1]['fontIndex'] < 4) {
                                    $fontIndex = $fmtRuns[$i - 1]['fontIndex'];
                                } else {
                                    // this has to do with that index 4 is omitted in all BIFF versions for some strange reason
                                    // check the OpenOffice documentation of the FONT record
                                    $fontIndex = $fmtRuns[$i - 1]['fontIndex'] - 1;
                                }
                                $textRun->setFont(clone $this->objFonts[$fontIndex]);
                            }
                        }
                    }
                }
                if ($this->readEmptyCells || trim($richText->getPlainText()) !== '') {
                    $cell = $this->phpSheet->getCell($columnString . ($row + 1));
                    $cell->setValueExplicit($richText, PHPExcel_Cell_DataType::TYPE_STRING);
                    $emptyCell = false;
                }
            } else {
                if ($this->readEmptyCells || trim($this->sst[$index]['value']) !== '') {
                    $cell = $this->phpSheet->getCell($columnString . ($row + 1));
                    $cell->setValueExplicit($this->sst[$index]['value'], PHPExcel_Cell_DataType::TYPE_STRING);
                    $emptyCell = false;
                }
            }

            if (!$this->readDataOnly && !$emptyCell) {
                // add style information
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }
        }
    }


    /**
     * Read MULRK record
     * This record represents a cell range containing RK value
     * cells. All cells are located in the same row.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readMulRk()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; index to first column
        $colFirst = self::getInt2d($recordData, 2);

        // offset: var; size: 2; index to last column
        $colLast = self::getInt2d($recordData, $length - 2);
        $columns = $colLast - $colFirst + 1;

        // offset within record data
        $offset = 4;

        for ($i = 0; $i < $columns; ++$i) {
            $columnString = PHPExcel_Cell::stringFromColumnIndex($colFirst + $i);

            // Read cell?
            if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
                // offset: var; size: 2; index to XF record
                $xfIndex = self::getInt2d($recordData, $offset);

                // offset: var; size: 4; RK value
                $numValue = self::getIEEE754(self::getInt4d($recordData, $offset + 2));
                $cell = $this->phpSheet->getCell($columnString . ($row + 1));
                if (!$this->readDataOnly) {
                    // add style
                    $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
                }

                // add cell value
                $cell->setValueExplicit($numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
            }

            $offset += 6;
        }
    }


    /**
     * Read NUMBER record
     * This record represents a cell that contains a
     * floating-point value.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readNumber()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getInt2d($recordData, 0);

        // offset: 2; size 2; index to column
        $column = self::getInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);

        // Read cell?
        if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset 4; size: 2; index to XF record
            $xfIndex = self::getInt2d($recordData, 4);

            $numValue = self::extractNumber(substr($recordData, 6, 8));

            $cell = $this->phpSheet->getCell($columnString . ($row + 1));
            if (!$this->readDataOnly) {
                // add cell style
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }

            // add cell value
            $cell->setValueExplicit($numValue, PHPExcel_Cell_DataType::TYPE_NUMERIC);
        }
    }


    /**
     * Read FORMULA record + perhaps a following STRING record if formula result is a string
     * This record contains the token array and the result of a
     * formula cell.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readFormula()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; row index
        $row = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; col index
        $column = self::getInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);

        // offset: 20: size: variable; formula structure
        $formulaStructure = substr($recordData, 20);

        // offset: 14: size: 2; option flags, recalculate always, recalculate on open etc.
        $options = self::getInt2d($recordData, 14);

        // bit: 0; mask: 0x0001; 1 = recalculate always
        // bit: 1; mask: 0x0002; 1 = calculate on open
        // bit: 2; mask: 0x0008; 1 = part of a shared formula
        $isPartOfSharedFormula = (bool) (0x0008 & $options);

        // WARNING:
        // We can apparently not rely on $isPartOfSharedFormula. Even when $isPartOfSharedFormula = true
        // the formula data may be ordinary formula data, therefore we need to check
        // explicitly for the tExp token (0x01)
        $isPartOfSharedFormula = $isPartOfSharedFormula && ord($formulaStructure{2}) == 0x01;

        if ($isPartOfSharedFormula) {
            // part of shared formula which means there will be a formula with a tExp token and nothing else
            // get the base cell, grab tExp token
            $baseRow = self::getInt2d($formulaStructure, 3);
            $baseCol = self::getInt2d($formulaStructure, 5);
            $this->_baseCell = PHPExcel_Cell::stringFromColumnIndex($baseCol). ($baseRow + 1);
        }

        // Read cell?
        if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            if ($isPartOfSharedFormula) {
                // formula is added to this cell after the sheet has been read
                $this->sharedFormulaParts[$columnString . ($row + 1)] = $this->_baseCell;
            }

            // offset: 16: size: 4; not used

            // offset: 4; size: 2; XF index
            $xfIndex = self::getInt2d($recordData, 4);

            // offset: 6; size: 8; result of the formula
            if ((ord($recordData{6}) == 0) && (ord($recordData{12}) == 255) && (ord($recordData{13}) == 255)) {
                // String formula. Result follows in appended STRING record
                $dataType = PHPExcel_Cell_DataType::TYPE_STRING;

                // read possible SHAREDFMLA record
                $code = self::getInt2d($this->data, $this->pos);
                if ($code == self::XLS_TYPE_SHAREDFMLA) {
                    $this->readSharedFmla();
                }

                // read STRING record
                $value = $this->readString();
            } elseif ((ord($recordData{6}) == 1)
                && (ord($recordData{12}) == 255)
                && (ord($recordData{13}) == 255)) {
                // Boolean formula. Result is in +2; 0=false, 1=true
                $dataType = PHPExcel_Cell_DataType::TYPE_BOOL;
                $value = (bool) ord($recordData{8});
            } elseif ((ord($recordData{6}) == 2)
                && (ord($recordData{12}) == 255)
                && (ord($recordData{13}) == 255)) {
                // Error formula. Error code is in +2
                $dataType = PHPExcel_Cell_DataType::TYPE_ERROR;
                $value = PHPExcel_Reader_Excel5_ErrorCode::lookup(ord($recordData{8}));
            } elseif ((ord($recordData{6}) == 3)
                && (ord($recordData{12}) == 255)
                && (ord($recordData{13}) == 255)) {
                // Formula result is a null string
                $dataType = PHPExcel_Cell_DataType::TYPE_NULL;
                $value = '';
            } else {
                // forumla result is a number, first 14 bytes like _NUMBER record
                $dataType = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                $value = self::extractNumber(substr($recordData, 6, 8));
            }

            $cell = $this->phpSheet->getCell($columnString . ($row + 1));
            if (!$this->readDataOnly) {
                // add cell style
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }

            // store the formula
            if (!$isPartOfSharedFormula) {
                // not part of shared formula
                // add cell value. If we can read formula, populate with formula, otherwise just used cached value
                try {
                    if ($this->version != self::XLS_BIFF8) {
                        throw new PHPExcel_Reader_Exception('Not BIFF8. Can only read BIFF8 formulas');
                    }
                    $formula = $this->getFormulaFromStructure($formulaStructure); // get formula in human language
                    $cell->setValueExplicit('=' . $formula, PHPExcel_Cell_DataType::TYPE_FORMULA);

                } catch (PHPExcel_Exception $e) {
                    $cell->setValueExplicit($value, $dataType);
                }
            } else {
                if ($this->version == self::XLS_BIFF8) {
                    // do nothing at this point, formula id added later in the code
                } else {
                    $cell->setValueExplicit($value, $dataType);
                }
            }

            // store the cached calculated value
            $cell->setCalculatedValue($value);
        }
    }


    /**
     * Read a SHAREDFMLA record. This function just stores the binary shared formula in the reader,
     * which usually contains relative references.
     * These will be used to construct the formula in each shared formula part after the sheet is read.
     */
    private function readSharedFmla()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0, size: 6; cell range address of the area used by the shared formula, not used for anything
        $cellRange = substr($recordData, 0, 6);
        $cellRange = $this->readBIFF5CellRangeAddressFixed($cellRange); // note: even BIFF8 uses BIFF5 syntax

        // offset: 6, size: 1; not used

        // offset: 7, size: 1; number of existing FORMULA records for this shared formula
        $no = ord($recordData{7});

        // offset: 8, size: var; Binary token array of the shared formula
        $formula = substr($recordData, 8);

        // at this point we only store the shared formula for later use
        $this->sharedFormulas[$this->_baseCell] = $formula;
    }


    /**
     * Read a STRING record from current stream position and advance the stream pointer to next record
     * This record is used for storing result from FORMULA record when it is a string, and
     * it occurs directly after the FORMULA record
     *
     * @return string The string contents as UTF-8
     */
    private function readString()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8) {
            $string = self::readUnicodeStringLong($recordData);
            $value = $string['value'];
        } else {
            $string = $this->readByteStringLong($recordData);
            $value = $string['value'];
        }

        return $value;
    }


    /**
     * Read BOOLERR record
     * This record represents a Boolean value or error value
     * cell.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readBoolErr()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; row index
        $row = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; column index
        $column = self::getInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);

        // Read cell?
        if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; index to XF record
            $xfIndex = self::getInt2d($recordData, 4);

            // offset: 6; size: 1; the boolean value or error value
            $boolErr = ord($recordData{6});

            // offset: 7; size: 1; 0=boolean; 1=error
            $isError = ord($recordData{7});

            $cell = $this->phpSheet->getCell($columnString . ($row + 1));
            switch ($isError) {
                case 0: // boolean
                    $value = (bool) $boolErr;

                    // add cell value
                    $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_BOOL);
                    break;
                case 1: // error type
                    $value = PHPExcel_Reader_Excel5_ErrorCode::lookup($boolErr);

                    // add cell value
                    $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_ERROR);
                    break;
            }

            if (!$this->readDataOnly) {
                // add cell style
                $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }
        }
    }


    /**
     * Read MULBLANK record
     * This record represents a cell range of empty cells. All
     * cells are located in the same row
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readMulBlank()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; index to first column
        $fc = self::getInt2d($recordData, 2);

        // offset: 4; size: 2 x nc; list of indexes to XF records
        // add style information
        if (!$this->readDataOnly && $this->readEmptyCells) {
            for ($i = 0; $i < $length / 2 - 3; ++$i) {
                $columnString = PHPExcel_Cell::stringFromColumnIndex($fc + $i);

                // Read cell?
                if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
                    $xfIndex = self::getInt2d($recordData, 4 + 2 * $i);
                    $this->phpSheet->getCell($columnString . ($row + 1))->setXfIndex($this->mapCellXfIndex[$xfIndex]);
                }
            }
        }

        // offset: 6; size 2; index to last column (not needed)
    }


    /**
     * Read LABEL record
     * This record represents a cell that contains a string. In
     * BIFF8 it is usually replaced by the LABELSST record.
     * Excel still uses this record, if it copies unformatted
     * text cells to the clipboard.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readLabel()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; index to row
        $row = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; index to column
        $column = self::getInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($column);

        // Read cell?
        if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; XF index
            $xfIndex = self::getInt2d($recordData, 4);

            // add cell value
            // todo: what if string is very long? continue record
            if ($this->version == self::XLS_BIFF8) {
                $string = self::readUnicodeStringLong(substr($recordData, 6));
                $value = $string['value'];
            } else {
                $string = $this->readByteStringLong(substr($recordData, 6));
                $value = $string['value'];
            }
            if ($this->readEmptyCells || trim($value) !== '') {
                $cell = $this->phpSheet->getCell($columnString . ($row + 1));
                $cell->setValueExplicit($value, PHPExcel_Cell_DataType::TYPE_STRING);

                if (!$this->readDataOnly) {
                    // add cell style
                    $cell->setXfIndex($this->mapCellXfIndex[$xfIndex]);
                }
            }
        }
    }


    /**
     * Read BLANK record
     */
    private function readBlank()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; row index
        $row = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; col index
        $col = self::getInt2d($recordData, 2);
        $columnString = PHPExcel_Cell::stringFromColumnIndex($col);

        // Read cell?
        if (($this->getReadFilter() !== null) && $this->getReadFilter()->readCell($columnString, $row + 1, $this->phpSheet->getTitle())) {
            // offset: 4; size: 2; XF index
            $xfIndex = self::getInt2d($recordData, 4);

            // add style information
            if (!$this->readDataOnly && $this->readEmptyCells) {
                $this->phpSheet->getCell($columnString . ($row + 1))->setXfIndex($this->mapCellXfIndex[$xfIndex]);
            }
        }
    }


    /**
     * Read MSODRAWING record
     */
    private function readMsoDrawing()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);

        // get spliced record data
        $splicedRecordData = $this->getSplicedRecordData();
        $recordData = $splicedRecordData['recordData'];

        $this->drawingData .= $recordData;
    }


    /**
     * Read OBJ record
     */
    private function readObj()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly || $this->version != self::XLS_BIFF8) {
            return;
        }

        // recordData consists of an array of subrecords looking like this:
        //    ft: 2 bytes; ftCmo type (0x15)
        //    cb: 2 bytes; size in bytes of ftCmo data
        //    ot: 2 bytes; Object Type
        //    id: 2 bytes; Object id number
        //    grbit: 2 bytes; Option Flags
        //    data: var; subrecord data

        // for now, we are just interested in the second subrecord containing the object type
        $ftCmoType  = self::getInt2d($recordData, 0);
        $cbCmoSize  = self::getInt2d($recordData, 2);
        $otObjType  = self::getInt2d($recordData, 4);
        $idObjID    = self::getInt2d($recordData, 6);
        $grbitOpts  = self::getInt2d($recordData, 6);

        $this->objs[] = array(
            'ftCmoType' => $ftCmoType,
            'cbCmoSize' => $cbCmoSize,
            'otObjType' => $otObjType,
            'idObjID'   => $idObjID,
            'grbitOpts' => $grbitOpts
        );
        $this->textObjRef = $idObjID;

//        echo '<b>_readObj()</b><br />';
//        var_dump(end($this->objs));
//        echo '<br />';
    }


    /**
     * Read WINDOW2 record
     */
    private function readWindow2()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; option flags
        $options = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; index to first visible row
        $firstVisibleRow = self::getInt2d($recordData, 2);

        // offset: 4; size: 2; index to first visible colum
        $firstVisibleColumn = self::getInt2d($recordData, 4);
        if ($this->version === self::XLS_BIFF8) {
            // offset:  8; size: 2; not used
            // offset: 10; size: 2; cached magnification factor in page break preview (in percent); 0 = Default (60%)
            // offset: 12; size: 2; cached magnification factor in normal view (in percent); 0 = Default (100%)
            // offset: 14; size: 4; not used
            $zoomscaleInPageBreakPreview = self::getInt2d($recordData, 10);
            if ($zoomscaleInPageBreakPreview === 0) {
                $zoomscaleInPageBreakPreview = 60;
            }
            $zoomscaleInNormalView = self::getInt2d($recordData, 12);
            if ($zoomscaleInNormalView === 0) {
                $zoomscaleInNormalView = 100;
            }
        }

        // bit: 1; mask: 0x0002; 0 = do not show gridlines, 1 = show gridlines
        $showGridlines = (bool) ((0x0002 & $options) >> 1);
        $this->phpSheet->setShowGridlines($showGridlines);

        // bit: 2; mask: 0x0004; 0 = do not show headers, 1 = show headers
        $showRowColHeaders = (bool) ((0x0004 & $options) >> 2);
        $this->phpSheet->setShowRowColHeaders($showRowColHeaders);

        // bit: 3; mask: 0x0008; 0 = panes are not frozen, 1 = panes are frozen
        $this->frozen = (bool) ((0x0008 & $options) >> 3);

        // bit: 6; mask: 0x0040; 0 = columns from left to right, 1 = columns from right to left
        $this->phpSheet->setRightToLeft((bool)((0x0040 & $options) >> 6));

        // bit: 10; mask: 0x0400; 0 = sheet not active, 1 = sheet active
        $isActive = (bool) ((0x0400 & $options) >> 10);
        if ($isActive) {
            $this->phpExcel->setActiveSheetIndex($this->phpExcel->getIndex($this->phpSheet));
        }

        // bit: 11; mask: 0x0800; 0 = normal view, 1 = page break view
        $isPageBreakPreview = (bool) ((0x0800 & $options) >> 11);

        //FIXME: set $firstVisibleRow and $firstVisibleColumn

        if ($this->phpSheet->getSheetView()->getView() !== PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_LAYOUT) {
            //NOTE: this setting is inferior to page layout view(Excel2007-)
            $view = $isPageBreakPreview ? PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_BREAK_PREVIEW : PHPExcel_Worksheet_SheetView::SHEETVIEW_NORMAL;
            $this->phpSheet->getSheetView()->setView($view);
            if ($this->version === self::XLS_BIFF8) {
                $zoomScale = $isPageBreakPreview ? $zoomscaleInPageBreakPreview : $zoomscaleInNormalView;
                $this->phpSheet->getSheetView()->setZoomScale($zoomScale);
                $this->phpSheet->getSheetView()->setZoomScaleNormal($zoomscaleInNormalView);
            }
        }
    }

    /**
     * Read PLV Record(Created by Excel2007 or upper)
     */
    private function readPageLayoutView()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        //var_dump(unpack("vrt/vgrbitFrt/V2reserved/vwScalePLV/vgrbit", $recordData));

        // offset: 0; size: 2; rt
        //->ignore
        $rt = self::getInt2d($recordData, 0);
        // offset: 2; size: 2; grbitfr
        //->ignore
        $grbitFrt = self::getInt2d($recordData, 2);
        // offset: 4; size: 8; reserved
        //->ignore

        // offset: 12; size 2; zoom scale
        $wScalePLV = self::getInt2d($recordData, 12);
        // offset: 14; size 2; grbit
        $grbit = self::getInt2d($recordData, 14);

        // decomprise grbit
        $fPageLayoutView   = $grbit & 0x01;
        $fRulerVisible     = ($grbit >> 1) & 0x01; //no support
        $fWhitespaceHidden = ($grbit >> 3) & 0x01; //no support

        if ($fPageLayoutView === 1) {
            $this->phpSheet->getSheetView()->setView(PHPExcel_Worksheet_SheetView::SHEETVIEW_PAGE_LAYOUT);
            $this->phpSheet->getSheetView()->setZoomScale($wScalePLV); //set by Excel2007 only if SHEETVIEW_PAGE_LAYOUT
        }
        //otherwise, we cannot know whether SHEETVIEW_PAGE_LAYOUT or SHEETVIEW_PAGE_BREAK_PREVIEW.
    }

    /**
     * Read SCL record
     */
    private function readScl()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // offset: 0; size: 2; numerator of the view magnification
        $numerator = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; numerator of the view magnification
        $denumerator = self::getInt2d($recordData, 2);

        // set the zoom scale (in percent)
        $this->phpSheet->getSheetView()->setZoomScale($numerator * 100 / $denumerator);
    }


    /**
     * Read PANE record
     */
    private function readPane()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; position of vertical split
            $px = self::getInt2d($recordData, 0);

            // offset: 2; size: 2; position of horizontal split
            $py = self::getInt2d($recordData, 2);

            if ($this->frozen) {
                // frozen panes
                $this->phpSheet->freezePane(PHPExcel_Cell::stringFromColumnIndex($px) . ($py + 1));
            } else {
                // unfrozen panes; split windows; not supported by PHPExcel core
            }
        }
    }


    /**
     * Read SELECTION record. There is one such record for each pane in the sheet.
     */
    private function readSelection()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 1; pane identifier
            $paneId = ord($recordData{0});

            // offset: 1; size: 2; index to row of the active cell
            $r = self::getInt2d($recordData, 1);

            // offset: 3; size: 2; index to column of the active cell
            $c = self::getInt2d($recordData, 3);

            // offset: 5; size: 2; index into the following cell range list to the
            //  entry that contains the active cell
            $index = self::getInt2d($recordData, 5);

            // offset: 7; size: var; cell range address list containing all selected cell ranges
            $data = substr($recordData, 7);
            $cellRangeAddressList = $this->readBIFF5CellRangeAddressList($data); // note: also BIFF8 uses BIFF5 syntax

            $selectedCells = $cellRangeAddressList['cellRangeAddresses'][0];

            // first row '1' + last row '16384' indicates that full column is selected (apparently also in BIFF8!)
            if (preg_match('/^([A-Z]+1\:[A-Z]+)16384$/', $selectedCells)) {
                $selectedCells = preg_replace('/^([A-Z]+1\:[A-Z]+)16384$/', '${1}1048576', $selectedCells);
            }

            // first row '1' + last row '65536' indicates that full column is selected
            if (preg_match('/^([A-Z]+1\:[A-Z]+)65536$/', $selectedCells)) {
                $selectedCells = preg_replace('/^([A-Z]+1\:[A-Z]+)65536$/', '${1}1048576', $selectedCells);
            }

            // first column 'A' + last column 'IV' indicates that full row is selected
            if (preg_match('/^(A[0-9]+\:)IV([0-9]+)$/', $selectedCells)) {
                $selectedCells = preg_replace('/^(A[0-9]+\:)IV([0-9]+)$/', '${1}XFD${2}', $selectedCells);
            }

            $this->phpSheet->setSelectedCells($selectedCells);
        }
    }


    private function includeCellRangeFiltered($cellRangeAddress)
    {
        $includeCellRange = true;
        if ($this->getReadFilter() !== null) {
            $includeCellRange = false;
            $rangeBoundaries = PHPExcel_Cell::getRangeBoundaries($cellRangeAddress);
            $rangeBoundaries[1][0]++;
            for ($row = $rangeBoundaries[0][1]; $row <= $rangeBoundaries[1][1]; $row++) {
                for ($column = $rangeBoundaries[0][0]; $column != $rangeBoundaries[1][0]; $column++) {
                    if ($this->getReadFilter()->readCell($column, $row, $this->phpSheet->getTitle())) {
                        $includeCellRange = true;
                        break 2;
                    }
                }
            }
        }
        return $includeCellRange;
    }


    /**
     * MERGEDCELLS
     *
     * This record contains the addresses of merged cell ranges
     * in the current sheet.
     *
     * --    "OpenOffice.org's Documentation of the Microsoft
     *         Excel File Format"
     */
    private function readMergedCells()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->version == self::XLS_BIFF8 && !$this->readDataOnly) {
            $cellRangeAddressList = $this->readBIFF8CellRangeAddressList($recordData);
            foreach ($cellRangeAddressList['cellRangeAddresses'] as $cellRangeAddress) {
                if ((strpos($cellRangeAddress, ':') !== false) &&
                    ($this->includeCellRangeFiltered($cellRangeAddress))) {
                    $this->phpSheet->mergeCells($cellRangeAddress);
                }
            }
        }
    }


    /**
     * Read HYPERLINK record
     */
    private function readHyperLink()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer forward to next record
        $this->pos += 4 + $length;

        if (!$this->readDataOnly) {
            // offset: 0; size: 8; cell range address of all cells containing this hyperlink
            try {
                $cellRange = $this->readBIFF8CellRangeAddressFixed($recordData, 0, 8);
            } catch (PHPExcel_Exception $e) {
                return;
            }

            // offset: 8, size: 16; GUID of StdLink

            // offset: 24, size: 4; unknown value

            // offset: 28, size: 4; option flags
            // bit: 0; mask: 0x00000001; 0 = no link or extant, 1 = file link or URL
            $isFileLinkOrUrl = (0x00000001 & self::getInt2d($recordData, 28)) >> 0;

            // bit: 1; mask: 0x00000002; 0 = relative path, 1 = absolute path or URL
            $isAbsPathOrUrl = (0x00000001 & self::getInt2d($recordData, 28)) >> 1;

            // bit: 2 (and 4); mask: 0x00000014; 0 = no description
            $hasDesc = (0x00000014 & self::getInt2d($recordData, 28)) >> 2;

            // bit: 3; mask: 0x00000008; 0 = no text, 1 = has text
            $hasText = (0x00000008 & self::getInt2d($recordData, 28)) >> 3;

            // bit: 7; mask: 0x00000080; 0 = no target frame, 1 = has target frame
            $hasFrame = (0x00000080 & self::getInt2d($recordData, 28)) >> 7;

            // bit: 8; mask: 0x00000100; 0 = file link or URL, 1 = UNC path (inc. server name)
            $isUNC = (0x00000100 & self::getInt2d($recordData, 28)) >> 8;

            // offset within record data
            $offset = 32;

            if ($hasDesc) {
                // offset: 32; size: var; character count of description text
                $dl = self::getInt4d($recordData, 32);
                // offset: 36; size: var; character array of description text, no Unicode string header, always 16-bit characters, zero terminated
                $desc = self::encodeUTF16(substr($recordData, 36, 2 * ($dl - 1)), false);
                $offset += 4 + 2 * $dl;
            }
            if ($hasFrame) {
                $fl = self::getInt4d($recordData, $offset);
                $offset += 4 + 2 * $fl;
            }

            // detect type of hyperlink (there are 4 types)
            $hyperlinkType = null;

            if ($isUNC) {
                $hyperlinkType = 'UNC';
            } elseif (!$isFileLinkOrUrl) {
                $hyperlinkType = 'workbook';
            } elseif (ord($recordData{$offset}) == 0x03) {
                $hyperlinkType = 'local';
            } elseif (ord($recordData{$offset}) == 0xE0) {
                $hyperlinkType = 'URL';
            }

            switch ($hyperlinkType) {
                case 'URL':
                    // section 5.58.2: Hyperlink containing a URL
                    // e.g. http://example.org/index.php

                    // offset: var; size: 16; GUID of URL Moniker
                    $offset += 16;
                    // offset: var; size: 4; size (in bytes) of character array of the URL including trailing zero word
                    $us = self::getInt4d($recordData, $offset);
                    $offset += 4;
                    // offset: var; size: $us; character array of the URL, no Unicode string header, always 16-bit characters, zero-terminated
                    $url = self::encodeUTF16(substr($recordData, $offset, $us - 2), false);
                    $nullOffset = strpos($url, 0x00);
                    if ($nullOffset) {
                        $url = substr($url, 0, $nullOffset);
                    }
                    $url .= $hasText ? '#' : '';
                    $offset += $us;
                    break;
                case 'local':
                    // section 5.58.3: Hyperlink to local file
                    // examples:
                    //   mydoc.txt
                    //   ../../somedoc.xls#Sheet!A1

                    // offset: var; size: 16; GUI of File Moniker
                    $offset += 16;

                    // offset: var; size: 2; directory up-level count.
                    $upLevelCount = self::getInt2d($recordData, $offset);
                    $offset += 2;

                    // offset: var; size: 4; character count of the shortened file path and name, including trailing zero word
                    $sl = self::getInt4d($recordData, $offset);
                    $offset += 4;

                    // offset: var; size: sl; character array of the shortened file path and name in 8.3-DOS-format (compressed Unicode string)
                    $shortenedFilePath = substr($recordData, $offset, $sl);
                    $shortenedFilePath = self::encodeUTF16($shortenedFilePath, true);
                    $shortenedFilePath = substr($shortenedFilePath, 0, -1); // remove trailing zero

                    $offset += $sl;

                    // offset: var; size: 24; unknown sequence
                    $offset += 24;

                    // extended file path
                    // offset: var; size: 4; size of the following file link field including string lenth mark
                    $sz = self::getInt4d($recordData, $offset);
                    $offset += 4;

                    // only present if $sz > 0
                    if ($sz > 0) {
                        // offset: var; size: 4; size of the character array of the extended file path and name
                        $xl = self::getInt4d($recordData, $offset);
                        $offset += 4;

                        // offset: var; size 2; unknown
                        $offset += 2;

                        // offset: var; size $xl; character array of the extended file path and name.
                        $extendedFilePath = substr($recordData, $offset, $xl);
                        $extendedFilePath = self::encodeUTF16($extendedFilePath, false);
                        $offset += $xl;
                    }

                    // construct the path
                    $url = str_repeat('..\\', $upLevelCount);
                    $url .= ($sz > 0) ? $extendedFilePath : $shortenedFilePath; // use extended path if available
                    $url .= $hasText ? '#' : '';

                    break;
                case 'UNC':
                    // section 5.58.4: Hyperlink to a File with UNC (Universal Naming Convention) Path
                    // todo: implement
                    return;
                case 'workbook':
                    // section 5.58.5: Hyperlink to the Current Workbook
                    // e.g. Sheet2!B1:C2, stored in text mark field
                    $url = 'sheet://';
                    break;
                default:
                    return;
            }

            if ($hasText) {
                // offset: var; size: 4; character count of text mark including trailing zero word
                $tl = self::getInt4d($recordData, $offset);
                $offset += 4;
                // offset: var; size: var; character array of the text mark without the # sign, no Unicode header, always 16-bit characters, zero-terminated
                $text = self::encodeUTF16(substr($recordData, $offset, 2 * ($tl - 1)), false);
                $url .= $text;
            }

            // apply the hyperlink to all the relevant cells
            foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cellRange) as $coordinate) {
                $this->phpSheet->getCell($coordinate)->getHyperLink()->setUrl($url);
            }
        }
    }


    /**
     * Read DATAVALIDATIONS record
     */
    private function readDataValidations()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer forward to next record
        $this->pos += 4 + $length;
    }


    /**
     * Read DATAVALIDATION record
     */
    private function readDataValidation()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer forward to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // offset: 0; size: 4; Options
        $options = self::getInt4d($recordData, 0);

        // bit: 0-3; mask: 0x0000000F; type
        $type = (0x0000000F & $options) >> 0;
        switch ($type) {
            case 0x00:
                $type = PHPExcel_Cell_DataValidation::TYPE_NONE;
                break;
            case 0x01:
                $type = PHPExcel_Cell_DataValidation::TYPE_WHOLE;
                break;
            case 0x02:
                $type = PHPExcel_Cell_DataValidation::TYPE_DECIMAL;
                break;
            case 0x03:
                $type = PHPExcel_Cell_DataValidation::TYPE_LIST;
                break;
            case 0x04:
                $type = PHPExcel_Cell_DataValidation::TYPE_DATE;
                break;
            case 0x05:
                $type = PHPExcel_Cell_DataValidation::TYPE_TIME;
                break;
            case 0x06:
                $type = PHPExcel_Cell_DataValidation::TYPE_TEXTLENGTH;
                break;
            case 0x07:
                $type = PHPExcel_Cell_DataValidation::TYPE_CUSTOM;
                break;
        }

        // bit: 4-6; mask: 0x00000070; error type
        $errorStyle = (0x00000070 & $options) >> 4;
        switch ($errorStyle) {
            case 0x00:
                $errorStyle = PHPExcel_Cell_DataValidation::STYLE_STOP;
                break;
            case 0x01:
                $errorStyle = PHPExcel_Cell_DataValidation::STYLE_WARNING;
                break;
            case 0x02:
                $errorStyle = PHPExcel_Cell_DataValidation::STYLE_INFORMATION;
                break;
        }

        // bit: 7; mask: 0x00000080; 1= formula is explicit (only applies to list)
        // I have only seen cases where this is 1
        $explicitFormula = (0x00000080 & $options) >> 7;

        // bit: 8; mask: 0x00000100; 1= empty cells allowed
        $allowBlank = (0x00000100 & $options) >> 8;

        // bit: 9; mask: 0x00000200; 1= suppress drop down arrow in list type validity
        $suppressDropDown = (0x00000200 & $options) >> 9;

        // bit: 18; mask: 0x00040000; 1= show prompt box if cell selected
        $showInputMessage = (0x00040000 & $options) >> 18;

        // bit: 19; mask: 0x00080000; 1= show error box if invalid values entered
        $showErrorMessage = (0x00080000 & $options) >> 19;

        // bit: 20-23; mask: 0x00F00000; condition operator
        $operator = (0x00F00000 & $options) >> 20;
        switch ($operator) {
            case 0x00:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_BETWEEN;
                break;
            case 0x01:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_NOTBETWEEN;
                break;
            case 0x02:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_EQUAL;
                break;
            case 0x03:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_NOTEQUAL;
                break;
            case 0x04:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_GREATERTHAN;
                break;
            case 0x05:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_LESSTHAN;
                break;
            case 0x06:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_GREATERTHANOREQUAL;
                break;
            case 0x07:
                $operator = PHPExcel_Cell_DataValidation::OPERATOR_LESSTHANOREQUAL;
                break;
        }

        // offset: 4; size: var; title of the prompt box
        $offset = 4;
        $string = self::readUnicodeStringLong(substr($recordData, $offset));
        $promptTitle = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];

        // offset: var; size: var; title of the error box
        $string = self::readUnicodeStringLong(substr($recordData, $offset));
        $errorTitle = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];

        // offset: var; size: var; text of the prompt box
        $string = self::readUnicodeStringLong(substr($recordData, $offset));
        $prompt = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];

        // offset: var; size: var; text of the error box
        $string = self::readUnicodeStringLong(substr($recordData, $offset));
        $error = $string['value'] !== chr(0) ? $string['value'] : '';
        $offset += $string['size'];

        // offset: var; size: 2; size of the formula data for the first condition
        $sz1 = self::getInt2d($recordData, $offset);
        $offset += 2;

        // offset: var; size: 2; not used
        $offset += 2;

        // offset: var; size: $sz1; formula data for first condition (without size field)
        $formula1 = substr($recordData, $offset, $sz1);
        $formula1 = pack('v', $sz1) . $formula1; // prepend the length
        try {
            $formula1 = $this->getFormulaFromStructure($formula1);

            // in list type validity, null characters are used as item separators
            if ($type == PHPExcel_Cell_DataValidation::TYPE_LIST) {
                $formula1 = str_replace(chr(0), ',', $formula1);
            }
        } catch (PHPExcel_Exception $e) {
            return;
        }
        $offset += $sz1;

        // offset: var; size: 2; size of the formula data for the first condition
        $sz2 = self::getInt2d($recordData, $offset);
        $offset += 2;

        // offset: var; size: 2; not used
        $offset += 2;

        // offset: var; size: $sz2; formula data for second condition (without size field)
        $formula2 = substr($recordData, $offset, $sz2);
        $formula2 = pack('v', $sz2) . $formula2; // prepend the length
        try {
            $formula2 = $this->getFormulaFromStructure($formula2);
        } catch (PHPExcel_Exception $e) {
            return;
        }
        $offset += $sz2;

        // offset: var; size: var; cell range address list with
        $cellRangeAddressList = $this->readBIFF8CellRangeAddressList(substr($recordData, $offset));
        $cellRangeAddresses = $cellRangeAddressList['cellRangeAddresses'];

        foreach ($cellRangeAddresses as $cellRange) {
            $stRange = $this->phpSheet->shrinkRangeToFit($cellRange);
            foreach (PHPExcel_Cell::extractAllCellReferencesInRange($stRange) as $coordinate) {
                $objValidation = $this->phpSheet->getCell($coordinate)->getDataValidation();
                $objValidation->setType($type);
                $objValidation->setErrorStyle($errorStyle);
                $objValidation->setAllowBlank((bool)$allowBlank);
                $objValidation->setShowInputMessage((bool)$showInputMessage);
                $objValidation->setShowErrorMessage((bool)$showErrorMessage);
                $objValidation->setShowDropDown(!$suppressDropDown);
                $objValidation->setOperator($operator);
                $objValidation->setErrorTitle($errorTitle);
                $objValidation->setError($error);
                $objValidation->setPromptTitle($promptTitle);
                $objValidation->setPrompt($prompt);
                $objValidation->setFormula1($formula1);
                $objValidation->setFormula2($formula2);
            }
        }
    }

    /**
     * Read SHEETLAYOUT record. Stores sheet tab color information.
     */
    private function readSheetLayout()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // local pointer in record data
        $offset = 0;

        if (!$this->readDataOnly) {
            // offset: 0; size: 2; repeated record identifier 0x0862

            // offset: 2; size: 10; not used

            // offset: 12; size: 4; size of record data
            // Excel 2003 uses size of 0x14 (documented), Excel 2007 uses size of 0x28 (not documented?)
            $sz = self::getInt4d($recordData, 12);

            switch ($sz) {
                case 0x14:
                    // offset: 16; size: 2; color index for sheet tab
                    $colorIndex = self::getInt2d($recordData, 16);
                    $color = PHPExcel_Reader_Excel5_Color::map($colorIndex, $this->palette, $this->version);
                    $this->phpSheet->getTabColor()->setRGB($color['rgb']);
                    break;
                case 0x28:
                    // TODO: Investigate structure for .xls SHEETLAYOUT record as saved by MS Office Excel 2007
                    return;
                    break;
            }
        }
    }


    /**
     * Read SHEETPROTECTION record (FEATHEADR)
     */
    private function readSheetProtection()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        if ($this->readDataOnly) {
            return;
        }

        // offset: 0; size: 2; repeated record header

        // offset: 2; size: 2; FRT cell reference flag (=0 currently)

        // offset: 4; size: 8; Currently not used and set to 0

        // offset: 12; size: 2; Shared feature type index (2=Enhanced Protetion, 4=SmartTag)
        $isf = self::getInt2d($recordData, 12);
        if ($isf != 2) {
            return;
        }

        // offset: 14; size: 1; =1 since this is a feat header

        // offset: 15; size: 4; size of rgbHdrSData

        // rgbHdrSData, assume "Enhanced Protection"
        // offset: 19; size: 2; option flags
        $options = self::getInt2d($recordData, 19);

        // bit: 0; mask 0x0001; 1 = user may edit objects, 0 = users must not edit objects
        $bool = (0x0001 & $options) >> 0;
        $this->phpSheet->getProtection()->setObjects(!$bool);

        // bit: 1; mask 0x0002; edit scenarios
        $bool = (0x0002 & $options) >> 1;
        $this->phpSheet->getProtection()->setScenarios(!$bool);

        // bit: 2; mask 0x0004; format cells
        $bool = (0x0004 & $options) >> 2;
        $this->phpSheet->getProtection()->setFormatCells(!$bool);

        // bit: 3; mask 0x0008; format columns
        $bool = (0x0008 & $options) >> 3;
        $this->phpSheet->getProtection()->setFormatColumns(!$bool);

        // bit: 4; mask 0x0010; format rows
        $bool = (0x0010 & $options) >> 4;
        $this->phpSheet->getProtection()->setFormatRows(!$bool);

        // bit: 5; mask 0x0020; insert columns
        $bool = (0x0020 & $options) >> 5;
        $this->phpSheet->getProtection()->setInsertColumns(!$bool);

        // bit: 6; mask 0x0040; insert rows
        $bool = (0x0040 & $options) >> 6;
        $this->phpSheet->getProtection()->setInsertRows(!$bool);

        // bit: 7; mask 0x0080; insert hyperlinks
        $bool = (0x0080 & $options) >> 7;
        $this->phpSheet->getProtection()->setInsertHyperlinks(!$bool);

        // bit: 8; mask 0x0100; delete columns
        $bool = (0x0100 & $options) >> 8;
        $this->phpSheet->getProtection()->setDeleteColumns(!$bool);

        // bit: 9; mask 0x0200; delete rows
        $bool = (0x0200 & $options) >> 9;
        $this->phpSheet->getProtection()->setDeleteRows(!$bool);

        // bit: 10; mask 0x0400; select locked cells
        $bool = (0x0400 & $options) >> 10;
        $this->phpSheet->getProtection()->setSelectLockedCells(!$bool);

        // bit: 11; mask 0x0800; sort cell range
        $bool = (0x0800 & $options) >> 11;
        $this->phpSheet->getProtection()->setSort(!$bool);

        // bit: 12; mask 0x1000; auto filter
        $bool = (0x1000 & $options) >> 12;
        $this->phpSheet->getProtection()->setAutoFilter(!$bool);

        // bit: 13; mask 0x2000; pivot tables
        $bool = (0x2000 & $options) >> 13;
        $this->phpSheet->getProtection()->setPivotTables(!$bool);

        // bit: 14; mask 0x4000; select unlocked cells
        $bool = (0x4000 & $options) >> 14;
        $this->phpSheet->getProtection()->setSelectUnlockedCells(!$bool);

        // offset: 21; size: 2; not used
    }


    /**
     * Read RANGEPROTECTION record
     * Reading of this record is based on Microsoft Office Excel 97-2000 Binary File Format Specification,
     * where it is referred to as FEAT record
     */
    private function readRangeProtection()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // move stream pointer to next record
        $this->pos += 4 + $length;

        // local pointer in record data
        $offset = 0;

        if (!$this->readDataOnly) {
            $offset += 12;

            // offset: 12; size: 2; shared feature type, 2 = enhanced protection, 4 = smart tag
            $isf = self::getInt2d($recordData, 12);
            if ($isf != 2) {
                // we only read FEAT records of type 2
                return;
            }
            $offset += 2;

            $offset += 5;

            // offset: 19; size: 2; count of ref ranges this feature is on
            $cref = self::getInt2d($recordData, 19);
            $offset += 2;

            $offset += 6;

            // offset: 27; size: 8 * $cref; list of cell ranges (like in hyperlink record)
            $cellRanges = array();
            for ($i = 0; $i < $cref; ++$i) {
                try {
                    $cellRange = $this->readBIFF8CellRangeAddressFixed(substr($recordData, 27 + 8 * $i, 8));
                } catch (PHPExcel_Exception $e) {
                    return;
                }
                $cellRanges[] = $cellRange;
                $offset += 8;
            }

            // offset: var; size: var; variable length of feature specific data
            $rgbFeat = substr($recordData, $offset);
            $offset += 4;

            // offset: var; size: 4; the encrypted password (only 16-bit although field is 32-bit)
            $wPassword = self::getInt4d($recordData, $offset);
            $offset += 4;

            // Apply range protection to sheet
            if ($cellRanges) {
                $this->phpSheet->protectCells(implode(' ', $cellRanges), strtoupper(dechex($wPassword)), true);
            }
        }
    }


    /**
     * Read IMDATA record
     */
    private function readImData()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);

        // get spliced record data
        $splicedRecordData = $this->getSplicedRecordData();
        $recordData = $splicedRecordData['recordData'];

        // UNDER CONSTRUCTION

        // offset: 0; size: 2; image format
        $cf = self::getInt2d($recordData, 0);

        // offset: 2; size: 2; environment from which the file was written
        $env = self::getInt2d($recordData, 2);

        // offset: 4; size: 4; length of the image data
        $lcb = self::getInt4d($recordData, 4);

        // offset: 8; size: var; image data
        $iData = substr($recordData, 8);

        switch ($cf) {
            case 0x09: // Windows bitmap format
                // BITMAPCOREINFO
                // 1. BITMAPCOREHEADER
                // offset: 0; size: 4; bcSize, Specifies the number of bytes required by the structure
                $bcSize = self::getInt4d($iData, 0);
    //            var_dump($bcSize);

                // offset: 4; size: 2; bcWidth, specifies the width of the bitmap, in pixels
                $bcWidth = self::getInt2d($iData, 4);
    //            var_dump($bcWidth);

                // offset: 6; size: 2; bcHeight, specifies the height of the bitmap, in pixels.
                $bcHeight = self::getInt2d($iData, 6);
    //            var_dump($bcHeight);
                $ih = imagecreatetruecolor($bcWidth, $bcHeight);

                // offset: 8; size: 2; bcPlanes, specifies the number of planes for the target device. This value must be 1

                // offset: 10; size: 2; bcBitCount specifies the number of bits-per-pixel. This value must be 1, 4, 8, or 24
                $bcBitCount = self::getInt2d($iData, 10);
    //            var_dump($bcBitCount);

                $rgbString = substr($iData, 12);
                $rgbTriples = array();
                while (strlen($rgbString) > 0) {
                    $rgbTriples[] = unpack('Cb/Cg/Cr', $rgbString);
                    $rgbString = substr($rgbString, 3);
                }
                $x = 0;
                $y = 0;
                foreach ($rgbTriples as $i => $rgbTriple) {
                    $color = imagecolorallocate($ih, $rgbTriple['r'], $rgbTriple['g'], $rgbTriple['b']);
                    imagesetpixel($ih, $x, $bcHeight - 1 - $y, $color);
                    $x = ($x + 1) % $bcWidth;
                    $y = $y + floor(($x + 1) / $bcWidth);
                }
                //imagepng($ih, 'image.png');

                $drawing = new PHPExcel_Worksheet_Drawing();
                $drawing->setPath($filename);
                $drawing->setWorksheet($this->phpSheet);
                break;
            case 0x02: // Windows metafile or Macintosh PICT format
            case 0x0e: // native format
            default:
                break;
        }

        // getSplicedRecordData() takes care of moving current position in data stream
    }


    /**
     * Read a free CONTINUE record. Free CONTINUE record may be a camouflaged MSODRAWING record
     * When MSODRAWING data on a sheet exceeds 8224 bytes, CONTINUE records are used instead. Undocumented.
     * In this case, we must treat the CONTINUE record as a MSODRAWING record
     */
    private function readContinue()
    {
        $length = self::getInt2d($this->data, $this->pos + 2);
        $recordData = $this->readRecordData($this->data, $this->pos + 4, $length);

        // check if we are reading drawing data
        // this is in case a free CONTINUE record occurs in other circumstances we are unaware of
        if ($this->drawingData == '') {
            // move stream pointer to next record
            $this->pos += 4 + $length;

            return;
        }

        // check if record data is at least 4 bytes long, otherwise there is no chance this is MSODRAWING data
        if ($length < 4) {
            // move stream pointer to next record
            $this->pos += 4 + $length;

            return;
        }

        // dirty check to see if CONTINUE record could be a camouflaged MSODRAWING record
        // look inside CONTINUE record to see if it looks like a part of an Escher stream
        // we know that Escher stream may be split at least at
        //        0xF003 MsofbtSpgrContainer
        //        0xF004 MsofbtSpContainer
        //        0xF00D MsofbtClientTextbox
        $validSplitPoints = array(0xF003, 0xF004, 0xF00D); // add identifiers if we find more

        $splitPoint = self::getInt2d($recordData, 2);
        if (in_array($splitPoint, $validSplitPoints)) {
            // get spliced record data (and move pointer to next record)
            $splicedRecordData = $this->getSplicedRecordData();
            $this->drawingData .= $splicedRecordData['recordData'];

            return;
        }

        // move stream pointer to next record
        $this->pos += 4 + $length;
    }


    /**
     * Reads a record from current position in data stream and continues reading data as long as CONTINUE
     * records are found. Splices the record data pieces and returns the combined string as if record data
     * is in one piece.
     * Moves to next current position in data stream to start of next record different from a CONtINUE record
     *
     * @return array
     */
    private function getSplicedRecordData()
    {
        $data = '';
        $spliceOffsets = array();

        $i = 0;
        $spliceOffsets[0] = 0;

        do {
            ++$i;

            // offset: 0; size: 2; identifier
            $identifier = self::getInt2d($this->data, $this->pos);
            // offset: 2; size: 2; length
            $length = self::getInt2d($this->data, $this->pos + 2);
            $data .= $this->readRecordData($this->data, $this->pos + 4, $length);

            $spliceOffsets[$i] = $spliceOffsets[$i - 1] + $length;

            $this->pos += 4 + $length;
            $nextIdentifier = self::getInt2d($this->data, $this->pos);
        } while ($nextIdentifier == self::XLS_TYPE_CONTINUE);

        $splicedData = array(
            'recordData' => $data,
            'spliceOffsets' => $spliceOffsets,
        );

        return $splicedData;

    }


    /**
     * Convert formula structure into human readable Excel formula like 'A3+A5*5'
     *
     * @param string $formulaStructure The complete binary data for the formula
     * @param string $baseCell Base cell, only needed when formula contains tRefN tokens, e.g. with shared formulas
     * @return string Human readable formula
     */
    private function getFormulaFromStructure($formulaStructure, $baseCell = 'A1')
    {
        // offset: 0; size: 2; size of the following formula data
        $sz = self::getInt2d($formulaStructure, 0);

        // offset: 2; size: sz
        $formulaData = substr($formulaStructure, 2, $sz);

        // for debug: dump the formula data
        //echo '<xmp>';
        //echo 'size: ' . $sz . "\n";
        //echo 'the entire formula data: ';
        //Debug::dump($formulaData);
        //echo "\n----\n";

        // offset: 2 + sz; size: variable (optional)
        if (strlen($formulaStructure) > 2 + $sz) {
            $additionalData = substr($formulaStructure, 2 + $sz);

            // for debug: dump the additional data
            //echo 'the entire additional data: ';
            //Debug::dump($additionalData);
            //echo "\n----\n";
        } else {
            $additionalData = '';
        }

        return $this->getFormulaFromData($formulaData, $additionalData, $baseCell);
    }


    /**
     * Take formula data and additional data for formula and return human readable formula
     *
     * @param string $formulaData The binary data for the formula itself
     * @param string $additionalData Additional binary data going with the formula
     * @param string $baseCell Base cell, only needed when formula contains tRefN tokens, e.g. with shared formulas
     * @return string Human readable formula
     */
    private function getFormulaFromData($formulaData, $additionalData = '', $baseCell = 'A1')
    {
        // start parsing the formula data
        $tokens = array();

        while (strlen($formulaData) > 0 and $token = $this->getNextToken($formulaData, $baseCell)) {
            $tokens[] = $token;
            $formulaData = substr($formulaData, $token['size']);

            // for debug: dump the token
            //var_dump($token);
        }

        $formulaString = $this->createFormulaFromTokens($tokens, $additionalData);

        return $formulaString;
    }


    /**
     * Take array of tokens together with additional data for formula and return human readable formula
     *
     * @param array $tokens
     * @param array $additionalData Additional binary data going with the formula
     * @param string $baseCell Base cell, only needed when formula contains tRefN tokens, e.g. with shared formulas
     * @return string Human readable formula
     */
    private function createFormulaFromTokens($tokens, $additionalData)
    {
        // empty formula?
        if (empty($tokens)) {
            return '';
        }

        $formulaStrings = array();
        foreach ($tokens as $token) {
            // initialize spaces
            $space0 = isset($space0) ? $space0 : ''; // spaces before next token, not tParen
            $space1 = isset($space1) ? $space1 : ''; // carriage returns before next token, not tParen
            $space2 = isset($space2) ? $space2 : ''; // spaces before opening parenthesis
            $space3 = isset($space3) ? $space3 : ''; // carriage returns before opening parenthesis
            $space4 = isset($space4) ? $space4 : ''; // spaces before closing parenthesis
            $space5 = isset($space5) ? $space5 : ''; // carriage returns before closing parenthesis

            switch ($token['name']) {
                case 'tAdd': // addition
                case 'tConcat': // addition
                case 'tDiv': // division
                case 'tEQ': // equality
                case 'tGE': // greater than or equal
                case 'tGT': // greater than
                case 'tIsect': // intersection
                case 'tLE': // less than or equal
                case 'tList': // less than or equal
                case 'tLT': // less than
                case 'tMul': // multiplication
                case 'tNE': // multiplication
                case 'tPower': // power
                case 'tRange': // range
                case 'tSub': // subtraction
                    $op2 = array_pop($formulaStrings);
                    $op1 = array_pop($formulaStrings);
                    $formulaStrings[] = "$op1$space1$space0{$token['data']}$op2";
                    unset($space0, $space1);
                    break;
                case 'tUplus': // unary plus
                case 'tUminus': // unary minus
                    $op = array_pop($formulaStrings);
                    $formulaStrings[] = "$space1$space0{$token['data']}$op";
                    unset($space0, $space1);
                    break;
                case 'tPercent': // percent sign
                    $op = array_pop($formulaStrings);
                    $formulaStrings[] = "$op$space1$space0{$token['data']}";
                    unset($space0, $space1);
                    break;
                case 'tAttrVolatile': // indicates volatile function
                case 'tAttrIf':
                case 'tAttrSkip':
                case 'tAttrChoose':
                    // token is only important for Excel formula evaluator
                    // do nothing
                    break;
                case 'tAttrSpace': // space / carriage return
                    // space will be used when next token arrives, do not alter formulaString stack
                    switch ($token['data']['spacetype']) {
                        case 'type0':
                            $space0 = str_repeat(' ', $token['data']['spacecount']);
                            break;
                        case 'type1':
                            $space1 = str_repeat("\n", $token['data']['spacecount']);
                            break;
                        case 'type2':
                            $space2 = str_repeat(' ', $token['data']['spacecount']);
                            break;
                        case 'type3':
                            $space3 = str_repeat("\n", $token['data']['spacecount']);
                            break;
                        case 'type4':
                            $space4 = str_repeat(' ', $token['data']['spacecount']);
                            break;
                        case 'type5':
                            $space5 = str_repeat("\n", $token['data']['spacecount']);
                            break;
                    }
                    break;
                case 'tAttrSum': // SUM function with one parameter
                    $op = array_pop($formulaStrings);
                    $formulaStrings[] = "{$space1}{$space0}SUM($op)";
                    unset($space0, $space1);
                    break;
                case 'tFunc': // function with fixed number of arguments
                case 'tFuncV': // function with variable number of arguments
                    if ($token['data']['function'] != '') {
                        // normal function
                        $ops = array(); // array of operators
                        for ($i = 0; $i < $token['data']['args']; ++$i) {
                            $ops[] = array_pop($formulaStrings);
                        }
                        $ops = array_reverse($ops);
                        $formulaStrings[] = "$space1$space0{$token['data']['function']}(" . implode(',', $ops) . ")";
                        unset($space0, $space1);
                    } else {
                        // add-in function
                        $ops = array(); // array of operators
                        for ($i = 0; $i < $token['data']['args'] - 1; ++$i) {
                            $ops[] = array_pop($formulaStrings);
                        }
                        $ops = array_reverse($ops);
                        $function = array_pop($formulaStrings);
                        $formulaStrings[] = "$space1$space0$function(" . implode(',', $ops) . ")";
                        unset($space0, $space1);
                    }
                    break;
                case 'tParen': // parenthesis
                    $expression = array_pop($formulaStrings);
                    $formulaStrings[] = "$space3$space2($expression$space5$space4)";
                    unset($space2, $space3, $space4, $space5);
                    break;
                case 'tArray': // array constant
                    $constantArray = self::readBIFF8ConstantArray($additionalData);
                    $formulaStrings[] = $space1 . $space0 . $constantArray['value'];
                    $additionalData = substr($additionalData, $constantArray['size']); // bite of chunk of additional data
                    unset($space0, $space1);
                    break;
                case 'tMemArea':
                    // bite off chunk of additional data
                    $cellRangeAddressList = $this->readBIFF8CellRangeAddressList($additionalData);
                    $additionalData = substr($additionalData, $cellRangeAddressList['size']);
                    $formulaStrings[] = "$space1$space0{$token['data']}";
                    unset($space0, $space1);
                    break;
                case 'tArea': // cell range address
                case 'tBool': // boolean
                case 'tErr': // error code
                case 'tInt': // integer
                case 'tMemErr':
                case 'tMemFunc':
                case 'tMissArg':
                case 'tName':
                case 'tNameX':
                case 'tNum': // number
                case 'tRef': // single cell reference
                case 'tRef3d': // 3d cell reference
                case 'tArea3d': // 3d cell range reference
                case 'tRefN':
                case 'tAreaN':
                case 'tStr': // string
                    $formulaStrings[] = "$space1$space0{$token['data']}";
                    unset($space0, $space1);
                    break;
            }
        }
        $formulaString = $formulaStrings[0];

        // for debug: dump the human readable formula
        //echo '----' . "\n";
        //echo 'Formula: ' . $formulaString;

        return $formulaString;
    }


    /**
     * Fetch next token from binary formula data
     *
     * @param string Formula data
     * @param string $baseCell Base cell, only needed when formula contains tRefN tokens, e.g. with shared formulas
     * @return array
     * @throws PHPExcel_Reader_Exception
     */
    private function getNextToken($formulaData, $baseCell = 'A1')
    {
        // offset: 0; size: 1; token id
        $id = ord($formulaData[0]); // token id
        $name = false; // initialize token name

        switch ($id) {
            case 0x03:
                $name = 'tAdd';
                $size = 1;
                $data = '+';
                break;
            case 0x04:
                $name = 'tSub';
                $size = 1;
                $data = '-';
                break;
            case 0x05:
                $name = 'tMul';
                $size = 1;
                $data = '*';
                break;
            case 0x06:
                $name = 'tDiv';
                $size = 1;
                $data = '/';
                break;
            case 0x07:
                $name = 'tPower';
                $size = 1;
                $data = '^';
                break;
            case 0x08:
                $name = 'tConcat';
                $size = 1;
                $data = '&';
                break;
            case 0x09:
                $name = 'tLT';
                $size = 1;
                $data = '<';
                break;
            case 0x0A:
                $name = 'tLE';
                $size = 1;
                $data = '<=';
                break;
            case 0x0B:
                $name = 'tEQ';
                $size = 1;
                $data = '=';
                break;
            case 0x0C:
                $name = 'tGE';
                $size = 1;
                $data = '>=';
                break;
            case 0x0D:
                $name = 'tGT';
                $size = 1;
                $data = '>';
                break;
            case 0x0E:
                $name = 'tNE';
                $size = 1;
                $data = '<>';
                break;
            case 0x0F:
                $name = 'tIsect';
                $size = 1;
                $data = ' ';
                break;
            case 0x10:
                $name = 'tList';
                $size = 1;
                $data = ',';
                break;
            case 0x11:
                $name = 'tRange';
                $size = 1;
                $data = ':';
                break;
            case 0x12:
                $name = 'tUplus';
                $size = 1;
                $data = '+';
                break;
            case 0x13:
                $name = 'tUminus';
                $size = 1;
                $data = '-';
                break;
            case 0x14:
                $name = 'tPercent';
                $size = 1;
                $data = '%';
                break;
            case 0x15:    //    parenthesis
                $name  = 'tParen';
                $size  = 1;
                $data = null;
                break;
            case 0x16:    //    missing argument
                $name = 'tMissArg';
                $size = 1;
                $data = '';
                break;
            case 0x17:    //    string
                $name = 'tStr';
                // offset: 1; size: var; Unicode string, 8-bit string length
                $string = self::readUnicodeStringShort(substr($formulaData, 1));
                $size = 1 + $string['size'];
                $data = self::UTF8toExcelDoubleQuoted($string['value']);
                break;
            case 0x19:    //    Special attribute
                // offset: 1; size: 1; attribute type flags:
                switch (ord($formulaData[1])) {
                    case 0x01:
                        $name = 'tAttrVolatile';
                        $size = 4;
                        $data = null;
                        break;
                    case 0x02:
                        $name = 'tAttrIf';
                        $size = 4;
                        $data = null;
                        break;
                    case 0x04:
                        $name = 'tAttrChoose';
                        // offset: 2; size: 2; number of choices in the CHOOSE function ($nc, number of parameters decreased by 1)
                        $nc = self::getInt2d($formulaData, 2);
                        // offset: 4; size: 2 * $nc
                        // offset: 4 + 2 * $nc; size: 2
                        $size = 2 * $nc + 6;
                        $data = null;
                        break;
                    case 0x08:
                        $name = 'tAttrSkip';
                        $size = 4;
                        $data = null;
                        break;
                    case 0x10:
                        $name = 'tAttrSum';
                        $size = 4;
                        $data = null;
                        break;
                    case 0x40:
                    case 0x41:
                        $name = 'tAttrSpace';
                        $size = 4;
                        // offset: 2; size: 2; space type and position
                        switch (ord($formulaData[2])) {
                            case 0x00:
                                $spacetype = 'type0';
                                break;
                            case 0x01:
                                $spacetype = 'type1';
                                break;
                            case 0x02:
                                $spacetype = 'type2';
                                break;
                            case 0x03:
                                $spacetype = 'type3';
                                break;
                            case 0x04:
                                $spacetype = 'type4';
                                break;
                            case 0x05:
                                $spacetype = 'type5';
                                break;
                            default:
                                throw new PHPExcel_Reader_Exception('Unrecognized space type in tAttrSpace token');
                                break;
                        }
                        // offset: 3; size: 1; number of inserted spaces/carriage returns
                        $spacecount = ord($formulaData[3]);

                        $data = array('spacetype' => $spacetype, 'spacecount' => $spacecount);
                        break;
                    default:
                        throw new PHPExcel_Reader_Exception('Unrecognized attribute flag in tAttr token');
                        break;
                }
                break;
            case 0x1C:    //    error code
                // offset: 1; size: 1; error code
                $name = 'tErr';
                $size = 2;
                $data = PHPExcel_Reader_Excel5_ErrorCode::lookup(ord($formulaData[1]));
                break;
            case 0x1D:    //    boolean
                // offset: 1; size: 1; 0 = false, 1 = true;
                $name = 'tBool';
                $size = 2;
                $data = ord($formulaData[1]) ? 'TRUE' : 'FALSE';
                break;
            case 0x1E:    //    integer
                // offset: 1; size: 2; unsigned 16-bit integer
                $name = 'tInt';
                $size = 3;
                $data = self::getInt2d($formulaData, 1);
                break;
            case 0x1F:    //    number
                // offset: 1; size: 8;
                $name = 'tNum';
                $size = 9;
                $data = self::extractNumber(substr($formulaData, 1));
                $data = str_replace(',', '.', (string)$data); // in case non-English locale
                break;
            case 0x20:    //    array constant
            case 0x40:
            case 0x60:
                // offset: 1; size: 7; not used
                $name = 'tArray';
                $size = 8;
                $data = null;
                break;
            case 0x21:    //    function with fixed number of arguments
            case 0x41:
            case 0x61:
                $name = 'tFunc';
                $size = 3;
                // offset: 1; size: 2; index to built-in sheet function
                switch (self::getInt2d($formulaData, 1)) {
                    case 2:
                        $function = 'ISNA';
                        $args = 1;
                        break;
                    case 3:
                        $function = 'ISERROR';
                        $args = 1;
                        break;
                    case 10:
                        $function = 'NA';
                        $args = 0;
                        break;
                    case 15:
                        $function = 'SIN';
                        $args = 1;
                        break;
                    case 16:
                        $function = 'COS';
                        $args = 1;
                        break;
                    case 17:
                        $function = 'TAN';
                        $args = 1;
                        break;
                    case 18:
                        $function = 'ATAN';
                        $args = 1;
                        break;
                    case 19:
                        $function = 'PI';
                        $args = 0;
                        break;
                    case 20:
                        $function = 'SQRT';
                        $args = 1;
                        break;
                    case 21:
                        $function = 'EXP';
                        $args = 1;
                        break;
                    case 22:
                        $function = 'LN';
                        $args = 1;
                        break;
                    case 23:
                        $function = 'LOG10';
                        $args = 1;
                        break;
                    case 24:
                        $function = 'ABS';
                        $args = 1;
                        break;
                    case 25:
                        $function = 'INT';
                        $args = 1;
                        break;
                    case 26:
                        $function = 'SIGN';
                        $args = 1;
                        break;
                    case 27:
                        $function = 'ROUND';
                        $args = 2;
                        break;
                    case 30:
                        $function = 'REPT';
                        $args = 2;
                        break;
                    case 31:
                        $function = 'MID';
                        $args = 3;
                        break;
                    case 32:
                        $function = 'LEN';
                        $args = 1;
                        break;
                    case 33:
                        $function = 'VALUE';
                        $args = 1;
                        break;
                    case 34:
                        $function = 'TRUE';
                        $args = 0;
                        break;
                    case 35:
                        $function = 'FALSE';
                        $args = 0;
                        break;
                    case 38:
                        $function = 'NOT';
                        $args = 1;
                        break;
                    case 39:
                        $function = 'MOD';
                        $args = 2;
                        break;
                    case 40:
                        $function = 'DCOUNT';
                        $args = 3;
                        break;
                    case 41:
                        $function = 'DSUM';
                        $args = 3;
                        break;
                    case 42:
                        $function = 'DAVERAGE';
                        $args = 3;
                        break;
                    case 43:
                        $function = 'DMIN';
                        $args = 3;
                        break;
                    case 44:
                        $function = 'DMAX';
                        $args = 3;
                        break;
                    case 45:
                        $function = 'DSTDEV';
                        $args = 3;
                        break;
                    case 48:
                        $function = 'TEXT';
                        $args = 2;
                        break;
                    case 61:
                        $function = 'MIRR';
                        $args = 3;
                        break;
                    case 63:
                        $function = 'RAND';
                        $args = 0;
                        break;
                    case 65:
                        $function = 'DATE';
                        $args = 3;
                        break;
                    case 66:
                        $function = 'TIME';
                        $args = 3;
                        break;
                    case 67:
                        $function = 'DAY';
                        $args = 1;
                        break;
                    case 68:
                        $function = 'MONTH';
                        $args = 1;
                        break;
                    case 69:
                        $function = 'YEAR';
                        $args = 1;
                        break;
                    case 71:
                        $function = 'HOUR';
                        $args = 1;
                        break;
                    case 72:
                        $function = 'MINUTE';
                        $args = 1;
                        break;
                    case 73:
                        $function = 'SECOND';
                        $args = 1;
                        break;
                    case 74:
                        $function = 'NOW';
                        $args = 0;
                        break;
                    case 75:
                        $function = 'AREAS';
                        $args = 1;
                        break;
                    case 76:
                        $function = 'ROWS';
                        $args = 1;
                        break;
                    case 77:
                        $function = 'COLUMNS';
                        $args = 1;
                        break;
                    case 83:
                        $function = 'TRANSPOSE';
                        $args = 1;
                        break;
                    case 86:
                        $function = 'TYPE';
                        $args = 1;
                        break;
                    case 97:
                        $function = 'ATAN2';
                        $args = 2;
                        break;
                    case 98:
                        $function = 'ASIN';
                        $args = 1;
                        break;
                    case 99:
                        $function = 'ACOS';
                        $args = 1;
                        break;
                    case 105:
                        $function = 'ISREF';
                        $args = 1;
                        break;
                    case 111:
                        $function = 'CHAR';
                        $args = 1;
                        break;
                    case 112:
                        $function = 'LOWER';
                        $args = 1;
                        break;
                    case 113:
                        $function = 'UPPER';
                        $args = 1;
                        break;
                    case 114:
                        $function = 'PROPER';
                        $args = 1;
                        break;
                    case 117:
                        $function = 'EXACT';
                        $args = 2;
                        break;
                    case 118:
                        $function = 'TRIM';
                        $args = 1;
                        break;
                    case 119:
                        $function = 'REPLACE';
                        $args = 4;
                        break;
                    case 121:
                        $function = 'CODE';
                        $args = 1;
                        break;
                    case 126:
                        $function = 'ISERR';
                        $args = 1;
                        break;
                    case 127:
                        $function = 'ISTEXT';
                        $args = 1;
                        break;
                    case 128:
                        $function = 'ISNUMBER';
                        $args = 1;
                        break;
                    case 129:
                        $function = 'ISBLANK';
                        $args = 1;
                        break;
                    case 130:
                        $function = 'T';
                        $args = 1;
                        break;
                    case 131:
                        $function = 'N';
                        $args = 1;
                        break;
                    case 140:
                        $function = 'DATEVALUE';
                        $args = 1;
                        break;
                    case 141:
                        $function = 'TIMEVALUE';
                        $args = 1;
                        break;
                    case 142:
                        $function = 'SLN';
                        $args = 3;
                        break;
                    case 143:
                        $function = 'SYD';
                        $args = 4;
                        break;
                    case 162:
                        $function = 'CLEAN';
                        $args = 1;
                        break;
                    case 163:
                        $function = 'MDETERM';
                        $args = 1;
                        break;
                    case 164:
                        $function = 'MINVERSE';
                        $args = 1;
                        break;
                    case 165:
                        $function = 'MMULT';
                        $args = 2;
                        break;
                    case 184:
                        $function = 'FACT';
                        $args = 1;
                        break;
                    case 189:
                        $function = 'DPRODUCT';
                        $args = 3;
                        break;
                    case 190:
                        $function = 'ISNONTEXT';
                        $args = 1;
                        break;
                    case 195:
                        $function = 'DSTDEVP';
                        $args = 3;
                        break;
                    case 196:
                        $function = 'DVARP';
                        $args = 3;
                        break;
                    case 198:
                        $function = 'ISLOGICAL';
                        $args = 1;
                        break;
                    case 199:
                        $function = 'DCOUNTA';
                        $args = 3;
                        break;
                    case 207:
                        $function = 'REPLACEB';
                        $args = 4;
                        break;
                    case 210:
                        $function = 'MIDB';
                        $args = 3;
                        break;
                    case 211:
                        $function = 'LENB';
                        $args = 1;
                        break;
                    case 212:
                        $function = 'ROUNDUP';
                        $args = 2;
                        break;
                    case 213:
                        $function = 'ROUNDDOWN';
                        $args = 2;
                        break;
                    case 214:
                        $function = 'ASC';
                        $args = 1;
                        break;
                    case 215:
                        $function = 'DBCS';
                        $args = 1;
                        break;
                    case 221:
                        $function = 'TODAY';
                        $args = 0;
                        break;
                    case 229:
                        $function = 'SINH';
                        $args = 1;
                        break;
                    case 230:
                        $function = 'COSH';
                        $args = 1;
                        break;
                    case 231:
                        $function = 'TANH';
                        $args = 1;
                        break;
                    case 232:
                        $function = 'ASINH';
                        $args = 1;
                        break;
                    case 233:
                        $function = 'ACOSH';
                        $args = 1;
                        break;
                    case 234:
                        $function = 'ATANH';
                        $args = 1;
                        break;
                    case 235:
                        $function = 'DGET';
                        $args = 3;
                        break;
                    case 244:
                        $function = 'INFO';
                        $args = 1;
                        break;
                    case 252:
                        $function = 'FREQUENCY';
                        $args = 2;
                        break;
                    case 261:
                        $function = 'ERROR.TYPE';
                        $args = 1;
                        break;
                    case 271:
                        $function = 'GAMMALN';
                        $args = 1;
                        break;
                    case 273:
                        $function = 'BINOMDIST';
                        $args = 4;
                        break;
                    case 274:
                        $function = 'CHIDIST';
                        $args = 2;
                        break;
                    case 275:
                        $function = 'CHIINV';
                        $args = 2;
                        break;
                    case 276:
                        $function = 'COMBIN';
                        $args = 2;
                        break;
                    case 277:
                        $function = 'CONFIDENCE';
                        $args = 3;
                        break;
                    case 278:
                        $function = 'CRITBINOM';
                        $args = 3;
                        break;
                    case 279:
                        $function = 'EVEN';
                        $args = 1;
                        break;
                    case 280:
                        $function = 'EXPONDIST';
                        $args = 3;
                        break;
                    case 281:
                        $function = 'FDIST';
                        $args = 3;
                        break;
                    case 282:
                        $function = 'FINV';
                        $args = 3;
                        break;
                    case 283:
                        $function = 'FISHER';
                        $args = 1;
                        break;
                    case 284:
                        $function = 'FISHERINV';
                        $args = 1;
                        break;
                    case 285:
                        $function = 'FLOOR';
                        $args = 2;
                        break;
                    case 286:
                        $function = 'GAMMADIST';
                        $args = 4;
                        break;
                    case 287:
                        $function = 'GAMMAINV';
                        $args = 3;
                        break;
                    case 288:
                        $function = 'CEILING';
                        $args = 2;
                        break;
                    case 289:
                        $function = 'HYPGEOMDIST';
                        $args = 4;
                        break;
                    case 290:
                        $function = 'LOGNORMDIST';
                        $args = 3;
                        break;
                    case 291:
                        $function = 'LOGINV';
                        $args = 3;
                        break;
                    case 292:
                        $function = 'NEGBINOMDIST';
                        $args = 3;
                        break;
                    case 293:
                        $function = 'NORMDIST';
                        $args = 4;
                        break;
                    case 294:
                        $function = 'NORMSDIST';
                        $args = 1;
                        break;
                    case 295:
                        $function = 'NORMINV';
                        $args = 3;
                        break;
                    case 296:
                        $function = 'NORMSINV';
                        $args = 1;
                        break;
                    case 297:
                        $function = 'STANDARDIZE';
                        $args = 3;
                        break;
                    case 298:
                        $function = 'ODD';
                        $args = 1;
                        break;
                    case 299:
                        $function = 'PERMUT';
                        $args = 2;
                        break;
                    case 300:
                        $function = 'POISSON';
                        $args = 3;
                        break;
                    case 301:
                        $function = 'TDIST';
                        $args = 3;
                        break;
                    case 302:
                        $function = 'WEIBULL';
                        $args = 4;
                        break;
                    case 303:
                        $function = 'SUMXMY2';
                        $args = 2;
                        break;
                    case 304:
                        $function = 'SUMX2MY2';
                        $args = 2;
                        break;
                    case 305:
                        $function = 'SUMX2PY2';
                        $args = 2;
                        break;
                    case 306:
                        $function = 'CHITEST';
                        $args = 2;
                        break;
                    case 307:
                        $function = 'CORREL';
                        $args = 2;
                        break;
                    case 308:
                        $function = 'COVAR';
                        $args = 2;
                        break;
                    case 309:
                        $function = 'FORECAST';
                        $args = 3;
                        break;
                    case 310:
                        $function = 'FTEST';
                        $args = 2;
                        break;
                    case 311:
                        $function = 'INTERCEPT';
                        $args = 2;
                        break;
                    case 312:
                        $function = 'PEARSON';
                        $args = 2;
                        break;
                    case 313:
                        $function = 'RSQ';
                        $args = 2;
                        break;
                    case 314:
                        $function = 'STEYX';
                        $args = 2;
                        break;
                    case 315:
                        $function = 'SLOPE';
                        $args = 2;
                        break;
                    case 316:
                        $function = 'TTEST';
                        $args = 4;
                        break;
                    case 325:
                        $function = 'LARGE';
                        $args = 2;
                        break;
                    case 326:
                        $function = 'SMALL';
                        $args = 2;
                        break;
                    case 327:
                        $function = 'QUARTILE';
                        $args = 2;
                        break;
                    case 328:
                        $function = 'PERCENTILE';
                        $args = 2;
                        break;
                    case 331:
                        $function = 'TRIMMEAN';
                        $args = 2;
                        break;
                    case 332:
                        $function = 'TINV';
                        $args = 2;
                        break;
                    case 337:
                        $function = 'POWER';
                        $args = 2;
                        break;
                    case 342:
                        $function = 'RADIANS';
                        $args = 1;
                        break;
                    case 343:
                        $function = 'DEGREES';
                        $args = 1;
                        break;
                    case 346:
                        $function = 'COUNTIF';
                        $args = 2;
                        break;
                    case 347:
                        $function = 'COUNTBLANK';
                        $args = 1;
                        break;
                    case 350:
                        $function = 'ISPMT';
                        $args = 4;
                        break;
                    case 351:
                        $function = 'DATEDIF';
                        $args = 3;
                        break;
                    case 352:
                        $function = 'DATESTRING';
                        $args = 1;
                        break;
                    case 353:
                        $function = 'NUMBERSTRING';
                        $args = 2;
                        break;
                    case 360:
                        $function = 'PHONETIC';
                        $args = 1;
                        break;
                    case 368:
                        $function = 'BAHTTEXT';
                        $args = 1;
                        break;
                    default:
                        throw new PHPExcel_Reader_Exception('Unrecognized function in formula');
                        break;
                }
                $data = array('function' => $function, 'args' => $args);
                break;
            case 0x22:    //    function with variable number of arguments
            case 0x42:
            case 0x62:
                $name = 'tFuncV';
                $size = 4;
                // offset: 1; size: 1; number of arguments
                $args = ord($formulaData[1]);
                // offset: 2: size: 2; index to built-in sheet function
                $index = self::getInt2d($formulaData, 2);
                switch ($index) {
                    case 0:
                        $function = 'COUNT';
                        break;
                    case 1:
                        $function = 'IF';
                        break;
                    case 4:
                        $function = 'SUM';
                        break;
                    case 5:
                        $function = 'AVERAGE';
                        break;
                    case 6:
                        $function = 'MIN';
                        break;
                    case 7:
                        $function = 'MAX';
                        break;
                    case 8:
                        $function = 'ROW';
                        break;
                    case 9:
                        $function = 'COLUMN';
                        break;
                    case 11:
                        $function = 'NPV';
                        break;
                    case 12:
                        $function = 'STDEV';
                        break;
                    case 13:
                        $function = 'DOLLAR';
                        break;
                    case 14:
                        $function = 'FIXED';
                        break;
                    case 28:
                        $function = 'LOOKUP';
                        break;
                    case 29:
                        $function = 'INDEX';
                        break;
                    case 36:
                        $function = 'AND';
                        break;
                    case 37:
                        $function = 'OR';
                        break;
                    case 46:
                        $function = 'VAR';
                        break;
                    case 49:
                        $function = 'LINEST';
                        break;
                    case 50:
                        $function = 'TREND';
                        break;
                    case 51:
                        $function = 'LOGEST';
                        break;
                    case 52:
                        $function = 'GROWTH';
                        break;
                    case 56:
                        $function = 'PV';
                        break;
                    case 57:
                        $function = 'FV';
                        break;
                    case 58:
                        $function = 'NPER';
                        break;
                    case 59:
                        $function = 'PMT';
                        break;
                    case 60:
                        $function = 'RATE';
                        break;
                    case 62:
                        $function = 'IRR';
                        break;
                    case 64:
                        $function = 'MATCH';
                        break;
                    case 70:
                        $function = 'WEEKDAY';
                        break;
                    case 78:
                        $function = 'OFFSET';
                        break;
                    case 82:
                        $function = 'SEARCH';
                        break;
                    case 100:
                        $function = 'CHOOSE';
                        break;
                    case 101:
                        $function = 'HLOOKUP';
                        break;
                    case 102:
                        $function = 'VLOOKUP';
                        break;
                    case 109:
                        $function = 'LOG';
                        break;
                    case 115:
                        $function = 'LEFT';
                        break;
                    case 116:
                        $function = 'RIGHT';
                        break;
                    case 120:
                        $function = 'SUBSTITUTE';
                        break;
                    case 124:
                        $function = 'FIND';
                        break;
                    case 125:
                        $function = 'CELL';
                        break;
                    case 144:
                        $function = 'DDB';
                        break;
                    case 148:
                        $function = 'INDIRECT';
                        break;
                    case 167:
                        $function = 'IPMT';
                        break;
                    case 168:
                        $function = 'PPMT';
                        break;
                    case 169:
                        $function = 'COUNTA';
                        break;
                    case 183:
                        $function = 'PRODUCT';
                        break;
                    case 193:
                        $function = 'STDEVP';
                        break;
                    case 194:
                        $function = 'VARP';
                        break;
                    case 197:
                        $function = 'TRUNC';
                        break;
                    case 204:
                        $function = 'USDOLLAR';
                        break;
                    case 205:
                        $function = 'FINDB';
                        break;
                    case 206:
                        $function = 'SEARCHB';
                        break;
                    case 208:
                        $function = 'LEFTB';
                        break;
                    case 209:
                        $function = 'RIGHTB';
                        break;
                    case 216:
                        $function = 'RANK';
                        break;
                    case 219:
                        $function = 'ADDRESS';
                        break;
                    case 220:
                        $function = 'DAYS360';
                        break;
                    case 222:
                        $function = 'VDB';
                        break;
                    case 227:
                        $function = 'MEDIAN';
                        break;
                    case 228:
                        $function = 'SUMPRODUCT';
                        break;
                    case 247:
                        $function = 'DB';
                        break;
                    case 255:
                        $function = '';
                        break;
                    case 269:
                        $function = 'AVEDEV';
                        break;
                    case 270:
                        $function = 'BETADIST';
                        break;
                    case 272:
                        $function = 'BETAINV';
                        break;
                    case 317:
                        $function = 'PROB';
                        break;
                    case 318:
                        $function = 'DEVSQ';
                        break;
                    case 319:
                        $function = 'GEOMEAN';
                        break;
                    case 320:
                        $function = 'HARMEAN';
                        break;
                    case 321:
                        $function = 'SUMSQ';
                        break;
                    case 322:
                        $function = 'KURT';
                        break;
                    case 323:
                        $function = 'SKEW';
                        break;
                    case 324:
                        $function = 'ZTEST';
                        break;
                    case 329:
                        $function = 'PERCENTRANK';
                        break;
                    case 330:
                        $function = 'MODE';
                        break;
                    case 336:
                        $function = 'CONCATENATE';
                        break;
                    case 344:
                        $function = 'SUBTOTAL';
                        break;
                    case 345:
                        $function = 'SUMIF';
                        break;
                    case 354:
                        $function = 'ROMAN';
                        break;
                    case 358:
                        $function = 'GETPIVOTDATA';
                        break;
                    case 359:
                        $function = 'HYPERLINK';
                        break;
                    case 361:
                        $function = 'AVERAGEA';
                        break;
                    case 362:
                        $function = 'MAXA';
                        break;
                    case 363:
                        $function = 'MINA';
                        break;
                    case 364:
                        $function = 'STDEVPA';
                        break;
                    case 365:
                        $function = 'VARPA';
                        break;
                    case 366:
                        $function = 'STDEVA';
                        break;
                    case 367:
                        $function = 'VARA';
                        break;
                    default:
                        throw new PHPExcel_Reader_Exception('Unrecognized function in formula');
                        break;
                }
                $data = array('function' => $function, 'args' => $args);
                break;
            case 0x23:    //    index to defined name
            case 0x43:
            case 0x63:
                $name = 'tName';
                $size = 5;
                // offset: 1; size: 2; one-based index to definedname record
                $definedNameIndex = self::getInt2d($formulaData, 1) - 1;
                // offset: 2; size: 2; not used
                $data = $this->definedname[$definedNameIndex]['name'];
                break;
            case 0x24:    //    single cell reference e.g. A5
            case 0x44:
            case 0x64:
                $name = 'tRef';
                $size = 5;
                $data = $this->readBIFF8CellAddress(substr($formulaData, 1, 4));
                break;
            case 0x25:    //    cell range reference to cells in the same sheet (2d)
            case 0x45:
            case 0x65:
                $name = 'tArea';
                $size = 9;
                $data = $this->readBIFF8CellRangeAddress(substr($formulaData, 1, 8));
                break;
            case 0x26:    //    Constant reference sub-expression
            case 0x46:
            case 0x66:
                $name = 'tMemArea';
                // offset: 1; size: 4; not used
                // offset: 5; size: 2; size of the following subexpression
                $subSize = self::getInt2d($formulaData, 5);
                $size = 7 + $subSize;
                $data = $this->getFormulaFromData(substr($formulaData, 7, $subSize));
                break;
            case 0x27:    //    Deleted constant reference sub-expression
            case 0x47:
            case 0x67:
                $name = 'tMemErr';
                // offset: 1; size: 4; not used
                // offset: 5; size: 2; size of the following subexpression
                $subSize = self::getInt2d($formulaData, 5);
                $size = 7 + $subSize;
                $data = $this->getFormulaFromData(substr($formulaData, 7, $subSize));
                break;
            case 0x29:    //    Variable reference sub-expression
            case 0x49:
            case 0x69:
                $name = 'tMemFunc';
                // offset: 1; size: 2; size of the following sub-expression
                $subSize = self::getInt2d($formulaData, 1);
                $size = 3 + $subSize;
                $data = $this->getFormulaFromData(substr($formulaData, 3, $subSize));
                break;
            case 0x2C: // Relative 2d cell reference reference, used in shared formulas and some other places
            case 0x4C:
            case 0x6C:
                $name = 'tRefN';
                $size = 5;
                $data = $this->readBIFF8CellAddressB(substr($formulaData, 1, 4), $baseCell);
                break;
            case 0x2D:    //    Relative 2d range reference
            case 0x4D:
            case 0x6D:
                $name = 'tAreaN';
                $size = 9;
                $data = $this->readBIFF8CellRangeAddressB(substr($formulaData, 1, 8), $baseCell);
                break;
            case 0x39:    //    External name
            case 0x59:
            case 0x79:
                $name = 'tNameX';
                $size = 7;
                // offset: 1; size: 2; index to REF entry in EXTERNSHEET record
                // offset: 3; size: 2; one-based index to DEFINEDNAME or EXTERNNAME record
                $index = self::getInt2d($formulaData, 3);
                // assume index is to EXTERNNAME record
                $data = $this->externalNames[$index - 1]['name'];
                // offset: 5; size: 2; not used
                break;
            case 0x3A:    //    3d reference to cell
            case 0x5A:
            case 0x7A:
                $name = 'tRef3d';
                $size = 7;

                try {
                    // offset: 1; size: 2; index to REF entry
                    $sheetRange = $this->readSheetRangeByRefIndex(self::getInt2d($formulaData, 1));
                    // offset: 3; size: 4; cell address
                    $cellAddress = $this->readBIFF8CellAddress(substr($formulaData, 3, 4));

                    $data = "$sheetRange!$cellAddress";
                } catch (PHPExcel_Exception $e) {
                    // deleted sheet reference
                    $data = '#REF!';
                }
                break;
            case 0x3B:    //    3d reference to cell range
            case 0x5B:
            case 0x7B:
                $name = 'tArea3d';
                $size = 11;

                try {
                    // offset: 1; size: 2; index to REF entry
                    $sheetRange = $this->readSheetRangeByRefIndex(self::getInt2d($formulaData, 1));
                    // offset: 3; size: 8; cell address
                    $cellRangeAddress = $this->readBIFF8CellRangeAddress(substr($formulaData, 3, 8));

                    $data = "$sheetRange!$cellRangeAddress";
                } catch (PHPExcel_Exception $e) {
                    // deleted sheet reference
                    $data = '#REF!';
                }
                break;
            // Unknown cases    // don't know how to deal with
            default:
                throw new PHPExcel_Reader_Exception('Unrecognized token ' . sprintf('%02X', $id) . ' in formula');
                break;
        }

        return array(
            'id' => $id,
            'name' => $name,
            'size' => $size,
            'data' => $data,
        );
    }


    /**
     * Reads a cell address in BIFF8 e.g. 'A2' or '$A$2'
     * section 3.3.4
     *
     * @param string $cellAddressStructure
     * @return string
     */
    private function readBIFF8CellAddress($cellAddressStructure)
    {
        // offset: 0; size: 2; index to row (0... 65535) (or offset (-32768... 32767))
        $row = self::getInt2d($cellAddressStructure, 0) + 1;

        // offset: 2; size: 2; index to column or column offset + relative flags
        // bit: 7-0; mask 0x00FF; column index
        $column = PHPExcel_Cell::stringFromColumnIndex(0x00FF & self::getInt2d($cellAddressStructure, 2));

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getInt2d($cellAddressStructure, 2))) {
            $column = '$' . $column;
        }
        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getInt2d($cellAddressStructure, 2))) {
            $row = '$' . $row;
        }

        return $column . $row;
    }


    /**
     * Reads a cell address in BIFF8 for shared formulas. Uses positive and negative values for row and column
     * to indicate offsets from a base cell
     * section 3.3.4
     *
     * @param string $cellAddressStructure
     * @param string $baseCell Base cell, only needed when formula contains tRefN tokens, e.g. with shared formulas
     * @return string
     */
    private function readBIFF8CellAddressB($cellAddressStructure, $baseCell = 'A1')
    {
        list($baseCol, $baseRow) = PHPExcel_Cell::coordinateFromString($baseCell);
        $baseCol = PHPExcel_Cell::columnIndexFromString($baseCol) - 1;

        // offset: 0; size: 2; index to row (0... 65535) (or offset (-32768... 32767))
        $rowIndex = self::getInt2d($cellAddressStructure, 0);
        $row = self::getInt2d($cellAddressStructure, 0) + 1;

        // offset: 2; size: 2; index to column or column offset + relative flags
        // bit: 7-0; mask 0x00FF; column index
        $colIndex = 0x00FF & self::getInt2d($cellAddressStructure, 2);

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getInt2d($cellAddressStructure, 2))) {
            $column = PHPExcel_Cell::stringFromColumnIndex($colIndex);
            $column = '$' . $column;
        } else {
            $colIndex = ($colIndex <= 127) ? $colIndex : $colIndex - 256;
            $column = PHPExcel_Cell::stringFromColumnIndex($baseCol + $colIndex);
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getInt2d($cellAddressStructure, 2))) {
            $row = '$' . $row;
        } else {
            $rowIndex = ($rowIndex <= 32767) ? $rowIndex : $rowIndex - 65536;
            $row = $baseRow + $rowIndex;
        }

        return $column . $row;
    }


    /**
     * Reads a cell range address in BIFF5 e.g. 'A2:B6' or 'A1'
     * always fixed range
     * section 2.5.14
     *
     * @param string $subData
     * @return string
     * @throws PHPExcel_Reader_Exception
     */
    private function readBIFF5CellRangeAddressFixed($subData)
    {
        // offset: 0; size: 2; index to first row
        $fr = self::getInt2d($subData, 0) + 1;

        // offset: 2; size: 2; index to last row
        $lr = self::getInt2d($subData, 2) + 1;

        // offset: 4; size: 1; index to first column
        $fc = ord($subData{4});

        // offset: 5; size: 1; index to last column
        $lc = ord($subData{5});

        // check values
        if ($fr > $lr || $fc > $lc) {
            throw new PHPExcel_Reader_Exception('Not a cell range address');
        }

        // column index to letter
        $fc = PHPExcel_Cell::stringFromColumnIndex($fc);
        $lc = PHPExcel_Cell::stringFromColumnIndex($lc);

        if ($fr == $lr and $fc == $lc) {
            return "$fc$fr";
        }
        return "$fc$fr:$lc$lr";
    }


    /**
     * Reads a cell range address in BIFF8 e.g. 'A2:B6' or 'A1'
     * always fixed range
     * section 2.5.14
     *
     * @param string $subData
     * @return string
     * @throws PHPExcel_Reader_Exception
     */
    private function readBIFF8CellRangeAddressFixed($subData)
    {
        // offset: 0; size: 2; index to first row
        $fr = self::getInt2d($subData, 0) + 1;

        // offset: 2; size: 2; index to last row
        $lr = self::getInt2d($subData, 2) + 1;

        // offset: 4; size: 2; index to first column
        $fc = self::getInt2d($subData, 4);

        // offset: 6; size: 2; index to last column
        $lc = self::getInt2d($subData, 6);

        // check values
        if ($fr > $lr || $fc > $lc) {
            throw new PHPExcel_Reader_Exception('Not a cell range address');
        }

        // column index to letter
        $fc = PHPExcel_Cell::stringFromColumnIndex($fc);
        $lc = PHPExcel_Cell::stringFromColumnIndex($lc);

        if ($fr == $lr and $fc == $lc) {
            return "$fc$fr";
        }
        return "$fc$fr:$lc$lr";
    }


    /**
     * Reads a cell range address in BIFF8 e.g. 'A2:B6' or '$A$2:$B$6'
     * there are flags indicating whether column/row index is relative
     * section 3.3.4
     *
     * @param string $subData
     * @return string
     */
    private function readBIFF8CellRangeAddress($subData)
    {
        // todo: if cell range is just a single cell, should this funciton
        // not just return e.g. 'A1' and not 'A1:A1' ?

        // offset: 0; size: 2; index to first row (0... 65535) (or offset (-32768... 32767))
            $fr = self::getInt2d($subData, 0) + 1;

        // offset: 2; size: 2; index to last row (0... 65535) (or offset (-32768... 32767))
            $lr = self::getInt2d($subData, 2) + 1;

        // offset: 4; size: 2; index to first column or column offset + relative flags

        // bit: 7-0; mask 0x00FF; column index
        $fc = PHPExcel_Cell::stringFromColumnIndex(0x00FF & self::getInt2d($subData, 4));

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getInt2d($subData, 4))) {
            $fc = '$' . $fc;
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getInt2d($subData, 4))) {
            $fr = '$' . $fr;
        }

        // offset: 6; size: 2; index to last column or column offset + relative flags

        // bit: 7-0; mask 0x00FF; column index
        $lc = PHPExcel_Cell::stringFromColumnIndex(0x00FF & self::getInt2d($subData, 6));

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getInt2d($subData, 6))) {
            $lc = '$' . $lc;
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getInt2d($subData, 6))) {
            $lr = '$' . $lr;
        }

        return "$fc$fr:$lc$lr";
    }


    /**
     * Reads a cell range address in BIFF8 for shared formulas. Uses positive and negative values for row and column
     * to indicate offsets from a base cell
     * section 3.3.4
     *
     * @param string $subData
     * @param string $baseCell Base cell
     * @return string Cell range address
     */
    private function readBIFF8CellRangeAddressB($subData, $baseCell = 'A1')
    {
        list($baseCol, $baseRow) = PHPExcel_Cell::coordinateFromString($baseCell);
        $baseCol = PHPExcel_Cell::columnIndexFromString($baseCol) - 1;

        // TODO: if cell range is just a single cell, should this funciton
        // not just return e.g. 'A1' and not 'A1:A1' ?

        // offset: 0; size: 2; first row
        $frIndex = self::getInt2d($subData, 0); // adjust below

        // offset: 2; size: 2; relative index to first row (0... 65535) should be treated as offset (-32768... 32767)
        $lrIndex = self::getInt2d($subData, 2); // adjust below

        // offset: 4; size: 2; first column with relative/absolute flags

        // bit: 7-0; mask 0x00FF; column index
        $fcIndex = 0x00FF & self::getInt2d($subData, 4);

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getInt2d($subData, 4))) {
            // absolute column index
            $fc = PHPExcel_Cell::stringFromColumnIndex($fcIndex);
            $fc = '$' . $fc;
        } else {
            // column offset
            $fcIndex = ($fcIndex <= 127) ? $fcIndex : $fcIndex - 256;
            $fc = PHPExcel_Cell::stringFromColumnIndex($baseCol + $fcIndex);
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getInt2d($subData, 4))) {
            // absolute row index
            $fr = $frIndex + 1;
            $fr = '$' . $fr;
        } else {
            // row offset
            $frIndex = ($frIndex <= 32767) ? $frIndex : $frIndex - 65536;
            $fr = $baseRow + $frIndex;
        }

        // offset: 6; size: 2; last column with relative/absolute flags

        // bit: 7-0; mask 0x00FF; column index
        $lcIndex = 0x00FF & self::getInt2d($subData, 6);
        $lcIndex = ($lcIndex <= 127) ? $lcIndex : $lcIndex - 256;
        $lc = PHPExcel_Cell::stringFromColumnIndex($baseCol + $lcIndex);

        // bit: 14; mask 0x4000; (1 = relative column index, 0 = absolute column index)
        if (!(0x4000 & self::getInt2d($subData, 6))) {
            // absolute column index
            $lc = PHPExcel_Cell::stringFromColumnIndex($lcIndex);
            $lc = '$' . $lc;
        } else {
            // column offset
            $lcIndex = ($lcIndex <= 127) ? $lcIndex : $lcIndex - 256;
            $lc = PHPExcel_Cell::stringFromColumnIndex($baseCol + $lcIndex);
        }

        // bit: 15; mask 0x8000; (1 = relative row index, 0 = absolute row index)
        if (!(0x8000 & self::getInt2d($subData, 6))) {
            // absolute row index
            $lr = $lrIndex + 1;
            $lr = '$' . $lr;
        } else {
            // row offset
            $lrIndex = ($lrIndex <= 32767) ? $lrIndex : $lrIndex - 65536;
            $lr = $baseRow + $lrIndex;
        }

        return "$fc$fr:$lc$lr";
    }


    /**
     * Read BIFF8 cell range address list
     * section 2.5.15
     *
     * @param string $subData
     * @return array
     */
    private function readBIFF8CellRangeAddressList($subData)
    {
        $cellRangeAddresses = array();

        // offset: 0; size: 2; number of the following cell range addresses
        $nm = self::getInt2d($subData, 0);

        $offset = 2;
        // offset: 2; size: 8 * $nm; list of $nm (fixed) cell range addresses
        for ($i = 0; $i < $nm; ++$i) {
            $cellRangeAddresses[] = $this->readBIFF8CellRangeAddressFixed(substr($subData, $offset, 8));
            $offset += 8;
        }

        return array(
            'size' => 2 + 8 * $nm,
            'cellRangeAddresses' => $cellRangeAddresses,
        );
    }


    /**
     * Read BIFF5 cell range address list
     * section 2.5.15
     *
     * @param string $subData
     * @return array
     */
    private function readBIFF5CellRangeAddressList($subData)
    {
        $cellRangeAddresses = array();

        // offset: 0; size: 2; number of the following cell range addresses
        $nm = self::getInt2d($subData, 0);

        $offset = 2;
        // offset: 2; size: 6 * $nm; list of $nm (fixed) cell range addresses
        for ($i = 0; $i < $nm; ++$i) {
            $cellRangeAddresses[] = $this->readBIFF5CellRangeAddressFixed(substr($subData, $offset, 6));
            $offset += 6;
        }

        return array(
            'size' => 2 + 6 * $nm,
            'cellRangeAddresses' => $cellRangeAddresses,
        );
    }


    /**
     * Get a sheet range like Sheet1:Sheet3 from REF index
     * Note: If there is only one sheet in the range, one gets e.g Sheet1
     * It can also happen that the REF structure uses the -1 (FFFF) code to indicate deleted sheets,
     * in which case an PHPExcel_Reader_Exception is thrown
     *
     * @param int $index
     * @return string|false
     * @throws PHPExcel_Reader_Exception
     */
    private function readSheetRangeByRefIndex($index)
    {
        if (isset($this->ref[$index])) {
            $type = $this->externalBooks[$this->ref[$index]['externalBookIndex']]['type'];

            switch ($type) {
                case 'internal':
                    // check if we have a deleted 3d reference
                    if ($this->ref[$index]['firstSheetIndex'] == 0xFFFF or $this->ref[$index]['lastSheetIndex'] == 0xFFFF) {
                        throw new PHPExcel_Reader_Exception('Deleted sheet reference');
                    }

                    // we have normal sheet range (collapsed or uncollapsed)
                    $firstSheetName = $this->sheets[$this->ref[$index]['firstSheetIndex']]['name'];
                    $lastSheetName = $this->sheets[$this->ref[$index]['lastSheetIndex']]['name'];

                    if ($firstSheetName == $lastSheetName) {
                        // collapsed sheet range
                        $sheetRange = $firstSheetName;
                    } else {
                        $sheetRange = "$firstSheetName:$lastSheetName";
                    }

                    // escape the single-quotes
                    $sheetRange = str_replace("'", "''", $sheetRange);

                    // if there are special characters, we need to enclose the range in single-quotes
                    // todo: check if we have identified the whole set of special characters
                    // it seems that the following characters are not accepted for sheet names
                    // and we may assume that they are not present: []*/:\?
                    if (preg_match("/[ !\"@#£$%&{()}<>=+'|^,;-]/", $sheetRange)) {
                        $sheetRange = "'$sheetRange'";
                    }

                    return $sheetRange;
                    break;
                default:
                    // TODO: external sheet support
                    throw new PHPExcel_Reader_Exception('Excel5 reader only supports internal sheets in fomulas');
                    break;
            }
        }
        return false;
    }


    /**
     * read BIFF8 constant value array from array data
     * returns e.g. array('value' => '{1,2;3,4}', 'size' => 40}
     * section 2.5.8
     *
     * @param string $arrayData
     * @return array
     */
    private static function readBIFF8ConstantArray($arrayData)
    {
        // offset: 0; size: 1; number of columns decreased by 1
        $nc = ord($arrayData[0]);

        // offset: 1; size: 2; number of rows decreased by 1
        $nr = self::getInt2d($arrayData, 1);
        $size = 3; // initialize
        $arrayData = substr($arrayData, 3);

        // offset: 3; size: var; list of ($nc + 1) * ($nr + 1) constant values
        $matrixChunks = array();
        for ($r = 1; $r <= $nr + 1; ++$r) {
            $items = array();
            for ($c = 1; $c <= $nc + 1; ++$c) {
                $constant = self::readBIFF8Constant($arrayData);
                $items[] = $constant['value'];
                $arrayData = substr($arrayData, $constant['size']);
                $size += $constant['size'];
            }
            $matrixChunks[] = implode(',', $items); // looks like e.g. '1,"hello"'
        }
        $matrix = '{' . implode(';', $matrixChunks) . '}';

        return array(
            'value' => $matrix,
            'size' => $size,
        );
    }


    /**
     * read BIFF8 constant value which may be 'Empty Value', 'Number', 'String Value', 'Boolean Value', 'Error Value'
     * section 2.5.7
     * returns e.g. array('value' => '5', 'size' => 9)
     *
     * @param string $valueData
     * @return array
     */
    private static function readBIFF8Constant($valueData)
    {
        // offset: 0; size: 1; identifier for type of constant
        $identifier = ord($valueData[0]);

        switch ($identifier) {
            case 0x00: // empty constant (what is this?)
                $value = '';
                $size = 9;
                break;
            case 0x01: // number
                // offset: 1; size: 8; IEEE 754 floating-point value
                $value = self::extractNumber(substr($valueData, 1, 8));
                $size = 9;
                break;
            case 0x02: // string value
                // offset: 1; size: var; Unicode string, 16-bit string length
                $string = self::readUnicodeStringLong(substr($valueData, 1));
                $value = '"' . $string['value'] . '"';
                $size = 1 + $string['size'];
                break;
            case 0x04: // boolean
                // offset: 1; size: 1; 0 = FALSE, 1 = TRUE
                if (ord($valueData[1])) {
                    $value = 'TRUE';
                } else {
                    $value = 'FALSE';
                }
                $size = 9;
                break;
            case 0x10: // error code
                // offset: 1; size: 1; error code
                $value = PHPExcel_Reader_Excel5_ErrorCode::lookup(ord($valueData[1]));
                $size = 9;
                break;
        }
        return array(
            'value' => $value,
            'size' => $size,
        );
    }


    /**
     * Extract RGB color
     * OpenOffice.org's Documentation of the Microsoft Excel File Format, section 2.5.4
     *
     * @param string $rgb Encoded RGB value (4 bytes)
     * @return array
     */
    private static function readRGB($rgb)
    {
        // offset: 0; size 1; Red component
        $r = ord($rgb{0});

        // offset: 1; size: 1; Green component
        $g = ord($rgb{1});

        // offset: 2; size: 1; Blue component
        $b = ord($rgb{2});

        // HEX notation, e.g. 'FF00FC'
        $rgb = sprintf('%02X%02X%02X', $r, $g, $b);

        return array('rgb' => $rgb);
    }


    /**
     * Read byte string (8-bit string length)
     * OpenOffice documentation: 2.5.2
     *
     * @param string $subData
     * @return array
     */
    private function readByteStringShort($subData)
    {
        // offset: 0; size: 1; length of the string (character count)
        $ln = ord($subData[0]);

        // offset: 1: size: var; character array (8-bit characters)
        $value = $this->decodeCodepage(substr($subData, 1, $ln));

        return array(
            'value' => $value,
            'size' => 1 + $ln, // size in bytes of data structure
        );
    }


    /**
     * Read byte string (16-bit string length)
     * OpenOffice documentation: 2.5.2
     *
     * @param string $subData
     * @return array
     */
    private function readByteStringLong($subData)
    {
        // offset: 0; size: 2; length of the string (character count)
        $ln = self::getInt2d($subData, 0);

        // offset: 2: size: var; character array (8-bit characters)
        $value = $this->decodeCodepage(substr($subData, 2));

        //return $string;
        return array(
            'value' => $value,
            'size' => 2 + $ln, // size in bytes of data structure
        );
    }


    /**
     * Extracts an Excel Unicode short string (8-bit string length)
     * OpenOffice documentation: 2.5.3
     * function will automatically find out where the Unicode string ends.
     *
     * @param string $subData
     * @return array
     */
    private static function readUnicodeStringShort($subData)
    {
        $value = '';

        // offset: 0: size: 1; length of the string (character count)
        $characterCount = ord($subData[0]);

        $string = self::readUnicodeString(substr($subData, 1), $characterCount);

        // add 1 for the string length
        $string['size'] += 1;

        return $string;
    }


    /**
     * Extracts an Excel Unicode long string (16-bit string length)
     * OpenOffice documentation: 2.5.3
     * this function is under construction, needs to support rich text, and Asian phonetic settings
     *
     * @param string $subData
     * @return array
     */
    private static function readUnicodeStringLong($subData)
    {
        $value = '';

        // offset: 0: size: 2; length of the string (character count)
        $characterCount = self::getInt2d($subData, 0);

        $string = self::readUnicodeString(substr($subData, 2), $characterCount);

        // add 2 for the string length
        $string['size'] += 2;

        return $string;
    }


    /**
     * Read Unicode string with no string length field, but with known character count
     * this function is under construction, needs to support rich text, and Asian phonetic settings
     * OpenOffice.org's Documentation of the Microsoft Excel File Format, section 2.5.3
     *
     * @param string $subData
     * @param int $characterCount
     * @return array
     */
    private static function readUnicodeString($subData, $characterCount)
    {
        $value = '';

        // offset: 0: size: 1; option flags
        // bit: 0; mask: 0x01; character compression (0 = compressed 8-bit, 1 = uncompressed 16-bit)
        $isCompressed = !((0x01 & ord($subData[0])) >> 0);

        // bit: 2; mask: 0x04; Asian phonetic settings
        $hasAsian = (0x04) & ord($subData[0]) >> 2;

        // bit: 3; mask: 0x08; Rich-Text settings
        $hasRichText = (0x08) & ord($subData[0]) >> 3;

        // offset: 1: size: var; character array
        // this offset assumes richtext and Asian phonetic settings are off which is generally wrong
        // needs to be fixed
        $value = self::encodeUTF16(substr($subData, 1, $isCompressed ? $characterCount : 2 * $characterCount), $isCompressed);

        return array(
            'value' => $value,
            'size' => $isCompressed ? 1 + $characterCount : 1 + 2 * $characterCount, // the size in bytes including the option flags
        );
    }


    /**
     * Convert UTF-8 string to string surounded by double quotes. Used for explicit string tokens in formulas.
     * Example:  hello"world  -->  "hello""world"
     *
     * @param string $value UTF-8 encoded string
     * @return string
     */
    private static function UTF8toExcelDoubleQuoted($value)
    {
        return '"' . str_replace('"', '""', $value) . '"';
    }


    /**
     * Reads first 8 bytes of a string and return IEEE 754 float
     *
     * @param string $data Binary string that is at least 8 bytes long
     * @return float
     */
    private static function extractNumber($data)
    {
        $rknumhigh = self::getInt4d($data, 4);
        $rknumlow = self::getInt4d($data, 0);
        $sign = ($rknumhigh & 0x80000000) >> 31;
        $exp = (($rknumhigh & 0x7ff00000) >> 20) - 1023;
        $mantissa = (0x100000 | ($rknumhigh & 0x000fffff));
        $mantissalow1 = ($rknumlow & 0x80000000) >> 31;
        $mantissalow2 = ($rknumlow & 0x7fffffff);
        $value = $mantissa / pow(2, (20 - $exp));

        if ($mantissalow1 != 0) {
            $value += 1 / pow(2, (21 - $exp));
        }

        $value += $mantissalow2 / pow(2, (52 - $exp));
        if ($sign) {
            $value *= -1;
        }

        return $value;
    }


    private static function getIEEE754($rknum)
    {
        if (($rknum & 0x02) != 0) {
            $value = $rknum >> 2;
        } else {
            // changes by mmp, info on IEEE754 encoding from
            // research.microsoft.com/~hollasch/cgindex/coding/ieeefloat.html
            // The RK format calls for using only the most significant 30 bits
            // of the 64 bit floating point value. The other 34 bits are assumed
            // to be 0 so we use the upper 30 bits of $rknum as follows...
            $sign = ($rknum & 0x80000000) >> 31;
            $exp = ($rknum & 0x7ff00000) >> 20;
            $mantissa = (0x100000 | ($rknum & 0x000ffffc));
            $value = $mantissa / pow(2, (20- ($exp - 1023)));
            if ($sign) {
                $value = -1 * $value;
            }
            //end of changes by mmp
        }
        if (($rknum & 0x01) != 0) {
            $value /= 100;
        }
        return $value;
    }


    /**
     * Get UTF-8 string from (compressed or uncompressed) UTF-16 string
     *
     * @param string $string
     * @param bool $compressed
     * @return string
     */
    private static function encodeUTF16($string, $compressed = '')
    {
        if ($compressed) {
            $string = self::uncompressByteString($string);
        }

        return PHPExcel_Shared_String::ConvertEncoding($string, 'UTF-8', 'UTF-16LE');
    }

    /**
     * Convert UTF-16 string in compressed notation to uncompressed form. Only used for BIFF8.
     *
     * @param string $string
     * @return string
     */
    private static function uncompressByteString($string)
    {
        $uncompressedString = '';
        $strLen = strlen($string);
        for ($i = 0; $i < $strLen; ++$i) {
            $uncompressedString .= $string[$i] . "\0";
        }

        return $uncompressedString;
    }

    /**
     * Convert string to UTF-8. Only used for BIFF5.
     *
     * @param string $string
     * @return string
     */
    private function decodeCodepage($string)
    {
        return PHPExcel_Shared_String::ConvertEncoding($string, 'UTF-8', $this->codepage);
    }

    /**
     * Read 16-bit unsigned integer
     *
     * @param string $data
     * @param int $pos
     * @return int
     */
    public static function getInt2d($data, $pos)
    {
        return ord($data[$pos]) | (ord($data[$pos+1]) << 8);
    }

    /**
     * Read 32-bit signed integer
     *
     * @param string $data
     * @param int $pos
     * @return int
     */
    public static function getInt4d($data, $pos)
    {
        // FIX: represent numbers correctly on 64-bit system
        // http://sourceforge.net/tracker/index.php?func=detail&aid=1487372&group_id=99160&atid=623334
        // Hacked by Andreas Rehm 2006 to ensure correct result of the <<24 block on 32 and 64bit systems
        $_or_24 = ord($data[$pos + 3]);
        if ($_or_24 >= 128) {
            // negative number
            $_ord_24 = -abs((256 - $_or_24) << 24);
        } else {
            $_ord_24 = ($_or_24 & 127) << 24;
        }
        return ord($data[$pos]) | (ord($data[$pos+1]) << 8) | (ord($data[$pos+2]) << 16) | $_ord_24;
    }

    private function parseRichText($is = '')
    {
        $value = new PHPExcel_RichText();
        $value->createText($is);

        return $value;
    }
}
