<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use function array_pop, chmod, decoct, dirname, end, fclose, file_exists, file_get_contents, file_put_contents, fopen, implode, is_dir, is_file, is_link, mkdir, preg_match, preg_split, realpath, rename, rmdir, rtrim, sprintf, str_replace, stream_copy_to_stream, stream_is_local, strtr, uniqid, unlink, usleep;
use const DIRECTORY_SEPARATOR;


/**
 * File system tool.
 */
final class FileSystem
{
	/**
	 * Creates a directory if it does not exist, including parent directories.
	 * @throws Nette\IOException  on error occurred
	 */
	public static function createDir(string $dir, int $mode = 0o777): void
	{
		if (!is_dir($dir) && !@mkdir($dir, $mode, recursive: true) && !is_dir($dir)) { // @ - dir may already exist
			throw new Nette\IOException(sprintf(
				"Unable to create directory '%s' with mode %s. %s",
				self::normalizePath($dir),
				decoct($mode),
				Helpers::getLastError(),
			));
		}
	}


	/**
	 * Copies a file or an entire directory. Overwrites existing files and directories by default.
	 * @throws Nette\IOException  on error occurred
	 * @throws Nette\InvalidStateException  if $overwrite is set to false and destination already exists
	 */
	public static function copy(string $origin, string $target, bool $overwrite = true): void
	{
		if (stream_is_local($origin) && !file_exists($origin)) {
			throw new Nette\IOException(sprintf("File or directory '%s' not found.", self::normalizePath($origin)));

		} elseif (!$overwrite && file_exists($target)) {
			throw new Nette\InvalidStateException(sprintf("File or directory '%s' already exists.", self::normalizePath($target)));

		} elseif (is_dir($origin)) {
			static::createDir($target);
			foreach (new \FilesystemIterator($target) as $item) {
				\assert($item instanceof \SplFileInfo);
				static::delete($item->getPathname());
			}

			foreach ($iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($origin, \RecursiveDirectoryIterator::SKIP_DOTS), \RecursiveIteratorIterator::SELF_FIRST) as $item) {
				if ($item->isDir()) {
					static::createDir($target . '/' . $iterator->getSubPathname());
				} else {
					static::copy($item->getPathname(), $target . '/' . $iterator->getSubPathname());
				}
			}
		} else {
			static::createDir(dirname($target));
			if (@stream_copy_to_stream(static::open($origin, 'rb'), static::open($target, 'wb')) === false) { // @ is escalated to exception
				throw new Nette\IOException(sprintf(
					"Unable to copy file '%s' to '%s'. %s",
					self::normalizePath($origin),
					self::normalizePath($target),
					Helpers::getLastError(),
				));
			}
		}
	}


	/**
	 * Opens file and returns resource.
	 * @return resource
	 * @throws Nette\IOException  on error occurred
	 */
	public static function open(string $path, string $mode)
	{
		$f = @fopen($path, $mode); // @ is escalated to exception
		if (!$f) {
			throw new Nette\IOException(sprintf(
				"Unable to open file '%s'. %s",
				self::normalizePath($path),
				Helpers::getLastError(),
			));
		}
		return $f;
	}


	/**
	 * Deletes a file or an entire directory if exists. If the directory is not empty, it deletes its contents first.
	 * @throws Nette\IOException  on error occurred
	 */
	public static function delete(string $path): void
	{
		if (is_file($path) || is_link($path)) {
			$func = DIRECTORY_SEPARATOR === '\\' && is_dir($path) ? 'rmdir' : 'unlink';
			if (!@$func($path)) { // @ is escalated to exception
				throw new Nette\IOException(sprintf(
					"Unable to delete '%s'. %s",
					self::normalizePath($path),
					Helpers::getLastError(),
				));
			}
		} elseif (is_dir($path)) {
			foreach (new \FilesystemIterator($path) as $item) {
				\assert($item instanceof \SplFileInfo);
				static::delete($item->getPathname());
			}

			if (!@rmdir($path)) { // @ is escalated to exception
				throw new Nette\IOException(sprintf(
					"Unable to delete directory '%s'. %s",
					self::normalizePath($path),
					Helpers::getLastError(),
				));
			}
		}
	}


	/**
	 * Renames or moves a file or a directory. Overwrites existing files and directories by default.
	 * @throws Nette\IOException  on error occurred
	 * @throws Nette\InvalidStateException  if $overwrite is set to false and destination already exists
	 */
	public static function rename(string $origin, string $target, bool $overwrite = true): void
	{
		if (!$overwrite && file_exists($target)) {
			throw new Nette\InvalidStateException(sprintf("File or directory '%s' already exists.", self::normalizePath($target)));

		} elseif (!file_exists($origin)) {
			throw new Nette\IOException(sprintf("File or directory '%s' not found.", self::normalizePath($origin)));

		} else {
			static::createDir(dirname($target));
			if (realpath($origin) !== realpath($target)) {
				static::delete($target);
			}

			if (!@rename($origin, $target)) { // @ is escalated to exception
				throw new Nette\IOException(sprintf(
					"Unable to rename file or directory '%s' to '%s'. %s",
					self::normalizePath($origin),
					self::normalizePath($target),
					Helpers::getLastError(),
				));
			}
		}
	}


	/**
	 * Reads the content of a file.
	 * @throws Nette\IOException  on error occurred
	 */
	public static function read(string $file): string
	{
		$content = @file_get_contents($file); // @ is escalated to exception
		if ($content === false) {
			throw new Nette\IOException(sprintf(
				"Unable to read file '%s'. %s",
				self::normalizePath($file),
				Helpers::getLastError(),
			));
		}

		return $content;
	}


	/**
	 * Reads the file content line by line. Because it reads continuously as we iterate over the lines,
	 * it is possible to read files larger than the available memory.
	 * @return \Generator<int, string>
	 * @throws Nette\IOException  on error occurred
	 */
	public static function readLines(string $file, bool $stripNewLines = true): \Generator
	{
		return (function ($f) use ($file, $stripNewLines) {
			$counter = 0;
			do {
				$line = Callback::invokeSafe('fgets', [$f], fn($error) => throw new Nette\IOException(sprintf(
					"Unable to read file '%s'. %s",
					self::normalizePath($file),
					$error,
				)));
				if ($line === false) {
					fclose($f);
					break;
				}
				if ($stripNewLines) {
					$line = rtrim($line, "\r\n");
				}

				yield $counter++ => $line;

			} while (true);
		})(static::open($file, 'r'));
	}


	/**
	 * Writes the string to a file. Creates the parent directory if it does not exist. Pass null as $mode to skip chmod.
	 * @throws Nette\IOException  on error occurred
	 */
	public static function write(string $file, string $content, ?int $mode = 0o666): void
	{
		static::createDir(dirname($file));
		if (@file_put_contents($file, $content) === false) { // @ is escalated to exception
			throw new Nette\IOException(sprintf(
				"Unable to write file '%s'. %s",
				self::normalizePath($file),
				Helpers::getLastError(),
			));
		}

		if ($mode !== null && !@chmod($file, $mode)) { // @ is escalated to exception
			throw new Nette\IOException(sprintf(
				"Unable to chmod file '%s' to mode %s. %s",
				self::normalizePath($file),
				decoct($mode),
				Helpers::getLastError(),
			));
		}
	}


	/**
	 * Writes the string to a file atomically: the content is written to a temporary file, which then replaces
	 * the target, so a concurrent reader never sees the file partially written or truncated.
	 * Creates the parent directory if it does not exist. Pass null as $mode to skip chmod.
	 * @throws Nette\IOException  on error occurred
	 */
	public static function writeAtomic(string $file, string $content, ?int $mode = 0o666): void
	{
		$file = realpath($file) ?: $file; // writes through a symlink to its target, as write() does
		$tmp = $file . '.' . uniqid('', more_entropy: true) . '.tmp';
		try {
			static::write($tmp, $content, $mode);
			// plain rename() is atomic; static::rename() must not be used here, it deletes the target first
			for ($i = 0; !@rename($tmp, $file); $i++) { // @ is escalated to exception
				if (!Helpers::IsWindows || $i >= 20) {
					throw new Nette\IOException(sprintf(
						"Unable to write file '%s'. %s",
						self::normalizePath($file),
						Helpers::getLastError(),
					));
				}
				usleep(5_000); // on Windows, rename fails while the target is open in another process
			}
		} catch (\Throwable $e) {
			@unlink($tmp);
			throw $e;
		}
	}


	/**
	 * Sets file permissions to `$fileMode` or directory permissions to `$dirMode`.
	 * Recursively traverses and sets permissions on the entire contents of the directory as well.
	 * @throws Nette\IOException  on error occurred
	 */
	public static function makeWritable(string $path, int $dirMode = 0o777, int $fileMode = 0o666): void
	{
		if (is_file($path)) {
			if (!@chmod($path, $fileMode)) { // @ is escalated to exception
				throw new Nette\IOException(sprintf(
					"Unable to chmod file '%s' to mode %s. %s",
					self::normalizePath($path),
					decoct($fileMode),
					Helpers::getLastError(),
				));
			}
		} elseif (is_dir($path)) {
			foreach (new \FilesystemIterator($path) as $item) {
				\assert($item instanceof \SplFileInfo);
				static::makeWritable($item->getPathname(), $dirMode, $fileMode);
			}

			if (!@chmod($path, $dirMode)) { // @ is escalated to exception
				throw new Nette\IOException(sprintf(
					"Unable to chmod directory '%s' to mode %s. %s",
					self::normalizePath($path),
					decoct($dirMode),
					Helpers::getLastError(),
				));
			}
		} else {
			throw new Nette\IOException(sprintf("File or directory '%s' not found.", self::normalizePath($path)));
		}
	}


	/**
	 * Determines if the path is absolute.
	 */
	public static function isAbsolute(string $path): bool
	{
		return (bool) preg_match('#([a-z]:)?[/\\\]|[a-z][a-z0-9+.-]*://#Ai', $path);
	}


	/**
	 * Determines whether the string is a valid cross-platform filename without any path information.
	 */
	public static function isValidFilename(string $name): bool
	{
		[$stem] = explode('.', $name, 2);
		return $name !== '' && $name !== '.' && $name !== '..'
			&& !preg_match('#[\x00-\x1F<>:"|?*\\\/]#', $name) // control and reserved characters
			&& !str_ends_with($name, '.') && !str_ends_with($name, ' ') // trailing dots/spaces
			&& !preg_match('#^(CON|PRN|AUX|NUL|COM[1-9]|LPT[1-9])$#i', $stem); // Windows reserved device names
	}


	/**
	 * Normalizes `..` and `.` and directory separators in path.
	 */
	public static function normalizePath(string $path): string
	{
		$parts = $path === '' ? [] : preg_split('~[/\\\]+~', $path);
		$res = [];
		foreach ($parts as $part) {
			if ($part === '..' && $res && end($res) !== '..' && end($res) !== '') {
				array_pop($res);
			} elseif ($part !== '.') {
				$res[] = $part;
			}
		}

		return $res === ['']
			? DIRECTORY_SEPARATOR
			: implode(DIRECTORY_SEPARATOR, $res);
	}


	/**
	 * Joins all segments of the path and normalizes the result.
	 */
	public static function joinPaths(string ...$paths): string
	{
		return self::normalizePath(implode('/', $paths));
	}


	/**
	 * Resolves a path against a base path. If the path is absolute, returns it directly, if it's relative, joins it with the base path.
	 */
	public static function resolvePath(string $basePath, string $path): string
	{
		return match (true) {
			self::isAbsolute($path) => self::platformSlashes($path),
			$path === '' => self::platformSlashes($basePath),
			default => self::joinPaths($basePath, $path),
		};
	}


	/**
	 * Converts backslashes to slashes.
	 */
	public static function unixSlashes(string $path): string
	{
		return strtr($path, '\\', '/');
	}


	/**
	 * Converts slashes to platform-specific directory separators.
	 */
	public static function platformSlashes(string $path): string
	{
		return DIRECTORY_SEPARATOR === '/'
			? strtr($path, '\\', '/')
			: str_replace(':\\\\', '://', strtr($path, '/', '\\')); // protocol://
	}
}
