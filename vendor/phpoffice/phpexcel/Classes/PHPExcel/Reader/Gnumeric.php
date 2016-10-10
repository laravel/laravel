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
 * PHPExcel_Reader_Gnumeric
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
class PHPExcel_Reader_Gnumeric extends PHPExcel_Reader_Abstract implements PHPExcel_Reader_IReader
{
    /**
     * Formats
     *
     * @var array
     */
    private $styles = array();

    /**
     * Shared Expressions
     *
     * @var array
     */
    private $expressions = array();

    private $referenceHelper = null;

    /**
     * Create a new PHPExcel_Reader_Gnumeric
     */
    public function __construct()
    {
        $this->readFilter     = new PHPExcel_Reader_DefaultReadFilter();
        $this->referenceHelper = PHPExcel_ReferenceHelper::getInstance();
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

        // Check if gzlib functions are available
        if (!function_exists('gzread')) {
            throw new PHPExcel_Reader_Exception("gzlib library is not enabled");
        }

        // Read signature data (first 3 bytes)
        $fh = fopen($pFilename, 'r');
        $data = fread($fh, 2);
        fclose($fh);

        if ($data != chr(0x1F).chr(0x8B)) {
            return false;
        }

        return true;
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

        $xml = new XMLReader();
        $xml->xml($this->securityScanFile('compress.zlib://'.realpath($pFilename)), null, PHPExcel_Settings::getLibXmlLoaderOptions());
        $xml->setParserProperty(2, true);

        $worksheetNames = array();
        while ($xml->read()) {
            if ($xml->name == 'gnm:SheetName' && $xml->nodeType == XMLReader::ELEMENT) {
                $xml->read();    //    Move onto the value node
                $worksheetNames[] = (string) $xml->value;
            } elseif ($xml->name == 'gnm:Sheets') {
                //    break out of the loop once we've got our sheet names rather than parse the entire file
                break;
            }
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

        $xml = new XMLReader();
        $xml->xml($this->securityScanFile('compress.zlib://'.realpath($pFilename)), null, PHPExcel_Settings::getLibXmlLoaderOptions());
        $xml->setParserProperty(2, true);

        $worksheetInfo = array();
        while ($xml->read()) {
            if ($xml->name == 'gnm:Sheet' && $xml->nodeType == XMLReader::ELEMENT) {
                $tmpInfo = array(
                    'worksheetName' => '',
                    'lastColumnLetter' => 'A',
                    'lastColumnIndex' => 0,
                    'totalRows' => 0,
                    'totalColumns' => 0,
                );

                while ($xml->read()) {
                    if ($xml->name == 'gnm:Name' && $xml->nodeType == XMLReader::ELEMENT) {
                        $xml->read();    //    Move onto the value node
                        $tmpInfo['worksheetName'] = (string) $xml->value;
                    } elseif ($xml->name == 'gnm:MaxCol' && $xml->nodeType == XMLReader::ELEMENT) {
                        $xml->read();    //    Move onto the value node
                        $tmpInfo['lastColumnIndex'] = (int) $xml->value;
                        $tmpInfo['totalColumns'] = (int) $xml->value + 1;
                    } elseif ($xml->name == 'gnm:MaxRow' && $xml->nodeType == XMLReader::ELEMENT) {
                        $xml->read();    //    Move onto the value node
                        $tmpInfo['totalRows'] = (int) $xml->value + 1;
                        break;
                    }
                }
                $tmpInfo['lastColumnLetter'] = PHPExcel_Cell::stringFromColumnIndex($tmpInfo['lastColumnIndex']);
                $worksheetInfo[] = $tmpInfo;
            }
        }

        return $worksheetInfo;
    }

    private function gzfileGetContents($filename)
    {
        $file = @gzopen($filename, 'rb');
        if ($file !== false) {
            $data = '';
            while (!gzeof($file)) {
                $data .= gzread($file, 1024);
            }
            gzclose($file);
        }
        return $data;
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
        // Check if file exists
        if (!file_exists($pFilename)) {
            throw new PHPExcel_Reader_Exception("Could not open " . $pFilename . " for reading! File does not exist.");
        }

        $timezoneObj = new DateTimeZone('Europe/London');
        $GMT = new DateTimeZone('UTC');

        $gFileData = $this->gzfileGetContents($pFilename);

//        echo '<pre>';
//        echo htmlentities($gFileData,ENT_QUOTES,'UTF-8');
//        echo '</pre><hr />';
//
        $xml = simplexml_load_string($this->securityScan($gFileData), 'SimpleXMLElement', PHPExcel_Settings::getLibXmlLoaderOptions());
        $namespacesMeta = $xml->getNamespaces(true);

//        var_dump($namespacesMeta);
//
        $gnmXML = $xml->children($namespacesMeta['gnm']);

        $docProps = $objPHPExcel->getProperties();
        //    Document Properties are held differently, depending on the version of Gnumeric
        if (isset($namespacesMeta['office'])) {
            $officeXML = $xml->children($namespacesMeta['office']);
            $officeDocXML = $officeXML->{'document-meta'};
            $officeDocMetaXML = $officeDocXML->meta;

            foreach ($officeDocMetaXML as $officePropertyData) {
                $officePropertyDC = array();
                if (isset($namespacesMeta['dc'])) {
                    $officePropertyDC = $officePropertyData->children($namespacesMeta['dc']);
                }
                foreach ($officePropertyDC as $propertyName => $propertyValue) {
                    $propertyValue = (string) $propertyValue;
                    switch ($propertyName) {
                        case 'title':
                            $docProps->setTitle(trim($propertyValue));
                            break;
                        case 'subject':
                            $docProps->setSubject(trim($propertyValue));
                            break;
                        case 'creator':
                            $docProps->setCreator(trim($propertyValue));
                            $docProps->setLastModifiedBy(trim($propertyValue));
                            break;
                        case 'date':
                            $creationDate = strtotime(trim($propertyValue));
                            $docProps->setCreated($creationDate);
                            $docProps->setModified($creationDate);
                            break;
                        case 'description':
                            $docProps->setDescription(trim($propertyValue));
                            break;
                    }
                }
                $officePropertyMeta = array();
                if (isset($namespacesMeta['meta'])) {
                    $officePropertyMeta = $officePropertyData->children($namespacesMeta['meta']);
                }
                foreach ($officePropertyMeta as $propertyName => $propertyValue) {
                    $attributes = $propertyValue->attributes($namespacesMeta['meta']);
                    $propertyValue = (string) $propertyValue;
                    switch ($propertyName) {
                        case 'keyword':
                            $docProps->setKeywords(trim($propertyValue));
                            break;
                        case 'initial-creator':
                            $docProps->setCreator(trim($propertyValue));
                            $docProps->setLastModifiedBy(trim($propertyValue));
                            break;
                        case 'creation-date':
                            $creationDate = strtotime(trim($propertyValue));
                            $docProps->setCreated($creationDate);
                            $docProps->setModified($creationDate);
                            break;
                        case 'user-defined':
                            list(, $attrName) = explode(':', $attributes['name']);
                            switch ($attrName) {
                                case 'publisher':
                                    $docProps->setCompany(trim($propertyValue));
                                    break;
                                case 'category':
                                    $docProps->setCategory(trim($propertyValue));
                                    break;
                                case 'manager':
                                    $docProps->setManager(trim($propertyValue));
                                    break;
                            }
                            break;
                    }
                }
            }
        } elseif (isset($gnmXML->Summary)) {
            foreach ($gnmXML->Summary->Item as $summaryItem) {
                $propertyName = $summaryItem->name;
                $propertyValue = $summaryItem->{'val-string'};
                switch ($propertyName) {
                    case 'title':
                        $docProps->setTitle(trim($propertyValue));
                        break;
                    case 'comments':
                        $docProps->setDescription(trim($propertyValue));
                        break;
                    case 'keywords':
                        $docProps->setKeywords(trim($propertyValue));
                        break;
                    case 'category':
                        $docProps->setCategory(trim($propertyValue));
                        break;
                    case 'manager':
                        $docProps->setManager(trim($propertyValue));
                        break;
                    case 'author':
                        $docProps->setCreator(trim($propertyValue));
                        $docProps->setLastModifiedBy(trim($propertyValue));
                        break;
                    case 'company':
                        $docProps->setCompany(trim($propertyValue));
                        break;
                }
            }
        }

        $worksheetID = 0;
        foreach ($gnmXML->Sheets->Sheet as $sheet) {
            $worksheetName = (string) $sheet->Name;
//            echo '<b>Worksheet: ', $worksheetName,'</b><br />';
            if ((isset($this->loadSheetsOnly)) && (!in_array($worksheetName, $this->loadSheetsOnly))) {
                continue;
            }

            $maxRow = $maxCol = 0;

            // Create new Worksheet
            $objPHPExcel->createSheet();
            $objPHPExcel->setActiveSheetIndex($worksheetID);
            //    Use false for $updateFormulaCellReferences to prevent adjustment of worksheet references in formula
            //        cells... during the load, all formulae should be correct, and we're simply bringing the worksheet
            //        name in line with the formula, not the reverse
            $objPHPExcel->getActiveSheet()->setTitle($worksheetName, false);

            if ((!$this->readDataOnly) && (isset($sheet->PrintInformation))) {
                if (isset($sheet->PrintInformation->Margins)) {
                    foreach ($sheet->PrintInformation->Margins->children('gnm', true) as $key => $margin) {
                        $marginAttributes = $margin->attributes();
                        $marginSize = 72 / 100;    //    Default
                        switch ($marginAttributes['PrefUnit']) {
                            case 'mm':
                                $marginSize = intval($marginAttributes['Points']) / 100;
                                break;
                        }
                        switch ($key) {
                            case 'top':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setTop($marginSize);
                                break;
                            case 'bottom':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setBottom($marginSize);
                                break;
                            case 'left':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setLeft($marginSize);
                                break;
                            case 'right':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setRight($marginSize);
                                break;
                            case 'header':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setHeader($marginSize);
                                break;
                            case 'footer':
                                $objPHPExcel->getActiveSheet()->getPageMargins()->setFooter($marginSize);
                                break;
                        }
                    }
                }
            }

            foreach ($sheet->Cells->Cell as $cell) {
                $cellAttributes = $cell->attributes();
                $row = (int) $cellAttributes->Row + 1;
                $column = (int) $cellAttributes->Col;

                if ($row > $maxRow) {
                    $maxRow = $row;
                }
                if ($column > $maxCol) {
                    $maxCol = $column;
                }

                $column = PHPExcel_Cell::stringFromColumnIndex($column);

                // Read cell?
                if ($this->getReadFilter() !== null) {
                    if (!$this->getReadFilter()->readCell($column, $row, $worksheetName)) {
                        continue;
                    }
                }

                $ValueType = $cellAttributes->ValueType;
                $ExprID = (string) $cellAttributes->ExprID;
//                echo 'Cell ', $column, $row,'<br />';
//                echo 'Type is ', $ValueType,'<br />';
//                echo 'Value is ', $cell,'<br />';
                $type = PHPExcel_Cell_DataType::TYPE_FORMULA;
                if ($ExprID > '') {
                    if (((string) $cell) > '') {
                        $this->expressions[$ExprID] = array(
                            'column'    => $cellAttributes->Col,
                            'row'        => $cellAttributes->Row,
                            'formula'    => (string) $cell
                        );
//                        echo 'NEW EXPRESSION ', $ExprID,'<br />';
                    } else {
                        $expression = $this->expressions[$ExprID];

                        $cell = $this->referenceHelper->updateFormulaReferences(
                            $expression['formula'],
                            'A1',
                            $cellAttributes->Col - $expression['column'],
                            $cellAttributes->Row - $expression['row'],
                            $worksheetName
                        );
//                        echo 'SHARED EXPRESSION ', $ExprID,'<br />';
//                        echo 'New Value is ', $cell,'<br />';
                    }
                    $type = PHPExcel_Cell_DataType::TYPE_FORMULA;
                } else {
                    switch ($ValueType) {
                        case '10':        //    NULL
                            $type = PHPExcel_Cell_DataType::TYPE_NULL;
                            break;
                        case '20':        //    Boolean
                            $type = PHPExcel_Cell_DataType::TYPE_BOOL;
                            $cell = ($cell == 'TRUE') ? true: false;
                            break;
                        case '30':        //    Integer
                            $cell = intval($cell);
                            // Excel 2007+ doesn't differentiate between integer and float, so set the value and dropthru to the next (numeric) case
                        case '40':        //    Float
                            $type = PHPExcel_Cell_DataType::TYPE_NUMERIC;
                            break;
                        case '50':        //    Error
                            $type = PHPExcel_Cell_DataType::TYPE_ERROR;
                            break;
                        case '60':        //    String
                            $type = PHPExcel_Cell_DataType::TYPE_STRING;
                            break;
                        case '70':        //    Cell Range
                        case '80':        //    Array
                    }
                }
                $objPHPExcel->getActiveSheet()->getCell($column.$row)->setValueExplicit($cell, $type);
            }

            if ((!$this->readDataOnly) && (isset($sheet->Objects))) {
                foreach ($sheet->Objects->children('gnm', true) as $key => $comment) {
                    $commentAttributes = $comment->attributes();
                    //    Only comment objects are handled at the moment
                    if ($commentAttributes->Text) {
                        $objPHPExcel->getActiveSheet()->getComment((string)$commentAttributes->ObjectBound)->setAuthor((string)$commentAttributes->Author)->setText($this->parseRichText((string)$commentAttributes->Text));
                    }
                }
            }
//            echo '$maxCol=', $maxCol,'; $maxRow=', $maxRow,'<br />';
//
            foreach ($sheet->Styles->StyleRegion as $styleRegion) {
                $styleAttributes = $styleRegion->attributes();
                if (($styleAttributes['startRow'] <= $maxRow) &&
                    ($styleAttributes['startCol'] <= $maxCol)) {
                    $startColumn = PHPExcel_Cell::stringFromColumnIndex((int) $styleAttributes['startCol']);
                    $startRow = $styleAttributes['startRow'] + 1;

                    $endColumn = ($styleAttributes['endCol'] > $maxCol) ? $maxCol : (int) $styleAttributes['endCol'];
                    $endColumn = PHPExcel_Cell::stringFromColumnIndex($endColumn);
                    $endRow = ($styleAttributes['endRow'] > $maxRow) ? $maxRow : $styleAttributes['endRow'];
                    $endRow += 1;
                    $cellRange = $startColumn.$startRow.':'.$endColumn.$endRow;
//                    echo $cellRange,'<br />';

                    $styleAttributes = $styleRegion->Style->attributes();
//                    var_dump($styleAttributes);
//                    echo '<br />';

                    //    We still set the number format mask for date/time values, even if readDataOnly is true
                    if ((!$this->readDataOnly) ||
                        (PHPExcel_Shared_Date::isDateTimeFormatCode((string) $styleAttributes['Format']))) {
                        $styleArray = array();
                        $styleArray['numberformat']['code'] = (string) $styleAttributes['Format'];
                        //    If readDataOnly is false, we set all formatting information
                        if (!$this->readDataOnly) {
                            switch ($styleAttributes['HAlign']) {
                                case '1':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_GENERAL;
                                    break;
                                case '2':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_LEFT;
                                    break;
                                case '4':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_RIGHT;
                                    break;
                                case '8':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER;
                                    break;
                                case '16':
                                case '64':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_CENTER_CONTINUOUS;
                                    break;
                                case '32':
                                    $styleArray['alignment']['horizontal'] = PHPExcel_Style_Alignment::HORIZONTAL_JUSTIFY;
                                    break;
                            }

                            switch ($styleAttributes['VAlign']) {
                                case '1':
                                    $styleArray['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_TOP;
                                    break;
                                case '2':
                                    $styleArray['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_BOTTOM;
                                    break;
                                case '4':
                                    $styleArray['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_CENTER;
                                    break;
                                case '8':
                                    $styleArray['alignment']['vertical'] = PHPExcel_Style_Alignment::VERTICAL_JUSTIFY;
                                    break;
                            }

                            $styleArray['alignment']['wrap'] = ($styleAttributes['WrapText'] == '1') ? true : false;
                            $styleArray['alignment']['shrinkToFit'] = ($styleAttributes['ShrinkToFit'] == '1') ? true : false;
                            $styleArray['alignment']['indent'] = (intval($styleAttributes["Indent"]) > 0) ? $styleAttributes["indent"] : 0;

                            $RGB = self::parseGnumericColour($styleAttributes["Fore"]);
                            $styleArray['font']['color']['rgb'] = $RGB;
                            $RGB = self::parseGnumericColour($styleAttributes["Back"]);
                            $shade = $styleAttributes["Shade"];
                            if (($RGB != '000000') || ($shade != '0')) {
                                $styleArray['fill']['color']['rgb'] = $styleArray['fill']['startcolor']['rgb'] = $RGB;
                                $RGB2 = self::parseGnumericColour($styleAttributes["PatternColor"]);
                                $styleArray['fill']['endcolor']['rgb'] = $RGB2;
                                switch ($shade) {
                                    case '1':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_SOLID;
                                        break;
                                    case '2':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR;
                                        break;
                                    case '3':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_GRADIENT_PATH;
                                        break;
                                    case '4':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKDOWN;
                                        break;
                                    case '5':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKGRAY;
                                        break;
                                    case '6':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKGRID;
                                        break;
                                    case '7':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKHORIZONTAL;
                                        break;
                                    case '8':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKTRELLIS;
                                        break;
                                    case '9':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKUP;
                                        break;
                                    case '10':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_DARKVERTICAL;
                                        break;
                                    case '11':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_GRAY0625;
                                        break;
                                    case '12':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_GRAY125;
                                        break;
                                    case '13':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTDOWN;
                                        break;
                                    case '14':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRAY;
                                        break;
                                    case '15':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTGRID;
                                        break;
                                    case '16':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTHORIZONTAL;
                                        break;
                                    case '17':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTTRELLIS;
                                        break;
                                    case '18':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTUP;
                                        break;
                                    case '19':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_LIGHTVERTICAL;
                                        break;
                                    case '20':
                                        $styleArray['fill']['type'] = PHPExcel_Style_Fill::FILL_PATTERN_MEDIUMGRAY;
                                        break;
                                }
                            }

                            $fontAttributes = $styleRegion->Style->Font->attributes();
//                            var_dump($fontAttributes);
//                            echo '<br />';
                            $styleArray['font']['name'] = (string) $styleRegion->Style->Font;
                            $styleArray['font']['size'] = intval($fontAttributes['Unit']);
                            $styleArray['font']['bold'] = ($fontAttributes['Bold'] == '1') ? true : false;
                            $styleArray['font']['italic'] = ($fontAttributes['Italic'] == '1') ? true : false;
                            $styleArray['font']['strike'] = ($fontAttributes['StrikeThrough'] == '1') ? true : false;
                            switch ($fontAttributes['Underline']) {
                                case '1':
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_SINGLE;
                                    break;
                                case '2':
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_DOUBLE;
                                    break;
                                case '3':
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_SINGLEACCOUNTING;
                                    break;
                                case '4':
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_DOUBLEACCOUNTING;
                                    break;
                                default:
                                    $styleArray['font']['underline'] = PHPExcel_Style_Font::UNDERLINE_NONE;
                                    break;
                            }
                            switch ($fontAttributes['Script']) {
                                case '1':
                                    $styleArray['font']['superScript'] = true;
                                    break;
                                case '-1':
                                    $styleArray['font']['subScript'] = true;
                                    break;
                            }

                            if (isset($styleRegion->Style->StyleBorder)) {
                                if (isset($styleRegion->Style->StyleBorder->Top)) {
                                    $styleArray['borders']['top'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Top->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Bottom)) {
                                    $styleArray['borders']['bottom'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Bottom->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Left)) {
                                    $styleArray['borders']['left'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Left->attributes());
                                }
                                if (isset($styleRegion->Style->StyleBorder->Right)) {
                                    $styleArray['borders']['right'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Right->attributes());
                                }
                                if ((isset($styleRegion->Style->StyleBorder->Diagonal)) && (isset($styleRegion->Style->StyleBorder->{'Rev-Diagonal'}))) {
                                    $styleArray['borders']['diagonal'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Diagonal->attributes());
                                    $styleArray['borders']['diagonaldirection'] = PHPExcel_Style_Borders::DIAGONAL_BOTH;
                                } elseif (isset($styleRegion->Style->StyleBorder->Diagonal)) {
                                    $styleArray['borders']['diagonal'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->Diagonal->attributes());
                                    $styleArray['borders']['diagonaldirection'] = PHPExcel_Style_Borders::DIAGONAL_UP;
                                } elseif (isset($styleRegion->Style->StyleBorder->{'Rev-Diagonal'})) {
                                    $styleArray['borders']['diagonal'] = self::parseBorderAttributes($styleRegion->Style->StyleBorder->{'Rev-Diagonal'}->attributes());
                                    $styleArray['borders']['diagonaldirection'] = PHPExcel_Style_Borders::DIAGONAL_DOWN;
                                }
                            }
                            if (isset($styleRegion->Style->HyperLink)) {
                                //    TO DO
                                $hyperlink = $styleRegion->Style->HyperLink->attributes();
                            }
                        }
//                        var_dump($styleArray);
//                        echo '<br />';
                        $objPHPExcel->getActiveSheet()->getStyle($cellRange)->applyFromArray($styleArray);
                    }
                }
            }

            if ((!$this->readDataOnly) && (isset($sheet->Cols))) {
                //    Column Widths
                $columnAttributes = $sheet->Cols->attributes();
                $defaultWidth = $columnAttributes['DefaultSizePts']  / 5.4;
                $c = 0;
                foreach ($sheet->Cols->ColInfo as $columnOverride) {
                    $columnAttributes = $columnOverride->attributes();
                    $column = $columnAttributes['No'];
                    $columnWidth = $columnAttributes['Unit']  / 5.4;
                    $hidden = ((isset($columnAttributes['Hidden'])) && ($columnAttributes['Hidden'] == '1')) ? true : false;
                    $columnCount = (isset($columnAttributes['Count'])) ? $columnAttributes['Count'] : 1;
                    while ($c < $column) {
                        $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setWidth($defaultWidth);
                        ++$c;
                    }
                    while (($c < ($column+$columnCount)) && ($c <= $maxCol)) {
                        $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setWidth($columnWidth);
                        if ($hidden) {
                            $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setVisible(false);
                        }
                        ++$c;
                    }
                }
                while ($c <= $maxCol) {
                    $objPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($c))->setWidth($defaultWidth);
                    ++$c;
                }
            }

            if ((!$this->readDataOnly) && (isset($sheet->Rows))) {
                //    Row Heights
                $rowAttributes = $sheet->Rows->attributes();
                $defaultHeight = $rowAttributes['DefaultSizePts'];
                $r = 0;

                foreach ($sheet->Rows->RowInfo as $rowOverride) {
                    $rowAttributes = $rowOverride->attributes();
                    $row = $rowAttributes['No'];
                    $rowHeight = $rowAttributes['Unit'];
                    $hidden = ((isset($rowAttributes['Hidden'])) && ($rowAttributes['Hidden'] == '1')) ? true : false;
                    $rowCount = (isset($rowAttributes['Count'])) ? $rowAttributes['Count'] : 1;
                    while ($r < $row) {
                        ++$r;
                        $objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight($defaultHeight);
                    }
                    while (($r < ($row+$rowCount)) && ($r < $maxRow)) {
                        ++$r;
                        $objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight($rowHeight);
                        if ($hidden) {
                            $objPHPExcel->getActiveSheet()->getRowDimension($r)->setVisible(false);
                        }
                    }
                }
                while ($r < $maxRow) {
                    ++$r;
                    $objPHPExcel->getActiveSheet()->getRowDimension($r)->setRowHeight($defaultHeight);
                }
            }

            //    Handle Merged Cells in this worksheet
            if (isset($sheet->MergedRegions)) {
                foreach ($sheet->MergedRegions->Merge as $mergeCells) {
                    if (strpos($mergeCells, ':') !== false) {
                        $objPHPExcel->getActiveSheet()->mergeCells($mergeCells);
                    }
                }
            }

            $worksheetID++;
        }

        //    Loop through definedNames (global named ranges)
        if (isset($gnmXML->Names)) {
            foreach ($gnmXML->Names->Name as $namedRange) {
                $name = (string) $namedRange->name;
                $range = (string) $namedRange->value;
                if (stripos($range, '#REF!') !== false) {
                    continue;
                }

                $range = explode('!', $range);
                $range[0] = trim($range[0], "'");
                if ($worksheet = $objPHPExcel->getSheetByName($range[0])) {
                    $extractedRange = str_replace('$', '', $range[1]);
                    $objPHPExcel->addNamedRange(new PHPExcel_NamedRange($name, $worksheet, $extractedRange));
                }
            }
        }

        // Return
        return $objPHPExcel;
    }

    private static function parseBorderAttributes($borderAttributes)
    {
        $styleArray = array();
        if (isset($borderAttributes["Color"])) {
            $styleArray['color']['rgb'] = self::parseGnumericColour($borderAttributes["Color"]);
        }

        switch ($borderAttributes["Style"]) {
            case '0':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_NONE;
                break;
            case '1':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_THIN;
                break;
            case '2':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUM;
                break;
            case '3':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_SLANTDASHDOT;
                break;
            case '4':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DASHED;
                break;
            case '5':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_THICK;
                break;
            case '6':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DOUBLE;
                break;
            case '7':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DOTTED;
                break;
            case '8':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUMDASHED;
                break;
            case '9':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DASHDOT;
                break;
            case '10':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUMDASHDOT;
                break;
            case '11':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_DASHDOTDOT;
                break;
            case '12':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT;
                break;
            case '13':
                $styleArray['style'] = PHPExcel_Style_Border::BORDER_MEDIUMDASHDOTDOT;
                break;
        }
        return $styleArray;
    }

    private function parseRichText($is = '')
    {
        $value = new PHPExcel_RichText();
        $value->createText($is);

        return $value;
    }

    private static function parseGnumericColour($gnmColour)
    {
        list($gnmR, $gnmG, $gnmB) = explode(':', $gnmColour);
        $gnmR = substr(str_pad($gnmR, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmG = substr(str_pad($gnmG, 4, '0', STR_PAD_RIGHT), 0, 2);
        $gnmB = substr(str_pad($gnmB, 4, '0', STR_PAD_RIGHT), 0, 2);
        return $gnmR . $gnmG . $gnmB;
    }
}
