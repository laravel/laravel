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
use Monolog\Utils;
use Monolog\Formatter\FlowdockFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

/**
 * Sends notifications through the Flowdock push API
 *
 * This must be configured with a FlowdockFormatter instance via setFormatter()
 *
 * Notes:
 * API token - Flowdock API token
 *
 * @author Dominik Liebler <liebler.dominik@gmail.com>
 * @see https://www.flowdock.com/api/push
 * @deprecated Since 2.9.0 and 3.3.0, Flowdock was shutdown we will thus drop this handler in Monolog 4
 */
class FlowdockHandler extends SocketHandler
{
    protected string $apiToken;

    /**
     * @throws MissingExtensionException if OpenSSL is missing
     */
    public function __construct(
        string $apiToken,
        $level = Level::Debug,
        bool $bubble = true,
        bool $persistent = false,
        float $timeout = 0.0,
        float $writingTimeout = 10.0,
        ?float $connectionTimeout = null,
        ?int $chunkSize = null
    ) {
        if (!\extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP extension is required to use the FlowdockHandler');
        }

        parent::__construct(
            'ssl://api.flowdock.com:443',
            $level,
            $bubble,
            $persistent,
            $timeout,
            $writingTimeout,
            $connectionTimeout,
            $chunkSize
        );
        $this->apiToken = $apiToken;
    }

    /**
     * @inheritDoc
     */
    public function setFormatter(FormatterInterface $formatter): HandlerInterface
    {
        if (!$formatter instanceof FlowdockFormatter) {
            throw new \InvalidArgumentException('The FlowdockHandler requires an instance of Monolog\Formatter\FlowdockFormatter to function correctly');
        }

        return parent::setFormatter($formatter);
    }

    /**
     * Gets the default formatter.
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        throw new \InvalidArgumentException('The FlowdockHandler must be configured (via setFormatter) with an instance of Monolog\Formatter\FlowdockFormatter to function correctly');
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        parent::write($record);

        $this->closeSocket();
    }

    /**
     * @inheritDoc
     */
    protected function generateDataStream(LogRecord $record): string
    {
        $content = $this->buildContent($record);

        return $this->buildHeader($content) . $content;
    }

    /**
     * Builds the body of API call
     */
    private function buildContent(LogRecord $record): string
    {
        return Utils::jsonEncode($record->formatted);
    }

    /**
     * Builds the header of the API Call
     */
    private function buildHeader(string $content): string
    {
        $header = "POST /v1/messages/team_inbox/" . $this->apiToken . " HTTP/1.1\r\n";
        $header .= "Host: api.flowdock.com\r\n";
        $header .= "Content-Type: application/json\r\n";
        $header .= "Content-Length: " . \strlen($content) . "\r\n";
        $header .= "\r\n";

        return $header;
    }
}
