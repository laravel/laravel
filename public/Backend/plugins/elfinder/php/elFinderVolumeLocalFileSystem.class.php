<?php

/**
 * elFinder driver for local filesystem.
 *
 * @author Dmitry (dio) Levashov
 * @author Troex Nevelin
 **/
class elFinderVolumeLocalFileSystem extends elFinderVolumeDriver {
	
	/**
	 * Driver id
	 * Must be started from letter and contains [a-z0-9]
	 * Used as part of volume id
	 *
	 * @var string
	 **/
	protected $driverId = 'l';
	
	/**
	 * Required to count total archive files size
	 *
	 * @var int
	 **/
	protected $archiveSize = 0;
	
	/**
	 * Constructor
	 * Extend options with required fields
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function __construct() {
		$this->options['alias']    = '';              // alias to replace root dir name
		$this->options['dirMode']  = 0755;            // new dirs mode
		$this->options['fileMode'] = 0644;            // new files mode
		$this->options['quarantine'] = '.quarantine';  // quarantine folder name - required to check archive (must be hidden)
		$this->options['maxArcFilesSize'] = 0;        // max allowed archive files size (0 - no limit)
	}
	
	/*********************************************************************/
	/*                        INIT AND CONFIGURE                         */
	/*********************************************************************/
	
	/**
	 * Configure after successfull mount.
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function configure() {
		$this->aroot = realpath($this->root);
		$root = $this->stat($this->root);
		
		if ($this->options['quarantine']) {
			$this->attributes[] = array(
				'pattern' => '~^'.preg_quote(DIRECTORY_SEPARATOR.$this->options['quarantine']).'$~',
				'read'    => false,
				'write'   => false,
				'locked'  => true,
				'hidden'  => true
			);
		}
		
		// chek thumbnails path
		if ($this->options['tmbPath']) {
			$this->options['tmbPath'] = strpos($this->options['tmbPath'], DIRECTORY_SEPARATOR) === false
				// tmb path set as dirname under root dir
				? $this->root.DIRECTORY_SEPARATOR.$this->options['tmbPath']
				// tmb path as full path
				: $this->_normpath($this->options['tmbPath']);
		}

		parent::configure();
		
		// if no thumbnails url - try detect it
		if ($root['read'] && !$this->tmbURL && $this->URL) {
			if (strpos($this->tmbPath, $this->root) === 0) {
				$this->tmbURL = $this->URL.str_replace(DIRECTORY_SEPARATOR, '/', substr($this->tmbPath, strlen($this->root)+1));
				if (preg_match("|[^/?&=]$|", $this->tmbURL)) {
					$this->tmbURL .= '/';
				}
			}
		}

		// check quarantine dir
		if (!empty($this->options['quarantine'])) {
			$this->quarantine = $this->root.DIRECTORY_SEPARATOR.$this->options['quarantine'];
			if ((!is_dir($this->quarantine) && !$this->_mkdir($this->root, $this->options['quarantine'])) || !is_writable($this->quarantine)) {
				$this->archivers['extract'] = array();
				$this->disabled[] = 'extract';
			}
		} else {
			$this->archivers['extract'] = array();
			$this->disabled[] = 'extract';
		}
		
	}
	
	/*********************************************************************/
	/*                               FS API                              */
	/*********************************************************************/

	/*********************** paths/urls *************************/
	
	/**
	 * Return parent directory path
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _dirname($path) {
		return dirname($path);
	}

	/**
	 * Return file name
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _basename($path) {
		return basename($path);
	}

	/**
	 * Join dir name and file name and retur full path
	 *
	 * @param  string  $dir
	 * @param  string  $name
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _joinPath($dir, $name) {
		return $dir.DIRECTORY_SEPARATOR.$name;
	}
	
	/**
	 * Return normalized path, this works the same as os.path.normpath() in Python
	 *
	 * @param  string  $path  path
	 * @return string
	 * @author Troex Nevelin
	 **/
	protected function _normpath($path) {
		if (empty($path)) {
			return '.';
		}

		if (strpos($path, '/') === 0) {
			$initial_slashes = true;
		} else {
			$initial_slashes = false;
		}
			
		if (($initial_slashes) 
		&& (strpos($path, '//') === 0) 
		&& (strpos($path, '///') === false)) {
			$initial_slashes = 2;
		}
			
		$initial_slashes = (int) $initial_slashes;

		$comps = explode('/', $path);
		$new_comps = array();
		foreach ($comps as $comp) {
			if (in_array($comp, array('', '.'))) {
				continue;
			}
				
			if (($comp != '..') 
			|| (!$initial_slashes && !$new_comps) 
			|| ($new_comps && (end($new_comps) == '..'))) {
				array_push($new_comps, $comp);
			} elseif ($new_comps) {
				array_pop($new_comps);
			}
		}
		$comps = $new_comps;
		$path = implode('/', $comps);
		if ($initial_slashes) {
			$path = str_repeat('/', $initial_slashes) . $path;
		}
		
		return $path ? $path : '.';
	}
	
	/**
	 * Return file path related to root dir
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _relpath($path) {
		return $path == $this->root ? '' : substr($path, strlen($this->root)+1);
	}
	
	/**
	 * Convert path related to root dir into real path
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _abspath($path) {
		return $path == DIRECTORY_SEPARATOR ? $this->root : $this->root.DIRECTORY_SEPARATOR.$path;
	}
	
	/**
	 * Return fake path started from root dir
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _path($path) {
		return $this->rootName.($path == $this->root ? '' : $this->separator.$this->_relpath($path));
	}
	
	/**
	 * Return true if $path is children of $parent
	 *
	 * @param  string  $path    path to check
	 * @param  string  $parent  parent path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _inpath($path, $parent) {
		return $path == $parent || strpos($path, $parent.DIRECTORY_SEPARATOR) === 0;
	}
	
	
	
	/***************** file stat ********************/

	/**
	 * Return stat for given path.
	 * Stat contains following fields:
	 * - (int)    size    file size in b. required
	 * - (int)    ts      file modification time in unix time. required
	 * - (string) mime    mimetype. required for folders, others - optionally
	 * - (bool)   read    read permissions. required
	 * - (bool)   write   write permissions. required
	 * - (bool)   locked  is object locked. optionally
	 * - (bool)   hidden  is object hidden. optionally
	 * - (string) alias   for symlinks - link target path relative to root path. optionally
	 * - (string) target  for symlinks - link target path. optionally
	 *
	 * If file does not exists - returns empty array or false.
	 *
	 * @param  string  $path    file path 
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _stat($path) {
		$stat = array();

		if (!file_exists($path)) {
			return $stat;
		}

		if ($path != $this->root && is_link($path)) {
			if (($target = $this->readlink($path)) == false 
			|| $target == $path) {
				$stat['mime']  = 'symlink-broken';
				$stat['read']  = false;
				$stat['write'] = false;
				$stat['size']  = 0;
				return $stat;
			}
			$stat['alias']  = $this->_path($target);
			$stat['target'] = $target;
			$path  = $target;
			$lstat = lstat($path);
			$size  = $lstat['size'];
		} else {
			$size = @filesize($path);
		}
		
		$dir = is_dir($path);
		
		$stat['mime']  = $dir ? 'directory' : $this->mimetype($path);
		$stat['ts']    = filemtime($path);
		$stat['read']  = is_readable($path);
		$stat['write'] = is_writable($path);
		if ($stat['read']) {
			$stat['size'] = $dir ? 0 : $size;
		}
		
		return $stat;
	}
	

	/**
	 * Return true if path is dir and has at least one childs directory
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _subdirs($path) {

		if (($dir = dir($path))) {
			$dir = dir($path);
			while (($entry = $dir->read()) !== false) {
				$p = $dir->path.DIRECTORY_SEPARATOR.$entry;
				if ($entry != '.' && $entry != '..' && is_dir($p) && !$this->attr($p, 'hidden')) {
					$dir->close();
					return true;
				}
			}
			$dir->close();
		}
		return false;
	}
	
	/**
	 * Return object width and height
	 * Ususaly used for images, but can be realize for video etc...
	 *
	 * @param  string  $path  file path
	 * @param  string  $mime  file mime type
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _dimensions($path, $mime) {
		clearstatcache();
		return strpos($mime, 'image') === 0 && ($s = @getimagesize($path)) !== false 
			? $s[0].'x'.$s[1] 
			: false;
	}
	/******************** file/dir content *********************/
	
	/**
	 * Return symlink target file
	 *
	 * @param  string  $path  link path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function readlink($path) {
		if (!($target = @readlink($path))) {
			return false;
		}
		
		if (substr($target, 0, 1) != DIRECTORY_SEPARATOR) {
			$target = dirname($path).DIRECTORY_SEPARATOR.$target;
		}
		
		$atarget = realpath($target);
		
		if (!$atarget) {
			return false;
		}
		
		$root  = $this->root;
		$aroot = $this->aroot;

		if ($this->_inpath($atarget, $this->aroot)) {
			return $this->_normpath($this->root.DIRECTORY_SEPARATOR.substr($atarget, strlen($this->aroot)+1));
		}

		return false;
	}
		
	/**
	 * Return files list in directory.
	 *
	 * @param  string  $path  dir path
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _scandir($path) {
		$files = array();
		
		foreach (scandir($path) as $name) {
			if ($name != '.' && $name != '..') {
				$files[] = $path.DIRECTORY_SEPARATOR.$name;
			}
		}
		return $files;
	}
		
	/**
	 * Open file and return file pointer
	 *
	 * @param  string  $path  file path
	 * @param  bool    $write open file for writing
	 * @return resource|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _fopen($path, $mode='rb') {
		return @fopen($path, 'r');
	}
	
	/**
	 * Close opened file
	 *
	 * @param  resource  $fp  file pointer
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _fclose($fp, $path='') {
		return @fclose($fp);
	}
	
	/********************  file/dir manipulations *************************/
	
	/**
	 * Create dir and return created dir path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new directory name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _mkdir($path, $name) {
		$path = $path.DIRECTORY_SEPARATOR.$name;

		if (@mkdir($path)) {
			@chmod($path, $this->options['dirMode']);
			return $path;
		}

		return false;
	}
	
	/**
	 * Create file and return it's path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new file name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _mkfile($path, $name) {
		$path = $path.DIRECTORY_SEPARATOR.$name;
		
		if (($fp = @fopen($path, 'w'))) {
			@fclose($fp);
			@chmod($path, $this->options['fileMode']);
			return $path;
		}
		return false;
	}
	
	/**
	 * Create symlink
	 *
	 * @param  string  $source     file to link to
	 * @param  string  $targetDir  folder to create link in
	 * @param  string  $name       symlink name
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _symlink($source, $targetDir, $name) {
		return @symlink($source, $targetDir.DIRECTORY_SEPARATOR.$name);
	}
	
	/**
	 * Copy file into another file
	 *
	 * @param  string  $source     source file path
	 * @param  string  $targetDir  target directory path
	 * @param  string  $name       new file name
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _copy($source, $targetDir, $name) {
		return copy($source, $targetDir.DIRECTORY_SEPARATOR.$name);
	}
	
	/**
	 * Move file into another parent dir.
	 * Return new file path or false.
	 *
	 * @param  string  $source  source file path
	 * @param  string  $target  target dir path
	 * @param  string  $name    file name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _move($source, $targetDir, $name) {
		$target = $targetDir.DIRECTORY_SEPARATOR.$name;
		return @rename($source, $target) ? $target : false;
	}
		
	/**
	 * Remove file
	 *
	 * @param  string  $path  file path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _unlink($path) {
		return @unlink($path);
	}

	/**
	 * Remove dir
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _rmdir($path) {
		return @rmdir($path);
	}
	
	/**
	 * Create new file and write into it from file pointer.
	 * Return new file path or false on error.
	 *
	 * @param  resource  $fp   file pointer
	 * @param  string    $dir  target dir path
	 * @param  string    $name file name
	 * @return bool|string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _save($fp, $dir, $name, $mime, $w, $h) {
		$path = $dir.DIRECTORY_SEPARATOR.$name;

		if (!($target = @fopen($path, 'wb'))) {
			return false;
		}

		while (!feof($fp)) {
			fwrite($target, fread($fp, 8192));
		}
		fclose($target);
		@chmod($path, $this->options['fileMode']);
		clearstatcache();
		return $path;
	}
	
	/**
	 * Get file contents
	 *
	 * @param  string  $path  file path
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _getContents($path) {
		return file_get_contents($path);
	}
	
	/**
	 * Write a string to a file
	 *
	 * @param  string  $path     file path
	 * @param  string  $content  new file content
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _filePutContents($path, $content) {
		if (@file_put_contents($path, $content, LOCK_EX) !== false) {
			clearstatcache();
			return true;
		}
		return false;
	}

	/**
	 * Detect available archivers
	 *
	 * @return void
	 **/
	protected function _checkArchivers() {
		if (!function_exists('exec')) {
			$this->options['archivers'] = $this->options['archive'] = array();
			return;
		}
		$arcs = array(
			'create'  => array(),
			'extract' => array()
			);
		
		//exec('tar --version', $o, $ctar);
		$this->procExec('tar --version', $o, $ctar);

		if ($ctar == 0) {
			$arcs['create']['application/x-tar']  = array('cmd' => 'tar', 'argc' => '-cf', 'ext' => 'tar');
			$arcs['extract']['application/x-tar'] = array('cmd' => 'tar', 'argc' => '-xf', 'ext' => 'tar');
			//$test = exec('gzip --version', $o, $c);
			unset($o);
			$test = $this->procExec('gzip --version', $o, $c);

			if ($c == 0) {
				$arcs['create']['application/x-gzip']  = array('cmd' => 'tar', 'argc' => '-czf', 'ext' => 'tgz');
				$arcs['extract']['application/x-gzip'] = array('cmd' => 'tar', 'argc' => '-xzf', 'ext' => 'tgz');
			}
			unset($o);
			//$test = exec('bzip2 --version', $o, $c);
			$test = $this->procExec('bzip2 --version', $o, $c);
			if ($c == 0) {
				$arcs['create']['application/x-bzip2']  = array('cmd' => 'tar', 'argc' => '-cjf', 'ext' => 'tbz');
				$arcs['extract']['application/x-bzip2'] = array('cmd' => 'tar', 'argc' => '-xjf', 'ext' => 'tbz');
			}
		}
		unset($o);
		//exec('zip --version', $o, $c);
		$this->procExec('zip -v', $o, $c);
		if ($c == 0) {
			$arcs['create']['application/zip']  = array('cmd' => 'zip', 'argc' => '-r9', 'ext' => 'zip');
		}
		unset($o);
		$this->procExec('unzip --help', $o, $c);
		if ($c == 0) {
			$arcs['extract']['application/zip'] = array('cmd' => 'unzip', 'argc' => '',  'ext' => 'zip');
		} 
		unset($o);
		//exec('rar --version', $o, $c);
		$this->procExec('rar --version', $o, $c);
		if ($c == 0 || $c == 7) {
			$arcs['create']['application/x-rar']  = array('cmd' => 'rar', 'argc' => 'a -inul', 'ext' => 'rar');
			$arcs['extract']['application/x-rar'] = array('cmd' => 'rar', 'argc' => 'x -y',    'ext' => 'rar');
		} else {
			unset($o);
			//$test = exec('unrar', $o, $c);
			$test = $this->procExec('unrar', $o, $c);
			if ($c==0 || $c == 7) {
				$arcs['extract']['application/x-rar'] = array('cmd' => 'unrar', 'argc' => 'x -y', 'ext' => 'rar');
			}
		}
		unset($o);
		//exec('7za --help', $o, $c);
		$this->procExec('7za --help', $o, $c);
		if ($c == 0) {
			$arcs['create']['application/x-7z-compressed']  = array('cmd' => '7za', 'argc' => 'a', 'ext' => '7z');
			$arcs['extract']['application/x-7z-compressed'] = array('cmd' => '7za', 'argc' => 'e -y', 'ext' => '7z');
			
			if (empty($arcs['create']['application/x-gzip'])) {
				$arcs['create']['application/x-gzip'] = array('cmd' => '7za', 'argc' => 'a -tgzip', 'ext' => 'tar.gz');
			}
			if (empty($arcs['extract']['application/x-gzip'])) {
				$arcs['extract']['application/x-gzip'] = array('cmd' => '7za', 'argc' => 'e -tgzip -y', 'ext' => 'tar.gz');
			}
			if (empty($arcs['create']['application/x-bzip2'])) {
				$arcs['create']['application/x-bzip2'] = array('cmd' => '7za', 'argc' => 'a -tbzip2', 'ext' => 'tar.bz');
			}
			if (empty($arcs['extract']['application/x-bzip2'])) {
				$arcs['extract']['application/x-bzip2'] = array('cmd' => '7za', 'argc' => 'a -tbzip2 -y', 'ext' => 'tar.bz');
			}
			if (empty($arcs['create']['application/zip'])) {
				$arcs['create']['application/zip'] = array('cmd' => '7za', 'argc' => 'a -tzip -l', 'ext' => 'zip');
			}
			if (empty($arcs['extract']['application/zip'])) {
				$arcs['extract']['application/zip'] = array('cmd' => '7za', 'argc' => 'e -tzip -y', 'ext' => 'zip');
			}
			if (empty($arcs['create']['application/x-tar'])) {
				$arcs['create']['application/x-tar'] = array('cmd' => '7za', 'argc' => 'a -ttar -l', 'ext' => 'tar');
			}
			if (empty($arcs['extract']['application/x-tar'])) {
				$arcs['extract']['application/x-tar'] = array('cmd' => '7za', 'argc' => 'e -ttar -y', 'ext' => 'tar');
			}
		}
		
		$this->archivers = $arcs;
	}

	/**
	 * Unpack archive
	 *
	 * @param  string  $path  archive path
	 * @param  array   $arc   archiver command and arguments (same as in $this->archivers)
	 * @return void
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
	protected function _unpack($path, $arc) {
		$cwd = getcwd();
		$dir = $this->_dirname($path);
		chdir($dir);
		$cmd = $arc['cmd'].' '.$arc['argc'].' '.escapeshellarg($this->_basename($path));
		$this->procExec($cmd, $o, $c);
		chdir($cwd);
	}

	/**
	 * Recursive symlinks search
	 *
	 * @param  string  $path  file/dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function _findSymlinks($path) {
		if (is_link($path)) {
			return true;
		}
		
		if (is_dir($path)) {
			foreach (scandir($path) as $name) {
				if ($name != '.' && $name != '..') {
					$p = $path.DIRECTORY_SEPARATOR.$name;
					if (is_link($p)) {
						return true;
					}
					if (is_dir($p) && $this->_findSymlinks($p)) {
						return true;
					} elseif (is_file($p)) {
						$this->archiveSize += filesize($p);
					}
				}
			}
		} else {
			$this->archiveSize += filesize($path);
		}
		
		return false;
	}

	/**
	 * Extract files from archive
	 *
	 * @param  string  $path  archive path
	 * @param  array   $arc   archiver command and arguments (same as in $this->archivers)
	 * @return true
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	protected function _extract($path, $arc) {
		
		if ($this->quarantine) {
			$dir     = $this->quarantine.DIRECTORY_SEPARATOR.str_replace(' ', '_', microtime()).basename($path);
			$archive = $dir.DIRECTORY_SEPARATOR.basename($path);
			
			if (!@mkdir($dir)) {
				return false;
			}
			
			chmod($dir, 0777);
			
			// copy in quarantine
			if (!copy($path, $archive)) {
				return false;
			}
			
			// extract in quarantine
			$this->_unpack($archive, $arc);
			@unlink($archive);
			
			// get files list
			$ls = array();
			foreach (scandir($dir) as $i => $name) {
				if ($name != '.' && $name != '..') {
					$ls[] = $name;
				}
			}
			
			// no files - extract error ?
			if (empty($ls)) {
				return false;
			}
			
			$this->archiveSize = 0;
			
			// find symlinks
			$symlinks = $this->_findSymlinks($dir);
			// remove arc copy
			$this->remove($dir);
			
			if ($symlinks) {
				return $this->setError(elFinder::ERROR_ARC_SYMLINKS);
			}

			// check max files size
			if ($this->options['maxArcFilesSize'] > 0 && $this->options['maxArcFilesSize'] < $this->archiveSize) {
				return $this->setError(elFinder::ERROR_ARC_MAXSIZE);
			}
			
			
			
			// archive contains one item - extract in archive dir
			if (count($ls) == 1) {
				$this->_unpack($path, $arc);
				$result = dirname($path).DIRECTORY_SEPARATOR.$ls[0];
				

			} else {
				// for several files - create new directory
				// create unique name for directory
				$name = basename($path);
				if (preg_match('/\.((tar\.(gz|bz|bz2|z|lzo))|cpio\.gz|ps\.gz|xcf\.(gz|bz2)|[a-z0-9]{1,4})$/i', $name, $m)) {
					$name = substr($name, 0,  strlen($name)-strlen($m[0]));
				}
				$test = dirname($path).DIRECTORY_SEPARATOR.$name;
				if (file_exists($test) || is_link($test)) {
					$name = $this->uniqueName(dirname($path), $name, '-', false);
				}
				
				$result  = dirname($path).DIRECTORY_SEPARATOR.$name;
				$archive = $result.DIRECTORY_SEPARATOR.basename($path);

				if (!$this->_mkdir(dirname($path), $name) || !copy($path, $archive)) {
					return false;
				}
				
				$this->_unpack($archive, $arc);
				@unlink($archive);
			}
			
			return file_exists($result) ? $result : false;
		}
	}
	
	/**
	 * Create archive and return its path
	 *
	 * @param  string  $dir    target dir
	 * @param  array   $files  files names list
	 * @param  string  $name   archive name
	 * @param  array   $arc    archiver options
	 * @return string|bool
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	protected function _archive($dir, $files, $name, $arc) {
		$cwd = getcwd();
		chdir($dir);
		
		$files = array_map('escapeshellarg', $files);
		
		$cmd = $arc['cmd'].' '.$arc['argc'].' '.escapeshellarg($name).' '.implode(' ', $files);
		$this->procExec($cmd, $o, $c);
		chdir($cwd);

		$path = $dir.DIRECTORY_SEPARATOR.$name;
		return file_exists($path) ? $path : false;
	}
	
} // END class 
