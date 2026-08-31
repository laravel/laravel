<?php declare(strict_types=1);

/**
 * This file is part of the Nette Framework (https://nette.org)
 * Copyright (c) 2004 David Grudl (https://davidgrudl.com)
 */

namespace Nette\Utils;

use Nette;
use const DIRECTORY_SEPARATOR;


/**
 * Represents the file or directory returned by the Finder.
 * @internal do not create instances directly
 */
final class FileInfo extends \SplFileInfo
{
	public function __construct(
		string $file,
		private readonly string $relativePath = '',
	) {
		parent::__construct($file);
		$this->setInfoClass(self::class);
	}


	/**
	 * Returns the relative directory path.
	 */
	public function getRelativePath(): string
	{
		return $this->relativePath;
	}


	/**
	 * Returns the relative path including file name.
	 */
	public function getRelativePathname(): string
	{
		return ($this->relativePath === '' ? '' : $this->relativePath . DIRECTORY_SEPARATOR)
			. $this->getBasename();
	}


	/**
	 * Returns the contents of the file.
	 * @throws Nette\IOException
	 */
	public function read(): string
	{
		return FileSystem::read($this->getPathname());
	}


	/**
	 * Writes the contents to the file.
	 * @throws Nette\IOException
	 */
	public function write(string $content): void
	{
		FileSystem::write($this->getPathname(), $content);
	}
}
