<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage;

use Symfony\Component\HttpFoundation\Session\SessionBagInterface;

/**
 * Metadata container.
 *
 * Adds metadata to the session.
 *
 * @author Drak <drak@zikula.org>
 */
class MetadataBag implements SessionBagInterface
{
    public const CREATED = 'c';
    public const UPDATED = 'u';
    public const LIFETIME = 'l';

    protected array $meta = [self::CREATED => 0, self::UPDATED => 0, self::LIFETIME => 0];

    private string $name = '__metadata';
    private int $lastUsed;

    /**
     * @param string   $storageKey      The key used to store bag in the session
     * @param int      $updateThreshold The time to wait between two UPDATED updates
     * @param int|null $cookieLifetime  The configured cookie lifetime; null to read from php.ini
     */
    public function __construct(
        private string $storageKey = '_sf2_meta',
        private int $updateThreshold = 0,
        private ?int $cookieLifetime = null,
    ) {
    }

    public function initialize(array &$array): void
    {
        $this->meta = &$array;

        if (isset($array[self::CREATED])) {
            $this->lastUsed = $this->meta[self::UPDATED];

            $timeStamp = time();
            if ($timeStamp - $array[self::UPDATED] >= $this->updateThreshold) {
                $this->meta[self::UPDATED] = $timeStamp;
            }
        } else {
            $this->stampCreated();
        }
    }

    /**
     * Gets the lifetime that the session cookie was set with.
     */
    public function getLifetime(): int
    {
        return $this->meta[self::LIFETIME];
    }

    /**
     * Stamps a new session's metadata.
     *
     * @param int|null $lifetime Sets the cookie lifetime for the session cookie. A null value
     *                           will leave the system settings unchanged, 0 sets the cookie
     *                           to expire with browser session. Time is in seconds, and is
     *                           not a Unix timestamp.
     */
    public function stampNew(?int $lifetime = null): void
    {
        $this->stampCreated($lifetime);
    }

    public function getStorageKey(): string
    {
        return $this->storageKey;
    }

    /**
     * Gets the created timestamp metadata.
     *
     * @return int Unix timestamp
     */
    public function getCreated(): int
    {
        return $this->meta[self::CREATED];
    }

    /**
     * Gets the last used metadata.
     *
     * @return int Unix timestamp
     */
    public function getLastUsed(): int
    {
        return $this->lastUsed;
    }

    public function clear(): mixed
    {
        // nothing to do
        return null;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets name.
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    private function stampCreated(?int $lifetime = null): void
    {
        $timeStamp = time();
        $this->meta[self::CREATED] = $this->meta[self::UPDATED] = $this->lastUsed = $timeStamp;
        $this->meta[self::LIFETIME] = $lifetime ?? $this->cookieLifetime ?? (int) \ini_get('session.cookie_lifetime');
    }
}
