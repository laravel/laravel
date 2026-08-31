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

use RuntimeException;
use Monolog\Level;
use Monolog\Utils;
use Monolog\LogRecord;

/**
 * Handler sends logs to Telegram using Telegram Bot API.
 *
 * How to use:
 *  1) Create a Telegram bot with https://telegram.me/BotFather;
 *  2) Create a Telegram channel or a group where logs will be recorded;
 *  3) Add the created bot from step 1 to the created channel/group from step 2.
 *
 * In order to create an instance of TelegramBotHandler use
 *  1. The Telegram bot API key from step 1
 *  2. The channel name with the `@` prefix if you created a public channel (e.g. `@my_public_channel`),
 *     or the channel ID with the `-100` prefix if you created a private channel (e.g. `-1001234567890`),
 *     or the group ID from step 2 (e.g. `-1234567890`).
 *
 * @link https://core.telegram.org/bots/api
 *
 * @author Mazur Alexandr <alexandrmazur96@gmail.com>
 */
class TelegramBotHandler extends AbstractProcessingHandler
{
    private const BOT_API = 'https://api.telegram.org/bot';

    /**
     * The available values of parseMode according to the Telegram api documentation
     */
    private const AVAILABLE_PARSE_MODES = [
        'HTML',
        'MarkdownV2',
        'Markdown', // legacy mode without underline and strikethrough, use MarkdownV2 instead
    ];

    /**
     * The maximum number of characters allowed in a message according to the Telegram api documentation
     */
    private const MAX_MESSAGE_LENGTH = 4096;

    /**
     * Telegram bot access token provided by BotFather.
     * Create telegram bot with https://telegram.me/BotFather and use access token from it.
     */
    private string $apiKey;

    /**
     * Telegram channel name.
     * Since to start with '@' symbol as prefix.
     */
    private string $channel;

    /**
     * The kind of formatting that is used for the message.
     * See available options at https://core.telegram.org/bots/api#formatting-options
     * or in AVAILABLE_PARSE_MODES
     */
    private string|null $parseMode;

    /**
     * Disables link previews for links in the message.
     */
    private bool|null $disableWebPagePreview;

    /**
     * Sends the message silently. Users will receive a notification with no sound.
     */
    private bool|null $disableNotification;

    /**
     * True - split a message longer than MAX_MESSAGE_LENGTH into parts and send in multiple messages.
     * False - truncates a message that is too long.
     */
    private bool $splitLongMessages;

    /**
     * Adds 1-second delay between sending a split message (according to Telegram API to avoid 429 Too Many Requests).
     */
    private bool $delayBetweenMessages;

    /**
     * Telegram message thread id, unique identifier for the target message thread (topic) of the forum; for forum supergroups only
     * See how to get the `message_thread_id` https://stackoverflow.com/a/75178418
     */
    private int|null $topic;

    /**
     * @param  string                    $apiKey               Telegram bot access token provided by BotFather
     * @param  string                    $channel              Telegram channel name
     * @param  bool                      $splitLongMessages    Split a message longer than MAX_MESSAGE_LENGTH into parts and send in multiple messages
     * @param  bool                      $delayBetweenMessages Adds delay between sending a split message according to Telegram API
     * @param  int                       $topic                Telegram message thread id, unique identifier for the target message thread (topic) of the forum
     * @throws MissingExtensionException If the curl extension is missing
     */
    public function __construct(
        string $apiKey,
        string $channel,
        $level = Level::Debug,
        bool   $bubble = true,
        ?string $parseMode = null,
        ?bool   $disableWebPagePreview = null,
        ?bool   $disableNotification = null,
        bool   $splitLongMessages = false,
        bool   $delayBetweenMessages = false,
        ?int   $topic = null
    ) {
        if (!\extension_loaded('curl')) {
            throw new MissingExtensionException('The curl extension is needed to use the TelegramBotHandler');
        }

        parent::__construct($level, $bubble);

        $this->apiKey = $apiKey;
        $this->channel = $channel;
        $this->setParseMode($parseMode);
        $this->disableWebPagePreview($disableWebPagePreview);
        $this->disableNotification($disableNotification);
        $this->splitLongMessages($splitLongMessages);
        $this->delayBetweenMessages($delayBetweenMessages);
        $this->setTopic($topic);
    }

    /**
     * @return $this
     */
    public function setParseMode(string|null $parseMode = null): self
    {
        if ($parseMode !== null && !\in_array($parseMode, self::AVAILABLE_PARSE_MODES, true)) {
            throw new \InvalidArgumentException('Unknown parseMode, use one of these: ' . implode(', ', self::AVAILABLE_PARSE_MODES) . '.');
        }

        $this->parseMode = $parseMode;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableWebPagePreview(bool|null $disableWebPagePreview = null): self
    {
        $this->disableWebPagePreview = $disableWebPagePreview;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableNotification(bool|null $disableNotification = null): self
    {
        $this->disableNotification = $disableNotification;

        return $this;
    }

    /**
     * True - split a message longer than MAX_MESSAGE_LENGTH into parts and send in multiple messages.
     * False - truncates a message that is too long.
     *
     * @return $this
     */
    public function splitLongMessages(bool $splitLongMessages = false): self
    {
        $this->splitLongMessages = $splitLongMessages;

        return $this;
    }

    /**
     * Adds 1-second delay between sending a split message (according to Telegram API to avoid 429 Too Many Requests).
     *
     * @return $this
     */
    public function delayBetweenMessages(bool $delayBetweenMessages = false): self
    {
        $this->delayBetweenMessages = $delayBetweenMessages;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTopic(?int $topic = null): self
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function handleBatch(array $records): void
    {
        $messages = [];

        foreach ($records as $record) {
            if (!$this->isHandling($record)) {
                continue;
            }

            if (\count($this->processors) > 0) {
                $record = $this->processRecord($record);
            }

            $messages[] = $record;
        }

        if (\count($messages) > 0) {
            $this->send((string) $this->getFormatter()->formatBatch($messages));
        }
    }

    /**
     * @inheritDoc
     */
    protected function write(LogRecord $record): void
    {
        $this->send($record->formatted);
    }

    /**
     * Send request to @link https://api.telegram.org/bot on SendMessage action.
     */
    protected function send(string $message): void
    {
        $messages = $this->handleMessageLength($message);

        foreach ($messages as $key => $msg) {
            if ($this->delayBetweenMessages && $key > 0) {
                sleep(1);
            }

            $this->sendCurl($msg);
        }
    }

    protected function sendCurl(string $message): void
    {
        if ('' === trim($message)) {
            return;
        }
        
        $ch = curl_init();
        $url = self::BOT_API . $this->apiKey . '/SendMessage';
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        $params = [
            'text' => $message,
            'chat_id' => $this->channel,
            'parse_mode' => $this->parseMode,
            'disable_web_page_preview' => $this->disableWebPagePreview,
            'disable_notification' => $this->disableNotification,
        ];
        if ($this->topic !== null) {
            $params['message_thread_id'] = $this->topic;
        }
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));

        $result = Curl\Util::execute($ch);
        if (!\is_string($result)) {
            throw new RuntimeException('Telegram API error. Description: No response');
        }
        $result = json_decode($result, true);

        if ($result['ok'] === false) {
            throw new RuntimeException('Telegram API error. Description: ' . $result['description']);
        }
    }

    /**
     * Handle a message that is too long: truncates or splits into several
     * @return string[]
     */
    private function handleMessageLength(string $message): array
    {
        $truncatedMarker = ' (…truncated)';
        if (!$this->splitLongMessages && \strlen($message) > self::MAX_MESSAGE_LENGTH) {
            return [Utils::substr($message, 0, self::MAX_MESSAGE_LENGTH - \strlen($truncatedMarker)) . $truncatedMarker];
        }

        return str_split($message, self::MAX_MESSAGE_LENGTH);
    }
}
