<?php namespace System;

class File {
	
	/**
	 * Get a file's extension. 
	 * 
	 * @param  string $path
	 * @return string
	 */
	public static function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

}