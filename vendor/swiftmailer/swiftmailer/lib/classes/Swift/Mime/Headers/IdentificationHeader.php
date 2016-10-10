<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * An ID MIME Header for something like Message-ID or Content-ID.
 *
 * @author Chris Corbyn
 */
class Swift_Mime_Headers_IdentificationHeader extends Swift_Mime_Headers_AbstractHeader
{
    /**
     * The IDs used in the value of this Header.
     *
     * This may hold multiple IDs or just a single ID.
     *
     * @var string[]
     */
    private $_ids = array();

    /**
     * Creates a new IdentificationHeader with the given $name and $id.
     *
     * @param string             $name
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
        return self::TYPE_ID;
    }

    /**
     * Set the model for the field body.
     *
     * This method takes a string ID, or an array of IDs.
     *
     * @param mixed $model
     *
     * @throws Swift_RfcComplianceException
     */
    public function setFieldBodyModel($model)
    {
        $this->setId($model);
    }

    /**
     * Get the model for the field body.
     *
     * This method returns an array of IDs
     *
     * @return array
     */
    public function getFieldBodyModel()
    {
        return $this->getIds();
    }

    /**
     * Set the ID used in the value of this header.
     *
     * @param string|array $id
     *
     * @throws Swift_RfcComplianceException
     */
    public function setId($id)
    {
        $this->setIds(is_array($id) ? $id : array($id));
    }

    /**
     * Get the ID used in the value of this Header.
     *
     * If multiple IDs are set only the first is returned.
     *
     * @return string
     */
    public function getId()
    {
        if (count($this->_ids) > 0) {
            return $this->_ids[0];
        }
    }

    /**
     * Set a collection of IDs to use in the value of this Header.
     *
     * @param string[] $ids
     *
     * @throws Swift_RfcComplianceException
     */
    public function setIds(array $ids)
    {
        $actualIds = array();

        foreach ($ids as $id) {
            $this->_assertValidId($id);
            $actualIds[] = $id;
        }

        $this->clearCachedValueIf($this->_ids != $actualIds);
        $this->_ids = $actualIds;
    }

    /**
     * Get the list of IDs used in this Header.
     *
     * @return string[]
     */
    public function getIds()
    {
        return $this->_ids;
    }

    /**
     * Get the string value of the body in this Header.
     *
     * This is not necessarily RFC 2822 compliant since folding white space will
     * not be added at this stage (see {@see toString()} for that).
     *
     * @see toString()
     *
     * @throws Swift_RfcComplianceException
     *
     * @return string
     */
    public function getFieldBody()
    {
        if (!$this->getCachedValue()) {
            $angleAddrs = array();

            foreach ($this->_ids as $id) {
                $angleAddrs[] = '<'.$id.'>';
            }

            $this->setCachedValue(implode(' ', $angleAddrs));
        }

        return $this->getCachedValue();
    }

    /**
     * Throws an Exception if the id passed does not comply with RFC 2822.
     *
     * @param string $id
     *
     * @throws Swift_RfcComplianceException
     */
    private function _assertValidId($id)
    {
        if (!preg_match(
            '/^'.$this->getGrammar()->getDefinition('id-left').'@'.
            $this->getGrammar()->getDefinition('id-right').'$/D',
            $id
            )) {
            throw new Swift_RfcComplianceException(
                'Invalid ID given <'.$id.'>'
                );
        }
    }
}
