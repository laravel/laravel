<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Contains a list of redundant Transports so when one fails, the next is used.
 *
 * @author Chris Corbyn
 */
class Swift_FailoverTransport extends Swift_Transport_FailoverTransport
{
    /**
     * Creates a new FailoverTransport with $transports.
     *
     * @param Swift_Transport[] $transports
     */
    public function __construct($transports = array())
    {
        call_user_func_array(
            array($this, 'Swift_Transport_FailoverTransport::__construct'),
            Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('transport.failover')
            );

        $this->setTransports($transports);
    }

    /**
     * Create a new FailoverTransport instance.
     *
     * @param Swift_Transport[] $transports
     *
     * @return Swift_FailoverTransport
     */
    public static function newInstance($transports = array())
    {
        return new self($transports);
    }
}
