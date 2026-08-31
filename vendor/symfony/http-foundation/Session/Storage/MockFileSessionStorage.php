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

/**
 * MockFileSessionStorage is used to mock sessions for
 * functional testing where you may need to persist session data
 * across separate PHP processes.
 *
 * No PHP session is actually started since a session can be initialized
 * and shutdown only once per PHP execution cycle and this class does
 * not pollute any session related globals, including session_*() functions
 * or session.* PHP ini directives.
 *
 * @author Drak <drak@zikula.org>
 */
class MockFileSessionStorage extends MockArraySessionStorage
{
    private string $savePath;

    /**
     * @param string|null $savePath Path of directory to save session files
     */
    public function __construct(?string $savePath = null, string $name = 'MOCKSESSID', ?MetadataBag $metaBag = null)
    {
        $savePath ??= sys_get_temp_dir();

        if (!is_dir($savePath) && !@mkdir($savePath, 0o777, true) && !is_dir($savePath)) {
            throw new \RuntimeException(\sprintf('Session Storage was not able to create directory "%s".', $savePath));
        }

        $this->savePath = $savePath;

        parent::__construct($name, $metaBag);
    }

    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (!$this->id) {
            $this->id = $this->generateId();
        }

        $this->read();

        $this->started = true;

        return true;
    }

    public function regenerate(bool $destroy = false, ?int $lifetime = null): bool
    {
        if (!$this->started) {
            $this->start();
        }

        if ($destroy) {
            $this->destroy();
        }

        return parent::regenerate($destroy, $lifetime);
    }

    public function save(): void
    {
        if (!$this->started) {
            throw new \RuntimeException('Trying to save a session that was not started yet or was already closed.');
        }

        $data = $this->data;

        foreach ($this->bags as $bag) {
            if (empty($data[$key = $bag->getStorageKey()])) {
                unset($data[$key]);
            }
        }
        if ([$key = $this->metadataBag->getStorageKey()] === array_keys($data)) {
            unset($data[$key]);
        }

        try {
            if ($data) {
                $path = $this->getFilePath();
                $tmp = $path.bin2hex(random_bytes(6));
                file_put_contents($tmp, serialize($data));
                rename($tmp, $path);
            } else {
                $this->destroy();
            }
        } finally {
            $this->data = $data;
        }

        // this is needed when the session object is reused across multiple requests
        // in functional tests.
        $this->started = false;
    }

    /**
     * Deletes a session from persistent storage.
     * Deliberately leaves session data in memory intact.
     */
    private function destroy(): void
    {
        set_error_handler(static function () {});
        try {
            unlink($this->getFilePath());
        } finally {
            restore_error_handler();
        }
    }

    /**
     * Calculate path to file.
     */
    private function getFilePath(): string
    {
        return $this->savePath.'/'.$this->id.'.mocksess';
    }

    /**
     * Reads session from storage and loads session.
     */
    private function read(): void
    {
        set_error_handler(static function () {});
        try {
            $data = file_get_contents($this->getFilePath());
        } finally {
            restore_error_handler();
        }

        $this->data = $data ? unserialize($data, ['allowed_classes' => true]) : [];

        $this->loadSession();
    }
}
