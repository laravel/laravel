<?php

/**
 * PHPExcel_Writer_OpenDocument_Settings
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
class PHPExcel_Writer_OpenDocument_Settings extends PHPExcel_Writer_OpenDocument_WriterPart
{
    /**
     * Write settings.xml to XML format
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

        // Settings
        $objWriter->startElement('office:document-settings');
            $objWriter->writeAttribute('xmlns:office', 'urn:oasis:names:tc:opendocument:xmlns:office:1.0');
            $objWriter->writeAttribute('xmlns:xlink', 'http://www.w3.org/1999/xlink');
            $objWriter->writeAttribute('xmlns:config', 'urn:oasis:names:tc:opendocument:xmlns:config:1.0');
            $objWriter->writeAttribute('xmlns:ooo', 'http://openoffice.org/2004/office');
            $objWriter->writeAttribute('office:version', '1.2');

            $objWriter->startElement('office:settings');
                $objWriter->startElement('config:config-item-set');
                    $objWriter->writeAttribute('config:name', 'ooo:view-settings');
                    $objWriter->startElement('config:config-item-map-indexed');
                        $objWriter->writeAttribute('config:name', 'Views');
                    $objWriter->endElement();
                $objWriter->endElement();
                $objWriter->startElement('config:config-item-set');
                    $objWriter->writeAttribute('config:name', 'ooo:configuration-settings');
                $objWriter->endElement();
            $objWriter->endElement();
        $objWriter->endElement();

        return $objWriter->getData();
    }
}
