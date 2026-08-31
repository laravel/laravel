<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Storage\Handler;

/**
 * Migrating session handler for migrating from one handler to another. It reads
 * from the current handler and writes both the current and new ones.
 *
 * It ignores errors from the new handler.
 *
 * @author Ross Motley <ross.motley@amara.com>
 * @author Oliver Radwell <oliver.radwell@amara.com>
 */
class MigratingSessionHandler implements \SessionHandlerInterface, \SessionUpdateTimestampHandlerInterface
{
    private \SessionHandlerInterface&\SessionUpdateTimestampHandlerInterface $currentHandler;
    private \SessionHandlerInterface&\SessionUpdateTimestampHandlerInterface $writeOnlyHandler;

    public function __construct(\SessionHandlerInterface $currentHandler, \SessionHandlerInterface $writeOnlyHandler)
    {
        if (!$currentHandler instanceof \SessionUpdateTimestampHandlerInterface) {
            $currentHandler = new StrictSessionHandler($currentHandler);
        }
        if (!$writeOnlyHandler instanceof \SessionUpdateTimestampHandlerInterface) {
            $writeOnlyHandler = new StrictSessionHandler($writeOnlyHandler);
        }

        $this->currentHandler = $currentHandler;
        $this->writeOnlyHandler = $writeOnlyHandler;
    }

    public function close(): bool
    {
        $result = $this->currentHandler->close();
        $this->writeOnlyHandler->close();

        return $result;
    }

    public function destroy(#[\SensitiveParameter] string $sessionId): bool
    {
        $result = $this->currentHandler->destroy($sessionId);
        $this->writeOnlyHandler->destroy($sessionId);

        return $result;
    }

    public function gc(int $maxlifetime): int|false
    {
        $result = $this->currentHandler->gc($maxlifetime);
        $this->writeOnlyHandler->gc($maxlifetime);

        return $result;
    }

    public function open(string $savePath, string $sessionName): bool
    {
        $result = $this->currentHandler->open($savePath, $sessionName);
        $this->writeOnlyHandler->open($savePath, $sessionName);

        return $result;
    }

    public function read(#[\SensitiveParameter] string $sessionId): string
    {
        // No reading from new handler until switch-over
        return $this->currentHandler->read($sessionId);
    }

    public function write(#[\SensitiveParameter] string $sessionId, string $sessionData): bool
    {
        $result = $this->currentHandler->write($sessionId, $sessionData);
        $this->writeOnlyHandler->write($sessionId, $sessionData);

        return $result;
    }

    public function validateId(#[\SensitiveParameter] string $sessionId): bool
    {
        // No reading from new handler until switch-over
        return $this->currentHandler->validateId($sessionId);
    }

    public function updateTimestamp(#[\SensitiveParameter] string $sessionId, string $sessionData): bool
    {
        $result = $this->currentHandler->updateTimestamp($sessionId, $sessionData);
        $this->writeOnlyHandler->updateTimestamp($sessionId, $sessionData);

        return $result;
    }
}
