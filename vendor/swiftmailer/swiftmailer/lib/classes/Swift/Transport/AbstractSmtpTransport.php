<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sends Messages over SMTP.
 *
 * @author Chris Corbyn
 */
abstract class Swift_Transport_AbstractSmtpTransport implements Swift_Transport
{
    /** Input-Output buffer for sending/receiving SMTP commands and responses */
    protected $_buffer;

    /** Connection status */
    protected $_started = false;

    /** The domain name to use in HELO command */
    protected $_domain = '[127.0.0.1]';

    /** The event dispatching layer */
    protected $_eventDispatcher;

    /** Source Ip */
    protected $_sourceIp;

    /** Return an array of params for the Buffer */
    abstract protected function _getBufferParams();

    /**
     * Creates a new EsmtpTransport using the given I/O buffer.
     *
     * @param Swift_Transport_IoBuffer     $buf
     * @param Swift_Events_EventDispatcher $dispatcher
     */
    public function __construct(Swift_Transport_IoBuffer $buf, Swift_Events_EventDispatcher $dispatcher)
    {
        $this->_eventDispatcher = $dispatcher;
        $this->_buffer = $buf;
        $this->_lookupHostname();
    }

    /**
     * Set the name of the local domain which Swift will identify itself as.
     *
     * This should be a fully-qualified domain name and should be truly the domain
     * you're using.
     *
     * If your server doesn't have a domain name, use the IP in square
     * brackets (i.e. [127.0.0.1]).
     *
     * @param string $domain
     *
     * @return Swift_Transport_AbstractSmtpTransport
     */
    public function setLocalDomain($domain)
    {
        $this->_domain = $domain;

        return $this;
    }

    /**
     * Get the name of the domain Swift will identify as.
     *
     * @return string
     */
    public function getLocalDomain()
    {
        return $this->_domain;
    }

    /**
     * Sets the source IP.
     *
     * @param string $source
     */
    public function setSourceIp($source)
    {
        $this->_sourceIp = $source;
    }

    /**
     * Returns the IP used to connect to the destination.
     *
     * @return string
     */
    public function getSourceIp()
    {
        return $this->_sourceIp;
    }

    /**
     * Start the SMTP connection.
     */
    public function start()
    {
        if (!$this->_started) {
            if ($evt = $this->_eventDispatcher->createTransportChangeEvent($this)) {
                $this->_eventDispatcher->dispatchEvent($evt, 'beforeTransportStarted');
                if ($evt->bubbleCancelled()) {
                    return;
                }
            }

            try {
                $this->_buffer->initialize($this->_getBufferParams());
            } catch (Swift_TransportException $e) {
                $this->_throwException($e);
            }
            $this->_readGreeting();
            $this->_doHeloCommand();

            if ($evt) {
                $this->_eventDispatcher->dispatchEvent($evt, 'transportStarted');
            }

            $this->_started = true;
        }
    }

    /**
     * Test if an SMTP connection has been established.
     *
     * @return bool
     */
    public function isStarted()
    {
        return $this->_started;
    }

    /**
     * Send the given Message.
     *
     * Recipient/sender data will be retrieved from the Message API.
     * The return value is the number of recipients who were accepted for delivery.
     *
     * @param Swift_Mime_Message $message
     * @param string[]           $failedRecipients An array of failures by-reference
     *
     * @return int
     */
    public function send(Swift_Mime_Message $message, &$failedRecipients = null)
    {
        $sent = 0;
        $failedRecipients = (array) $failedRecipients;

        if ($evt = $this->_eventDispatcher->createSendEvent($this, $message)) {
            $this->_eventDispatcher->dispatchEvent($evt, 'beforeSendPerformed');
            if ($evt->bubbleCancelled()) {
                return 0;
            }
        }

        if (!$reversePath = $this->_getReversePath($message)) {
            $this->_throwException(new Swift_TransportException(
                'Cannot send message without a sender address'
                )
            );
        }

        $to = (array) $message->getTo();
        $cc = (array) $message->getCc();
        $tos = array_merge($to, $cc);
        $bcc = (array) $message->getBcc();

        $message->setBcc(array());

        try {
            $sent += $this->_sendTo($message, $reversePath, $tos, $failedRecipients);
            $sent += $this->_sendBcc($message, $reversePath, $bcc, $failedRecipients);
        } catch (Exception $e) {
            $message->setBcc($bcc);
            throw $e;
        }

        $message->setBcc($bcc);

        if ($evt) {
            if ($sent == count($to) + count($cc) + count($bcc)) {
                $evt->setResult(Swift_Events_SendEvent::RESULT_SUCCESS);
            } elseif ($sent > 0) {
                $evt->setResult(Swift_Events_SendEvent::RESULT_TENTATIVE);
            } else {
                $evt->setResult(Swift_Events_SendEvent::RESULT_FAILED);
            }
            $evt->setFailedRecipients($failedRecipients);
            $this->_eventDispatcher->dispatchEvent($evt, 'sendPerformed');
        }

        $message->generateId(); //Make sure a new Message ID is used

        return $sent;
    }

    /**
     * Stop the SMTP connection.
     */
    public function stop()
    {
        if ($this->_started) {
            if ($evt = $this->_eventDispatcher->createTransportChangeEvent($this)) {
                $this->_eventDispatcher->dispatchEvent($evt, 'beforeTransportStopped');
                if ($evt->bubbleCancelled()) {
                    return;
                }
            }

            try {
                $this->executeCommand("QUIT\r\n", array(221));
            } catch (Swift_TransportException $e) {
            }

            try {
                $this->_buffer->terminate();

                if ($evt) {
                    $this->_eventDispatcher->dispatchEvent($evt, 'transportStopped');
                }
            } catch (Swift_TransportException $e) {
                $this->_throwException($e);
            }
        }
        $this->_started = false;
    }

    /**
     * Register a plugin.
     *
     * @param Swift_Events_EventListener $plugin
     */
    public function registerPlugin(Swift_Events_EventListener $plugin)
    {
        $this->_eventDispatcher->bindEventListener($plugin);
    }

    /**
     * Reset the current mail transaction.
     */
    public function reset()
    {
        $this->executeCommand("RSET\r\n", array(250));
    }

    /**
     * Get the IoBuffer where read/writes are occurring.
     *
     * @return Swift_Transport_IoBuffer
     */
    public function getBuffer()
    {
        return $this->_buffer;
    }

    /**
     * Run a command against the buffer, expecting the given response codes.
     *
     * If no response codes are given, the response will not be validated.
     * If codes are given, an exception will be thrown on an invalid response.
     *
     * @param string   $command
     * @param int[]    $codes
     * @param string[] $failures An array of failures by-reference
     *
     * @return string
     */
    public function executeCommand($command, $codes = array(), &$failures = null)
    {
        $failures = (array) $failures;
        $seq = $this->_buffer->write($command);
        $response = $this->_getFullResponse($seq);
        if ($evt = $this->_eventDispatcher->createCommandEvent($this, $command, $codes)) {
            $this->_eventDispatcher->dispatchEvent($evt, 'commandSent');
        }
        $this->_assertResponseCode($response, $codes);

        return $response;
    }

    /** Read the opening SMTP greeting */
    protected function _readGreeting()
    {
        $this->_assertResponseCode($this->_getFullResponse(0), array(220));
    }

    /** Send the HELO welcome */
    protected function _doHeloCommand()
    {
        $this->executeCommand(
            sprintf("HELO %s\r\n", $this->_domain), array(250)
            );
    }

    /** Send the MAIL FROM command */
    protected function _doMailFromCommand($address)
    {
        $this->executeCommand(
            sprintf("MAIL FROM:<%s>\r\n", $address), array(250)
            );
    }

    /** Send the RCPT TO command */
    protected function _doRcptToCommand($address)
    {
        $this->executeCommand(
            sprintf("RCPT TO:<%s>\r\n", $address), array(250, 251, 252)
            );
    }

    /** Send the DATA command */
    protected function _doDataCommand()
    {
        $this->executeCommand("DATA\r\n", array(354));
    }

    /** Stream the contents of the message over the buffer */
    protected function _streamMessage(Swift_Mime_Message $message)
    {
        $this->_buffer->setWriteTranslations(array("\r\n." => "\r\n.."));
        try {
            $message->toByteStream($this->_buffer);
            $this->_buffer->flushBuffers();
        } catch (Swift_TransportException $e) {
            $this->_throwException($e);
        }
        $this->_buffer->setWriteTranslations(array());
        $this->executeCommand("\r\n.\r\n", array(250));
    }

    /** Determine the best-use reverse path for this message */
    protected function _getReversePath(Swift_Mime_Message $message)
    {
        $return = $message->getReturnPath();
        $sender = $message->getSender();
        $from = $message->getFrom();
        $path = null;
        if (!empty($return)) {
            $path = $return;
        } elseif (!empty($sender)) {
            // Don't use array_keys
            reset($sender); // Reset Pointer to first pos
            $path = key($sender); // Get key
        } elseif (!empty($from)) {
            reset($from); // Reset Pointer to first pos
            $path = key($from); // Get key
        }

        return $path;
    }

    /** Throw a TransportException, first sending it to any listeners */
    protected function _throwException(Swift_TransportException $e)
    {
        if ($evt = $this->_eventDispatcher->createTransportExceptionEvent($this, $e)) {
            $this->_eventDispatcher->dispatchEvent($evt, 'exceptionThrown');
            if (!$evt->bubbleCancelled()) {
                throw $e;
            }
        } else {
            throw $e;
        }
    }

    /** Throws an Exception if a response code is incorrect */
    protected function _assertResponseCode($response, $wanted)
    {
        list($code) = sscanf($response, '%3d');
        $valid = (empty($wanted) || in_array($code, $wanted));

        if ($evt = $this->_eventDispatcher->createResponseEvent($this, $response,
            $valid)) {
            $this->_eventDispatcher->dispatchEvent($evt, 'responseReceived');
        }

        if (!$valid) {
            $this->_throwException(
                new Swift_TransportException(
                    'Expected response code '.implode('/', $wanted).' but got code '.
                    '"'.$code.'", with message "'.$response.'"',
                    $code)
                );
        }
    }

    /** Get an entire multi-line response using its sequence number */
    protected function _getFullResponse($seq)
    {
        $response = '';
        try {
            do {
                $line = $this->_buffer->readLine($seq);
                $response .= $line;
            } while (null !== $line && false !== $line && ' ' != $line{3});
        } catch (Swift_TransportException $e) {
            $this->_throwException($e);
        } catch (Swift_IoException $e) {
            $this->_throwException(
                new Swift_TransportException(
                    $e->getMessage())
                );
        }

        return $response;
    }

    /** Send an email to the given recipients from the given reverse path */
    private function _doMailTransaction($message, $reversePath, array $recipients, array &$failedRecipients)
    {
        $sent = 0;
        $this->_doMailFromCommand($reversePath);
        foreach ($recipients as $forwardPath) {
            try {
                $this->_doRcptToCommand($forwardPath);
                $sent++;
            } catch (Swift_TransportException $e) {
                $failedRecipients[] = $forwardPath;
            }
        }

        if ($sent != 0) {
            $this->_doDataCommand();
            $this->_streamMessage($message);
        } else {
            $this->reset();
        }

        return $sent;
    }

    /** Send a message to the given To: recipients */
    private function _sendTo(Swift_Mime_Message $message, $reversePath, array $to, array &$failedRecipients)
    {
        if (empty($to)) {
            return 0;
        }

        return $this->_doMailTransaction($message, $reversePath, array_keys($to),
            $failedRecipients);
    }

    /** Send a message to all Bcc: recipients */
    private function _sendBcc(Swift_Mime_Message $message, $reversePath, array $bcc, array &$failedRecipients)
    {
        $sent = 0;
        foreach ($bcc as $forwardPath => $name) {
            $message->setBcc(array($forwardPath => $name));
            $sent += $this->_doMailTransaction(
                $message, $reversePath, array($forwardPath), $failedRecipients
                );
        }

        return $sent;
    }

    /** Try to determine the hostname of the server this is run on */
    private function _lookupHostname()
    {
        if (!empty($_SERVER['SERVER_NAME'])
            && $this->_isFqdn($_SERVER['SERVER_NAME'])) {
            $this->_domain = $_SERVER['SERVER_NAME'];
        } elseif (!empty($_SERVER['SERVER_ADDR'])) {
            $this->_domain = sprintf('[%s]', $_SERVER['SERVER_ADDR']);
        }
    }

    /** Determine is the $hostname is a fully-qualified name */
    private function _isFqdn($hostname)
    {
        // We could do a really thorough check, but there's really no point
        if (false !== $dotPos = strpos($hostname, '.')) {
            return ($dotPos > 0) && ($dotPos != strlen($hostname) - 1);
        } else {
            return false;
        }
    }

    /**
     * Destructor.
     */
    public function __destruct()
    {
        $this->stop();
    }
}
