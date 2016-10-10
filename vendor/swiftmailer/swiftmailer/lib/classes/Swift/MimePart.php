<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A MIME part, in a multipart message.
 *
 * @author Chris Corbyn
 */
class Swift_MimePart extends Swift_Mime_MimePart
{
    /**
     * Create a new MimePart.
     *
     * Details may be optionally passed into the constructor.
     *
     * @param string $body
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($body = null, $contentType = null, $charset = null)
    {
        call_user_func_array(
            array($this, 'Swift_Mime_MimePart::__construct'),
            Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('mime.part')
            );

        if (!isset($charset)) {
            $charset = Swift_DependencyContainer::getInstance()
                ->lookup('properties.charset');
        }
        $this->setBody($body);
        $this->setCharset($charset);
        if ($contentType) {
            $this->setContentType($contentType);
        }
    }

    /**
     * Create a new MimePart.
     *
     * @param string $body
     * @param string $contentType
     * @param string $charset
     *
     * @return Swift_Mime_MimePart
     */
    public static function newInstance($body = null, $contentType = null, $charset = null)
    {
        return new self($body, $contentType, $charset);
    }
}
