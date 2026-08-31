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

/**
 * Guesses the MIME type of a file.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface MimeTypeGuesserInterface
{
    /**
     * Returns true if this guesser is supported.
     */
    public function isGuesserSupported(): bool;

    /**
     * Guesses the MIME type of the file with the given path.
     *
     * @throws \LogicException           If the guesser is not supported
     * @throws \InvalidArgumentException If the file does not exist or is not readable
     */
    public function guessMimeType(string $path): ?string;
}
