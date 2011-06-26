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
		'csv'   => 'text/x-comma-separated-values',
		'bin'   => 'application/macbinary',
		'dms'   => 'application/octet-stream',
		'lha'   => 'application/octet-stream',
		'lzh'   => 'application/octet-stream',
		'exe'   => 'application/octet-stream',
		'class' => 'application/octet-stream',
		'psd'   => 'application/x-photoshop',
		'so'    => 'application/octet-stream',
		'sea'   => 'application/octet-stream',
		'dll'   => 'application/octet-stream',
		'oda'   => 'application/oda',
		'pdf'   => 'application/pdf',
		'ai'    => 'application/postscript',
		'eps'   => 'application/postscript',
		'ps'    => 'application/postscript',
		'smi'   => 'application/smil',
		'smil'  => 'application/smil',
		'mif'   => 'application/vnd.mif',
		'xls'   => 'application/excel',
		'ppt'   => 'application/powerpoint',
		'wbxml' => 'application/wbxml',
		'wmlc'  => 'application/wmlc',
		'dcr'   => 'application/x-director',
		'dir'   => 'application/x-director',
		'dxr'   => 'application/x-director',
		'dvi'   => 'application/x-dvi',
		'gtar'  => 'application/x-gtar',
		'gz'    => 'application/x-gzip',
		'php'   => 'application/x-httpd-php',
		'php4'  => 'application/x-httpd-php',
		'php3'  => 'application/x-httpd-php',
		'phtml' => 'application/x-httpd-php',
		'phps'  => 'application/x-httpd-php-source',
		'js'    => 'application/x-javascript',
		'swf'   => 'application/x-shockwave-flash',
		'sit'   => 'application/x-stuffit',
		'tar'   => 'application/x-tar',
		'tgz'   => 'application/x-tar',
		'xhtml' => 'application/xhtml+xml',
		'xht'   => 'application/xhtml+xml',
		'zip'   => 'application/x-zip',
		'mid'   => 'audio/midi',
		'midi'  => 'audio/midi',
		'mpga'  => 'audio/mpeg',
		'mp2'   => 'audio/mpeg',
		'mp3'   => 'audio/mpeg',
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
		'jpeg'  => 'image/jpeg',
		'jpg'   => 'image/jpeg',
		'jpe'   => 'image/jpeg',
		'png'   => 'image/png',
		'tiff'  => 'image/tiff',
		'tif'   => 'image/tiff',
		'css'   => 'text/css',
		'html'  => 'text/html',
		'htm'   => 'text/html',
		'shtml' => 'text/html',
		'txt'   => 'text/plain',
		'text'  => 'text/plain',
		'log'   => 'text/plain',
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
		'word'  => 'application/msword',
		'xl'    => 'application/excel',
		'eml'   => 'message/rfc822'
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
		return (array_key_exists($extension, static::$mimes)) ? static::$mimes[$extension] : $default;
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