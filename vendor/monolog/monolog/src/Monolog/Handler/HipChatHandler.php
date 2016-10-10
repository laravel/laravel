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

/**
 * Sends notifications through the hipchat api to a hipchat room
 *
 * Notes:
 * API token - HipChat API token
 * Room      - HipChat Room Id or name, where messages are sent
 * Name      - Name used to send the message (from)
 * notify    - Should the message trigger a notification in the clients
 * version   - The API version to use (HipChatHandler::API_V1 | HipChatHandler::API_V2)
 *
 * @author Rafael Dohms <rafael@doh.ms>
 * @see    https://www.hipchat.com/docs/api
 */
class HipChatHandler extends SocketHandler
{
    /**
     * Use API version 1
     */
    const API_V1 = 'v1';

    /**
     * Use API version v2
     */
    const API_V2 = 'v2';

    /**
     * The maximum allowed length for the name used in the "from" field.
     */
    const MAXIMUM_NAME_LENGTH = 15;

    /**
     * The maximum allowed length for the message.
     */
    const MAXIMUM_MESSAGE_LENGTH = 9500;

    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $room;

    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $notify;

    /**
     * @var string
     */
    private $format;

    /**
     * @var string
     */
    private $host;

    /**
     * @var string
     */
    private $version;

    /**
     * @param string $token   HipChat API Token
     * @param string $room    The room that should be alerted of the message (Id or Name)
     * @param string $name    Name used in the "from" field.
     * @param bool   $notify  Trigger a notification in clients or not
     * @param int    $level   The minimum logging level at which this handler will be triggered
     * @param bool   $bubble  Whether the messages that are handled can bubble up the stack or not
     * @param bool   $useSSL  Whether to connect via SSL.
     * @param string $format  The format of the messages (default to text, can be set to html if you have html in the messages)
     * @param string $host    The HipChat server hostname.
     * @param string $version The HipChat API version (default HipChatHandler::API_V1)
     */
    public function __construct($token, $room, $name = 'Monolog', $notify = false, $level = Logger::CRITICAL, $bubble = true, $useSSL = true, $format = 'text', $host = 'api.hipchat.com', $version = self::API_V1)
    {
        if ($version == self::API_V1 && !$this->validateStringLength($name, static::MAXIMUM_NAME_LENGTH)) {
            throw new \InvalidArgumentException('The supplied name is too long. HipChat\'s v1 API supports names up to 15 UTF-8 characters.');
        }

        $connectionString = $useSSL ? 'ssl://'.$host.':443' : $host.':80';
        parent::__construct($connectionString, $level, $bubble);

        $this->token = $token;
        $this->name = $name;
        $this->notify = $notify;
        $this->room = $room;
        $this->format = $format;
        $this->host = $host;
        $this->version = $version;
    }

    /**
     * {@inheritdoc}
     *
     * @param  array  $record
     * @return string
     */
    protected function generateDataStream($record)
    {
        $content = $this->buildContent($record);

        return $this->buildHeader($content) . $content;
    }

    /**
     * Builds the body of API call
     *
     * @param  array  $record
     * @return string
     */
    private function buildContent($record)
    {
        $dataArray = array(
            'notify' => $this->version == self::API_V1 ?
                ($this->notify ? 1 : 0) :
                ($this->notify ? 'true' : 'false'),
            'message' => $record['formatted'],
            'message_format' => $this->format,
            'color' => $this->getAlertColor($record['level']),
        );

        // if we are using the legacy API then we need to send some additional information
        if ($this->version == self::API_V1) {
            $dataArray['room_id'] = $this->room;
        }

        // append the sender name if it is set
        // always append it if we use the v1 api (it is required in v1)
        if ($this->version == self::API_V1 || $this->name !== null) {
            $dataArray['from'] = (string) $this->name;
        }

        return http_build_query($dataArray);
    }

    /**
     * Builds the header of the API Call
     *
     * @param  string $content
     * @return string
     */
    private function buildHeader($content)
    {
        if ($this->version == self::API_V1) {
            $header = "POST /v1/rooms/message?format=json&auth_token={$this->token} HTTP/1.1\r\n";
        } else {
            // needed for rooms with special (spaces, etc) characters in the name
            $room = rawurlencode($this->room);
            $header = "POST /v2/room/{$room}/notification?auth_token={$this->token} HTTP/1.1\r\n";
        }

        $header .= "Host: {$this->host}\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($content) . "\r\n";
        $header .= "\r\n";

        return $header;
    }

    /**
     * Assigns a color to each level of log records.
     *
     * @param  int    $level
     * @return string
     */
    protected function getAlertColor($level)
    {
        switch (true) {
            case $level >= Logger::ERROR:
                return 'red';
            case $level >= Logger::WARNING:
                return 'yellow';
            case $level >= Logger::INFO:
                return 'green';
            case $level == Logger::DEBUG:
                return 'gray';
            default:
                return 'yellow';
        }
    }

    /**
     * {@inheritdoc}
     *
     * @param array $record
     */
    protected function write(array $record)
    {
        parent::write($record);
        $this->closeSocket();
    }

    /**
     * {@inheritdoc}
     */
    public function handleBatch(array $records)
    {
        if (count($records) == 0) {
            return true;
        }

        $batchRecords = $this->combineRecords($records);

        $handled = false;
        foreach ($batchRecords as $batchRecord) {
            if ($this->isHandling($batchRecord)) {
                $this->write($batchRecord);
                $handled = true;
            }
        }

        if (!$handled) {
            return false;
        }

        return false === $this->bubble;
    }

    /**
     * Combines multiple records into one. Error level of the combined record
     * will be the highest level from the given records. Datetime will be taken
     * from the first record.
     *
     * @param $records
     * @return array
     */
    private function combineRecords($records)
    {
        $batchRecord = null;
        $batchRecords = array();
        $messages = array();
        $formattedMessages = array();
        $level = 0;
        $levelName = null;
        $datetime = null;

        foreach ($records as $record) {
            $record = $this->processRecord($record);

            if ($record['level'] > $level) {
                $level = $record['level'];
                $levelName = $record['level_name'];
            }

            if (null === $datetime) {
                $datetime = $record['datetime'];
            }

            $messages[] = $record['message'];
            $messageStr = implode(PHP_EOL, $messages);
            $formattedMessages[] = $this->getFormatter()->format($record);
            $formattedMessageStr = implode('', $formattedMessages);

            $batchRecord = array(
                'message'   => $messageStr,
                'formatted' => $formattedMessageStr,
                'context'   => array(),
                'extra'     => array(),
            );

            if (!$this->validateStringLength($batchRecord['formatted'], static::MAXIMUM_MESSAGE_LENGTH)) {
                // Pop the last message and implode the remaining messages
                $lastMessage = array_pop($messages);
                $lastFormattedMessage = array_pop($formattedMessages);
                $batchRecord['message'] = implode(PHP_EOL, $messages);
                $batchRecord['formatted'] = implode('', $formattedMessages);

                $batchRecords[] = $batchRecord;
                $messages = array($lastMessage);
                $formattedMessages = array($lastFormattedMessage);

                $batchRecord = null;
            }
        }

        if (null !== $batchRecord) {
            $batchRecords[] = $batchRecord;
        }

        // Set the max level and datetime for all records
        foreach ($batchRecords as &$batchRecord) {
            $batchRecord = array_merge(
                $batchRecord,
                array(
                    'level'      => $level,
                    'level_name' => $levelName,
                    'datetime'   => $datetime,
                )
            );
        }

        return $batchRecords;
    }

    /**
     * Validates the length of a string.
     *
     * If the `mb_strlen()` function is available, it will use that, as HipChat
     * allows UTF-8 characters. Otherwise, it will fall back to `strlen()`.
     *
     * Note that this might cause false failures in the specific case of using
     * a valid name with less than 16 characters, but 16 or more bytes, on a
     * system where `mb_strlen()` is unavailable.
     *
     * @param string $str
     * @param int    $length
     *
     * @return bool
     */
    private function validateStringLength($str, $length)
    {
        if (function_exists('mb_strlen')) {
            return (mb_strlen($str) <= $length);
        }

        return (strlen($str) <= $length);
    }
}
