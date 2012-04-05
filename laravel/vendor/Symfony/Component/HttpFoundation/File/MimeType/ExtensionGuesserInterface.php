<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\File\MimeType;

/**
 * Guesses the file extension corresponding to a given mime type
 */
interface ExtensionGuesserInterface
{
    /**
     * Makes a best guess for a file extension, given a mime type
     *
     * @param  string $mimeType   The mime type
     * @return string             The guessed extension or NULL, if none could be guessed
     */
    function guess($mimeType);
}
