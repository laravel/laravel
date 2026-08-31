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

use Monolog\Formatter\FormatterInterface;
use Monolog\Formatter\LineFormatter;
use Monolog\Level;
use Monolog\LogRecord;

/**
 * Sends logs to Fleep.io using Webhook integrations
 *
 * You'll need a Fleep.io account to use this handler.
 *
 * @see https://fleep.io/integrations/webhooks/ Fleep Webhooks Documentation
 * @author Ando Roots <ando@sqroot.eu>
 */
class FleepHookHandler extends SocketHandler
{
    protected const FLEEP_HOST = 'fleep.io';

    protected const FLEEP_HOOK_URI = '/hook/';

    /**
     * @var string Webhook token (specifies the conversation where logs are sent)
     */
    protected string $token;

    /**
     * Construct a new Fleep.io Handler.
     *
     * For instructions on how to create a new web hook in your conversations
     * see https://fleep.io/integrations/webhooks/
     *
     * @param  string                    $token Webhook token
     * @throws MissingExtensionException if OpenSSL is missing
     */
    public function __construct(
        string $token,
        $level = Level::Debug,
        bool $bubble = true,
        bool $persistent = false,
        float $timeout = 0.0,
        float $writingTimeout = 10.0,
        ?float $connectionTimeout = null,
        ?int $chunkSize = null
    ) {
        if (!\extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP extension is required to use the FleepHookHandler');
        }

        $this->token = $token;

        $connectionString = 'ssl://' . static::FLEEP_HOST . ':443';
        parent::__construct(
            $connectionString,
            $level,
            $bubble,
            $persistent,
            $timeout,
            $writingTimeout,
            $connectionTimeout,
            $chunkSize
        );
    }

    /**
     * Returns the default formatter to use with this handler
     *
     * Overloaded to remove empty context and extra arrays from the end of the log message.
     *
     * @return LineFormatter
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new LineFormatter(null, null, true, true);
    }

    /**
     * Handles a log record
     */
    public function write(LogRecord $record): void
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
     * Builds the header of the API Call
     */
    private function buildHeader(string $content): string
    {
        $header = "POST " . static::FLEEP_HOOK_URI . $this->token . " HTTP/1.1\r\n";
        $header .= "Host: " . static::FLEEP_HOST . "\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . \strlen($content) . "\r\n";
        $header .= "\r\n";

        return $header;
    }

    /**
     * Builds the body of API call
     */
    private function buildContent(LogRecord $record): string
    {
        $dataArray = [
            'message' => $record->formatted,
        ];

        return http_build_query($dataArray);
    }
}
