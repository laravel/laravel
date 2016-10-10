<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Allows customization of Messages on-the-fly.
 *
 * @author Chris Corbyn
 * @author Fabien Potencier
 */
class Swift_Plugins_DecoratorPlugin implements Swift_Events_SendListener, Swift_Plugins_Decorator_Replacements
{
    /** The replacement map */
    private $_replacements;

    /** The body as it was before replacements */
    private $_originalBody;

    /** The original headers of the message, before replacements */
    private $_originalHeaders = array();

    /** Bodies of children before they are replaced */
    private $_originalChildBodies = array();

    /** The Message that was last replaced */
    private $_lastMessage;

    /**
     * Create a new DecoratorPlugin with $replacements.
     *
     * The $replacements can either be an associative array, or an implementation
     * of {@link Swift_Plugins_Decorator_Replacements}.
     *
     * When using an array, it should be of the form:
     * <code>
     * $replacements = array(
     *  "address1@domain.tld" => array("{a}" => "b", "{c}" => "d"),
     *  "address2@domain.tld" => array("{a}" => "x", "{c}" => "y")
     * )
     * </code>
     *
     * When using an instance of {@link Swift_Plugins_Decorator_Replacements},
     * the object should return just the array of replacements for the address
     * given to {@link Swift_Plugins_Decorator_Replacements::getReplacementsFor()}.
     *
     * @param mixed $replacements Array or Swift_Plugins_Decorator_Replacements
     */
    public function __construct($replacements)
    {
        $this->setReplacements($replacements);
    }

    /**
     * Sets replacements.
     *
     * @param mixed $replacements Array or Swift_Plugins_Decorator_Replacements
     *
     * @see __construct()
     */
    public function setReplacements($replacements)
    {
        if (!($replacements instanceof Swift_Plugins_Decorator_Replacements)) {
            $this->_replacements = (array) $replacements;
        } else {
            $this->_replacements = $replacements;
        }
    }

    /**
     * Invoked immediately before the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function beforeSendPerformed(Swift_Events_SendEvent $evt)
    {
        $message = $evt->getMessage();
        $this->_restoreMessage($message);
        $to = array_keys($message->getTo());
        $address = array_shift($to);
        if ($replacements = $this->getReplacementsFor($address)) {
            $body = $message->getBody();
            $search = array_keys($replacements);
            $replace = array_values($replacements);
            $bodyReplaced = str_replace(
                $search, $replace, $body
                );
            if ($body != $bodyReplaced) {
                $this->_originalBody = $body;
                $message->setBody($bodyReplaced);
            }

            foreach ($message->getHeaders()->getAll() as $header) {
                $body = $header->getFieldBodyModel();
                $count = 0;
                if (is_array($body)) {
                    $bodyReplaced = array();
                    foreach ($body as $key => $value) {
                        $count1 = 0;
                        $count2 = 0;
                        $key = is_string($key) ? str_replace($search, $replace, $key, $count1) : $key;
                        $value = is_string($value) ? str_replace($search, $replace, $value, $count2) : $value;
                        $bodyReplaced[$key] = $value;

                        if (!$count && ($count1 || $count2)) {
                            $count = 1;
                        }
                    }
                } else {
                    $bodyReplaced = str_replace($search, $replace, $body, $count);
                }

                if ($count) {
                    $this->_originalHeaders[$header->getFieldName()] = $body;
                    $header->setFieldBodyModel($bodyReplaced);
                }
            }

            $children = (array) $message->getChildren();
            foreach ($children as $child) {
                list($type) = sscanf($child->getContentType(), '%[^/]/%s');
                if ('text' == $type) {
                    $body = $child->getBody();
                    $bodyReplaced = str_replace(
                        $search, $replace, $body
                        );
                    if ($body != $bodyReplaced) {
                        $child->setBody($bodyReplaced);
                        $this->_originalChildBodies[$child->getId()] = $body;
                    }
                }
            }
            $this->_lastMessage = $message;
        }
    }

    /**
     * Find a map of replacements for the address.
     *
     * If this plugin was provided with a delegate instance of
     * {@link Swift_Plugins_Decorator_Replacements} then the call will be
     * delegated to it.  Otherwise, it will attempt to find the replacements
     * from the array provided in the constructor.
     *
     * If no replacements can be found, an empty value (NULL) is returned.
     *
     * @param string $address
     *
     * @return array
     */
    public function getReplacementsFor($address)
    {
        if ($this->_replacements instanceof Swift_Plugins_Decorator_Replacements) {
            return $this->_replacements->getReplacementsFor($address);
        } else {
            return isset($this->_replacements[$address])
                ? $this->_replacements[$address]
                : null
                ;
        }
    }

    /**
     * Invoked immediately after the Message is sent.
     *
     * @param Swift_Events_SendEvent $evt
     */
    public function sendPerformed(Swift_Events_SendEvent $evt)
    {
        $this->_restoreMessage($evt->getMessage());
    }

    /** Restore a changed message back to its original state */
    private function _restoreMessage(Swift_Mime_Message $message)
    {
        if ($this->_lastMessage === $message) {
            if (isset($this->_originalBody)) {
                $message->setBody($this->_originalBody);
                $this->_originalBody = null;
            }
            if (!empty($this->_originalHeaders)) {
                foreach ($message->getHeaders()->getAll() as $header) {
                    if (array_key_exists($header->getFieldName(), $this->_originalHeaders)) {
                        $header->setFieldBodyModel($this->_originalHeaders[$header->getFieldName()]);
                    }
                }
                $this->_originalHeaders = array();
            }
            if (!empty($this->_originalChildBodies)) {
                $children = (array) $message->getChildren();
                foreach ($children as $child) {
                    $id = $child->getId();
                    if (array_key_exists($id, $this->_originalChildBodies)) {
                        $child->setBody($this->_originalChildBodies[$id]);
                    }
                }
                $this->_originalChildBodies = array();
            }
            $this->_lastMessage = null;
        }
    }
}
