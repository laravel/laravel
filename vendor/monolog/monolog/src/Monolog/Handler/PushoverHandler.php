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
use Monolog\Logger;
use Monolog\Utils;
use Psr\Log\LogLevel;
use Monolog\LogRecord;

/**
 * Sends notifications through the pushover api to mobile phones
 *
 * @author Sebastian Göttschkes <sebastian.goettschkes@googlemail.com>
 * @see    https://www.pushover.net/api
 */
class PushoverHandler extends SocketHandler
{
    private string $token;

    /** @var array<int|string> */
    private array $users;

    private string $title;

    private string|int|null $user = null;

    private int $retry;

    private int $expire;

    private Level $highPriorityLevel;

    private Level $emergencyLevel;

    private bool $useFormattedMessage = false;

    /**
     * All parameters that can be sent to Pushover
     * @see https://pushover.net/api
     * @var array<string, bool>
     */
    private array $parameterNames = [
        'token' => true,
        'user' => true,
        'message' => true,
        'device' => true,
        'title' => true,
        'url' => true,
        'url_title' => true,
        'priority' => true,
        'timestamp' => true,
        'sound' => true,
        'retry' => true,
        'expire' => true,
        'callback' => true,
    ];

    /**
     * Sounds the api supports by default
     * @see https://pushover.net/api#sounds
     * @var string[]
     */
    private array $sounds = [
        'pushover', 'bike', 'bugle', 'cashregister', 'classical', 'cosmic', 'falling', 'gamelan', 'incoming',
        'intermission', 'magic', 'mechanical', 'pianobar', 'siren', 'spacealarm', 'tugboat', 'alien', 'climb',
        'persistent', 'echo', 'updown', 'none',
    ];

    /**
     * @param string       $token  Pushover api token
     * @param string|array $users  Pushover user id or array of ids the message will be sent to
     * @param string|null  $title  Title sent to the Pushover API
     * @param bool         $useSSL Whether to connect via SSL. Required when pushing messages to users that are not
     *                             the pushover.net app owner. OpenSSL is required for this option.
     * @param int          $retry  The retry parameter specifies how often (in seconds) the Pushover servers will
     *                             send the same notification to the user.
     * @param int          $expire The expire parameter specifies how many seconds your notification will continue
     *                             to be retried for (every retry seconds).
     *
     * @param int|string|Level|LogLevel::* $highPriorityLevel The minimum logging level at which this handler will start
     *                                                        sending "high priority" requests to the Pushover API
     * @param int|string|Level|LogLevel::* $emergencyLevel    The minimum logging level at which this handler will start
     *                                                        sending "emergency" requests to the Pushover API
     *
     *
     * @phpstan-param string|array<int|string>    $users
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $highPriorityLevel
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $emergencyLevel
     */
    public function __construct(
        string $token,
        $users,
        ?string $title = null,
        int|string|Level $level = Level::Critical,
        bool $bubble = true,
        bool $useSSL = true,
        int|string|Level $highPriorityLevel = Level::Critical,
        int|string|Level $emergencyLevel = Level::Emergency,
        int $retry = 30,
        int $expire = 25200,
        bool $persistent = false,
        float $timeout = 0.0,
        float $writingTimeout = 10.0,
        ?float $connectionTimeout = null,
        ?int $chunkSize = null
    ) {
        $connectionString = $useSSL ? 'ssl://api.pushover.net:443' : 'api.pushover.net:80';
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

        $this->token = $token;
        $this->users = (array) $users;
        $this->title = $title ?? (string) gethostname();
        $this->highPriorityLevel = Logger::toMonologLevel($highPriorityLevel);
        $this->emergencyLevel = Logger::toMonologLevel($emergencyLevel);
        $this->retry = $retry;
        $this->expire = $expire;
    }

    protected function generateDataStream(LogRecord $record): string
    {
        $content = $this->buildContent($record);

        return $this->buildHeader($content) . $content;
    }

    private function buildContent(LogRecord $record): string
    {
        // Pushover has a limit of 512 characters on title and message combined.
        $maxMessageLength = 512 - \strlen($this->title);

        $message = ($this->useFormattedMessage) ? $record->formatted : $record->message;
        $message = Utils::substr($message, 0, $maxMessageLength);

        $timestamp = $record->datetime->getTimestamp();

        $dataArray = [
            'token' => $this->token,
            'user' => $this->user,
            'message' => $message,
            'title' => $this->title,
            'timestamp' => $timestamp,
        ];

        if ($record->level->value >= $this->emergencyLevel->value) {
            $dataArray['priority'] = 2;
            $dataArray['retry'] = $this->retry;
            $dataArray['expire'] = $this->expire;
        } elseif ($record->level->value >= $this->highPriorityLevel->value) {
            $dataArray['priority'] = 1;
        }

        // First determine the available parameters
        $context = array_intersect_key($record->context, $this->parameterNames);
        $extra = array_intersect_key($record->extra, $this->parameterNames);

        // Least important info should be merged with subsequent info
        $dataArray = array_merge($extra, $context, $dataArray);

        // Only pass sounds that are supported by the API
        if (isset($dataArray['sound']) && !\in_array($dataArray['sound'], $this->sounds, true)) {
            unset($dataArray['sound']);
        }

        return http_build_query($dataArray);
    }

    private function buildHeader(string $content): string
    {
        $header = "POST /1/messages.json HTTP/1.1\r\n";
        $header .= "Host: api.pushover.net\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . \strlen($content) . "\r\n";
        $header .= "\r\n";

        return $header;
    }

    protected function write(LogRecord $record): void
    {
        foreach ($this->users as $user) {
            $this->user = $user;

            parent::write($record);
            $this->closeSocket();
        }

        $this->user = null;
    }

    /**
     * @param  int|string|Level|LogLevel::* $level
     * @return $this
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    public function setHighPriorityLevel(int|string|Level $level): self
    {
        $this->highPriorityLevel = Logger::toMonologLevel($level);

        return $this;
    }

    /**
     * @param  int|string|Level|LogLevel::* $level
     * @return $this
     *
     * @phpstan-param value-of<Level::VALUES>|value-of<Level::NAMES>|Level|LogLevel::* $level
     */
    public function setEmergencyLevel(int|string|Level $level): self
    {
        $this->emergencyLevel = Logger::toMonologLevel($level);

        return $this;
    }

    /**
     * Use the formatted message?
     *
     * @return $this
     */
    public function useFormattedMessage(bool $useFormattedMessage): self
    {
        $this->useFormattedMessage = $useFormattedMessage;

        return $this;
    }
}
