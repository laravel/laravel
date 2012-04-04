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
 * Guesses the mime type of a file
 *
 * @author Bernhard Schussek <bernhard.schussek@symfony.com>
 */
interface MimeTypeGuesserInterface
{
    /**
     * Guesses the mime type of the file with the given path.
     *
     * @param  string $path   The path to the file
     *
     * @return string         The mime type or NULL, if none could be guessed
     *
     * @throws FileNotFoundException  If the file does not exist
     * @throws AccessDeniedException  If the file could not be read
     */
    function guess($path);
}
