<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime;

use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Exception\LogicException;

/**
 * Guesses the MIME type with the binary "file" (only available on *nix).
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class FileBinaryMimeTypeGuesser implements MimeTypeGuesserInterface
{
    /**
     * The $cmd pattern must contain a "%s" string that will be replaced
     * with the file name to guess.
     *
     * The command output must start with the MIME type of the file.
     *
     * @param string $cmd The command to run to get the MIME type of a file
     */
    public function __construct(
        private string $cmd = 'file -b --mime -- %s 2>/dev/null',
    ) {
    }

    public function isGuesserSupported(): bool
    {
        static $supported = null;

        if (null !== $supported) {
            return $supported;
        }

        if ('\\' === \DIRECTORY_SEPARATOR || !\function_exists('shell_exec') || !\function_exists('escapeshellarg')) {
            return $supported = false;
        }

        return $supported = '' !== trim(shell_exec('command -v file') ?: '');
    }

    public function guessMimeType(string $path): ?string
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InvalidArgumentException(\sprintf('The "%s" file does not exist or is not readable.', $path));
        }

        if (!$this->isGuesserSupported()) {
            throw new LogicException(\sprintf('The "%s" guesser is not supported.', __CLASS__));
        }

        // need to use --mime instead of -i. see #6641
        $type = trim(shell_exec(\sprintf($this->cmd, escapeshellarg((str_starts_with($path, '-') ? './' : '').$path))) ?: '');

        if (!preg_match('#^([a-z0-9\-]+/[a-z0-9\-\+\.]+)#i', $type, $match)) {
            // it's not a type, but an error message
            return null;
        }

        return $match[1];
    }
}
