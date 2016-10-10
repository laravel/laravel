<?php

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use Monolog\Logger;
use Monolog\Handler\SyslogUdp\UdpSocket;

/**
 * A Handler for logging to a remote syslogd server.
 *
 * @author Jesper Skovgaard Nielsen <nulpunkt@gmail.com>
 */
class SyslogUdpHandler extends AbstractSyslogHandler
{
    protected $socket;

    /**
     * @param string  $host
     * @param int     $port
     * @param mixed   $facility
     * @param int     $level    The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble   Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($host, $port = 514, $facility = LOG_USER, $level = Logger::DEBUG, $bubble = true)
    {
        parent::__construct($facility, $level, $bubble);

        $this->socket = new UdpSocket($host, $port ?: 514);
    }

    protected function write(array $record)
    {
        $lines = $this->splitMessageIntoLines($record['formatted']);

        $header = $this->makeCommonSyslogHeader($this->logLevels[$record['level']]);

        foreach ($lines as $line) {
            $this->socket->write($line, $header);
        }
    }

    public function close()
    {
        $this->socket->close();
    }

    private function splitMessageIntoLines($message)
    {
        if (is_array($message)) {
            $message = implode("\n", $message);
        }

        return preg_split('/$\R?^/m', $message);
    }

    /**
     * Make common syslog header (see rfc5424)
     */
    protected function makeCommonSyslogHeader($severity)
    {
        $priority = $severity + $this->facility;

        return "<$priority>1 ";
    }

    /**
     * Inject your own socket, mainly used for testing
     */
    public function setSocket($socket)
    {
        $this->socket = $socket;
    }
}
