<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A Mailbox Address MIME Header for something like From or Sender.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_Headers_MailboxHeader extends Swift_Mime_Headers_AbstractHeader
{
    /**
     * The mailboxes used in this Header.
     *
     * @var string[]
     */
    private $_mailboxes = array();

    /**
     * Creates a new MailboxHeader with $name.
     *
     * @param string                   $name    of Header
     * @param Swift_Mime_HeaderEncoder $encoder
     * @param Swift_Mime_Grammar       $grammar
     */
    public function __construct($name, Swift_Mime_HeaderEncoder $encoder, Swift_Mime_Grammar $grammar)
    {
        $this->setFieldName($name);
        $this->setEncoder($encoder);
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
        return self::TYPE_MAILBOX;
    }

    /**
     * Set the model for the field body.
     *
     * This method takes a string, or an array of addresses.
     *
     * @param mixed $model
     *
     * @throws Swift_RfcComplianceException
     */
    public function setFieldBodyModel($model)
    {
        $this->setNameAddresses($model);
    }

    /**
     * Get the model for the field body.
     *
     * This method returns an associative array like {@link getNameAddresses()}
     *
     * @throws Swift_RfcComplianceException
     *
     * @return array
     */
    public function getFieldBodyModel()
    {
        return $this->getNameAddresses();
    }

    /**
     * Set a list of mailboxes to be shown in this Header.
     *
     * The mailboxes can be a simple array of addresses, or an array of
     * key=>value pairs where (email => personalName).
     * Example:
     * <code>
     * <?php
     * //Sets two mailboxes in the Header, one with a personal name
     * $header->setNameAddresses(array(
     *  'chris@swiftmailer.org' => 'Chris Corbyn',
     *  'mark@swiftmailer.org' //No associated personal name
     *  ));
     * ?>
     * </code>
     *
     * @see __construct()
     * @see setAddresses()
     * @see setValue()
     *
     * @param string|string[] $mailboxes
     *
     * @throws Swift_RfcComplianceException
     */
    public function setNameAddresses($mailboxes)
    {
        $this->_mailboxes = $this->normalizeMailboxes((array) $mailboxes);
        $this->setCachedValue(null); //Clear any cached value
    }

    /**
     * Get the full mailbox list of this Header as an array of valid RFC 2822 strings.
     *
     * Example:
     * <code>
     * <?php
     * $header = new Swift_Mime_Headers_MailboxHeader('From',
     *  array('chris@swiftmailer.org' => 'Chris Corbyn',
     *  'mark@swiftmailer.org' => 'Mark Corbyn')
     *  );
     * print_r($header->getNameAddressStrings());
     * // array (
     * // 0 => Chris Corbyn <chris@swiftmailer.org>,
     * // 1 => Mark Corbyn <mark@swiftmailer.org>
     * // )
     * ?>
     * </code>
     *
     * @see getNameAddresses()
     * @see toString()
     *
     * @throws Swift_RfcComplianceException
     *
     * @return string[]
     */
    public function getNameAddressStrings()
    {
        return $this->_createNameAddressStrings($this->getNameAddresses());
    }

    /**
     * Get all mailboxes in this Header as key=>value pairs.
     *
     * The key is the address and the value is the name (or null if none set).
     * Example:
     * <code>
     * <?php
     * $header = new Swift_Mime_Headers_MailboxHeader('From',
     *  array('chris@swiftmailer.org' => 'Chris Corbyn',
     *  'mark@swiftmailer.org' => 'Mark Corbyn')
     *  );
     * print_r($header->getNameAddresses());
     * // array (
     * // chris@swiftmailer.org => Chris Corbyn,
     * // mark@swiftmailer.org => Mark Corbyn
     * // )
     * ?>
     * </code>
     *
     * @see getAddresses()
     * @see getNameAddressStrings()
     *
     * @return string[]
     */
    public function getNameAddresses()
    {
        return $this->_mailboxes;
    }

    /**
     * Makes this Header represent a list of plain email addresses with no names.
     *
     * Example:
     * <code>
     * <?php
     * //Sets three email addresses as the Header data
     * $header->setAddresses(
     *  array('one@domain.tld', 'two@domain.tld', 'three@domain.tld')
     *  );
     * ?>
     * </code>
     *
     * @see setNameAddresses()
     * @see setValue()
     *
     * @param string[] $addresses
     *
     * @throws Swift_RfcComplianceException
     */
    public function setAddresses($addresses)
    {
        $this->setNameAddresses(array_values((array) $addresses));
    }

    /**
     * Get all email addresses in this Header.
     *
     * @see getNameAddresses()
     *
     * @return string[]
     */
    public function getAddresses()
    {
        return array_keys($this->_mailboxes);
    }

    /**
     * Remove one or more addresses from this Header.
     *
     * @param string|string[] $addresses
     */
    public function removeAddresses($addresses)
    {
        $this->setCachedValue(null);
        foreach ((array) $addresses as $address) {
            unset($this->_mailboxes[$address]);
        }
    }

    /**
     * Get the string value of the body in this Header.
     *
     * This is not necessarily RFC 2822 compliant since folding white space will
     * not be added at this stage (see {@link toString()} for that).
     *
     * @see toString()
     *
     * @throws Swift_RfcComplianceException
     *
     * @return string
     */
    public function getFieldBody()
    {
        // Compute the string value of the header only if needed
        if (is_null($this->getCachedValue())) {
            $this->setCachedValue($this->createMailboxListString($this->_mailboxes));
        }

        return $this->getCachedValue();
    }

    // -- Points of extension

    /**
     * Normalizes a user-input list of mailboxes into consistent key=>value pairs.
     *
     * @param string[] $mailboxes
     *
     * @return string[]
     */
    protected function normalizeMailboxes(array $mailboxes)
    {
        $actualMailboxes = array();

        foreach ($mailboxes as $key => $value) {
            if (is_string($key)) {
                //key is email addr
                $address = $key;
                $name = $value;
            } else {
                $address = $value;
                $name = null;
            }
            $this->_assertValidAddress($address);
            $actualMailboxes[$address] = $name;
        }

        return $actualMailboxes;
    }

    /**
     * Produces a compliant, formatted display-name based on the string given.
     *
     * @param string $displayName as displayed
     * @param bool   $shorten     the first line to make remove for header name
     *
     * @return string
     */
    protected function createDisplayNameString($displayName, $shorten = false)
    {
        return $this->createPhrase($this, $displayName,
            $this->getCharset(), $this->getEncoder(), $shorten
            );
    }

    /**
     * Creates a string form of all the mailboxes in the passed array.
     *
     * @param string[] $mailboxes
     *
     * @throws Swift_RfcComplianceException
     *
     * @return string
     */
    protected function createMailboxListString(array $mailboxes)
    {
        return implode(', ', $this->_createNameAddressStrings($mailboxes));
    }

    /**
     * Redefine the encoding requirements for mailboxes.
     *
     * Commas and semicolons are used to separate
     * multiple addresses, and should therefore be encoded
     *
     * @param string $token
     *
     * @return bool
     */
    protected function tokenNeedsEncoding($token)
    {
        return preg_match('/[,;]/', $token) || parent::tokenNeedsEncoding($token);
    }

    /**
     * Return an array of strings conforming the the name-addr spec of RFC 2822.
     *
     * @param string[] $mailboxes
     *
     * @return string[]
     */
    private function _createNameAddressStrings(array $mailboxes)
    {
        $strings = array();

        foreach ($mailboxes as $email => $name) {
            $mailboxStr = $email;
            if (!is_null($name)) {
                $nameStr = $this->createDisplayNameString($name, empty($strings));
                $mailboxStr = $nameStr.' <'.$mailboxStr.'>';
            }
            $strings[] = $mailboxStr;
        }

        return $strings;
    }

    /**
     * Throws an Exception if the address passed does not comply with RFC 2822.
     *
     * @param string $address
     *
     * @throws Swift_RfcComplianceException If invalid.
     */
    private function _assertValidAddress($address)
    {
        if (!preg_match('/^'.$this->getGrammar()->getDefinition('addr-spec').'$/D',
            $address)) {
            throw new Swift_RfcComplianceException(
                'Address in mailbox given ['.$address.
                '] does not comply with RFC 2822, 3.6.2.'
                );
        }
    }
}
