<?php
/**
 * PHPExcel
 *
 * Copyright (c) 2006 - 2014 PHPExcel
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
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_Worksheet
 *
 * @category   PHPExcel
 * @package    PHPExcel_Worksheet
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_Worksheet implements PHPExcel_IComparable
{
    /* Break types */
    const BREAK_NONE   = 0;
    const BREAK_ROW    = 1;
    const BREAK_COLUMN = 2;

    /* Sheet state */
    const SHEETSTATE_VISIBLE    = 'visible';
    const SHEETSTATE_HIDDEN     = 'hidden';
    const SHEETSTATE_VERYHIDDEN = 'veryHidden';

    /**
     * Invalid characters in sheet title
     *
     * @var array
     */
    private static $_invalidCharacters = array('*', ':', '/', '\\', '?', '[', ']');

    /**
     * Parent spreadsheet
     *
     * @var PHPExcel
     */
    private $_parent;

    /**
     * Cacheable collection of cells
     *
     * @var PHPExcel_CachedObjectStorage_xxx
     */
    private $_cellCollection = null;

    /**
     * Collection of row dimensions
     *
     * @var PHPExcel_Worksheet_RowDimension[]
     */
    private $_rowDimensions = array();

    /**
     * Default row dimension
     *
     * @var PHPExcel_Worksheet_RowDimension
     */
    private $_defaultRowDimension = null;

    /**
     * Collection of column dimensions
     *
     * @var PHPExcel_Worksheet_ColumnDimension[]
     */
    private $_columnDimensions = array();

    /**
     * Default column dimension
     *
     * @var PHPExcel_Worksheet_ColumnDimension
     */
    private $_defaultColumnDimension = null;

    /**
     * Collection of drawings
     *
     * @var PHPExcel_Worksheet_BaseDrawing[]
     */
    private $_drawingCollection = null;

    /**
     * Collection of Chart objects
     *
     * @var PHPExcel_Chart[]
     */
    private $_chartCollection = array();

    /**
     * Worksheet title
     *
     * @var string
     */
    private $_title;

    /**
     * Sheet state
     *
     * @var string
     */
    private $_sheetState;

    /**
     * Page setup
     *
     * @var PHPExcel_Worksheet_PageSetup
     */
    private $_pageSetup;

    /**
     * Page margins
     *
     * @var PHPExcel_Worksheet_PageMargins
     */
    private $_pageMargins;

    /**
     * Page header/footer
     *
     * @var PHPExcel_Worksheet_HeaderFooter
     */
    private $_headerFooter;

    /**
     * Sheet view
     *
     * @var PHPExcel_Worksheet_SheetView
     */
    private $_sheetView;

    /**
     * Protection
     *
     * @var PHPExcel_Worksheet_Protection
     */
    private $_protection;

    /**
     * Collection of styles
     *
     * @var PHPExcel_Style[]
     */
    private $_styles = array();

    /**
     * Conditional styles. Indexed by cell coordinate, e.g. 'A1'
     *
     * @var array
     */
    private $_conditionalStylesCollection = array();

    /**
     * Is the current cell collection sorted already?
     *
     * @var boolean
     */
    private $_cellCollectionIsSorted = false;

    /**
     * Collection of breaks
     *
     * @var array
     */
    private $_breaks = array();

    /**
     * Collection of merged cell ranges
     *
     * @var array
     */
    private $_mergeCells = array();

    /**
     * Collection of protected cell ranges
     *
     * @var array
     */
    private $_protectedCells = array();

    /**
     * Autofilter Range and selection
     *
     * @var PHPExcel_Worksheet_AutoFilter
     */
    private $_autoFilter = NULL;

    /**
     * Freeze pane
     *
     * @var string
     */
    private $_freezePane = '';

    /**
     * Show gridlines?
     *
     * @var boolean
     */
    private $_showGridlines = true;

    /**
    * Print gridlines?
    *
    * @var boolean
    */
    private $_printGridlines = false;

    /**
    * Show row and column headers?
    *
    * @var boolean
    */
    private $_showRowColHeaders = true;

    /**
     * Show summary below? (Row/Column outline)
     *
     * @var boolean
     */
    private $_showSummaryBelow = true;

    /**
     * Show summary right? (Row/Column outline)
     *
     * @var boolean
     */
    private $_showSummaryRight = true;

    /**
     * Collection of comments
     *
     * @var PHPExcel_Comment[]
     */
    private $_comments = array();

    /**
     * Active cell. (Only one!)
     *
     * @var string
     */
    private $_activeCell = 'A1';

    /**
     * Selected cells
     *
     * @var string
     */
    private $_selectedCells = 'A1';

    /**
     * Cached highest column
     *
     * @var string
     */
    private $_cachedHighestColumn = 'A';

    /**
     * Cached highest row
     *
     * @var int
     */
    private $_cachedHighestRow = 1;

    /**
     * Right-to-left?
     *
     * @var boolean
     */
    private $_rightToLeft = false;

    /**
     * Hyperlinks. Indexed by cell coordinate, e.g. 'A1'
     *
     * @var array
     */
    private $_hyperlinkCollection = array();

    /**
     * Data validation objects. Indexed by cell coordinate, e.g. 'A1'
     *
     * @var array
     */
    private $_dataValidationCollection = array();

    /**
     * Tab color
     *
     * @var PHPExcel_Style_Color
     */
    private $_tabColor;

    /**
     * Dirty flag
     *
     * @var boolean
     */
    private $_dirty    = true;

    /**
     * Hash
     *
     * @var string
     */
    private $_hash    = null;

    /**
    * CodeName
    *
    * @var string
    */
    private $_codeName = null;

	/**
     * Create a new worksheet
     *
     * @param PHPExcel        $pParent
     * @param string        $pTitle
     */
    public function __construct(PHPExcel $pParent = null, $pTitle = 'Worksheet')
    {
        // Set parent and title
        $this->_parent = $pParent;
        $this->setTitle($pTitle, FALSE);
        // setTitle can change $pTitle
	    $this->setCodeName($this->getTitle());
        $this->setSheetState(PHPExcel_Worksheet::SHEETSTATE_VISIBLE);

        $this->_cellCollection        = PHPExcel_CachedObjectStorageFactory::getInstance($this);

        // Set page setup
        $this->_pageSetup            = new PHPExcel_Worksheet_PageSetup();

        // Set page margins
        $this->_pageMargins         = new PHPExcel_Worksheet_PageMargins();

        // Set page header/footer
        $this->_headerFooter        = new PHPExcel_Worksheet_HeaderFooter();

        // Set sheet view
        $this->_sheetView            = new PHPExcel_Worksheet_SheetView();

        // Drawing collection
        $this->_drawingCollection    = new ArrayObject();

        // Chart collection
        $this->_chartCollection     = new ArrayObject();

        // Protection
        $this->_protection            = new PHPExcel_Worksheet_Protection();

        // Default row dimension
        $this->_defaultRowDimension = new PHPExcel_Worksheet_RowDimension(NULL);

        // Default column dimension
        $this->_defaultColumnDimension    = new PHPExcel_Worksheet_ColumnDimension(NULL);

        $this->_autoFilter            = new PHPExcel_Worksheet_AutoFilter(NULL, $this);
    }


    /**
     * Disconnect all cells from this PHPExcel_Worksheet object,
     *    typically so that the worksheet object can be unset
     *
     */
	public function disconnectCells() {
    	if ( $this->_cellCollection !== NULL){
            $this->_cellCollection->unsetWorksheetCells();
            $this->_cellCollection = NULL;
    	}
        //    detach ourself from the workbook, so that it can then delete this worksheet successfully
        $this->_parent = null;
    }

    /**
     * Code to execute when this worksheet is unset()
     *
     */
	function __destruct() {
		PHPExcel_Calculation::getInstance($this->_parent)
		    ->clearCalculationCacheForWorksheet($this->_title);

		$this->disconnectCells();
	}

   /**
     * Return the cache controller for the cell collection
     *
     * @return PHPExcel_CachedObjectStorage_xxx
     */
	public function getCellCacheController() {
        return $this->_cellCollection;
    }    //    function getCellCacheController()


    /**
     * Get array of invalid characters for sheet title
     *
     * @return array
     */
    public static function getInvalidCharacters()
    {
        return self::$_invalidCharacters;
    }

    /**
     * Check sheet code name for valid Excel syntax
     *
     * @param string $pValue The string to check
     * @return string The valid string
     * @throws Exception
     */
    private static function _checkSheetCodeName($pValue)
    {
        $CharCount = PHPExcel_Shared_String::CountCharacters($pValue);
        if ($CharCount == 0) {
            throw new PHPExcel_Exception('Sheet code name cannot be empty.');
        }
        // Some of the printable ASCII characters are invalid:  * : / \ ? [ ] and  first and last characters cannot be a "'"
        if ((str_replace(self::$_invalidCharacters, '', $pValue) !== $pValue) || 
            (PHPExcel_Shared_String::Substring($pValue,-1,1)=='\'') || 
            (PHPExcel_Shared_String::Substring($pValue,0,1)=='\'')) {
            throw new PHPExcel_Exception('Invalid character found in sheet code name');
        }
 
        // Maximum 31 characters allowed for sheet title
        if ($CharCount > 31) {
            throw new PHPExcel_Exception('Maximum 31 characters allowed in sheet code name.');
        }
 
        return $pValue;
    }

   /**
     * Check sheet title for valid Excel syntax
     *
     * @param string $pValue The string to check
     * @return string The valid string
     * @throws PHPExcel_Exception
     */
    private static function _checkSheetTitle($pValue)
    {
        // Some of the printable ASCII characters are invalid:  * : / \ ? [ ]
        if (str_replace(self::$_invalidCharacters, '', $pValue) !== $pValue) {
            throw new PHPExcel_Exception('Invalid character found in sheet title');
        }

        // Maximum 31 characters allowed for sheet title
        if (PHPExcel_Shared_String::CountCharacters($pValue) > 31) {
            throw new PHPExcel_Exception('Maximum 31 characters allowed in sheet title.');
        }

        return $pValue;
    }

    /**
     * Get collection of cells
     *
     * @param boolean $pSorted Also sort the cell collection?
     * @return PHPExcel_Cell[]
     */
    public function getCellCollection($pSorted = true)
    {
        if ($pSorted) {
            // Re-order cell collection
            return $this->sortCellCollection();
        }
        if ($this->_cellCollection !== NULL) {
            return $this->_cellCollection->getCellList();
        }
        return array();
    }

    /**
     * Sort collection of cells
     *
     * @return PHPExcel_Worksheet
     */
    public function sortCellCollection()
    {
        if ($this->_cellCollection !== NULL) {
            return $this->_cellCollection->getSortedCellList();
        }
        return array();
    }

    /**
     * Get collection of row dimensions
     *
     * @return PHPExcel_Worksheet_RowDimension[]
     */
    public function getRowDimensions()
    {
        return $this->_rowDimensions;
    }

    /**
     * Get default row dimension
     *
     * @return PHPExcel_Worksheet_RowDimension
     */
    public function getDefaultRowDimension()
    {
        return $this->_defaultRowDimension;
    }

    /**
     * Get collection of column dimensions
     *
     * @return PHPExcel_Worksheet_ColumnDimension[]
     */
    public function getColumnDimensions()
    {
        return $this->_columnDimensions;
    }

    /**
     * Get default column dimension
     *
     * @return PHPExcel_Worksheet_ColumnDimension
     */
    public function getDefaultColumnDimension()
    {
        return $this->_defaultColumnDimension;
    }

    /**
     * Get collection of drawings
     *
     * @return PHPExcel_Worksheet_BaseDrawing[]
     */
    public function getDrawingCollection()
    {
        return $this->_drawingCollection;
    }

    /**
     * Get collection of charts
     *
     * @return PHPExcel_Chart[]
     */
    public function getChartCollection()
    {
        return $this->_chartCollection;
    }

    /**
     * Add chart
     *
     * @param PHPExcel_Chart $pChart
     * @param int|null $iChartIndex Index where chart should go (0,1,..., or null for last)
     * @return PHPExcel_Chart
     */
    public function addChart(PHPExcel_Chart $pChart = null, $iChartIndex = null)
    {
        $pChart->setWorksheet($this);
        if (is_null($iChartIndex)) {
            $this->_chartCollection[] = $pChart;
        } else {
            // Insert the chart at the requested index
            array_splice($this->_chartCollection, $iChartIndex, 0, array($pChart));
        }

        return $pChart;
    }

    /**
     * Return the count of charts on this worksheet
     *
     * @return int        The number of charts
     */
    public function getChartCount()
    {
        return count($this->_chartCollection);
    }

    /**
     * Get a chart by its index position
     *
     * @param string $index Chart index position
     * @return false|PHPExcel_Chart
     * @throws PHPExcel_Exception
     */
    public function getChartByIndex($index = null)
    {
        $chartCount = count($this->_chartCollection);
        if ($chartCount == 0) {
            return false;
        }
        if (is_null($index)) {
            $index = --$chartCount;
        }
        if (!isset($this->_chartCollection[$index])) {
            return false;
        }

        return $this->_chartCollection[$index];
    }

    /**
     * Return an array of the names of charts on this worksheet
     *
     * @return string[] The names of charts
     * @throws PHPExcel_Exception
     */
    public function getChartNames()
    {
        $chartNames = array();
        foreach($this->_chartCollection as $chart) {
            $chartNames[] = $chart->getName();
        }
        return $chartNames;
    }

    /**
     * Get a chart by name
     *
     * @param string $chartName Chart name
     * @return false|PHPExcel_Chart
     * @throws PHPExcel_Exception
     */
    public function getChartByName($chartName = '')
    {
        $chartCount = count($this->_chartCollection);
        if ($chartCount == 0) {
            return false;
        }
        foreach($this->_chartCollection as $index => $chart) {
            if ($chart->getName() == $chartName) {
                return $this->_chartCollection[$index];
            }
        }
        return false;
    }

    /**
     * Refresh column dimensions
     *
     * @return PHPExcel_Worksheet
     */
    public function refreshColumnDimensions()
    {
        $currentColumnDimensions = $this->getColumnDimensions();
        $newColumnDimensions = array();

        foreach ($currentColumnDimensions as $objColumnDimension) {
            $newColumnDimensions[$objColumnDimension->getColumnIndex()] = $objColumnDimension;
        }

        $this->_columnDimensions = $newColumnDimensions;

        return $this;
    }

    /**
     * Refresh row dimensions
     *
     * @return PHPExcel_Worksheet
     */
    public function refreshRowDimensions()
    {
        $currentRowDimensions = $this->getRowDimensions();
        $newRowDimensions = array();

        foreach ($currentRowDimensions as $objRowDimension) {
            $newRowDimensions[$objRowDimension->getRowIndex()] = $objRowDimension;
        }

        $this->_rowDimensions = $newRowDimensions;

        return $this;
    }

    /**
     * Calculate worksheet dimension
     *
     * @return string  String containing the dimension of this worksheet
     */
    public function calculateWorksheetDimension()
    {
        // Return
        return 'A1' . ':' .  $this->getHighestColumn() . $this->getHighestRow();
    }

    /**
     * Calculate worksheet data dimension
     *
     * @return string  String containing the dimension of this worksheet that actually contain data
     */
    public function calculateWorksheetDataDimension()
    {
        // Return
        return 'A1' . ':' .  $this->getHighestDataColumn() . $this->getHighestDataRow();
    }

    /**
     * Calculate widths for auto-size columns
     *
     * @param  boolean  $calculateMergeCells  Calculate merge cell width
     * @return PHPExcel_Worksheet;
     */
    public function calculateColumnWidths($calculateMergeCells = false)
    {
        // initialize $autoSizes array
        $autoSizes = array();
        foreach ($this->getColumnDimensions() as $colDimension) {
            if ($colDimension->getAutoSize()) {
                $autoSizes[$colDimension->getColumnIndex()] = -1;
            }
        }

        // There is only something to do if there are some auto-size columns
        if (!empty($autoSizes)) {

            // build list of cells references that participate in a merge
            $isMergeCell = array();
            foreach ($this->getMergeCells() as $cells) {
                foreach (PHPExcel_Cell::extractAllCellReferencesInRange($cells) as $cellReference) {
                    $isMergeCell[$cellReference] = true;
                }
            }

            // loop through all cells in the worksheet
            foreach ($this->getCellCollection(false) as $cellID) {
                $cell = $this->getCell($cellID);
				if (isset($autoSizes[$this->_cellCollection->getCurrentColumn()])) {
                    // Determine width if cell does not participate in a merge
					if (!isset($isMergeCell[$this->_cellCollection->getCurrentAddress()])) {
                        // Calculated value
                        // To formatted string
						$cellValue = PHPExcel_Style_NumberFormat::toFormattedString(
							$cell->getCalculatedValue(),
							$this->getParent()->getCellXfByIndex($cell->getXfIndex())->getNumberFormat()->getFormatCode()
						);

						$autoSizes[$this->_cellCollection->getCurrentColumn()] = max(
							(float) $autoSizes[$this->_cellCollection->getCurrentColumn()],
                            (float)PHPExcel_Shared_Font::calculateColumnWidth(
								$this->getParent()->getCellXfByIndex($cell->getXfIndex())->getFont(),
                                $cellValue,
								$this->getParent()->getCellXfByIndex($cell->getXfIndex())->getAlignment()->getTextRotation(),
                                $this->getDefaultStyle()->getFont()
                            )
                        );
                    }
                }
            }

            // adjust column widths
            foreach ($autoSizes as $columnIndex => $width) {
                if ($width == -1) $width = $this->getDefaultColumnDimension()->getWidth();
                $this->getColumnDimension($columnIndex)->setWidth($width);
            }
        }

        return $this;
    }

    /**
     * Get parent
     *
     * @return PHPExcel
     */
	public function getParent() {
        return $this->_parent;
    }

    /**
     * Re-bind parent
     *
     * @param PHPExcel $parent
     * @return PHPExcel_Worksheet
     */
	public function rebindParent(PHPExcel $parent) {
        if ($this->_parent !== null) {
            $namedRanges = $this->_parent->getNamedRanges();
            foreach ($namedRanges as $namedRange) {
                $parent->addNamedRange($namedRange);
            }

            $this->_parent->removeSheetByIndex(
                $this->_parent->getIndex($this)
            );
        }
        $this->_parent = $parent;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->_title;
    }

    /**
     * Set title
     *
     * @param string $pValue String containing the dimension of this worksheet
     * @param string $updateFormulaCellReferences boolean Flag indicating whether cell references in formulae should
     *        	be updated to reflect the new sheet name.
     *          This should be left as the default true, unless you are
     *          certain that no formula cells on any worksheet contain
     *          references to this worksheet
     * @return PHPExcel_Worksheet
     */
    public function setTitle($pValue = 'Worksheet', $updateFormulaCellReferences = true)
    {
        // Is this a 'rename' or not?
        if ($this->getTitle() == $pValue) {
            return $this;
        }

        // Syntax check
        self::_checkSheetTitle($pValue);

        // Old title
        $oldTitle = $this->getTitle();

        if ($this->_parent) {
            // Is there already such sheet name?
			if ($this->_parent->sheetNameExists($pValue)) {
                // Use name, but append with lowest possible integer

                if (PHPExcel_Shared_String::CountCharacters($pValue) > 29) {
                    $pValue = PHPExcel_Shared_String::Substring($pValue,0,29);
                }
                $i = 1;
				while ($this->_parent->sheetNameExists($pValue . ' ' . $i)) {
                    ++$i;
                    if ($i == 10) {
                        if (PHPExcel_Shared_String::CountCharacters($pValue) > 28) {
                            $pValue = PHPExcel_Shared_String::Substring($pValue,0,28);
                        }
                    } elseif ($i == 100) {
                        if (PHPExcel_Shared_String::CountCharacters($pValue) > 27) {
                            $pValue = PHPExcel_Shared_String::Substring($pValue,0,27);
                        }
                    }
                }

                $altTitle = $pValue . ' ' . $i;
                return $this->setTitle($altTitle,$updateFormulaCellReferences);
            }
        }

        // Set title
        $this->_title = $pValue;
        $this->_dirty = true;

        if ($this->_parent) {
            // New title
            $newTitle = $this->getTitle();
			PHPExcel_Calculation::getInstance($this->_parent)
			    ->renameCalculationCacheForWorksheet($oldTitle, $newTitle);
            if ($updateFormulaCellReferences)
				PHPExcel_ReferenceHelper::getInstance()->updateNamedFormulas($this->_parent, $oldTitle, $newTitle);
        }

        return $this;
    }

    /**
     * Get sheet state
     *
     * @return string Sheet state (visible, hidden, veryHidden)
     */
	public function getSheetState() {
        return $this->_sheetState;
    }

    /**
     * Set sheet state
     *
     * @param string $value Sheet state (visible, hidden, veryHidden)
     * @return PHPExcel_Worksheet
     */
	public function setSheetState($value = PHPExcel_Worksheet::SHEETSTATE_VISIBLE) {
        $this->_sheetState = $value;
        return $this;
    }

    /**
     * Get page setup
     *
     * @return PHPExcel_Worksheet_PageSetup
     */
    public function getPageSetup()
    {
        return $this->_pageSetup;
    }

    /**
     * Set page setup
     *
     * @param PHPExcel_Worksheet_PageSetup    $pValue
     * @return PHPExcel_Worksheet
     */
    public function setPageSetup(PHPExcel_Worksheet_PageSetup $pValue)
    {
        $this->_pageSetup = $pValue;
        return $this;
    }

    /**
     * Get page margins
     *
     * @return PHPExcel_Worksheet_PageMargins
     */
    public function getPageMargins()
    {
        return $this->_pageMargins;
    }

    /**
     * Set page margins
     *
     * @param PHPExcel_Worksheet_PageMargins    $pValue
     * @return PHPExcel_Worksheet
     */
    public function setPageMargins(PHPExcel_Worksheet_PageMargins $pValue)
    {
        $this->_pageMargins = $pValue;
        return $this;
    }

    /**
     * Get page header/footer
     *
     * @return PHPExcel_Worksheet_HeaderFooter
     */
    public function getHeaderFooter()
    {
        return $this->_headerFooter;
    }

    /**
     * Set page header/footer
     *
     * @param PHPExcel_Worksheet_HeaderFooter    $pValue
     * @return PHPExcel_Worksheet
     */
    public function setHeaderFooter(PHPExcel_Worksheet_HeaderFooter $pValue)
    {
        $this->_headerFooter = $pValue;
        return $this;
    }

    /**
     * Get sheet view
     *
     * @return PHPExcel_Worksheet_SheetView
     */
    public function getSheetView()
    {
        return $this->_sheetView;
    }

    /**
     * Set sheet view
     *
     * @param PHPExcel_Worksheet_SheetView    $pValue
     * @return PHPExcel_Worksheet
     */
    public function setSheetView(PHPExcel_Worksheet_SheetView $pValue)
    {
        $this->_sheetView = $pValue;
        return $this;
    }

    /**
     * Get Protection
     *
     * @return PHPExcel_Worksheet_Protection
     */
    public function getProtection()
    {
        return $this->_protection;
    }

    /**
     * Set Protection
     *
     * @param PHPExcel_Worksheet_Protection    $pValue
     * @return PHPExcel_Worksheet
     */
    public function setProtection(PHPExcel_Worksheet_Protection $pValue)
    {
        $this->_protection = $pValue;
        $this->_dirty = true;

        return $this;
    }

    /**
     * Get highest worksheet column
     *
     * @param   string     $row        Return the data highest column for the specified row,
     *                                     or the highest column of any row if no row number is passed
     * @return string Highest column name
     */
    public function getHighestColumn($row = null)
    {
        if ($row == null) {
            return $this->_cachedHighestColumn;
        }
        return $this->getHighestDataColumn($row);
    }

    /**
     * Get highest worksheet column that contains data
     *
     * @param   string     $row        Return the highest data column for the specified row,
     *                                     or the highest data column of any row if no row number is passed
     * @return string Highest column name that contains data
     */
    public function getHighestDataColumn($row = null)
    {
        return $this->_cellCollection->getHighestColumn($row);
    }

    /**
     * Get highest worksheet row
     *
     * @param   string     $column     Return the highest data row for the specified column,
     *                                     or the highest row of any column if no column letter is passed
     * @return int Highest row number
     */
    public function getHighestRow($column = null)
    {
        if ($column == null) {
            return $this->_cachedHighestRow;
        }
        return $this->getHighestDataRow($column);
    }

    /**
     * Get highest worksheet row that contains data
     *
     * @param   string     $column     Return the highest data row for the specified column,
     *                                     or the highest data row of any column if no column letter is passed
     * @return string Highest row number that contains data
     */
    public function getHighestDataRow($column = null)
    {
        return $this->_cellCollection->getHighestRow($column);
    }

    /**
     * Get highest worksheet column and highest row that have cell records
     *
     * @return array Highest column name and highest row number
     */
    public function getHighestRowAndColumn()
    {
        return $this->_cellCollection->getHighestRowAndColumn();
    }

    /**
     * Set a cell value
     *
     * @param string $pCoordinate Coordinate of the cell
     * @param mixed $pValue Value of the cell
     * @param bool $returnCell   Return the worksheet (false, default) or the cell (true)
     * @return PHPExcel_Worksheet|PHPExcel_Cell    Depending on the last parameter being specified
     */
    public function setCellValue($pCoordinate = 'A1', $pValue = null, $returnCell = false)
    {
        $cell = $this->getCell($pCoordinate)->setValue($pValue);
        return ($returnCell) ? $cell : $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates
     *
     * @param string $pColumn Numeric column coordinate of the cell (A = 0)
     * @param string $pRow Numeric row coordinate of the cell
     * @param mixed $pValue Value of the cell
     * @param bool $returnCell Return the worksheet (false, default) or the cell (true)
     * @return PHPExcel_Worksheet|PHPExcel_Cell    Depending on the last parameter being specified
     */
    public function setCellValueByColumnAndRow($pColumn = 0, $pRow = 1, $pValue = null, $returnCell = false)
    {
        $cell = $this->getCellByColumnAndRow($pColumn, $pRow)->setValue($pValue);
        return ($returnCell) ? $cell : $this;
    }

    /**
     * Set a cell value
     *
     * @param string $pCoordinate Coordinate of the cell
     * @param mixed  $pValue Value of the cell
     * @param string $pDataType Explicit data type
     * @param bool $returnCell Return the worksheet (false, default) or the cell (true)
     * @return PHPExcel_Worksheet|PHPExcel_Cell    Depending on the last parameter being specified
     */
    public function setCellValueExplicit($pCoordinate = 'A1', $pValue = null, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING, $returnCell = false)
    {
        // Set value
        $cell = $this->getCell($pCoordinate)->setValueExplicit($pValue, $pDataType);
        return ($returnCell) ? $cell : $this;
    }

    /**
     * Set a cell value by using numeric cell coordinates
     *
     * @param string $pColumn Numeric column coordinate of the cell
     * @param string $pRow Numeric row coordinate of the cell
     * @param mixed $pValue Value of the cell
     * @param string $pDataType Explicit data type
     * @param bool $returnCell Return the worksheet (false, default) or the cell (true)
     * @return PHPExcel_Worksheet|PHPExcel_Cell    Depending on the last parameter being specified
     */
    public function setCellValueExplicitByColumnAndRow($pColumn = 0, $pRow = 1, $pValue = null, $pDataType = PHPExcel_Cell_DataType::TYPE_STRING, $returnCell = false)
    {
        $cell = $this->getCellByColumnAndRow($pColumn, $pRow)->setValueExplicit($pValue, $pDataType);
        return ($returnCell) ? $cell : $this;
    }

    /**
     * Get cell at a specific coordinate
     *
     * @param string $pCoordinate    Coordinate of the cell
     * @throws PHPExcel_Exception
     * @return PHPExcel_Cell Cell that was found
     */
    public function getCell($pCoordinate = 'A1')
    {
        // Check cell collection
        if ($this->_cellCollection->isDataSet($pCoordinate)) {
            return $this->_cellCollection->getCacheData($pCoordinate);
        }

        // Worksheet reference?
        if (strpos($pCoordinate, '!') !== false) {
            $worksheetReference = PHPExcel_Worksheet::extractSheetTitle($pCoordinate, true);
			return $this->_parent->getSheetByName($worksheetReference[0])->getCell($worksheetReference[1]);
        }

        // Named range?
        if ((!preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF.'$/i', $pCoordinate, $matches)) &&
            (preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_NAMEDRANGE.'$/i', $pCoordinate, $matches))) {
            $namedRange = PHPExcel_NamedRange::resolveRange($pCoordinate, $this);
            if ($namedRange !== NULL) {
                $pCoordinate = $namedRange->getRange();
                return $namedRange->getWorksheet()->getCell($pCoordinate);
            }
        }

        // Uppercase coordinate
        $pCoordinate = strtoupper($pCoordinate);

        if (strpos($pCoordinate, ':') !== false || strpos($pCoordinate, ',') !== false) {
            throw new PHPExcel_Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate, '$') !== false) {
            throw new PHPExcel_Exception('Cell coordinate must not be absolute.');
        }

        // Create new cell object
        return $this->_createNewCell($pCoordinate);
    }

    /**
     * Get cell at a specific coordinate by using numeric cell coordinates
     *
     * @param  string $pColumn Numeric column coordinate of the cell
     * @param string $pRow Numeric row coordinate of the cell
     * @return PHPExcel_Cell Cell that was found
     */
    public function getCellByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        $columnLetter = PHPExcel_Cell::stringFromColumnIndex($pColumn);
        $coordinate = $columnLetter . $pRow;

        if ($this->_cellCollection->isDataSet($coordinate)) {
            return $this->_cellCollection->getCacheData($coordinate);
        }

		return $this->_createNewCell($coordinate);
    }

    /**
     * Create a new cell at the specified coordinate
     *
     * @param string $pCoordinate    Coordinate of the cell
     * @return PHPExcel_Cell Cell that was created
     */
	private function _createNewCell($pCoordinate)
	{
		$cell = $this->_cellCollection->addCacheData(
			$pCoordinate,
			new PHPExcel_Cell(
				NULL, 
				PHPExcel_Cell_DataType::TYPE_NULL, 
				$this
			)
		);
        $this->_cellCollectionIsSorted = false;

        // Coordinates
        $aCoordinates = PHPExcel_Cell::coordinateFromString($pCoordinate);
        if (PHPExcel_Cell::columnIndexFromString($this->_cachedHighestColumn) < PHPExcel_Cell::columnIndexFromString($aCoordinates[0]))
            $this->_cachedHighestColumn = $aCoordinates[0];
        $this->_cachedHighestRow = max($this->_cachedHighestRow, $aCoordinates[1]);

        // Cell needs appropriate xfIndex from dimensions records
		//    but don't create dimension records if they don't already exist
        $rowDimension    = $this->getRowDimension($aCoordinates[1], FALSE);
        $columnDimension = $this->getColumnDimension($aCoordinates[0], FALSE);

        if ($rowDimension !== NULL && $rowDimension->getXfIndex() > 0) {
            // then there is a row dimension with explicit style, assign it to the cell
            $cell->setXfIndex($rowDimension->getXfIndex());
        } elseif ($columnDimension !== NULL && $columnDimension->getXfIndex() > 0) {
            // then there is a column dimension, assign it to the cell
            $cell->setXfIndex($columnDimension->getXfIndex());
        }

        return $cell;
	}
	
    /**
     * Does the cell at a specific coordinate exist?
     *
     * @param string $pCoordinate  Coordinate of the cell
     * @throws PHPExcel_Exception
     * @return boolean
     */
    public function cellExists($pCoordinate = 'A1')
    {
        // Worksheet reference?
        if (strpos($pCoordinate, '!') !== false) {
            $worksheetReference = PHPExcel_Worksheet::extractSheetTitle($pCoordinate, true);
			return $this->_parent->getSheetByName($worksheetReference[0])->cellExists($worksheetReference[1]);
        }

        // Named range?
        if ((!preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_CELLREF.'$/i', $pCoordinate, $matches)) &&
            (preg_match('/^'.PHPExcel_Calculation::CALCULATION_REGEXP_NAMEDRANGE.'$/i', $pCoordinate, $matches))) {
            $namedRange = PHPExcel_NamedRange::resolveRange($pCoordinate, $this);
            if ($namedRange !== NULL) {
                $pCoordinate = $namedRange->getRange();
                if ($this->getHashCode() != $namedRange->getWorksheet()->getHashCode()) {
                    if (!$namedRange->getLocalOnly()) {
                        return $namedRange->getWorksheet()->cellExists($pCoordinate);
                    } else {
                        throw new PHPExcel_Exception('Named range ' . $namedRange->getName() . ' is not accessible from within sheet ' . $this->getTitle());
                    }
                }
            }
            else { return false; }
        }

        // Uppercase coordinate
        $pCoordinate = strtoupper($pCoordinate);

        if (strpos($pCoordinate,':') !== false || strpos($pCoordinate,',') !== false) {
            throw new PHPExcel_Exception('Cell coordinate can not be a range of cells.');
        } elseif (strpos($pCoordinate,'$') !== false) {
            throw new PHPExcel_Exception('Cell coordinate must not be absolute.');
        } else {
            // Coordinates
            $aCoordinates = PHPExcel_Cell::coordinateFromString($pCoordinate);

            // Cell exists?
            return $this->_cellCollection->isDataSet($pCoordinate);
        }
    }

    /**
     * Cell at a specific coordinate by using numeric cell coordinates exists?
     *
     * @param string $pColumn Numeric column coordinate of the cell
     * @param string $pRow Numeric row coordinate of the cell
     * @return boolean
     */
    public function cellExistsByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->cellExists(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Get row dimension at a specific row
     *
     * @param int $pRow Numeric index of the row
     * @return PHPExcel_Worksheet_RowDimension
     */
    public function getRowDimension($pRow = 1, $create = TRUE)
    {
        // Found
        $found = null;

        // Get row dimension
        if (!isset($this->_rowDimensions[$pRow])) {
			if (!$create)
				return NULL;
            $this->_rowDimensions[$pRow] = new PHPExcel_Worksheet_RowDimension($pRow);

            $this->_cachedHighestRow = max($this->_cachedHighestRow,$pRow);
        }
        return $this->_rowDimensions[$pRow];
    }

    /**
     * Get column dimension at a specific column
     *
     * @param string $pColumn String index of the column
     * @return PHPExcel_Worksheet_ColumnDimension
     */
    public function getColumnDimension($pColumn = 'A', $create = TRUE)
    {
        // Uppercase coordinate
        $pColumn = strtoupper($pColumn);

        // Fetch dimensions
        if (!isset($this->_columnDimensions[$pColumn])) {
			if (!$create)
				return NULL;
            $this->_columnDimensions[$pColumn] = new PHPExcel_Worksheet_ColumnDimension($pColumn);

            if (PHPExcel_Cell::columnIndexFromString($this->_cachedHighestColumn) < PHPExcel_Cell::columnIndexFromString($pColumn))
                $this->_cachedHighestColumn = $pColumn;
        }
        return $this->_columnDimensions[$pColumn];
    }

    /**
     * Get column dimension at a specific column by using numeric cell coordinates
     *
     * @param string $pColumn Numeric column coordinate of the cell
     * @return PHPExcel_Worksheet_ColumnDimension
     */
    public function getColumnDimensionByColumn($pColumn = 0)
    {
        return $this->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($pColumn));
    }

    /**
     * Get styles
     *
     * @return PHPExcel_Style[]
     */
    public function getStyles()
    {
        return $this->_styles;
    }

    /**
     * Get default style of workbook.
     *
     * @deprecated
     * @return PHPExcel_Style
     * @throws PHPExcel_Exception
     */
    public function getDefaultStyle()
    {
        return $this->_parent->getDefaultStyle();
    }

    /**
     * Set default style - should only be used by PHPExcel_IReader implementations!
     *
     * @deprecated
     * @param PHPExcel_Style $pValue
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function setDefaultStyle(PHPExcel_Style $pValue)
    {
        $this->_parent->getDefaultStyle()->applyFromArray(array(
            'font' => array(
                'name' => $pValue->getFont()->getName(),
                'size' => $pValue->getFont()->getSize(),
            ),
        ));
        return $this;
    }

    /**
     * Get style for cell
     *
     * @param string $pCellCoordinate Cell coordinate to get style for
     * @return PHPExcel_Style
     * @throws PHPExcel_Exception
     */
    public function getStyle($pCellCoordinate = 'A1')
    {
        // set this sheet as active
        $this->_parent->setActiveSheetIndex($this->_parent->getIndex($this));

        // set cell coordinate as active
        $this->setSelectedCells($pCellCoordinate);

        return $this->_parent->getCellXfSupervisor();
    }

    /**
     * Get conditional styles for a cell
     *
     * @param string $pCoordinate
     * @return PHPExcel_Style_Conditional[]
     */
    public function getConditionalStyles($pCoordinate = 'A1')
    {
        if (!isset($this->_conditionalStylesCollection[$pCoordinate])) {
            $this->_conditionalStylesCollection[$pCoordinate] = array();
        }
        return $this->_conditionalStylesCollection[$pCoordinate];
    }

    /**
     * Do conditional styles exist for this cell?
     *
     * @param string $pCoordinate
     * @return boolean
     */
    public function conditionalStylesExists($pCoordinate = 'A1')
    {
        if (isset($this->_conditionalStylesCollection[$pCoordinate])) {
            return true;
        }
        return false;
    }

    /**
     * Removes conditional styles for a cell
     *
     * @param string $pCoordinate
     * @return PHPExcel_Worksheet
     */
    public function removeConditionalStyles($pCoordinate = 'A1')
    {
        unset($this->_conditionalStylesCollection[$pCoordinate]);
        return $this;
    }

    /**
     * Get collection of conditional styles
     *
     * @return array
     */
    public function getConditionalStylesCollection()
    {
        return $this->_conditionalStylesCollection;
    }

    /**
     * Set conditional styles
     *
     * @param $pCoordinate string E.g. 'A1'
     * @param $pValue PHPExcel_Style_Conditional[]
     * @return PHPExcel_Worksheet
     */
    public function setConditionalStyles($pCoordinate = 'A1', $pValue)
    {
        $this->_conditionalStylesCollection[$pCoordinate] = $pValue;
        return $this;
    }

    /**
     * Get style for cell by using numeric cell coordinates
     *
     * @param int $pColumn  Numeric column coordinate of the cell
     * @param int $pRow Numeric row coordinate of the cell
     * @return PHPExcel_Style
     */
    public function getStyleByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->getStyle(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Set shared cell style to a range of cells
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
     * @deprecated
     * @param PHPExcel_Style $pSharedCellStyle Cell style to share
     * @param string $pRange Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function setSharedStyle(PHPExcel_Style $pSharedCellStyle = null, $pRange = '')
    {
        $this->duplicateStyle($pSharedCellStyle, $pRange);
        return $this;
    }

    /**
     * Duplicate cell style to a range of cells
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
     * @param PHPExcel_Style $pCellStyle Cell style to duplicate
     * @param string $pRange Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function duplicateStyle(PHPExcel_Style $pCellStyle = null, $pRange = '')
    {
        // make sure we have a real style and not supervisor
        $style = $pCellStyle->getIsSupervisor() ? $pCellStyle->getSharedComponent() : $pCellStyle;

        // Add the style to the workbook if necessary
        $workbook = $this->_parent;
		if ($existingStyle = $this->_parent->getCellXfByHashCode($pCellStyle->getHashCode())) {
            // there is already such cell Xf in our collection
            $xfIndex = $existingStyle->getIndex();
        } else {
            // we don't have such a cell Xf, need to add
            $workbook->addCellXf($pCellStyle);
            $xfIndex = $pCellStyle->getIndex();
        }

        // Calculate range outer borders
        list($rangeStart, $rangeEnd) = PHPExcel_Cell::rangeBoundaries($pRange . ':' . $pRange);

        // Make sure we can loop upwards on rows and columns
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        // Loop through cells and apply styles
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->getCell(PHPExcel_Cell::stringFromColumnIndex($col - 1) . $row)->setXfIndex($xfIndex);
            }
        }

        return $this;
    }

    /**
     * Duplicate conditional style to a range of cells
     *
     * Please note that this will overwrite existing cell styles for cells in range!
     *
	 * @param	array of PHPExcel_Style_Conditional	$pCellStyle	Cell style to duplicate
     * @param string $pRange Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function duplicateConditionalStyle(array $pCellStyle = null, $pRange = '')
    {
        foreach($pCellStyle as $cellStyle) {
            if (!($cellStyle instanceof PHPExcel_Style_Conditional)) {
                throw new PHPExcel_Exception('Style is not a conditional style');
            }
        }

        // Calculate range outer borders
        list($rangeStart, $rangeEnd) = PHPExcel_Cell::rangeBoundaries($pRange . ':' . $pRange);

        // Make sure we can loop upwards on rows and columns
        if ($rangeStart[0] > $rangeEnd[0] && $rangeStart[1] > $rangeEnd[1]) {
            $tmp = $rangeStart;
            $rangeStart = $rangeEnd;
            $rangeEnd = $tmp;
        }

        // Loop through cells and apply styles
        for ($col = $rangeStart[0]; $col <= $rangeEnd[0]; ++$col) {
            for ($row = $rangeStart[1]; $row <= $rangeEnd[1]; ++$row) {
                $this->setConditionalStyles(PHPExcel_Cell::stringFromColumnIndex($col - 1) . $row, $pCellStyle);
            }
        }

        return $this;
    }

    /**
     * Duplicate cell style array to a range of cells
     *
     * Please note that this will overwrite existing cell styles for cells in range,
     * if they are in the styles array. For example, if you decide to set a range of
     * cells to font bold, only include font bold in the styles array.
     *
     * @deprecated
     * @param array $pStyles Array containing style information
     * @param string $pRange Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @param boolean $pAdvanced Advanced mode for setting borders.
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function duplicateStyleArray($pStyles = null, $pRange = '', $pAdvanced = true)
    {
        $this->getStyle($pRange)->applyFromArray($pStyles, $pAdvanced);
        return $this;
    }

    /**
     * Set break on a cell
     *
     * @param string $pCell Cell coordinate (e.g. A1)
     * @param int $pBreak Break type (type of PHPExcel_Worksheet::BREAK_*)
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function setBreak($pCell = 'A1', $pBreak = PHPExcel_Worksheet::BREAK_NONE)
    {
        // Uppercase coordinate
        $pCell = strtoupper($pCell);

        if ($pCell != '') {
        	if ($pBreak == PHPExcel_Worksheet::BREAK_NONE) {
        		if (isset($this->_breaks[$pCell])) {
	            	unset($this->_breaks[$pCell]);
        		}
        	} else {
	            $this->_breaks[$pCell] = $pBreak;
	        }
        } else {
            throw new PHPExcel_Exception('No cell coordinate specified.');
        }

        return $this;
    }

    /**
     * Set break on a cell by using numeric cell coordinates
     *
     * @param integer $pColumn Numeric column coordinate of the cell
     * @param integer $pRow Numeric row coordinate of the cell
     * @param  integer $pBreak Break type (type of PHPExcel_Worksheet::BREAK_*)
     * @return PHPExcel_Worksheet
     */
    public function setBreakByColumnAndRow($pColumn = 0, $pRow = 1, $pBreak = PHPExcel_Worksheet::BREAK_NONE)
    {
        return $this->setBreak(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow, $pBreak);
    }

    /**
     * Get breaks
     *
     * @return array[]
     */
    public function getBreaks()
    {
        return $this->_breaks;
    }

    /**
     * Set merge on a cell range
     *
     * @param string $pRange  Cell range (e.g. A1:E1)
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function mergeCells($pRange = 'A1:A1')
    {
        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        if (strpos($pRange,':') !== false) {
            $this->_mergeCells[$pRange] = $pRange;

            // make sure cells are created

            // get the cells in the range
            $aReferences = PHPExcel_Cell::extractAllCellReferencesInRange($pRange);

            // create upper left cell if it does not already exist
            $upperLeft = $aReferences[0];
            if (!$this->cellExists($upperLeft)) {
                $this->getCell($upperLeft)->setValueExplicit(null, PHPExcel_Cell_DataType::TYPE_NULL);
            }

            // create or blank out the rest of the cells in the range
            $count = count($aReferences);
            for ($i = 1; $i < $count; $i++) {
                $this->getCell($aReferences[$i])->setValueExplicit(null, PHPExcel_Cell_DataType::TYPE_NULL);
            }

        } else {
            throw new PHPExcel_Exception('Merge must be set on a range of cells.');
        }

        return $this;
    }

    /**
     * Set merge on a cell range by using numeric cell coordinates
     *
     * @param int $pColumn1    Numeric column coordinate of the first cell
     * @param int $pRow1        Numeric row coordinate of the first cell
     * @param int $pColumn2    Numeric column coordinate of the last cell
     * @param int $pRow2        Numeric row coordinate of the last cell
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function mergeCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1)
    {
        $cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
        return $this->mergeCells($cellRange);
    }

    /**
     * Remove merge on a cell range
     *
     * @param    string            $pRange        Cell range (e.g. A1:E1)
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function unmergeCells($pRange = 'A1:A1')
    {
        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        if (strpos($pRange,':') !== false) {
            if (isset($this->_mergeCells[$pRange])) {
                unset($this->_mergeCells[$pRange]);
            } else {
                throw new PHPExcel_Exception('Cell range ' . $pRange . ' not known as merged.');
            }
        } else {
            throw new PHPExcel_Exception('Merge can only be removed from a range of cells.');
        }

        return $this;
    }

    /**
     * Remove merge on a cell range by using numeric cell coordinates
     *
     * @param int $pColumn1    Numeric column coordinate of the first cell
     * @param int $pRow1        Numeric row coordinate of the first cell
     * @param int $pColumn2    Numeric column coordinate of the last cell
     * @param int $pRow2        Numeric row coordinate of the last cell
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function unmergeCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1)
    {
        $cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
        return $this->unmergeCells($cellRange);
    }

    /**
     * Get merge cells array.
     *
     * @return array[]
     */
    public function getMergeCells()
    {
        return $this->_mergeCells;
    }

    /**
     * Set merge cells array for the entire sheet. Use instead mergeCells() to merge
     * a single cell range.
     *
     * @param array
     */
    public function setMergeCells($pValue = array())
    {
        $this->_mergeCells = $pValue;

        return $this;
    }

    /**
     * Set protection on a cell range
     *
     * @param    string            $pRange                Cell (e.g. A1) or cell range (e.g. A1:E1)
     * @param    string            $pPassword            Password to unlock the protection
     * @param    boolean        $pAlreadyHashed    If the password has already been hashed, set this to true
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function protectCells($pRange = 'A1', $pPassword = '', $pAlreadyHashed = false)
    {
        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        if (!$pAlreadyHashed) {
            $pPassword = PHPExcel_Shared_PasswordHasher::hashPassword($pPassword);
        }
        $this->_protectedCells[$pRange] = $pPassword;

        return $this;
    }

    /**
     * Set protection on a cell range by using numeric cell coordinates
     *
     * @param int  $pColumn1            Numeric column coordinate of the first cell
     * @param int  $pRow1                Numeric row coordinate of the first cell
     * @param int  $pColumn2            Numeric column coordinate of the last cell
     * @param int  $pRow2                Numeric row coordinate of the last cell
     * @param string $pPassword            Password to unlock the protection
     * @param    boolean $pAlreadyHashed    If the password has already been hashed, set this to true
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function protectCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1, $pPassword = '', $pAlreadyHashed = false)
    {
        $cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
        return $this->protectCells($cellRange, $pPassword, $pAlreadyHashed);
    }

    /**
     * Remove protection on a cell range
     *
     * @param    string            $pRange        Cell (e.g. A1) or cell range (e.g. A1:E1)
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function unprotectCells($pRange = 'A1')
    {
        // Uppercase coordinate
        $pRange = strtoupper($pRange);

        if (isset($this->_protectedCells[$pRange])) {
            unset($this->_protectedCells[$pRange]);
        } else {
            throw new PHPExcel_Exception('Cell range ' . $pRange . ' not known as protected.');
        }
        return $this;
    }

    /**
     * Remove protection on a cell range by using numeric cell coordinates
     *
     * @param int  $pColumn1            Numeric column coordinate of the first cell
     * @param int  $pRow1                Numeric row coordinate of the first cell
     * @param int  $pColumn2            Numeric column coordinate of the last cell
     * @param int $pRow2                Numeric row coordinate of the last cell
     * @param string $pPassword            Password to unlock the protection
     * @param    boolean $pAlreadyHashed    If the password has already been hashed, set this to true
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function unprotectCellsByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1, $pPassword = '', $pAlreadyHashed = false)
    {
        $cellRange = PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1 . ':' . PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2;
        return $this->unprotectCells($cellRange, $pPassword, $pAlreadyHashed);
    }

    /**
     * Get protected cells
     *
     * @return array[]
     */
    public function getProtectedCells()
    {
        return $this->_protectedCells;
    }

    /**
     *    Get Autofilter
     *
     *    @return PHPExcel_Worksheet_AutoFilter
     */
    public function getAutoFilter()
    {
        return $this->_autoFilter;
    }

    /**
     *    Set AutoFilter
     *
     *    @param    PHPExcel_Worksheet_AutoFilter|string   $pValue
     *            A simple string containing a Cell range like 'A1:E10' is permitted for backward compatibility
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet
     */
    public function setAutoFilter($pValue)
    {
        if (is_string($pValue)) {
            $this->_autoFilter->setRange($pValue);
        } elseif(is_object($pValue) && ($pValue instanceof PHPExcel_Worksheet_AutoFilter)) {
            $this->_autoFilter = $pValue;
        }
        return $this;
    }

    /**
     *    Set Autofilter Range by using numeric cell coordinates
     *
     *    @param  integer  $pColumn1    Numeric column coordinate of the first cell
     *    @param  integer  $pRow1       Numeric row coordinate of the first cell
     *    @param  integer  $pColumn2    Numeric column coordinate of the second cell
     *    @param  integer  $pRow2       Numeric row coordinate of the second cell
     *    @throws    PHPExcel_Exception
     *    @return PHPExcel_Worksheet
     */
    public function setAutoFilterByColumnAndRow($pColumn1 = 0, $pRow1 = 1, $pColumn2 = 0, $pRow2 = 1)
    {
        return $this->setAutoFilter(
            PHPExcel_Cell::stringFromColumnIndex($pColumn1) . $pRow1
            . ':' .
            PHPExcel_Cell::stringFromColumnIndex($pColumn2) . $pRow2
        );
    }

    /**
     * Remove autofilter
     *
     * @return PHPExcel_Worksheet
     */
    public function removeAutoFilter()
    {
        $this->_autoFilter->setRange(NULL);
        return $this;
    }

    /**
     * Get Freeze Pane
     *
     * @return string
     */
    public function getFreezePane()
    {
        return $this->_freezePane;
    }

    /**
     * Freeze Pane
     *
     * @param    string        $pCell        Cell (i.e. A2)
     *                                    Examples:
     *                                        A2 will freeze the rows above cell A2 (i.e row 1)
     *                                        B1 will freeze the columns to the left of cell B1 (i.e column A)
     *                                        B2 will freeze the rows above and to the left of cell A2
     *                                            (i.e row 1 and column A)
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function freezePane($pCell = '')
    {
        // Uppercase coordinate
        $pCell = strtoupper($pCell);

        if (strpos($pCell,':') === false && strpos($pCell,',') === false) {
            $this->_freezePane = $pCell;
        } else {
            throw new PHPExcel_Exception('Freeze pane can not be set on a range of cells.');
        }
        return $this;
    }

    /**
     * Freeze Pane by using numeric cell coordinates
     *
     * @param int $pColumn    Numeric column coordinate of the cell
     * @param int $pRow        Numeric row coordinate of the cell
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function freezePaneByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->freezePane(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Unfreeze Pane
     *
     * @return PHPExcel_Worksheet
     */
    public function unfreezePane()
    {
        return $this->freezePane('');
    }

    /**
     * Insert a new row, updating all possible related data
     *
     * @param int $pBefore    Insert before this one
     * @param int $pNumRows    Number of rows to insert
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function insertNewRowBefore($pBefore = 1, $pNumRows = 1) {
        if ($pBefore >= 1) {
            $objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore('A' . $pBefore, 0, $pNumRows, $this);
        } else {
            throw new PHPExcel_Exception("Rows can only be inserted before at least row 1.");
        }
        return $this;
    }

    /**
     * Insert a new column, updating all possible related data
     *
     * @param int $pBefore    Insert before this one
     * @param int $pNumCols    Number of columns to insert
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function insertNewColumnBefore($pBefore = 'A', $pNumCols = 1) {
        if (!is_numeric($pBefore)) {
            $objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore($pBefore . '1', $pNumCols, 0, $this);
        } else {
            throw new PHPExcel_Exception("Column references should not be numeric.");
        }
        return $this;
    }

    /**
     * Insert a new column, updating all possible related data
     *
     * @param int $pBefore    Insert before this one (numeric column coordinate of the cell)
     * @param int $pNumCols    Number of columns to insert
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function insertNewColumnBeforeByIndex($pBefore = 0, $pNumCols = 1) {
        if ($pBefore >= 0) {
            return $this->insertNewColumnBefore(PHPExcel_Cell::stringFromColumnIndex($pBefore), $pNumCols);
        } else {
            throw new PHPExcel_Exception("Columns can only be inserted before at least column A (0).");
        }
    }

    /**
     * Delete a row, updating all possible related data
     *
     * @param int $pRow        Remove starting with this one
     * @param int $pNumRows    Number of rows to remove
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function removeRow($pRow = 1, $pNumRows = 1) {
        if ($pRow >= 1) {
            $objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore('A' . ($pRow + $pNumRows), 0, -$pNumRows, $this);
        } else {
            throw new PHPExcel_Exception("Rows to be deleted should at least start from row 1.");
        }
        return $this;
    }

    /**
     * Remove a column, updating all possible related data
     *
     * @param int $pColumn    Remove starting with this one
     * @param int $pNumCols    Number of columns to remove
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function removeColumn($pColumn = 'A', $pNumCols = 1) {
        if (!is_numeric($pColumn)) {
            $pColumn = PHPExcel_Cell::stringFromColumnIndex(PHPExcel_Cell::columnIndexFromString($pColumn) - 1 + $pNumCols);
            $objReferenceHelper = PHPExcel_ReferenceHelper::getInstance();
            $objReferenceHelper->insertNewBefore($pColumn . '1', -$pNumCols, 0, $this);
        } else {
            throw new PHPExcel_Exception("Column references should not be numeric.");
        }
        return $this;
    }

    /**
     * Remove a column, updating all possible related data
     *
     * @param int $pColumn    Remove starting with this one (numeric column coordinate of the cell)
     * @param int $pNumCols    Number of columns to remove
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function removeColumnByIndex($pColumn = 0, $pNumCols = 1) {
        if ($pColumn >= 0) {
            return $this->removeColumn(PHPExcel_Cell::stringFromColumnIndex($pColumn), $pNumCols);
        } else {
            throw new PHPExcel_Exception("Columns to be deleted should at least start from column 0");
        }
    }

    /**
     * Show gridlines?
     *
     * @return boolean
     */
    public function getShowGridlines() {
        return $this->_showGridlines;
    }

    /**
     * Set show gridlines
     *
     * @param boolean $pValue    Show gridlines (true/false)
     * @return PHPExcel_Worksheet
     */
    public function setShowGridlines($pValue = false) {
        $this->_showGridlines = $pValue;
        return $this;
    }

    /**
    * Print gridlines?
    *
    * @return boolean
    */
    public function getPrintGridlines() {
        return $this->_printGridlines;
    }

    /**
    * Set print gridlines
    *
    * @param boolean $pValue Print gridlines (true/false)
    * @return PHPExcel_Worksheet
    */
    public function setPrintGridlines($pValue = false) {
        $this->_printGridlines = $pValue;
        return $this;
    }

    /**
    * Show row and column headers?
    *
    * @return boolean
    */
    public function getShowRowColHeaders() {
        return $this->_showRowColHeaders;
    }

    /**
    * Set show row and column headers
    *
    * @param boolean $pValue Show row and column headers (true/false)
    * @return PHPExcel_Worksheet
    */
    public function setShowRowColHeaders($pValue = false) {
        $this->_showRowColHeaders = $pValue;
        return $this;
    }

    /**
     * Show summary below? (Row/Column outlining)
     *
     * @return boolean
     */
    public function getShowSummaryBelow() {
        return $this->_showSummaryBelow;
    }

    /**
     * Set show summary below
     *
     * @param boolean $pValue    Show summary below (true/false)
     * @return PHPExcel_Worksheet
     */
    public function setShowSummaryBelow($pValue = true) {
        $this->_showSummaryBelow = $pValue;
        return $this;
    }

    /**
     * Show summary right? (Row/Column outlining)
     *
     * @return boolean
     */
    public function getShowSummaryRight() {
        return $this->_showSummaryRight;
    }

    /**
     * Set show summary right
     *
     * @param boolean $pValue    Show summary right (true/false)
     * @return PHPExcel_Worksheet
     */
    public function setShowSummaryRight($pValue = true) {
        $this->_showSummaryRight = $pValue;
        return $this;
    }

    /**
     * Get comments
     *
     * @return PHPExcel_Comment[]
     */
    public function getComments()
    {
        return $this->_comments;
    }

    /**
     * Set comments array for the entire sheet.
     *
	 * @param array of PHPExcel_Comment
     * @return PHPExcel_Worksheet
     */
    public function setComments($pValue = array())
    {
        $this->_comments = $pValue;

        return $this;
    }

    /**
     * Get comment for cell
     *
     * @param string $pCellCoordinate    Cell coordinate to get comment for
     * @return PHPExcel_Comment
     * @throws PHPExcel_Exception
     */
    public function getComment($pCellCoordinate = 'A1')
    {
        // Uppercase coordinate
        $pCellCoordinate = strtoupper($pCellCoordinate);

        if (strpos($pCellCoordinate,':') !== false || strpos($pCellCoordinate,',') !== false) {
            throw new PHPExcel_Exception('Cell coordinate string can not be a range of cells.');
        } else if (strpos($pCellCoordinate,'$') !== false) {
            throw new PHPExcel_Exception('Cell coordinate string must not be absolute.');
        } else if ($pCellCoordinate == '') {
            throw new PHPExcel_Exception('Cell coordinate can not be zero-length string.');
        } else {
            // Check if we already have a comment for this cell.
            // If not, create a new comment.
            if (isset($this->_comments[$pCellCoordinate])) {
                return $this->_comments[$pCellCoordinate];
            } else {
                $newComment = new PHPExcel_Comment();
                $this->_comments[$pCellCoordinate] = $newComment;
                return $newComment;
            }
        }
    }

    /**
     * Get comment for cell by using numeric cell coordinates
     *
     * @param int $pColumn    Numeric column coordinate of the cell
     * @param int $pRow        Numeric row coordinate of the cell
     * @return PHPExcel_Comment
     */
    public function getCommentByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->getComment(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Get selected cell
     *
     * @deprecated
     * @return string
     */
    public function getSelectedCell()
    {
        return $this->getSelectedCells();
    }

    /**
     * Get active cell
     *
     * @return string Example: 'A1'
     */
    public function getActiveCell()
    {
        return $this->_activeCell;
    }

    /**
     * Get selected cells
     *
     * @return string
     */
    public function getSelectedCells()
    {
        return $this->_selectedCells;
    }

    /**
     * Selected cell
     *
     * @param    string        $pCoordinate    Cell (i.e. A1)
     * @return PHPExcel_Worksheet
     */
    public function setSelectedCell($pCoordinate = 'A1')
    {
        return $this->setSelectedCells($pCoordinate);
    }

    /**
     * Select a range of cells.
     *
     * @param    string        $pCoordinate    Cell range, examples: 'A1', 'B2:G5', 'A:C', '3:6'
     * @throws    PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function setSelectedCells($pCoordinate = 'A1')
    {
        // Uppercase coordinate
        $pCoordinate = strtoupper($pCoordinate);

        // Convert 'A' to 'A:A'
        $pCoordinate = preg_replace('/^([A-Z]+)$/', '${1}:${1}', $pCoordinate);

        // Convert '1' to '1:1'
        $pCoordinate = preg_replace('/^([0-9]+)$/', '${1}:${1}', $pCoordinate);

        // Convert 'A:C' to 'A1:C1048576'
        $pCoordinate = preg_replace('/^([A-Z]+):([A-Z]+)$/', '${1}1:${2}1048576', $pCoordinate);

        // Convert '1:3' to 'A1:XFD3'
        $pCoordinate = preg_replace('/^([0-9]+):([0-9]+)$/', 'A${1}:XFD${2}', $pCoordinate);

        if (strpos($pCoordinate,':') !== false || strpos($pCoordinate,',') !== false) {
            list($first, ) = PHPExcel_Cell::splitRange($pCoordinate);
            $this->_activeCell = $first[0];
        } else {
            $this->_activeCell = $pCoordinate;
        }
        $this->_selectedCells = $pCoordinate;
        return $this;
    }

    /**
     * Selected cell by using numeric cell coordinates
     *
     * @param int $pColumn Numeric column coordinate of the cell
     * @param int $pRow Numeric row coordinate of the cell
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function setSelectedCellByColumnAndRow($pColumn = 0, $pRow = 1)
    {
        return $this->setSelectedCells(PHPExcel_Cell::stringFromColumnIndex($pColumn) . $pRow);
    }

    /**
     * Get right-to-left
     *
     * @return boolean
     */
    public function getRightToLeft() {
        return $this->_rightToLeft;
    }

    /**
     * Set right-to-left
     *
     * @param boolean $value    Right-to-left true/false
     * @return PHPExcel_Worksheet
     */
    public function setRightToLeft($value = false) {
        $this->_rightToLeft = $value;
        return $this;
    }

    /**
     * Fill worksheet from values in array
     *
     * @param array $source Source array
     * @param mixed $nullValue Value in source array that stands for blank cell
     * @param string $startCell Insert array starting from this cell address as the top left coordinate
     * @param boolean $strictNullComparison Apply strict comparison when testing for null values in the array
     * @throws PHPExcel_Exception
     * @return PHPExcel_Worksheet
     */
    public function fromArray($source = null, $nullValue = null, $startCell = 'A1', $strictNullComparison = false) {
        if (is_array($source)) {
            //    Convert a 1-D array to 2-D (for ease of looping)
            if (!is_array(end($source))) {
                $source = array($source);
            }

            // start coordinate
            list ($startColumn, $startRow) = PHPExcel_Cell::coordinateFromString($startCell);

            // Loop through $source
            foreach ($source as $rowData) {
                $currentColumn = $startColumn;
                foreach($rowData as $cellValue) {
                    if ($strictNullComparison) {
                        if ($cellValue !== $nullValue) {
                            // Set cell value
                            $this->getCell($currentColumn . $startRow)->setValue($cellValue);
                        }
                    } else {
                        if ($cellValue != $nullValue) {
                            // Set cell value
                            $this->getCell($currentColumn . $startRow)->setValue($cellValue);
                        }
                    }
                    ++$currentColumn;
                }
                ++$startRow;
            }
        } else {
            throw new PHPExcel_Exception("Parameter \$source should be an array.");
        }
        return $this;
    }

    /**
     * Create array from a range of cells
     *
     * @param string $pRange Range of cells (i.e. "A1:B10"), or just one cell (i.e. "A1")
     * @param mixed $nullValue Value returned in the array entry if a cell doesn't exist
     * @param boolean $calculateFormulas Should formulas be calculated?
     * @param boolean $formatData Should formatting be applied to cell values?
     * @param boolean $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                               True - Return rows and columns indexed by their actual row and column IDs
     * @return array
     */
	public function rangeToArray($pRange = 'A1', $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false) {
        // Returnvalue
        $returnValue = array();
        //    Identify the range that we need to extract from the worksheet
        list($rangeStart, $rangeEnd) = PHPExcel_Cell::rangeBoundaries($pRange);
        $minCol = PHPExcel_Cell::stringFromColumnIndex($rangeStart[0] -1);
        $minRow = $rangeStart[1];
        $maxCol = PHPExcel_Cell::stringFromColumnIndex($rangeEnd[0] -1);
        $maxRow = $rangeEnd[1];

        $maxCol++;
        // Loop through rows
        $r = -1;
        for ($row = $minRow; $row <= $maxRow; ++$row) {
            $rRef = ($returnCellRef) ? $row : ++$r;
            $c = -1;
            // Loop through columns in the current row
            for ($col = $minCol; $col != $maxCol; ++$col) {
                $cRef = ($returnCellRef) ? $col : ++$c;
                //    Using getCell() will create a new cell if it doesn't already exist. We don't want that to happen
                //        so we test and retrieve directly against _cellCollection
                if ($this->_cellCollection->isDataSet($col.$row)) {
                    // Cell exists
                    $cell = $this->_cellCollection->getCacheData($col.$row);
                    if ($cell->getValue() !== null) {
                        if ($cell->getValue() instanceof PHPExcel_RichText) {
                            $returnValue[$rRef][$cRef] = $cell->getValue()->getPlainText();
                        } else {
                            if ($calculateFormulas) {
                                $returnValue[$rRef][$cRef] = $cell->getCalculatedValue();
                            } else {
                                $returnValue[$rRef][$cRef] = $cell->getValue();
                            }
                        }

                        if ($formatData) {
                            $style = $this->_parent->getCellXfByIndex($cell->getXfIndex());
                            $returnValue[$rRef][$cRef] = PHPExcel_Style_NumberFormat::toFormattedString(
                            	$returnValue[$rRef][$cRef],
								($style && $style->getNumberFormat()) ?
									$style->getNumberFormat()->getFormatCode() :
									PHPExcel_Style_NumberFormat::FORMAT_GENERAL
                            );
                        }
                    } else {
                        // Cell holds a NULL
                        $returnValue[$rRef][$cRef] = $nullValue;
                    }
                } else {
                    // Cell doesn't exist
                    $returnValue[$rRef][$cRef] = $nullValue;
                }
            }
        }

        // Return
        return $returnValue;
    }


    /**
     * Create array from a range of cells
     *
     * @param  string $pNamedRange Name of the Named Range
     * @param  mixed  $nullValue Value returned in the array entry if a cell doesn't exist
     * @param  boolean $calculateFormulas  Should formulas be calculated?
     * @param  boolean $formatData  Should formatting be applied to cell values?
     * @param  boolean $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                                True - Return rows and columns indexed by their actual row and column IDs
     * @return array
     * @throws PHPExcel_Exception
     */
	public function namedRangeToArray($pNamedRange = '', $nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false) {
        $namedRange = PHPExcel_NamedRange::resolveRange($pNamedRange, $this);
        if ($namedRange !== NULL) {
            $pWorkSheet = $namedRange->getWorksheet();
            $pCellRange = $namedRange->getRange();

			return $pWorkSheet->rangeToArray(	$pCellRange,
												$nullValue, $calculateFormulas, $formatData, $returnCellRef);
        }

        throw new PHPExcel_Exception('Named Range '.$pNamedRange.' does not exist.');
    }


    /**
     * Create array from worksheet
     *
     * @param mixed $nullValue Value returned in the array entry if a cell doesn't exist
     * @param boolean $calculateFormulas Should formulas be calculated?
     * @param boolean $formatData  Should formatting be applied to cell values?
     * @param boolean $returnCellRef False - Return a simple array of rows and columns indexed by number counting from zero
     *                               True - Return rows and columns indexed by their actual row and column IDs
     * @return array
     */
	public function toArray($nullValue = null, $calculateFormulas = true, $formatData = true, $returnCellRef = false) {
        // Garbage collect...
        $this->garbageCollect();

        //    Identify the range that we need to extract from the worksheet
        $maxCol = $this->getHighestColumn();
        $maxRow = $this->getHighestRow();
        // Return
		return $this->rangeToArray(	'A1:'.$maxCol.$maxRow,
									$nullValue, $calculateFormulas, $formatData, $returnCellRef);
    }

    /**
     * Get row iterator
     *
     * @param  integer                           $startRow    The row number at which to start iterating
     * @return PHPExcel_Worksheet_RowIterator
     */
	public function getRowIterator($startRow = 1) {
        return new PHPExcel_Worksheet_RowIterator($this,$startRow);
    }

    /**
     * Run PHPExcel garabage collector.
     *
     * @return PHPExcel_Worksheet
     */
	public function garbageCollect() {
        // Flush cache
        $this->_cellCollection->getCacheData('A1');
        // Build a reference table from images
//        $imageCoordinates = array();
//        $iterator = $this->getDrawingCollection()->getIterator();
//        while ($iterator->valid()) {
//            $imageCoordinates[$iterator->current()->getCoordinates()] = true;
//
//            $iterator->next();
//        }
//
        // Lookup highest column and highest row if cells are cleaned
        $colRow = $this->_cellCollection->getHighestRowAndColumn();
        $highestRow = $colRow['row'];
        $highestColumn = PHPExcel_Cell::columnIndexFromString($colRow['column']);

        // Loop through column dimensions
        foreach ($this->_columnDimensions as $dimension) {
            $highestColumn = max($highestColumn,PHPExcel_Cell::columnIndexFromString($dimension->getColumnIndex()));
        }

        // Loop through row dimensions
        foreach ($this->_rowDimensions as $dimension) {
            $highestRow = max($highestRow,$dimension->getRowIndex());
        }

        // Cache values
        if ($highestColumn < 0) {
            $this->_cachedHighestColumn = 'A';
        } else {
            $this->_cachedHighestColumn = PHPExcel_Cell::stringFromColumnIndex(--$highestColumn);
        }
        $this->_cachedHighestRow = $highestRow;

        // Return
        return $this;
    }

    /**
     * Get hash code
     *
     * @return string    Hash code
     */
	public function getHashCode() {
        if ($this->_dirty) {
            $this->_hash = md5( $this->_title .
                                $this->_autoFilter .
                                ($this->_protection->isProtectionEnabled() ? 't' : 'f') .
                                __CLASS__
                              );
            $this->_dirty = false;
        }
        return $this->_hash;
    }

    /**
     * Extract worksheet title from range.
     *
     * Example: extractSheetTitle("testSheet!A1") ==> 'A1'
     * Example: extractSheetTitle("'testSheet 1'!A1", true) ==> array('testSheet 1', 'A1');
     *
     * @param string $pRange    Range to extract title from
     * @param bool $returnRange    Return range? (see example)
     * @return mixed
     */
	public static function extractSheetTitle($pRange, $returnRange = false) {
        // Sheet title included?
        if (($sep = strpos($pRange, '!')) === false) {
            return '';
        }

        if ($returnRange) {
            return array( trim(substr($pRange, 0, $sep),"'"),
                          substr($pRange, $sep + 1)
                        );
        }

        return substr($pRange, $sep + 1);
    }

    /**
     * Get hyperlink
     *
     * @param string $pCellCoordinate    Cell coordinate to get hyperlink for
     */
    public function getHyperlink($pCellCoordinate = 'A1')
    {
        // return hyperlink if we already have one
        if (isset($this->_hyperlinkCollection[$pCellCoordinate])) {
            return $this->_hyperlinkCollection[$pCellCoordinate];
        }

        // else create hyperlink
        $this->_hyperlinkCollection[$pCellCoordinate] = new PHPExcel_Cell_Hyperlink();
        return $this->_hyperlinkCollection[$pCellCoordinate];
    }

    /**
     * Set hyperlnk
     *
     * @param string $pCellCoordinate    Cell coordinate to insert hyperlink
     * @param    PHPExcel_Cell_Hyperlink    $pHyperlink
     * @return PHPExcel_Worksheet
     */
    public function setHyperlink($pCellCoordinate = 'A1', PHPExcel_Cell_Hyperlink $pHyperlink = null)
    {
        if ($pHyperlink === null) {
            unset($this->_hyperlinkCollection[$pCellCoordinate]);
        } else {
            $this->_hyperlinkCollection[$pCellCoordinate] = $pHyperlink;
        }
        return $this;
    }

    /**
     * Hyperlink at a specific coordinate exists?
     *
     * @param string $pCoordinate
     * @return boolean
     */
    public function hyperlinkExists($pCoordinate = 'A1')
    {
        return isset($this->_hyperlinkCollection[$pCoordinate]);
    }

    /**
     * Get collection of hyperlinks
     *
     * @return PHPExcel_Cell_Hyperlink[]
     */
    public function getHyperlinkCollection()
    {
        return $this->_hyperlinkCollection;
    }

    /**
     * Get data validation
     *
     * @param string $pCellCoordinate Cell coordinate to get data validation for
     */
    public function getDataValidation($pCellCoordinate = 'A1')
    {
        // return data validation if we already have one
        if (isset($this->_dataValidationCollection[$pCellCoordinate])) {
            return $this->_dataValidationCollection[$pCellCoordinate];
        }

        // else create data validation
        $this->_dataValidationCollection[$pCellCoordinate] = new PHPExcel_Cell_DataValidation();
        return $this->_dataValidationCollection[$pCellCoordinate];
    }

    /**
     * Set data validation
     *
     * @param string $pCellCoordinate    Cell coordinate to insert data validation
     * @param    PHPExcel_Cell_DataValidation    $pDataValidation
     * @return PHPExcel_Worksheet
     */
    public function setDataValidation($pCellCoordinate = 'A1', PHPExcel_Cell_DataValidation $pDataValidation = null)
    {
        if ($pDataValidation === null) {
            unset($this->_dataValidationCollection[$pCellCoordinate]);
        } else {
            $this->_dataValidationCollection[$pCellCoordinate] = $pDataValidation;
        }
        return $this;
    }

    /**
     * Data validation at a specific coordinate exists?
     *
     * @param string $pCoordinate
     * @return boolean
     */
    public function dataValidationExists($pCoordinate = 'A1')
    {
        return isset($this->_dataValidationCollection[$pCoordinate]);
    }

    /**
     * Get collection of data validations
     *
     * @return PHPExcel_Cell_DataValidation[]
     */
    public function getDataValidationCollection()
    {
        return $this->_dataValidationCollection;
    }

    /**
     * Accepts a range, returning it as a range that falls within the current highest row and column of the worksheet
     *
     * @param string $range
     * @return string Adjusted range value
     */
	public function shrinkRangeToFit($range) {
        $maxCol = $this->getHighestColumn();
        $maxRow = $this->getHighestRow();
        $maxCol = PHPExcel_Cell::columnIndexFromString($maxCol);

        $rangeBlocks = explode(' ',$range);
        foreach ($rangeBlocks as &$rangeSet) {
            $rangeBoundaries = PHPExcel_Cell::getRangeBoundaries($rangeSet);

            if (PHPExcel_Cell::columnIndexFromString($rangeBoundaries[0][0]) > $maxCol) { $rangeBoundaries[0][0] = PHPExcel_Cell::stringFromColumnIndex($maxCol); }
            if ($rangeBoundaries[0][1] > $maxRow) { $rangeBoundaries[0][1] = $maxRow; }
            if (PHPExcel_Cell::columnIndexFromString($rangeBoundaries[1][0]) > $maxCol) { $rangeBoundaries[1][0] = PHPExcel_Cell::stringFromColumnIndex($maxCol); }
            if ($rangeBoundaries[1][1] > $maxRow) { $rangeBoundaries[1][1] = $maxRow; }
            $rangeSet = $rangeBoundaries[0][0].$rangeBoundaries[0][1].':'.$rangeBoundaries[1][0].$rangeBoundaries[1][1];
        }
        unset($rangeSet);
        $stRange = implode(' ',$rangeBlocks);

        return $stRange;
    }

    /**
     * Get tab color
     *
     * @return PHPExcel_Style_Color
     */
    public function getTabColor()
    {
        if ($this->_tabColor === NULL)
            $this->_tabColor = new PHPExcel_Style_Color();

        return $this->_tabColor;
    }

    /**
     * Reset tab color
     *
     * @return PHPExcel_Worksheet
     */
    public function resetTabColor()
    {
        $this->_tabColor = null;
        unset($this->_tabColor);

        return $this;
    }

    /**
     * Tab color set?
     *
     * @return boolean
     */
    public function isTabColorSet()
    {
        return ($this->_tabColor !== NULL);
    }

    /**
     * Copy worksheet (!= clone!)
     *
     * @return PHPExcel_Worksheet
     */
	public function copy() {
        $copied = clone $this;

        return $copied;
    }

    /**
     * Implement PHP __clone to create a deep clone, not just a shallow copy.
     */
	public function __clone() {
        foreach ($this as $key => $val) {
            if ($key == '_parent') {
                continue;
            }

            if (is_object($val) || (is_array($val))) {
                if ($key == '_cellCollection') {
                    $newCollection = clone $this->_cellCollection;
                    $newCollection->copyCellCollection($this);
                    $this->_cellCollection = $newCollection;
                } elseif ($key == '_drawingCollection') {
                    $newCollection = clone $this->_drawingCollection;
                    $this->_drawingCollection = $newCollection;
                } elseif (($key == '_autoFilter') && ($this->_autoFilter instanceof PHPExcel_Worksheet_AutoFilter)) {
                    $newAutoFilter = clone $this->_autoFilter;
                    $this->_autoFilter = $newAutoFilter;
                    $this->_autoFilter->setParent($this);
                } else {
                    $this->{$key} = unserialize(serialize($val));
                }
            }
        }
    }
/**
	 * Define the code name of the sheet
	 *
	 * @param null|string Same rule as Title minus space not allowed (but, like Excel, change silently space to underscore)
	 * @return objWorksheet
	 * @throws PHPExcel_Exception
	*/
	public function setCodeName($pValue=null){
		// Is this a 'rename' or not?
		if ($this->getCodeName() == $pValue) {
			return $this;
		}
		$pValue = str_replace(' ', '_', $pValue);//Excel does this automatically without flinching, we are doing the same
		// Syntax check
        // throw an exception if not valid
		self::_checkSheetCodeName($pValue);

		// We use the same code that setTitle to find a valid codeName else not using a space (Excel don't like) but a '_'
		
        if ($this->getParent()) {
			// Is there already such sheet name?
			if ($this->getParent()->sheetCodeNameExists($pValue)) {
				// Use name, but append with lowest possible integer

				if (PHPExcel_Shared_String::CountCharacters($pValue) > 29) {
					$pValue = PHPExcel_Shared_String::Substring($pValue,0,29);
				}
				$i = 1;
				while ($this->getParent()->sheetCodeNameExists($pValue . '_' . $i)) {
					++$i;
					if ($i == 10) {
						if (PHPExcel_Shared_String::CountCharacters($pValue) > 28) {
							$pValue = PHPExcel_Shared_String::Substring($pValue,0,28);
						}
					} elseif ($i == 100) {
						if (PHPExcel_Shared_String::CountCharacters($pValue) > 27) {
							$pValue = PHPExcel_Shared_String::Substring($pValue,0,27);
						}
					}
				}

				$pValue = $pValue . '_' . $i;// ok, we have a valid name
				//codeName is'nt used in formula : no need to call for an update
				//return $this->setTitle($altTitle,$updateFormulaCellReferences);
			}
		}

		$this->_codeName=$pValue;
		return $this;
	}
	/**
	 * Return the code name of the sheet
	 *
	 * @return null|string
	*/
	public function getCodeName(){
		return $this->_codeName;
	}
	/**
	 * Sheet has a code name ?
	 * @return boolean
	*/
	public function hasCodeName(){
		return !(is_null($this->_codeName));
	}
}
