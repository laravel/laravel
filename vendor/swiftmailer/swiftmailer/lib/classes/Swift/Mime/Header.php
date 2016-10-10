<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A MIME Header.
 *
 * @author Chris Corbyn
 */
interface Swift_Mime_Header
{
    /** Text headers */
    const TYPE_TEXT = 2;

    /**  headers (text + params) */
    const TYPE_PARAMETERIZED = 6;

    /** Mailbox and address headers */
    const TYPE_MAILBOX = 8;

    /** Date and time headers */
    const TYPE_DATE = 16;

    /** Identification headers */
    const TYPE_ID = 32;

    /** Address path headers */
    const TYPE_PATH = 64;

    /**
     * Get the type of Header that this instance represents.
     *
     * @see TYPE_TEXT, TYPE_PARAMETERIZED, TYPE_MAILBOX
     * @see TYPE_DATE, TYPE_ID, TYPE_PATH
     *
     * @return int
     */
    public function getFieldType();

    /**
     * Set the model for the field body.
     *
     * The actual types needed will vary depending upon the type of Header.
     *
     * @param mixed $model
     */
    public function setFieldBodyModel($model);

    /**
     * Set the charset used when rendering the Header.
     *
     * @param string $charset
     */
    public function setCharset($charset);

    /**
     * Get the model for the field body.
     *
     * The return type depends on the specifics of the Header.
     *
     * @return mixed
     */
    public function getFieldBodyModel();

    /**
     * Get the name of this header (e.g. Subject).
     *
     * The name is an identifier and as such will be immutable.
     *
     * @return string
     */
    public function getFieldName();

    /**
     * Get the field body, prepared for folding into a final header value.
     *
     * @return string
     */
    public function getFieldBody();

    /**
     * Get this Header rendered as a compliant string.
     *
     * @return string
     */
    public function toString();
}
