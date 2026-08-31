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
use Monolog\LogRecord;

/**
 * @author Robert Kaufmann III <rok3@rok3.me>
 */
class LogEntriesHandler extends SocketHandler
{
    protected string $logToken;

    /**
     * @param string $token  Log token supplied by LogEntries
     * @param bool   $useSSL Whether or not SSL encryption should be used.
     * @param string $host   Custom hostname to send the data to if needed
     *
     * @throws MissingExtensionException If SSL encryption is set to true and OpenSSL is missing
     */
    public function __construct(
        string $token,
        bool $useSSL = true,
        $level = Level::Debug,
        bool $bubble = true,
        string $host = 'data.logentries.com',
        bool $persistent = false,
        float $timeout = 0.0,
        float $writingTimeout = 10.0,
        ?float $connectionTimeout = null,
        ?int $chunkSize = null
    ) {
        if ($useSSL && !\extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP plugin is required to use SSL encrypted connection for LogEntriesHandler');
        }

        $endpoint = $useSSL ? 'ssl://' . $host . ':443' : $host . ':80';
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
    }

    /**
     * @inheritDoc
     */
    protected function generateDataStream(LogRecord $record): string
    {
        return $this->logToken . ' ' . $record->formatted;
    }
}
