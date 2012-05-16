<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesser;
use Symfony\Component\HttpFoundation\File\MimeType\ExtensionGuesser;

/**
 * A file in the file system.
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony.com>
 *
 * @api
 */
class File extends \SplFileInfo
{
    /**
     * Constructs a new file from the given path.
     *
     * @param string  $path      The path to the file
     * @param Boolean $checkPath Whether to check the path or not
     *
     * @throws FileNotFoundException If the given path is not a file
     *
     * @api
     */
    public function __construct($path, $checkPath = true)
    {
        if ($checkPath && !is_file($path)) {
            throw new FileNotFoundException($path);
        }

        parent::__construct($path);
    }

    /**
     * Returns the extension based on the mime type.
     *
     * If the mime type is unknown, returns null.
     *
     * @return string|null The guessed extension or null if it cannot be guessed
     *
     * @api
     */
    public function guessExtension()
    {
        $type = $this->getMimeType();
        $guesser = ExtensionGuesser::getInstance();

        return $guesser->guess($type);
    }

    /**
     * Returns the mime type of the file.
     *
     * The mime type is guessed using the functions finfo(), mime_content_type()
     * and the system binary "file" (in this order), depending on which of those
     * is available on the current operating system.
     *
     * @return string|null The guessed mime type (i.e. "application/pdf")
     *
     * @api
     */
    public function getMimeType()
    {
        $guesser = MimeTypeGuesser::getInstance();

        return $guesser->guess($this->getPathname());
    }

    /**
     * Returns the extension of the file.
     *
     * \SplFileInfo::getExtension() is not available before PHP 5.3.6
     *
     * @return string The extension
     *
     * @api
     */
    public function getExtension()
    {
        return pathinfo($this->getBasename(), PATHINFO_EXTENSION);
    }

    /**
     * Moves the file to a new location.
     *
     * @param string $directory The destination folder
     * @param string $name      The new file name
     *
     * @return File A File object representing the new file
     *
     * @throws FileException if the target file could not be created
     *
     * @api
     */
    public function move($directory, $name = null)
    {
        if (!is_dir($directory)) {
            if (false === @mkdir($directory, 0777, true)) {
                throw new FileException(sprintf('Unable to create the "%s" directory', $directory));
            }
        } elseif (!is_writable($directory)) {
            throw new FileException(sprintf('Unable to write in the "%s" directory', $directory));
        }

        $target = $directory.DIRECTORY_SEPARATOR.(null === $name ? $this->getBasename() : basename($name));

        if (!@rename($this->getPathname(), $target)) {
            $error = error_get_last();
            throw new FileException(sprintf('Could not move the file "%s" to "%s" (%s)', $this->getPathname(), $target, strip_tags($error['message'])));
        }

        chmod($target, 0666);

        return new File($target);
    }
}
