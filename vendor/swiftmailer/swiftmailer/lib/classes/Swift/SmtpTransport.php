<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2004-2009 Chris Corbyn
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sends Messages over SMTP with ESMTP support.
 *
 * @author Chris Corbyn
 *
 * @method Swift_SmtpTransport setUsername(string $username) Set the username to authenticate with.
 * @method string              getUsername()                 Get the username to authenticate with.
 * @method Swift_SmtpTransport setPassword(string $password) Set the password to authenticate with.
 * @method string              getPassword()                 Get the password to authenticate with.
 * @method Swift_SmtpTransport setAuthMode(string $mode)     Set the auth mode to use to authenticate.
 * @method string              getAuthMode()                 Get the auth mode to use to authenticate.
 */
class Swift_SmtpTransport extends Swift_Transport_EsmtpTransport
{
    /**
     * Create a new SmtpTransport, optionally with $host, $port and $security.
     *
     * @param string $host
     * @param int    $port
     * @param string $security
     */
    public function __construct($host = 'localhost', $port = 25, $security = null)
    {
        call_user_func_array(
            array($this, 'Swift_Transport_EsmtpTransport::__construct'),
            Swift_DependencyContainer::getInstance()
                ->createDependenciesFor('transport.smtp')
            );

        $this->setHost($host);
        $this->setPort($port);
        $this->setEncryption($security);
    }

    /**
     * Create a new SmtpTransport instance.
     *
     * @param string $host
     * @param int    $port
     * @param string $security
     *
     * @return Swift_SmtpTransport
     */
    public static function newInstance($host = 'localhost', $port = 25, $security = null)
    {
        return new self($host, $port, $security);
    }
}
