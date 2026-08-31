<?php

namespace Illuminate\Filesystem;

use Illuminate\Contracts\Filesystem\LockTimeoutException;

class LockableFile
{
    /**
     * The file resource.
     *
     * @var resource
     */
    protected $handle;

    /**
     * The file path.
     *
     * @var string
     */
    protected $path;

    /**
     * Indicates if the file is locked.
     *
     * @var bool
     */
    protected $isLocked = false;

    /**
     * Create a new File instance.
     *
     * @param  string  $path
     * @param  string  $mode
     */
    public function __construct($path, $mode)
    {
        $this->path = $path;

        $this->ensureDirectoryExists($path);
        $this->createResource($path, $mode);
    }

    /**
     * Create the file's directory if necessary.
     *
     * @param  string  $path
     * @return void
     */
    protected function ensureDirectoryExists($path)
    {
        if (! file_exists(dirname($path))) {
            @mkdir(dirname($path), 0777, true);
        }
    }

    /**
     * Create the file resource.
     *
     * @param  string  $path
     * @param  string  $mode
     * @return void
     *
     * @throws \Exception
     */
    protected function createResource($path, $mode)
    {
        $this->handle = fopen($path, $mode);
    }

    /**
     * Read the file contents.
     *
     * @param  int|null  $length
     * @return string
     */
    public function read($length = null)
    {
        clearstatcache(true, $this->path);

        return fread($this->handle, $length ?? ($this->size() ?: 1));
    }

    /**
     * Get the file size.
     *
     * @return int
     */
    public function size()
    {
        return filesize($this->path);
    }

    /**
     * Write to the file.
     *
     * @param  string  $contents
     * @return $this
     */
    public function write($contents)
    {
        fwrite($this->handle, $contents);

        fflush($this->handle);

        return $this;
    }

    /**
     * Truncate the file.
     *
     * @return $this
     */
    public function truncate()
    {
        rewind($this->handle);

        ftruncate($this->handle, 0);

        return $this;
    }

    /**
     * Get a shared lock on the file.
     *
     * @param  bool  $block
     * @return $this
     *
     * @throws \Illuminate\Contracts\Filesystem\LockTimeoutException
     */
    public function getSharedLock($block = false)
    {
        if (! flock($this->handle, LOCK_SH | ($block ? 0 : LOCK_NB))) {
            throw new LockTimeoutException("Unable to acquire file lock at path [{$this->path}].");
        }

        $this->isLocked = true;

        return $this;
    }

    /**
     * Get an exclusive lock on the file.
     *
     * @param  bool  $block
     * @return $this
     *
     * @throws \Illuminate\Contracts\Filesystem\LockTimeoutException
     */
    public function getExclusiveLock($block = false)
    {
        if (! flock($this->handle, LOCK_EX | ($block ? 0 : LOCK_NB))) {
            throw new LockTimeoutException("Unable to acquire file lock at path [{$this->path}].");
        }

        $this->isLocked = true;

        return $this;
    }

    /**
     * Release the lock on the file.
     *
     * @return $this
     */
    public function releaseLock()
    {
        flock($this->handle, LOCK_UN);

        $this->isLocked = false;

        return $this;
    }

    /**
     * Close the file.
     *
     * @return bool
     */
    public function close()
    {
        if ($this->isLocked) {
            $this->releaseLock();
        }

        return fclose($this->handle);
    }
}
