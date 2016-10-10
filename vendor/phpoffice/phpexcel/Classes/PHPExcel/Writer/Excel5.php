<?php

/**
 * PHPExcel_Writer_Excel5
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
class PHPExcel_Writer_Excel5 extends PHPExcel_Writer_Abstract implements PHPExcel_Writer_IWriter
{
    /**
     * PHPExcel object
     *
     * @var PHPExcel
     */
    private $phpExcel;

    /**
     * Total number of shared strings in workbook
     *
     * @var int
     */
    private $strTotal = 0;

    /**
     * Number of unique shared strings in workbook
     *
     * @var int
     */
    private $strUnique = 0;

    /**
     * Array of unique shared strings in workbook
     *
     * @var array
     */
    private $strTable = array();

    /**
     * Color cache. Mapping between RGB value and color index.
     *
     * @var array
     */
    private $colors;

    /**
     * Formula parser
     *
     * @var PHPExcel_Writer_Excel5_Parser
     */
    private $parser;

    /**
     * Identifier clusters for drawings. Used in MSODRAWINGGROUP record.
     *
     * @var array
     */
    private $IDCLs;

    /**
     * Basic OLE object summary information
     *
     * @var array
     */
    private $summaryInformation;

    /**
     * Extended OLE object document summary information
     *
     * @var array
     */
    private $documentSummaryInformation;

    /**
     * Create a new PHPExcel_Writer_Excel5
     *
     * @param    PHPExcel    $phpExcel    PHPExcel object
     */
    public function __construct(PHPExcel $phpExcel)
    {
        $this->phpExcel    = $phpExcel;

        $this->parser        = new PHPExcel_Writer_Excel5_Parser();
    }

    /**
     * Save PHPExcel to file
     *
     * @param    string        $pFilename
     * @throws    PHPExcel_Writer_Exception
     */
    public function save($pFilename = null)
    {

        // garbage collect
        $this->phpExcel->garbageCollect();

        $saveDebugLog = PHPExcel_Calculation::getInstance($this->phpExcel)->getDebugLog()->getWriteDebugLog();
        PHPExcel_Calculation::getInstance($this->phpExcel)->getDebugLog()->setWriteDebugLog(false);
        $saveDateReturnType = PHPExcel_Calculation_Functions::getReturnDateType();
        PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);

        // initialize colors array
        $this->colors = array();

        // Initialise workbook writer
        $this->writerWorkbook = new PHPExcel_Writer_Excel5_Workbook($this->phpExcel, $this->strTotal, $this->strUnique, $this->strTable, $this->colors, $this->parser);

        // Initialise worksheet writers
        $countSheets = $this->phpExcel->getSheetCount();
        for ($i = 0; $i < $countSheets; ++$i) {
            $this->writerWorksheets[$i] = new PHPExcel_Writer_Excel5_Worksheet($this->strTotal, $this->strUnique, $this->strTable, $this->colors, $this->parser, $this->preCalculateFormulas, $this->phpExcel->getSheet($i));
        }

        // build Escher objects. Escher objects for workbooks needs to be build before Escher object for workbook.
        $this->buildWorksheetEschers();
        $this->buildWorkbookEscher();

        // add 15 identical cell style Xfs
        // for now, we use the first cellXf instead of cellStyleXf
        $cellXfCollection = $this->phpExcel->getCellXfCollection();
        for ($i = 0; $i < 15; ++$i) {
            $this->writerWorkbook->addXfWriter($cellXfCollection[0], true);
        }

        // add all the cell Xfs
        foreach ($this->phpExcel->getCellXfCollection() as $style) {
            $this->writerWorkbook->addXfWriter($style, false);
        }

        // add fonts from rich text eleemnts
        for ($i = 0; $i < $countSheets; ++$i) {
            foreach ($this->writerWorksheets[$i]->phpSheet->getCellCollection() as $cellID) {
                $cell = $this->writerWorksheets[$i]->phpSheet->getCell($cellID);
                $cVal = $cell->getValue();
                if ($cVal instanceof PHPExcel_RichText) {
                    $elements = $cVal->getRichTextElements();
                    foreach ($elements as $element) {
                        if ($element instanceof PHPExcel_RichText_Run) {
                            $font = $element->getFont();
                            $this->writerWorksheets[$i]->fontHashIndex[$font->getHashCode()] = $this->writerWorkbook->addFont($font);
                        }
                    }
                }
            }
        }

        // initialize OLE file
        $workbookStreamName = 'Workbook';
        $OLE = new PHPExcel_Shared_OLE_PPS_File(PHPExcel_Shared_OLE::Asc2Ucs($workbookStreamName));

        // Write the worksheet streams before the global workbook stream,
        // because the byte sizes of these are needed in the global workbook stream
        $worksheetSizes = array();
        for ($i = 0; $i < $countSheets; ++$i) {
            $this->writerWorksheets[$i]->close();
            $worksheetSizes[] = $this->writerWorksheets[$i]->_datasize;
        }

        // add binary data for global workbook stream
        $OLE->append($this->writerWorkbook->writeWorkbook($worksheetSizes));

        // add binary data for sheet streams
        for ($i = 0; $i < $countSheets; ++$i) {
            $OLE->append($this->writerWorksheets[$i]->getData());
        }

        $this->documentSummaryInformation = $this->writeDocumentSummaryInformation();
        // initialize OLE Document Summary Information
        if (isset($this->documentSummaryInformation) && !empty($this->documentSummaryInformation)) {
            $OLE_DocumentSummaryInformation = new PHPExcel_Shared_OLE_PPS_File(PHPExcel_Shared_OLE::Asc2Ucs(chr(5) . 'DocumentSummaryInformation'));
            $OLE_DocumentSummaryInformation->append($this->documentSummaryInformation);
        }

        $this->summaryInformation = $this->writeSummaryInformation();
        // initialize OLE Summary Information
        if (isset($this->summaryInformation) && !empty($this->summaryInformation)) {
            $OLE_SummaryInformation = new PHPExcel_Shared_OLE_PPS_File(PHPExcel_Shared_OLE::Asc2Ucs(chr(5) . 'SummaryInformation'));
            $OLE_SummaryInformation->append($this->summaryInformation);
        }

        // define OLE Parts
        $arrRootData = array($OLE);
        // initialize OLE Properties file
        if (isset($OLE_SummaryInformation)) {
            $arrRootData[] = $OLE_SummaryInformation;
        }
        // initialize OLE Extended Properties file
        if (isset($OLE_DocumentSummaryInformation)) {
            $arrRootData[] = $OLE_DocumentSummaryInformation;
        }

        $root = new PHPExcel_Shared_OLE_PPS_Root(time(), time(), $arrRootData);
        // save the OLE file
        $res = $root->save($pFilename);

        PHPExcel_Calculation_Functions::setReturnDateType($saveDateReturnType);
        PHPExcel_Calculation::getInstance($this->phpExcel)->getDebugLog()->setWriteDebugLog($saveDebugLog);
    }

    /**
     * Set temporary storage directory
     *
     * @deprecated
     * @param    string    $pValue        Temporary storage directory
     * @throws    PHPExcel_Writer_Exception    when directory does not exist
     * @return PHPExcel_Writer_Excel5
     */
    public function setTempDir($pValue = '')
    {
        return $this;
    }

    /**
     * Build the Worksheet Escher objects
     *
     */
    private function buildWorksheetEschers()
    {
        // 1-based index to BstoreContainer
        $blipIndex = 0;
        $lastReducedSpId = 0;
        $lastSpId = 0;

        foreach ($this->phpExcel->getAllsheets() as $sheet) {
            // sheet index
            $sheetIndex = $sheet->getParent()->getIndex($sheet);

            $escher = null;

            // check if there are any shapes for this sheet
            $filterRange = $sheet->getAutoFilter()->getRange();
            if (count($sheet->getDrawingCollection()) == 0 && empty($filterRange)) {
                continue;
            }

            // create intermediate Escher object
            $escher = new PHPExcel_Shared_Escher();

            // dgContainer
            $dgContainer = new PHPExcel_Shared_Escher_DgContainer();

            // set the drawing index (we use sheet index + 1)
            $dgId = $sheet->getParent()->getIndex($sheet) + 1;
            $dgContainer->setDgId($dgId);
            $escher->setDgContainer($dgContainer);

            // spgrContainer
            $spgrContainer = new PHPExcel_Shared_Escher_DgContainer_SpgrContainer();
            $dgContainer->setSpgrContainer($spgrContainer);

            // add one shape which is the group shape
            $spContainer = new PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer();
            $spContainer->setSpgr(true);
            $spContainer->setSpType(0);
            $spContainer->setSpId(($sheet->getParent()->getIndex($sheet) + 1) << 10);
            $spgrContainer->addChild($spContainer);

            // add the shapes

            $countShapes[$sheetIndex] = 0; // count number of shapes (minus group shape), in sheet

            foreach ($sheet->getDrawingCollection() as $drawing) {
                ++$blipIndex;

                ++$countShapes[$sheetIndex];

                // add the shape
                $spContainer = new PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer();

                // set the shape type
                $spContainer->setSpType(0x004B);
                // set the shape flag
                $spContainer->setSpFlag(0x02);

                // set the shape index (we combine 1-based sheet index and $countShapes to create unique shape index)
                $reducedSpId = $countShapes[$sheetIndex];
                $spId = $reducedSpId
                    | ($sheet->getParent()->getIndex($sheet) + 1) << 10;
                $spContainer->setSpId($spId);

                // keep track of last reducedSpId
                $lastReducedSpId = $reducedSpId;

                // keep track of last spId
                $lastSpId = $spId;

                // set the BLIP index
                $spContainer->setOPT(0x4104, $blipIndex);

                // set coordinates and offsets, client anchor
                $coordinates = $drawing->getCoordinates();
                $offsetX = $drawing->getOffsetX();
                $offsetY = $drawing->getOffsetY();
                $width = $drawing->getWidth();
                $height = $drawing->getHeight();

                $twoAnchor = PHPExcel_Shared_Excel5::oneAnchor2twoAnchor($sheet, $coordinates, $offsetX, $offsetY, $width, $height);

                $spContainer->setStartCoordinates($twoAnchor['startCoordinates']);
                $spContainer->setStartOffsetX($twoAnchor['startOffsetX']);
                $spContainer->setStartOffsetY($twoAnchor['startOffsetY']);
                $spContainer->setEndCoordinates($twoAnchor['endCoordinates']);
                $spContainer->setEndOffsetX($twoAnchor['endOffsetX']);
                $spContainer->setEndOffsetY($twoAnchor['endOffsetY']);

                $spgrContainer->addChild($spContainer);
            }

            // AutoFilters
            if (!empty($filterRange)) {
                $rangeBounds = PHPExcel_Cell::rangeBoundaries($filterRange);
                $iNumColStart = $rangeBounds[0][0];
                $iNumColEnd = $rangeBounds[1][0];

                $iInc = $iNumColStart;
                while ($iInc <= $iNumColEnd) {
                    ++$countShapes[$sheetIndex];

                    // create an Drawing Object for the dropdown
                    $oDrawing  = new PHPExcel_Worksheet_BaseDrawing();
                    // get the coordinates of drawing
                    $cDrawing   = PHPExcel_Cell::stringFromColumnIndex($iInc - 1) . $rangeBounds[0][1];
                    $oDrawing->setCoordinates($cDrawing);
                    $oDrawing->setWorksheet($sheet);

                    // add the shape
                    $spContainer = new PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer();
                    // set the shape type
                    $spContainer->setSpType(0x00C9);
                    // set the shape flag
                    $spContainer->setSpFlag(0x01);

                    // set the shape index (we combine 1-based sheet index and $countShapes to create unique shape index)
                    $reducedSpId = $countShapes[$sheetIndex];
                    $spId = $reducedSpId
                        | ($sheet->getParent()->getIndex($sheet) + 1) << 10;
                    $spContainer->setSpId($spId);

                    // keep track of last reducedSpId
                    $lastReducedSpId = $reducedSpId;

                    // keep track of last spId
                    $lastSpId = $spId;

                    $spContainer->setOPT(0x007F, 0x01040104); // Protection -> fLockAgainstGrouping
                    $spContainer->setOPT(0x00BF, 0x00080008); // Text -> fFitTextToShape
                    $spContainer->setOPT(0x01BF, 0x00010000); // Fill Style -> fNoFillHitTest
                    $spContainer->setOPT(0x01FF, 0x00080000); // Line Style -> fNoLineDrawDash
                    $spContainer->setOPT(0x03BF, 0x000A0000); // Group Shape -> fPrint

                    // set coordinates and offsets, client anchor
                    $endCoordinates = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::stringFromColumnIndex($iInc - 1));
                    $endCoordinates .= $rangeBounds[0][1] + 1;

                    $spContainer->setStartCoordinates($cDrawing);
                    $spContainer->setStartOffsetX(0);
                    $spContainer->setStartOffsetY(0);
                    $spContainer->setEndCoordinates($endCoordinates);
                    $spContainer->setEndOffsetX(0);
                    $spContainer->setEndOffsetY(0);

                    $spgrContainer->addChild($spContainer);
                    $iInc++;
                }
            }

            // identifier clusters, used for workbook Escher object
            $this->IDCLs[$dgId] = $lastReducedSpId;

            // set last shape index
            $dgContainer->setLastSpId($lastSpId);

            // set the Escher object
            $this->writerWorksheets[$sheetIndex]->setEscher($escher);
        }
    }

    /**
     * Build the Escher object corresponding to the MSODRAWINGGROUP record
     */
    private function buildWorkbookEscher()
    {
        $escher = null;

        // any drawings in this workbook?
        $found = false;
        foreach ($this->phpExcel->getAllSheets() as $sheet) {
            if (count($sheet->getDrawingCollection()) > 0) {
                $found = true;
                break;
            }
        }

        // nothing to do if there are no drawings
        if (!$found) {
            return;
        }

        // if we reach here, then there are drawings in the workbook
        $escher = new PHPExcel_Shared_Escher();

        // dggContainer
        $dggContainer = new PHPExcel_Shared_Escher_DggContainer();
        $escher->setDggContainer($dggContainer);

        // set IDCLs (identifier clusters)
        $dggContainer->setIDCLs($this->IDCLs);

        // this loop is for determining maximum shape identifier of all drawing
        $spIdMax = 0;
        $totalCountShapes = 0;
        $countDrawings = 0;

        foreach ($this->phpExcel->getAllsheets() as $sheet) {
            $sheetCountShapes = 0; // count number of shapes (minus group shape), in sheet

            if (count($sheet->getDrawingCollection()) > 0) {
                ++$countDrawings;

                foreach ($sheet->getDrawingCollection() as $drawing) {
                    ++$sheetCountShapes;
                    ++$totalCountShapes;

                    $spId = $sheetCountShapes | ($this->phpExcel->getIndex($sheet) + 1) << 10;
                    $spIdMax = max($spId, $spIdMax);
                }
            }
        }

        $dggContainer->setSpIdMax($spIdMax + 1);
        $dggContainer->setCDgSaved($countDrawings);
        $dggContainer->setCSpSaved($totalCountShapes + $countDrawings); // total number of shapes incl. one group shapes per drawing

        // bstoreContainer
        $bstoreContainer = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer();
        $dggContainer->setBstoreContainer($bstoreContainer);

        // the BSE's (all the images)
        foreach ($this->phpExcel->getAllsheets() as $sheet) {
            foreach ($sheet->getDrawingCollection() as $drawing) {
                if ($drawing instanceof PHPExcel_Worksheet_Drawing) {
                    $filename = $drawing->getPath();

                    list($imagesx, $imagesy, $imageFormat) = getimagesize($filename);

                    switch ($imageFormat) {
                        case 1: // GIF, not supported by BIFF8, we convert to PNG
                            $blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG;
                            ob_start();
                            imagepng(imagecreatefromgif($filename));
                            $blipData = ob_get_contents();
                            ob_end_clean();
                            break;
                        case 2: // JPEG
                            $blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG;
                            $blipData = file_get_contents($filename);
                            break;
                        case 3: // PNG
                            $blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG;
                            $blipData = file_get_contents($filename);
                            break;
                        case 6: // Windows DIB (BMP), we convert to PNG
                            $blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG;
                            ob_start();
                            imagepng(PHPExcel_Shared_Drawing::imagecreatefrombmp($filename));
                            $blipData = ob_get_contents();
                            ob_end_clean();
                            break;
                        default:
                            continue 2;
                    }

                    $blip = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip();
                    $blip->setData($blipData);

                    $BSE = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE();
                    $BSE->setBlipType($blipType);
                    $BSE->setBlip($blip);

                    $bstoreContainer->addBSE($BSE);
                } elseif ($drawing instanceof PHPExcel_Worksheet_MemoryDrawing) {
                    switch ($drawing->getRenderingFunction()) {
                        case PHPExcel_Worksheet_MemoryDrawing::RENDERING_JPEG:
                            $blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG;
                            $renderingFunction = 'imagejpeg';
                            break;
                        case PHPExcel_Worksheet_MemoryDrawing::RENDERING_GIF:
                        case PHPExcel_Worksheet_MemoryDrawing::RENDERING_PNG:
                        case PHPExcel_Worksheet_MemoryDrawing::RENDERING_DEFAULT:
                            $blipType = PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG;
                            $renderingFunction = 'imagepng';
                            break;
                    }

                    ob_start();
                    call_user_func($renderingFunction, $drawing->getImageResource());
                    $blipData = ob_get_contents();
                    ob_end_clean();

                    $blip = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip();
                    $blip->setData($blipData);

                    $BSE = new PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE();
                    $BSE->setBlipType($blipType);
                    $BSE->setBlip($blip);

                    $bstoreContainer->addBSE($BSE);
                }
            }
        }

        // Set the Escher object
        $this->writerWorkbook->setEscher($escher);
    }

    /**
     * Build the OLE Part for DocumentSummary Information
     * @return string
     */
    private function writeDocumentSummaryInformation()
    {
        // offset: 0; size: 2; must be 0xFE 0xFF (UTF-16 LE byte order mark)
        $data = pack('v', 0xFFFE);
        // offset: 2; size: 2;
        $data .= pack('v', 0x0000);
        // offset: 4; size: 2; OS version
        $data .= pack('v', 0x0106);
        // offset: 6; size: 2; OS indicator
        $data .= pack('v', 0x0002);
        // offset: 8; size: 16
        $data .= pack('VVVV', 0x00, 0x00, 0x00, 0x00);
        // offset: 24; size: 4; section count
        $data .= pack('V', 0x0001);

        // offset: 28; size: 16; first section's class id: 02 d5 cd d5 9c 2e 1b 10 93 97 08 00 2b 2c f9 ae
        $data .= pack('vvvvvvvv', 0xD502, 0xD5CD, 0x2E9C, 0x101B, 0x9793, 0x0008, 0x2C2B, 0xAEF9);
        // offset: 44; size: 4; offset of the start
        $data .= pack('V', 0x30);

        // SECTION
        $dataSection = array();
        $dataSection_NumProps = 0;
        $dataSection_Summary = '';
        $dataSection_Content = '';

        // GKPIDDSI_CODEPAGE: CodePage
        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x01),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x02), // 2 byte signed integer
                               'data'    => array('data' => 1252));
        $dataSection_NumProps++;

        // GKPIDDSI_CATEGORY : Category
        if ($this->phpExcel->getProperties()->getCategory()) {
            $dataProp = $this->phpExcel->getProperties()->getCategory();
            $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x02),
                                   'offset' => array('pack' => 'V'),
                                   'type'     => array('pack' => 'V', 'data' => 0x1E),
                                   'data'    => array('data' => $dataProp, 'length' => strlen($dataProp)));
            $dataSection_NumProps++;
        }
        // GKPIDDSI_VERSION :Version of the application that wrote the property storage
        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x17),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x03),
                               'data'    => array('pack' => 'V', 'data' => 0x000C0000));
        $dataSection_NumProps++;
        // GKPIDDSI_SCALE : FALSE
        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x0B),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x0B),
                               'data'    => array('data' => false));
        $dataSection_NumProps++;
        // GKPIDDSI_LINKSDIRTY : True if any of the values for the linked properties have changed outside of the application
        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x10),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x0B),
                               'data'    => array('data' => false));
        $dataSection_NumProps++;
        // GKPIDDSI_SHAREDOC : FALSE
        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x13),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x0B),
                               'data'    => array('data' => false));
        $dataSection_NumProps++;
        // GKPIDDSI_HYPERLINKSCHANGED : True if any of the values for the _PID_LINKS (hyperlink text) have changed outside of the application
        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x16),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x0B),
                               'data'    => array('data' => false));
        $dataSection_NumProps++;

        // GKPIDDSI_DOCSPARTS
        // MS-OSHARED p75 (2.3.3.2.2.1)
        // Structure is VtVecUnalignedLpstrValue (2.3.3.1.9)
        // cElements
        $dataProp = pack('v', 0x0001);
        $dataProp .= pack('v', 0x0000);
        // array of UnalignedLpstr
          // cch
          $dataProp .= pack('v', 0x000A);
          $dataProp .= pack('v', 0x0000);
          // value
          $dataProp .= 'Worksheet'.chr(0);

        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x0D),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x101E),
                               'data'    => array('data' => $dataProp, 'length' => strlen($dataProp)));
        $dataSection_NumProps++;

        // GKPIDDSI_HEADINGPAIR
        // VtVecHeadingPairValue
          // cElements
          $dataProp = pack('v', 0x0002);
          $dataProp .= pack('v', 0x0000);
          // Array of vtHeadingPair
            // vtUnalignedString - headingString
              // stringType
              $dataProp .= pack('v', 0x001E);
              // padding
              $dataProp .= pack('v', 0x0000);
              // UnalignedLpstr
                // cch
                $dataProp .= pack('v', 0x0013);
                $dataProp .= pack('v', 0x0000);
                // value
                $dataProp .= 'Feuilles de calcul';
            // vtUnalignedString - headingParts
              // wType : 0x0003 = 32 bit signed integer
              $dataProp .= pack('v', 0x0300);
              // padding
              $dataProp .= pack('v', 0x0000);
              // value
              $dataProp .= pack('v', 0x0100);
              $dataProp .= pack('v', 0x0000);
              $dataProp .= pack('v', 0x0000);
              $dataProp .= pack('v', 0x0000);

        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x0C),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x100C),
                               'data'    => array('data' => $dataProp, 'length' => strlen($dataProp)));
        $dataSection_NumProps++;

        //         4     Section Length
        //        4     Property count
        //        8 * $dataSection_NumProps (8 =  ID (4) + OffSet(4))
        $dataSection_Content_Offset = 8 + $dataSection_NumProps * 8;
        foreach ($dataSection as $dataProp) {
            // Summary
            $dataSection_Summary .= pack($dataProp['summary']['pack'], $dataProp['summary']['data']);
            // Offset
            $dataSection_Summary .= pack($dataProp['offset']['pack'], $dataSection_Content_Offset);
            // DataType
            $dataSection_Content .= pack($dataProp['type']['pack'], $dataProp['type']['data']);
            // Data
            if ($dataProp['type']['data'] == 0x02) { // 2 byte signed integer
                $dataSection_Content .= pack('V', $dataProp['data']['data']);

                $dataSection_Content_Offset += 4 + 4;
            } elseif ($dataProp['type']['data'] == 0x03) { // 4 byte signed integer
                $dataSection_Content .= pack('V', $dataProp['data']['data']);

                $dataSection_Content_Offset += 4 + 4;
            } elseif ($dataProp['type']['data'] == 0x0B) { // Boolean
                if ($dataProp['data']['data'] == false) {
                    $dataSection_Content .= pack('V', 0x0000);
                } else {
                    $dataSection_Content .= pack('V', 0x0001);
                }
                $dataSection_Content_Offset += 4 + 4;
            } elseif ($dataProp['type']['data'] == 0x1E) { // null-terminated string prepended by dword string length
                // Null-terminated string
                $dataProp['data']['data'] .= chr(0);
                $dataProp['data']['length'] += 1;
                // Complete the string with null string for being a %4
                $dataProp['data']['length'] = $dataProp['data']['length'] + ((4 - $dataProp['data']['length'] % 4)==4 ? 0 : (4 - $dataProp['data']['length'] % 4));
                $dataProp['data']['data'] = str_pad($dataProp['data']['data'], $dataProp['data']['length'], chr(0), STR_PAD_RIGHT);

                $dataSection_Content .= pack('V', $dataProp['data']['length']);
                $dataSection_Content .= $dataProp['data']['data'];

                $dataSection_Content_Offset += 4 + 4 + strlen($dataProp['data']['data']);
            } elseif ($dataProp['type']['data'] == 0x40) { // Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                $dataSection_Content .= $dataProp['data']['data'];

                $dataSection_Content_Offset += 4 + 8;
            } else {
                // Data Type Not Used at the moment
                $dataSection_Content .= $dataProp['data']['data'];

                $dataSection_Content_Offset += 4 + $dataProp['data']['length'];
            }
        }
        // Now $dataSection_Content_Offset contains the size of the content

        // section header
        // offset: $secOffset; size: 4; section length
        //         + x  Size of the content (summary + content)
        $data .= pack('V', $dataSection_Content_Offset);
        // offset: $secOffset+4; size: 4; property count
        $data .= pack('V', $dataSection_NumProps);
        // Section Summary
        $data .= $dataSection_Summary;
        // Section Content
        $data .= $dataSection_Content;

        return $data;
    }

    /**
     * Build the OLE Part for Summary Information
     * @return string
     */
    private function writeSummaryInformation()
    {
        // offset: 0; size: 2; must be 0xFE 0xFF (UTF-16 LE byte order mark)
        $data = pack('v', 0xFFFE);
        // offset: 2; size: 2;
        $data .= pack('v', 0x0000);
        // offset: 4; size: 2; OS version
        $data .= pack('v', 0x0106);
        // offset: 6; size: 2; OS indicator
        $data .= pack('v', 0x0002);
        // offset: 8; size: 16
        $data .= pack('VVVV', 0x00, 0x00, 0x00, 0x00);
        // offset: 24; size: 4; section count
        $data .= pack('V', 0x0001);

        // offset: 28; size: 16; first section's class id: e0 85 9f f2 f9 4f 68 10 ab 91 08 00 2b 27 b3 d9
        $data .= pack('vvvvvvvv', 0x85E0, 0xF29F, 0x4FF9, 0x1068, 0x91AB, 0x0008, 0x272B, 0xD9B3);
        // offset: 44; size: 4; offset of the start
        $data .= pack('V', 0x30);

        // SECTION
        $dataSection = array();
        $dataSection_NumProps = 0;
        $dataSection_Summary = '';
        $dataSection_Content = '';

        // CodePage : CP-1252
        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x01),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x02), // 2 byte signed integer
                               'data'    => array('data' => 1252));
        $dataSection_NumProps++;

        //    Title
        if ($this->phpExcel->getProperties()->getTitle()) {
            $dataProp = $this->phpExcel->getProperties()->getTitle();
            $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x02),
                                   'offset' => array('pack' => 'V'),
                                   'type'     => array('pack' => 'V', 'data' => 0x1E), // null-terminated string prepended by dword string length
                                   'data'    => array('data' => $dataProp, 'length' => strlen($dataProp)));
            $dataSection_NumProps++;
        }
        //    Subject
        if ($this->phpExcel->getProperties()->getSubject()) {
            $dataProp = $this->phpExcel->getProperties()->getSubject();
            $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x03),
                                   'offset' => array('pack' => 'V'),
                                   'type'     => array('pack' => 'V', 'data' => 0x1E), // null-terminated string prepended by dword string length
                                   'data'    => array('data' => $dataProp, 'length' => strlen($dataProp)));
            $dataSection_NumProps++;
        }
        //    Author (Creator)
        if ($this->phpExcel->getProperties()->getCreator()) {
            $dataProp = $this->phpExcel->getProperties()->getCreator();
            $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x04),
                                   'offset' => array('pack' => 'V'),
                                   'type'     => array('pack' => 'V', 'data' => 0x1E), // null-terminated string prepended by dword string length
                                   'data'    => array('data' => $dataProp, 'length' => strlen($dataProp)));
            $dataSection_NumProps++;
        }
        //    Keywords
        if ($this->phpExcel->getProperties()->getKeywords()) {
            $dataProp = $this->phpExcel->getProperties()->getKeywords();
            $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x05),
                                   'offset' => array('pack' => 'V'),
                                   'type'     => array('pack' => 'V', 'data' => 0x1E), // null-terminated string prepended by dword string length
                                   'data'    => array('data' => $dataProp, 'length' => strlen($dataProp)));
            $dataSection_NumProps++;
        }
        //    Comments (Description)
        if ($this->phpExcel->getProperties()->getDescription()) {
            $dataProp = $this->phpExcel->getProperties()->getDescription();
            $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x06),
                                   'offset' => array('pack' => 'V'),
                                   'type'     => array('pack' => 'V', 'data' => 0x1E), // null-terminated string prepended by dword string length
                                   'data'    => array('data' => $dataProp, 'length' => strlen($dataProp)));
            $dataSection_NumProps++;
        }
        //    Last Saved By (LastModifiedBy)
        if ($this->phpExcel->getProperties()->getLastModifiedBy()) {
            $dataProp = $this->phpExcel->getProperties()->getLastModifiedBy();
            $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x08),
                                   'offset' => array('pack' => 'V'),
                                   'type'     => array('pack' => 'V', 'data' => 0x1E), // null-terminated string prepended by dword string length
                                   'data'    => array('data' => $dataProp, 'length' => strlen($dataProp)));
            $dataSection_NumProps++;
        }
        //    Created Date/Time
        if ($this->phpExcel->getProperties()->getCreated()) {
            $dataProp = $this->phpExcel->getProperties()->getCreated();
            $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x0C),
                                   'offset' => array('pack' => 'V'),
                                   'type'     => array('pack' => 'V', 'data' => 0x40), // Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                                   'data'    => array('data' => PHPExcel_Shared_OLE::LocalDate2OLE($dataProp)));
            $dataSection_NumProps++;
        }
        //    Modified Date/Time
        if ($this->phpExcel->getProperties()->getModified()) {
            $dataProp = $this->phpExcel->getProperties()->getModified();
            $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x0D),
                                   'offset' => array('pack' => 'V'),
                                   'type'     => array('pack' => 'V', 'data' => 0x40), // Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                                   'data'    => array('data' => PHPExcel_Shared_OLE::LocalDate2OLE($dataProp)));
            $dataSection_NumProps++;
        }
        //    Security
        $dataSection[] = array('summary'=> array('pack' => 'V', 'data' => 0x13),
                               'offset' => array('pack' => 'V'),
                               'type'     => array('pack' => 'V', 'data' => 0x03), // 4 byte signed integer
                               'data'    => array('data' => 0x00));
        $dataSection_NumProps++;


        //         4     Section Length
        //        4     Property count
        //        8 * $dataSection_NumProps (8 =  ID (4) + OffSet(4))
        $dataSection_Content_Offset = 8 + $dataSection_NumProps * 8;
        foreach ($dataSection as $dataProp) {
            // Summary
            $dataSection_Summary .= pack($dataProp['summary']['pack'], $dataProp['summary']['data']);
            // Offset
            $dataSection_Summary .= pack($dataProp['offset']['pack'], $dataSection_Content_Offset);
            // DataType
            $dataSection_Content .= pack($dataProp['type']['pack'], $dataProp['type']['data']);
            // Data
            if ($dataProp['type']['data'] == 0x02) { // 2 byte signed integer
                $dataSection_Content .= pack('V', $dataProp['data']['data']);

                $dataSection_Content_Offset += 4 + 4;
            } elseif ($dataProp['type']['data'] == 0x03) { // 4 byte signed integer
                $dataSection_Content .= pack('V', $dataProp['data']['data']);

                $dataSection_Content_Offset += 4 + 4;
            } elseif ($dataProp['type']['data'] == 0x1E) { // null-terminated string prepended by dword string length
                // Null-terminated string
                $dataProp['data']['data'] .= chr(0);
                $dataProp['data']['length'] += 1;
                // Complete the string with null string for being a %4
                $dataProp['data']['length'] = $dataProp['data']['length'] + ((4 - $dataProp['data']['length'] % 4)==4 ? 0 : (4 - $dataProp['data']['length'] % 4));
                $dataProp['data']['data'] = str_pad($dataProp['data']['data'], $dataProp['data']['length'], chr(0), STR_PAD_RIGHT);

                $dataSection_Content .= pack('V', $dataProp['data']['length']);
                $dataSection_Content .= $dataProp['data']['data'];

                $dataSection_Content_Offset += 4 + 4 + strlen($dataProp['data']['data']);
            } elseif ($dataProp['type']['data'] == 0x40) { // Filetime (64-bit value representing the number of 100-nanosecond intervals since January 1, 1601)
                $dataSection_Content .= $dataProp['data']['data'];

                $dataSection_Content_Offset += 4 + 8;
            } else {
                // Data Type Not Used at the moment
            }
        }
        // Now $dataSection_Content_Offset contains the size of the content

        // section header
        // offset: $secOffset; size: 4; section length
        //         + x  Size of the content (summary + content)
        $data .= pack('V', $dataSection_Content_Offset);
        // offset: $secOffset+4; size: 4; property count
        $data .= pack('V', $dataSection_NumProps);
        // Section Summary
        $data .= $dataSection_Summary;
        // Section Content
        $data .= $dataSection_Content;

        return $data;
    }
}
