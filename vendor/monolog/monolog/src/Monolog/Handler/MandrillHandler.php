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
 * MandrillHandler uses cURL to send the emails to the Mandrill API
 *
 * @author Adam Nicholson <adamnicholson10@gmail.com>
 */
class MandrillHandler extends MailHandler
{
    protected $message;
    protected $apiKey;

    /**
     * @param string                  $apiKey  A valid Mandrill API key
     * @param callable|\Swift_Message $message An example message for real messages, only the body will be replaced
     * @param int                     $level   The minimum logging level at which this handler will be triggered
     * @param Boolean                 $bubble  Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($apiKey, $message, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        if (!$message instanceof \Swift_Message && is_callable($message)) {
            $message = call_user_func($message);
        }
        if (!$message instanceof \Swift_Message) {
            throw new \InvalidArgumentException('You must provide either a Swift_Message instance or a callable returning it');
        }
        $this->message = $message;
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    protected function send($content, array $records)
    {
        $message = clone $this->message;
        $message->setBody($content);
        $message->setDate(time());

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, 'https://mandrillapp.com/api/1.0/messages/send-raw.json');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            'key' => $this->apiKey,
            'raw_message' => (string) $message,
            'async' => false,
        )));

        Curl\Util::execute($ch);
    }
}
