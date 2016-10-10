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
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt	LGPL
 * @version    1.8.0, 2014-03-02
 */


/**
 * PHPExcel_CachedObjectStorage_Memcache
 *
 * @category   PHPExcel
 * @package    PHPExcel_CachedObjectStorage
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_CachedObjectStorage_Memcache extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache {

	/**
	 * Prefix used to uniquely identify cache data for this worksheet
	 *
	 * @var string
	 */
	private $_cachePrefix = null;

	/**
	 * Cache timeout
	 *
	 * @var integer
	 */
	private $_cacheTime = 600;

	/**
	 * Memcache interface
	 *
	 * @var resource
	 */
	private $_memcache = null;


    /**
     * Store cell data in cache for the current cell object if it's "dirty",
     *     and the 'nullify' the current cell object
     *
	 * @return	void
     * @throws	PHPExcel_Exception
     */
	protected function _storeData() {
		if ($this->_currentCellIsDirty && !empty($this->_currentObjectID)) {
			$this->_currentObject->detach();

			$obj = serialize($this->_currentObject);
			if (!$this->_memcache->replace($this->_cachePrefix.$this->_currentObjectID.'.cache',$obj,NULL,$this->_cacheTime)) {
				if (!$this->_memcache->add($this->_cachePrefix.$this->_currentObjectID.'.cache',$obj,NULL,$this->_cacheTime)) {
					$this->__destruct();
					throw new PHPExcel_Exception('Failed to store cell '.$this->_currentObjectID.' in MemCache');
				}
			}
			$this->_currentCellIsDirty = false;
		}
		$this->_currentObjectID = $this->_currentObject = null;
	}	//	function _storeData()


    /**
     * Add or Update a cell in cache identified by coordinate address
     *
     * @param	string			$pCoord		Coordinate address of the cell to update
     * @param	PHPExcel_Cell	$cell		Cell to update
	 * @return	void
     * @throws	PHPExcel_Exception
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
	}	//	function addCacheData()


	/**
	 * Is a value set in the current PHPExcel_CachedObjectStorage_ICache for an indexed cell?
	 *
	 * @param	string		$pCoord		Coordinate address of the cell to check
	 * @return	void
	 * @return	boolean
	 */
	public function isDataSet($pCoord) {
		//	Check if the requested entry is the current object, or exists in the cache
		if (parent::isDataSet($pCoord)) {
			if ($this->_currentObjectID == $pCoord) {
				return true;
			}
			//	Check if the requested entry still exists in Memcache
			$success = $this->_memcache->get($this->_cachePrefix.$pCoord.'.cache');
			if ($success === false) {
				//	Entry no longer exists in Memcache, so clear it from the cache array
				parent::deleteCacheData($pCoord);
				throw new PHPExcel_Exception('Cell entry '.$pCoord.' no longer exists in MemCache');
			}
			return true;
		}
		return false;
	}	//	function isDataSet()


	/**
     * Get cell at a specific coordinate
     *
     * @param 	string 			$pCoord		Coordinate of the cell
     * @throws 	PHPExcel_Exception
     * @return 	PHPExcel_Cell 	Cell that was found, or null if not found
     */
	public function getCacheData($pCoord) {
		if ($pCoord === $this->_currentObjectID) {
			return $this->_currentObject;
		}
		$this->_storeData();

		//	Check if the entry that has been requested actually exists
		if (parent::isDataSet($pCoord)) {
			$obj = $this->_memcache->get($this->_cachePrefix.$pCoord.'.cache');
			if ($obj === false) {
				//	Entry no longer exists in Memcache, so clear it from the cache array
				parent::deleteCacheData($pCoord);
				throw new PHPExcel_Exception('Cell entry '.$pCoord.' no longer exists in MemCache');
			}
		} else {
			//	Return null if requested entry doesn't exist in cache
			return null;
		}

		//	Set current entry to the requested entry
		$this->_currentObjectID = $pCoord;
		$this->_currentObject = unserialize($obj);
        //    Re-attach this as the cell's parent
        $this->_currentObject->attach($this);

		//	Return requested entry
		return $this->_currentObject;
	}	//	function getCacheData()


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
     * @param	string			$pCoord		Coordinate address of the cell to delete
     * @throws	PHPExcel_Exception
     */
	public function deleteCacheData($pCoord) {
		//	Delete the entry from Memcache
		$this->_memcache->delete($this->_cachePrefix.$pCoord.'.cache');

		//	Delete the entry from our cell address array
		parent::deleteCacheData($pCoord);
	}	//	function deleteCacheData()


	/**
	 * Clone the cell collection
	 *
	 * @param	PHPExcel_Worksheet	$parent		The new worksheet
	 * @return	void
	 */
	public function copyCellCollection(PHPExcel_Worksheet $parent) {
		parent::copyCellCollection($parent);
		//	Get a new id for the new file name
		$baseUnique = $this->_getUniqueID();
		$newCachePrefix = substr(md5($baseUnique),0,8).'.';
		$cacheList = $this->getCellList();
		foreach($cacheList as $cellID) {
			if ($cellID != $this->_currentObjectID) {
				$obj = $this->_memcache->get($this->_cachePrefix.$cellID.'.cache');
				if ($obj === false) {
					//	Entry no longer exists in Memcache, so clear it from the cache array
					parent::deleteCacheData($cellID);
					throw new PHPExcel_Exception('Cell entry '.$cellID.' no longer exists in MemCache');
				}
				if (!$this->_memcache->add($newCachePrefix.$cellID.'.cache',$obj,NULL,$this->_cacheTime)) {
					$this->__destruct();
					throw new PHPExcel_Exception('Failed to store cell '.$cellID.' in MemCache');
				}
			}
		}
		$this->_cachePrefix = $newCachePrefix;
	}	//	function copyCellCollection()


	/**
	 * Clear the cell collection and disconnect from our parent
	 *
	 * @return	void
	 */
	public function unsetWorksheetCells() {
		if(!is_null($this->_currentObject)) {
			$this->_currentObject->detach();
			$this->_currentObject = $this->_currentObjectID = null;
		}

		//	Flush the Memcache cache
		$this->__destruct();

		$this->_cellCache = array();

		//	detach ourself from the worksheet, so that it can then delete this object successfully
		$this->_parent = null;
	}	//	function unsetWorksheetCells()


	/**
	 * Initialise this new cell collection
	 *
	 * @param	PHPExcel_Worksheet	$parent		The worksheet for this cell collection
	 * @param	array of mixed		$arguments	Additional initialisation arguments
	 */
	public function __construct(PHPExcel_Worksheet $parent, $arguments) {
		$memcacheServer	= (isset($arguments['memcacheServer']))	? $arguments['memcacheServer']	: 'localhost';
		$memcachePort	= (isset($arguments['memcachePort']))	? $arguments['memcachePort']	: 11211;
		$cacheTime		= (isset($arguments['cacheTime']))		? $arguments['cacheTime']		: 600;

		if (is_null($this->_cachePrefix)) {
			$baseUnique = $this->_getUniqueID();
			$this->_cachePrefix = substr(md5($baseUnique),0,8).'.';

			//	Set a new Memcache object and connect to the Memcache server
			$this->_memcache = new Memcache();
			if (!$this->_memcache->addServer($memcacheServer, $memcachePort, false, 50, 5, 5, true, array($this, 'failureCallback'))) {
				throw new PHPExcel_Exception('Could not connect to MemCache server at '.$memcacheServer.':'.$memcachePort);
			}
			$this->_cacheTime = $cacheTime;

			parent::__construct($parent);
		}
	}	//	function __construct()


	/**
	 * Memcache error handler
	 *
	 * @param	string	$host		Memcache server
	 * @param	integer	$port		Memcache port
     * @throws	PHPExcel_Exception
	 */
	public function failureCallback($host, $port) {
		throw new PHPExcel_Exception('memcache '.$host.':'.$port.' failed');
	}


	/**
	 * Destroy this cell collection
	 */
	public function __destruct() {
		$cacheList = $this->getCellList();
		foreach($cacheList as $cellID) {
			$this->_memcache->delete($this->_cachePrefix.$cellID.'.cache');
		}
	}	//	function __destruct()

	/**
	 * Identify whether the caching method is currently available
	 * Some methods are dependent on the availability of certain extensions being enabled in the PHP build
	 *
	 * @return	boolean
	 */
	public static function cacheMethodIsAvailable() {
		if (!function_exists('memcache_add')) {
			return false;
		}

		return true;
	}

}
