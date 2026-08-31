<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Proxy;

use Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;

/**
 * @author Drak <drak@zikula.org>
 */
class SessionHandlerProxy extends AbstractProxy implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    public function __construct(
        protected \SessionHandlerInterface $handler,
    ) {
        $this->wrapper = $handler instanceof \SessionHandler;
        $this->saveHandlerName = $this->wrapper || ($handler instanceof StrictSessionHandler && $handler->isWrapper()) ? \ini_get('session.save_handler') : 'user';
    }

    public function getHandler(): \SessionHandlerInterface
    {
        return $this->handler;
    }

    // \SessionHandlerInterface

    public function open(string $savePath, string $sessionName): bool
    {
        return $this->handler->open($savePath, $sessionName);
    }

    public function close(): bool
    {
        return $this->handler->close();
    }

    public function read(#[\SensitiveParameter] string $sessionId): string|false
    {
        return $this->handler->read($sessionId);
    }

    public function write(#[\SensitiveParameter] string $sessionId, string $data): bool
    {
        return $this->handler->write($sessionId, $data);
    }

    public function destroy(#[\SensitiveParameter] string $sessionId): bool
    {
        return $this->handler->destroy($sessionId);
    }

    public function gc(int $maxlifetime): int|false
    {
        return $this->handler->gc($maxlifetime);
    }

    public function validateId(#[\SensitiveParameter] string $sessionId): bool
    {
        return !$this->handler instanceof \SessionUpdateTimestampHandlerInterface || $this->handler->validateId($sessionId);
    }

    public function updateTimestamp(#[\SensitiveParameter] string $sessionId, string $data): bool
    {
        return $this->handler instanceof \SessionUpdateTimestampHandlerInterface ? $this->handler->updateTimestamp($sessionId, $data) : $this->write($sessionId, $data);
    }
}
