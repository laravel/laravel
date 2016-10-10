<?php

/*
 * This file is part of SwiftMailer.
 * (c) 2009 Fabien Potencier <fabien.potencier@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Stores Messages in a queue.
 *
 * @author Fabien Potencier
 */
class Swift_SpoolTransport extends Swift_Transport_SpoolTransport
{
    /**
     * Create a new SpoolTransport.
     *
     * @param Swift_Spool $spool
     */
    public function __construct(Swift_Spool $spool)
    {
        $arguments = Swift_DependencyContainer::getInstance()
            ->createDependenciesFor('transport.spool');

        $arguments[] = $spool;

        call_user_func_array(
            array($this, 'Swift_Transport_SpoolTransport::__construct'),
            $arguments
        );
    }

    /**
     * Create a new SpoolTransport instance.
     *
     * @param Swift_Spool $spool
     *
     * @return Swift_SpoolTransport
     */
    public static function newInstance(Swift_Spool $spool)
    {
        return new self($spool);
    }
}
