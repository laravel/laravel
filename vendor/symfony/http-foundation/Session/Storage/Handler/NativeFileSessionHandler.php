<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * Native session handler using PHP's built in file storage.
 *
 * @author Drak <drak@zikula.org>
 */
class NativeFileSessionHandler extends \SessionHandler
{
    /**
     * @param string|null $savePath Path of directory to save session files
     *                              Default null will leave setting as defined by PHP.
     *                              '/path', 'N;/path', or 'N;octal-mode;/path
     *
     * @see https://php.net/session.configuration#ini.session.save-path for further details.
     *
     * @throws \InvalidArgumentException On invalid $savePath
     * @throws \RuntimeException         When failing to create the save directory
     */
    public function __construct(?string $savePath = null)
    {
        $baseDir = $savePath ??= \ini_get('session.save_path');

        if ($count = substr_count($savePath, ';')) {
            if ($count > 2) {
                throw new \InvalidArgumentException(\sprintf('Invalid argument $savePath \'%s\'.', $savePath));
            }

            // characters after last ';' are the path
            $baseDir = ltrim(strrchr($savePath, ';'), ';');
        }

        if ($baseDir && !is_dir($baseDir) && !@mkdir($baseDir, 0o777, true) && !is_dir($baseDir)) {
            throw new \RuntimeException(\sprintf('Session Storage was not able to create directory "%s".', $baseDir));
        }

        if ($savePath !== \ini_get('session.save_path')) {
            ini_set('session.save_path', $savePath);
        }
        if ('files' !== \ini_get('session.save_handler')) {
            ini_set('session.save_handler', 'files');
        }
    }
}
