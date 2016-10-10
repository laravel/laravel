<?php

/**
 * PHPExcel_Writer_Excel2007
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
class PHPExcel_Writer_Excel2007 extends PHPExcel_Writer_Abstract implements PHPExcel_Writer_IWriter
{
    /**
     * Pre-calculate formulas
     * Forces PHPExcel to recalculate all formulae in a workbook when saving, so that the pre-calculated values are
     *    immediately available to MS Excel or other office spreadsheet viewer when opening the file
     *
     * Overrides the default TRUE for this specific writer for performance reasons
     *
     * @var boolean
     */
    protected $preCalculateFormulas = false;

    /**
     * Office2003 compatibility
     *
     * @var boolean
     */
    private $office2003compatibility = false;

    /**
     * Private writer parts
     *
     * @var PHPExcel_Writer_Excel2007_WriterPart[]
     */
    private $writerParts    = array();

    /**
     * Private PHPExcel
     *
     * @var PHPExcel
     */
    private $spreadSheet;

    /**
     * Private string table
     *
     * @var string[]
     */
    private $stringTable    = array();

    /**
     * Private unique PHPExcel_Style_Conditional HashTable
     *
     * @var PHPExcel_HashTable
     */
    private $stylesConditionalHashTable;

    /**
     * Private unique PHPExcel_Style HashTable
     *
     * @var PHPExcel_HashTable
     */
    private $styleHashTable;

    /**
     * Private unique PHPExcel_Style_Fill HashTable
     *
     * @var PHPExcel_HashTable
     */
    private $fillHashTable;

    /**
     * Private unique PHPExcel_Style_Font HashTable
     *
     * @var PHPExcel_HashTable
     */
    private $fontHashTable;

    /**
     * Private unique PHPExcel_Style_Borders HashTable
     *
     * @var PHPExcel_HashTable
     */
    private $bordersHashTable ;

    /**
     * Private unique PHPExcel_Style_NumberFormat HashTable
     *
     * @var PHPExcel_HashTable
     */
    private $numFmtHashTable;

    /**
     * Private unique PHPExcel_Worksheet_BaseDrawing HashTable
     *
     * @var PHPExcel_HashTable
     */
    private $drawingHashTable;

    /**
     * Create a new PHPExcel_Writer_Excel2007
     *
     * @param     PHPExcel    $pPHPExcel
     */
    public function __construct(PHPExcel $pPHPExcel = null)
    {
        // Assign PHPExcel
        $this->setPHPExcel($pPHPExcel);

        $writerPartsArray = array(  'stringtable'       => 'PHPExcel_Writer_Excel2007_StringTable',
                                    'contenttypes'      => 'PHPExcel_Writer_Excel2007_ContentTypes',
                                    'docprops'          => 'PHPExcel_Writer_Excel2007_DocProps',
                                    'rels'              => 'PHPExcel_Writer_Excel2007_Rels',
                                    'theme'             => 'PHPExcel_Writer_Excel2007_Theme',
                                    'style'             => 'PHPExcel_Writer_Excel2007_Style',
                                    'workbook'          => 'PHPExcel_Writer_Excel2007_Workbook',
                                    'worksheet'         => 'PHPExcel_Writer_Excel2007_Worksheet',
                                    'drawing'           => 'PHPExcel_Writer_Excel2007_Drawing',
                                    'comments'          => 'PHPExcel_Writer_Excel2007_Comments',
                                    'chart'             => 'PHPExcel_Writer_Excel2007_Chart',
                                    'relsvba'           => 'PHPExcel_Writer_Excel2007_RelsVBA',
                                    'relsribbonobjects' => 'PHPExcel_Writer_Excel2007_RelsRibbon'
                                 );

        //    Initialise writer parts
        //        and Assign their parent IWriters
        foreach ($writerPartsArray as $writer => $class) {
            $this->writerParts[$writer] = new $class($this);
        }

        $hashTablesArray = array( 'stylesConditionalHashTable',    'fillHashTable',        'fontHashTable',
                                  'bordersHashTable',                'numFmtHashTable',        'drawingHashTable',
                                  'styleHashTable'
                                );

        // Set HashTable variables
        foreach ($hashTablesArray as $tableName) {
            $this->$tableName     = new PHPExcel_HashTable();
        }
    }

    /**
     * Get writer part
     *
     * @param     string     $pPartName        Writer part name
     * @return     PHPExcel_Writer_Excel2007_WriterPart
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
     * @param     string         $pFilename
     * @throws     PHPExcel_Writer_Exception
     */
    public function save($pFilename = null)
    {
        if ($this->spreadSheet !== null) {
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

            $saveDebugLog = PHPExcel_Calculation::getInstance($this->spreadSheet)->getDebugLog()->getWriteDebugLog();
            PHPExcel_Calculation::getInstance($this->spreadSheet)->getDebugLog()->setWriteDebugLog(false);
            $saveDateReturnType = PHPExcel_Calculation_Functions::getReturnDateType();
            PHPExcel_Calculation_Functions::setReturnDateType(PHPExcel_Calculation_Functions::RETURNDATE_EXCEL);

            // Create string lookup table
            $this->stringTable = array();
            for ($i = 0; $i < $this->spreadSheet->getSheetCount(); ++$i) {
                $this->stringTable = $this->getWriterPart('StringTable')->createStringTable($this->spreadSheet->getSheet($i), $this->stringTable);
            }

            // Create styles dictionaries
            $this->styleHashTable->addFromSource($this->getWriterPart('Style')->allStyles($this->spreadSheet));
            $this->stylesConditionalHashTable->addFromSource($this->getWriterPart('Style')->allConditionalStyles($this->spreadSheet));
            $this->fillHashTable->addFromSource($this->getWriterPart('Style')->allFills($this->spreadSheet));
            $this->fontHashTable->addFromSource($this->getWriterPart('Style')->allFonts($this->spreadSheet));
            $this->bordersHashTable->addFromSource($this->getWriterPart('Style')->allBorders($this->spreadSheet));
            $this->numFmtHashTable->addFromSource($this->getWriterPart('Style')->allNumberFormats($this->spreadSheet));

            // Create drawing dictionary
            $this->drawingHashTable->addFromSource($this->getWriterPart('Drawing')->allDrawings($this->spreadSheet));

            // Create new ZIP file and open it for writing
            $zipClass = PHPExcel_Settings::getZipClass();
            $objZip = new $zipClass();

            //    Retrieve OVERWRITE and CREATE constants from the instantiated zip class
            //    This method of accessing constant values from a dynamic class should work with all appropriate versions of PHP
            $ro = new ReflectionObject($objZip);
            $zipOverWrite = $ro->getConstant('OVERWRITE');
            $zipCreate = $ro->getConstant('CREATE');

            if (file_exists($pFilename)) {
                unlink($pFilename);
            }
            // Try opening the ZIP file
            if ($objZip->open($pFilename, $zipOverWrite) !== true) {
                if ($objZip->open($pFilename, $zipCreate) !== true) {
                    throw new PHPExcel_Writer_Exception("Could not open " . $pFilename . " for writing.");
                }
            }

            // Add [Content_Types].xml to ZIP file
            $objZip->addFromString('[Content_Types].xml', $this->getWriterPart('ContentTypes')->writeContentTypes($this->spreadSheet, $this->includeCharts));

            //if hasMacros, add the vbaProject.bin file, Certificate file(if exists)
            if ($this->spreadSheet->hasMacros()) {
                $macrosCode=$this->spreadSheet->getMacrosCode();
                if (!is_null($macrosCode)) {// we have the code ?
                    $objZip->addFromString('xl/vbaProject.bin', $macrosCode);//allways in 'xl', allways named vbaProject.bin
                    if ($this->spreadSheet->hasMacrosCertificate()) {//signed macros ?
                        // Yes : add the certificate file and the related rels file
                        $objZip->addFromString('xl/vbaProjectSignature.bin', $this->spreadSheet->getMacrosCertificate());
                        $objZip->addFromString('xl/_rels/vbaProject.bin.rels', $this->getWriterPart('RelsVBA')->writeVBARelationships($this->spreadSheet));
                    }
                }
            }
            //a custom UI in this workbook ? add it ("base" xml and additional objects (pictures) and rels)
            if ($this->spreadSheet->hasRibbon()) {
                $tmpRibbonTarget=$this->spreadSheet->getRibbonXMLData('target');
                $objZip->addFromString($tmpRibbonTarget, $this->spreadSheet->getRibbonXMLData('data'));
                if ($this->spreadSheet->hasRibbonBinObjects()) {
                    $tmpRootPath=dirname($tmpRibbonTarget).'/';
                    $ribbonBinObjects=$this->spreadSheet->getRibbonBinObjects('data');//the files to write
                    foreach ($ribbonBinObjects as $aPath => $aContent) {
                        $objZip->addFromString($tmpRootPath.$aPath, $aContent);
                    }
                    //the rels for files
                    $objZip->addFromString($tmpRootPath.'_rels/'.basename($tmpRibbonTarget).'.rels', $this->getWriterPart('RelsRibbonObjects')->writeRibbonRelationships($this->spreadSheet));
                }
            }
            
            // Add relationships to ZIP file
            $objZip->addFromString('_rels/.rels', $this->getWriterPart('Rels')->writeRelationships($this->spreadSheet));
            $objZip->addFromString('xl/_rels/workbook.xml.rels', $this->getWriterPart('Rels')->writeWorkbookRelationships($this->spreadSheet));

            // Add document properties to ZIP file
            $objZip->addFromString('docProps/app.xml', $this->getWriterPart('DocProps')->writeDocPropsApp($this->spreadSheet));
            $objZip->addFromString('docProps/core.xml', $this->getWriterPart('DocProps')->writeDocPropsCore($this->spreadSheet));
            $customPropertiesPart = $this->getWriterPart('DocProps')->writeDocPropsCustom($this->spreadSheet);
            if ($customPropertiesPart !== null) {
                $objZip->addFromString('docProps/custom.xml', $customPropertiesPart);
            }

            // Add theme to ZIP file
            $objZip->addFromString('xl/theme/theme1.xml', $this->getWriterPart('Theme')->writeTheme($this->spreadSheet));

            // Add string table to ZIP file
            $objZip->addFromString('xl/sharedStrings.xml', $this->getWriterPart('StringTable')->writeStringTable($this->stringTable));

            // Add styles to ZIP file
            $objZip->addFromString('xl/styles.xml', $this->getWriterPart('Style')->writeStyles($this->spreadSheet));

            // Add workbook to ZIP file
            $objZip->addFromString('xl/workbook.xml', $this->getWriterPart('Workbook')->writeWorkbook($this->spreadSheet, $this->preCalculateFormulas));

            $chartCount = 0;
            // Add worksheets
            for ($i = 0; $i < $this->spreadSheet->getSheetCount(); ++$i) {
                $objZip->addFromString('xl/worksheets/sheet' . ($i + 1) . '.xml', $this->getWriterPart('Worksheet')->writeWorksheet($this->spreadSheet->getSheet($i), $this->stringTable, $this->includeCharts));
                if ($this->includeCharts) {
                    $charts = $this->spreadSheet->getSheet($i)->getChartCollection();
                    if (count($charts) > 0) {
                        foreach ($charts as $chart) {
                            $objZip->addFromString('xl/charts/chart' . ($chartCount + 1) . '.xml', $this->getWriterPart('Chart')->writeChart($chart, $this->preCalculateFormulas));
                            $chartCount++;
                        }
                    }
                }
            }

            $chartRef1 = $chartRef2 = 0;
            // Add worksheet relationships (drawings, ...)
            for ($i = 0; $i < $this->spreadSheet->getSheetCount(); ++$i) {
                // Add relationships
                $objZip->addFromString('xl/worksheets/_rels/sheet' . ($i + 1) . '.xml.rels', $this->getWriterPart('Rels')->writeWorksheetRelationships($this->spreadSheet->getSheet($i), ($i + 1), $this->includeCharts));

                $drawings = $this->spreadSheet->getSheet($i)->getDrawingCollection();
                $drawingCount = count($drawings);
                if ($this->includeCharts) {
                    $chartCount = $this->spreadSheet->getSheet($i)->getChartCount();
                }

                // Add drawing and image relationship parts
                if (($drawingCount > 0) || ($chartCount > 0)) {
                    // Drawing relationships
                    $objZip->addFromString('xl/drawings/_rels/drawing' . ($i + 1) . '.xml.rels', $this->getWriterPart('Rels')->writeDrawingRelationships($this->spreadSheet->getSheet($i), $chartRef1, $this->includeCharts));

                    // Drawings
                    $objZip->addFromString('xl/drawings/drawing' . ($i + 1) . '.xml', $this->getWriterPart('Drawing')->writeDrawings($this->spreadSheet->getSheet($i), $chartRef2, $this->includeCharts));
                }

                // Add comment relationship parts
                if (count($this->spreadSheet->getSheet($i)->getComments()) > 0) {
                    // VML Comments
                    $objZip->addFromString('xl/drawings/vmlDrawing' . ($i + 1) . '.vml', $this->getWriterPart('Comments')->writeVMLComments($this->spreadSheet->getSheet($i)));

                    // Comments
                    $objZip->addFromString('xl/comments' . ($i + 1) . '.xml', $this->getWriterPart('Comments')->writeComments($this->spreadSheet->getSheet($i)));
                }

                // Add header/footer relationship parts
                if (count($this->spreadSheet->getSheet($i)->getHeaderFooter()->getImages()) > 0) {
                    // VML Drawings
                    $objZip->addFromString('xl/drawings/vmlDrawingHF' . ($i + 1) . '.vml', $this->getWriterPart('Drawing')->writeVMLHeaderFooterImages($this->spreadSheet->getSheet($i)));

                    // VML Drawing relationships
                    $objZip->addFromString('xl/drawings/_rels/vmlDrawingHF' . ($i + 1) . '.vml.rels', $this->getWriterPart('Rels')->writeHeaderFooterDrawingRelationships($this->spreadSheet->getSheet($i)));

                    // Media
                    foreach ($this->spreadSheet->getSheet($i)->getHeaderFooter()->getImages() as $image) {
                        $objZip->addFromString('xl/media/' . $image->getIndexedFilename(), file_get_contents($image->getPath()));
                    }
                }
            }

            // Add media
            for ($i = 0; $i < $this->getDrawingHashTable()->count(); ++$i) {
                if ($this->getDrawingHashTable()->getByIndex($i) instanceof PHPExcel_Worksheet_Drawing) {
                    $imageContents = null;
                    $imagePath = $this->getDrawingHashTable()->getByIndex($i)->getPath();
                    if (strpos($imagePath, 'zip://') !== false) {
                        $imagePath = substr($imagePath, 6);
                        $imagePathSplitted = explode('#', $imagePath);

                        $imageZip = new ZipArchive();
                        $imageZip->open($imagePathSplitted[0]);
                        $imageContents = $imageZip->getFromName($imagePathSplitted[1]);
                        $imageZip->close();
                        unset($imageZip);
                    } else {
                        $imageContents = file_get_contents($imagePath);
                    }

                    $objZip->addFromString('xl/media/' . str_replace(' ', '_', $this->getDrawingHashTable()->getByIndex($i)->getIndexedFilename()), $imageContents);
                } elseif ($this->getDrawingHashTable()->getByIndex($i) instanceof PHPExcel_Worksheet_MemoryDrawing) {
                    ob_start();
                    call_user_func(
                        $this->getDrawingHashTable()->getByIndex($i)->getRenderingFunction(),
                        $this->getDrawingHashTable()->getByIndex($i)->getImageResource()
                    );
                    $imageContents = ob_get_contents();
                    ob_end_clean();

                    $objZip->addFromString('xl/media/' . str_replace(' ', '_', $this->getDrawingHashTable()->getByIndex($i)->getIndexedFilename()), $imageContents);
                }
            }

            PHPExcel_Calculation_Functions::setReturnDateType($saveDateReturnType);
            PHPExcel_Calculation::getInstance($this->spreadSheet)->getDebugLog()->setWriteDebugLog($saveDebugLog);

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
        } else {
            throw new PHPExcel_Writer_Exception("PHPExcel object unassigned.");
        }
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
            throw new PHPExcel_Writer_Exception("No PHPExcel object assigned.");
        }
    }

    /**
     * Set PHPExcel object
     *
     * @param     PHPExcel     $pPHPExcel    PHPExcel object
     * @throws    PHPExcel_Writer_Exception
     * @return PHPExcel_Writer_Excel2007
     */
    public function setPHPExcel(PHPExcel $pPHPExcel = null)
    {
        $this->spreadSheet = $pPHPExcel;
        return $this;
    }

    /**
     * Get string table
     *
     * @return string[]
     */
    public function getStringTable()
    {
        return $this->stringTable;
    }

    /**
     * Get PHPExcel_Style HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getStyleHashTable()
    {
        return $this->styleHashTable;
    }

    /**
     * Get PHPExcel_Style_Conditional HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getStylesConditionalHashTable()
    {
        return $this->stylesConditionalHashTable;
    }

    /**
     * Get PHPExcel_Style_Fill HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getFillHashTable()
    {
        return $this->fillHashTable;
    }

    /**
     * Get PHPExcel_Style_Font HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getFontHashTable()
    {
        return $this->fontHashTable;
    }

    /**
     * Get PHPExcel_Style_Borders HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getBordersHashTable()
    {
        return $this->bordersHashTable;
    }

    /**
     * Get PHPExcel_Style_NumberFormat HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getNumFmtHashTable()
    {
        return $this->numFmtHashTable;
    }

    /**
     * Get PHPExcel_Worksheet_BaseDrawing HashTable
     *
     * @return PHPExcel_HashTable
     */
    public function getDrawingHashTable()
    {
        return $this->drawingHashTable;
    }

    /**
     * Get Office2003 compatibility
     *
     * @return boolean
     */
    public function getOffice2003Compatibility()
    {
        return $this->office2003compatibility;
    }

    /**
     * Set Office2003 compatibility
     *
     * @param boolean $pValue    Office2003 compatibility?
     * @return PHPExcel_Writer_Excel2007
     */
    public function setOffice2003Compatibility($pValue = false)
    {
        $this->office2003compatibility = $pValue;
        return $this;
    }
}
