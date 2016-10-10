<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * The Message class for building emails.
 *
 * @author Chris Corbyn
 */
class Swift_Message extends Swift_Mime_SimpleMessage
{
    /**
     * @var Swift_Signers_HeaderSigner[]
     */
    private $headerSigners = array();

    /**
     * @var Swift_Signers_BodySigner[]
     */
    private $bodySigners = array();

    /**
     * @var array
     */
    private $savedMessage = array();

    /**
     * Create a new Message.
     *
     * Details may be optionally passed into the constructor.
     *
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $charset
     */
    public function __construct($subject = null, $body = null, $contentType = null, $charset = null)
    {
        call_user_func_array(
            array($this, 'Swift_Mime_SimpleMessage::__construct'),
            Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('mime.message')
            );

        if (!isset($charset)) {
            $charset = Swift_DependencyContainer::getInstance()
                ->lookup('properties.charset');
        }
        $this->setSubject($subject);
        $this->setBody($body);
        $this->setCharset($charset);
        if ($contentType) {
            $this->setContentType($contentType);
        }
    }

    /**
     * Create a new Message.
     *
     * @param string $subject
     * @param string $body
     * @param string $contentType
     * @param string $charset
     *
     * @return Swift_Message
     */
    public static function newInstance($subject = null, $body = null, $contentType = null, $charset = null)
    {
        return new self($subject, $body, $contentType, $charset);
    }

    /**
     * Add a MimePart to this Message.
     *
     * @param string|Swift_OutputByteStream $body
     * @param string                        $contentType
     * @param string                        $charset
     *
     * @return Swift_Mime_SimpleMessage
     */
    public function addPart($body, $contentType = null, $charset = null)
    {
        return $this->attach(Swift_MimePart::newInstance(
            $body, $contentType, $charset
            ));
    }

    /**
     * Attach a new signature handler to the message.
     *
     * @param Swift_Signer $signer
     *
     * @return Swift_Message
     */
    public function attachSigner(Swift_Signer $signer)
    {
        if ($signer instanceof Swift_Signers_HeaderSigner) {
            $this->headerSigners[] = $signer;
        } elseif ($signer instanceof Swift_Signers_BodySigner) {
            $this->bodySigners[] = $signer;
        }

        return $this;
    }

    /**
     * Attach a new signature handler to the message.
     *
     * @param Swift_Signer $signer
     *
     * @return Swift_Message
     */
    public function detachSigner(Swift_Signer $signer)
    {
        if ($signer instanceof Swift_Signers_HeaderSigner) {
            foreach ($this->headerSigners as $k => $headerSigner) {
                if ($headerSigner === $signer) {
                    unset($this->headerSigners[$k]);

                    return $this;
                }
            }
        } elseif ($signer instanceof Swift_Signers_BodySigner) {
            foreach ($this->bodySigners as $k => $bodySigner) {
                if ($bodySigner === $signer) {
                    unset($this->bodySigners[$k]);

                    return $this;
                }
            }
        }

        return $this;
    }

    /**
     * Get this message as a complete string.
     *
     * @return string
     */
    public function toString()
    {
        if (empty($this->headerSigners) && empty($this->bodySigners)) {
            return parent::toString();
        }

        $this->saveMessage();

        $this->doSign();

        $string = parent::toString();

        $this->restoreMessage();

        return $string;
    }

    /**
     * Write this message to a {@link Swift_InputByteStream}.
     *
     * @param Swift_InputByteStream $is
     */
    public function toByteStream(Swift_InputByteStream $is)
    {
        if (empty($this->headerSigners) && empty($this->bodySigners)) {
            parent::toByteStream($is);

            return;
        }

        $this->saveMessage();

        $this->doSign();

        parent::toByteStream($is);

        $this->restoreMessage();
    }

    public function __wakeup()
    {
        Swift_DependencyContainer::getInstance()->createDependenciesFor('mime.message');
    }

    /**
     * loops through signers and apply the signatures.
     */
    protected function doSign()
    {
        foreach ($this->bodySigners as $signer) {
            $altered = $signer->getAlteredHeaders();
            $this->saveHeaders($altered);
            $signer->signMessage($this);
        }

        foreach ($this->headerSigners as $signer) {
            $altered = $signer->getAlteredHeaders();
            $this->saveHeaders($altered);
            $signer->reset();

            $signer->setHeaders($this->getHeaders());

            $signer->startBody();
            $this->_bodyToByteStream($signer);
            $signer->endBody();

            $signer->addSignature($this->getHeaders());
        }
    }

    /**
     * save the message before any signature is applied.
     */
    protected function saveMessage()
    {
        $this->savedMessage = array('headers' => array());
        $this->savedMessage['body'] = $this->getBody();
        $this->savedMessage['children'] = $this->getChildren();
        if (count($this->savedMessage['children']) > 0 && $this->getBody() != '') {
            $this->setChildren(array_merge(array($this->_becomeMimePart()), $this->savedMessage['children']));
            $this->setBody('');
        }
    }

    /**
     * save the original headers.
     *
     * @param array $altered
     */
    protected function saveHeaders(array $altered)
    {
        foreach ($altered as $head) {
            $lc = strtolower($head);

            if (!isset($this->savedMessage['headers'][$lc])) {
                $this->savedMessage['headers'][$lc] = $this->getHeaders()->getAll($head);
            }
        }
    }

    /**
     * Remove or restore altered headers.
     */
    protected function restoreHeaders()
    {
        foreach ($this->savedMessage['headers'] as $name => $savedValue) {
            $headers = $this->getHeaders()->getAll($name);

            foreach ($headers as $key => $value) {
                if (!isset($savedValue[$key])) {
                    $this->getHeaders()->remove($name, $key);
                }
            }
        }
    }

    /**
     * Restore message body.
     */
    protected function restoreMessage()
    {
        $this->setBody($this->savedMessage['body']);
        $this->setChildren($this->savedMessage['children']);

        $this->restoreHeaders();
        $this->savedMessage = array();
    }

    /**
     * Clone Message Signers.
     *
     * @see Swift_Mime_SimpleMimeEntity::__clone()
     */
    public function __clone()
    {
        parent::__clone();
        foreach ($this->bodySigners as $key => $bodySigner) {
            $this->bodySigners[$key] = clone($bodySigner);
        }

        foreach ($this->headerSigners as $key => $headerSigner) {
            $this->headerSigners[$key] = clone($headerSigner);
        }
    }
}
