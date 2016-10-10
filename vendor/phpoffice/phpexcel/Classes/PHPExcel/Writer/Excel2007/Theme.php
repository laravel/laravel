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
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */


/**
 * PHPExcel_Writer_Excel2007_Theme
 *
 * @category   PHPExcel
 * @package    PHPExcel_Writer_Excel2007
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Writer_Excel2007_Theme extends PHPExcel_Writer_Excel2007_WriterPart
{
    /**
     * Map of Major fonts to write
     * @static    array of string
     *
     */
    private static $majorFonts = array(
        'Jpan' => 'ＭＳ Ｐゴシック',
        'Hang' => '맑은 고딕',
        'Hans' => '宋体',
        'Hant' => '新細明體',
        'Arab' => 'Times New Roman',
        'Hebr' => 'Times New Roman',
        'Thai' => 'Tahoma',
        'Ethi' => 'Nyala',
        'Beng' => 'Vrinda',
        'Gujr' => 'Shruti',
        'Khmr' => 'MoolBoran',
        'Knda' => 'Tunga',
        'Guru' => 'Raavi',
        'Cans' => 'Euphemia',
        'Cher' => 'Plantagenet Cherokee',
        'Yiii' => 'Microsoft Yi Baiti',
        'Tibt' => 'Microsoft Himalaya',
        'Thaa' => 'MV Boli',
        'Deva' => 'Mangal',
        'Telu' => 'Gautami',
        'Taml' => 'Latha',
        'Syrc' => 'Estrangelo Edessa',
        'Orya' => 'Kalinga',
        'Mlym' => 'Kartika',
        'Laoo' => 'DokChampa',
        'Sinh' => 'Iskoola Pota',
        'Mong' => 'Mongolian Baiti',
        'Viet' => 'Times New Roman',
        'Uigh' => 'Microsoft Uighur',
        'Geor' => 'Sylfaen',
    );

    /**
     * Map of Minor fonts to write
     * @static    array of string
     *
     */
    private static $minorFonts = array(
        'Jpan' => 'ＭＳ Ｐゴシック',
        'Hang' => '맑은 고딕',
        'Hans' => '宋体',
        'Hant' => '新細明體',
        'Arab' => 'Arial',
        'Hebr' => 'Arial',
        'Thai' => 'Tahoma',
        'Ethi' => 'Nyala',
        'Beng' => 'Vrinda',
        'Gujr' => 'Shruti',
        'Khmr' => 'DaunPenh',
        'Knda' => 'Tunga',
        'Guru' => 'Raavi',
        'Cans' => 'Euphemia',
        'Cher' => 'Plantagenet Cherokee',
        'Yiii' => 'Microsoft Yi Baiti',
        'Tibt' => 'Microsoft Himalaya',
        'Thaa' => 'MV Boli',
        'Deva' => 'Mangal',
        'Telu' => 'Gautami',
        'Taml' => 'Latha',
        'Syrc' => 'Estrangelo Edessa',
        'Orya' => 'Kalinga',
        'Mlym' => 'Kartika',
        'Laoo' => 'DokChampa',
        'Sinh' => 'Iskoola Pota',
        'Mong' => 'Mongolian Baiti',
        'Viet' => 'Arial',
        'Uigh' => 'Microsoft Uighur',
        'Geor' => 'Sylfaen',
    );

    /**
     * Map of core colours
     * @static    array of string
     *
     */
    private static $colourScheme = array(
        'dk2'        => '1F497D',
        'lt2'        => 'EEECE1',
        'accent1'    => '4F81BD',
        'accent2'    => 'C0504D',
        'accent3'    => '9BBB59',
        'accent4'    => '8064A2',
        'accent5'    => '4BACC6',
        'accent6'    => 'F79646',
        'hlink'        => '0000FF',
        'folHlink'    => '800080',
    );
            
    /**
     * Write theme to XML format
     *
     * @param     PHPExcel    $pPHPExcel
     * @return     string         XML Output
     * @throws     PHPExcel_Writer_Exception
     */
    public function writeTheme(PHPExcel $pPHPExcel = null)
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

        // a:theme
        $objWriter->startElement('a:theme');
        $objWriter->writeAttribute('xmlns:a', 'http://schemas.openxmlformats.org/drawingml/2006/main');
        $objWriter->writeAttribute('name', 'Office Theme');

            // a:themeElements
            $objWriter->startElement('a:themeElements');

                // a:clrScheme
                $objWriter->startElement('a:clrScheme');
                $objWriter->writeAttribute('name', 'Office');

                    // a:dk1
                    $objWriter->startElement('a:dk1');

                        // a:sysClr
                        $objWriter->startElement('a:sysClr');
                        $objWriter->writeAttribute('val', 'windowText');
                        $objWriter->writeAttribute('lastClr', '000000');
                        $objWriter->endElement();

                    $objWriter->endElement();

                    // a:lt1
                    $objWriter->startElement('a:lt1');

                        // a:sysClr
                        $objWriter->startElement('a:sysClr');
                        $objWriter->writeAttribute('val', 'window');
                        $objWriter->writeAttribute('lastClr', 'FFFFFF');
                        $objWriter->endElement();

                    $objWriter->endElement();

                    // a:dk2
                    $this->writeColourScheme($objWriter);

                $objWriter->endElement();

                // a:fontScheme
                $objWriter->startElement('a:fontScheme');
                $objWriter->writeAttribute('name', 'Office');

                    // a:majorFont
                    $objWriter->startElement('a:majorFont');
                        $this->writeFonts($objWriter, 'Cambria', self::$majorFonts);
                    $objWriter->endElement();

                    // a:minorFont
                    $objWriter->startElement('a:minorFont');
                        $this->writeFonts($objWriter, 'Calibri', self::$minorFonts);
                    $objWriter->endElement();

                $objWriter->endElement();

                // a:fmtScheme
                $objWriter->startElement('a:fmtScheme');
                $objWriter->writeAttribute('name', 'Office');

                    // a:fillStyleLst
                    $objWriter->startElement('a:fillStyleLst');

                        // a:solidFill
                        $objWriter->startElement('a:solidFill');

                            // a:schemeClr
                            $objWriter->startElement('a:schemeClr');
                            $objWriter->writeAttribute('val', 'phClr');
                            $objWriter->endElement();

                        $objWriter->endElement();

                        // a:gradFill
                        $objWriter->startElement('a:gradFill');
                        $objWriter->writeAttribute('rotWithShape', '1');

                            // a:gsLst
                            $objWriter->startElement('a:gsLst');

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '0');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:tint
                                        $objWriter->startElement('a:tint');
                                        $objWriter->writeAttribute('val', '50000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '300000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '35000');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:tint
                                        $objWriter->startElement('a:tint');
                                        $objWriter->writeAttribute('val', '37000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '300000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '100000');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:tint
                                        $objWriter->startElement('a:tint');
                                        $objWriter->writeAttribute('val', '15000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '350000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                            $objWriter->endElement();

                            // a:lin
                            $objWriter->startElement('a:lin');
                            $objWriter->writeAttribute('ang', '16200000');
                            $objWriter->writeAttribute('scaled', '1');
                            $objWriter->endElement();

                        $objWriter->endElement();

                        // a:gradFill
                        $objWriter->startElement('a:gradFill');
                        $objWriter->writeAttribute('rotWithShape', '1');

                            // a:gsLst
                            $objWriter->startElement('a:gsLst');

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '0');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:shade
                                        $objWriter->startElement('a:shade');
                                        $objWriter->writeAttribute('val', '51000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '130000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '80000');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:shade
                                        $objWriter->startElement('a:shade');
                                        $objWriter->writeAttribute('val', '93000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '130000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '100000');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:shade
                                        $objWriter->startElement('a:shade');
                                        $objWriter->writeAttribute('val', '94000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '135000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                            $objWriter->endElement();

                            // a:lin
                            $objWriter->startElement('a:lin');
                            $objWriter->writeAttribute('ang', '16200000');
                            $objWriter->writeAttribute('scaled', '0');
                            $objWriter->endElement();

                        $objWriter->endElement();

                    $objWriter->endElement();

                    // a:lnStyleLst
                    $objWriter->startElement('a:lnStyleLst');

                        // a:ln
                        $objWriter->startElement('a:ln');
                        $objWriter->writeAttribute('w', '9525');
                        $objWriter->writeAttribute('cap', 'flat');
                        $objWriter->writeAttribute('cmpd', 'sng');
                        $objWriter->writeAttribute('algn', 'ctr');

                            // a:solidFill
                            $objWriter->startElement('a:solidFill');

                                // a:schemeClr
                                $objWriter->startElement('a:schemeClr');
                                $objWriter->writeAttribute('val', 'phClr');

                                        // a:shade
                                        $objWriter->startElement('a:shade');
                                        $objWriter->writeAttribute('val', '95000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '105000');
                                        $objWriter->endElement();

                                $objWriter->endElement();

                            $objWriter->endElement();

                            // a:prstDash
                            $objWriter->startElement('a:prstDash');
                            $objWriter->writeAttribute('val', 'solid');
                            $objWriter->endElement();

                        $objWriter->endElement();

                        // a:ln
                        $objWriter->startElement('a:ln');
                        $objWriter->writeAttribute('w', '25400');
                        $objWriter->writeAttribute('cap', 'flat');
                        $objWriter->writeAttribute('cmpd', 'sng');
                        $objWriter->writeAttribute('algn', 'ctr');

                            // a:solidFill
                            $objWriter->startElement('a:solidFill');

                                // a:schemeClr
                                $objWriter->startElement('a:schemeClr');
                                $objWriter->writeAttribute('val', 'phClr');
                                $objWriter->endElement();

                            $objWriter->endElement();

                            // a:prstDash
                            $objWriter->startElement('a:prstDash');
                            $objWriter->writeAttribute('val', 'solid');
                            $objWriter->endElement();

                        $objWriter->endElement();

                        // a:ln
                        $objWriter->startElement('a:ln');
                        $objWriter->writeAttribute('w', '38100');
                        $objWriter->writeAttribute('cap', 'flat');
                        $objWriter->writeAttribute('cmpd', 'sng');
                        $objWriter->writeAttribute('algn', 'ctr');

                            // a:solidFill
                            $objWriter->startElement('a:solidFill');

                                // a:schemeClr
                                $objWriter->startElement('a:schemeClr');
                                $objWriter->writeAttribute('val', 'phClr');
                                $objWriter->endElement();

                            $objWriter->endElement();

                            // a:prstDash
                            $objWriter->startElement('a:prstDash');
                            $objWriter->writeAttribute('val', 'solid');
                            $objWriter->endElement();

                        $objWriter->endElement();

                    $objWriter->endElement();



                    // a:effectStyleLst
                    $objWriter->startElement('a:effectStyleLst');

                        // a:effectStyle
                        $objWriter->startElement('a:effectStyle');

                            // a:effectLst
                            $objWriter->startElement('a:effectLst');

                                // a:outerShdw
                                $objWriter->startElement('a:outerShdw');
                                $objWriter->writeAttribute('blurRad', '40000');
                                $objWriter->writeAttribute('dist', '20000');
                                $objWriter->writeAttribute('dir', '5400000');
                                $objWriter->writeAttribute('rotWithShape', '0');

                                    // a:srgbClr
                                    $objWriter->startElement('a:srgbClr');
                                    $objWriter->writeAttribute('val', '000000');

                                        // a:alpha
                                        $objWriter->startElement('a:alpha');
                                        $objWriter->writeAttribute('val', '38000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                            $objWriter->endElement();

                        $objWriter->endElement();

                        // a:effectStyle
                        $objWriter->startElement('a:effectStyle');

                            // a:effectLst
                            $objWriter->startElement('a:effectLst');

                                // a:outerShdw
                                $objWriter->startElement('a:outerShdw');
                                $objWriter->writeAttribute('blurRad', '40000');
                                $objWriter->writeAttribute('dist', '23000');
                                $objWriter->writeAttribute('dir', '5400000');
                                $objWriter->writeAttribute('rotWithShape', '0');

                                    // a:srgbClr
                                    $objWriter->startElement('a:srgbClr');
                                    $objWriter->writeAttribute('val', '000000');

                                        // a:alpha
                                        $objWriter->startElement('a:alpha');
                                        $objWriter->writeAttribute('val', '35000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                            $objWriter->endElement();

                        $objWriter->endElement();

                        // a:effectStyle
                        $objWriter->startElement('a:effectStyle');

                            // a:effectLst
                            $objWriter->startElement('a:effectLst');

                                // a:outerShdw
                                $objWriter->startElement('a:outerShdw');
                                $objWriter->writeAttribute('blurRad', '40000');
                                $objWriter->writeAttribute('dist', '23000');
                                $objWriter->writeAttribute('dir', '5400000');
                                $objWriter->writeAttribute('rotWithShape', '0');

                                    // a:srgbClr
                                    $objWriter->startElement('a:srgbClr');
                                    $objWriter->writeAttribute('val', '000000');

                                        // a:alpha
                                        $objWriter->startElement('a:alpha');
                                        $objWriter->writeAttribute('val', '35000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                            $objWriter->endElement();

                            // a:scene3d
                            $objWriter->startElement('a:scene3d');

                                // a:camera
                                $objWriter->startElement('a:camera');
                                $objWriter->writeAttribute('prst', 'orthographicFront');

                                    // a:rot
                                    $objWriter->startElement('a:rot');
                                    $objWriter->writeAttribute('lat', '0');
                                    $objWriter->writeAttribute('lon', '0');
                                    $objWriter->writeAttribute('rev', '0');
                                    $objWriter->endElement();

                                $objWriter->endElement();

                                // a:lightRig
                                $objWriter->startElement('a:lightRig');
                                $objWriter->writeAttribute('rig', 'threePt');
                                $objWriter->writeAttribute('dir', 't');

                                    // a:rot
                                    $objWriter->startElement('a:rot');
                                    $objWriter->writeAttribute('lat', '0');
                                    $objWriter->writeAttribute('lon', '0');
                                    $objWriter->writeAttribute('rev', '1200000');
                                    $objWriter->endElement();

                                $objWriter->endElement();

                            $objWriter->endElement();

                            // a:sp3d
                            $objWriter->startElement('a:sp3d');

                                // a:bevelT
                                $objWriter->startElement('a:bevelT');
                                $objWriter->writeAttribute('w', '63500');
                                $objWriter->writeAttribute('h', '25400');
                                $objWriter->endElement();

                            $objWriter->endElement();

                        $objWriter->endElement();

                    $objWriter->endElement();

                    // a:bgFillStyleLst
                    $objWriter->startElement('a:bgFillStyleLst');

                        // a:solidFill
                        $objWriter->startElement('a:solidFill');

                            // a:schemeClr
                            $objWriter->startElement('a:schemeClr');
                            $objWriter->writeAttribute('val', 'phClr');
                            $objWriter->endElement();

                        $objWriter->endElement();

                        // a:gradFill
                        $objWriter->startElement('a:gradFill');
                        $objWriter->writeAttribute('rotWithShape', '1');

                            // a:gsLst
                            $objWriter->startElement('a:gsLst');

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '0');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:tint
                                        $objWriter->startElement('a:tint');
                                        $objWriter->writeAttribute('val', '40000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '350000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '40000');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:tint
                                        $objWriter->startElement('a:tint');
                                        $objWriter->writeAttribute('val', '45000');
                                        $objWriter->endElement();

                                        // a:shade
                                        $objWriter->startElement('a:shade');
                                        $objWriter->writeAttribute('val', '99000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '350000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '100000');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:shade
                                        $objWriter->startElement('a:shade');
                                        $objWriter->writeAttribute('val', '20000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '255000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                            $objWriter->endElement();

                            // a:path
                            $objWriter->startElement('a:path');
                            $objWriter->writeAttribute('path', 'circle');

                                // a:fillToRect
                                $objWriter->startElement('a:fillToRect');
                                $objWriter->writeAttribute('l', '50000');
                                $objWriter->writeAttribute('t', '-80000');
                                $objWriter->writeAttribute('r', '50000');
                                $objWriter->writeAttribute('b', '180000');
                                $objWriter->endElement();

                            $objWriter->endElement();

                        $objWriter->endElement();

                        // a:gradFill
                        $objWriter->startElement('a:gradFill');
                        $objWriter->writeAttribute('rotWithShape', '1');

                            // a:gsLst
                            $objWriter->startElement('a:gsLst');

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '0');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:tint
                                        $objWriter->startElement('a:tint');
                                        $objWriter->writeAttribute('val', '80000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '300000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                                // a:gs
                                $objWriter->startElement('a:gs');
                                $objWriter->writeAttribute('pos', '100000');

                                    // a:schemeClr
                                    $objWriter->startElement('a:schemeClr');
                                    $objWriter->writeAttribute('val', 'phClr');

                                        // a:shade
                                        $objWriter->startElement('a:shade');
                                        $objWriter->writeAttribute('val', '30000');
                                        $objWriter->endElement();

                                        // a:satMod
                                        $objWriter->startElement('a:satMod');
                                        $objWriter->writeAttribute('val', '200000');
                                        $objWriter->endElement();

                                    $objWriter->endElement();

                                $objWriter->endElement();

                            $objWriter->endElement();

                            // a:path
                            $objWriter->startElement('a:path');
                            $objWriter->writeAttribute('path', 'circle');

                                // a:fillToRect
                                $objWriter->startElement('a:fillToRect');
                                $objWriter->writeAttribute('l', '50000');
                                $objWriter->writeAttribute('t', '50000');
                                $objWriter->writeAttribute('r', '50000');
                                $objWriter->writeAttribute('b', '50000');
                                $objWriter->endElement();

                            $objWriter->endElement();

                        $objWriter->endElement();

                    $objWriter->endElement();

                $objWriter->endElement();

            $objWriter->endElement();

            // a:objectDefaults
            $objWriter->writeElement('a:objectDefaults', null);

            // a:extraClrSchemeLst
            $objWriter->writeElement('a:extraClrSchemeLst', null);

        $objWriter->endElement();

        // Return
        return $objWriter->getData();
    }

    /**
     * Write fonts to XML format
     *
     * @param     PHPExcel_Shared_XMLWriter    $objWriter
     * @param     string                        $latinFont
     * @param     array of string                $fontSet
     * @return     string                         XML Output
     * @throws     PHPExcel_Writer_Exception
     */
    private function writeFonts($objWriter, $latinFont, $fontSet)
    {
        // a:latin
        $objWriter->startElement('a:latin');
        $objWriter->writeAttribute('typeface', $latinFont);
        $objWriter->endElement();

        // a:ea
        $objWriter->startElement('a:ea');
        $objWriter->writeAttribute('typeface', '');
        $objWriter->endElement();

        // a:cs
        $objWriter->startElement('a:cs');
        $objWriter->writeAttribute('typeface', '');
        $objWriter->endElement();

        foreach ($fontSet as $fontScript => $typeface) {
            $objWriter->startElement('a:font');
                $objWriter->writeAttribute('script', $fontScript);
                $objWriter->writeAttribute('typeface', $typeface);
            $objWriter->endElement();
        }
    }

    /**
     * Write colour scheme to XML format
     *
     * @param     PHPExcel_Shared_XMLWriter    $objWriter
     * @return     string                         XML Output
     * @throws     PHPExcel_Writer_Exception
     */
    private function writeColourScheme($objWriter)
    {
        foreach (self::$colourScheme as $colourName => $colourValue) {
            $objWriter->startElement('a:'.$colourName);

                $objWriter->startElement('a:srgbClr');
                    $objWriter->writeAttribute('val', $colourValue);
                $objWriter->endElement();

            $objWriter->endElement();
        }
    }
}
