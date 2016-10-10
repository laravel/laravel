<?php

/**
 * PHPExcel_Writer_Excel2007_StringTable
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
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Writer_Excel2007_StringTable extends PHPExcel_Writer_Excel2007_WriterPart
{
    /**
     * Create worksheet stringtable
     *
     * @param     PHPExcel_Worksheet     $pSheet                Worksheet
     * @param     string[]                 $pExistingTable     Existing table to eventually merge with
     * @return     string[]                 String table for worksheet
     * @throws     PHPExcel_Writer_Exception
     */
    public function createStringTable($pSheet = null, $pExistingTable = null)
    {
        if ($pSheet !== null) {
            // Create string lookup table
            $aStringTable = array();
            $cellCollection = null;
            $aFlippedStringTable = null;    // For faster lookup

            // Is an existing table given?
            if (($pExistingTable !== null) && is_array($pExistingTable)) {
                $aStringTable = $pExistingTable;
            }

            // Fill index array
            $aFlippedStringTable = $this->flipStringTable($aStringTable);

            // Loop through cells
            foreach ($pSheet->getCellCollection() as $cellID) {
                $cell = $pSheet->getCell($cellID);
                $cellValue = $cell->getValue();
                if (!is_object($cellValue) &&
                    ($cellValue !== null) &&
                    $cellValue !== '' &&
                    !isset($aFlippedStringTable[$cellValue]) &&
                    ($cell->getDataType() == PHPExcel_Cell_DataType::TYPE_STRING || $cell->getDataType() == PHPExcel_Cell_DataType::TYPE_STRING2 || $cell->getDataType() == PHPExcel_Cell_DataType::TYPE_NULL)) {
                        $aStringTable[] = $cellValue;
                        $aFlippedStringTable[$cellValue] = true;
                } elseif ($cellValue instanceof PHPExcel_RichText &&
                          ($cellValue !== null) &&
                          !isset($aFlippedStringTable[$cellValue->getHashCode()])) {
                                $aStringTable[] = $cellValue;
                                $aFlippedStringTable[$cellValue->getHashCode()] = true;
                }
            }

            return $aStringTable;
        } else {
            throw new PHPExcel_Writer_Exception("Invalid PHPExcel_Worksheet object passed.");
        }
    }

    /**
     * Write string table to XML format
     *
     * @param     string[]     $pStringTable
     * @return     string         XML Output
     * @throws     PHPExcel_Writer_Exception
     */
    public function writeStringTable($pStringTable = null)
    {
        if ($pStringTable !== null) {
            // Create XML writer
            $objWriter = null;
            if ($this->getParentWriter()->getUseDiskCaching()) {
                $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
            } else {
                $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
            }

            // XML header
            $objWriter->startDocument('1.0', 'UTF-8', 'yes');

            // String table
            $objWriter->startElement('sst');
            $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $objWriter->writeAttribute('uniqueCount', count($pStringTable));

            // Loop through string table
            foreach ($pStringTable as $textElement) {
                $objWriter->startElement('si');

                if (! $textElement instanceof PHPExcel_RichText) {
                    $textToWrite = PHPExcel_Shared_String::ControlCharacterPHP2OOXML($textElement);
                    $objWriter->startElement('t');
                    if ($textToWrite !== trim($textToWrite)) {
                        $objWriter->writeAttribute('xml:space', 'preserve');
                    }
                    $objWriter->writeRawData($textToWrite);
                    $objWriter->endElement();
                } elseif ($textElement instanceof PHPExcel_RichText) {
                    $this->writeRichText($objWriter, $textElement);
                }

                $objWriter->endElement();
            }

            $objWriter->endElement();

            return $objWriter->getData();
        } else {
            throw new PHPExcel_Writer_Exception("Invalid string table array passed.");
        }
    }

    /**
     * Write Rich Text
     *
     * @param     PHPExcel_Shared_XMLWriter    $objWriter         XML Writer
     * @param     PHPExcel_RichText            $pRichText        Rich text
     * @param     string                        $prefix            Optional Namespace prefix
     * @throws     PHPExcel_Writer_Exception
     */
    public function writeRichText(PHPExcel_Shared_XMLWriter $objWriter = null, PHPExcel_RichText $pRichText = null, $prefix = null)
    {
        if ($prefix !== null) {
            $prefix .= ':';
        }
            
        // Loop through rich text elements
        $elements = $pRichText->getRichTextElements();
        foreach ($elements as $element) {
            // r
            $objWriter->startElement($prefix.'r');

            // rPr
            if ($element instanceof PHPExcel_RichText_Run) {
                // rPr
                $objWriter->startElement($prefix.'rPr');

                // rFont
                $objWriter->startElement($prefix.'rFont');
                $objWriter->writeAttribute('val', $element->getFont()->getName());
                $objWriter->endElement();

                // Bold
                $objWriter->startElement($prefix.'b');
                $objWriter->writeAttribute('val', ($element->getFont()->getBold() ? 'true' : 'false'));
                $objWriter->endElement();

                // Italic
                $objWriter->startElement($prefix.'i');
                $objWriter->writeAttribute('val', ($element->getFont()->getItalic() ? 'true' : 'false'));
                $objWriter->endElement();

                // Superscript / subscript
                if ($element->getFont()->getSuperScript() || $element->getFont()->getSubScript()) {
                    $objWriter->startElement($prefix.'vertAlign');
                    if ($element->getFont()->getSuperScript()) {
                        $objWriter->writeAttribute('val', 'superscript');
                    } elseif ($element->getFont()->getSubScript()) {
                        $objWriter->writeAttribute('val', 'subscript');
                    }
                    $objWriter->endElement();
                }

                // Strikethrough
                $objWriter->startElement($prefix.'strike');
                $objWriter->writeAttribute('val', ($element->getFont()->getStrikethrough() ? 'true' : 'false'));
                $objWriter->endElement();

                // Color
                $objWriter->startElement($prefix.'color');
                $objWriter->writeAttribute('rgb', $element->getFont()->getColor()->getARGB());
                $objWriter->endElement();

                // Size
                $objWriter->startElement($prefix.'sz');
                $objWriter->writeAttribute('val', $element->getFont()->getSize());
                $objWriter->endElement();

                // Underline
                $objWriter->startElement($prefix.'u');
                $objWriter->writeAttribute('val', $element->getFont()->getUnderline());
                $objWriter->endElement();

                $objWriter->endElement();
            }

            // t
            $objWriter->startElement($prefix.'t');
            $objWriter->writeAttribute('xml:space', 'preserve');
            $objWriter->writeRawData(PHPExcel_Shared_String::ControlCharacterPHP2OOXML($element->getText()));
            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    /**
     * Write Rich Text
     *
     * @param     PHPExcel_Shared_XMLWriter    $objWriter         XML Writer
     * @param     string|PHPExcel_RichText    $pRichText        text string or Rich text
     * @param     string                        $prefix            Optional Namespace prefix
     * @throws     PHPExcel_Writer_Exception
     */
    public function writeRichTextForCharts(PHPExcel_Shared_XMLWriter $objWriter = null, $pRichText = null, $prefix = null)
    {
        if (!$pRichText instanceof PHPExcel_RichText) {
            $textRun = $pRichText;
            $pRichText = new PHPExcel_RichText();
            $pRichText->createTextRun($textRun);
        }

        if ($prefix !== null) {
            $prefix .= ':';
        }
            
        // Loop through rich text elements
        $elements = $pRichText->getRichTextElements();
        foreach ($elements as $element) {
            // r
            $objWriter->startElement($prefix.'r');

            // rPr
            $objWriter->startElement($prefix.'rPr');

            // Bold
            $objWriter->writeAttribute('b', ($element->getFont()->getBold() ? 1 : 0));
            // Italic
            $objWriter->writeAttribute('i', ($element->getFont()->getItalic() ? 1 : 0));
            // Underline
            $underlineType = $element->getFont()->getUnderline();
            switch ($underlineType) {
                case 'single':
                    $underlineType = 'sng';
                    break;
                case 'double':
                    $underlineType = 'dbl';
                    break;
            }
            $objWriter->writeAttribute('u', $underlineType);
            // Strikethrough
            $objWriter->writeAttribute('strike', ($element->getFont()->getStrikethrough() ? 'sngStrike' : 'noStrike'));

            // rFont
            $objWriter->startElement($prefix.'latin');
                $objWriter->writeAttribute('typeface', $element->getFont()->getName());
            $objWriter->endElement();

                // Superscript / subscript
//                    if ($element->getFont()->getSuperScript() || $element->getFont()->getSubScript()) {
//                        $objWriter->startElement($prefix.'vertAlign');
//                        if ($element->getFont()->getSuperScript()) {
//                            $objWriter->writeAttribute('val', 'superscript');
//                        } elseif ($element->getFont()->getSubScript()) {
//                            $objWriter->writeAttribute('val', 'subscript');
//                        }
//                        $objWriter->endElement();
//                    }
//
            $objWriter->endElement();

            // t
            $objWriter->startElement($prefix.'t');
//                    $objWriter->writeAttribute('xml:space', 'preserve');    //    Excel2010 accepts, Excel2007 complains
            $objWriter->writeRawData(PHPExcel_Shared_String::ControlCharacterPHP2OOXML($element->getText()));
            $objWriter->endElement();

            $objWriter->endElement();
        }
    }

    /**
     * Flip string table (for index searching)
     *
     * @param     array    $stringTable    Stringtable
     * @return     array
     */
    public function flipStringTable($stringTable = array())
    {
        // Return value
        $returnValue = array();

        // Loop through stringtable and add flipped items to $returnValue
        foreach ($stringTable as $key => $value) {
            if (! $value instanceof PHPExcel_RichText) {
                $returnValue[$value] = $key;
            } elseif ($value instanceof PHPExcel_RichText) {
                $returnValue[$value->getHashCode()] = $key;
            }
        }

        return $returnValue;
    }
}
