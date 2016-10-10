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
 * SwiftMailerHandler uses Swift_Mailer to send the emails
 *
 * @author Gyula Sallai
 */
class SwiftMailerHandler extends MailHandler
{
    protected $mailer;
    private $messageTemplate;

    /**
     * @param \Swift_Mailer           $mailer  The mailer to use
     * @param callable|\Swift_Message $message An example message for real messages, only the body will be replaced
     * @param int                     $level   The minimum logging level at which this handler will be triggered
     * @param Boolean                 $bubble  Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(\Swift_Mailer $mailer, $message, $level = Logger::ERROR, $bubble = true)
    {
        parent::__construct($level, $bubble);

        $this->mailer = $mailer;
        $this->messageTemplate = $message;
    }

    /**
     * {@inheritdoc}
     */
    protected function send($content, array $records)
    {
        $this->mailer->send($this->buildMessage($content, $records));
    }

    /**
     * Creates instance of Swift_Message to be sent
     *
     * @param  string         $content formatted email body to be sent
     * @param  array          $records Log records that formed the content
     * @return \Swift_Message
     */
    protected function buildMessage($content, array $records)
    {
        $message = null;
        if ($this->messageTemplate instanceof \Swift_Message) {
            $message = clone $this->messageTemplate;
            $message->generateId();
        } elseif (is_callable($this->messageTemplate)) {
            $message = call_user_func($this->messageTemplate, $content, $records);
        }

        if (!$message instanceof \Swift_Message) {
            throw new \InvalidArgumentException('Could not resolve message as instance of Swift_Message or a callable returning it');
        }

        $message->setBody($content);
        $message->setDate(time());

        return $message;
    }

    /**
     * BC getter, to be removed in 2.0
     */
    public function __get($name)
    {
        if ($name === 'message') {
            trigger_error('SwiftMailerHandler->message is deprecated, use ->buildMessage() instead to retrieve the message', E_USER_DEPRECATED);

            return $this->buildMessage(null, array());
        }

        throw new \InvalidArgumentException('Invalid property '.$name);
    }
}
