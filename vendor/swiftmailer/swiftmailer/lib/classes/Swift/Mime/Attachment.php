<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * An attachment, in a multipart message.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_Attachment extends Swift_Mime_SimpleMimeEntity
{
    /** Recognized MIME types */
    private $_mimeTypes = array();

    /**
     * Create a new Attachment with $headers, $encoder and $cache.
     *
     * @param Swift_Mime_HeaderSet      $headers
     * @param Swift_Mime_ContentEncoder $encoder
     * @param Swift_KeyCache            $cache
     * @param Swift_Mime_Grammar        $grammar
     * @param array                     $mimeTypes optional
     */
    public function __construct(Swift_Mime_HeaderSet $headers, Swift_Mime_ContentEncoder $encoder, Swift_KeyCache $cache, Swift_Mime_Grammar $grammar, $mimeTypes = array())
    {
        parent::__construct($headers, $encoder, $cache, $grammar);
        $this->setDisposition('attachment');
        $this->setContentType('application/octet-stream');
        $this->_mimeTypes = $mimeTypes;
    }

    /**
     * Get the nesting level used for this attachment.
     *
     * Always returns {@link LEVEL_MIXED}.
     *
     * @return int
     */
    public function getNestingLevel()
    {
        return self::LEVEL_MIXED;
    }

    /**
     * Get the Content-Disposition of this attachment.
     *
     * By default attachments have a disposition of "attachment".
     *
     * @return string
     */
    public function getDisposition()
    {
        return $this->_getHeaderFieldModel('Content-Disposition');
    }

    /**
     * Set the Content-Disposition of this attachment.
     *
     * @param string $disposition
     *
     * @return Swift_Mime_Attachment
     */
    public function setDisposition($disposition)
    {
        if (!$this->_setHeaderFieldModel('Content-Disposition', $disposition)) {
            $this->getHeaders()->addParameterizedHeader(
                'Content-Disposition', $disposition
                );
        }

        return $this;
    }

    /**
     * Get the filename of this attachment when downloaded.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->_getHeaderParameter('Content-Disposition', 'filename');
    }

    /**
     * Set the filename of this attachment.
     *
     * @param string $filename
     *
     * @return Swift_Mime_Attachment
     */
    public function setFilename($filename)
    {
        $this->_setHeaderParameter('Content-Disposition', 'filename', $filename);
        $this->_setHeaderParameter('Content-Type', 'name', $filename);

        return $this;
    }

    /**
     * Get the file size of this attachment.
     *
     * @return int
     */
    public function getSize()
    {
        return $this->_getHeaderParameter('Content-Disposition', 'size');
    }

    /**
     * Set the file size of this attachment.
     *
     * @param int $size
     *
     * @return Swift_Mime_Attachment
     */
    public function setSize($size)
    {
        $this->_setHeaderParameter('Content-Disposition', 'size', $size);

        return $this;
    }

    /**
     * Set the file that this attachment is for.
     *
     * @param Swift_FileStream $file
     * @param string           $contentType optional
     *
     * @return Swift_Mime_Attachment
     */
    public function setFile(Swift_FileStream $file, $contentType = null)
    {
        $this->setFilename(basename($file->getPath()));
        $this->setBody($file, $contentType);
        if (!isset($contentType)) {
            $extension = strtolower(substr(
                $file->getPath(), strrpos($file->getPath(), '.') + 1
                ));

            if (array_key_exists($extension, $this->_mimeTypes)) {
                $this->setContentType($this->_mimeTypes[$extension]);
            }
        }

        return $this;
    }
}
