<?php
/////////////////////////////////////////////////////////////////////////////////
/// getID3() by James Heinrich <info@getid3.org>                               //
//  available at http://getid3.sourceforge.net                                 //
//            or http://www.getid3.org                                         //
//          also https://github.com/JamesHeinrich/getID3                       //
/////////////////////////////////////////////////////////////////////////////////
///                                                                            //
// extension.cache.sqlite3.php - part of getID3()                              //
// Please see readme.txt for more information                                  //
//                                                                            ///
/////////////////////////////////////////////////////////////////////////////////
///                                                                            //
// MySQL extension written by Allan Hansen <ahØartemis*dk>                     //
// Table name mod by Carlo Capocasa <calroØcarlocapocasa*com>                  //
// MySQL extension was reworked for SQLite3 by Karl G. Holz <newaeonØmac*com>  //
//                                                                            ///
/////////////////////////////////////////////////////////////////////////////////
/**
* This is a caching extension for getID3(). It works the exact same
* way as the getID3 class, but return cached information much faster
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
*       require_once 'getid3/extension.cache.sqlite3.php';
*       // all parameters are optional, defaults are:
*       $getID3 = new getID3_cached_sqlite3($table='getid3_cache', $hide=FALSE);
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
*   sqlite3             table='getid3_cache', hide=false        (PHP5)
*

***  database file will be stored in the same directory as this script,
***  webserver must have write access to that directory!
***  set $hide to TRUE to prefix db file with .ht to pervent access from web client
***  this is a default setting in the Apache configuration:

# The following lines prevent .htaccess and .htpasswd files from being viewed by Web clients.

<Files ~ "^\.ht">
    Order allow,deny
    Deny from all
    Satisfy all
</Files>

********************************************************************************
*
*   -------------------------------------------------------------------
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
* Recommended Cache Types
*
*   Infrequent updates, many reads      any DBM
*   Frequent updates                    mysql
********************************************************************************
*
* IMHO this is still a bit slow, I'm using this with MP4/MOV/ M4v files
* there is a plan to add directory scanning and analyzing to make things work much faster
*
*
*/
class getID3_cached_sqlite3 extends getID3 {

	/**
	* __construct()
	* @param string $table holds name of sqlite table
	* @return type
	*/
	public function __construct($table='getid3_cache', $hide=false) {
		$this->table = $table; // Set table
		$file = dirname(__FILE__).'/'.basename(__FILE__, 'php').'sqlite';
		if ($hide) {
			$file = dirname(__FILE__).'/.ht.'.basename(__FILE__, 'php').'sqlite';
		}
		$this->db = new SQLite3($file);
		$db = $this->db;
		$this->create_table();   // Create cache table if not exists
		$version = '';
		$sql = $this->version_check;
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':filename', getID3::VERSION, SQLITE3_TEXT);
		$result = $stmt->execute();
		list($version) = $result->fetchArray();
		if ($version != getID3::VERSION) { // Check version number and clear cache if changed
			$this->clear_cache();
		}
		return parent::__construct();
	}

	/**
	* close the database connection
	*/
	public function __destruct() {
		$db=$this->db;
		$db->close();
	}

	/**
	* hold the sqlite db
	* @var SQLite Resource
	*/
	private $db;

	/**
	* table to use for caching
	* @var string $table
	*/
	private $table;

	/**
	* clear the cache
	* @access private
	* @return type
	*/
	private function clear_cache() {
		$db = $this->db;
		$sql = $this->delete_cache;
		$db->exec($sql);
		$sql = $this->set_version;
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':filename', getID3::VERSION, SQLITE3_TEXT);
		$stmt->bindValue(':dirname', getID3::VERSION, SQLITE3_TEXT);
		$stmt->bindValue(':val', getID3::VERSION, SQLITE3_TEXT);
		return $stmt->execute();
	}

	/**
	* analyze file and cache them, if cached pull from the db
	* @param type $filename
	* @return boolean
	*/
	public function analyze($filename) {
		if (!file_exists($filename)) {
			return false;
		}
		// items to track for caching
		$filetime = filemtime($filename);
		$filesize = filesize($filename);
		// this will be saved for a quick directory lookup of analized files
		// ... why do 50 seperate sql quries when you can do 1 for the same result
		$dirname  = dirname($filename);
		// Lookup file
		$db = $this->db;
		$sql = $this->get_id3_data;
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':filename', $filename, SQLITE3_TEXT);
		$stmt->bindValue(':filesize', $filesize, SQLITE3_INTEGER);
		$stmt->bindValue(':filetime', $filetime, SQLITE3_INTEGER);
		$res = $stmt->execute();
		list($result) = $res->fetchArray();
		if (count($result) > 0 ) {
			return unserialize(base64_decode($result));
		}
		// if it hasn't been analyzed before, then do it now
		$analysis = parent::analyze($filename);
		// Save result
		$sql = $this->cache_file;
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':filename', $filename, SQLITE3_TEXT);
		$stmt->bindValue(':dirname', $dirname, SQLITE3_TEXT);
		$stmt->bindValue(':filesize', $filesize, SQLITE3_INTEGER);
		$stmt->bindValue(':filetime', $filetime, SQLITE3_INTEGER);
		$stmt->bindValue(':atime', time(), SQLITE3_INTEGER);
		$stmt->bindValue(':val', base64_encode(serialize($analysis)), SQLITE3_TEXT);
		$res = $stmt->execute();
		return $analysis;
	}

	/**
	* create data base table
	* this is almost the same as MySQL, with the exception of the dirname being added
	* @return type
	*/
	private function create_table() {
		$db = $this->db;
		$sql = $this->make_table;
		return $db->exec($sql);
	}

	/**
	* get cached directory
	*
	* This function is not in the MySQL extention, it's ment to speed up requesting multiple files
	* which is ideal for podcasting, playlists, etc.
	*
	* @access public
	* @param string $dir directory to search the cache database for
	* @return array return an array of matching id3 data
	*/
	public function get_cached_dir($dir) {
		$db = $this->db;
		$rows = array();
		$sql = $this->get_cached_dir;
		$stmt = $db->prepare($sql);
		$stmt->bindValue(':dirname', $dir, SQLITE3_TEXT);
		$res = $stmt->execute();
		while ($row=$res->fetchArray()) {
			$rows[] = unserialize(base64_decode($row));
		}
		return $rows;
	}

	/**
	* use the magical __get() for sql queries
	*
	* access as easy as $this->{case name}, returns NULL if query is not found
	*/
	public function __get($name) {
		switch($name) {
			case 'version_check':
				return "SELECT val FROM $this->table WHERE filename = :filename AND filesize = '-1' AND filetime = '-1' AND analyzetime = '-1'";
				break;
			case 'delete_cache':
				return "DELETE FROM $this->table";
				break;
			case 'set_version':
				return "INSERT INTO $this->table (filename, dirname, filesize, filetime, analyzetime, val) VALUES (:filename, :dirname, -1, -1, -1, :val)";
				break;
			case 'get_id3_data':
				return "SELECT val FROM $this->table WHERE filename = :filename AND filesize = :filesize AND filetime = :filetime";
				break;
			case 'cache_file':
				return "INSERT INTO $this->table (filename, dirname, filesize, filetime, analyzetime, val) VALUES (:filename, :dirname, :filesize, :filetime, :atime, :val)";
				break;
			case 'make_table':
				return "CREATE TABLE IF NOT EXISTS $this->table (filename VARCHAR(255) NOT NULL DEFAULT '', dirname VARCHAR(255) NOT NULL DEFAULT '', filesize INT(11) NOT NULL DEFAULT '0', filetime INT(11) NOT NULL DEFAULT '0', analyzetime INT(11) NOT NULL DEFAULT '0', val text not null, PRIMARY KEY (filename, filesize, filetime))";
				break;
			case 'get_cached_dir':
				return "SELECT val FROM $this->table WHERE dirname = :dirname";
				break;
		}
		return null;
	}

}
