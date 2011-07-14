<?php

class Utils {

	/**
	 * Recursively remove a directory.
	 *
	 * @param  string  $directory
	 * @return void
	 */
	public static function rrmdir($directory)
	{
		if (is_dir($directory)) 
		{ 
		 	$objects = scandir($directory);

		 	foreach ($objects as $object) 
		 	{ 
		   		if ($object != "." && $object != "..") 
		   		{ 
		     		if (filetype($directory."/".$object) == "dir") static::rrmdir($directory."/".$object); else unlink($directory."/".$object); 
		   		} 
		 	} 

		 	reset($objects); 
		 	rmdir($directory); 
		} 
	}

}