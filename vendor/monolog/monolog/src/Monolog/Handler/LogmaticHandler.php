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

use Monolog\Level;
use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LogmaticFormatter;
use Monolog\LogRecord;

/**
 * @author Julien Breux <julien.breux@gmail.com>
 */
class LogmaticHandler extends SocketHandler
{
    private string $logToken;

    private string $hostname;

    private string $appName;

    /**
     * @param string $token    Log token supplied by Logmatic.
     * @param string $hostname Host name supplied by Logmatic.
     * @param string $appName  Application name supplied by Logmatic.
     * @param bool   $useSSL   Whether or not SSL encryption should be used.
     *
     * @throws MissingExtensionException If SSL encryption is set to true and OpenSSL is missing
     */
    public function __construct(
        string $token,
        string $hostname = '',
        string $appName = '',
        bool $useSSL = true,
        $level = Level::Debug,
        bool $bubble = true,
        bool $persistent = false,
        float $timeout = 0.0,
        float $writingTimeout = 10.0,
        ?float $connectionTimeout = null,
        ?int $chunkSize = null
    ) {
        if ($useSSL && !\extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP extension is required to use SSL encrypted connection for LogmaticHandler');
        }

        $endpoint = $useSSL ? 'ssl://api.logmatic.io:10515' : 'api.logmatic.io:10514';
        $endpoint .= '/v1/';

        parent::__construct(
            $endpoint,
            $level,
            $bubble,
            $persistent,
            $timeout,
            $writingTimeout,
            $connectionTimeout,
            $chunkSize
        );

        $this->logToken = $token;
        $this->hostname = $hostname;
        $this->appName  = $appName;
    }

    /**
     * @inheritDoc
     */
    protected function generateDataStream(LogRecord $record): string
    {
        return $this->logToken . ' ' . $record->formatted;
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        $formatter = new LogmaticFormatter();

        if ($this->hostname !== '') {
            $formatter->setHostname($this->hostname);
        }
        if ($this->appName !== '') {
            $formatter->setAppName($this->appName);
        }

        return $formatter;
    }
}
