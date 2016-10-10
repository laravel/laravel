<?php namespace Illuminate\Filesystem;

use FilesystemIterator;
use Symfony\Component\Finder\Finder;

class FileNotFoundException extends \Exception {}

class Filesystem {

	/**
	 * Determine if a file exists.
	 *
	 * @param  string  $path
	 * @return bool
	 */
	public function exists($path)
	{
		return file_exists($path);
	}

	/**
	 * Get the contents of a file.
	 *
	 * @param  string  $path
	 * @return string
	 *
	 * @throws FileNotFoundException
	 */
	public function get($path)
	{
		if ($this->isFile($path)) return file_get_contents($path);

		throw new FileNotFoundException("File does not exist at path {$path}");
	}

	/**
	 * Get the returned value of a file.
	 *
	 * @param  string  $path
	 * @return mixed
	 *
	 * @throws FileNotFoundException
	 */
	public function getRequire($path)
	{
		if ($this->isFile($path)) return require $path;

		throw new FileNotFoundException("File does not exist at path {$path}");
	}

	/**
	 * Require the given file once.
	 *
	 * @param  string  $file
	 * @return mixed
	 */
	public function requireOnce($file)
	{
		require_once $file;
	}

	/**
	 * Write the contents of a file.
	 *
	 * @param  string  $path
	 * @param  string  $contents
	 * @return int
	 */
	public function put($path, $contents)
	{
		return file_put_contents($path, $contents);
	}

	/**
	 * Prepend to a file.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public function prepend($path, $data)
	{
		if ($this->exists($path))
		{
			return $this->put($path, $data.$this->get($path));
		}
		else
		{
			return $this->put($path, $data);
		}
	}

	/**
	 * Append to a file.
	 *
	 * @param  string  $path
	 * @param  string  $data
	 * @return int
	 */
	public function append($path, $data)
	{
		return file_put_contents($path, $data, FILE_APPEND);
	}

	/**
	 * Delete the file at a given path.
	 *
	 * @param  string|array  $paths
	 * @return bool
	 */
	public function delete($paths)
	{
		$paths = is_array($paths) ? $paths : func_get_args();

		$success = true;

		foreach ($paths as $path) { if ( ! @unlink($path)) $success = false; }

		return $success;
	}

	/**
	 * Move a file to a new location.
	 *
	 * @param  string  $path
	 * @param  string  $target
	 * @return bool
	 */
	public function move($path, $target)
	{
		return rename($path, $target);
	}

	/**
	 * Copy a file to a new location.
	 *
	 * @param  string  $path
	 * @param  string  $target
	 * @return bool
	 */
	public function copy($path, $target)
	{
		return copy($path, $target);
	}

	/**
	 * Extract the file extension from a file path.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function extension($path)
	{
		return pathinfo($path, PATHINFO_EXTENSION);
	}

	/**
	 * Get the file type of a given file.
	 *
	 * @param  string  $path
	 * @return string
	 */
	public function type($path)
	{
		return filetype($path);
	}

	/**
	 * Get the file size of a given file.
	 *
	 * @param  string  $path
	 * @return int
	 */
	public function size($path)
	{
		return filesize($path);
	}

	/**
	 * Get the file's last modification time.
	 *
	 * @param  string  $path
	 * @return int
	 */
	public function lastModified($path)
	{
		return filemtime($path);
	}

	/**
	 * Determine if the given path is a directory.
	 *
	 * @param  string  $directory
	 * @return bool
	 */
	public function isDirectory($directory)
	{
		return is_dir($directory);
	}

	/**
	 * Determine if the given path is writable.
	 *
	 * @param  string  $path
	 * @return bool
	 */
	public function isWritable($path)
	{
		return is_writable($path);
	}

	/**
	 * Determine if the given path is a file.
	 *
	 * @param  string  $file
	 * @return bool
	 */
	public function isFile($file)
	{
		return is_file($file);
	}

	/**
	 * Find path names matching a given pattern.
	 *
	 * @param  string  $pattern
	 * @param  int     $flags
	 * @return array
	 */
	public function glob($pattern, $flags = 0)
	{
		return glob($pattern, $flags);
	}

	/**
	 * Get an array of all files in a directory.
	 *
	 * @param  string  $directory
	 * @return array
	 */
	public function files($directory)
	{
		$glob = glob($directory.'/*');

		if ($glob === false) return array();

		// To get the appropriate files, we'll simply glob the directory and filter
		// out any "files" that are not truly files so we do not end up with any
		// directories in our list, but only true files within the directory.
		return array_filter($glob, function($file)
		{
			return filetype($file) == 'file';
		});
	}

	/**
	 * Get all of the files from the given directory (recursive).
	 *
	 * @param  string  $directory
	 * @return array
	 */
	public function allFiles($directory)
	{
		return iterator_to_array(Finder::create()->files()->in($directory), false);
	}

	/**
	 * Get all of the directories within a given directory.
	 *
	 * @param  string  $directory
	 * @return array
	 */
	public function directories($directory)
	{
		$directories = array();

		foreach (Finder::create()->in($directory)->directories()->depth(0) as $dir)
		{
			$directories[] = $dir->getPathname();
		}

		return $directories;
	}

	/**
	 * Create a directory.
	 *
	 * @param  string  $path
	 * @param  int     $mode
	 * @param  bool    $recursive
	 * @param  bool    $force
	 * @return bool
	 */
	public function makeDirectory($path, $mode = 0777, $recursive = false, $force = false)
	{
		if ($force)
		{
			return @mkdir($path, $mode, $recursive);
		}
		else
		{
			return mkdir($path, $mode, $recursive);
		}
	}

	/**
	 * Copy a directory from one location to another.
	 *
	 * @param  string  $directory
	 * @param  string  $destination
	 * @param  int     $options
	 * @return bool
	 */
	public function copyDirectory($directory, $destination, $options = null)
	{
		if ( ! $this->isDirectory($directory)) return false;

		$options = $options ?: FilesystemIterator::SKIP_DOTS;

		// If the destination directory does not actually exist, we will go ahead and
		// create it recursively, which just gets the destination prepared to copy
		// the files over. Once we make the directory we'll proceed the copying.
		if ( ! $this->isDirectory($destination))
		{
			$this->makeDirectory($destination, 0777, true);
		}

		$items = new FilesystemIterator($directory, $options);

		foreach ($items as $item)
		{
			// As we spin through items, we will check to see if the current file is actually
			// a directory or a file. When it is actually a directory we will need to call
			// back into this function recursively to keep copying these nested folders.
			$target = $destination.'/'.$item->getBasename();

			if ($item->isDir())
			{
				$path = $item->getPathname();

				if ( ! $this->copyDirectory($path, $target, $options)) return false;
			}

			// If the current items is just a regular file, we will just copy this to the new
			// location and keep looping. If for some reason the copy fails we'll bail out
			// and return false, so the developer is aware that the copy process failed.
			else
			{
				if ( ! $this->copy($item->getPathname(), $target)) return false;
			}
		}

		return true;
	}

	/**
	 * Recursively delete a directory.
	 *
	 * The directory itself may be optionally preserved.
	 *
	 * @param  string  $directory
	 * @param  bool    $preserve
	 * @return bool
	 */
	public function deleteDirectory($directory, $preserve = false)
	{
		if ( ! $this->isDirectory($directory)) return false;

		$items = new FilesystemIterator($directory);

		foreach ($items as $item)
		{
			// If the item is a directory, we can just recurse into the function and
			// delete that sub-director, otherwise we'll just delete the file and
			// keep iterating through each file until the directory is cleaned.
			if ($item->isDir())
			{
				$this->deleteDirectory($item->getPathname());
			}

			// If the item is just a file, we can go ahead and delete it since we're
			// just looping through and waxing all of the files in this directory
			// and calling directories recursively, so we delete the real path.
			else
			{
				$this->delete($item->getPathname());
			}
		}

		if ( ! $preserve) @rmdir($directory);

		return true;
	}

	/**
	 * Empty the specified directory of all files and folders.
	 *
	 * @param  string  $directory
	 * @return bool
	 */
	public function cleanDirectory($directory)
	{
		return $this->deleteDirectory($directory, true);
	}

}
