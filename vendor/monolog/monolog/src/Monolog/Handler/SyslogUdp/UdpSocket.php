<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler\SyslogUdp;

use Monolog\Utils;
use Socket;

class UdpSocket
{
    protected const DATAGRAM_MAX_LENGTH = 65023;

    protected string $ip;
    protected int $port;
    protected ?Socket $socket = null;

    public function __construct(string $ip, int $port = 514)
    {
        $this->ip = $ip;
        $this->port = $port;
    }

    public function write(string $line, string $header = ""): void
    {
        $this->send($this->assembleMessage($line, $header));
    }

    public function close(): void
    {
        if ($this->socket instanceof Socket) {
            socket_close($this->socket);
            $this->socket = null;
        }
    }

    protected function getSocket(): Socket
    {
        if (null !== $this->socket) {
            return $this->socket;
        }

        $domain = AF_INET;
        $protocol = SOL_UDP;
        // Check if we are using unix sockets.
        if ($this->port === 0) {
            $domain = AF_UNIX;
            $protocol = IPPROTO_IP;
        }

        $socket = socket_create($domain, SOCK_DGRAM, $protocol);
        if ($socket instanceof Socket) {
            return $this->socket = $socket;
        }

        throw new \RuntimeException('The UdpSocket to '.$this->ip.':'.$this->port.' could not be opened via socket_create');
    }

    protected function send(string $chunk): void
    {
        socket_sendto($this->getSocket(), $chunk, \strlen($chunk), $flags = 0, $this->ip, $this->port);
    }

    protected function assembleMessage(string $line, string $header): string
    {
        $chunkSize = static::DATAGRAM_MAX_LENGTH - \strlen($header);

        return $header . Utils::substr($line, 0, $chunkSize);
    }
}
