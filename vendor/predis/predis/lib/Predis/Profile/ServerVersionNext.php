<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Profile;

/**
 * Server profile for the current unstable version of Redis.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class ServerVersionNext extends ServerVersion30
{
    /**
     * {@inheritdoc}
     */
    public function getVersion()
    {
        return '3.0';
    }

    /**
     * {@inheritdoc}
     */
    public function getSupportedCommands()
    {
        return array_merge(parent::getSupportedCommands(), array());
    }
}
