<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Security\Core\Authentication\RememberMe;

/**
 * This class is only used by PersistentTokenRememberMeServices internally.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class PersistentToken implements PersistentTokenInterface
{
    private $class;
    private $username;
    private $series;
    private $tokenValue;
    private $lastUsed;

    /**
     * Constructor
     *
     * @param string    $class
     * @param string    $username
     * @param string    $series
     * @param string    $tokenValue
     * @param \DateTime $lastUsed
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($class, $username, $series, $tokenValue, \DateTime $lastUsed)
    {
        if (empty($class)) {
            throw new \InvalidArgumentException('$class must not be empty.');
        }
        if (empty($username)) {
            throw new \InvalidArgumentException('$username must not be empty.');
        }
        if (empty($series)) {
            throw new \InvalidArgumentException('$series must not be empty.');
        }
        if (empty($tokenValue)) {
            throw new \InvalidArgumentException('$tokenValue must not be empty.');
        }

        $this->class = $class;
        $this->username = $username;
        $this->series = $series;
        $this->tokenValue = $tokenValue;
        $this->lastUsed = $lastUsed;
    }

    /**
     * {@inheritdoc}
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * {@inheritdoc}
     */
    public function getSeries()
    {
        return $this->series;
    }

    /**
     * {@inheritdoc}
     */
    public function getTokenValue()
    {
        return $this->tokenValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getLastUsed()
    {
        return $this->lastUsed;
    }
}
