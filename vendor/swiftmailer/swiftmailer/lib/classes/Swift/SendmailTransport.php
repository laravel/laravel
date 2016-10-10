<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * SendmailTransport for sending mail through a Sendmail/Postfix (etc..) binary.
 *
 * @author Chris Corbyn
 */
class Swift_SendmailTransport extends Swift_Transport_SendmailTransport
{
    /**
     * Create a new SendmailTransport, optionally using $command for sending.
     *
     * @param string $command
     */
    public function __construct($command = '/usr/sbin/sendmail -bs')
    {
        call_user_func_array(
            array($this, 'Swift_Transport_SendmailTransport::__construct'),
            Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('transport.sendmail')
            );

        $this->setCommand($command);
    }

    /**
     * Create a new SendmailTransport instance.
     *
     * @param string $command
     *
     * @return Swift_SendmailTransport
     */
    public static function newInstance($command = '/usr/sbin/sendmail -bs')
    {
        return new self($command);
    }
}
