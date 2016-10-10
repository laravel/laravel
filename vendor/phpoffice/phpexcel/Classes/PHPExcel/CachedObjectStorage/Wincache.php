<?php

/**
 * PHPExcel_CachedObjectStorage_Wincache
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
 * @package    PHPExcel_CachedObjectStorage
 * @copyright  Copyright (c) 2006 - 2015 PHPExcel (http://www.codeplex.com/PHPExcel)
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt    LGPL
 * @version    ##VERSION##, ##DATE##
 */
class PHPExcel_CachedObjectStorage_Wincache extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache
{
    /**
     * Prefix used to uniquely identify cache data for this worksheet
     *
     * @var string
     */
    private $cachePrefix = null;

    /**
     * Cache timeout
     *
     * @var integer
     */
    private $cacheTime = 600;


    /**
     * Store cell data in cache for the current cell object if it's "dirty",
     *     and the 'nullify' the current cell object
     *
     * @return    void
     * @throws    PHPExcel_Exception
     */
    protected function storeData()
    {
        if ($this->currentCellIsDirty && !empty($this->currentObjectID)) {
            $this->currentObject->detach();

            $obj = serialize($this->currentObject);
            if (wincache_ucache_exists($this->cachePrefix.$this->currentObjectID.'.cache')) {
                if (!wincache_ucache_set($this->cachePrefix.$this->currentObjectID.'.cache', $obj, $this->cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell '.$this->currentObjectID.' in WinCache');
                }
            } else {
                if (!wincache_ucache_add($this->cachePrefix.$this->currentObjectID.'.cache', $obj, $this->cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell '.$this->currentObjectID.' in WinCache');
                }
            }
            $this->currentCellIsDirty = false;
        }

        $this->currentObjectID = $this->currentObject = null;
    }

    /**
     * Add or Update a cell in cache identified by coordinate address
     *
     * @param    string            $pCoord        Coordinate address of the cell to update
     * @param    PHPExcel_Cell    $cell        Cell to update
     * @return    PHPExcel_Cell
     * @throws    PHPExcel_Exception
     */
    public function addCacheData($pCoord, PHPExcel_Cell $cell)
    {
        if (($pCoord !== $this->currentObjectID) && ($this->currentObjectID !== null)) {
            $this->storeData();
        }
        $this->cellCache[$pCoord] = true;

        $this->currentObjectID = $pCoord;
        $this->currentObject = $cell;
        $this->currentCellIsDirty = true;

        return $cell;
    }

    /**
     * Is a value set in the current PHPExcel_CachedObjectStorage_ICache for an indexed cell?
     *
     * @param    string        $pCoord        Coordinate address of the cell to check
     * @return    boolean
     */
    public function isDataSet($pCoord)
    {
        //    Check if the requested entry is the current object, or exists in the cache
        if (parent::isDataSet($pCoord)) {
            if ($this->currentObjectID == $pCoord) {
                return true;
            }
            //    Check if the requested entry still exists in cache
            $success = wincache_ucache_exists($this->cachePrefix.$pCoord.'.cache');
            if ($success === false) {
                //    Entry no longer exists in Wincache, so clear it from the cache array
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry '.$pCoord.' no longer exists in WinCache');
            }
            return true;
        }
        return false;
    }


    /**
     * Get cell at a specific coordinate
     *
     * @param    string            $pCoord        Coordinate of the cell
     * @throws    PHPExcel_Exception
     * @return    PHPExcel_Cell    Cell that was found, or null if not found
     */
    public function getCacheData($pCoord)
    {
        if ($pCoord === $this->currentObjectID) {
            return $this->currentObject;
        }
        $this->storeData();

        //    Check if the entry that has been requested actually exists
        $obj = null;
        if (parent::isDataSet($pCoord)) {
            $success = false;
            $obj = wincache_ucache_get($this->cachePrefix.$pCoord.'.cache', $success);
            if ($success === false) {
                //    Entry no longer exists in WinCache, so clear it from the cache array
                parent::deleteCacheData($pCoord);
                throw new PHPExcel_Exception('Cell entry '.$pCoord.' no longer exists in WinCache');
            }
        } else {
            //    Return null if requested entry doesn't exist in cache
            return null;
        }

        //    Set current entry to the requested entry
        $this->currentObjectID = $pCoord;
        $this->currentObject = unserialize($obj);
        //    Re-attach this as the cell's parent
        $this->currentObject->attach($this);

        //    Return requested entry
        return $this->currentObject;
    }


    /**
     * Get a list of all cell addresses currently held in cache
     *
     * @return  string[]
     */
    public function getCellList()
    {
        if ($this->currentObjectID !== null) {
            $this->storeData();
        }

        return parent::getCellList();
    }

    /**
     * Delete a cell in cache identified by coordinate address
     *
     * @param    string            $pCoord        Coordinate address of the cell to delete
     * @throws    PHPExcel_Exception
     */
    public function deleteCacheData($pCoord)
    {
        //    Delete the entry from Wincache
        wincache_ucache_delete($this->cachePrefix.$pCoord.'.cache');

        //    Delete the entry from our cell address array
        parent::deleteCacheData($pCoord);
    }

    /**
     * Clone the cell collection
     *
     * @param    PHPExcel_Worksheet    $parent        The new worksheet
     * @return    void
     */
    public function copyCellCollection(PHPExcel_Worksheet $parent)
    {
        parent::copyCellCollection($parent);
        //    Get a new id for the new file name
        $baseUnique = $this->getUniqueID();
        $newCachePrefix = substr(md5($baseUnique), 0, 8) . '.';
        $cacheList = $this->getCellList();
        foreach ($cacheList as $cellID) {
            if ($cellID != $this->currentObjectID) {
                $success = false;
                $obj = wincache_ucache_get($this->cachePrefix.$cellID.'.cache', $success);
                if ($success === false) {
                    //    Entry no longer exists in WinCache, so clear it from the cache array
                    parent::deleteCacheData($cellID);
                    throw new PHPExcel_Exception('Cell entry '.$cellID.' no longer exists in Wincache');
                }
                if (!wincache_ucache_add($newCachePrefix.$cellID.'.cache', $obj, $this->cacheTime)) {
                    $this->__destruct();
                    throw new PHPExcel_Exception('Failed to store cell '.$cellID.' in Wincache');
                }
            }
        }
        $this->cachePrefix = $newCachePrefix;
    }


    /**
     * Clear the cell collection and disconnect from our parent
     *
     * @return    void
     */
    public function unsetWorksheetCells()
    {
        if (!is_null($this->currentObject)) {
            $this->currentObject->detach();
            $this->currentObject = $this->currentObjectID = null;
        }

        //    Flush the WinCache cache
        $this->__destruct();

        $this->cellCache = array();

        //    detach ourself from the worksheet, so that it can then delete this object successfully
        $this->parent = null;
    }

    /**
     * Initialise this new cell collection
     *
     * @param    PHPExcel_Worksheet    $parent        The worksheet for this cell collection
     * @param    array of mixed        $arguments    Additional initialisation arguments
     */
    public function __construct(PHPExcel_Worksheet $parent, $arguments)
    {
        $cacheTime    = (isset($arguments['cacheTime']))    ? $arguments['cacheTime']    : 600;

        if (is_null($this->cachePrefix)) {
            $baseUnique = $this->getUniqueID();
            $this->cachePrefix = substr(md5($baseUnique), 0, 8).'.';
            $this->cacheTime = $cacheTime;

            parent::__construct($parent);
        }
    }

    /**
     * Destroy this cell collection
     */
    public function __destruct()
    {
        $cacheList = $this->getCellList();
        foreach ($cacheList as $cellID) {
            wincache_ucache_delete($this->cachePrefix.$cellID.'.cache');
        }
    }

    /**
     * Identify whether the caching method is currently available
     * Some methods are dependent on the availability of certain extensions being enabled in the PHP build
     *
     * @return    boolean
     */
    public static function cacheMethodIsAvailable()
    {
        if (!function_exists('wincache_ucache_add')) {
            return false;
        }

        return true;
    }
}
