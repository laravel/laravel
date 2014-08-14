<?php
/**
 * Base class for elFinder volume.
 * Provide 2 layers:
 *  1. Public API (commands)
 *  2. abstract fs API
 *
 * All abstract methods begin with "_"
 *
 * @author Dmitry (dio) Levashov
 * @author Troex Nevelin
 * @author Alexey Sukhotin
 **/
abstract class elFinderVolumeDriver {
	
	/**
	 * Driver id
	 * Must be started from letter and contains [a-z0-9]
	 * Used as part of volume id
	 *
	 * @var string
	 **/
	protected $driverId = 'a';
	
	/**
	 * Volume id - used as prefix for files hashes
	 *
	 * @var string
	 **/
	protected $id = '';
	
	/**
	 * Flag - volume "mounted" and available
	 *
	 * @var bool
	 **/
	protected $mounted = false;
	
	/**
	 * Root directory path
	 *
	 * @var string
	 **/
	protected $root = '';
	
	/**
	 * Root basename | alias
	 *
	 * @var string
	 **/
	protected $rootName = '';
	
	/**
	 * Default directory to open
	 *
	 * @var string
	 **/
	protected $startPath = '';
	
	/**
	 * Base URL
	 *
	 * @var string
	 **/
	protected $URL = '';
	
	/**
	 * Thumbnails dir path
	 *
	 * @var string
	 **/
	protected $tmbPath = '';
	
	/**
	 * Is thumbnails dir writable
	 *
	 * @var bool
	 **/
	protected $tmbPathWritable = false;
	
	/**
	 * Thumbnails base URL
	 *
	 * @var string
	 **/
	protected $tmbURL = '';
	
	/**
	 * Thumbnails size in px
	 *
	 * @var int
	 **/
	protected $tmbSize = 48;
	
	/**
	 * Image manipulation lib name
	 * auto|imagick|mogtify|gd
	 *
	 * @var string
	 **/
	protected $imgLib = 'auto';
	
	/**
	 * Library to crypt files name
	 *
	 * @var string
	 **/
	protected $cryptLib = '';
	
	/**
	 * Archivers config
	 *
	 * @var array
	 **/
	protected $archivers = array(
		'create'  => array(),
		'extract' => array()
	);
	
	/**
	 * How many subdirs levels return for tree
	 *
	 * @var int
	 **/
	protected $treeDeep = 1;
	
	/**
	 * Errors from last failed action
	 *
	 * @var array
	 **/
	protected $error = array();
	
	/**
	 * Today 24:00 timestamp
	 *
	 * @var int
	 **/
	protected $today = 0;
	
	/**
	 * Yesterday 24:00 timestamp
	 *
	 * @var int
	 **/
	protected $yesterday = 0;
	
	/**
	 * Object configuration
	 *
	 * @var array
	 **/
	protected $options = array(
		'id'              => '',
		// root directory path
		'path'            => '',
		// open this path on initial request instead of root path
		'startPath'       => '',
		// how many subdirs levels return per request
		'treeDeep'        => 1,
		// root url, not set to disable sending URL to client (replacement for old "fileURL" option)
		'URL'             => '',
		// directory separator. required by client to show paths correctly
		'separator'       => DIRECTORY_SEPARATOR,
		// library to crypt/uncrypt files names (not implemented)
		'cryptLib'        => '',
		// how to detect files mimetypes. (auto/internal/finfo/mime_content_type)
		'mimeDetect'      => 'auto',
		// mime.types file path (for mimeDetect==internal)
		'mimefile'        => '',
		// directory for thumbnails
		'tmbPath'         => '.tmb',
		// mode to create thumbnails dir
		'tmbPathMode'     => 0777,
		// thumbnails dir URL. Set it if store thumbnails outside root directory
		'tmbURL'          => '',
		// thumbnails size (px)
		'tmbSize'         => 48,
		// thumbnails crop (true - crop, false - scale image to fit thumbnail size)
		'tmbCrop'         => true,
		// thumbnails background color (hex #rrggbb or 'transparent')
		'tmbBgColor'      => '#ffffff',
		// image manipulations library
		'imgLib'          => 'auto',
		// on paste file -  if true - old file will be replaced with new one, if false new file get name - original_name-number.ext
		'copyOverwrite'   => true,
		// if true - join new and old directories content on paste
		'copyJoin'        => true,
		// on upload -  if true - old file will be replaced with new one, if false new file get name - original_name-number.ext
		'uploadOverwrite' => true,
		// mimetypes allowed to upload
		'uploadAllow'     => array(),
		// mimetypes not allowed to upload
		'uploadDeny'      => array(),
		// order to proccess uploadAllow and uploadDeny options
		'uploadOrder'     => array('deny', 'allow'),
		// maximum upload file size. NOTE - this is size for every uploaded files
		'uploadMaxSize'   => 0,
		// files dates format
		'dateFormat'      => 'j M Y H:i',
		// files time format
		'timeFormat'      => 'H:i',
		// if true - every folder will be check for children folders, otherwise all folders will be marked as having subfolders
		'checkSubfolders' => true,
		// allow to copy from this volume to other ones?
		'copyFrom'        => true,
		// allow to copy from other volumes to this one?
		'copyTo'          => true,
		// list of commands disabled on this root
		'disabled'        => array(),
		// regexp or function name to validate new file name
		'acceptedName'    => '/^\w[\w\s\.\%\-\(\)\[\]]*$/u',
		// function/class method to control files permissions
		'accessControl'   => null,
		// some data required by access control
		'accessControlData' => null,
		// default permissions. not set hidden/locked here - take no effect
		'defaults'     => array(
			'read'   => true,
			'write'  => true
		),
		// files attributes
		'attributes'   => array(),
		// Allowed archive's mimetypes to create. Leave empty for all available types.
		'archiveMimes' => array(),
		// Manual config for archivers. See example below. Leave empty for auto detect
		'archivers'    => array(),
		// required to fix bug on macos
		'utf8fix'      => false,
		 //                           й                 ё              Й               Ё              Ø         Å
		'utf8patterns' => array("\u0438\u0306", "\u0435\u0308", "\u0418\u0306", "\u0415\u0308", "\u00d8A", "\u030a"),
		'utf8replace'  => array("\u0439",        "\u0451",       "\u0419",       "\u0401",       "\u00d8", "\u00c5")
	);

	/**
	 * Defaults permissions
	 *
	 * @var array
	 **/
	protected $defaults = array(
		'read'   => true,
		'write'  => true,
		'locked' => false,
		'hidden' => false
	);
	
	/**
	 * Access control function/class
	 *
	 * @var mixed
	 **/
	protected $attributes = array();
	
	/**
	 * Access control function/class
	 *
	 * @var mixed
	 **/
	protected $access = null;
	
	/**
	 * Mime types allowed to upload
	 *
	 * @var array
	 **/
	protected $uploadAllow = array();
	
	/**
	 * Mime types denied to upload
	 *
	 * @var array
	 **/
	protected $uploadDeny = array();
	
	/**
	 * Order to validate uploadAllow and uploadDeny
	 *
	 * @var array
	 **/
	protected $uploadOrder = array();
	
	/**
	 * Maximum allowed upload file size.
	 * Set as number or string with unit - "10M", "500K", "1G"
	 *
	 * @var int|string
	 **/
	protected $uploadMaxSize = 0;
	
	/**
	 * Mimetype detect method
	 *
	 * @var string
	 **/
	protected $mimeDetect = 'auto';
	
	/**
	 * Flag - mimetypes from externail file was loaded
	 *
	 * @var bool
	 **/
	private static $mimetypesLoaded = false;
	
	/**
	 * Finfo object for mimeDetect == 'finfo'
	 *
	 * @var object
	 **/
	protected $finfo = null;
	
	/**
	 * List of disabled client's commands
	 *
	 * @var array
	 **/
	protected $diabled = array();
	
	/**
	 * default extensions/mimetypes for mimeDetect == 'internal' 
	 *
	 * @var array
	 **/
	protected static $mimetypes = array(
		// applications
		'ai'    => 'application/postscript',
		'eps'   => 'application/postscript',
		'exe'   => 'application/x-executable',
		'doc'   => 'application/vnd.ms-word',
		'xls'   => 'application/vnd.ms-excel',
		'ppt'   => 'application/vnd.ms-powerpoint',
		'pps'   => 'application/vnd.ms-powerpoint',
		'pdf'   => 'application/pdf',
		'xml'   => 'application/xml',
		'odt'   => 'application/vnd.oasis.opendocument.text',
		'swf'   => 'application/x-shockwave-flash',
		'torrent' => 'application/x-bittorrent',
		'jar'   => 'application/x-jar',
		// archives
		'gz'    => 'application/x-gzip',
		'tgz'   => 'application/x-gzip',
		'bz'    => 'application/x-bzip2',
		'bz2'   => 'application/x-bzip2',
		'tbz'   => 'application/x-bzip2',
		'zip'   => 'application/zip',
		'rar'   => 'application/x-rar',
		'tar'   => 'application/x-tar',
		'7z'    => 'application/x-7z-compressed',
		// texts
		'txt'   => 'text/plain',
		'php'   => 'text/x-php',
		'html'  => 'text/html',
		'htm'   => 'text/html',
		'js'    => 'text/javascript',
		'css'   => 'text/css',
		'rtf'   => 'text/rtf',
		'rtfd'  => 'text/rtfd',
		'py'    => 'text/x-python',
		'java'  => 'text/x-java-source',
		'rb'    => 'text/x-ruby',
		'sh'    => 'text/x-shellscript',
		'pl'    => 'text/x-perl',
		'xml'   => 'text/xml',
		'sql'   => 'text/x-sql',
		'c'     => 'text/x-csrc',
		'h'     => 'text/x-chdr',
		'cpp'   => 'text/x-c++src',
		'hh'    => 'text/x-c++hdr',
		'log'   => 'text/plain',
		'csv'   => 'text/x-comma-separated-values',
		// images
		'bmp'   => 'image/x-ms-bmp',
		'jpg'   => 'image/jpeg',
		'jpeg'  => 'image/jpeg',
		'gif'   => 'image/gif',
		'png'   => 'image/png',
		'tif'   => 'image/tiff',
		'tiff'  => 'image/tiff',
		'tga'   => 'image/x-targa',
		'psd'   => 'image/vnd.adobe.photoshop',
		'ai'    => 'image/vnd.adobe.photoshop',
		'xbm'   => 'image/xbm',
		'pxm'   => 'image/pxm',
		//audio
		'mp3'   => 'audio/mpeg',
		'mid'   => 'audio/midi',
		'ogg'   => 'audio/ogg',
		'oga'   => 'audio/ogg',
		'm4a'   => 'audio/x-m4a',
		'wav'   => 'audio/wav',
		'wma'   => 'audio/x-ms-wma',
		// video
		'avi'   => 'video/x-msvideo',
		'dv'    => 'video/x-dv',
		'mp4'   => 'video/mp4',
		'mpeg'  => 'video/mpeg',
		'mpg'   => 'video/mpeg',
		'mov'   => 'video/quicktime',
		'wm'    => 'video/x-ms-wmv',
		'flv'   => 'video/x-flv',
		'mkv'   => 'video/x-matroska',
		'webm'  => 'video/webm',
		'ogv'   => 'video/ogg',
		'ogm'   => 'video/ogg'
		);
	
	/**
	 * Directory separator - required by client
	 *
	 * @var string
	 **/
	protected $separator = DIRECTORY_SEPARATOR;
	
	/**
	 * Mimetypes allowed to display
	 *
	 * @var array
	 **/
	protected $onlyMimes = array();
	
	/**
	 * Store files moved or overwrited files info
	 *
	 * @var array
	 **/
	protected $removed = array();
	
	/**
	 * Cache storage
	 *
	 * @var array
	 **/
	protected $cache = array();
	
	/**
	 * Cache by folders
	 *
	 * @var array
	 **/
	protected $dirsCache = array();
	
	/*********************************************************************/
	/*                            INITIALIZATION                         */
	/*********************************************************************/
	
	/**
	 * Prepare driver before mount volume.
	 * Return true if volume is ready.
	 *
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function init() {
		return true;
	}	
		
	/**
	 * Configure after successfull mount.
	 * By default set thumbnails path and image manipulation library.
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function configure() {
		// set thumbnails path
		$path = $this->options['tmbPath'];
		if ($path) {
			if (!file_exists($path)) {
				if (@mkdir($path)) {
					chmod($path, $this->options['tmbPathMode']);
				} else {
					$path = '';
				}
			} 
			
			if (is_dir($path) && is_readable($path)) {
				$this->tmbPath = $path;
				$this->tmbPathWritable = is_writable($path);
			}
		}

		// set image manipulation library
		$type = preg_match('/^(imagick|gd|auto)$/i', $this->options['imgLib'])
			? strtolower($this->options['imgLib'])
			: 'auto';

		if (($type == 'imagick' || $type == 'auto') && extension_loaded('imagick')) {
			$this->imgLib = 'imagick';
		} else {
			$this->imgLib = function_exists('gd_info') ? 'gd' : '';
		}
		
	}
	
	
	/*********************************************************************/
	/*                              PUBLIC API                           */
	/*********************************************************************/
	
	/**
	 * Return driver id. Used as a part of volume id.
	 *
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	public function driverId() {
		return $this->driverId;
	}
	
	/**
	 * Return volume id
	 *
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	public function id() {
		return $this->id;
	}
		
	/**
	 * Return debug info for client
	 *
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function debug() {
		return array(
			'id'         => $this->id(),
			'name'       => strtolower(substr(get_class($this), strlen('elfinderdriver'))),
			'mimeDetect' => $this->mimeDetect,
			'imgLib'     => $this->imgLib
		);
	}
	
	/**
	 * "Mount" volume.
	 * Return true if volume available for read or write, 
	 * false - otherwise
	 *
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
	public function mount(array $opts) {
		if (!isset($opts['path']) || $opts['path'] === '') {
			return false;
		}
		
		$this->options = array_merge($this->options, $opts);
		$this->id = $this->driverId.(!empty($this->options['id']) ? $this->options['id'] : elFinder::$volumesCnt++).'_';
		$this->root = $this->_normpath($this->options['path']);
		$this->separator = isset($this->options['separator']) ? $this->options['separator'] : DIRECTORY_SEPARATOR;
		
		// default file attribute
		$this->defaults = array(
			'read'    => isset($this->options['defaults']['read'])  ? !!$this->options['defaults']['read']  : true,
			'write'   => isset($this->options['defaults']['write']) ? !!$this->options['defaults']['write'] : true,
			'locked'  => false,
			'hidden'  => false
		);

		// root attributes
		$this->attributes[] = array(
			'pattern' => '~^'.preg_quote(DIRECTORY_SEPARATOR).'$~',
			'locked'  => true,
			'hidden'  => false
		);
		// set files attributes
		if (!empty($this->options['attributes']) && is_array($this->options['attributes'])) {
			
			foreach ($this->options['attributes'] as $a) {
				// attributes must contain pattern and at least one rule
				if (!empty($a['pattern']) || count($a) > 1) {
					$this->attributes[] = $a;
				}
			}
		}

		if (!empty($this->options['accessControl'])) {
			if (is_string($this->options['accessControl']) 
			&& function_exists($this->options['accessControl'])) {
				$this->access = $this->options['accessControl'];
			} elseif (is_array($this->options['accessControl']) 
			&& count($this->options['accessControl']) > 1 
			&& is_object($this->options['accessControl'][0])
			&& method_exists($this->options['accessControl'][0], $this->options['accessControl'][1])) {
				$this->access = array($this->options['accessControl'][0], $this->options['accessControl'][1]);
			}
		}
		
		$this->today     = mktime(0,0,0, date('m'), date('d'), date('Y'));
		$this->yesterday = $this->today-86400;
		
		// debug($this->attributes);
		if (!$this->init()) {
			return false;
		}
		
		// check some options is arrays
		$this->uploadAllow = isset($this->options['uploadAllow']) && is_array($this->options['uploadAllow'])
			? $this->options['uploadAllow']
			: array();
			
		$this->uploadDeny = isset($this->options['uploadDeny']) && is_array($this->options['uploadDeny'])
			? $this->options['uploadDeny']
			: array();

		if (is_string($this->options['uploadOrder'])) { // telephat_mode on, compatibility with 1.x
			$parts = explode(',', isset($this->options['uploadOrder']) ? $this->options['uploadOrder'] : 'deny,allow');
			$this->uploadOrder = array(trim($parts[0]), trim($parts[1]));
		} else { // telephat_mode off
			$this->uploadOrder = $this->options['uploadOrder'];
		}
			
		if (!empty($this->options['uploadMaxSize'])) {
			$size = ''.$this->options['uploadMaxSize'];
			$unit = strtolower(substr($size, strlen($size) - 1));
			$n = 1;
			switch ($unit) {
				case 'k':
					$n = 1024;
					break;
				case 'm':
					$n = 1048576;
					break;
				case 'g':
					$n = 1073741824;
			}
			$this->uploadMaxSize = intval($size)*$n;
		}
			
		$this->disabled = isset($this->options['disabled']) && is_array($this->options['disabled'])
			? $this->options['disabled']
			: array();
		
		$this->cryptLib   = $this->options['cryptLib'];
		$this->mimeDetect = $this->options['mimeDetect'];

		// find available mimetype detect method
		$type = strtolower($this->options['mimeDetect']);
		$type = preg_match('/^(finfo|mime_content_type|internal|auto)$/i', $type) ? $type : 'auto';
		$regexp = '/text\/x\-(php|c\+\+)/';
		
		if (($type == 'finfo' || $type == 'auto') 
		&& class_exists('finfo')
		&& preg_match($regexp, array_shift(explode(';', @finfo_file(finfo_open(FILEINFO_MIME), __FILE__))))) {
			$type = 'finfo';
			$this->finfo = finfo_open(FILEINFO_MIME);
		} elseif (($type == 'mime_content_type' || $type == 'auto') 
		&& function_exists('mime_content_type')
		&& preg_match($regexp, array_shift(explode(';', mime_content_type(__FILE__))))) {
			$type = 'mime_content_type';
		} else {
			$type = 'internal';
		}
		$this->mimeDetect = $type;

		// load mimes from external file for mimeDetect == 'internal'
		// based on Alexey Sukhotin idea and patch: http://elrte.org/redmine/issues/163
		// file must be in file directory or in parent one 
		if ($this->mimeDetect == 'internal' && !self::$mimetypesLoaded) {
			self::$mimetypesLoaded = true;
			$this->mimeDetect = 'internal';
			$file = false;
			if (!empty($this->options['mimefile']) && file_exists($this->options['mimefile'])) {
				$file = $this->options['mimefile'];
			} elseif (file_exists(dirname(__FILE__).DIRECTORY_SEPARATOR.'mime.types')) {
				$file = dirname(__FILE__).DIRECTORY_SEPARATOR.'mime.types';
			} elseif (file_exists(dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'mime.types')) {
				$file = dirname(dirname(__FILE__)).DIRECTORY_SEPARATOR.'mime.types';
			}

			if ($file && file_exists($file)) {
				$mimecf = file($file);

				foreach ($mimecf as $line_num => $line) {
					if (!preg_match('/^\s*#/', $line)) {
						$mime = preg_split('/\s+/', $line, -1, PREG_SPLIT_NO_EMPTY);
						for ($i = 1, $size = count($mime); $i < $size ; $i++) {
							if (!isset(self::$mimetypes[$mime[$i]])) {
								self::$mimetypes[$mime[$i]] = $mime[0];
							}
						}
					}
				}
			}
		}

		$this->rootName = empty($this->options['alias']) ? $this->_basename($this->root) : $this->options['alias'];
		$root = $this->stat($this->root);
		
		if (!$root) {
			return $this->setError('Root folder does not exists.');
		}
		if (!$root['read'] && !$root['write']) {
			return $this->setError('Root folder has not read and write permissions.');
		}
		
		// debug($root);
		
		if ($root['read']) {
			// check startPath - path to open by default instead of root
			if ($this->options['startPath']) {
				$start = $this->stat($this->options['startPath']);
				if (!empty($start)
				&& $start['mime'] == 'directory'
				&& $start['read']
				&& empty($start['hidden'])
				&& $this->_inpath($this->options['startPath'], $this->root)) {
					$this->startPath = $this->options['startPath'];
					if (substr($this->startPath, -1, 1) == $this->options['separator']) {
						$this->startPath = substr($this->startPath, 0, -1);
					}
				}
			}
		} else {
			$this->options['URL']     = '';
			$this->options['tmbURL']  = '';
			$this->options['tmbPath'] = '';
			// read only volume
			array_unshift($this->attributes, array(
				'pattern' => '/.*/',
				'read'    => false
			));
		}
		$this->treeDeep = $this->options['treeDeep'] > 0 ? (int)$this->options['treeDeep'] : 1;
		$this->tmbSize  = $this->options['tmbSize'] > 0 ? (int)$this->options['tmbSize'] : 48;
		$this->URL      = $this->options['URL'];
		if ($this->URL && preg_match("|[^/?&=]$|", $this->URL)) {
			$this->URL .= '/';
		}

		$this->tmbURL   = !empty($this->options['tmbURL']) ? $this->options['tmbURL'] : '';
		if ($this->tmbURL && preg_match("|[^/?&=]$|", $this->tmbURL)) {
			$this->tmbURL .= '/';
		}
		
		$this->nameValidator = is_string($this->options['acceptedName']) && !empty($this->options['acceptedName']) 
			? $this->options['acceptedName']
			: '';

		$this->_checkArchivers();
		// manual control archive types to create
		if (!empty($this->options['archiveMimes']) && is_array($this->options['archiveMimes'])) {
			foreach ($this->archivers['create'] as $mime => $v) {
				if (!in_array($mime, $this->options['archiveMimes'])) {
					unset($this->archivers['create'][$mime]);
				}
			}
		}
		
		// manualy add archivers
		if (!empty($this->options['archivers']['create']) && is_array($this->options['archivers']['create'])) {
			foreach ($this->options['archivers']['create'] as $mime => $conf) {
				if (strpos($mime, 'application/') === 0 
				&& !empty($conf['cmd']) 
				&& isset($conf['argc']) 
				&& !empty($conf['ext'])
				&& !isset($this->archivers['create'][$mime])) {
					$this->archivers['create'][$mime] = $conf;
				}
			}
		}
		
		if (!empty($this->options['archivers']['extract']) && is_array($this->options['archivers']['extract'])) {
			foreach ($this->options['archivers']['extract'] as $mime => $conf) {
				if (substr($mime, 'application/') === 0 
				&& !empty($cons['cmd']) 
				&& isset($conf['argc']) 
				&& !empty($conf['ext'])
				&& !isset($this->archivers['extract'][$mime])) {
					$this->archivers['extract'][$mime] = $conf;
				}
			}
		}

		$this->configure();
		// echo $this->uploadMaxSize;
		// echo $this->options['uploadMaxSize'];
		return $this->mounted = true;
	}
	
	/**
	 * Some "unmount" stuffs - may be required by virtual fs
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function umount() {
	}
	
	/**
	 * Return error message from last failed action
	 *
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function error() {
		return $this->error;
	}
	
	/**
	 * Set mimetypes allowed to display to client
	 *
	 * @param  array  $mimes
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function setMimesFilter($mimes) {
		if (is_array($mimes)) {
			$this->onlyMimes = $mimes;
		}
	}
	
	/**
	 * Return root folder hash
	 *
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	public function root() {
		return $this->encode($this->root);
	}
	
	/**
	 * Return root or startPath hash
	 *
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	public function defaultPath() {
		return $this->encode($this->startPath ? $this->startPath : $this->root);
	}
		
	/**
	 * Return volume options required by client:
	 *
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function options($hash) {
		return array(
			'path'          => $this->_path($this->decode($hash)),
			'url'           => $this->URL,
			'tmbUrl'        => $this->tmbURL,
			'disabled'      => $this->disabled,
			'separator'     => $this->separator,
			'copyOverwrite' => intval($this->options['copyOverwrite']),
			'archivers'     => array(
				'create'  => array_keys($this->archivers['create']),
				'extract' => array_keys($this->archivers['extract'])
			)
		);
	}
	
	/**
	 * Return true if command disabled in options
	 *
	 * @param  string  $cmd  command name
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	public function commandDisabled($cmd) {
		return in_array($cmd, $this->disabled);
	}
	
	/**
	 * Return true if mime is required mimes list
	 *
	 * @param  string     $mime   mime type to check
	 * @param  array      $mimes  allowed mime types list or not set to use client mimes list
	 * @param  bool|null  $empty  what to return on empty list
	 * @return bool|null
	 * @author Dmitry (dio) Levashov
	 * @author Troex Nevelin
	 **/
	public function mimeAccepted($mime, $mimes = array(), $empty = true) {
		$mimes = !empty($mimes) ? $mimes : $this->onlyMimes;
		if (empty($mimes)) {
			return $empty;
		}
		return $mime == 'directory'
			|| in_array('all', $mimes)
			|| in_array('All', $mimes)
			|| in_array($mime, $mimes)
			|| in_array(substr($mime, 0, strpos($mime, '/')), $mimes);
	}
	
	/**
	 * Return true if voume is readable.
	 *
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	public function isReadable() {
		$stat = $this->stat($this->root);
		return $stat['read'];
	}
	
	/**
	 * Return true if copy from this volume allowed
	 *
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	public function copyFromAllowed() {
		return !!$this->options['copyFrom'];
	}
	
	/**
	 * Return file path related to root
	 *
	 * @param  string   $hash  file hash
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	public function path($hash) {
		return $this->_path($this->decode($hash));
	}
	
	/**
	 * Return file real path if file exists
	 *
	 * @param  string  $hash  file hash
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	public function realpath($hash) {
		$path = $this->decode($hash);
		return $this->stat($path) ? $path : false;
	}
	
	/**
	 * Return list of moved/overwrited files
	 *
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function removed() {
		return $this->removed;
	}
	
	/**
	 * Clean removed files list
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function resetRemoved() {
		$this->removed = array();
	}
	
	/**
	 * Return file/dir hash or first founded child hash with required attr == $val
	 *
	 * @param  string   $hash  file hash
	 * @param  string   $attr  attribute name
	 * @param  bool     $val   attribute value
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function closest($hash, $attr, $val) {
		return ($path = $this->closestByAttr($this->decode($hash), $attr, $val)) ? $this->encode($path) : false;
	}
	
	/**
	 * Return file info or false on error
	 *
	 * @param  string   $hash      file hash
	 * @param  bool     $realpath  add realpath field to file info
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function file($hash) {
		$path = $this->decode($hash);
		
		return ($file = $this->stat($path)) ? $file : $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		
		if (($file = $this->stat($path)) != false) {
			if ($realpath) {
				$file['realpath'] = $path;
			}
			return $file;
		}
		return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
	}
	
	/**
	 * Return folder info
	 *
	 * @param  string   $hash  folder hash
	 * @param  bool     $hidden  return hidden file info
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function dir($hash, $resolveLink=false) {
		if (($dir = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_DIR_NOT_FOUND);
		}

		if ($resolveLink && !empty($dir['thash'])) {
			$dir = $this->file($dir['thash']);
		}
		
		return $dir && $dir['mime'] == 'directory' && empty($dir['hidden']) 
			? $dir 
			: $this->setError(elFinder::ERROR_NOT_DIR);
	}
	
	/**
	 * Return directory content or false on error
	 *
	 * @param  string   $hash   file hash
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function scandir($hash) {
		if (($dir = $this->dir($hash)) == false) {
			return false;
		}
		
		return $dir['read']
			? $this->getScandir($this->decode($hash))
			: $this->setError(elFinder::ERROR_PERM_DENIED);
	}

	/**
	 * Return dir files names list
	 * 
	 * @param  string  $hash   file hash
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function ls($hash) {
		if (($dir = $this->dir($hash)) == false || !$dir['read']) {
			return false;
		}
		
		$list = array();
		$path = $this->decode($hash);
		
		foreach ($this->getScandir($path) as $stat) {
			if (empty($stat['hidden']) && $this->mimeAccepted($stat['mime'])) {
				$list[] = $stat['name'];
			}
		}

		return $list;
	}

	/**
	 * Return subfolders for required folder or false on error
	 *
	 * @param  string   $hash  folder hash or empty string to get tree from root folder
	 * @param  int      $deep  subdir deep
	 * @param  string   $exclude  dir hash which subfolders must be exluded from result, required to not get stat twice on cwd subfolders
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function tree($hash='', $deep=0, $exclude='') {
		$path = $hash ? $this->decode($hash) : $this->root;
		
		if (($dir = $this->stat($path)) == false || $dir['mime'] != 'directory') {
			return false;
		}
		
		$dirs = $this->gettree($path, $deep > 0 ? $deep -1 : $this->treeDeep-1, $this->decode($exclude));
		array_unshift($dirs, $dir);
		return $dirs;
	}
	
	/**
	 * Return part of dirs tree from required dir up to root dir
	 *
	 * @param  string  $hash  directory hash
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function parents($hash) {
		if (($current = $this->dir($hash)) == false) {
			return false;
		}

		$path = $this->decode($hash);
		$tree = array();
		
		while ($path && $path != $this->root) {
			$path = $this->_dirname($path);
			$stat = $this->stat($path);
			if (!empty($stat['hidden']) || !$stat['read']) {
				return false;
			}
			
			array_unshift($tree, $stat);
			if ($path != $this->root) {
				foreach ($this->gettree($path, 0) as $dir) {
					if (!in_array($dir, $tree)) {
						$tree[] = $dir;
					}
				}
			}
		}

		return $tree ? $tree : array($current);
	}
	
	/**
	 * Create thumbnail for required file and return its name of false on failed
	 *
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function tmb($hash) {
		$path = $this->decode($hash);
		$stat = $this->stat($path);
		
		if (isset($stat['tmb'])) {
			return $stat['tmb'] == "1" ? $this->createTmb($path, $stat) : $stat['tmb'];
		}
		return false;
	}
	
	/**
	 * Return file size / total directory size
	 *
	 * @param  string   file hash
	 * @return int
	 * @author Dmitry (dio) Levashov
	 **/
	public function size($hash) {
		return $this->countSize($this->decode($hash));
	}
	
	/**
	 * Open file for reading and return file pointer
	 *
	 * @param  string   file hash
	 * @return Resource
	 * @author Dmitry (dio) Levashov
	 **/
	public function open($hash) {
		if (($file = $this->file($hash)) == false
		|| $file['mime'] == 'directory') {
			return false;
		}
		
		return $this->_fopen($this->decode($hash), 'rb');
	}
	
	/**
	 * Close file pointer
	 *
	 * @param  Resource  $fp   file pointer
	 * @param  string    $hash file hash
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	public function close($fp, $hash) {
		$this->_fclose($fp, $this->decode($hash));
	}
	
	/**
	 * Create directory and return dir info
	 *
	 * @param  string   $dst  destination directory
	 * @param  string   $name directory name
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function mkdir($dst, $name) {
		if ($this->commandDisabled('mkdir')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (!$this->nameAccepted($name)) {
			return $this->setError(elFinder::ERROR_INVALID_NAME);
		}
		
		if (($dir = $this->dir($dst)) == false) {
			return $this->setError(elFinder::ERROR_TRGDIR_NOT_FOUND, '#'.$dst);
		}
		
		if (!$dir['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($dst);
		$dst  = $this->_joinPath($path, $name);
		$stat = $this->stat($dst);
		if (!empty($stat)) {
			return $this->setError(elFinder::ERROR_EXISTS, $name);
		}
		$this->clearcache();
		return ($path = $this->_mkdir($path, $name)) ? $this->stat($path) : false;
	}
	
	/**
	 * Create empty file and return its info
	 *
	 * @param  string   $dst  destination directory
	 * @param  string   $name file name
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function mkfile($dst, $name) {
		if ($this->commandDisabled('mkfile')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (!$this->nameAccepted($name)) {
			return $this->setError(elFinder::ERROR_INVALID_NAME);
		}
		
		if (($dir = $this->dir($dst)) == false) {
			return $this->setError(elFinder::ERROR_TRGDIR_NOT_FOUND, '#'.$dst);
		}
		
		if (!$dir['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($dst);

		if ($this->stat($this->_joinPath($path, $name))) {
			return $this->setError(elFinder::ERROR_EXISTS, $name);
		}
		$this->clearcache();
		return ($path = $this->_mkfile($path, $name)) ? $this->stat($path) : false;
	}
	
	/**
	 * Rename file and return file info
	 *
	 * @param  string  $hash  file hash
	 * @param  string  $name  new file name
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function rename($hash, $name) {
		if ($this->commandDisabled('rename')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (!$this->nameAccepted($name)) {
			return $this->setError(elFinder::ERROR_INVALID_NAME, $name);
		}
		
		if (!($file = $this->file($hash))) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if ($name == $file['name']) {
			return $file;
		}
		
		if (!empty($file['locked'])) {
			return $this->setError(elFinder::ERROR_LOCKED, $file['name']);
		}
		
		$path = $this->decode($hash);
		$dir  = $this->_dirname($path);
		$stat = $this->stat($this->_joinPath($dir, $name));
		if ($stat) {
			return $this->setError(elFinder::ERROR_EXISTS, $name);
		}
		
		if (!$this->_move($path, $dir, $name)) {
			return false;
		}
		
		if (!empty($stat['tmb']) && $stat['tmb'] != "1") {
			$this->rmTmb($stat['tmb']);
		}
		
		$path = $this->_joinPath($dir, $name);

		$this->clearcache();
		return $this->stat($path);
	}
	
	/**
	 * Create file copy with suffix "copy number" and return its info
	 *
	 * @param  string   $hash    file hash
	 * @param  string   $suffix  suffix to add to file name
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function duplicate($hash, $suffix='copy') {
		if ($this->commandDisabled('duplicate')) {
			return $this->setError(elFinder::ERROR_COPY, '#'.$hash, elFinder::ERROR_PERM_DENIED);
		}
		
		if (($file = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_COPY, elFinder::ERROR_FILE_NOT_FOUND);
		}

		$path = $this->decode($hash);
		$dir  = $this->_dirname($path);

		return ($path = $this->copy($path, $dir, $this->uniqueName($dir, $this->_basename($path), ' '.$suffix.' '))) == false
			? false
			: $this->stat($path);
	}
	
	/**
	 * Save uploaded file. 
	 * On success return array with new file stat and with removed file hash (if existed file was replaced)
	 *
	 * @param  Resource $fp      file pointer
	 * @param  string   $dst     destination folder hash
	 * @param  string   $src     file name
	 * @param  string   $tmpname file tmp name - required to detect mime type
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function upload($fp, $dst, $name, $tmpname) {
		if ($this->commandDisabled('upload')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (($dir = $this->dir($dst)) == false) {
			return $this->setError(elFinder::ERROR_TRGDIR_NOT_FOUND, '#'.$dst);
		}

		if (!$dir['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (!$this->nameAccepted($name)) {
			return $this->setError(elFinder::ERROR_INVALID_NAME);
		}
		
		$mime = $this->mimetype($this->mimeDetect == 'internal' ? $name : $tmpname); 
		if ($mime == 'unknown' && $this->mimeDetect == 'internal') {
			$mime = elFinderVolumeDriver::mimetypeInternalDetect($name);
		}

		// logic based on http://httpd.apache.org/docs/2.2/mod/mod_authz_host.html#order
		$allow  = $this->mimeAccepted($mime, $this->uploadAllow, null);
		$deny   = $this->mimeAccepted($mime, $this->uploadDeny,  null);
		$upload = true; // default to allow
		if (strtolower($this->uploadOrder[0]) == 'allow') { // array('allow', 'deny'), default is to 'deny'
			$upload = false; // default is deny
			if (!$deny && ($allow === true)) { // match only allow
				$upload = true;
			}// else (both match | no match | match only deny) { deny }
		} else { // array('deny', 'allow'), default is to 'allow' - this is the default rule
			$upload = true; // default is allow
			if (($deny === true) && !$allow) { // match only deny
				$upload = false;
			} // else (both match | no match | match only allow) { allow }
		}
		if (!$upload) {
			return $this->setError(elFinder::ERROR_UPLOAD_FILE_MIME);
		}

		if ($this->uploadMaxSize > 0 && filesize($tmpname) > $this->uploadMaxSize) {
			return $this->setError(elFinder::ERROR_UPLOAD_FILE_SIZE);
		}

		$dstpath = $this->decode($dst);
		$test    = $this->_joinPath($dstpath, $name);
		
		$file = $this->stat($test);
		$this->clearcache();
		
		if ($file) { // file exists
			if ($this->options['uploadOverwrite']) {
				if (!$file['write']) {
					return $this->setError(elFinder::ERROR_PERM_DENIED);
				} elseif ($file['mime'] == 'directory') {
					return $this->setError(elFinder::ERROR_NOT_REPLACE, $name);
				} 
				$this->remove($file);
			} else {
				$name = $this->uniqueName($dstpath, $name, '-', false);
			}
		}
		
		$w = $h = 0;
		if (strpos($mime, 'image') === 0 && ($s = getimagesize($tmpname))) {
			$w = $s[0];
			$h = $s[1];
		}
		// $this->clearcache();
		if (($path = $this->_save($fp, $dstpath, $name, $mime, $w, $h)) == false) {
			return false;
		}
		
		

		return $this->stat($path);
	}
	
	/**
	 * Paste files
	 *
	 * @param  Object  $volume  source volume
	 * @param  string  $source  file hash
	 * @param  string  $dst     destination dir hash
	 * @param  bool    $rmSrc   remove source after copy?
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function paste($volume, $src, $dst, $rmSrc = false) {
		$err = $rmSrc ? elFinder::ERROR_MOVE : elFinder::ERROR_COPY;
		
		if ($this->commandDisabled('paste')) {
			return $this->setError($err, '#'.$src, elFinder::ERROR_PERM_DENIED);
		}

		if (($file = $volume->file($src, $rmSrc)) == false) {
			return $this->setError($err, '#'.$src, elFinder::ERROR_FILE_NOT_FOUND);
		}

		$name = $file['name'];
		$errpath = $volume->path($src);
		
		if (($dir = $this->dir($dst)) == false) {
			return $this->setError($err, $errpath, elFinder::ERROR_TRGDIR_NOT_FOUND, '#'.$dst);
		}
		
		if (!$dir['write'] || !$file['read']) {
			return $this->setError($err, $errpath, elFinder::ERROR_PERM_DENIED);
		}

		$destination = $this->decode($dst);

		if (($test = $volume->closest($src, $rmSrc ? 'locked' : 'read', $rmSrc))) {
			return $rmSrc
				? $this->setError($err, $errpath, elFinder::ERROR_LOCKED, $volume->path($test))
				: $this->setError($err, $errpath, elFinder::ERROR_PERM_DENIED);
		}

		$test = $this->_joinPath($destination, $name);
		$stat = $this->stat($test);
		$this->clearcache();
		if ($stat) {
			if ($this->options['copyOverwrite']) {
				// do not replace file with dir or dir with file
				if (!$this->isSameType($file['mime'], $stat['mime'])) {
					return $this->setError(elFinder::ERROR_NOT_REPLACE, $this->_path($test));
				}
				// existed file is not writable
				if (!$stat['write']) {
					return $this->setError($err, $errpath, elFinder::ERROR_PERM_DENIED);
				}
				// existed file locked or has locked child
				if (($locked = $this->closestByAttr($test, 'locked', true))) {
					return $this->setError(elFinder::ERROR_LOCKED, $this->_path($locked));
				}
				// remove existed file
				if (!$this->remove($test)) {
					return $this->setError(elFinder::ERROR_REPLACE, $this->_path($test));
				}
			} else {
				$name = $this->uniqueName($destination, $name, ' ', false);
			}
		}
		
		// copy/move inside current volume
		if ($volume == $this) {
			$source = $this->decode($src);
			// do not copy into itself
			if ($this->_inpath($destination, $source)) {
				return $this->setError(elFinder::ERROR_COPY_INTO_ITSELF, $path);
			}
			$method = $rmSrc ? 'move' : 'copy';
			
			return ($path = $this->$method($source, $destination, $name)) ? $this->stat($path) : false;
		}
		
		
		// copy/move from another volume
		if (!$this->options['copyTo'] || !$volume->copyFromAllowed()) {
			return $this->setError(elFinder::ERROR_COPY, $errpath, elFinder::ERROR_PERM_DENIED);
		}
		
		if (($path = $this->copyFrom($volume, $src, $destination, $name)) == false) {
			return false;
		}
		
		if ($rmSrc) {
			if ($volume->rm($src)) {
				$this->removed[] = $file;
			} else {
				return $this->setError(elFinder::ERROR_MOVE, $errpath, elFinder::ERROR_RM_SRC);
			}
		}
		return $this->stat($path);
	}
	
	/**
	 * Return file contents
	 *
	 * @param  string  $hash  file hash
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	public function getContents($hash) {
		$file = $this->file($hash);
		
		if (!$file) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if ($file['mime'] == 'directory') {
			return $this->setError(elFinder::ERROR_NOT_FILE);
		}
		
		if (!$file['read']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		return $this->_getContents($this->decode($hash));
	}
	
	/**
	 * Put content in text file and return file info.
	 *
	 * @param  string  $hash     file hash
	 * @param  string  $content  new file content
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function putContents($hash, $content) {
		if ($this->commandDisabled('edit')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($hash);
		
		if (!($file = $this->file($hash))) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if (!$file['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		$this->clearcache();
		return $this->_filePutContents($path, $content) ? $this->stat($path) : false;
	}
	
	/**
	 * Extract files from archive
	 *
	 * @param  string  $hash  archive hash
	 * @return array|bool
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	public function extract($hash) {
		if ($this->commandDisabled('extract')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (($file = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		$archiver = isset($this->archivers['extract'][$file['mime']])
			? $this->archivers['extract'][$file['mime']]
			: false;
			
		if (!$archiver) {
			return $this->setError(elFinder::ERROR_NOT_ARCHIVE);
		}
		
		$path   = $this->decode($hash);
		$parent = $this->stat($this->_dirname($path));

		if (!$file['read'] || !$parent['write']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		$this->clearcache();
		return ($path = $this->_extract($path, $archiver)) ? $this->stat($path) : false;
	}

	/**
	 * Add files to archive
	 *
	 * @return void
	 **/
	public function archive($hashes, $mime) {
		if ($this->commandDisabled('archive')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}

		$archiver = isset($this->archivers['create'][$mime])
			? $this->archivers['create'][$mime]
			: false;
			
		if (!$archiver) {
			return $this->setError(elFinder::ERROR_ARCHIVE_TYPE);
		}
		
		$files = array();
		
		foreach ($hashes as $hash) {
			if (($file = $this->file($hash)) == false) {
				return $this->error(elFinder::ERROR_FILE_NOT_FOUND, '#'+$hash);
			}
			if (!$file['read']) {
				return $this->error(elFinder::ERROR_PERM_DENIED);
			}
			$path = $this->decode($hash);
			if (!isset($dir)) {
				$dir = $this->_dirname($path);
				$stat = $this->stat($dir);
				if (!$stat['write']) {
					return $this->error(elFinder::ERROR_PERM_DENIED);
				}
			}
			
			$files[] = $this->_basename($path);
		}
		
		$name = (count($files) == 1 ? $files[0] : 'Archive').'.'.$archiver['ext'];
		$name = $this->uniqueName($dir, $name, '');
		$this->clearcache();
		return ($path = $this->_archive($dir, $files, $name, $archiver)) ? $this->stat($path) : false;
	}
	
	/**
	 * Resize image
	 *
	 * @param  string   $hash    image file
	 * @param  int      $width   new width
	 * @param  int      $height  new height
	 * @param  int      $x       X start poistion for crop
	 * @param  int      $y       Y start poistion for crop
	 * @param  string   $mode    action how to mainpulate image
	 * @return array|false
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 * @author nao-pon
	 * @author Troex Nevelin
	 **/
	public function resize($hash, $width, $height, $x, $y, $mode = 'resize', $bg = '', $degree = 0) {
		if ($this->commandDisabled('resize')) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		if (($file = $this->file($hash)) == false) {
			return $this->setError(elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if (!$file['write'] || !$file['read']) {
			return $this->setError(elFinder::ERROR_PERM_DENIED);
		}
		
		$path = $this->decode($hash);
		
		if (!$this->canResize($path, $file)) {
			return $this->setError(elFinder::ERROR_UNSUPPORT_TYPE);
		}

		switch($mode) {
			
			case 'propresize':
				$result = $this->imgResize($path, $width, $height, true, true);
				break;

			case 'crop':
				$result = $this->imgCrop($path, $width, $height, $x, $y);
				break;

			case 'fitsquare':
				$result = $this->imgSquareFit($path, $width, $height, 'center', 'middle', ($bg ? $bg : $this->options['tmbBgColor']));
				break;

			case 'rotate':
				$result = $this->imgRotate($path, $degree, ($bg ? $bg : $this->options['tmbBgColor']));
				break;

			default:
				$result = $this->imgResize($path, $width, $height, false, true);
				break;
		}

		if ($result) {
			if (!empty($file['tmb']) && $file['tmb'] != "1") {
				$this->rmTmb($file['tmb']);
			}
			$this->clearcache();
			return $this->stat($path);
		}
		
   		return false;
	}
	
	/**
	 * Remove file/dir
	 *
	 * @param  string  $hash  file hash
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	public function rm($hash) {
		return $this->commandDisabled('rm')
			? array(elFinder::ERROR_ACCESS_DENIED)
			: $this->remove($this->decode($hash));
	}
	
	/**
	 * Search files
	 *
	 * @param  string  $q  search string
	 * @param  array   $mimes
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function search($q, $mimes) {
		return $this->doSearch($this->root, $q, $mimes);
	}
	
	/**
	 * Return image dimensions
	 *
	 * @param  string  $hash  file hash
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	public function dimensions($hash) {
		if (($file = $this->file($hash)) == false) {
			return false;
		}
		
		return $this->_dimensions($this->decode($hash), $file['mime']);
	}
	
	/**
	 * Save error message
	 *
	 * @param  array  error 
	 * @return false
	 * @author Dmitry(dio) Levashov
	 **/
	protected function setError($error) {
		
		$this->error = array();
		
		foreach (func_get_args() as $err) {
			if (is_array($err)) {
				$this->error = array_merge($this->error, $err);
			} else {
				$this->error[] = $err;
			}
		}
		
		// $this->error = is_array($error) ? $error : func_get_args();
		return false;
	}
	
	/*********************************************************************/
	/*                               FS API                              */
	/*********************************************************************/
	
	/***************** paths *******************/
	
	/**
	 * Encode path into hash
	 *
	 * @param  string  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 * @author Troex Nevelin
	 **/
	protected function encode($path) {
		if ($path !== '') {

			// cut ROOT from $path for security reason, even if hacker decodes the path he will not know the root
			$p = $this->_relpath($path);
			// if reqesting root dir $path will be empty, then assign '/' as we cannot leave it blank for crypt
			if ($p === '')	{
				$p = DIRECTORY_SEPARATOR;
			}

			// TODO crypt path and return hash
			$hash = $this->crypt($p);
			// hash is used as id in HTML that means it must contain vaild chars
			// make base64 html safe and append prefix in begining
			$hash = strtr(base64_encode($hash), '+/=', '-_.');
			// remove dots '.' at the end, before it was '=' in base64
			$hash = rtrim($hash, '.'); 
			// append volume id to make hash unique
			return $this->id.$hash;
		}
	}
	
	/**
	 * Decode path from hash
	 *
	 * @param  string  file hash
	 * @return string
	 * @author Dmitry (dio) Levashov
	 * @author Troex Nevelin
	 **/
	protected function decode($hash) {
		if (strpos($hash, $this->id) === 0) {
			// cut volume id after it was prepended in encode
			$h = substr($hash, strlen($this->id));
			// replace HTML safe base64 to normal
			$h = base64_decode(strtr($h, '-_.', '+/='));
			// TODO uncrypt hash and return path
			$path = $this->uncrypt($h); 
			// append ROOT to path after it was cut in encode
			return $this->_abspath($path);//$this->root.($path == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR.$path); 
		}
	}
	
	/**
	 * Return crypted path 
	 * Not implemented
	 *
	 * @param  string  path
	 * @return mixed
	 * @author Dmitry (dio) Levashov
	 **/
	protected function crypt($path) {
		return $path;
	}
	
	/**
	 * Return uncrypted path 
	 * Not implemented
	 *
	 * @param  mixed  hash
	 * @return mixed
	 * @author Dmitry (dio) Levashov
	 **/
	protected function uncrypt($hash) {
		return $hash;
	}
	
	/**
	 * Validate file name based on $this->options['acceptedName'] regexp
	 *
	 * @param  string  $name  file name
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function nameAccepted($name) {
		if ($this->nameValidator) {
			if (function_exists($this->nameValidator)) {
				$f = $this->nameValidator;
				return $f($name);
			}
			return preg_match($this->nameValidator, $name);
		}
		return true;
	}
	
	/**
	 * Return new unique name based on file name and suffix
	 *
	 * @param  string  $path    file path
	 * @param  string  $suffix  suffix append to name
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	public function uniqueName($dir, $name, $suffix = ' copy', $checkNum=true) {
		$ext  = '';

		if (preg_match('/\.((tar\.(gz|bz|bz2|z|lzo))|cpio\.gz|ps\.gz|xcf\.(gz|bz2)|[a-z0-9]{1,4})$/i', $name, $m)) {
			$ext  = '.'.$m[1];
			$name = substr($name, 0,  strlen($name)-strlen($m[0]));
		} 
		
		if ($checkNum && preg_match('/('.$suffix.')(\d*)$/i', $name, $m)) {
			$i    = (int)$m[2];
			$name = substr($name, 0, strlen($name)-strlen($m[2]));
		} else {
			$i     = 1;
			$name .= $suffix;
		}
		$max = $i+100000;

		while ($i <= $max) {
			$n = $name.($i > 0 ? $i : '').$ext;

			if (!$this->stat($this->_joinPath($dir, $n))) {
				$this->clearcache();
				return $n;
			}
			$i++;
		}
		return $name.md5($dir).$ext;
	}
	
	/*********************** file stat *********************/
	
	/**
	 * Check file attribute
	 *
	 * @param  string  $path  file path
	 * @param  string  $name  attribute name (read|write|locked|hidden)
	 * @param  bool    $val   attribute value returned by file system
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function attr($path, $name, $val=false) {
		if (!isset($this->defaults[$name])) {
			return false;
		}
		
		
		$perm = null;
		
		if ($this->access) {
			if (is_array($this->access)) {
				$obj    = $this->access[0];
				$method = $this->access[1];
				$perm   = $obj->{$method}($name, $path, $this->options['accessControlData'], $this);
			} else {
				$func = $this->access;
				$perm = $func($name, $path, $this->options['accessControlData'], $this);
			}
			
			if ($perm !== null) {
				return !!$perm;
			}
		}
		
		for ($i = 0, $c = count($this->attributes); $i < $c; $i++) {
			$attrs = $this->attributes[$i];
			$p = $this->separator.$this->_relpath($path);
			if (isset($attrs[$name]) && isset($attrs['pattern']) && preg_match($attrs['pattern'], $p)) {
				$perm = $attrs[$name];
			} 
		}
		
		return $perm === null ? $this->defaults[$name] : !!$perm;
	}
	
	/**
	 * Return fileinfo 
	 *
	 * @param  string  $path  file cache
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function stat($path) {
		return isset($this->cache[$path])
			? $this->cache[$path]
			: $this->updateCache($path, $this->_stat($path));
	}
	
	/**
	 * Put file stat in cache and return it
	 *
	 * @param  string  $path   file path
	 * @param  array   $stat   file stat
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function updateCache($path, $stat) {
		if (empty($stat) || !is_array($stat)) {
			return $this->cache[$path] = array();
		}

		$stat['hash'] = $this->encode($path);

		$root = $path == $this->root;
		
		if ($root) {
			$stat['volumeid'] = $this->id;
			if ($this->rootName) {
				$stat['name'] = $this->rootName;
			}
		} else {
			if (empty($stat['name'])) {
				$stat['name'] = $this->_basename($path);
			}
			if (empty($stat['phash'])) {
				$stat['phash'] = $this->encode($this->_dirname($path));
			}
		}
		
		// fix name if required
		if ($this->options['utf8fix'] && $this->options['utf8patterns'] && $this->options['utf8replace']) {
			$stat['name'] = json_decode(str_replace($this->options['utf8patterns'], $this->options['utf8replace'], json_encode($stat['name'])));
		}
		
		
		if (empty($stat['mime'])) {
			$stat['mime'] = $this->mimetype($stat['name']);
		}
		
		// @todo move dateformat to client
		$stat['date'] = isset($stat['ts'])
			? $this->formatDate($stat['ts'])
			: 'unknown';
			
		if (!isset($stat['size'])) {
			$stat['size'] = 'unknown';
		}	

		$stat['read']  = intval($this->attr($path, 'read', isset($stat['read']) ? !!$stat['read'] : false));
		$stat['write'] = intval($this->attr($path, 'write', isset($stat['write']) ? !!$stat['write'] : false));
		if ($root) {
			$stat['locked'] = 1;
		} elseif ($this->attr($path, 'locked', !empty($stat['locked']))) {
			$stat['locked'] = 1;
		} else {
			unset($stat['locked']);
		}

		if ($root) {
			unset($stat['hidden']);
		} elseif ($this->attr($path, 'hidden', !empty($stat['hidden'])) 
		|| !$this->mimeAccepted($stat['mime'])) {
			$stat['hidden'] = $root ? 0 : 1;
		} else {
			unset($stat['hidden']);
		}
		
		if ($stat['read'] && empty($stat['hidden'])) {
			
			if ($stat['mime'] == 'directory') {
				// for dir - check for subdirs

				if ($this->options['checkSubfolders']) {
					if (isset($stat['dirs'])) {
						if ($stat['dirs']) {
							$stat['dirs'] = 1;
						} else {
							unset($stat['dirs']);
						}
					} elseif (!empty($stat['alias']) && !empty($stat['target'])) {
						$stat['dirs'] = isset($this->cache[$stat['target']])
							? intval(isset($this->cache[$stat['target']]['dirs']))
							: $this->_subdirs($stat['target']);
						
					} elseif ($this->_subdirs($path)) {
						$stat['dirs'] = 1;
					}
				} else {
					$stat['dirs'] = 1;
				}
			} else {
				// for files - check for thumbnails
				$p = isset($stat['target']) ? $stat['target'] : $path;
				if ($this->tmbURL && !isset($stat['tmb']) && $this->canCreateTmb($p, $stat)) {
					$tmb = $this->gettmb($p, $stat);
					$stat['tmb'] = $tmb ? $tmb : 1;
				}
				
			}
		}
		
		if (!empty($stat['alias']) && !empty($stat['target'])) {
			$stat['thash'] = $this->encode($stat['target']);
			unset($stat['target']);
		}

		return $this->cache[$path] = $stat;
	}
	
	/**
	 * Get stat for folder content and put in cache
	 *
	 * @param  string  $path
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function cacheDir($path) {
		$this->dirsCache[$path] = array();

		foreach ($this->_scandir($path) as $p) {
			if (($stat = $this->stat($p)) && empty($stat['hidden'])) {
				$this->dirsCache[$path][] = $p;
			}
		}	
	}
	
	/**
	 * Clean cache
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function clearcache() {
		$this->cache = $this->dirsCache = array();
	}
	
	/**
	 * Return file mimetype
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function mimetype($path) {
		$type = '';
		
		if ($this->mimeDetect == 'finfo') {
			$type = @finfo_file($this->finfo, $path); 
		} elseif ($type == 'mime_content_type') {
			$type = mime_content_type($path);
		} else {
			$type = elFinderVolumeDriver::mimetypeInternalDetect($path);
		}
		
		$type = explode(';', $type);
		$type = trim($type[0]);
		
		if ($type == 'application/x-empty') {
			// finfo return this mime for empty files
			$type = 'text/plain';
		} elseif ($type == 'application/x-zip') {
			// http://elrte.org/redmine/issues/163
			$type = 'application/zip';
		}
		
		return $type == 'unknown' && $this->mimeDetect != 'internal'
			? elFinderVolumeDriver::mimetypeInternalDetect($path)
			: $type;
		
	}
	
	/**
	 * Detect file mimetype using "internal" method
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	static protected function mimetypeInternalDetect($path) {
		$pinfo = pathinfo($path); 
		$ext   = isset($pinfo['extension']) ? strtolower($pinfo['extension']) : '';
		return isset(elFinderVolumeDriver::$mimetypes[$ext]) ? elFinderVolumeDriver::$mimetypes[$ext] : 'unknown';
		
	}
	
	/**
	 * Return file/total directory size
	 *
	 * @param  string  $path  file path
	 * @return int
	 * @author Dmitry (dio) Levashov
	 **/
	protected function countSize($path) {
		$stat = $this->stat($path);

		if (empty($stat) || !$stat['read'] || !empty($stat['hidden'])) {
			return 'unknown';
		}
		
		if ($stat['mime'] != 'directory') {
			return $stat['size'];
		}
		
		$subdirs = $this->options['checkSubfolders'];
		$this->options['checkSubfolders'] = true;
		$result = 0;
		foreach ($this->getScandir($path) as $stat) {
			$size = $stat['mime'] == 'directory' && $stat['read'] 
				? $this->countSize($this->_joinPath($path, $stat['name'])) 
				: $stat['size'];
			if ($size > 0) {
				$result += $size;
			}
		}
		$this->options['checkSubfolders'] = $subdirs;
		return $result;
	}
	
	/**
	 * Return true if all mimes is directory or files
	 *
	 * @param  string  $mime1  mimetype
	 * @param  string  $mime2  mimetype
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function isSameType($mime1, $mime2) {
		return ($mime1 == 'directory' && $mime1 == $mime2) || ($mime1 != 'directory' && $mime2 != 'directory');
	}
	
	/**
	 * If file has required attr == $val - return file path,
	 * If dir has child with has required attr == $val - return child path
	 *
	 * @param  string   $path  file path
	 * @param  string   $attr  attribute name
	 * @param  bool     $val   attribute value
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function closestByAttr($path, $attr, $val) {
		$stat = $this->stat($path);
		
		if (empty($stat)) {
			return false;
		}
		
		$v = isset($stat[$attr]) ? $stat[$attr] : false;
		
		if ($v == $val) {
			return $path;
		}

		return $stat['mime'] == 'directory'
			? $this->childsByAttr($path, $attr, $val) 
			: false;
	}
	
	/**
	 * Return first found children with required attr == $val
	 *
	 * @param  string   $path  file path
	 * @param  string   $attr  attribute name
	 * @param  bool     $val   attribute value
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function childsByAttr($path, $attr, $val) {
		foreach ($this->_scandir($path) as $p) {
			if (($_p = $this->closestByAttr($p, $attr, $val)) != false) {
				return $_p;
			}
		}
		return false;
	}
	
	/*****************  get content *******************/
	
	/**
	 * Return required dir's files info.
	 * If onlyMimes is set - return only dirs and files of required mimes
	 *
	 * @param  string  $path  dir path
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function getScandir($path) {
		$files = array();
		
		!isset($this->dirsCache[$path]) && $this->cacheDir($path);

		foreach ($this->dirsCache[$path] as $p) {
			if (($stat = $this->stat($p)) && empty($stat['hidden'])) {
				$files[] = $stat;
			}
		}

		return $files;
	}
	
	
	/**
	 * Return subdirs tree
	 *
	 * @param  string  $path  parent dir path
	 * @param  int     $deep  tree deep
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function gettree($path, $deep, $exclude='') {
		$dirs = array();
		
		!isset($this->dirsCache[$path]) && $this->cacheDir($path);

		foreach ($this->dirsCache[$path] as $p) {
			$stat = $this->stat($p);
			
			if ($stat && empty($stat['hidden']) && $path != $exclude && $stat['mime'] == 'directory') {
				$dirs[] = $stat;
				if ($deep > 0 && !empty($stat['dirs'])) {
					$dirs = array_merge($dirs, $this->gettree($p, $deep-1));
				}
			}
		}

		return $dirs;
	}	
		
	/**
	 * Recursive files search
	 *
	 * @param  string  $path   dir path
	 * @param  string  $q      search string
	 * @param  array   $mimes
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	protected function doSearch($path, $q, $mimes) {
		$result = array();

		foreach($this->_scandir($path) as $p) {
			$stat = $this->stat($p);

			if (!$stat) { // invalid links
				continue;
			}

			if (!empty($stat['hidden']) || !$this->mimeAccepted($stat['mime'])) {
				continue;
			}
			
			$name = $stat['name'];

			if ($this->stripos($name, $q) !== false) {
				$stat['path'] = $this->_path($p);
				if ($this->URL && !isset($stat['url'])) {
					$stat['url'] = $this->URL . str_replace($this->separator, '/', substr($p, strlen($this->root) + 1));
				}
				
				$result[] = $stat;
			}
			if ($stat['mime'] == 'directory' && $stat['read'] && !isset($stat['alias'])) {
				$result = array_merge($result, $this->doSearch($p, $q, $mimes));
			}
		}
		
		return $result;
	}
		
	/**********************  manuipulations  ******************/
		
	/**
	 * Copy file/recursive copy dir only in current volume.
	 * Return new file path or false.
	 *
	 * @param  string  $src   source path
	 * @param  string  $dst   destination dir path
	 * @param  string  $name  new file name (optionaly)
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function copy($src, $dst, $name) {
		$srcStat = $this->stat($src);
		$this->clearcache();
		
		if (!empty($srcStat['thash'])) {
			$target = $this->decode($srcStat['thash']);
			$stat   = $this->stat($target);
			$this->clearcache();
			return $stat && $this->_symlink($target, $dst, $name)
				? $this->_joinPath($dst, $name)
				: $this->setError(elFinder::ERROR_COPY, $this->_path($src));
		} 
		
		if ($srcStat['mime'] == 'directory') {
			$test = $this->stat($this->_joinPath($dst, $name));
			
			if (($test && $test['mime'] != 'directory') || !$this->_mkdir($dst, $name)) {
				return $this->setError(elFinder::ERROR_COPY, $this->_path($src));
			}
			
			$dst = $this->_joinPath($dst, $name);
			
			foreach ($this->getScandir($src) as $stat) {
				if (empty($stat['hidden'])) {
					$name = $stat['name'];
					if (!$this->copy($this->_joinPath($src, $name), $dst, $name)) {
						return false;
					}
				}
			}
			$this->clearcache();
			return $dst;
		} 

		return $this->_copy($src, $dst, $name) 
			? $this->_joinPath($dst, $name) 
			: $this->setError(elFinder::ERROR_COPY, $this->_path($src));
	}
	
	/**
	 * Move file
	 * Return new file path or false.
	 *
	 * @param  string  $src   source path
	 * @param  string  $dst   destination dir path
	 * @param  string  $name  new file name 
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function move($src, $dst, $name) {
		$stat = $this->stat($src);
		$stat['realpath'] = $src;
		$this->clearcache();
		
		if ($this->_move($src, $dst, $name)) {
			$this->removed[] = $stat;
			return $this->_joinPath($dst, $name);
		}
		
		return $this->setError(elFinder::ERROR_MOVE, $this->_path($src));
	}
	
	/**
	 * Copy file from another volume.
	 * Return new file path or false.
	 *
	 * @param  Object  $volume       source volume
	 * @param  string  $src          source file hash
	 * @param  string  $destination  destination dir path
	 * @param  string  $name         file name
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function copyFrom($volume, $src, $destination, $name) {
		
		if (($source = $volume->file($src)) == false) {
			return $this->setError(elFinder::ERROR_COPY, '#'.$src, $volume->error());
		}
		
		$errpath = $volume->path($src);
		
		if (!$this->nameAccepted($source['name'])) {
			return $this->setError(elFinder::ERROR_COPY, $errpath, elFinder::ERROR_INVALID_NAME);
		}
				
		if (!$source['read']) {
			return $this->setError(elFinder::ERROR_COPY, $errpath, elFinder::ERROR_PERM_DENIED);
		}
		
		if ($source['mime'] == 'directory') {
			$stat = $this->stat($this->_joinPath($destination, $name));
			$this->clearcache();
			if ((!$stat || $stat['mime'] != 'directory') && !$this->_mkdir($destination, $name)) {
				return $this->setError(elFinder::ERROR_COPY, $errpath);
			}
			
			$path = $this->_joinPath($destination, $name);
			
			foreach ($volume->scandir($src) as $entr) {
				if (!$this->copyFrom($volume, $entr['hash'], $path, $entr['name'])) {
					return false;
				}
			}
			
		} else {
			$mime = $source['mime'];
			$w = $h = 0;
			if (strpos($mime, 'image') === 0 && ($dim = $volume->dimensions($src))) {
				$s = explode('x', $dim);
				$w = $s[0];
				$h = $s[1];
			}
			
			if (($fp = $volume->open($src)) == false
			|| ($path = $this->_save($fp, $destination, $name, $mime, $w, $h)) == false) {
				$fp && $volume->close($fp, $src);
				return $this->setError(elFinder::ERROR_COPY, $errpath);
			}
			$volume->close($fp, $src);
		}
		
		return $path;
	}
		
	/**
	 * Remove file/ recursive remove dir
	 *
	 * @param  string  $path   file path
	 * @param  bool    $force  try to remove even if file locked
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function remove($path, $force = false) {
		$stat = $this->stat($path);
		$stat['realpath'] = $path;
		if (!empty($stat['tmb']) && $stat['tmb'] != "1") {
			$this->rmTmb($stat['tmb']);
		}
		$this->clearcache();
		
		if (empty($stat)) {
			return $this->setError(elFinder::ERROR_RM, $this->_path($path), elFinder::ERROR_FILE_NOT_FOUND);
		}
		
		if (!$force && !empty($stat['locked'])) {
			return $this->setError(elFinder::ERROR_LOCKED, $this->_path($path));
		}
		
		if ($stat['mime'] == 'directory') {
			foreach ($this->_scandir($path) as $p) {
				$name = $this->_basename($p);
				if ($name != '.' && $name != '..' && !$this->remove($p)) {
					return false;
				}
			}
			if (!$this->_rmdir($path)) {
				return $this->setError(elFinder::ERROR_RM, $this->_path($path));
			}
			
		} else {
			if (!$this->_unlink($path)) {
				return $this->setError(elFinder::ERROR_RM, $this->_path($path));
			}
		}

		$this->removed[] = $stat;
		return true;
	}
	

	/************************* thumbnails **************************/
		
	/**
	 * Return thumbnail file name for required file
	 *
	 * @param  array  $stat  file stat
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function tmbname($stat) {
		return $stat['hash'].$stat['ts'].'.png';
	}
	
	/**
	 * Return thumnbnail name if exists
	 *
	 * @param  string  $path file path
	 * @param  array   $stat file stat
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function gettmb($path, $stat) {
		if ($this->tmbURL && $this->tmbPath) {
			// file itself thumnbnail
			if (strpos($path, $this->tmbPath) === 0) {
				return basename($path);
			}

			$name = $this->tmbname($stat);
			if (file_exists($this->tmbPath.DIRECTORY_SEPARATOR.$name)) {
				return $name;
			}
		}
		return false;
	}
	
	/**
	 * Return true if thumnbnail for required file can be created
	 *
	 * @param  string  $path  thumnbnail path 
	 * @param  array   $stat  file stat
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function canCreateTmb($path, $stat) {
		return $this->tmbPathWritable 
			&& strpos($path, $this->tmbPath) === false // do not create thumnbnail for thumnbnail
			&& $this->imgLib 
			&& strpos($stat['mime'], 'image') === 0 
			&& ($this->imgLib == 'gd' ? $stat['mime'] == 'image/jpeg' || $stat['mime'] == 'image/png' || $stat['mime'] == 'image/gif' : true);
	}
	
	/**
	 * Return true if required file can be resized.
	 * By default - the same as canCreateTmb
	 *
	 * @param  string  $path  thumnbnail path 
	 * @param  array   $stat  file stat
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	protected function canResize($path, $stat) {
		return $this->canCreateTmb($path, $stat);
	}
	
	/**
	 * Create thumnbnail and return it's URL on success
	 *
	 * @param  string  $path  file path
	 * @param  string  $mime  file mime type
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	protected function createTmb($path, $stat) {
		if (!$stat || !$this->canCreateTmb($path, $stat)) {
			return false;
		}

		$name = $this->tmbname($stat);
		$tmb  = $this->tmbPath.DIRECTORY_SEPARATOR.$name;

		// copy image into tmbPath so some drivers does not store files on local fs
		if (($src = $this->_fopen($path, 'rb')) == false) {
			return false;
		}

		if (($trg = fopen($tmb, 'wb')) == false) {
			$this->_fclose($src, $path);
			return false;
		}

		while (!feof($src)) {
			fwrite($trg, fread($src, 8192));
		}

		$this->_fclose($src, $path);
		fclose($trg);

		$result = false;
		
		$tmbSize = $this->tmbSize;
		
  		if (($s = getimagesize($tmb)) == false) {
			return false;
		}
    
    	/* If image smaller or equal thumbnail size - just fitting to thumbnail square */
    	if ($s[0] <= $tmbSize && $s[1]  <= $tmbSize) {
     	   $result = $this->imgSquareFit($tmb, $tmbSize, $tmbSize, 'center', 'middle', $this->options['tmbBgColor'], 'png' );

	    } else {

	    	if ($this->options['tmbCrop']) {
        
        		/* Resize and crop if image bigger than thumbnail */
	        	if (!(($s[0] > $tmbSize && $s[1] <= $tmbSize) || ($s[0] <= $tmbSize && $s[1] > $tmbSize) ) || ($s[0] > $tmbSize && $s[1] > $tmbSize)) {
    				$result = $this->imgResize($tmb, $tmbSize, $tmbSize, true, false, 'png');
	        	}

				if (($s = getimagesize($tmb)) != false) {
					$x = $s[0] > $tmbSize ? intval(($s[0] - $tmbSize)/2) : 0;
					$y = $s[1] > $tmbSize ? intval(($s[1] - $tmbSize)/2) : 0;
					$result = $this->imgCrop($tmb, $tmbSize, $tmbSize, $x, $y, 'png');
				}

    		} else {
        		$result = $this->imgResize($tmb, $tmbSize, $tmbSize, true, true, $this->imgLib, 'png');
        		$result = $this->imgSquareFit($tmb, $tmbSize, $tmbSize, 'center', 'middle', $this->options['tmbBgColor'], 'png' );
      		}

		}
		if (!$result) {
			unlink($tmb);
			return false;
		}

		return $name;
	}
	
	/**
	 * Resize image
	 *
	 * @param  string   $path               image file
	 * @param  int      $width              new width
	 * @param  int      $height             new height
	 * @param  bool	    $keepProportions    crop image
	 * @param  bool	    $resizeByBiggerSide resize image based on bigger side if true
	 * @param  string   $destformat         image destination format
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
  	protected function imgResize($path, $width, $height, $keepProportions = false, $resizeByBiggerSide = true, $destformat = null) {
		if (($s = @getimagesize($path)) == false) {
			return false;
		}

    	$result = false;
    	
		list($size_w, $size_h) = array($width, $height);
    
    	if ($keepProportions == true) {
           
      		list($orig_w, $orig_h, $new_w, $new_h) = array($s[0], $s[1], $width, $height);
        
      		/* Calculating image scale width and height */
      		$xscale = $orig_w / $new_w;
      		$yscale = $orig_h / $new_h;

      		/* Resizing by biggest side */

			if ($resizeByBiggerSide) {

		        if ($orig_w > $orig_h) {
					$size_h = $orig_h * $width / $orig_w;
					$size_w = $width;
        		} else {
          			$size_w = $orig_w * $height / $orig_h;
          			$size_h = $height;
				}
      
			} else {
        		if ($orig_w > $orig_h) {
          			$size_w = $orig_w * $height / $orig_h;
          			$size_h = $height;
		        } else {
					$size_h = $orig_h * $width / $orig_w;
					$size_w = $width;
				}
			}
    	}

		switch ($this->imgLib) {
			case 'imagick':
				
				try {
					$img = new imagick($path);
				} catch (Exception $e) {

					return false;
				}

				$img->resizeImage($size_w, $size_h, Imagick::FILTER_LANCZOS, true);
					
				$result = $img->writeImage($path);

				return $result ? $path : false;

				break;

			case 'gd':
				if ($s['mime'] == 'image/jpeg') {
					$img = imagecreatefromjpeg($path);
				} elseif ($s['mime'] == 'image/png') {
					$img = imagecreatefrompng($path);
				} elseif ($s['mime'] == 'image/gif') {
					$img = imagecreatefromgif($path);
				} elseif ($s['mime'] == 'image/xbm') {
					$img = imagecreatefromxbm($path);
				}

				if ($img &&  false != ($tmp = imagecreatetruecolor($size_w, $size_h))) {
					if (!imagecopyresampled($tmp, $img, 0, 0, 0, 0, $size_w, $size_h, $s[0], $s[1])) {
							return false;
					}
		
					if ($destformat == 'jpg'  || ($destformat == null && $s['mime'] == 'image/jpeg')) {
						$result = imagejpeg($tmp, $path, 100);
					} else if ($destformat == 'gif' || ($destformat == null && $s['mime'] == 'image/gif')) {
						$result = imagegif($tmp, $path, 7);
					} else {
						$result = imagepng($tmp, $path, 7);
					}

					imagedestroy($img);
					imagedestroy($tmp);

					return $result ? $path : false;

				}
				break;
		}
		
		return false;
  	}
  
	/**
	 * Crop image
	 *
	 * @param  string   $path               image file
	 * @param  int      $width              crop width
	 * @param  int      $height             crop height
	 * @param  bool	    $x                  crop left offset
	 * @param  bool	    $y                  crop top offset
	 * @param  string   $destformat         image destination format
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
  	protected function imgCrop($path, $width, $height, $x, $y, $destformat = null) {
		if (($s = @getimagesize($path)) == false) {
			return false;
		}

		$result = false;
		
		switch ($this->imgLib) {
			case 'imagick':
				
				try {
					$img = new imagick($path);
				} catch (Exception $e) {

					return false;
				}

				$img->cropImage($width, $height, $x, $y);

				$result = $img->writeImage($path);

				return $result ? $path : false;

				break;

			case 'gd':
				if ($s['mime'] == 'image/jpeg') {
					$img = imagecreatefromjpeg($path);
				} elseif ($s['mime'] == 'image/png') {
					$img = imagecreatefrompng($path);
				} elseif ($s['mime'] == 'image/gif') {
					$img = imagecreatefromgif($path);
				} elseif ($s['mime'] == 'image/xbm') {
					$img = imagecreatefromxbm($path);
				}

				if ($img &&  false != ($tmp = imagecreatetruecolor($width, $height))) {

					if (!imagecopy($tmp, $img, 0, 0, $x, $y, $width, $height)) {
						return false;
					}
					
					if ($destformat == 'jpg'  || ($destformat == null && $s['mime'] == 'image/jpeg')) {
						$result = imagejpeg($tmp, $path, 100);
					} else if ($destformat == 'gif' || ($destformat == null && $s['mime'] == 'image/gif')) {
						$result = imagegif($tmp, $path, 7);
					} else {
						$result = imagepng($tmp, $path, 7);
					}

					imagedestroy($img);
					imagedestroy($tmp);

					return $result ? $path : false;

				}
				break;
		}

		return false;
	}

	/**
	 * Put image to square
	 *
	 * @param  string   $path               image file
	 * @param  int      $width              square width
	 * @param  int      $height             square height
	 * @param  int	    $align              reserved
	 * @param  int 	    $valign             reserved
	 * @param  string   $bgcolor            square background color in #rrggbb format
	 * @param  string   $destformat         image destination format
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 * @author Alexey Sukhotin
	 **/
		protected function imgSquareFit($path, $width, $height, $align = 'center', $valign = 'middle', $bgcolor = '#0000ff', $destformat = null) {
		if (($s = @getimagesize($path)) == false) {
			return false;
		}

		$result = false;

		/* Coordinates for image over square aligning */
		$y = ceil(abs($height - $s[1]) / 2); 
		$x = ceil(abs($width - $s[0]) / 2);
    
		switch ($this->imgLib) {
			case 'imagick':
				try {
					$img = new imagick($path);
				} catch (Exception $e) {
					return false;
				}

				$img1 = new Imagick();
				$img1->newImage($width, $height, new ImagickPixel($bgcolor));
				$img1->setImageColorspace($img->getImageColorspace());
				$img1->setImageFormat($destformat != null ? $destformat : $img->getFormat());
				$img1->compositeImage( $img, imagick::COMPOSITE_OVER, $x, $y );
				$result = $img1->writeImage($path);
				return $result ? $path : false;

				break;

			case 'gd':
				if ($s['mime'] == 'image/jpeg') {
					$img = imagecreatefromjpeg($path);
				} elseif ($s['mime'] == 'image/png') {
					$img = imagecreatefrompng($path);
				} elseif ($s['mime'] == 'image/gif') {
					$img = imagecreatefromgif($path);
				} elseif ($s['mime'] == 'image/xbm') {
					$img = imagecreatefromxbm($path);
				}

				if ($img &&  false != ($tmp = imagecreatetruecolor($width, $height))) {

					if ($bgcolor == 'transparent') {
						list($r, $g, $b) = array(0, 0, 255);
					} else {
						list($r, $g, $b) = sscanf($bgcolor, "#%02x%02x%02x");
					}

					$bgcolor1 = imagecolorallocate($tmp, $r, $g, $b);
						
					if ($bgcolor == 'transparent') {
						$bgcolor1 = imagecolortransparent($tmp, $bgcolor1);
					}

					imagefill($tmp, 0, 0, $bgcolor1);

					if (!imagecopy($tmp, $img, $x, $y, 0, 0, $s[0], $s[1])) {
						return false;
					}

					if ($destformat == 'jpg'  || ($destformat == null && $s['mime'] == 'image/jpeg')) {
						$result = imagejpeg($tmp, $path, 100);
					} else if ($destformat == 'gif' || ($destformat == null && $s['mime'] == 'image/gif')) {
						$result = imagegif($tmp, $path, 7);
					} else {
						$result = imagepng($tmp, $path, 7);
					}

					imagedestroy($img);
					imagedestroy($tmp);

					return $result ? $path : false;
				}
				break;
		}

		return false;
	}

	/**
	 * Rotate image
	 *
	 * @param  string   $path               image file
	 * @param  int      $degree             rotete degrees
	 * @param  string   $bgcolor            square background color in #rrggbb format
	 * @param  string   $destformat         image destination format
	 * @return string|false
	 * @author nao-pon
	 * @author Troex Nevelin
	 **/
	protected function imgRotate($path, $degree, $bgcolor = '#ffffff', $destformat = null) {
		if (($s = @getimagesize($path)) == false) {
			return false;
		}

		$result = false;

		switch ($this->imgLib) {
			case 'imagick':
				try {
					$img = new imagick($path);
				} catch (Exception $e) {
					return false;
				}

				$img->rotateImage(new ImagickPixel($bgcolor), $degree);
				$result = $img->writeImage($path);
				return $result ? $path : false;

				break;

			case 'gd':
				if ($s['mime'] == 'image/jpeg') {
					$img = imagecreatefromjpeg($path);
				} elseif ($s['mime'] == 'image/png') {
					$img = imagecreatefrompng($path);
				} elseif ($s['mime'] == 'image/gif') {
					$img = imagecreatefromgif($path);
				} elseif ($s['mime'] == 'image/xbm') {
					$img = imagecreatefromxbm($path);
				}

				$degree = 360 - $degree;
				list($r, $g, $b) = sscanf($bgcolor, "#%02x%02x%02x");
				$bgcolor = imagecolorallocate($img, $r, $g, $b);
				$tmp = imageRotate($img, $degree, (int)$bgcolor);

				if ($destformat == 'jpg' || ($destformat == null && $s['mime'] == 'image/jpeg')) {
					$result = imagejpeg($tmp, $path, 100);
				} else if ($destformat == 'gif' || ($destformat == null && $s['mime'] == 'image/gif')) {
					$result = imagegif($tmp, $path, 7);
				} else {
					$result = imagepng($tmp, $path, 7);
				}

				imageDestroy($img);
				imageDestroy($tmp);

				return $result ? $path : false;

				break;
		}

		return false;
	}

	/**
	 * Execute shell command
	 *
	 * @param  string  $command       command line
	 * @param  array   $output        stdout strings
	 * @param  array   $return_var    process exit code
	 * @param  array   $error_output  stderr strings
	 * @return int     exit code
	 * @author Alexey Sukhotin
	 **/
	protected function procExec($command , array &$output = null, &$return_var = -1, array &$error_output = null) {

		$descriptorspec = array(
			0 => array("pipe", "r"),  // stdin
			1 => array("pipe", "w"),  // stdout
			2 => array("pipe", "w")   // stderr
		);

		$process = proc_open($command, $descriptorspec, $pipes, null, null);

		if (is_resource($process)) {

			fclose($pipes[0]);

			$tmpout = '';
			$tmperr = '';

			$output = stream_get_contents($pipes[1]);
			$error_output = stream_get_contents($pipes[2]);

			fclose($pipes[1]);
			fclose($pipes[2]);
			$return_var = proc_close($process);


		}
		
		return $return_var;
		
	}
	
	/**
	 * Remove thumbnail
	 *
	 * @param  string  $path  file path
	 * @return void
	 * @author Dmitry (dio) Levashov
	 **/
	protected function rmTmb($tmb) {
		$tmb = $this->tmbPath.DIRECTORY_SEPARATOR.$tmb;
		file_exists($tmb) && @unlink($tmb);
		clearstatcache();
	}

	/*********************** misc *************************/
	
	/**
	 * Return smart formatted date
	 *
	 * @param  int     $ts  file timestamp
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	protected function formatDate($ts) {
		if ($ts > $this->today) {
			return 'Today '.date($this->options['timeFormat'], $ts);
		}
		
		if ($ts > $this->yesterday) {
			return 'Yesterday '.date($this->options['timeFormat'], $ts);
		} 
		
		return date($this->options['dateFormat'], $ts);
	}

	/**
	* Find position of first occurrence of string in a string with multibyte support
	*
	* @param  string  $haystack  The string being checked.
	* @param  string  $needle    The string to find in haystack.
	* @param  int     $offset    The search offset. If it is not specified, 0 is used.
	* @return int|bool
	* @author Alexey Sukhotin
	**/
	protected function stripos($haystack , $needle , $offset = 0) {
		if (function_exists('mb_stripos')) {
			return mb_stripos($haystack , $needle , $offset);
		} else if (function_exists('mb_strtolower') && function_exists('mb_strpos')) {
			return mb_strpos(mb_strtolower($haystack), mb_strtolower($needle), $offset);
		} 
		return stripos($haystack , $needle , $offset);
	}

	/**==================================* abstract methods *====================================**/
	
	/*********************** paths/urls *************************/
	
	/**
	 * Return parent directory path
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _dirname($path);

	/**
	 * Return file name
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _basename($path);

	/**
	 * Join dir name and file name and return full path.
	 * Some drivers (db) use int as path - so we give to concat path to driver itself
	 *
	 * @param  string  $dir   dir path
	 * @param  string  $name  file name
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _joinPath($dir, $name);

	/**
	 * Return normalized path 
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _normpath($path);

	/**
	 * Return file path related to root dir
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _relpath($path);
	
	/**
	 * Convert path related to root dir into real path
	 *
	 * @param  string  $path  rel file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _abspath($path);
	
	/**
	 * Return fake path started from root dir.
	 * Required to show path on client side.
	 *
	 * @param  string  $path  file path
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _path($path);
	
	/**
	 * Return true if $path is children of $parent
	 *
	 * @param  string  $path    path to check
	 * @param  string  $parent  parent path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _inpath($path, $parent);
	
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
	abstract protected function _stat($path);
	

	/***************** file stat ********************/

		
	/**
	 * Return true if path is dir and has at least one childs directory
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _subdirs($path);
	
	/**
	 * Return object width and height
	 * Ususaly used for images, but can be realize for video etc...
	 *
	 * @param  string  $path  file path
	 * @param  string  $mime  file mime type
	 * @return string
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _dimensions($path, $mime);
	
	/******************** file/dir content *********************/

	/**
	 * Return files list in directory
	 *
	 * @param  string  $path  dir path
	 * @return array
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _scandir($path);
	
	/**
	 * Open file and return file pointer
	 *
	 * @param  string  $path  file path
	 * @param  bool    $write open file for writing
	 * @return resource|false
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _fopen($path, $mode="rb");
	
	/**
	 * Close opened file
	 * 
	 * @param  resource  $fp    file pointer
	 * @param  string    $path  file path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _fclose($fp, $path='');
	
	/********************  file/dir manipulations *************************/
	
	/**
	 * Create dir and return created dir path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new directory name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _mkdir($path, $name);
	
	/**
	 * Create file and return it's path or false on failed
	 *
	 * @param  string  $path  parent dir path
	 * @param string  $name  new file name
	 * @return string|bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _mkfile($path, $name);
	
	/**
	 * Create symlink
	 *
	 * @param  string  $source     file to link to
	 * @param  string  $targetDir  folder to create link in
	 * @param  string  $name       symlink name
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _symlink($source, $targetDir, $name);
	
	/**
	 * Copy file into another file (only inside one volume)
	 *
	 * @param  string  $source  source file path
	 * @param  string  $target  target dir path
	 * @param  string  $name    file name
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _copy($source, $targetDir, $name);
	
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
	abstract protected function _move($source, $targetDir, $name);
	
	/**
	 * Remove file
	 *
	 * @param  string  $path  file path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _unlink($path);

	/**
	 * Remove dir
	 *
	 * @param  string  $path  dir path
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _rmdir($path);

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
	abstract protected function _save($fp, $dir, $name, $mime, $w, $h);
	
	/**
	 * Get file contents
	 *
	 * @param  string  $path  file path
	 * @return string|false
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _getContents($path);
	
	/**
	 * Write a string to a file
	 *
	 * @param  string  $path     file path
	 * @param  string  $content  new file content
	 * @return bool
	 * @author Dmitry (dio) Levashov
	 **/
	abstract protected function _filePutContents($path, $content);

	/**
	 * Extract files from archive
	 *
	 * @param  string  $path file path
	 * @param  array   $arc  archiver options
	 * @return bool
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	abstract protected function _extract($path, $arc);

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
	abstract protected function _archive($dir, $files, $name, $arc);

	/**
	 * Detect available archivers
	 *
	 * @return void
	 * @author Dmitry (dio) Levashov, 
	 * @author Alexey Sukhotin
	 **/
	abstract protected function _checkArchivers();
	
} // END class
