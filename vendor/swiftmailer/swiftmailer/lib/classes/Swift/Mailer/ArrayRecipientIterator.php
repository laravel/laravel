<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Wraps a standard PHP array in an iterator.
 *
 * @author Chris Corbyn
 */
class Swift_Mailer_ArrayRecipientIterator implements Swift_Mailer_RecipientIterator
{
    /**
     * The list of recipients.
     *
     * @var array
     */
    private $_recipients = array();

    /**
     * Create a new ArrayRecipientIterator from $recipients.
     *
     * @param array $recipients
     */
    public function __construct(array $recipients)
    {
        $this->_recipients = $recipients;
    }

    /**
     * Returns true only if there are more recipients to send to.
     *
     * @return bool
     */
    public function hasNext()
    {
        return !empty($this->_recipients);
    }

    /**
     * Returns an array where the keys are the addresses of recipients and the
     * values are the names. e.g. ('foo@bar' => 'Foo') or ('foo@bar' => NULL).
     *
     * @return array
     */
    public function nextRecipient()
    {
        return array_splice($this->_recipients, 0, 1);
    }
}
