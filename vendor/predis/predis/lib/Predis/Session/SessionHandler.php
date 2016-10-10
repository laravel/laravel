<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Session;

use SessionHandlerInterface;
use Predis\ClientInterface;

/**
 * Session handler class that relies on Predis\Client to store PHP's sessions
 * data into one or multiple Redis servers.
 *
 * This class is mostly intended for PHP 5.4 but it can be used under PHP 5.3 provided
 * that a polyfill for `SessionHandlerInterface` is defined by either you or an external
 * package such as `symfony/http-foundation`.
 *
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class SessionHandler implements SessionHandlerInterface
{
    protected $client;
    protected $ttl;

    /**
     * @param ClientInterface $client  Fully initialized client instance.
     * @param array           $options Session handler options.
     */
    public function __construct(ClientInterface $client, Array $options = array())
    {
        $this->client = $client;
        $this->ttl = (int) (isset($options['gc_maxlifetime']) ? $options['gc_maxlifetime'] : ini_get('session.gc_maxlifetime'));
    }

    /**
     * Registers the handler instance as the current session handler.
     */
    public function register()
    {
        if (version_compare(PHP_VERSION, '5.4.0') >= 0) {
            session_set_save_handler($this, true);
        } else {
            session_set_save_handler(
                array($this, 'open'),
                array($this, 'close'),
                array($this, 'read'),
                array($this, 'write'),
                array($this, 'destroy'),
                array($this, 'gc')
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function open($save_path, $session_id)
    {
        // NOOP
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        // NOOP
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function gc($maxlifetime)
    {
        // NOOP
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function read($session_id)
    {
        if ($data = $this->client->get($session_id)) {
            return $data;
        }

        return '';
    }
    /**
     * {@inheritdoc}
     */
    public function write($session_id, $session_data)
    {
        $this->client->setex($session_id, $this->ttl, $session_data);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function destroy($session_id)
    {
        $this->client->del($session_id);

        return true;
    }

    /**
     * Returns the underlying client instance.
     *
     * @return ClientInterface
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Returns the session max lifetime value.
     *
     * @return int
     */
    public function getMaxLifeTime()
    {
        return $this->ttl;
    }
}
