<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * An embedded file, in a multipart message.
 *
 * @author Chris Corbyn
 */
class Swift_EmbeddedFile extends Swift_Mime_EmbeddedFile
{
    /**
     * Create a new EmbeddedFile.
     *
     * Details may be optionally provided to the constructor.
     *
     * @param string|Swift_OutputByteStream $data
     * @param string                        $filename
     * @param string                        $contentType
     */
    public function __construct($data = null, $filename = null, $contentType = null)
    {
        call_user_func_array(
            array($this, 'Swift_Mime_EmbeddedFile::__construct'),
            Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('mime.embeddedfile')
            );

        $this->setBody($data);
        $this->setFilename($filename);
        if ($contentType) {
            $this->setContentType($contentType);
        }
    }

    /**
     * Create a new EmbeddedFile.
     *
     * @param string|Swift_OutputByteStream $data
     * @param string                        $filename
     * @param string                        $contentType
     *
     * @return Swift_Mime_EmbeddedFile
     */
    public static function newInstance($data = null, $filename = null, $contentType = null)
    {
        return new self($data, $filename, $contentType);
    }

    /**
     * Create a new EmbeddedFile from a filesystem path.
     *
     * @param string $path
     *
     * @return Swift_Mime_EmbeddedFile
     */
    public static function fromPath($path)
    {
        return self::newInstance()->setFile(
            new Swift_ByteStream_FileByteStream($path)
            );
    }
}
