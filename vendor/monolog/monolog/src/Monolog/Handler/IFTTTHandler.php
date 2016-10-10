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
 * IFTTTHandler uses cURL to trigger IFTTT Maker actions
 *
 * Register a secret key and trigger/event name at https://ifttt.com/maker
 *
 * value1 will be the channel from monolog's Logger constructor,
 * value2 will be the level name (ERROR, WARNING, ..)
 * value3 will be the log record's message
 *
 * @author Nehal Patel <nehal@nehalpatel.me>
 */
class IFTTTHandler extends AbstractProcessingHandler
{
    private $eventName;
    private $secretKey;

    /**
     * @param string  $eventName The name of the IFTTT Maker event that should be triggered
     * @param string  $secretKey A valid IFTTT secret key
     * @param int     $level     The minimum logging level at which this handler will be triggered
     * @param Boolean $bubble    Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct($eventName, $secretKey, $level = Logger::ERROR, $bubble = true)
    {
        $this->eventName = $eventName;
        $this->secretKey = $secretKey;

        parent::__construct($level, $bubble);
    }

    /**
     * {@inheritdoc}
     */
    public function write(array $record)
    {
        $postData = array(
            "value1" => $record["channel"],
            "value2" => $record["level_name"],
            "value3" => $record["message"],
        );
        $postString = json_encode($postData);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://maker.ifttt.com/trigger/" . $this->eventName . "/with/key/" . $this->secretKey);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postString);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
        ));

        Curl\Util::execute($ch);
    }
}
