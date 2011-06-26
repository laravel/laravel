<?php namespace System;

class File {

	/**
	 * Extensions and their matching MIME types.
	 *
	 * @var array
	 */
	public static $mimes = array(
		'hqx'   => 'application/mac-binhex40',
		'cpt'   => 'application/mac-compactpro',
		'csv'   => array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/octet-stream'),
		'bin'   => 'application/macbinary',
		'dms'   => 'application/octet-stream',
		'lha'   => 'application/octet-stream',
		'lzh'   => 'application/octet-stream',
		'exe'   => array('application/octet-stream', 'application/x-msdownload'),
		'class' => 'application/octet-stream',
		'psd'   => 'application/x-photoshop',
		'so'    => 'application/octet-stream',
		'sea'   => 'application/octet-stream',
		'dll'   => 'application/octet-stream',
		'oda'   => 'application/oda',
		'pdf'   => array('application/pdf', 'application/x-download'),
		'ai'    => 'application/postscript',
		'eps'   => 'application/postscript',
		'ps'    => 'application/postscript',
		'smi'   => 'application/smil',
		'smil'  => 'application/smil',
		'mif'   => 'application/vnd.mif',
		'xls'   => array('application/excel', 'application/vnd.ms-excel', 'application/msexcel'),
		'ppt'   => array('application/powerpoint', 'application/vnd.ms-powerpoint'),
		'wbxml' => 'application/wbxml',
		'wmlc'  => 'application/wmlc',
		'dcr'   => 'application/x-director',
		'dir'   => 'application/x-director',
		'dxr'   => 'application/x-director',
		'dvi'   => 'application/x-dvi',
		'gtar'  => 'application/x-gtar',
		'gz'    => 'application/x-gzip',
		'php'   => array('application/x-httpd-php', 'text/x-php'),
		'php4'  => 'application/x-httpd-php',
		'php3'  => 'application/x-httpd-php',
		'phtml' => 'application/x-httpd-php',
		'phps'  => 'application/x-httpd-php-source',
		'js'    => 'application/x-javascript',
		'swf'   => 'application/x-shockwave-flash',
		'sit'   => 'application/x-stuffit',
		'tar'   => 'application/x-tar',
		'tgz'   => array('application/x-tar', 'application/x-gzip-compressed'),
		'xhtml' => 'application/xhtml+xml',
		'xht'   => 'application/xhtml+xml',
		'zip'   => array('application/x-zip', 'application/zip', 'application/x-zip-compressed'),
		'mid'   => 'audio/midi',
		'midi'  => 'audio/midi',
		'mpga'  => 'audio/mpeg',
		'mp2'   => 'audio/mpeg',
		'mp3'   => array('audio/mpeg', 'audio/mpg', 'audio/mpeg3', 'audio/mp3'),
		'aif'   => 'audio/x-aiff',
		'aiff'  => 'audio/x-aiff',
		'aifc'  => 'audio/x-aiff',
		'ram'   => 'audio/x-pn-realaudio',
		'rm'    => 'audio/x-pn-realaudio',
		'rpm'   => 'audio/x-pn-realaudio-plugin',
		'ra'    => 'audio/x-realaudio',
		'rv'    => 'video/vnd.rn-realvideo',
		'wav'   => 'audio/x-wav',
		'bmp'   => 'image/bmp',
		'gif'   => 'image/gif',
		'jpeg'  => array('image/jpeg', 'image/pjpeg'),
		'jpg'   => array('image/jpeg', 'image/pjpeg'),
		'jpe'   => array('image/jpeg', 'image/pjpeg'),
		'png'   => 'image/png',
		'tiff'  => 'image/tiff',
		'tif'   => 'image/tiff',
		'css'   => 'text/css',
		'html'  => 'text/html',
		'htm'   => 'text/html',
		'shtml' => 'text/html',
		'txt'   => 'text/plain',
		'text'  => 'text/plain',
		'log'   => array('text/plain', 'text/x-log'),
		'rtx'   => 'text/richtext',
		'rtf'   => 'text/rtf',
		'xml'   => 'text/xml',
		'xsl'   => 'text/xml',
		'mpeg'  => 'video/mpeg',
		'mpg'   => 'video/mpeg',
		'mpe'   => 'video/mpeg',
		'qt'    => 'video/quicktime',
		'mov'   => 'video/quicktime',
		'avi'   => 'video/x-msvideo',
		'movie' => 'video/x-sgi-movie',
		'doc'   => 'application/msword',
		'docx'  => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'xlsx'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'word'  => array('application/msword', 'application/octet-stream'),
		'xl'    => 'application/excel',
		'eml'   => 'message/rfc822',
		'json'  => array('application/json', 'text/json'),
	);

	/**
	 * Get the contents of a file.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public static function get($path)
	{
		return file_get_contents($path);
	}

	/**
	 * Write to a file.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public static function put($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX);
	}

	/**
	 * Append to a file.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public static function append($path, $data)
	{
		return file_put_contents($path, $data, LOCK_EX | FILE_APPEND);
	}

	/**
	 * Extract the extension from a file path.
	 * 
	 * @param  string  $path
	 * @return string
	 */
	public static function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Get a file MIME type by extension.
	 *
	 * @param  string  $extension
	 * @param  string  $default
	 * @return string
	 */
	public static function mime($extension, $default = 'application/octet-stream')
	{
		if (array_key_exists($extension, static::$mimes))
		{
			return (is_array(static::$mimes[$extension])) ? static::$mimes[$extension][0] : static::$mimes[$extension];
		}

		return $default;
	}

	/**
	 * Determine if a file is a given type.
	 *
	 * The Fileinfo PHP extension will be used to determine the MIME
	 * type of the file. Any extension in the File::$mimes array may
	 * be passed as a type.
	 */
	public static function is($extension, $path)
	{
		if ( ! array_key_exists($extension, static::$mimes))
		{
			throw new \Exception("File extension [$extension] is unknown. Cannot determine file type.");
		}

		$mime = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $path);

		return (is_array(static::$mimes[$extension])) ? in_array($mime, static::$mimes[$extension]) : $mime === static::$mimes[$extension];
	}

	/**
	 * Create a response that will force a file to be downloaded.
	 *
	 * @param  string  $path
	 * @param  string  $name
	 * @return Response
	 */
	public static function download($path, $name = null)
	{
		if (is_null($name))
		{
			$name = basename($path);
		}

		$response = Response::make(static::get($path));

		$response->header('Content-Description', 'File Transfer');
		$response->header('Content-Type', static::mime(static::extension($path)));
		$response->header('Content-Disposition', 'attachment; filename="'.$name.'"');
		$response->header('Content-Transfer-Encoding', 'binary');
		$response->header('Expires', 0);
		$response->header('Cache-Control', 'must-revalidate, post-check=0, pre-check=0');
		$response->header('Pragma', 'public');
		$response->header('Content-Length', filesize($path));

		return $response;
	}

	/**
	 * Move an uploaded file to storage.
	 *
	 * @param  string  $key
	 * @param  string  $path
	 * @return bool
	 */
	public static function upload($key, $path)
	{
		if ( ! array_key_exists($key, $_FILES))
		{
			return false;
		}

		return move_uploaded_file($_FILES[$key]['tmp_name'], $path);
	}
}