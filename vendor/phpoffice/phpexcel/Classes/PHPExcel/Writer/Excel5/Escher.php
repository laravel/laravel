<?php
/**
 * PHPExcel
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


/**
 * PHPExcel_Shared_Escher_DggContainer_BstoreContainer
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel5
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel5_Escher
{
    /**
     * The object we are writing
     */
    private $object;

    /**
     * The written binary data
     */
    private $data;

    /**
     * Shape offsets. Positions in binary stream where a new shape record begins
     *
     * @var array
     */
    private $spOffsets;

    /**
     * Shape types.
     *
     * @var array
     */
    private $spTypes;
    
    /**
     * Constructor
     *
     * @param mixed
     */
    public function __construct($object)
    {
        $this->object = $object;
    }

    /**
     * Process the object to be written
     */
    public function close()
    {
        // initialize
        $this->data = '';

        switch (get_class($this->object)) {
            case 'PHPExcel_Shared_Escher':
                if ($dggContainer = $this->object->getDggContainer()) {
                    $writer = new PHPExcel_Writer_Excel5_Escher($dggContainer);
                    $this->data = $writer->close();
                } elseif ($dgContainer = $this->object->getDgContainer()) {
                    $writer = new PHPExcel_Writer_Excel5_Escher($dgContainer);
                    $this->data = $writer->close();
                    $this->spOffsets = $writer->getSpOffsets();
                    $this->spTypes = $writer->getSpTypes();
                }
                break;
            case 'PHPExcel_Shared_Escher_DggContainer':
                // this is a container record

                // initialize
                $innerData = '';

                // write the dgg
                $recVer            = 0x0;
                $recInstance    = 0x0000;
                $recType        = 0xF006;

                $recVerInstance  = $recVer;
                $recVerInstance |= $recInstance << 4;

                // dgg data
                $dggData =
                    pack(
                        'VVVV',
                        $this->object->getSpIdMax(), // maximum shape identifier increased by one
                        $this->object->getCDgSaved() + 1, // number of file identifier clusters increased by one
                        $this->object->getCSpSaved(),
                        $this->object->getCDgSaved() // count total number of drawings saved
                    );

                // add file identifier clusters (one per drawing)
                $IDCLs = $this->object->getIDCLs();

                foreach ($IDCLs as $dgId => $maxReducedSpId) {
                    $dggData .= pack('VV', $dgId, $maxReducedSpId + 1);
                }

                $header = pack('vvV', $recVerInstance, $recType, strlen($dggData));
                $innerData .= $header . $dggData;

                // write the bstoreContainer
                if ($bstoreContainer = $this->object->getBstoreContainer()) {
                    $writer = new PHPExcel_Writer_Excel5_Escher($bstoreContainer);
                    $innerData .= $writer->close();
                }

                // write the record
                $recVer            = 0xF;
                $recInstance    = 0x0000;
                $recType        = 0xF000;
                $length            = strlen($innerData);

                $recVerInstance  = $recVer;
                $recVerInstance |= $recInstance << 4;

                $header = pack('vvV', $recVerInstance, $recType, $length);

                $this->data = $header . $innerData;
                break;
            case 'PHPExcel_Shared_Escher_DggContainer_BstoreContainer':
                // this is a container record

                // initialize
                $innerData = '';

                // treat the inner data
                if ($BSECollection = $this->object->getBSECollection()) {
                    foreach ($BSECollection as $BSE) {
                        $writer = new PHPExcel_Writer_Excel5_Escher($BSE);
                        $innerData .= $writer->close();
                    }
                }

                // write the record
                $recVer            = 0xF;
                $recInstance    = count($this->object->getBSECollection());
                $recType        = 0xF001;
                $length            = strlen($innerData);

                $recVerInstance  = $recVer;
                $recVerInstance |= $recInstance << 4;

                $header = pack('vvV', $recVerInstance, $recType, $length);

                $this->data = $header . $innerData;
                break;
            case 'PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE':
                // this is a semi-container record

                // initialize
                $innerData = '';

                // here we treat the inner data
                if ($blip = $this->object->getBlip()) {
                    $writer = new PHPExcel_Writer_Excel5_Escher($blip);
                    $innerData .= $writer->close();
                }

                // initialize
                $data = '';

                $btWin32 = $this->object->getBlipType();
                $btMacOS = $this->object->getBlipType();
                $data .= pack('CC', $btWin32, $btMacOS);

                $rgbUid = pack('VVVV', 0, 0, 0, 0); // todo
                $data .= $rgbUid;

                $tag = 0;
                $size = strlen($innerData);
                $cRef = 1;
                $foDelay = 0; //todo
                $unused1 = 0x0;
                $cbName = 0x0;
                $unused2 = 0x0;
                $unused3 = 0x0;
                $data .= pack('vVVVCCCC', $tag, $size, $cRef, $foDelay, $unused1, $cbName, $unused2, $unused3);

                $data .= $innerData;

                // write the record
                $recVer            = 0x2;
                $recInstance    = $this->object->getBlipType();
                $recType        = 0xF007;
                $length            = strlen($data);

                $recVerInstance  = $recVer;
                $recVerInstance |=    $recInstance << 4;

                $header = pack('vvV', $recVerInstance, $recType, $length);

                $this->data = $header;

                $this->data .= $data;
                break;
            case 'PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE_Blip':
                // this is an atom record

                // write the record
                switch ($this->object->getParent()->getBlipType()) {
                    case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_JPEG:
                        // initialize
                        $innerData = '';

                        $rgbUid1 = pack('VVVV', 0, 0, 0, 0); // todo
                        $innerData .= $rgbUid1;

                        $tag = 0xFF; // todo
                        $innerData .= pack('C', $tag);

                        $innerData .= $this->object->getData();

                        $recVer            = 0x0;
                        $recInstance    = 0x46A;
                        $recType        = 0xF01D;
                        $length            = strlen($innerData);

                        $recVerInstance  = $recVer;
                        $recVerInstance |=    $recInstance << 4;

                        $header = pack('vvV', $recVerInstance, $recType, $length);

                        $this->data = $header;

                        $this->data .= $innerData;
                        break;

                    case PHPExcel_Shared_Escher_DggContainer_BstoreContainer_BSE::BLIPTYPE_PNG:
                        // initialize
                        $innerData = '';

                        $rgbUid1 = pack('VVVV', 0, 0, 0, 0); // todo
                        $innerData .= $rgbUid1;

                        $tag = 0xFF; // todo
                        $innerData .= pack('C', $tag);

                        $innerData .= $this->object->getData();

                        $recVer            = 0x0;
                        $recInstance    = 0x6E0;
                        $recType        = 0xF01E;
                        $length            = strlen($innerData);

                        $recVerInstance  = $recVer;
                        $recVerInstance |=    $recInstance << 4;

                        $header = pack('vvV', $recVerInstance, $recType, $length);

                        $this->data = $header;

                        $this->data .= $innerData;
                        break;
                }
                break;
            case 'PHPExcel_Shared_Escher_DgContainer':
                // this is a container record

                // initialize
                $innerData = '';

                // write the dg
                $recVer            = 0x0;
                $recInstance    = $this->object->getDgId();
                $recType        = 0xF008;
                $length            = 8;

                $recVerInstance  = $recVer;
                $recVerInstance |= $recInstance << 4;

                $header = pack('vvV', $recVerInstance, $recType, $length);

                // number of shapes in this drawing (including group shape)
                $countShapes = count($this->object->getSpgrContainer()->getChildren());
                $innerData .= $header . pack('VV', $countShapes, $this->object->getLastSpId());
                //$innerData .= $header . pack('VV', 0, 0);

                // write the spgrContainer
                if ($spgrContainer = $this->object->getSpgrContainer()) {
                    $writer = new PHPExcel_Writer_Excel5_Escher($spgrContainer);
                    $innerData .= $writer->close();

                    // get the shape offsets relative to the spgrContainer record
                    $spOffsets = $writer->getSpOffsets();
                    $spTypes   = $writer->getSpTypes();
                    
                    // save the shape offsets relative to dgContainer
                    foreach ($spOffsets as & $spOffset) {
                        $spOffset += 24; // add length of dgContainer header data (8 bytes) plus dg data (16 bytes)
                    }

                    $this->spOffsets = $spOffsets;
                    $this->spTypes = $spTypes;
                }

                // write the record
                $recVer            = 0xF;
                $recInstance    = 0x0000;
                $recType        = 0xF002;
                $length            = strlen($innerData);

                $recVerInstance  = $recVer;
                $recVerInstance |= $recInstance << 4;

                $header = pack('vvV', $recVerInstance, $recType, $length);

                $this->data = $header . $innerData;
                break;
            case 'PHPExcel_Shared_Escher_DgContainer_SpgrContainer':
                // this is a container record

                // initialize
                $innerData = '';

                // initialize spape offsets
                $totalSize = 8;
                $spOffsets = array();
                $spTypes   = array();

                // treat the inner data
                foreach ($this->object->getChildren() as $spContainer) {
                    $writer = new PHPExcel_Writer_Excel5_Escher($spContainer);
                    $spData = $writer->close();
                    $innerData .= $spData;

                    // save the shape offsets (where new shape records begin)
                    $totalSize += strlen($spData);
                    $spOffsets[] = $totalSize;
                    
                    $spTypes = array_merge($spTypes, $writer->getSpTypes());
                }

                // write the record
                $recVer            = 0xF;
                $recInstance    = 0x0000;
                $recType        = 0xF003;
                $length            = strlen($innerData);

                $recVerInstance  = $recVer;
                $recVerInstance |= $recInstance << 4;

                $header = pack('vvV', $recVerInstance, $recType, $length);

                $this->data = $header . $innerData;
                $this->spOffsets = $spOffsets;
                $this->spTypes = $spTypes;
                break;
            case 'PHPExcel_Shared_Escher_DgContainer_SpgrContainer_SpContainer':
                // initialize
                $data = '';

                // build the data

                // write group shape record, if necessary?
                if ($this->object->getSpgr()) {
                    $recVer            = 0x1;
                    $recInstance    = 0x0000;
                    $recType        = 0xF009;
                    $length            = 0x00000010;

                    $recVerInstance  = $recVer;
                    $recVerInstance |= $recInstance << 4;

                    $header = pack('vvV', $recVerInstance, $recType, $length);

                    $data .= $header . pack('VVVV', 0, 0, 0, 0);
                }
                $this->spTypes[] = ($this->object->getSpType());

                // write the shape record
                $recVer            = 0x2;
                $recInstance    = $this->object->getSpType(); // shape type
                $recType        = 0xF00A;
                $length            = 0x00000008;

                $recVerInstance  = $recVer;
                $recVerInstance |= $recInstance << 4;

                $header = pack('vvV', $recVerInstance, $recType, $length);

                $data .= $header . pack('VV', $this->object->getSpId(), $this->object->getSpgr() ? 0x0005 : 0x0A00);


                // the options
                if ($this->object->getOPTCollection()) {
                    $optData = '';

                    $recVer            = 0x3;
                    $recInstance    = count($this->object->getOPTCollection());
                    $recType        = 0xF00B;
                    foreach ($this->object->getOPTCollection() as $property => $value) {
                        $optData .= pack('vV', $property, $value);
                    }
                    $length            = strlen($optData);

                    $recVerInstance  = $recVer;
                    $recVerInstance |= $recInstance << 4;

                    $header = pack('vvV', $recVerInstance, $recType, $length);
                    $data .= $header . $optData;
                }

                // the client anchor
                if ($this->object->getStartCoordinates()) {
                    $clientAnchorData = '';

                    $recVer            = 0x0;
                    $recInstance    = 0x0;
                    $recType        = 0xF010;

                    // start coordinates
                    list($column, $row) = PHPExcel_Cell::coordinateFromString($this->object->getStartCoordinates());
                    $c1 = PHPExcel_Cell::columnIndexFromString($column) - 1;
                    $r1 = $row - 1;

                    // start offsetX
                    $startOffsetX = $this->object->getStartOffsetX();

                    // start offsetY
                    $startOffsetY = $this->object->getStartOffsetY();

                    // end coordinates
                    list($column, $row) = PHPExcel_Cell::coordinateFromString($this->object->getEndCoordinates());
                    $c2 = PHPExcel_Cell::columnIndexFromString($column) - 1;
                    $r2 = $row - 1;

                    // end offsetX
                    $endOffsetX = $this->object->getEndOffsetX();

                    // end offsetY
                    $endOffsetY = $this->object->getEndOffsetY();

                    $clientAnchorData = pack('vvvvvvvvv', $this->object->getSpFlag(), $c1, $startOffsetX, $r1, $startOffsetY, $c2, $endOffsetX, $r2, $endOffsetY);
                    
                    $length            = strlen($clientAnchorData);

                    $recVerInstance  = $recVer;
                    $recVerInstance |= $recInstance << 4;

                    $header = pack('vvV', $recVerInstance, $recType, $length);
                    $data .= $header . $clientAnchorData;
                }

                // the client data, just empty for now
                if (!$this->object->getSpgr()) {
                    $clientDataData = '';

                    $recVer            = 0x0;
                    $recInstance    = 0x0;
                    $recType        = 0xF011;

                    $length = strlen($clientDataData);

                    $recVerInstance  = $recVer;
                    $recVerInstance |= $recInstance << 4;

                    $header = pack('vvV', $recVerInstance, $recType, $length);
                    $data .= $header . $clientDataData;
                }

                // write the record
                $recVer            = 0xF;
                $recInstance    = 0x0000;
                $recType        = 0xF004;
                $length            = strlen($data);

                $recVerInstance  = $recVer;
                $recVerInstance |= $recInstance << 4;

                $header = pack('vvV', $recVerInstance, $recType, $length);

                $this->data = $header . $data;
                break;
        }

        return $this->data;
    }

    /**
     * Gets the shape offsets
     *
     * @return array
     */
    public function getSpOffsets()
    {
        return $this->spOffsets;
    }

    /**
     * Gets the shape types
     *
     * @return array
     */
    public function getSpTypes()
    {
        return $this->spTypes;
    }
}
