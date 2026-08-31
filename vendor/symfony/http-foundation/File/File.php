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
use Symfony\Component\Mime\MimeTypes;

/**
 * A file in the file system.
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class File extends \SplFileInfo
{
    /**
     * Constructs a new file from the given path.
     *
     * @param string $path      The path to the file
     * @param bool   $checkPath Whether to check the path or not
     *
     * @throws FileNotFoundException If the given path is not a file
     */
    public function __construct(string $path, bool $checkPath = true)
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
     * This method uses the mime type as guessed by getMimeType()
     * to guess the file extension.
     *
     * @see MimeTypes
     * @see getMimeType()
     */
    public function guessExtension(): ?string
    {
        if (!class_exists(MimeTypes::class)) {
            throw new \LogicException('You cannot guess the extension as the Mime component is not installed. Try running "composer require symfony/mime".');
        }

        return MimeTypes::getDefault()->getExtensions($this->getMimeType())[0] ?? null;
    }

    /**
     * Returns the mime type of the file.
     *
     * The mime type is guessed using a MimeTypeGuesserInterface instance,
     * which uses finfo_file() then the "file" system binary,
     * depending on which of those are available.
     *
     * @see MimeTypes
     */
    public function getMimeType(): ?string
    {
        if (!class_exists(MimeTypes::class)) {
            throw new \LogicException('You cannot guess the mime type as the Mime component is not installed. Try running "composer require symfony/mime".');
        }

        return MimeTypes::getDefault()->guessMimeType($this->getPathname());
    }

    /**
     * Moves the file to a new location.
     *
     * @throws FileException if the target file could not be created
     */
    public function move(string $directory, ?string $name = null): self
    {
        $target = $this->getTargetFile($directory, $name);

        set_error_handler(static function ($type, $msg) use (&$error) { $error = $msg; });
        try {
            $renamed = rename($this->getPathname(), $target);
        } finally {
            restore_error_handler();
        }
        if (!$renamed) {
            throw new FileException(\sprintf('Could not move the file "%s" to "%s" (%s).', $this->getPathname(), $target, strip_tags($error)));
        }

        @chmod($target, 0o666 & ~umask());

        return $target;
    }

    public function getContent(): string
    {
        $content = file_get_contents($this->getPathname());

        if (false === $content) {
            throw new FileException(\sprintf('Could not get the content of the file "%s".', $this->getPathname()));
        }

        return $content;
    }

    protected function getTargetFile(string $directory, ?string $name = null): self
    {
        if (!is_dir($directory) && !@mkdir($directory, 0o777, true) && !is_dir($directory)) {
            if (is_file($directory)) {
                throw new FileException(\sprintf('Unable to create the "%s" directory: a similarly-named file exists.', $directory));
            }
            throw new FileException(\sprintf('Unable to create the "%s" directory.', $directory));
        } elseif (!is_writable($directory)) {
            throw new FileException(\sprintf('Unable to write in the "%s" directory.', $directory));
        }

        $target = rtrim($directory, '/\\').\DIRECTORY_SEPARATOR.(null === $name ? $this->getBasename() : $this->getName($name));

        return new self($target, false);
    }

    /**
     * Returns locale independent base name of the given path.
     */
    protected function getName(string $name): string
    {
        $originalName = str_replace('\\', '/', $name);
        $pos = strrpos($originalName, '/');

        return false === $pos ? $originalName : substr($originalName, $pos + 1);
    }
}
