<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
/////////////////////////////////////////////////////////////////
//                                                             //
// extension.cache.dbm.php - part of getID3()                  //
// Please see readme.txt for more information                  //
//                                                            ///
/////////////////////////////////////////////////////////////////
//                                                             //
// This extension written by Allan Hansen <ahØartemis*dk>      //
//                                                            ///
/////////////////////////////////////////////////////////////////


/**
* This is a caching extension for getID3(). It works the exact same
* way as the getID3 class, but return cached information very fast
*
* Example:
*
*    Normal getID3 usage (example):
*
*       require_once 'getid3/getid3.php';
*       $getID3 = new getID3;
*       $getID3->encoding = 'UTF-8';
*       $info1 = $getID3->analyze('file1.flac');
*       $info2 = $getID3->analyze('file2.wv');
*
*    getID3_cached usage:
*
*       require_once 'getid3/getid3.php';
*       require_once 'getid3/getid3/extension.cache.dbm.php';
*       $getID3 = new getID3_cached('db3', '/tmp/getid3_cache.dbm',
*                                          '/tmp/getid3_cache.lock');
*       $getID3->encoding = 'UTF-8';
*       $info1 = $getID3->analyze('file1.flac');
*       $info2 = $getID3->analyze('file2.wv');
*
*
* Supported Cache Types
*
*   SQL Databases:          (use extension.cache.mysql)
*
*   cache_type          cache_options
*   -------------------------------------------------------------------
*   mysql               host, database, username, password
*
*
*   DBM-Style Databases:    (this extension)
*
*   cache_type          cache_options
*   -------------------------------------------------------------------
*   gdbm                dbm_filename, lock_filename
*   ndbm                dbm_filename, lock_filename
*   db2                 dbm_filename, lock_filename
*   db3                 dbm_filename, lock_filename
*   db4                 dbm_filename, lock_filename  (PHP5 required)
*
*   PHP must have write access to both dbm_filename and lock_filename.
*
*
* Recommended Cache Types
*
*   Infrequent updates, many reads      any DBM
*   Frequent updates                    mysql
*/


class getID3_cached_dbm extends getID3
{

	// public: constructor - see top of this file for cache type and cache_options
	public function getID3_cached_dbm($cache_type, $dbm_filename, $lock_filename) {

		// Check for dba extension
		if (!extension_loaded('dba')) {
			throw new Exception('PHP is not compiled with dba support, required to use DBM style cache.');
		}

		// Check for specific dba driver
		if (!function_exists('dba_handlers') || !in_array($cache_type, dba_handlers())) {
			throw new Exception('PHP is not compiled --with '.$cache_type.' support, required to use DBM style cache.');
		}

		// Create lock file if needed
		if (!file_exists($lock_filename)) {
			if (!touch($lock_filename)) {
				throw new Exception('failed to create lock file: '.$lock_filename);
			}
		}

		// Open lock file for writing
		if (!is_writeable($lock_filename)) {
			throw new Exception('lock file: '.$lock_filename.' is not writable');
		}
		$this->lock = fopen($lock_filename, 'w');

		// Acquire exclusive write lock to lock file
		flock($this->lock, LOCK_EX);

		// Create dbm-file if needed
		if (!file_exists($dbm_filename)) {
			if (!touch($dbm_filename)) {
				throw new Exception('failed to create dbm file: '.$dbm_filename);
			}
		}

		// Try to open dbm file for writing
		$this->dba = dba_open($dbm_filename, 'w', $cache_type);
		if (!$this->dba) {

			// Failed - create new dbm file
			$this->dba = dba_open($dbm_filename, 'n', $cache_type);

			if (!$this->dba) {
				throw new Exception('failed to create dbm file: '.$dbm_filename);
			}

			// Insert getID3 version number
			dba_insert(getID3::VERSION, getID3::VERSION, $this->dba);
		}

		// Init misc values
		$this->cache_type   = $cache_type;
		$this->dbm_filename = $dbm_filename;

		// Register destructor
		register_shutdown_function(array($this, '__destruct'));

		// Check version number and clear cache if changed
		if (dba_fetch(getID3::VERSION, $this->dba) != getID3::VERSION) {
			$this->clear_cache();
		}

		parent::__construct();
	}



	// public: destructor
	public function __destruct() {

		// Close dbm file
		dba_close($this->dba);

		// Release exclusive lock
		flock($this->lock, LOCK_UN);

		// Close lock file
		fclose($this->lock);
	}



	// public: clear cache
	public function clear_cache() {

		// Close dbm file
		dba_close($this->dba);

		// Create new dbm file
		$this->dba = dba_open($this->dbm_filename, 'n', $this->cache_type);

		if (!$this->dba) {
			throw new Exception('failed to clear cache/recreate dbm file: '.$this->dbm_filename);
		}

		// Insert getID3 version number
		dba_insert(getID3::VERSION, getID3::VERSION, $this->dba);

		// Re-register shutdown function
		register_shutdown_function(array($this, '__destruct'));
	}



	// public: analyze file
	public function analyze($filename) {

		if (file_exists($filename)) {

			// Calc key     filename::mod_time::size    - should be unique
			$key = $filename.'::'.filemtime($filename).'::'.filesize($filename);

			// Loopup key
			$result = dba_fetch($key, $this->dba);

			// Hit
			if ($result !== false) {
				return unserialize($result);
			}
		}

		// Miss
		$result = parent::analyze($filename);

		// Save result
		if (file_exists($filename)) {
			dba_insert($key, serialize($result), $this->dba);
		}

		return $result;
	}

}
