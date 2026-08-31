<?php

namespace GuzzleHttp\Cookie;

use GuzzleHttp\Exception\InvalidArgumentException;

/**
 * Persists non-session cookies using a JSON formatted file
 */
class FileCookieJar extends CookieJar
{
    /**
     * @var string filename
     */
    private $filename;

    /**
     * @var bool Control whether to persist session cookies or not.
     */
    private $storeSessionCookies;

    /**
     * Create a new FileCookieJar object
     *
     * @param string $cookieFile          File to store the cookie data
     * @param bool   $storeSessionCookies Set to true to store session cookies
     *                                    in the cookie jar.
     *
     * @throws \RuntimeException if the file cannot be found or created
     */
    public function __construct(string $cookieFile, bool $storeSessionCookies = false)
    {
        parent::__construct();
        $this->filename = $cookieFile;
        $this->storeSessionCookies = $storeSessionCookies;

        if (\file_exists($cookieFile)) {
            $this->load($cookieFile);
        }
    }

    /**
     * Saves the file when shutting down
     */
    public function __destruct()
    {
        $this->save($this->filename);
    }

    /**
     * Saves the cookies to a file.
     *
     * @param string $filename File to save
     *
     * @throws \RuntimeException if the file cannot be found or created
     */
    public function save(string $filename): void
    {
        $json = [];
        /** @var SetCookie $cookie */
        foreach ($this as $cookie) {
            if (CookieJar::shouldPersist($cookie, $this->storeSessionCookies)) {
                $data = $cookie->toArray();
                $data['HostOnly'] = $cookie->getHostOnly();
                $json[] = $data;
            }
        }

        $jsonStr = \json_encode($json);
        if (\JSON_ERROR_NONE !== \json_last_error()) {
            throw new InvalidArgumentException('json_encode error: '.\json_last_error_msg());
        }

        /** @var non-empty-string $jsonStr */
        if (false === \file_put_contents($filename, $jsonStr, \LOCK_EX)) {
            throw new \RuntimeException("Unable to save file {$filename}");
        }
    }

    /**
     * Load cookies from a JSON formatted file.
     *
     * Old cookies are kept unless overwritten by newly loaded ones.
     *
     * @param string $filename Cookie file to load.
     *
     * @throws \RuntimeException if the file cannot be loaded.
     */
    public function load(string $filename): void
    {
        $json = \file_get_contents($filename);
        if (false === $json) {
            throw new \RuntimeException("Unable to load file {$filename}");
        }
        if ($json === '') {
            return;
        }

        $data = \json_decode($json, true);
        if (\JSON_ERROR_NONE !== \json_last_error()) {
            throw new InvalidArgumentException('json_decode error: '.\json_last_error_msg());
        }

        if (\is_array($data)) {
            $cookies = [];
            foreach ($data as $cookie) {
                if (!\is_array($cookie) || !\array_key_exists('HostOnly', $cookie) || !\is_bool($cookie['HostOnly'])) {
                    throw new \RuntimeException("Invalid cookie file: {$filename}");
                }

                $cookies[] = new SetCookie($cookie);
            }

            foreach ($cookies as $cookie) {
                $this->setCookie($cookie);
            }
        } elseif (\is_scalar($data) && !empty($data)) {
            throw new \RuntimeException("Invalid cookie file: {$filename}");
        }
    }
}
