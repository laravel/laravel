<?php

namespace GuzzleHttp\Cookie;

/**
 * Persists cookies in the client session
 */
class SessionCookieJar extends CookieJar
{
    /**
     * @var string session key
     */
    private $sessionKey;

    /**
     * @var bool Control whether to persist session cookies or not.
     */
    private $storeSessionCookies;

    /**
     * Create a new SessionCookieJar object
     *
     * @param string $sessionKey          Session key name to store the cookie
     *                                    data in session
     * @param bool   $storeSessionCookies Set to true to store session cookies
     *                                    in the cookie jar.
     */
    public function __construct(string $sessionKey, bool $storeSessionCookies = false)
    {
        parent::__construct();
        $this->sessionKey = $sessionKey;
        $this->storeSessionCookies = $storeSessionCookies;
        $this->load();
    }

    /**
     * Saves cookies to session when shutting down
     */
    public function __destruct()
    {
        $this->save();
    }

    /**
     * Save cookies to the client session
     */
    public function save(): void
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

        $json = \json_encode($json);
        if (false === $json) {
            throw new \RuntimeException('Unable to encode cookie data');
        }

        $_SESSION[$this->sessionKey] = $json;
    }

    /**
     * Load the contents of the client session into the data array
     */
    protected function load(): void
    {
        if (!isset($_SESSION[$this->sessionKey])) {
            return;
        }

        $json = $_SESSION[$this->sessionKey];
        if (!\is_string($json)) {
            throw new \RuntimeException('Invalid cookie data');
        }

        $data = \json_decode($json, true);
        if (\is_array($data)) {
            $cookies = [];
            foreach ($data as $cookie) {
                if (!\is_array($cookie) || !\array_key_exists('HostOnly', $cookie) || !\is_bool($cookie['HostOnly'])) {
                    throw new \RuntimeException('Invalid cookie data');
                }

                $cookies[] = new SetCookie($cookie);
            }

            foreach ($cookies as $cookie) {
                $this->setCookie($cookie);
            }
        } elseif (\is_scalar($data) && \strlen((string) $data)) {
            throw new \RuntimeException('Invalid cookie data');
        }
    }
}
