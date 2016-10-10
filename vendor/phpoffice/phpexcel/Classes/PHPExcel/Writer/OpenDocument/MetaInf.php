<?php

/**
 * PHPExcel_Writer_OpenDocument_MetaInf
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
class PHPExcel_Writer_OpenDocument_MetaInf extends PHPExcel_Writer_OpenDocument_WriterPart
{
    /**
     * Write META-INF/manifest.xml to XML format
     *
     * @param     PHPExcel    $pPHPExcel
     * @return     string         XML Output
     * @throws     PHPExcel_Writer_Exception
     */
    public function writeManifest(PHPExcel $pPHPExcel = null)
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

        // Manifest
        $objWriter->startElement('manifest:manifest');
            $objWriter->writeAttribute('xmlns:manifest', 'urn:oasis:names:tc:opendocument:xmlns:manifest:1.0');
            $objWriter->writeAttribute('manifest:version', '1.2');

            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', '/');
                $objWriter->writeAttribute('manifest:version', '1.2');
                $objWriter->writeAttribute('manifest:media-type', 'application/vnd.oasis.opendocument.spreadsheet');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'meta.xml');
                $objWriter->writeAttribute('manifest:media-type', 'text/xml');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'settings.xml');
                $objWriter->writeAttribute('manifest:media-type', 'text/xml');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'content.xml');
                $objWriter->writeAttribute('manifest:media-type', 'text/xml');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'Thumbnails/thumbnail.png');
                $objWriter->writeAttribute('manifest:media-type', 'image/png');
            $objWriter->endElement();
            $objWriter->startElement('manifest:file-entry');
                $objWriter->writeAttribute('manifest:full-path', 'styles.xml');
                $objWriter->writeAttribute('manifest:media-type', 'text/xml');
            $objWriter->endElement();
        $objWriter->endElement();

        return $objWriter->getData();
    }
}
