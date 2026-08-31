<?php declare(strict_types=1);

/*
 * This file is part of the Monolog package.
 *
 * (c) Jordi Boggiano <j.boggiano@seld.be>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Monolog\Handler;

use DateTimeInterface;
use Monolog\Handler\SyslogUdp\UdpSocket;
use Monolog\Level;
use Monolog\LogRecord;
use Monolog\Utils;

/**
 * A Handler for logging to a remote syslogd server.
 *
 * @author Jesper Skovgaard Nielsen <nulpunkt@gmail.com>
 * @author Dominik Kukacka <dominik.kukacka@gmail.com>
 */
class SyslogUdpHandler extends AbstractSyslogHandler
{
    const RFC3164 = 0;
    const RFC5424 = 1;
    const RFC5424e = 2;

    /** @var array<self::RFC*, string> */
    private array $dateFormats = [
        self::RFC3164 => 'M d H:i:s',
        self::RFC5424 => \DateTime::RFC3339,
        self::RFC5424e => \DateTime::RFC3339_EXTENDED,
    ];

    protected UdpSocket $socket;
    protected string $ident;
    /** @var self::RFC* */
    protected int $rfc;

    /**
     * @param  string                    $host     Either IP/hostname or a path to a unix socket (port must be 0 then)
     * @param  int                       $port     Port number, or 0 if $host is a unix socket
     * @param  string|int                $facility Either one of the names of the keys in $this->facilities, or a LOG_* facility constant
     * @param  bool                      $bubble   Whether the messages that are handled can bubble up the stack or not
     * @param  string                    $ident    Program name or tag for each log message.
     * @param  int                       $rfc      RFC to format the message for.
     * @throws MissingExtensionException when there is no socket extension
     *
     * @phpstan-param self::RFC* $rfc
     */
    public function __construct(string $host, int $port = 514, string|int $facility = LOG_USER, int|string|Level $level = Level::Debug, bool $bubble = true, string $ident = 'php', int $rfc = self::RFC5424)
    {
        if (!\extension_loaded('sockets')) {
            throw new MissingExtensionException('The sockets extension is required to use the SyslogUdpHandler');
        }

        parent::__construct($facility, $level, $bubble);

        $this->ident = $ident;
        $this->rfc = $rfc;

        $this->socket = new UdpSocket($host, $port);
    }

    protected function write(LogRecord $record): void
    {
        $lines = $this->splitMessageIntoLines($record->formatted);

        $header = $this->makeCommonSyslogHeader($this->toSyslogPriority($record->level), $record->datetime);

        foreach ($lines as $line) {
            $this->socket->write($line, $header);
        }
    }

    public function close(): void
    {
        $this->socket->close();
    }

    /**
     * @param  string|string[] $message
     * @return string[]
     */
    private function splitMessageIntoLines($message): array
    {
        if (\is_array($message)) {
            $message = implode("\n", $message);
        }

        $lines = preg_split('/$\R?^/m', (string) $message, -1, PREG_SPLIT_NO_EMPTY);
        if (false === $lines) {
            $pcreErrorCode = preg_last_error();

            throw new \RuntimeException('Could not preg_split: ' . $pcreErrorCode . ' / ' . preg_last_error_msg());
        }

        return $lines;
    }

    /**
     * Make common syslog header (see rfc5424 or rfc3164)
     */
    protected function makeCommonSyslogHeader(int $severity, DateTimeInterface $datetime): string
    {
        $priority = $severity + $this->facility;

        $pid = getmypid();
        if (false === $pid) {
            $pid = '-';
        }

        $hostname = gethostname();
        if (false === $hostname) {
            $hostname = '-';
        }

        if ($this->rfc === self::RFC3164) {
            // see https://github.com/phpstan/phpstan/issues/5348
            // @phpstan-ignore-next-line
            $dateNew = $datetime->setTimezone(new \DateTimeZone('UTC'));
            $date = $dateNew->format($this->dateFormats[$this->rfc]);

            return "<$priority>" .
                $date . " " .
                $hostname . " " .
                $this->ident . "[" . $pid . "]: ";
        }

        $date = $datetime->format($this->dateFormats[$this->rfc]);

        return "<$priority>1 " .
            $date . " " .
            $hostname . " " .
            $this->ident . " " .
            $pid . " - - ";
    }

    /**
     * Inject your own socket, mainly used for testing
     *
     * @return $this
     */
    public function setSocket(UdpSocket $socket): self
    {
        $this->socket = $socket;

        return $this;
    }
}
