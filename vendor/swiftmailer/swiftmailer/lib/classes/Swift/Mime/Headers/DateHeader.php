<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A Date MIME Header for Swift Mailer.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_Headers_DateHeader extends Swift_Mime_Headers_AbstractHeader
{
    /**
     * The UNIX timestamp value of this Header.
     *
     * @var int
     */
    private $_timestamp;

    /**
     * Creates a new DateHeader with $name and $timestamp.
     *
     * Example:
     * <code>
     * <?php
     * $header = new Swift_Mime_Headers_DateHeader('Date', time());
     * ?>
     * </code>
     *
     * @param string             $name    of Header
     * @param Swift_Mime_Grammar $grammar
     */
    public function __construct($name, Swift_Mime_Grammar $grammar)
    {
        $this->setFieldName($name);
        parent::__construct($grammar);
    }

    /**
     * Get the type of Header that this instance represents.
     *
     * @see TYPE_TEXT, TYPE_PARAMETERIZED, TYPE_MAILBOX
     * @see TYPE_DATE, TYPE_ID, TYPE_PATH
     *
     * @return int
     */
    public function getFieldType()
    {
        return self::TYPE_DATE;
    }

    /**
     * Set the model for the field body.
     *
     * This method takes a UNIX timestamp.
     *
     * @param int $model
     */
    public function setFieldBodyModel($model)
    {
        $this->setTimestamp($model);
    }

    /**
     * Get the model for the field body.
     *
     * This method returns a UNIX timestamp.
     *
     * @return mixed
     */
    public function getFieldBodyModel()
    {
        return $this->getTimestamp();
    }

    /**
     * Get the UNIX timestamp of the Date in this Header.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->_timestamp;
    }

    /**
     * Set the UNIX timestamp of the Date in this Header.
     *
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        if (!is_null($timestamp)) {
            $timestamp = (int) $timestamp;
        }
        $this->clearCachedValueIf($this->_timestamp != $timestamp);
        $this->_timestamp = $timestamp;
    }

    /**
     * Get the string value of the body in this Header.
     *
     * This is not necessarily RFC 2822 compliant since folding white space will
     * not be added at this stage (see {@link toString()} for that).
     *
     * @see toString()
     *
     * @return string
     */
    public function getFieldBody()
    {
        if (!$this->getCachedValue()) {
            if (isset($this->_timestamp)) {
                $this->setCachedValue(date('r', $this->_timestamp));
            }
        }

        return $this->getCachedValue();
    }
}
