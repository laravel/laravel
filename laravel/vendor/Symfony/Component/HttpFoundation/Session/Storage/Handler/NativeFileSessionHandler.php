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
 * NativeFileSessionHandler.
 *
 * Native session handler using PHP's built in file storage.
 *
 * @author Drak <drak@zikula.org>
 */
class NativeFileSessionHandler extends NativeSessionHandler
{
    /**
     * Constructor.
     *
     * @param string $savePath Path of directory to save session files.  Default null will leave setting as defined by PHP.
     */
    public function __construct($savePath = null)
    {
        if (null === $savePath) {
            $savePath = ini_get('session.save_path');
        }

        if ($savePath && !is_dir($savePath)) {
            mkdir($savePath, 0777, true);
        }

        ini_set('session.save_handler', 'files');
        ini_set('session.save_path', $savePath);
    }
}
