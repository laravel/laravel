<?php

/**
 * PHPExcel_Writer_Excel2007_DocProps
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
class PHPExcel_Writer_Excel2007_DocProps extends PHPExcel_Writer_Excel2007_WriterPart
{
    /**
     * Write docProps/app.xml to XML format
     *
     * @param     PHPExcel    $pPHPExcel
     * @return     string         XML Output
     * @throws     PHPExcel_Writer_Exception
     */
    public function writeDocPropsApp(PHPExcel $pPHPExcel = null)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // Properties
        $objWriter->startElement('Properties');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/officeDocument/2006/extended-properties');
        $objWriter->writeAttribute('xmlns:vt', 'http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes');

        // Application
        $objWriter->writeElement('Application', 'Microsoft Excel');

        // DocSecurity
        $objWriter->writeElement('DocSecurity', '0');

        // ScaleCrop
        $objWriter->writeElement('ScaleCrop', 'false');

        // HeadingPairs
        $objWriter->startElement('HeadingPairs');

        // Vector
        $objWriter->startElement('vt:vector');
        $objWriter->writeAttribute('size', '2');
        $objWriter->writeAttribute('baseType', 'variant');

        // Variant
        $objWriter->startElement('vt:variant');
        $objWriter->writeElement('vt:lpstr', 'Worksheets');
        $objWriter->endElement();

        // Variant
        $objWriter->startElement('vt:variant');
        $objWriter->writeElement('vt:i4', $pPHPExcel->getSheetCount());
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        // TitlesOfParts
        $objWriter->startElement('TitlesOfParts');

        // Vector
        $objWriter->startElement('vt:vector');
        $objWriter->writeAttribute('size', $pPHPExcel->getSheetCount());
        $objWriter->writeAttribute('baseType', 'lpstr');

        $sheetCount = $pPHPExcel->getSheetCount();
        for ($i = 0; $i < $sheetCount; ++$i) {
            $objWriter->writeElement('vt:lpstr', $pPHPExcel->getSheet($i)->getTitle());
        }

        $objWriter->endElement();

        $objWriter->endElement();

        // Company
        $objWriter->writeElement('Company', $pPHPExcel->getProperties()->getCompany());

        // Company
        $objWriter->writeElement('Manager', $pPHPExcel->getProperties()->getManager());

        // LinksUpToDate
        $objWriter->writeElement('LinksUpToDate', 'false');

        // SharedDoc
        $objWriter->writeElement('SharedDoc', 'false');

        // HyperlinksChanged
        $objWriter->writeElement('HyperlinksChanged', 'false');

        // AppVersion
        $objWriter->writeElement('AppVersion', '12.0000');

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write docProps/core.xml to XML format
     *
     * @param     PHPExcel    $pPHPExcel
     * @return     string         XML Output
     * @throws     PHPExcel_Writer_Exception
     */
    public function writeDocPropsCore(PHPExcel $pPHPExcel = null)
    {
        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // cp:coreProperties
        $objWriter->startElement('cp:coreProperties');
        $objWriter->writeAttribute('xmlns:cp', 'http://schemas.openxmlformats.org/package/2006/metadata/core-properties');
        $objWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $objWriter->writeAttribute('xmlns:dcterms', 'http://purl.org/dc/terms/');
        $objWriter->writeAttribute('xmlns:dcmitype', 'http://purl.org/dc/dcmitype/');
        $objWriter->writeAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');

        // dc:creator
        $objWriter->writeElement('dc:creator', $pPHPExcel->getProperties()->getCreator());

        // cp:lastModifiedBy
        $objWriter->writeElement('cp:lastModifiedBy', $pPHPExcel->getProperties()->getLastModifiedBy());

        // dcterms:created
        $objWriter->startElement('dcterms:created');
        $objWriter->writeAttribute('xsi:type', 'dcterms:W3CDTF');
        $objWriter->writeRawData(date(DATE_W3C, $pPHPExcel->getProperties()->getCreated()));
        $objWriter->endElement();

        // dcterms:modified
        $objWriter->startElement('dcterms:modified');
        $objWriter->writeAttribute('xsi:type', 'dcterms:W3CDTF');
        $objWriter->writeRawData(date(DATE_W3C, $pPHPExcel->getProperties()->getModified()));
        $objWriter->endElement();

        // dc:title
        $objWriter->writeElement('dc:title', $pPHPExcel->getProperties()->getTitle());

        // dc:description
        $objWriter->writeElement('dc:description', $pPHPExcel->getProperties()->getDescription());

        // dc:subject
        $objWriter->writeElement('dc:subject', $pPHPExcel->getProperties()->getSubject());

        // cp:keywords
        $objWriter->writeElement('cp:keywords', $pPHPExcel->getProperties()->getKeywords());

        // cp:category
        $objWriter->writeElement('cp:category', $pPHPExcel->getProperties()->getCategory());

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write docProps/custom.xml to XML format
     *
     * @param     PHPExcel    $pPHPExcel
     * @return     string         XML Output
     * @throws     PHPExcel_Writer_Exception
     */
    public function writeDocPropsCustom(PHPExcel $pPHPExcel = null)
    {
        $customPropertyList = $pPHPExcel->getProperties()->getCustomProperties();
        if (empty($customPropertyList)) {
            return;
        }

        // Create XML writer
        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8', 'yes');

        // cp:coreProperties
        $objWriter->startElement('Properties');
        $objWriter->writeAttribute('xmlns', 'http://schemas.openxmlformats.org/officeDocument/2006/custom-properties');
        $objWriter->writeAttribute('xmlns:vt', 'http://schemas.openxmlformats.org/officeDocument/2006/docPropsVTypes');


        foreach ($customPropertyList as $key => $customProperty) {
            $propertyValue = $pPHPExcel->getProperties()->getCustomPropertyValue($customProperty);
            $propertyType = $pPHPExcel->getProperties()->getCustomPropertyType($customProperty);

            $objWriter->startElement('property');
            $objWriter->writeAttribute('fmtid', '{D5CDD505-2E9C-101B-9397-08002B2CF9AE}');
            $objWriter->writeAttribute('pid', $key+2);
            $objWriter->writeAttribute('name', $customProperty);

            switch ($propertyType) {
                case 'i':
                    $objWriter->writeElement('vt:i4', $propertyValue);
                    break;
                case 'f':
                    $objWriter->writeElement('vt:r8', $propertyValue);
                    break;
                case 'b':
                    $objWriter->writeElement('vt:bool', ($propertyValue) ? 'true' : 'false');
                    break;
                case 'd':
                    $objWriter->startElement('vt:filetime');
                    $objWriter->writeRawData(date(DATE_W3C, $propertyValue));
                    $objWriter->endElement();
                    break;
                default:
                    $objWriter->writeElement('vt:lpwstr', $propertyValue);
                    break;
            }

            $objWriter->endElement();
        }


        $objWriter->endElement();

        return $objWriter->getData();
    }
}
