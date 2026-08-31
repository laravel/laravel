<?php

namespace Illuminate\Session;

use SessionHandlerInterface;

class NullSessionHandler implements SessionHandlerInterface
{
    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     */
    public function read($sessionId): string
    {
        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function write($sessionId, $data): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function destroy($sessionId): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function gc($lifetime): int
    {
        return 0;
    }
}
