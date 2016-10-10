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
 * @package    PHPExcel_CachedObjectStorage
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_CachedObjectStorage_APC
 *
 * @category   PHPExcel
 * @package    PHPExcel_CachedObjectStorage
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_CachedObjectStorage_APC extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache {

    /**
     * Prefix used to uniquely identify cache data for this worksheet
     *
     * @access    private
     * @var string
     */
    private $_cachePrefix = null;

    /**
     * Cache timeout
     *
     * @access    private
     * @var integer
     */
    private $_cacheTime = 600;


    /**
     * Store cell data in cache for the current cell object if it's "dirty",
     *     and the 'nullify' the current cell object
     *
     * @access  private
     * @return  void
     * @throws  PHPExcel_Exception
     */
    protected function _storeData() {
        if ($this->_currentCellIsDirty && !empty($this->_currentObjectID)) {
            $this->_currentObject->detach();

            if (!apc_store($this->_cachePrefix.$this->_currentObjectID.'.cache',serialize($this->_currentObject),$this->_cacheTime)) {
                $this->__destruct();
                throw new PHPExcel_Exception('Failed to store cell '.$this->_currentObjectID.' in APC');
            }
            $this->_currentCellIsDirty = false;
        }
        $this->_currentObjectID = $this->_currentObject = null;
    }    //    function _storeData()


    /**
     * Add or Update a cell in cache identified by coordinate address
     *
     * @access  public
     * @param   string         $pCoord  Coordinate address of the cell to update
     * @param   PHPExcel_Cell  $cell    Cell to update
     * @return  void
     * @throws  PHPExcel_Exception
     */
    public function addCacheData($pCoord, PHPExcel_Cell $cell) {
        if (($pCoord !== $this->_currentObjectID) && ($this->_currentObjectID !== null)) {
            $this->_storeData();
        }
        $this->_cellCache[$pCoord] = true;

        $this->_currentObjectID = $pCoord;
        $this->_currentObject = $cell;
        $this->_currentCellIsDirty = true;

        return $cell;
    }    //    function addCacheData()


    /**
     * Is a value set in the current PHPExcel_CachedObjectStorage_ICache for an indexed cell?
     *
     * @access  public
     * @param   string  $pCoord  Coordinate address of the cell to check
     * @return  void
     * @return  boolean
     */
    public function isDataSet($pCoord) {
        //    Check if the requested entry is the current object, or exists in the cache
        if (parent::isDataSet($pCoord)) {
            if ($this->_currentObjectID == $pCoord) {
                return true;
            }
            //    Check if the requested entry still exists in apc
            $success = apc_fetch($this->_cachePrefix.$pCoord.'.cache');
            if ($success === FALSE) {
                //    Entry no longer exists in APC, so clear it from the cache array
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry '.$pCoord.' no longer exists in APC cache');
            }
            return true;
        }
        return false;
    }    //    function isDataSet()


    /**
     * Get cell at a specific coordinate
     *
     * @access  public
     * @param   string         $pCoord  Coordinate of the cell
     * @throws  PHPExcel_Exception
     * @return  PHPExcel_Cell  Cell that was found, or null if not found
     */
    public function getCacheData($pCoord) {
        if ($pCoord === $this->_currentObjectID) {
            return $this->_currentObject;
        }
        $this->_storeData();

        //    Check if the entry that has been requested actually exists
        if (parent::isDataSet($pCoord)) {
            $obj = apc_fetch($this->_cachePrefix.$pCoord.'.cache');
            if ($obj === FALSE) {
                //    Entry no longer exists in APC, so clear it from the cache array
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry '.$pCoord.' no longer exists in APC cache');
            }
        } else {
            //    Return null if requested entry doesn't exist in cache
            return null;
        }

        //    Set current entry to the requested entry
        $this->_currentObjectID = $pCoord;
        $this->_currentObject = unserialize($obj);
        //    Re-attach this as the cell's parent
        $this->_currentObject->attach($this);

        //    Return requested entry
        return $this->_currentObject;
    }    //    function getCacheData()


	/**
	 * Get a list of all cell addresses currently held in cache
	 *
	 * @return  array of string
	 */
	public function getCellList() {
		if ($this->_currentObjectID !== null) {
			$this->_storeData();
		}

		return parent::getCellList();
	}


    /**
     * Delete a cell in cache identified by coordinate address
     *
     * @access  public
     * @param   string  $pCoord  Coordinate address of the cell to delete
     * @throws  PHPExcel_Exception
     */
    public function deleteCacheData($pCoord) {
        //    Delete the entry from APC
        apc_delete($this->_cachePrefix.$pCoord.'.cache');

        //    Delete the entry from our cell address array
        parent::deleteCacheData($pCoord);
    }    //    function deleteCacheData()


    /**
     * Clone the cell collection
     *
     * @access  public
     * @param   PHPExcel_Worksheet  $parent  The new worksheet
     * @throws  PHPExcel_Exception
     * @return  void
     */
    public function copyCellCollection(PHPExcel_Worksheet $parent) {
        parent::copyCellCollection($parent);
        //    Get a new id for the new file name
        $baseUnique = $this->_getUniqueID();
        $newCachePrefix = substr(md5($baseUnique),0,8).'.';
        $cacheList = $this->getCellList();
        foreach($cacheList as $cellID) {
            if ($cellID != $this->_currentObjectID) {
                $obj = apc_fetch($this->_cachePrefix.$cellID.'.cache');
                if ($obj === FALSE) {
                    //    Entry no longer exists in APC, so clear it from the cache array
                    parent::deleteCacheData($cellID);
                    throw new PHPExcel_Exception('Cell entry '.$cellID.' no longer exists in APC');
                }
                if (!apc_store($newCachePrefix.$cellID.'.cache',$obj,$this->_cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell '.$cellID.' in APC');
                }
            }
        }
        $this->_cachePrefix = $newCachePrefix;
    }    //    function copyCellCollection()


    /**
     * Clear the cell collection and disconnect from our parent
     *
     * @return  void
     */
    public function unsetWorksheetCells() {
        if ($this->_currentObject !== NULL) {
            $this->_currentObject->detach();
            $this->_currentObject = $this->_currentObjectID = null;
        }

        //    Flush the APC cache
        $this->__destruct();

        $this->_cellCache = array();

        //    detach ourself from the worksheet, so that it can then delete this object successfully
        $this->_parent = null;
    }    //    function unsetWorksheetCells()


    /**
     * Initialise this new cell collection
     *
     * @param  PHPExcel_Worksheet  $parent     The worksheet for this cell collection
     * @param  array of mixed      $arguments  Additional initialisation arguments
     */
    public function __construct(PHPExcel_Worksheet $parent, $arguments) {
        $cacheTime = (isset($arguments['cacheTime'])) ? $arguments['cacheTime'] : 600;

        if ($this->_cachePrefix === NULL) {
            $baseUnique = $this->_getUniqueID();
            $this->_cachePrefix = substr(md5($baseUnique),0,8).'.';
            $this->_cacheTime = $cacheTime;

            parent::__construct($parent);
        }
    }    //    function __construct()


    /**
     * Destroy this cell collection
     */
    public function __destruct() {
        $cacheList = $this->getCellList();
        foreach($cacheList as $cellID) {
            apc_delete($this->_cachePrefix.$cellID.'.cache');
        }
    }    //    function __destruct()


    /**
     * Identify whether the caching method is currently available
     * Some methods are dependent on the availability of certain extensions being enabled in the PHP build
     *
     * @return  boolean
     */
    public static function cacheMethodIsAvailable() {
        if (!function_exists('apc_store')) {
            return FALSE;
        }
        if (apc_sma_info() === FALSE) {
            return FALSE;
        }

        return TRUE;
    }

}
