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

use Monolog\Formatter\WildfireFormatter;
use Monolog\Formatter\FormatterInterface;
use Monolog\LogRecord;

/**
 * Simple FirePHP Handler (http://www.firephp.org/), which uses the Wildfire protocol.
 *
 * @author Eric Clemmons (@ericclemmons) <eric@uxdriven.com>
 */
class FirePHPHandler extends AbstractProcessingHandler
{
    use WebRequestRecognizerTrait;

    /**
     * WildFire JSON header message format
     */
    protected const PROTOCOL_URI = 'http://meta.wildfirehq.org/Protocol/JsonStream/0.2';

    /**
     * FirePHP structure for parsing messages & their presentation
     */
    protected const STRUCTURE_URI = 'http://meta.firephp.org/Wildfire/Structure/FirePHP/FirebugConsole/0.1';

    /**
     * Must reference a "known" plugin, otherwise headers won't display in FirePHP
     */
    protected const PLUGIN_URI = 'http://meta.firephp.org/Wildfire/Plugin/FirePHP/Library-FirePHPCore/0.3';

    /**
     * Header prefix for Wildfire to recognize & parse headers
     */
    protected const HEADER_PREFIX = 'X-Wf';

    /**
     * Whether or not Wildfire vendor-specific headers have been generated & sent yet
     */
    protected static bool $initialized = false;

    /**
     * Shared static message index between potentially multiple handlers
     */
    protected static int $messageIndex = 1;

    protected static bool $sendHeaders = true;

    /**
     * Base header creation function used by init headers & record headers
     *
     * @param array<int|string> $meta    Wildfire Plugin, Protocol & Structure Indexes
     * @param string            $message Log message
     *
     * @return array<string, string> Complete header string ready for the client as key and message as value
     *
     * @phpstan-return non-empty-array<string, string>
     */
    protected function createHeader(array $meta, string $message): array
    {
        $header = sprintf('%s-%s', static::HEADER_PREFIX, join('-', $meta));

        return [$header => $message];
    }

    /**
     * Creates message header from record
     *
     * @return array<string, string>
     *
     * @phpstan-return non-empty-array<string, string>
     *
     * @see createHeader()
     */
    protected function createRecordHeader(LogRecord $record): array
    {
        // Wildfire is extensible to support multiple protocols & plugins in a single request,
        // but we're not taking advantage of that (yet), so we're using "1" for simplicity's sake.
        return $this->createHeader(
            [1, 1, 1, self::$messageIndex++],
            $record->formatted
        );
    }

    /**
     * @inheritDoc
     */
    protected function getDefaultFormatter(): FormatterInterface
    {
        return new WildfireFormatter();
    }

    /**
     * Wildfire initialization headers to enable message parsing
     *
     * @see createHeader()
     * @see sendHeader()
     *
     * @return array<string, string>
     */
    protected function getInitHeaders(): array
    {
        // Initial payload consists of required headers for Wildfire
        return array_merge(
            $this->createHeader(['Protocol', 1], static::PROTOCOL_URI),
            $this->createHeader([1, 'Structure', 1], static::STRUCTURE_URI),
            $this->createHeader([1, 'Plugin', 1], static::PLUGIN_URI)
        );
    }

    /**
     * Send header string to the client
     */
    protected function sendHeader(string $header, string $content): void
    {
        if (!headers_sent() && self::$sendHeaders) {
            header(sprintf('%s: %s', $header, $content));
        }
    }

    /**
     * Creates & sends header for a record, ensuring init headers have been sent prior
     *
     * @see sendHeader()
     * @see sendInitHeaders()
     */
    protected function write(LogRecord $record): void
    {
        if (!self::$sendHeaders || !$this->isWebRequest()) {
            return;
        }

        // WildFire-specific headers must be sent prior to any messages
        if (!self::$initialized) {
            self::$initialized = true;

            self::$sendHeaders = $this->headersAccepted();
            if (!self::$sendHeaders) {
                return;
            }

            foreach ($this->getInitHeaders() as $header => $content) {
                $this->sendHeader($header, $content);
            }
        }

        $header = $this->createRecordHeader($record);
        if (trim(current($header)) !== '') {
            $this->sendHeader(key($header), current($header));
        }
    }

    /**
     * Verifies if the headers are accepted by the current user agent
     */
    protected function headersAccepted(): bool
    {
        if (isset($_SERVER['HTTP_USER_AGENT']) && 1 === preg_match('{\bFirePHP/\d+\.\d+\b}', $_SERVER['HTTP_USER_AGENT'])) {
            return true;
        }

        return isset($_SERVER['HTTP_X_FIREPHP_VERSION']);
    }
}
