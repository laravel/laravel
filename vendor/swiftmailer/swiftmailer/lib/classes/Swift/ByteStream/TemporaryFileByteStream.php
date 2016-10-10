<?php

/*
* This file is part of SwiftMailer.
* (c) 2004-2009 Chris Corbyn
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

/**
 * @author Romain-Geissler
 */
class Swift_ByteStream_TemporaryFileByteStream extends Swift_ByteStream_FileByteStream
{
    public function __construct()
    {
        $filePath = tempnam(sys_get_temp_dir(), 'FileByteStream');

        if ($filePath === false) {
            throw new Swift_IoException('Failed to retrieve temporary file name.');
        }

        parent::__construct($filePath, true);
    }

    public function getContent()
    {
        if (($content = file_get_contents($this->getPath())) === false) {
            throw new Swift_IoException('Failed to get temporary file content.');
        }

        return $content;
    }

    public function __destruct()
    {
        if (file_exists($this->getPath())) {
            @unlink($this->getPath());
        }
    }
}
