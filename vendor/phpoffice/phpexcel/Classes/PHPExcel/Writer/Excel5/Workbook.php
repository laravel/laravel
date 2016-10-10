<?php

/**
 * PHPExcel_Writer_Excel5_Workbook
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

// Original file header of PEAR::Spreadsheet_Excel_Writer_Workbook (used as the base for this class):
// -----------------------------------------------------------------------------------------
// /*
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
class PHPExcel_Writer_Excel5_Workbook extends PHPExcel_Writer_Excel5_BIFFwriter
{
    /**
     * Formula parser
     *
     * @var PHPExcel_Writer_Excel5_Parser
     */
    private $parser;

    /**
     * The BIFF file size for the workbook.
     * @var integer
     * @see calcSheetOffsets()
     */
    private $biffSize;

    /**
     * XF Writers
     * @var PHPExcel_Writer_Excel5_Xf[]
     */
    private $xfWriters = array();

    /**
     * Array containing the colour palette
     * @var array
     */
    private $palette;

    /**
     * The codepage indicates the text encoding used for strings
     * @var integer
     */
    private $codepage;

    /**
     * The country code used for localization
     * @var integer
     */
    private $countryCode;

    /**
     * Workbook
     * @var PHPExcel
     */
    private $phpExcel;

    /**
     * Fonts writers
     *
     * @var PHPExcel_Writer_Excel5_Font[]
     */
    private $fontWriters = array();

    /**
     * Added fonts. Maps from font's hash => index in workbook
     *
     * @var array
     */
    private $addedFonts = array();

    /**
     * Shared number formats
     *
     * @var array
     */
    private $numberFormats = array();

    /**
     * Added number formats. Maps from numberFormat's hash => index in workbook
     *
     * @var array
     */
    private $addedNumberFormats = array();

    /**
     * Sizes of the binary worksheet streams
     *
     * @var array
     */
    private $worksheetSizes = array();

    /**
     * Offsets of the binary worksheet streams relative to the start of the global workbook stream
     *
     * @var array
     */
    private $worksheetOffsets = array();

    /**
     * Total number of shared strings in workbook
     *
     * @var int
     */
    private $stringTotal;

    /**
     * Number of unique shared strings in workbook
     *
     * @var int
     */
    private $stringUnique;

    /**
     * Array of unique shared strings in workbook
     *
     * @var array
     */
    private $stringTable;

    /**
     * Color cache
     */
    private $colors;

    /**
     * Escher object corresponding to MSODRAWINGGROUP
     *
     * @var PHPExcel_Shared_Escher
     */
    private $escher;


    /**
     * Class constructor
     *
     * @param PHPExcel    $phpExcel        The Workbook
     * @param int        &$str_total        Total number of strings
     * @param int        &$str_unique    Total number of unique strings
     * @param array        &$str_table        String Table
     * @param array        &$colors        Colour Table
     * @param mixed        $parser            The formula parser created for the Workbook
     */
    public function __construct(PHPExcel $phpExcel = null, &$str_total, &$str_unique, &$str_table, &$colors, $parser)
    {
        // It needs to call its parent's constructor explicitly
        parent::__construct();

        $this->parser        = $parser;
        $this->biffSize     = 0;
        $this->palette      = array();
        $this->countryCode = -1;

        $this->stringTotal      = &$str_total;
        $this->stringUnique     = &$str_unique;
        $this->stringTable      = &$str_table;
        $this->colors        = &$colors;
        $this->setPaletteXl97();

        $this->phpExcel = $phpExcel;

        // set BIFFwriter limit for CONTINUE records
        //        $this->_limit = 8224;
        $this->codepage = 0x04B0;

        // Add empty sheets and Build color cache
        $countSheets = $phpExcel->getSheetCount();
        for ($i = 0; $i < $countSheets; ++$i) {
            $phpSheet = $phpExcel->getSheet($i);

            $this->parser->setExtSheet($phpSheet->getTitle(), $i);  // Register worksheet name with parser

            $supbook_index = 0x00;
            $ref = pack('vvv', $supbook_index, $i, $i);
            $this->parser->references[] = $ref;  // Register reference with parser

            // Sheet tab colors?
            if ($phpSheet->isTabColorSet()) {
                $this->addColor($phpSheet->getTabColor()->getRGB());
            }
        }

    }

    /**
     * Add a new XF writer
     *
     * @param PHPExcel_Style
     * @param boolean Is it a style XF?
     * @return int Index to XF record
     */
    public function addXfWriter($style, $isStyleXf = false)
    {
        $xfWriter = new PHPExcel_Writer_Excel5_Xf($style);
        $xfWriter->setIsStyleXf($isStyleXf);

        // Add the font if not already added
        $fontIndex = $this->addFont($style->getFont());

        // Assign the font index to the xf record
        $xfWriter->setFontIndex($fontIndex);

        // Background colors, best to treat these after the font so black will come after white in custom palette
        $xfWriter->setFgColor($this->addColor($style->getFill()->getStartColor()->getRGB()));
        $xfWriter->setBgColor($this->addColor($style->getFill()->getEndColor()->getRGB()));
        $xfWriter->setBottomColor($this->addColor($style->getBorders()->getBottom()->getColor()->getRGB()));
        $xfWriter->setTopColor($this->addColor($style->getBorders()->getTop()->getColor()->getRGB()));
        $xfWriter->setRightColor($this->addColor($style->getBorders()->getRight()->getColor()->getRGB()));
        $xfWriter->setLeftColor($this->addColor($style->getBorders()->getLeft()->getColor()->getRGB()));
        $xfWriter->setDiagColor($this->addColor($style->getBorders()->getDiagonal()->getColor()->getRGB()));

        // Add the number format if it is not a built-in one and not already added
        if ($style->getNumberFormat()->getBuiltInFormatCode() === false) {
            $numberFormatHashCode = $style->getNumberFormat()->getHashCode();

            if (isset($this->addedNumberFormats[$numberFormatHashCode])) {
                $numberFormatIndex = $this->addedNumberFormats[$numberFormatHashCode];
            } else {
                $numberFormatIndex = 164 + count($this->numberFormats);
                $this->numberFormats[$numberFormatIndex] = $style->getNumberFormat();
                $this->addedNumberFormats[$numberFormatHashCode] = $numberFormatIndex;
            }
        } else {
            $numberFormatIndex = (int) $style->getNumberFormat()->getBuiltInFormatCode();
        }

        // Assign the number format index to xf record
        $xfWriter->setNumberFormatIndex($numberFormatIndex);

        $this->xfWriters[] = $xfWriter;

        $xfIndex = count($this->xfWriters) - 1;
        return $xfIndex;
    }

    /**
     * Add a font to added fonts
     *
     * @param PHPExcel_Style_Font $font
     * @return int Index to FONT record
     */
    public function addFont(PHPExcel_Style_Font $font)
    {
        $fontHashCode = $font->getHashCode();
        if (isset($this->addedFonts[$fontHashCode])) {
            $fontIndex = $this->addedFonts[$fontHashCode];
        } else {
            $countFonts = count($this->fontWriters);
            $fontIndex = ($countFonts < 4) ? $countFonts : $countFonts + 1;

            $fontWriter = new PHPExcel_Writer_Excel5_Font($font);
            $fontWriter->setColorIndex($this->addColor($font->getColor()->getRGB()));
            $this->fontWriters[] = $fontWriter;

            $this->addedFonts[$fontHashCode] = $fontIndex;
        }
        return $fontIndex;
    }

    /**
     * Alter color palette adding a custom color
     *
     * @param string $rgb E.g. 'FF00AA'
     * @return int Color index
     */
    private function addColor($rgb)
    {
        if (!isset($this->colors[$rgb])) {
            if (count($this->colors) < 57) {
                // then we add a custom color altering the palette
                $colorIndex = 8 + count($this->colors);
                $this->palette[$colorIndex] =
                    array(
                        hexdec(substr($rgb, 0, 2)),
                        hexdec(substr($rgb, 2, 2)),
                        hexdec(substr($rgb, 4)),
                        0
                    );
                $this->colors[$rgb] = $colorIndex;
            } else {
                // no room for more custom colors, just map to black
                $colorIndex = 0;
            }
        } else {
            // fetch already added custom color
            $colorIndex = $this->colors[$rgb];
        }

        return $colorIndex;
    }

    /**
     * Sets the colour palette to the Excel 97+ default.
     *
     * @access private
     */
    private function setPaletteXl97()
    {
        $this->palette = array(
            0x08 => array(0x00, 0x00, 0x00, 0x00),
            0x09 => array(0xff, 0xff, 0xff, 0x00),
            0x0A => array(0xff, 0x00, 0x00, 0x00),
            0x0B => array(0x00, 0xff, 0x00, 0x00),
            0x0C => array(0x00, 0x00, 0xff, 0x00),
            0x0D => array(0xff, 0xff, 0x00, 0x00),
            0x0E => array(0xff, 0x00, 0xff, 0x00),
            0x0F => array(0x00, 0xff, 0xff, 0x00),
            0x10 => array(0x80, 0x00, 0x00, 0x00),
            0x11 => array(0x00, 0x80, 0x00, 0x00),
            0x12 => array(0x00, 0x00, 0x80, 0x00),
            0x13 => array(0x80, 0x80, 0x00, 0x00),
            0x14 => array(0x80, 0x00, 0x80, 0x00),
            0x15 => array(0x00, 0x80, 0x80, 0x00),
            0x16 => array(0xc0, 0xc0, 0xc0, 0x00),
            0x17 => array(0x80, 0x80, 0x80, 0x00),
            0x18 => array(0x99, 0x99, 0xff, 0x00),
            0x19 => array(0x99, 0x33, 0x66, 0x00),
            0x1A => array(0xff, 0xff, 0xcc, 0x00),
            0x1B => array(0xcc, 0xff, 0xff, 0x00),
            0x1C => array(0x66, 0x00, 0x66, 0x00),
            0x1D => array(0xff, 0x80, 0x80, 0x00),
            0x1E => array(0x00, 0x66, 0xcc, 0x00),
            0x1F => array(0xcc, 0xcc, 0xff, 0x00),
            0x20 => array(0x00, 0x00, 0x80, 0x00),
            0x21 => array(0xff, 0x00, 0xff, 0x00),
            0x22 => array(0xff, 0xff, 0x00, 0x00),
            0x23 => array(0x00, 0xff, 0xff, 0x00),
            0x24 => array(0x80, 0x00, 0x80, 0x00),
            0x25 => array(0x80, 0x00, 0x00, 0x00),
            0x26 => array(0x00, 0x80, 0x80, 0x00),
            0x27 => array(0x00, 0x00, 0xff, 0x00),
            0x28 => array(0x00, 0xcc, 0xff, 0x00),
            0x29 => array(0xcc, 0xff, 0xff, 0x00),
            0x2A => array(0xcc, 0xff, 0xcc, 0x00),
            0x2B => array(0xff, 0xff, 0x99, 0x00),
            0x2C => array(0x99, 0xcc, 0xff, 0x00),
            0x2D => array(0xff, 0x99, 0xcc, 0x00),
            0x2E => array(0xcc, 0x99, 0xff, 0x00),
            0x2F => array(0xff, 0xcc, 0x99, 0x00),
            0x30 => array(0x33, 0x66, 0xff, 0x00),
            0x31 => array(0x33, 0xcc, 0xcc, 0x00),
            0x32 => array(0x99, 0xcc, 0x00, 0x00),
            0x33 => array(0xff, 0xcc, 0x00, 0x00),
            0x34 => array(0xff, 0x99, 0x00, 0x00),
            0x35 => array(0xff, 0x66, 0x00, 0x00),
            0x36 => array(0x66, 0x66, 0x99, 0x00),
            0x37 => array(0x96, 0x96, 0x96, 0x00),
            0x38 => array(0x00, 0x33, 0x66, 0x00),
            0x39 => array(0x33, 0x99, 0x66, 0x00),
            0x3A => array(0x00, 0x33, 0x00, 0x00),
            0x3B => array(0x33, 0x33, 0x00, 0x00),
            0x3C => array(0x99, 0x33, 0x00, 0x00),
            0x3D => array(0x99, 0x33, 0x66, 0x00),
            0x3E => array(0x33, 0x33, 0x99, 0x00),
            0x3F => array(0x33, 0x33, 0x33, 0x00),
        );
    }

    /**
     * Assemble worksheets into a workbook and send the BIFF data to an OLE
     * storage.
     *
     * @param    array    $pWorksheetSizes    The sizes in bytes of the binary worksheet streams
     * @return    string    Binary data for workbook stream
     */
    public function writeWorkbook($pWorksheetSizes = null)
    {
        $this->worksheetSizes = $pWorksheetSizes;

        // Calculate the number of selected worksheet tabs and call the finalization
        // methods for each worksheet
        $total_worksheets = $this->phpExcel->getSheetCount();

        // Add part 1 of the Workbook globals, what goes before the SHEET records
        $this->storeBof(0x0005);
        $this->writeCodepage();
        $this->writeWindow1();

        $this->writeDateMode();
        $this->writeAllFonts();
        $this->writeAllNumberFormats();
        $this->writeAllXfs();
        $this->writeAllStyles();
        $this->writePalette();

        // Prepare part 3 of the workbook global stream, what goes after the SHEET records
        $part3 = '';
        if ($this->countryCode != -1) {
            $part3 .= $this->writeCountry();
        }
        $part3 .= $this->writeRecalcId();

        $part3 .= $this->writeSupbookInternal();
        /* TODO: store external SUPBOOK records and XCT and CRN records
        in case of external references for BIFF8 */
        $part3 .= $this->writeExternalsheetBiff8();
        $part3 .= $this->writeAllDefinedNamesBiff8();
        $part3 .= $this->writeMsoDrawingGroup();
        $part3 .= $this->writeSharedStringsTable();

        $part3 .= $this->writeEof();

        // Add part 2 of the Workbook globals, the SHEET records
        $this->calcSheetOffsets();
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $this->writeBoundSheet($this->phpExcel->getSheet($i), $this->worksheetOffsets[$i]);
        }

        // Add part 3 of the Workbook globals
        $this->_data .= $part3;

        return $this->_data;
    }

    /**
     * Calculate offsets for Worksheet BOF records.
     *
     * @access private
     */
    private function calcSheetOffsets()
    {
        $boundsheet_length = 10;  // fixed length for a BOUNDSHEET record

        // size of Workbook globals part 1 + 3
        $offset            = $this->_datasize;

        // add size of Workbook globals part 2, the length of the SHEET records
        $total_worksheets = count($this->phpExcel->getAllSheets());
        foreach ($this->phpExcel->getWorksheetIterator() as $sheet) {
            $offset += $boundsheet_length + strlen(PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($sheet->getTitle()));
        }

        // add the sizes of each of the Sheet substreams, respectively
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $this->worksheetOffsets[$i] = $offset;
            $offset += $this->worksheetSizes[$i];
        }
        $this->biffSize = $offset;
    }

    /**
     * Store the Excel FONT records.
     */
    private function writeAllFonts()
    {
        foreach ($this->fontWriters as $fontWriter) {
            $this->append($fontWriter->writeFont());
        }
    }

    /**
     * Store user defined numerical formats i.e. FORMAT records
     */
    private function writeAllNumberFormats()
    {
        foreach ($this->numberFormats as $numberFormatIndex => $numberFormat) {
            $this->writeNumberFormat($numberFormat->getFormatCode(), $numberFormatIndex);
        }
    }

    /**
     * Write all XF records.
     */
    private function writeAllXfs()
    {
        foreach ($this->xfWriters as $xfWriter) {
            $this->append($xfWriter->writeXf());
        }
    }

    /**
     * Write all STYLE records.
     */
    private function writeAllStyles()
    {
        $this->writeStyle();
    }

    /**
     * Write the EXTERNCOUNT and EXTERNSHEET records. These are used as indexes for
     * the NAME records.
     */
    private function writeExternals()
    {
        $countSheets = $this->phpExcel->getSheetCount();
        // Create EXTERNCOUNT with number of worksheets
        $this->writeExternalCount($countSheets);

        // Create EXTERNSHEET for each worksheet
        for ($i = 0; $i < $countSheets; ++$i) {
            $this->writeExternalSheet($this->phpExcel->getSheet($i)->getTitle());
        }
    }

    /**
     * Write the NAME record to define the print area and the repeat rows and cols.
     */
    private function writeNames()
    {
        // total number of sheets
        $total_worksheets = $this->phpExcel->getSheetCount();

        // Create the print area NAME records
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->phpExcel->getSheet($i)->getPageSetup();
            // Write a Name record if the print area has been defined
            if ($sheetSetup->isPrintAreaSet()) {
                // Print area
                $printArea = PHPExcel_Cell::splitRange($sheetSetup->getPrintArea());
                $printArea = $printArea[0];
                $printArea[0] = PHPExcel_Cell::coordinateFromString($printArea[0]);
                $printArea[1] = PHPExcel_Cell::coordinateFromString($printArea[1]);

                $print_rowmin = $printArea[0][1] - 1;
                $print_rowmax = $printArea[1][1] - 1;
                $print_colmin = PHPExcel_Cell::columnIndexFromString($printArea[0][0]) - 1;
                $print_colmax = PHPExcel_Cell::columnIndexFromString($printArea[1][0]) - 1;

                $this->writeNameShort(
                    $i, // sheet index
                    0x06, // NAME type
                    $print_rowmin,
                    $print_rowmax,
                    $print_colmin,
                    $print_colmax
                );
            }
        }

        // Create the print title NAME records
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->phpExcel->getSheet($i)->getPageSetup();

            // simultaneous repeatColumns repeatRows
            if ($sheetSetup->isColumnsToRepeatAtLeftSet() && $sheetSetup->isRowsToRepeatAtTopSet()) {
                $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                $colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
                $colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;

                $repeat = $sheetSetup->getRowsToRepeatAtTop();
                $rowmin = $repeat[0] - 1;
                $rowmax = $repeat[1] - 1;

                $this->writeNameLong(
                    $i, // sheet index
                    0x07, // NAME type
                    $rowmin,
                    $rowmax,
                    $colmin,
                    $colmax
                );

            // (exclusive) either repeatColumns or repeatRows
            } elseif ($sheetSetup->isColumnsToRepeatAtLeftSet() || $sheetSetup->isRowsToRepeatAtTopSet()) {
                // Columns to repeat
                if ($sheetSetup->isColumnsToRepeatAtLeftSet()) {
                    $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                    $colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
                    $colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
                } else {
                    $colmin = 0;
                    $colmax = 255;
                }

                // Rows to repeat
                if ($sheetSetup->isRowsToRepeatAtTopSet()) {
                    $repeat = $sheetSetup->getRowsToRepeatAtTop();
                    $rowmin = $repeat[0] - 1;
                    $rowmax = $repeat[1] - 1;
                } else {
                    $rowmin = 0;
                    $rowmax = 65535;
                }

                $this->writeNameShort(
                    $i, // sheet index
                    0x07, // NAME type
                    $rowmin,
                    $rowmax,
                    $colmin,
                    $colmax
                );
            }
        }
    }

    /**
     * Writes all the DEFINEDNAME records (BIFF8).
     * So far this is only used for repeating rows/columns (print titles) and print areas
     */
    private function writeAllDefinedNamesBiff8()
    {
        $chunk = '';

        // Named ranges
        if (count($this->phpExcel->getNamedRanges()) > 0) {
            // Loop named ranges
            $namedRanges = $this->phpExcel->getNamedRanges();
            foreach ($namedRanges as $namedRange) {
                // Create absolute coordinate
                $range = PHPExcel_Cell::splitRange($namedRange->getRange());
                for ($i = 0; $i < count($range); $i++) {
                    $range[$i][0] = '\'' . str_replace("'", "''", $namedRange->getWorksheet()->getTitle()) . '\'!' . PHPExcel_Cell::absoluteCoordinate($range[$i][0]);
                    if (isset($range[$i][1])) {
                        $range[$i][1] = PHPExcel_Cell::absoluteCoordinate($range[$i][1]);
                    }
                }
                $range = PHPExcel_Cell::buildRange($range); // e.g. Sheet1!$A$1:$B$2

                // parse formula
                try {
                    $error = $this->parser->parse($range);
                    $formulaData = $this->parser->toReversePolish();

                    // make sure tRef3d is of type tRef3dR (0x3A)
                    if (isset($formulaData{0}) and ($formulaData{0} == "\x7A" or $formulaData{0} == "\x5A")) {
                        $formulaData = "\x3A" . substr($formulaData, 1);
                    }

                    if ($namedRange->getLocalOnly()) {
                        // local scope
                        $scope = $this->phpExcel->getIndex($namedRange->getScope()) + 1;
                    } else {
                        // global scope
                        $scope = 0;
                    }
                    $chunk .= $this->writeData($this->writeDefinedNameBiff8($namedRange->getName(), $formulaData, $scope, false));

                } catch (PHPExcel_Exception $e) {
                    // do nothing
                }
            }
        }

        // total number of sheets
        $total_worksheets = $this->phpExcel->getSheetCount();

        // write the print titles (repeating rows, columns), if any
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->phpExcel->getSheet($i)->getPageSetup();
            // simultaneous repeatColumns repeatRows
            if ($sheetSetup->isColumnsToRepeatAtLeftSet() && $sheetSetup->isRowsToRepeatAtTopSet()) {
                $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                $colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
                $colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;

                $repeat = $sheetSetup->getRowsToRepeatAtTop();
                $rowmin = $repeat[0] - 1;
                $rowmax = $repeat[1] - 1;

                // construct formula data manually
                $formulaData = pack('Cv', 0x29, 0x17); // tMemFunc
                $formulaData .= pack('Cvvvvv', 0x3B, $i, 0, 65535, $colmin, $colmax); // tArea3d
                $formulaData .= pack('Cvvvvv', 0x3B, $i, $rowmin, $rowmax, 0, 255); // tArea3d
                $formulaData .= pack('C', 0x10); // tList

                // store the DEFINEDNAME record
                $chunk .= $this->writeData($this->writeDefinedNameBiff8(pack('C', 0x07), $formulaData, $i + 1, true));

            // (exclusive) either repeatColumns or repeatRows
            } elseif ($sheetSetup->isColumnsToRepeatAtLeftSet() || $sheetSetup->isRowsToRepeatAtTopSet()) {
                // Columns to repeat
                if ($sheetSetup->isColumnsToRepeatAtLeftSet()) {
                    $repeat = $sheetSetup->getColumnsToRepeatAtLeft();
                    $colmin = PHPExcel_Cell::columnIndexFromString($repeat[0]) - 1;
                    $colmax = PHPExcel_Cell::columnIndexFromString($repeat[1]) - 1;
                } else {
                    $colmin = 0;
                    $colmax = 255;
                }
                // Rows to repeat
                if ($sheetSetup->isRowsToRepeatAtTopSet()) {
                    $repeat = $sheetSetup->getRowsToRepeatAtTop();
                    $rowmin = $repeat[0] - 1;
                    $rowmax = $repeat[1] - 1;
                } else {
                    $rowmin = 0;
                    $rowmax = 65535;
                }

                // construct formula data manually because parser does not recognize absolute 3d cell references
                $formulaData = pack('Cvvvvv', 0x3B, $i, $rowmin, $rowmax, $colmin, $colmax);

                // store the DEFINEDNAME record
                $chunk .= $this->writeData($this->writeDefinedNameBiff8(pack('C', 0x07), $formulaData, $i + 1, true));
            }
        }

        // write the print areas, if any
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetSetup = $this->phpExcel->getSheet($i)->getPageSetup();
            if ($sheetSetup->isPrintAreaSet()) {
                // Print area, e.g. A3:J6,H1:X20
                $printArea = PHPExcel_Cell::splitRange($sheetSetup->getPrintArea());
                $countPrintArea = count($printArea);

                $formulaData = '';
                for ($j = 0; $j < $countPrintArea; ++$j) {
                    $printAreaRect = $printArea[$j]; // e.g. A3:J6
                    $printAreaRect[0] = PHPExcel_Cell::coordinateFromString($printAreaRect[0]);
                    $printAreaRect[1] = PHPExcel_Cell::coordinateFromString($printAreaRect[1]);

                    $print_rowmin = $printAreaRect[0][1] - 1;
                    $print_rowmax = $printAreaRect[1][1] - 1;
                    $print_colmin = PHPExcel_Cell::columnIndexFromString($printAreaRect[0][0]) - 1;
                    $print_colmax = PHPExcel_Cell::columnIndexFromString($printAreaRect[1][0]) - 1;

                    // construct formula data manually because parser does not recognize absolute 3d cell references
                    $formulaData .= pack('Cvvvvv', 0x3B, $i, $print_rowmin, $print_rowmax, $print_colmin, $print_colmax);

                    if ($j > 0) {
                        $formulaData .= pack('C', 0x10); // list operator token ','
                    }
                }

                // store the DEFINEDNAME record
                $chunk .= $this->writeData($this->writeDefinedNameBiff8(pack('C', 0x06), $formulaData, $i + 1, true));
            }
        }

        // write autofilters, if any
        for ($i = 0; $i < $total_worksheets; ++$i) {
            $sheetAutoFilter = $this->phpExcel->getSheet($i)->getAutoFilter();
            $autoFilterRange = $sheetAutoFilter->getRange();
            if (!empty($autoFilterRange)) {
                $rangeBounds = PHPExcel_Cell::rangeBoundaries($autoFilterRange);

                //Autofilter built in name
                $name = pack('C', 0x0D);

                $chunk .= $this->writeData($this->writeShortNameBiff8($name, $i + 1, $rangeBounds, true));
            }
        }

        return $chunk;
    }

    /**
     * Write a DEFINEDNAME record for BIFF8 using explicit binary formula data
     *
     * @param    string        $name            The name in UTF-8
     * @param    string        $formulaData    The binary formula data
     * @param    string        $sheetIndex        1-based sheet index the defined name applies to. 0 = global
     * @param    boolean        $isBuiltIn        Built-in name?
     * @return    string    Complete binary record data
     */
    private function writeDefinedNameBiff8($name, $formulaData, $sheetIndex = 0, $isBuiltIn = false)
    {
        $record = 0x0018;

        // option flags
        $options = $isBuiltIn ? 0x20 : 0x00;

        // length of the name, character count
        $nlen = PHPExcel_Shared_String::CountCharacters($name);

        // name with stripped length field
        $name = substr(PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($name), 2);

        // size of the formula (in bytes)
        $sz = strlen($formulaData);

        // combine the parts
        $data = pack('vCCvvvCCCC', $options, 0, $nlen, $sz, 0, $sheetIndex, 0, 0, 0, 0)
            . $name . $formulaData;
        $length = strlen($data);

        $header = pack('vv', $record, $length);

        return $header . $data;
    }

    /**
     * Write a short NAME record
     *
     * @param    string         $name
     * @param    string         $sheetIndex        1-based sheet index the defined name applies to. 0 = global
     * @param    integer[][]  $rangeBounds    range boundaries
     * @param    boolean      $isHidden
     * @return    string    Complete binary record data
     * */
    private function writeShortNameBiff8($name, $sheetIndex = 0, $rangeBounds, $isHidden = false)
    {
        $record = 0x0018;

        // option flags
        $options = ($isHidden  ? 0x21 : 0x00);

        $extra  = pack(
            'Cvvvvv',
            0x3B,
            $sheetIndex - 1,
            $rangeBounds[0][1] - 1,
            $rangeBounds[1][1] - 1,
            $rangeBounds[0][0] - 1,
            $rangeBounds[1][0] - 1
        );

        // size of the formula (in bytes)
        $sz = strlen($extra);

        // combine the parts
        $data = pack('vCCvvvCCCCC', $options, 0, 1, $sz, 0, $sheetIndex, 0, 0, 0, 0, 0)
            . $name . $extra;
        $length = strlen($data);

        $header = pack('vv', $record, $length);

        return $header . $data;
    }

    /**
     * Stores the CODEPAGE biff record.
     */
    private function writeCodepage()
    {
        $record = 0x0042;             // Record identifier
        $length = 0x0002;             // Number of bytes to follow
        $cv  = $this->codepage;   // The code page

        $header = pack('vv', $record, $length);
        $data   = pack('v', $cv);

        $this->append($header . $data);
    }

    /**
     * Write Excel BIFF WINDOW1 record.
     */
    private function writeWindow1()
    {
        $record = 0x003D;   // Record identifier
        $length = 0x0012;   // Number of bytes to follow

        $xWn  = 0x0000;     // Horizontal position of window
        $yWn  = 0x0000;     // Vertical position of window
        $dxWn = 0x25BC;     // Width of window
        $dyWn = 0x1572;     // Height of window

        $grbit = 0x0038;    // Option flags

        // not supported by PHPExcel, so there is only one selected sheet, the active
        $ctabsel = 1;       // Number of workbook tabs selected

        $wTabRatio = 0x0258;    // Tab to scrollbar ratio

        // not supported by PHPExcel, set to 0
        $itabFirst = 0;     // 1st displayed worksheet
        $itabCur   = $this->phpExcel->getActiveSheetIndex();    // Active worksheet

        $header = pack("vv", $record, $length);
        $data   = pack("vvvvvvvvv", $xWn, $yWn, $dxWn, $dyWn, $grbit, $itabCur, $itabFirst, $ctabsel, $wTabRatio);
        $this->append($header . $data);
    }

    /**
     * Writes Excel BIFF BOUNDSHEET record.
     *
     * @param PHPExcel_Worksheet  $sheet Worksheet name
     * @param integer $offset    Location of worksheet BOF
     */
    private function writeBoundSheet($sheet, $offset)
    {
        $sheetname = $sheet->getTitle();
        $record    = 0x0085;                    // Record identifier

        // sheet state
        switch ($sheet->getSheetState()) {
            case PHPExcel_Worksheet::SHEETSTATE_VISIBLE:
                $ss = 0x00;
                break;
            case PHPExcel_Worksheet::SHEETSTATE_HIDDEN:
                $ss = 0x01;
                break;
            case PHPExcel_Worksheet::SHEETSTATE_VERYHIDDEN:
                $ss = 0x02;
                break;
            default:
                $ss = 0x00;
                break;
        }

        // sheet type
        $st = 0x00;

        $grbit = 0x0000;                    // Visibility and sheet type

        $data = pack("VCC", $offset, $ss, $st);
        $data .= PHPExcel_Shared_String::UTF8toBIFF8UnicodeShort($sheetname);

        $length = strlen($data);
        $header = pack("vv", $record, $length);
        $this->append($header . $data);
    }

    /**
     * Write Internal SUPBOOK record
     */
    private function writeSupbookInternal()
    {
        $record    = 0x01AE;   // Record identifier
        $length    = 0x0004;   // Bytes to follow

        $header    = pack("vv", $record, $length);
        $data      = pack("vv", $this->phpExcel->getSheetCount(), 0x0401);
        return $this->writeData($header . $data);
    }

    /**
     * Writes the Excel BIFF EXTERNSHEET record. These references are used by
     * formulas.
     *
     */
    private function writeExternalsheetBiff8()
    {
        $totalReferences = count($this->parser->references);
        $record = 0x0017;                     // Record identifier
        $length = 2 + 6 * $totalReferences;  // Number of bytes to follow

        $supbook_index = 0;           // FIXME: only using internal SUPBOOK record
        $header = pack("vv", $record, $length);
        $data   = pack('v', $totalReferences);
        for ($i = 0; $i < $totalReferences; ++$i) {
            $data .= $this->parser->references[$i];
        }
        return $this->writeData($header . $data);
    }

    /**
     * Write Excel BIFF STYLE records.
     */
    private function writeStyle()
    {
        $record = 0x0293;   // Record identifier
        $length = 0x0004;   // Bytes to follow

        $ixfe    = 0x8000;  // Index to cell style XF
        $BuiltIn = 0x00;     // Built-in style
        $iLevel  = 0xff;     // Outline style level

        $header = pack("vv", $record, $length);
        $data   = pack("vCC", $ixfe, $BuiltIn, $iLevel);
        $this->append($header . $data);
    }

    /**
     * Writes Excel FORMAT record for non "built-in" numerical formats.
     *
     * @param string  $format Custom format string
     * @param integer $ifmt   Format index code
     */
    private function writeNumberFormat($format, $ifmt)
    {
        $record = 0x041E;    // Record identifier

        $numberFormatString = PHPExcel_Shared_String::UTF8toBIFF8UnicodeLong($format);
        $length = 2 + strlen($numberFormatString);    // Number of bytes to follow


        $header = pack("vv", $record, $length);
        $data   = pack("v", $ifmt) .  $numberFormatString;
        $this->append($header . $data);
    }

    /**
     * Write DATEMODE record to indicate the date system in use (1904 or 1900).
     */
    private function writeDateMode()
    {
        $record = 0x0022;   // Record identifier
        $length = 0x0002;   // Bytes to follow

        $f1904  = (PHPExcel_Shared_Date::getExcelCalendar() == PHPExcel_Shared_Date::CALENDAR_MAC_1904)
            ? 1
            : 0;   // Flag for 1904 date system

        $header = pack("vv", $record, $length);
        $data   = pack("v", $f1904);
        $this->append($header . $data);
    }

    /**
     * Write BIFF record EXTERNCOUNT to indicate the number of external sheet
     * references in the workbook.
     *
     * Excel only stores references to external sheets that are used in NAME.
     * The workbook NAME record is required to define the print area and the repeat
     * rows and columns.
     *
     * A similar method is used in Worksheet.php for a slightly different purpose.
     *
     * @param integer $cxals Number of external references
     */
    private function writeExternalCount($cxals)
    {
        $record = 0x0016;   // Record identifier
        $length = 0x0002;   // Number of bytes to follow

        $header = pack("vv", $record, $length);
        $data   = pack("v", $cxals);
        $this->append($header . $data);
    }

    /**
     * Writes the Excel BIFF EXTERNSHEET record. These references are used by
     * formulas. NAME record is required to define the print area and the repeat
     * rows and columns.
     *
     * A similar method is used in Worksheet.php for a slightly different purpose.
     *
     * @param string $sheetname Worksheet name
     */
    private function writeExternalSheet($sheetname)
    {
        $record = 0x0017;                     // Record identifier
        $length = 0x02 + strlen($sheetname);  // Number of bytes to follow

        $cch    = strlen($sheetname);         // Length of sheet name
        $rgch   = 0x03;                       // Filename encoding

        $header = pack("vv", $record, $length);
        $data   = pack("CC", $cch, $rgch);
        $this->append($header . $data . $sheetname);
    }

    /**
     * Store the NAME record in the short format that is used for storing the print
     * area, repeat rows only and repeat columns only.
     *
     * @param integer $index  Sheet index
     * @param integer $type   Built-in name type
     * @param integer $rowmin Start row
     * @param integer $rowmax End row
     * @param integer $colmin Start colum
     * @param integer $colmax End column
     */
    private function writeNameShort($index, $type, $rowmin, $rowmax, $colmin, $colmax)
    {
        $record = 0x0018;       // Record identifier
        $length = 0x0024;       // Number of bytes to follow

        $grbit  = 0x0020;       // Option flags
        $chKey  = 0x00;         // Keyboard shortcut
        $cch    = 0x01;         // Length of text name
        $cce    = 0x0015;       // Length of text definition
        $ixals  = $index + 1;   // Sheet index
        $itab   = $ixals;       // Equal to ixals
        $cchCustMenu    = 0x00;         // Length of cust menu text
        $cchDescription = 0x00;         // Length of description text
        $cchHelptopic   = 0x00;         // Length of help topic text
        $cchStatustext  = 0x00;         // Length of status bar text
        $rgch           = $type;        // Built-in name type

        $unknown03 = 0x3b;
        $unknown04 = 0xffff - $index;
        $unknown05 = 0x0000;
        $unknown06 = 0x0000;
        $unknown07 = 0x1087;
        $unknown08 = 0x8005;

        $header = pack("vv", $record, $length);
        $data = pack("v", $grbit);
        $data .= pack("C", $chKey);
        $data .= pack("C", $cch);
        $data .= pack("v", $cce);
        $data .= pack("v", $ixals);
        $data .= pack("v", $itab);
        $data .= pack("C", $cchCustMenu);
        $data .= pack("C", $cchDescription);
        $data .= pack("C", $cchHelptopic);
        $data .= pack("C", $cchStatustext);
        $data .= pack("C", $rgch);
        $data .= pack("C", $unknown03);
        $data .= pack("v", $unknown04);
        $data .= pack("v", $unknown05);
        $data .= pack("v", $unknown06);
        $data .= pack("v", $unknown07);
        $data .= pack("v", $unknown08);
        $data .= pack("v", $index);
        $data .= pack("v", $index);
        $data .= pack("v", $rowmin);
        $data .= pack("v", $rowmax);
        $data .= pack("C", $colmin);
        $data .= pack("C", $colmax);
        $this->append($header . $data);
    }

    /**
     * Store the NAME record in the long format that is used for storing the repeat
     * rows and columns when both are specified. This shares a lot of code with
     * writeNameShort() but we use a separate method to keep the code clean.
     * Code abstraction for reuse can be carried too far, and I should know. ;-)
     *
     * @param integer $index Sheet index
     * @param integer $type  Built-in name type
     * @param integer $rowmin Start row
     * @param integer $rowmax End row
     * @param integer $colmin Start colum
     * @param integer $colmax End column
     */
    private function writeNameLong($index, $type, $rowmin, $rowmax, $colmin, $colmax)
    {
        $record          = 0x0018;       // Record identifier
        $length          = 0x003d;       // Number of bytes to follow
        $grbit           = 0x0020;       // Option flags
        $chKey           = 0x00;         // Keyboard shortcut
        $cch             = 0x01;         // Length of text name
        $cce             = 0x002e;       // Length of text definition
        $ixals           = $index + 1;   // Sheet index
        $itab            = $ixals;       // Equal to ixals
        $cchCustMenu     = 0x00;         // Length of cust menu text
        $cchDescription  = 0x00;         // Length of description text
        $cchHelptopic    = 0x00;         // Length of help topic text
        $cchStatustext   = 0x00;         // Length of status bar text
        $rgch            = $type;        // Built-in name type

        $unknown01       = 0x29;
        $unknown02       = 0x002b;
        $unknown03       = 0x3b;
        $unknown04       = 0xffff-$index;
        $unknown05       = 0x0000;
        $unknown06       = 0x0000;
        $unknown07       = 0x1087;
        $unknown08       = 0x8008;

        $header = pack("vv", $record, $length);
        $data = pack("v", $grbit);
        $data .= pack("C", $chKey);
        $data .= pack("C", $cch);
        $data .= pack("v", $cce);
        $data .= pack("v", $ixals);
        $data .= pack("v", $itab);
        $data .= pack("C", $cchCustMenu);
        $data .= pack("C", $cchDescription);
        $data .= pack("C", $cchHelptopic);
        $data .= pack("C", $cchStatustext);
        $data .= pack("C", $rgch);
        $data .= pack("C", $unknown01);
        $data .= pack("v", $unknown02);
        // Column definition
        $data .= pack("C", $unknown03);
        $data .= pack("v", $unknown04);
        $data .= pack("v", $unknown05);
        $data .= pack("v", $unknown06);
        $data .= pack("v", $unknown07);
        $data .= pack("v", $unknown08);
        $data .= pack("v", $index);
        $data .= pack("v", $index);
        $data .= pack("v", 0x0000);
        $data .= pack("v", 0x3fff);
        $data .= pack("C", $colmin);
        $data .= pack("C", $colmax);
        // Row definition
        $data .= pack("C", $unknown03);
        $data .= pack("v", $unknown04);
        $data .= pack("v", $unknown05);
        $data .= pack("v", $unknown06);
        $data .= pack("v", $unknown07);
        $data .= pack("v", $unknown08);
        $data .= pack("v", $index);
        $data .= pack("v", $index);
        $data .= pack("v", $rowmin);
        $data .= pack("v", $rowmax);
        $data .= pack("C", 0x00);
        $data .= pack("C", 0xff);
        // End of data
        $data .= pack("C", 0x10);
        $this->append($header . $data);
    }

    /**
     * Stores the COUNTRY record for localization
     *
     * @return string
     */
    private function writeCountry()
    {
        $record = 0x008C;    // Record identifier
        $length = 4;         // Number of bytes to follow

        $header = pack('vv', $record, $length);
        /* using the same country code always for simplicity */
        $data = pack('vv', $this->countryCode, $this->countryCode);
        //$this->append($header . $data);
        return $this->writeData($header . $data);
    }

    /**
     * Write the RECALCID record
     *
     * @return string
     */
    private function writeRecalcId()
    {
        $record = 0x01C1;    // Record identifier
        $length = 8;         // Number of bytes to follow

        $header = pack('vv', $record, $length);

        // by inspection of real Excel files, MS Office Excel 2007 writes this
        $data = pack('VV', 0x000001C1, 0x00001E667);

        return $this->writeData($header . $data);
    }

    /**
     * Stores the PALETTE biff record.
     */
    private function writePalette()
    {
        $aref = $this->palette;

        $record = 0x0092;                // Record identifier
        $length = 2 + 4 * count($aref);  // Number of bytes to follow
        $ccv    = count($aref);          // Number of RGB values to follow
        $data = '';                      // The RGB data

        // Pack the RGB data
        foreach ($aref as $color) {
            foreach ($color as $byte) {
                $data .= pack("C", $byte);
            }
        }

        $header = pack("vvv", $record, $length, $ccv);
        $this->append($header . $data);
    }

    /**
     * Handling of the SST continue blocks is complicated by the need to include an
     * additional continuation byte depending on whether the string is split between
     * blocks or whether it starts at the beginning of the block. (There are also
     * additional complications that will arise later when/if Rich Strings are
     * supported).
     *
     * The Excel documentation says that the SST record should be followed by an
     * EXTSST record. The EXTSST record is a hash table that is used to optimise
     * access to SST. However, despite the documentation it doesn't seem to be
     * required so we will ignore it.
     *
     * @return string Binary data
     */
    private function writeSharedStringsTable()
    {
        // maximum size of record data (excluding record header)
        $continue_limit = 8224;

        // initialize array of record data blocks
        $recordDatas = array();

        // start SST record data block with total number of strings, total number of unique strings
        $recordData = pack("VV", $this->stringTotal, $this->stringUnique);

        // loop through all (unique) strings in shared strings table
        foreach (array_keys($this->stringTable) as $string) {
            // here $string is a BIFF8 encoded string

            // length = character count
            $headerinfo = unpack("vlength/Cencoding", $string);

            // currently, this is always 1 = uncompressed
            $encoding = $headerinfo["encoding"];

            // initialize finished writing current $string
            $finished = false;

            while ($finished === false) {
                // normally, there will be only one cycle, but if string cannot immediately be written as is
                // there will be need for more than one cylcle, if string longer than one record data block, there
                // may be need for even more cycles

                if (strlen($recordData) + strlen($string) <= $continue_limit) {
                    // then we can write the string (or remainder of string) without any problems
                    $recordData .= $string;

                    if (strlen($recordData) + strlen($string) == $continue_limit) {
                        // we close the record data block, and initialize a new one
                        $recordDatas[] = $recordData;
                        $recordData = '';
                    }

                    // we are finished writing this string
                    $finished = true;
                } else {
                    // special treatment writing the string (or remainder of the string)
                    // If the string is very long it may need to be written in more than one CONTINUE record.

                    // check how many bytes more there is room for in the current record
                    $space_remaining = $continue_limit - strlen($recordData);

                    // minimum space needed
                    // uncompressed: 2 byte string length length field + 1 byte option flags + 2 byte character
                    // compressed:   2 byte string length length field + 1 byte option flags + 1 byte character
                    $min_space_needed = ($encoding == 1) ? 5 : 4;

                    // We have two cases
                    // 1. space remaining is less than minimum space needed
                    //        here we must waste the space remaining and move to next record data block
                    // 2. space remaining is greater than or equal to minimum space needed
                    //        here we write as much as we can in the current block, then move to next record data block

                    // 1. space remaining is less than minimum space needed
                    if ($space_remaining < $min_space_needed) {
                        // we close the block, store the block data
                        $recordDatas[] = $recordData;

                        // and start new record data block where we start writing the string
                        $recordData = '';

                    // 2. space remaining is greater than or equal to minimum space needed
                    } else {
                        // initialize effective remaining space, for Unicode strings this may need to be reduced by 1, see below
                        $effective_space_remaining = $space_remaining;

                        // for uncompressed strings, sometimes effective space remaining is reduced by 1
                        if ($encoding == 1 && (strlen($string) - $space_remaining) % 2 == 1) {
                            --$effective_space_remaining;
                        }

                        // one block fininshed, store the block data
                        $recordData .= substr($string, 0, $effective_space_remaining);

                        $string = substr($string, $effective_space_remaining); // for next cycle in while loop
                        $recordDatas[] = $recordData;

                        // start new record data block with the repeated option flags
                        $recordData = pack('C', $encoding);
                    }
                }
            }
        }

        // Store the last record data block unless it is empty
        // if there was no need for any continue records, this will be the for SST record data block itself
        if (strlen($recordData) > 0) {
            $recordDatas[] = $recordData;
        }

        // combine into one chunk with all the blocks SST, CONTINUE,...
        $chunk = '';
        foreach ($recordDatas as $i => $recordData) {
            // first block should have the SST record header, remaing should have CONTINUE header
            $record = ($i == 0) ? 0x00FC : 0x003C;

            $header = pack("vv", $record, strlen($recordData));
            $data = $header . $recordData;

            $chunk .= $this->writeData($data);
        }

        return $chunk;
    }

    /**
     * Writes the MSODRAWINGGROUP record if needed. Possibly split using CONTINUE records.
     */
    private function writeMsoDrawingGroup()
    {
        // write the Escher stream if necessary
        if (isset($this->escher)) {
            $writer = new PHPExcel_Writer_Excel5_Escher($this->escher);
            $data = $writer->close();

            $record = 0x00EB;
            $length = strlen($data);
            $header = pack("vv", $record, $length);

            return $this->writeData($header . $data);
        } else {
            return '';
        }
    }

    /**
     * Get Escher object
     *
     * @return PHPExcel_Shared_Escher
     */
    public function getEscher()
    {
        return $this->escher;
    }

    /**
     * Set Escher object
     *
     * @param PHPExcel_Shared_Escher $pValue
     */
    public function setEscher(PHPExcel_Shared_Escher $pValue = null)
    {
        $this->escher = $pValue;
    }
}
