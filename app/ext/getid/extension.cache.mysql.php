<?php
/////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>               //
//  available at http://getid3.sourceforge.net                 //
//            or http://www.getid3.org                         //
//          also https://github.com/JamesHeinrich/getID3       //
/////////////////////////////////////////////////////////////////
//                                                             //
// extension.cache.mysql.php - part of getID3()                //
// Please see readme.txt for more information                  //
//                                                            ///
/////////////////////////////////////////////////////////////////
//                                                             //
// This extension written by Allan Hansen <ahØartemis*dk>      //
// Table name mod by Carlo Capocasa <calroØcarlocapocasa*com>  //
//                                                            ///
/////////////////////////////////////////////////////////////////


/**
* This is a caching extension for getID3(). It works the exact same
* way as the getID3 class, but return cached information very fast
*
* Example:  (see also demo.cache.mysql.php in /demo/)
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
*       require_once 'getid3/getid3/extension.cache.mysql.php';
*       // 5th parameter (tablename) is optional, default is 'getid3_cache'
*       $getID3 = new getID3_cached_mysql('localhost', 'database', 'username', 'password', 'tablename');
*       $getID3->encoding = 'UTF-8';
*       $info1 = $getID3->analyze('file1.flac');
*       $info2 = $getID3->analyze('file2.wv');
*
*
* Supported Cache Types    (this extension)
*
*   SQL Databases:
*
*   cache_type          cache_options
*   -------------------------------------------------------------------
*   mysql               host, database, username, password
*
*
*   DBM-Style Databases:    (use extension.cache.dbm)
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


class getID3_cached_mysql extends getID3
{

	// private vars
	private $cursor;
	private $connection;


	// public: constructor - see top of this file for cache type and cache_options
	public function getID3_cached_mysql($host, $database, $username, $password, $table='getid3_cache') {

		// Check for mysql support
		if (!function_exists('mysql_pconnect')) {
			throw new Exception('PHP not compiled with mysql support.');
		}

		// Connect to database
		$this->connection = mysql_pconnect($host, $username, $password);
		if (!$this->connection) {
			throw new Exception('mysql_pconnect() failed - check permissions and spelling.');
		}

		// Select database
		if (!mysql_select_db($database, $this->connection)) {
			throw new Exception('Cannot use database '.$database);
		}

		// Set table
		$this->table = $table;

		// Create cache table if not exists
		$this->create_table();

		// Check version number and clear cache if changed
		$version = '';
		$SQLquery  = 'SELECT `value`';
		$SQLquery .= ' FROM `'.mysql_real_escape_string($this->table).'`';
		$SQLquery .= ' WHERE (`filename` = \''.mysql_real_escape_string(getID3::VERSION).'\')';
		$SQLquery .= ' AND (`filesize` = -1)';
		$SQLquery .= ' AND (`filetime` = -1)';
		$SQLquery .= ' AND (`analyzetime` = -1)';
		if ($this->cursor = mysql_query($SQLquery, $this->connection)) {
			list($version) = mysql_fetch_array($this->cursor);
		}
		if ($version != getID3::VERSION) {
			$this->clear_cache();
		}

		parent::__construct();
	}



	// public: clear cache
	public function clear_cache() {

		$this->cursor = mysql_query('DELETE FROM `'.mysql_real_escape_string($this->table).'`', $this->connection);
		$this->cursor = mysql_query('INSERT INTO `'.mysql_real_escape_string($this->table).'` VALUES (\''.getID3::VERSION.'\', -1, -1, -1, \''.getID3::VERSION.'\')', $this->connection);
	}



	// public: analyze file
	public function analyze($filename) {

		if (file_exists($filename)) {

			// Short-hands
			$filetime = filemtime($filename);
			$filesize =  filesize($filename);

			// Lookup file
			$SQLquery  = 'SELECT `value`';
			$SQLquery .= ' FROM `'.mysql_real_escape_string($this->table).'`';
			$SQLquery .= ' WHERE (`filename` = \''.mysql_real_escape_string($filename).'\')';
			$SQLquery .= '   AND (`filesize` = \''.mysql_real_escape_string($filesize).'\')';
			$SQLquery .= '   AND (`filetime` = \''.mysql_real_escape_string($filetime).'\')';
			$this->cursor = mysql_query($SQLquery, $this->connection);
			if (mysql_num_rows($this->cursor) > 0) {
				// Hit
				list($result) = mysql_fetch_array($this->cursor);
				return unserialize(base64_decode($result));
			}
		}

		// Miss
		$analysis = parent::analyze($filename);

		// Save result
		if (file_exists($filename)) {
			$SQLquery  = 'INSERT INTO `'.mysql_real_escape_string($this->table).'` (`filename`, `filesize`, `filetime`, `analyzetime`, `value`) VALUES (';
			$SQLquery .=   '\''.mysql_real_escape_string($filename).'\'';
			$SQLquery .= ', \''.mysql_real_escape_string($filesize).'\'';
			$SQLquery .= ', \''.mysql_real_escape_string($filetime).'\'';
			$SQLquery .= ', \''.mysql_real_escape_string(time()   ).'\'';
			$SQLquery .= ', \''.mysql_real_escape_string(base64_encode(serialize($analysis))).'\')';
			$this->cursor = mysql_query($SQLquery, $this->connection);
		}
		return $analysis;
	}



	// private: (re)create sql table
	private function create_table($drop=false) {

		$SQLquery  = 'CREATE TABLE IF NOT EXISTS `'.mysql_real_escape_string($this->table).'` (';
		$SQLquery .=   '`filename` VARCHAR(255) NOT NULL DEFAULT \'\'';
		$SQLquery .= ', `filesize` INT(11) NOT NULL DEFAULT \'0\'';
		$SQLquery .= ', `filetime` INT(11) NOT NULL DEFAULT \'0\'';
		$SQLquery .= ', `analyzetime` INT(11) NOT NULL DEFAULT \'0\'';
		$SQLquery .= ', `value` TEXT NOT NULL';
		$SQLquery .= ', PRIMARY KEY (`filename`, `filesize`, `filetime`)) ENGINE=MyISAM';
		$this->cursor = mysql_query($SQLquery, $this->connection);
		echo mysql_error($this->connection);
	}
}
