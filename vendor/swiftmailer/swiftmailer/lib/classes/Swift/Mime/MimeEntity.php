<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A MIME entity, such as an attachment.
 *
 * @author Chris Corbyn
 */
interface Swift_Mime_MimeEntity extends Swift_Mime_CharsetObserver, Swift_Mime_EncodingObserver
{
    /** Main message document; there can only be one of these */
    const LEVEL_TOP = 16;

    /** An entity which nests with the same precedence as an attachment */
    const LEVEL_MIXED = 256;

    /** An entity which nests with the same precedence as a mime part */
    const LEVEL_ALTERNATIVE = 4096;

    /** An entity which nests with the same precedence as embedded content */
    const LEVEL_RELATED = 65536;

    /**
     * Get the level at which this entity shall be nested in final document.
     *
     * The lower the value, the more outermost the entity will be nested.
     *
     * @see LEVEL_TOP, LEVEL_MIXED, LEVEL_RELATED, LEVEL_ALTERNATIVE
     *
     * @return int
     */
    public function getNestingLevel();

    /**
     * Get the qualified content-type of this mime entity.
     *
     * @return string
     */
    public function getContentType();

    /**
     * Returns a unique ID for this entity.
     *
     * For most entities this will likely be the Content-ID, though it has
     * no explicit semantic meaning and can be considered an identifier for
     * programming logic purposes.
     *
     * If a Content-ID header is present, this value SHOULD match the value of
     * the header.
     *
     * @return string
     */
    public function getId();

    /**
     * Get all children nested inside this entity.
     *
     * These are not just the immediate children, but all children.
     *
     * @return Swift_Mime_MimeEntity[]
     */
    public function getChildren();

    /**
     * Set all children nested inside this entity.
     *
     * This includes grandchildren.
     *
     * @param Swift_Mime_MimeEntity[] $children
     */
    public function setChildren(array $children);

    /**
     * Get the collection of Headers in this Mime entity.
     *
     * @return Swift_Mime_HeaderSet
     */
    public function getHeaders();

    /**
     * Get the body content of this entity as a string.
     *
     * Returns NULL if no body has been set.
     *
     * @return string|null
     */
    public function getBody();

    /**
     * Set the body content of this entity as a string.
     *
     * @param string $body
     * @param string $contentType optional
     */
    public function setBody($body, $contentType = null);

    /**
     * Get this entire entity in its string form.
     *
     * @return string
     */
    public function toString();

    /**
     * Get this entire entity as a ByteStream.
     *
     * @param Swift_InputByteStream $is to write to
     */
    public function toByteStream(Swift_InputByteStream $is);
}
