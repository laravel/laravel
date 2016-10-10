<?php

/**
 * PHPExcel_Writer_OpenDocument_Meta
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
 * @package    PHPExcel_Writer_OpenDocument
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_Writer_OpenDocument_Meta extends PHPExcel_Writer_OpenDocument_WriterPart
{
    /**
     * Write meta.xml to XML format
     *
     * @param   PHPExcel                   $pPHPExcel
     * @return  string                     XML Output
     * @throws  PHPExcel_Writer_Exception
     */
    public function write(PHPExcel $pPHPExcel = null)
    {
        if (!$pPHPExcel) {
            $pPHPExcel = $this->getParentWriter()->getPHPExcel();
        }

        $objWriter = null;
        if ($this->getParentWriter()->getUseDiskCaching()) {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_DISK, $this->getParentWriter()->getDiskCachingDirectory());
        } else {
            $objWriter = new PHPExcel_Shared_XMLWriter(PHPExcel_Shared_XMLWriter::STORAGE_MEMORY);
        }

        // XML header
        $objWriter->startDocument('1.0', 'UTF-8');

        // Meta
        $objWriter->startElement('office:document-meta');

        $objWriter->writeAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
        $objWriter->writeAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
        $objWriter->writeAttribute('xmlns:dc', 'http://purl.org/dc/elements/1.1/');
        $objWriter->writeAttribute('xmlns:meta', 'urn:oasis:names:tc:opendocument:xmlns:meta:1.0');
        $objWriter->writeAttribute('xmlns:ooo', 'http://openoffice.org/2004/office');
        $objWriter->writeAttribute('xmlns:grddl', 'http://www.w3.org/2003/g/data-view#');
        $objWriter->writeAttribute('office:version', '1.2');

        $objWriter->startElement('office:meta');

        $objWriter->writeElement('meta:initial-creator', $pPHPExcel->getProperties()->getCreator());
        $objWriter->writeElement('dc:creator', $pPHPExcel->getProperties()->getCreator());
        $objWriter->writeElement('meta:creation-date', date(DATE_W3C, $pPHPExcel->getProperties()->getCreated()));
        $objWriter->writeElement('dc:date', date(DATE_W3C, $pPHPExcel->getProperties()->getCreated()));
        $objWriter->writeElement('dc:title', $pPHPExcel->getProperties()->getTitle());
        $objWriter->writeElement('dc:description', $pPHPExcel->getProperties()->getDescription());
        $objWriter->writeElement('dc:subject', $pPHPExcel->getProperties()->getSubject());
        $keywords = explode(' ', $pPHPExcel->getProperties()->getKeywords());
        foreach ($keywords as $keyword) {
            $objWriter->writeElement('meta:keyword', $keyword);
        }

        //<meta:document-statistic meta:table-count="XXX" meta:cell-count="XXX" meta:object-count="XXX"/>
        $objWriter->startElement('meta:user-defined');
        $objWriter->writeAttribute('meta:name', 'Company');
        $objWriter->writeRaw($pPHPExcel->getProperties()->getCompany());
        $objWriter->endElement();
 
        $objWriter->startElement('meta:user-defined');
        $objWriter->writeAttribute('meta:name', 'category');
        $objWriter->writeRaw($pPHPExcel->getProperties()->getCategory());
        $objWriter->endElement();

        $objWriter->endElement();

        $objWriter->endElement();

        return $objWriter->getData();
    }
}
