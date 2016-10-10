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
use Monolog\Formatter\LineFormatter;

/**
 * Sends notifications through Slack API
 *
 * @author Greg Kedzierski <greg@gregkedzierski.com>
 * @see    https://api.slack.com/
 */
class SlackHandler extends SocketHandler
{
    /**
     * Slack API token
     * @var string
     */
    private $token;

    /**
     * Slack channel (encoded ID or name)
     * @var string
     */
    private $channel;

    /**
     * Name of a bot
     * @var string
     */
    private $username;

    /**
     * Emoji icon name
     * @var string
     */
    private $iconEmoji;

    /**
     * Whether the message should be added to Slack as attachment (plain text otherwise)
     * @var bool
     */
    private $useAttachment;

    /**
     * Whether the the context/extra messages added to Slack as attachments are in a short style
     * @var bool
     */
    private $useShortAttachment;

    /**
     * Whether the attachment should include context and extra data
     * @var bool
     */
    private $includeContextAndExtra;

    /**
     * @var LineFormatter
     */
    private $lineFormatter;

    /**
     * @param  string                    $token                  Slack API token
     * @param  string                    $channel                Slack channel (encoded ID or name)
     * @param  string                    $username               Name of a bot
     * @param  bool                      $useAttachment          Whether the message should be added to Slack as attachment (plain text otherwise)
     * @param  string|null               $iconEmoji              The emoji name to use (or null)
     * @param  int                       $level                  The minimum logging level at which this handler will be triggered
     * @param  bool                      $bubble                 Whether the messages that are handled can bubble up the stack or not
     * @param  bool                      $useShortAttachment     Whether the the context/extra messages added to Slack as attachments are in a short style
     * @param  bool                      $includeContextAndExtra Whether the attachment should include context and extra data
     * @throws MissingExtensionException If no OpenSSL PHP extension configured
     */
    public function __construct($token, $channel, $username = 'Monolog', $useAttachment = true, $iconEmoji = null, $level = Logger::CRITICAL, $bubble = true, $useShortAttachment = false, $includeContextAndExtra = false)
    {
        if (!extension_loaded('openssl')) {
            throw new MissingExtensionException('The OpenSSL PHP extension is required to use the SlackHandler');
        }

        parent::__construct('ssl://slack.com:443', $level, $bubble);

        $this->token = $token;
        $this->channel = $channel;
        $this->username = $username;
        $this->iconEmoji = trim($iconEmoji, ':');
        $this->useAttachment = $useAttachment;
        $this->useShortAttachment = $useShortAttachment;
        $this->includeContextAndExtra = $includeContextAndExtra;

        if ($this->includeContextAndExtra && $this->useShortAttachment) {
            $this->lineFormatter = new LineFormatter;
        }
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
        $dataArray = $this->prepareContentData($record);

        return http_build_query($dataArray);
    }

    /**
     * Prepares content data
     *
     * @param  array $record
     * @return array
     */
    protected function prepareContentData($record)
    {
        $dataArray = array(
            'token'       => $this->token,
            'channel'     => $this->channel,
            'username'    => $this->username,
            'text'        => '',
            'attachments' => array(),
        );

        if ($this->useAttachment) {
            $attachment = array(
                'fallback' => $record['message'],
                'color'    => $this->getAttachmentColor($record['level']),
                'fields'   => array(),
            );

            if ($this->useShortAttachment) {
                $attachment['title'] = $record['level_name'];
                $attachment['text'] = $record['message'];
            } else {
                $attachment['title'] = 'Message';
                $attachment['text'] = $record['message'];
                $attachment['fields'][] = array(
                    'title' => 'Level',
                    'value' => $record['level_name'],
                    'short' => true,
                );
            }

            if ($this->includeContextAndExtra) {
                if (!empty($record['extra'])) {
                    if ($this->useShortAttachment) {
                        $attachment['fields'][] = array(
                            'title' => "Extra",
                            'value' => $this->stringify($record['extra']),
                            'short' => $this->useShortAttachment,
                        );
                    } else {
                        // Add all extra fields as individual fields in attachment
                        foreach ($record['extra'] as $var => $val) {
                            $attachment['fields'][] = array(
                                'title' => $var,
                                'value' => $val,
                                'short' => $this->useShortAttachment,
                            );
                        }
                    }
                }

                if (!empty($record['context'])) {
                    if ($this->useShortAttachment) {
                        $attachment['fields'][] = array(
                            'title' => "Context",
                            'value' => $this->stringify($record['context']),
                            'short' => $this->useShortAttachment,
                        );
                    } else {
                        // Add all context fields as individual fields in attachment
                        foreach ($record['context'] as $var => $val) {
                            $attachment['fields'][] = array(
                                'title' => $var,
                                'value' => $val,
                                'short' => $this->useShortAttachment,
                            );
                        }
                    }
                }
            }

            $dataArray['attachments'] = json_encode(array($attachment));
        } else {
            $dataArray['text'] = $record['message'];
        }

        if ($this->iconEmoji) {
            $dataArray['icon_emoji'] = ":{$this->iconEmoji}:";
        }

        return $dataArray;
    }

    /**
     * Builds the header of the API Call
     *
     * @param  string $content
     * @return string
     */
    private function buildHeader($content)
    {
        $header = "POST /api/chat.postMessage HTTP/1.1\r\n";
        $header .= "Host: slack.com\r\n";
        $header .= "Content-Type: application/x-www-form-urlencoded\r\n";
        $header .= "Content-Length: " . strlen($content) . "\r\n";
        $header .= "\r\n";

        return $header;
    }

    /**
     * {@inheritdoc}
     *
     * @param array $record
     */
    protected function write(array $record)
    {
        parent::write($record);
        $res = $this->getResource();
        if (is_resource($res)) {
            @fread($res, 2048);
        }
        $this->closeSocket();
    }

    /**
     * Returned a Slack message attachment color associated with
     * provided level.
     *
     * @param  int    $level
     * @return string
     */
    protected function getAttachmentColor($level)
    {
        switch (true) {
            case $level >= Logger::ERROR:
                return 'danger';
            case $level >= Logger::WARNING:
                return 'warning';
            case $level >= Logger::INFO:
                return 'good';
            default:
                return '#e3e4e6';
        }
    }

    /**
     * Stringifies an array of key/value pairs to be used in attachment fields
     *
     * @param  array  $fields
     * @return string
     */
    protected function stringify($fields)
    {
        $string = '';
        foreach ($fields as $var => $val) {
            $string .= $var.': '.$this->lineFormatter->stringify($val)." | ";
        }

        $string = rtrim($string, " |");

        return $string;
    }
}
