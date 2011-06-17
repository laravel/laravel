<?php namespace System;

class Download extends File {

	/**
	 * Create a download response. The proper headers will be sent
	 * to the browser to force the file to be downloaded.
	 *
	 * 
	 * @deprecated For older apps, use File class
	 * 
	 * @param  string  $path
	 * @param  string  $name
	 * @return Response
	 */
	public static function file($path, $name = null)
	{
		return parent::download($path, $name);
	}

}