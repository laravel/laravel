<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sends Messages over SMTP with ESMTP support.
 *
 * @author Chris Corbyn
 */
class Swift_Transport_EsmtpTransport extends Swift_Transport_AbstractSmtpTransport implements Swift_Transport_SmtpAgent
{
    /**
     * ESMTP extension handlers.
     *
     * @var Swift_Transport_EsmtpHandler[]
     */
    private $_handlers = array();

    /**
     * ESMTP capabilities.
     *
     * @var string[]
     */
    private $_capabilities = array();

    /**
     * Connection buffer parameters.
     *
     * @var array
     */
    private $_params = array(
        'protocol' => 'tcp',
        'host' => 'localhost',
        'port' => 25,
        'timeout' => 30,
        'blocking' => 1,
        'tls' => false,
        'type' => Swift_Transport_IoBuffer::TYPE_SOCKET,
        );

    /**
     * Creates a new EsmtpTransport using the given I/O buffer.
     *
     * @param Swift_Transport_IoBuffer       $buf
     * @param Swift_Transport_EsmtpHandler[] $extensionHandlers
     * @param Swift_Events_EventDispatcher   $dispatcher
     */
    public function __construct(Swift_Transport_IoBuffer $buf, array $extensionHandlers, Swift_Events_EventDispatcher $dispatcher)
    {
        parent::__construct($buf, $dispatcher);
        $this->setExtensionHandlers($extensionHandlers);
    }

    /**
     * Set the host to connect to.
     *
     * @param string $host
     *
     * @return Swift_Transport_EsmtpTransport
     */
    public function setHost($host)
    {
        $this->_params['host'] = $host;

        return $this;
    }

    /**
     * Get the host to connect to.
     *
     * @return string
     */
    public function getHost()
    {
        return $this->_params['host'];
    }

    /**
     * Set the port to connect to.
     *
     * @param int $port
     *
     * @return Swift_Transport_EsmtpTransport
     */
    public function setPort($port)
    {
        $this->_params['port'] = (int) $port;

        return $this;
    }

    /**
     * Get the port to connect to.
     *
     * @return int
     */
    public function getPort()
    {
        return $this->_params['port'];
    }

    /**
     * Set the connection timeout.
     *
     * @param int $timeout seconds
     *
     * @return Swift_Transport_EsmtpTransport
     */
    public function setTimeout($timeout)
    {
        $this->_params['timeout'] = (int) $timeout;
        $this->_buffer->setParam('timeout', (int) $timeout);

        return $this;
    }

    /**
     * Get the connection timeout.
     *
     * @return int
     */
    public function getTimeout()
    {
        return $this->_params['timeout'];
    }

    /**
     * Set the encryption type (tls or ssl).
     *
     * @param string $encryption
     *
     * @return Swift_Transport_EsmtpTransport
     */
    public function setEncryption($encryption)
    {
        if ('tls' == $encryption) {
            $this->_params['protocol'] = 'tcp';
            $this->_params['tls'] = true;
        } else {
            $this->_params['protocol'] = $encryption;
            $this->_params['tls'] = false;
        }

        return $this;
    }

    /**
     * Get the encryption type.
     *
     * @return string
     */
    public function getEncryption()
    {
        return $this->_params['tls'] ? 'tls' : $this->_params['protocol'];
    }

    /**
     * Sets the source IP.
     *
     * @param string $source
     *
     * @return Swift_Transport_EsmtpTransport
     */
    public function setSourceIp($source)
    {
        $this->_params['sourceIp'] = $source;

        return $this;
    }

    /**
     * Returns the IP used to connect to the destination.
     *
     * @return string
     */
    public function getSourceIp()
    {
        return isset($this->_params['sourceIp']) ? $this->_params['sourceIp'] : null;
    }

    /**
     * Set ESMTP extension handlers.
     *
     * @param Swift_Transport_EsmtpHandler[] $handlers
     *
     * @return Swift_Transport_EsmtpTransport
     */
    public function setExtensionHandlers(array $handlers)
    {
        $assoc = array();
        foreach ($handlers as $handler) {
            $assoc[$handler->getHandledKeyword()] = $handler;
        }
        uasort($assoc, array($this, '_sortHandlers'));
        $this->_handlers = $assoc;
        $this->_setHandlerParams();

        return $this;
    }

    /**
     * Get ESMTP extension handlers.
     *
     * @return Swift_Transport_EsmtpHandler[]
     */
    public function getExtensionHandlers()
    {
        return array_values($this->_handlers);
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
        $stopSignal = false;
        $response = null;
        foreach ($this->_getActiveHandlers() as $handler) {
            $response = $handler->onCommand(
                $this, $command, $codes, $failures, $stopSignal
                );
            if ($stopSignal) {
                return $response;
            }
        }

        return parent::executeCommand($command, $codes, $failures);
    }

    // -- Mixin invocation code

    /** Mixin handling method for ESMTP handlers */
    public function __call($method, $args)
    {
        foreach ($this->_handlers as $handler) {
            if (in_array(strtolower($method),
                array_map('strtolower', (array) $handler->exposeMixinMethods())
                )) {
                $return = call_user_func_array(array($handler, $method), $args);
                // Allow fluid method calls
                if (is_null($return) && substr($method, 0, 3) == 'set') {
                    return $this;
                } else {
                    return $return;
                }
            }
        }
        trigger_error('Call to undefined method '.$method, E_USER_ERROR);
    }

    /** Get the params to initialize the buffer */
    protected function _getBufferParams()
    {
        return $this->_params;
    }

    /** Overridden to perform EHLO instead */
    protected function _doHeloCommand()
    {
        try {
            $response = $this->executeCommand(
                sprintf("EHLO %s\r\n", $this->_domain), array(250)
                );
        } catch (Swift_TransportException $e) {
            return parent::_doHeloCommand();
        }

        if ($this->_params['tls']) {
            try {
                $this->executeCommand("STARTTLS\r\n", array(220));

                if (!$this->_buffer->startTLS()) {
                    throw new Swift_TransportException('Unable to connect with TLS encryption');
                }

                try {
                    $response = $this->executeCommand(
                        sprintf("EHLO %s\r\n", $this->_domain), array(250)
                        );
                } catch (Swift_TransportException $e) {
                    return parent::_doHeloCommand();
                }
            } catch (Swift_TransportException $e) {
                $this->_throwException($e);
            }
        }

        $this->_capabilities = $this->_getCapabilities($response);
        $this->_setHandlerParams();
        foreach ($this->_getActiveHandlers() as $handler) {
            $handler->afterEhlo($this);
        }
    }

    /** Overridden to add Extension support */
    protected function _doMailFromCommand($address)
    {
        $handlers = $this->_getActiveHandlers();
        $params = array();
        foreach ($handlers as $handler) {
            $params = array_merge($params, (array) $handler->getMailParams());
        }
        $paramStr = !empty($params) ? ' '.implode(' ', $params) : '';
        $this->executeCommand(
            sprintf("MAIL FROM:<%s>%s\r\n", $address, $paramStr), array(250)
            );
    }

    /** Overridden to add Extension support */
    protected function _doRcptToCommand($address)
    {
        $handlers = $this->_getActiveHandlers();
        $params = array();
        foreach ($handlers as $handler) {
            $params = array_merge($params, (array) $handler->getRcptParams());
        }
        $paramStr = !empty($params) ? ' '.implode(' ', $params) : '';
        $this->executeCommand(
            sprintf("RCPT TO:<%s>%s\r\n", $address, $paramStr), array(250, 251, 252)
            );
    }

    /** Determine ESMTP capabilities by function group */
    private function _getCapabilities($ehloResponse)
    {
        $capabilities = array();
        $ehloResponse = trim($ehloResponse);
        $lines = explode("\r\n", $ehloResponse);
        array_shift($lines);
        foreach ($lines as $line) {
            if (preg_match('/^[0-9]{3}[ -]([A-Z0-9-]+)((?:[ =].*)?)$/Di', $line, $matches)) {
                $keyword = strtoupper($matches[1]);
                $paramStr = strtoupper(ltrim($matches[2], ' ='));
                $params = !empty($paramStr) ? explode(' ', $paramStr) : array();
                $capabilities[$keyword] = $params;
            }
        }

        return $capabilities;
    }

    /** Set parameters which are used by each extension handler */
    private function _setHandlerParams()
    {
        foreach ($this->_handlers as $keyword => $handler) {
            if (array_key_exists($keyword, $this->_capabilities)) {
                $handler->setKeywordParams($this->_capabilities[$keyword]);
            }
        }
    }

    /** Get ESMTP handlers which are currently ok to use */
    private function _getActiveHandlers()
    {
        $handlers = array();
        foreach ($this->_handlers as $keyword => $handler) {
            if (array_key_exists($keyword, $this->_capabilities)) {
                $handlers[] = $handler;
            }
        }

        return $handlers;
    }

    /** Custom sort for extension handler ordering */
    private function _sortHandlers($a, $b)
    {
        return $a->getPriorityOver($b->getHandledKeyword());
    }
}
