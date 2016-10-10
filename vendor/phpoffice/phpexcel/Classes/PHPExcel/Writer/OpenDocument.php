<?php

/**
 * PHPExcel_Writer_OpenDocument
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
class PHPExcel_Writer_OpenDocument extends PHPExcel_Writer_Abstract implements PHPExcel_Writer_IWriter
{
    /**
     * Private writer parts
     *
     * @var PHPExcel_Writer_OpenDocument_WriterPart[]
     */
    private $writerParts = array();

    /**
     * Private PHPExcel
     *
     * @var PHPExcel
     */
    private $spreadSheet;

    /**
     * Create a new PHPExcel_Writer_OpenDocument
     *
     * @param PHPExcel $pPHPExcel
     */
    public function __construct(PHPExcel $pPHPExcel = null)
    {
        $this->setPHPExcel($pPHPExcel);

        $writerPartsArray = array(
            'content'    => 'PHPExcel_Writer_OpenDocument_Content',
            'meta'       => 'PHPExcel_Writer_OpenDocument_Meta',
            'meta_inf'   => 'PHPExcel_Writer_OpenDocument_MetaInf',
            'mimetype'   => 'PHPExcel_Writer_OpenDocument_Mimetype',
            'settings'   => 'PHPExcel_Writer_OpenDocument_Settings',
            'styles'     => 'PHPExcel_Writer_OpenDocument_Styles',
            'thumbnails' => 'PHPExcel_Writer_OpenDocument_Thumbnails'
        );

        foreach ($writerPartsArray as $writer => $class) {
            $this->writerParts[$writer] = new $class($this);
        }
    }

    /**
     * Get writer part
     *
     * @param  string  $pPartName  Writer part name
     * @return PHPExcel_Writer_Excel2007_WriterPart
     */
    public function getWriterPart($pPartName = '')
    {
        if ($pPartName != '' && isset($this->writerParts[strtolower($pPartName)])) {
            return $this->writerParts[strtolower($pPartName)];
        } else {
            return null;
        }
    }

    /**
     * Save PHPExcel to file
     *
     * @param  string  $pFilename
     * @throws PHPExcel_Writer_Exception
     */
    public function save($pFilename = null)
    {
        if (!$this->spreadSheet) {
            throw new PHPExcel_Writer_Exception('PHPExcel object unassigned.');
        }

        // garbage collect
        $this->spreadSheet->garbageCollect();

        // If $pFilename is php://output or php://stdout, make it a temporary file...
        $originalFilename = $pFilename;
        if (strtolower($pFilename) == 'php://output' || strtolower($pFilename) == 'php://stdout') {
            $pFilename = @tempnam(PHPExcel_Shared_File::sys_get_temp_dir(), 'phpxltmp');
            if ($pFilename == '') {
                $pFilename = $originalFilename;
            }
        }

        $objZip = $this->createZip($pFilename);

        $objZip->addFromString('META-INF/manifest.xml', $this->getWriterPart('meta_inf')->writeManifest());
        $objZip->addFromString('Thumbnails/thumbnail.png', $this->getWriterPart('thumbnails')->writeThumbnail());
        $objZip->addFromString('content.xml', $this->getWriterPart('content')->write());
        $objZip->addFromString('meta.xml', $this->getWriterPart('meta')->write());
        $objZip->addFromString('mimetype', $this->getWriterPart('mimetype')->write());
        $objZip->addFromString('settings.xml', $this->getWriterPart('settings')->write());
        $objZip->addFromString('styles.xml', $this->getWriterPart('styles')->write());

        // Close file
        if ($objZip->close() === false) {
            throw new PHPExcel_Writer_Exception("Could not close zip file $pFilename.");
        }

        // If a temporary file was used, copy it to the correct file stream
        if ($originalFilename != $pFilename) {
            if (copy($pFilename, $originalFilename) === false) {
                throw new PHPExcel_Writer_Exception("Could not copy temporary zip file $pFilename to $originalFilename.");
            }
            @unlink($pFilename);
        }
    }

    /**
     * Create zip object
     *
     * @param string $pFilename
     * @throws PHPExcel_Writer_Exception
     * @return ZipArchive
     */
    private function createZip($pFilename)
    {
        // Create new ZIP file and open it for writing
        $zipClass = PHPExcel_Settings::getZipClass();
        $objZip = new $zipClass();

        // Retrieve OVERWRITE and CREATE constants from the instantiated zip class
        // This method of accessing constant values from a dynamic class should work with all appropriate versions of PHP
        $ro = new ReflectionObject($objZip);
        $zipOverWrite = $ro->getConstant('OVERWRITE');
        $zipCreate = $ro->getConstant('CREATE');

        if (file_exists($pFilename)) {
            unlink($pFilename);
        }
        // Try opening the ZIP file
        if ($objZip->open($pFilename, $zipOverWrite) !== true) {
            if ($objZip->open($pFilename, $zipCreate) !== true) {
                throw new PHPExcel_Writer_Exception("Could not open $pFilename for writing.");
            }
        }

        return $objZip;
    }

    /**
     * Get PHPExcel object
     *
     * @return PHPExcel
     * @throws PHPExcel_Writer_Exception
     */
    public function getPHPExcel()
    {
        if ($this->spreadSheet !== null) {
            return $this->spreadSheet;
        } else {
            throw new PHPExcel_Writer_Exception('No PHPExcel assigned.');
        }
    }

    /**
     * Set PHPExcel object
     *
     * @param  PHPExcel  $pPHPExcel  PHPExcel object
     * @throws PHPExcel_Writer_Exception
     * @return PHPExcel_Writer_Excel2007
     */
    public function setPHPExcel(PHPExcel $pPHPExcel = null)
    {
        $this->spreadSheet = $pPHPExcel;
        return $this;
    }
}
