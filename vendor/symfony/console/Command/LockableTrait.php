<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\Store\SemaphoreStore;

/**
 * Basic lock feature for commands.
 *
 * @author Geoffrey Brier <geoffrey.brier@gmail.com>
 */
trait LockableTrait
{
    private ?LockInterface $lock = null;

    private ?LockFactory $lockFactory = null;

    /**
     * Locks a command.
     */
    private function lock(?string $name = null, bool $blocking = false): bool
    {
        if (!class_exists(SemaphoreStore::class)) {
            throw new LogicException('To enable the locking feature you must install the symfony/lock component. Try running "composer require symfony/lock".');
        }

        if (null !== $this->lock) {
            throw new LogicException('A lock is already in place.');
        }

        if (null === $this->lockFactory) {
            if (SemaphoreStore::isSupported()) {
                $store = new SemaphoreStore();
            } else {
                $store = new FlockStore();
            }

            $this->lockFactory = new LockFactory($store);
        }

        if (!$name) {
            if ($this instanceof Command) {
                $name = $this->getName();
            } elseif ($attribute = (new \ReflectionClass($this::class))->getAttributes(AsCommand::class)) {
                $name = $attribute[0]->newInstance()->name;
            } else {
                throw new LogicException(\sprintf('Lock name missing: provide it via "%s()", #[AsCommand] attribute, or by extending Command class.', __METHOD__));
            }
        }

        $this->lock = $this->lockFactory->createLock($name);
        if (!$this->lock->acquire($blocking)) {
            $this->lock = null;

            return false;
        }

        return true;
    }

    /**
     * Releases the command lock if there is one.
     */
    private function release(): void
    {
        if ($this->lock) {
            $this->lock->release();
            $this->lock = null;
        }
    }
}
