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
 * PHPExcel_CachedObjectStorage_SQLite3
 *
 * @category   PHPExcel
 * @package    PHPExcel_CachedObjectStorage
 * @copyright  Copyright (c) 2006 - 2014 PHPExcel (http://www.codeplex.com/PHPExcel)
 */
class PHPExcel_CachedObjectStorage_SQLite3 extends PHPExcel_CachedObjectStorage_CacheBase implements PHPExcel_CachedObjectStorage_ICache {

	/**
	 * Database table name
	 *
	 * @var string
	 */
	private $_TableName = null;

	/**
	 * Database handle
	 *
	 * @var resource
	 */
	private $_DBHandle = null;

	/**
	 * Prepared statement for a SQLite3 select query
	 *
	 * @var SQLite3Stmt
	 */
	private $_selectQuery;

	/**
	 * Prepared statement for a SQLite3 insert query
	 *
	 * @var SQLite3Stmt
	 */
	private $_insertQuery;

	/**
	 * Prepared statement for a SQLite3 update query
	 *
	 * @var SQLite3Stmt
	 */
	private $_updateQuery;

	/**
	 * Prepared statement for a SQLite3 delete query
	 *
	 * @var SQLite3Stmt
	 */
	private $_deleteQuery;

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

			$this->_insertQuery->bindValue('id',$this->_currentObjectID,SQLITE3_TEXT);
			$this->_insertQuery->bindValue('data',serialize($this->_currentObject),SQLITE3_BLOB);
			$result = $this->_insertQuery->execute();
			if ($result === false)
				throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
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

		$this->_currentObjectID = $pCoord;
		$this->_currentObject = $cell;
		$this->_currentCellIsDirty = true;

		return $cell;
	}	//	function addCacheData()


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

		$this->_selectQuery->bindValue('id',$pCoord,SQLITE3_TEXT);
		$cellResult = $this->_selectQuery->execute();
		if ($cellResult === FALSE) {
			throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
		}
		$cellData = $cellResult->fetchArray(SQLITE3_ASSOC);
		if ($cellData === FALSE) {
			//	Return null if requested entry doesn't exist in cache
			return NULL;
		}

		//	Set current entry to the requested entry
		$this->_currentObjectID = $pCoord;

		$this->_currentObject = unserialize($cellData['value']);
        //    Re-attach this as the cell's parent
        $this->_currentObject->attach($this);

		//	Return requested entry
		return $this->_currentObject;
	}	//	function getCacheData()


	/**
	 *	Is a value set for an indexed cell?
	 *
	 * @param	string		$pCoord		Coordinate address of the cell to check
	 * @return	boolean
	 */
	public function isDataSet($pCoord) {
		if ($pCoord === $this->_currentObjectID) {
			return TRUE;
		}

		//	Check if the requested entry exists in the cache
		$this->_selectQuery->bindValue('id',$pCoord,SQLITE3_TEXT);
		$cellResult = $this->_selectQuery->execute();
		if ($cellResult === FALSE) {
			throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
		}
		$cellData = $cellResult->fetchArray(SQLITE3_ASSOC);

		return ($cellData === FALSE) ? FALSE : TRUE;
	}	//	function isDataSet()


    /**
     *	Delete a cell in cache identified by coordinate address
     *
     * @param	string			$pCoord		Coordinate address of the cell to delete
     * @throws	PHPExcel_Exception
     */
	public function deleteCacheData($pCoord) {
		if ($pCoord === $this->_currentObjectID) {
			$this->_currentObject->detach();
			$this->_currentObjectID = $this->_currentObject = NULL;
		}

		//	Check if the requested entry exists in the cache
		$this->_deleteQuery->bindValue('id',$pCoord,SQLITE3_TEXT);
		$result = $this->_deleteQuery->execute();
		if ($result === FALSE)
			throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());

		$this->_currentCellIsDirty = FALSE;
	}	//	function deleteCacheData()


	/**
	 * Move a cell object from one address to another
	 *
	 * @param	string		$fromAddress	Current address of the cell to move
	 * @param	string		$toAddress		Destination address of the cell to move
	 * @return	boolean
	 */
	public function moveCell($fromAddress, $toAddress) {
		if ($fromAddress === $this->_currentObjectID) {
			$this->_currentObjectID = $toAddress;
		}

		$this->_deleteQuery->bindValue('id',$toAddress,SQLITE3_TEXT);
		$result = $this->_deleteQuery->execute();
		if ($result === false)
			throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());

		$this->_updateQuery->bindValue('toid',$toAddress,SQLITE3_TEXT);
		$this->_updateQuery->bindValue('fromid',$fromAddress,SQLITE3_TEXT);
		$result = $this->_updateQuery->execute();
		if ($result === false)
			throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());

		return TRUE;
	}	//	function moveCell()


	/**
	 * Get a list of all cell addresses currently held in cache
	 *
	 * @return	array of string
	 */
	public function getCellList() {
		if ($this->_currentObjectID !== null) {
			$this->_storeData();
		}

		$query = "SELECT id FROM kvp_".$this->_TableName;
		$cellIdsResult = $this->_DBHandle->query($query);
		if ($cellIdsResult === false)
			throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());

		$cellKeys = array();
		while ($row = $cellIdsResult->fetchArray(SQLITE3_ASSOC)) {
			$cellKeys[] = $row['id'];
		}

		return $cellKeys;
	}	//	function getCellList()


	/**
	 * Clone the cell collection
	 *
	 * @param	PHPExcel_Worksheet	$parent		The new worksheet
	 * @return	void
	 */
	public function copyCellCollection(PHPExcel_Worksheet $parent) {
		$this->_currentCellIsDirty;
        $this->_storeData();

		//	Get a new id for the new table name
		$tableName = str_replace('.','_',$this->_getUniqueID());
		if (!$this->_DBHandle->exec('CREATE TABLE kvp_'.$tableName.' (id VARCHAR(12) PRIMARY KEY, value BLOB)
		                                       AS SELECT * FROM kvp_'.$this->_TableName))
			throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());

		//	Copy the existing cell cache file
		$this->_TableName = $tableName;
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
		//	detach ourself from the worksheet, so that it can then delete this object successfully
		$this->_parent = null;

		//	Close down the temporary cache file
		$this->__destruct();
	}	//	function unsetWorksheetCells()


	/**
	 * Initialise this new cell collection
	 *
	 * @param	PHPExcel_Worksheet	$parent		The worksheet for this cell collection
	 */
	public function __construct(PHPExcel_Worksheet $parent) {
		parent::__construct($parent);
		if (is_null($this->_DBHandle)) {
			$this->_TableName = str_replace('.','_',$this->_getUniqueID());
			$_DBName = ':memory:';

			$this->_DBHandle = new SQLite3($_DBName);
			if ($this->_DBHandle === false)
				throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
			if (!$this->_DBHandle->exec('CREATE TABLE kvp_'.$this->_TableName.' (id VARCHAR(12) PRIMARY KEY, value BLOB)'))
				throw new PHPExcel_Exception($this->_DBHandle->lastErrorMsg());
		}

		$this->_selectQuery = $this->_DBHandle->prepare("SELECT value FROM kvp_".$this->_TableName." WHERE id = :id");
		$this->_insertQuery = $this->_DBHandle->prepare("INSERT OR REPLACE INTO kvp_".$this->_TableName." VALUES(:id,:data)");
		$this->_updateQuery = $this->_DBHandle->prepare("UPDATE kvp_".$this->_TableName." SET id=:toId WHERE id=:fromId");
		$this->_deleteQuery = $this->_DBHandle->prepare("DELETE FROM kvp_".$this->_TableName." WHERE id = :id");
	}	//	function __construct()


	/**
	 * Destroy this cell collection
	 */
	public function __destruct() {
		if (!is_null($this->_DBHandle)) {
			$this->_DBHandle->exec('DROP TABLE kvp_'.$this->_TableName);
			$this->_DBHandle->close();
		}
		$this->_DBHandle = null;
	}	//	function __destruct()


	/**
	 * Identify whether the caching method is currently available
	 * Some methods are dependent on the availability of certain extensions being enabled in the PHP build
	 *
	 * @return	boolean
	 */
	public static function cacheMethodIsAvailable() {
		if (!class_exists('SQLite3',FALSE)) {
			return false;
		}

		return true;
	}

}
