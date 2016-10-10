<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sends Messages using the mail() function.
 *
 * @author Chris Corbyn
 */
class Swift_MailTransport extends Swift_Transport_MailTransport
{
    /**
     * Create a new MailTransport, optionally specifying $extraParams.
     *
     * @param string $extraParams
     */
    public function __construct($extraParams = '-f%s')
    {
        call_user_func_array(
            array($this, 'Swift_Transport_MailTransport::__construct'),
            Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('transport.mail')
            );

        $this->setExtraParams($extraParams);
    }

    /**
     * Create a new MailTransport instance.
     *
     * @param string $extraParams To be passed to mail()
     *
     * @return Swift_MailTransport
     */
    public static function newInstance($extraParams = '-f%s')
    {
        return new self($extraParams);
    }
}
