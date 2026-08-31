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
 * Adds basic `SessionUpdateTimestampHandlerInterface` behaviors to another `SessionHandlerInterface`.
 *
 * @author Nicolas Grekas <p@tchwork.com>
 */
class StrictSessionHandler extends AbstractSessionHandler
{
    private bool $doDestroy;

    public function __construct(
        private \SessionHandlerInterface $handler,
    ) {
        if ($handler instanceof \SessionUpdateTimestampHandlerInterface) {
            throw new \LogicException(\sprintf('"%s" is already an instance of "SessionUpdateTimestampHandlerInterface", you cannot wrap it with "%s".', get_debug_type($handler), self::class));
        }
    }

    /**
     * Returns true if this handler wraps an internal PHP session save handler using \SessionHandler.
     *
     * @internal
     */
    public function isWrapper(): bool
    {
        return $this->handler instanceof \SessionHandler;
    }

    public function open(string $savePath, string $sessionName): bool
    {
        parent::open($savePath, $sessionName);

        return $this->handler->open($savePath, $sessionName);
    }

    protected function doRead(#[\SensitiveParameter] string $sessionId): string
    {
        return $this->handler->read($sessionId);
    }

    public function updateTimestamp(#[\SensitiveParameter] string $sessionId, string $data): bool
    {
        return $this->write($sessionId, $data);
    }

    protected function doWrite(#[\SensitiveParameter] string $sessionId, string $data): bool
    {
        return $this->handler->write($sessionId, $data);
    }

    public function destroy(#[\SensitiveParameter] string $sessionId): bool
    {
        $this->doDestroy = true;
        $destroyed = parent::destroy($sessionId);

        return $this->doDestroy ? $this->doDestroy($sessionId) : $destroyed;
    }

    protected function doDestroy(#[\SensitiveParameter] string $sessionId): bool
    {
        $this->doDestroy = false;

        return $this->handler->destroy($sessionId);
    }

    public function close(): bool
    {
        return $this->handler->close();
    }

    public function gc(int $maxlifetime): int|false
    {
        return $this->handler->gc($maxlifetime);
    }
}
