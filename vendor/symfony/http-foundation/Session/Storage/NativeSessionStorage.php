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
use Symfony\Component\HttpFoundation\Session\Storage\Handler\StrictSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

// Help opcache.preload discover always-needed symbols
class_exists(MetadataBag::class);
class_exists(StrictSessionHandler::class);
class_exists(SessionHandlerProxy::class);

/**
 * This provides a base class for session attribute storage.
 *
 * @author Drak <drak@zikula.org>
 */
class NativeSessionStorage implements SessionStorageInterface
{
    /**
     * @var SessionBagInterface[]
     */
    protected array $bags = [];
    protected bool $started = false;
    protected bool $closed = false;
    protected AbstractProxy|\SessionHandlerInterface $saveHandler;
    protected MetadataBag $metadataBag;

    /**
     * Depending on how you want the storage driver to behave you probably
     * want to override this constructor entirely.
     *
     * List of options for $options array with their defaults.
     *
     * @see https://php.net/session.configuration for options
     * but we omit 'session.' from the beginning of the keys for convenience.
     *
     * ("auto_start", is not supported as it tells PHP to start a session before
     * PHP starts to execute user-land code. Setting during runtime has no effect).
     *
     * cache_limiter, "" (use "0" to prevent headers from being sent entirely).
     * cache_expire, "0"
     * cookie_domain, ""
     * cookie_httponly, ""
     * cookie_lifetime, "0"
     * cookie_path, "/"
     * cookie_secure, ""
     * cookie_samesite, null
     * gc_divisor, "100"
     * gc_maxlifetime, "1440"
     * gc_probability, "1"
     * lazy_write, "1"
     * name, "PHPSESSID"
     * referer_check, "" (deprecated since Symfony 7.2, to be removed in Symfony 8.0)
     * serialize_handler, "php"
     * use_strict_mode, "1"
     * use_cookies, "1"
     * use_only_cookies, "1" (deprecated since Symfony 7.2, to be removed in Symfony 8.0)
     * use_trans_sid, "0" (deprecated since Symfony 7.2, to be removed in Symfony 8.0)
     * sid_length, "32" (@deprecated since Symfony 7.2, to be removed in 8.0)
     * sid_bits_per_character, "5" (@deprecated since Symfony 7.2, to be removed in 8.0)
     * trans_sid_hosts, $_SERVER['HTTP_HOST'] (deprecated since Symfony 7.2, to be removed in Symfony 8.0)
     * trans_sid_tags, "a=href,area=href,frame=src,form=" (deprecated since Symfony 7.2, to be removed in Symfony 8.0)
     */
    public function __construct(array $options = [], AbstractProxy|\SessionHandlerInterface|null $handler = null, ?MetadataBag $metaBag = null)
    {
        if (!\extension_loaded('session')) {
            throw new \LogicException('PHP extension "session" is required.');
        }

        $options += [
            'cache_limiter' => '',
            'cache_expire' => 0,
            'use_cookies' => 1,
            'lazy_write' => 1,
            'use_strict_mode' => 1,
        ];

        session_register_shutdown();

        $this->setMetadataBag($metaBag);
        $this->setOptions($options);
        $this->setSaveHandler($handler);
    }

    /**
     * Gets the save handler instance.
     */
    public function getSaveHandler(): AbstractProxy|\SessionHandlerInterface
    {
        return $this->saveHandler;
    }

    public function start(): bool
    {
        if ($this->started) {
            return true;
        }

        if (\PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (filter_var(\ini_get('session.use_cookies'), \FILTER_VALIDATE_BOOL) && headers_sent($file, $line)) {
            throw new \RuntimeException(\sprintf('Failed to start the session because headers have already been sent by "%s" at line %d.', $file, $line));
        }

        $sessionId = $_COOKIE[session_name()] ?? null;
        /*
         * Explanation of the session ID regular expression: `/^[a-zA-Z0-9,-]{22,250}$/`.
         *
         * ---------- Part 1
         *
         * The part `[a-zA-Z0-9,-]` is related to the PHP ini directive `session.sid_bits_per_character` defined as 6.
         * See https://php.net/session.configuration#ini.session.sid-bits-per-character
         * Allowed values are integers such as:
         * - 4 for range `a-f0-9`
         * - 5 for range `a-v0-9` (@deprecated since Symfony 7.2, it will default to 4 and the option will be ignored in Symfony 8.0)
         * - 6 for range `a-zA-Z0-9,-` (@deprecated since Symfony 7.2, it will default to 4 and the option will be ignored in Symfony 8.0)
         *
         * ---------- Part 2
         *
         * The part `{22,250}` is related to the PHP ini directive `session.sid_length`.
         * See https://php.net/session.configuration#ini.session.sid-length
         * Allowed values are integers between 22 and 256, but we use 250 for the max.
         *
         * Where does the 250 come from?
         * - The length of Windows and Linux filenames is limited to 255 bytes. Then the max must not exceed 255.
         * - The session filename prefix is `sess_`, a 5 bytes string. Then the max must not exceed 255 - 5 = 250.
         *
         * This is @deprecated since Symfony 7.2, the sid length will default to 32 and the option will be ignored in Symfony 8.0.
         *
         * ---------- Conclusion
         *
         * The parts 1 and 2 prevent the warning below:
         * `PHP Warning: SessionHandler::read(): Session ID is too long or contains illegal characters. Only the A-Z, a-z, 0-9, "-", and "," characters are allowed.`
         *
         * The part 2 prevents the warning below:
         * `PHP Warning: SessionHandler::read(): open(filepath, O_RDWR) failed: No such file or directory (2).`
         */
        if ($sessionId && $this->saveHandler instanceof AbstractProxy && 'files' === $this->saveHandler->getSaveHandlerName() && !preg_match('/^[a-zA-Z0-9,-]{22,250}$/', $sessionId)) {
            // the session ID in the header is invalid, create a new one
            session_id(session_create_id());
        }

        // ok to try and start the session
        if (!session_start()) {
            throw new \RuntimeException('Failed to start the session.');
        }

        $this->loadSession();

        return true;
    }

    public function getId(): string
    {
        return $this->saveHandler->getId();
    }

    public function setId(string $id): void
    {
        $this->saveHandler->setId($id);
    }

    public function getName(): string
    {
        return $this->saveHandler->getName();
    }

    public function setName(string $name): void
    {
        $this->saveHandler->setName($name);
    }

    public function regenerate(bool $destroy = false, ?int $lifetime = null): bool
    {
        // Cannot regenerate the session ID for non-active sessions.
        if (\PHP_SESSION_ACTIVE !== session_status()) {
            return false;
        }

        if (headers_sent()) {
            return false;
        }

        if (null !== $lifetime && $lifetime != \ini_get('session.cookie_lifetime')) {
            $this->save();
            ini_set('session.cookie_lifetime', $lifetime);
            $this->start();
        }

        if ($destroy) {
            $this->metadataBag->stampNew();
        }

        return session_regenerate_id($destroy);
    }

    public function save(): void
    {
        // Store a copy so we can restore the bags in case the session was not left empty
        $session = $_SESSION;

        foreach ($this->bags as $bag) {
            if (empty($_SESSION[$key = $bag->getStorageKey()])) {
                unset($_SESSION[$key]);
            }
        }
        if ($_SESSION && [$key = $this->metadataBag->getStorageKey()] === array_keys($_SESSION)) {
            unset($_SESSION[$key]);
        }

        // Register error handler to add information about the current save handler
        $previousHandler = set_error_handler(function ($type, $msg, $file, $line) use (&$previousHandler) {
            if (\E_WARNING === $type && str_starts_with($msg, 'session_write_close():')) {
                $handler = $this->saveHandler instanceof SessionHandlerProxy ? $this->saveHandler->getHandler() : $this->saveHandler;
                $msg = \sprintf('session_write_close(): Failed to write session data with "%s" handler', $handler::class);
            }

            return $previousHandler ? $previousHandler($type, $msg, $file, $line) : false;
        });

        try {
            session_write_close();
        } finally {
            restore_error_handler();

            // Restore only if not empty
            if ($_SESSION) {
                $_SESSION = $session;
            }
        }

        $this->closed = true;
        $this->started = false;
    }

    public function clear(): void
    {
        // clear out the bags
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        // clear out the session
        $_SESSION = [];

        // reconnect the bags to the session
        $this->loadSession();
    }

    public function registerBag(SessionBagInterface $bag): void
    {
        if ($this->started) {
            throw new \LogicException('Cannot register a bag when the session is already started.');
        }

        $this->bags[$bag->getName()] = $bag;
    }

    public function getBag(string $name): SessionBagInterface
    {
        if (!isset($this->bags[$name])) {
            throw new \InvalidArgumentException(\sprintf('The SessionBagInterface "%s" is not registered.', $name));
        }

        if (!$this->started && $this->saveHandler->isActive()) {
            $this->loadSession();
        } elseif (!$this->started) {
            $this->start();
        }

        return $this->bags[$name];
    }

    public function setMetadataBag(?MetadataBag $metaBag): void
    {
        $this->metadataBag = $metaBag ?? new MetadataBag();
    }

    /**
     * Gets the MetadataBag.
     */
    public function getMetadataBag(): MetadataBag
    {
        return $this->metadataBag;
    }

    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * Sets session.* ini variables.
     *
     * For convenience we omit 'session.' from the beginning of the keys.
     * Explicitly ignores other ini keys.
     *
     * @param array $options Session ini directives [key => value]
     *
     * @see https://php.net/session.configuration
     */
    public function setOptions(array $options): void
    {
        if (headers_sent() || \PHP_SESSION_ACTIVE === session_status()) {
            return;
        }

        $validOptions = array_flip([
            'cache_expire', 'cache_limiter', 'cookie_domain', 'cookie_httponly',
            'cookie_lifetime', 'cookie_path', 'cookie_secure', 'cookie_samesite',
            'gc_divisor', 'gc_maxlifetime', 'gc_probability',
            'lazy_write', 'name', 'referer_check',
            'serialize_handler', 'use_strict_mode', 'use_cookies',
            'use_only_cookies', 'use_trans_sid',
            'sid_length', 'sid_bits_per_character', 'trans_sid_hosts', 'trans_sid_tags',
        ]);

        foreach ($options as $key => $value) {
            if (\in_array($key, ['referer_check', 'use_only_cookies', 'use_trans_sid', 'trans_sid_hosts', 'trans_sid_tags', 'sid_length', 'sid_bits_per_character'], true)) {
                trigger_deprecation('symfony/http-foundation', '7.2', 'NativeSessionStorage\'s "%s" option is deprecated and will be ignored in Symfony 8.0.', $key);
            }

            if (isset($validOptions[$key])) {
                if ('cookie_secure' === $key && 'auto' === $value) {
                    continue;
                }
                ini_set('session.'.$key, $value);
            }
        }
    }

    /**
     * Registers session save handler as a PHP session handler.
     *
     * To use internal PHP session save handlers, override this method using ini_set with
     * session.save_handler and session.save_path e.g.
     *
     *     ini_set('session.save_handler', 'files');
     *     ini_set('session.save_path', '/tmp');
     *
     * or pass in a \SessionHandler instance which configures session.save_handler in the
     * constructor, for a template see NativeFileSessionHandler.
     *
     * @see https://php.net/session-set-save-handler
     * @see https://php.net/sessionhandlerinterface
     * @see https://php.net/sessionhandler
     *
     * @throws \InvalidArgumentException
     */
    public function setSaveHandler(AbstractProxy|\SessionHandlerInterface|null $saveHandler): void
    {
        // Wrap $saveHandler in proxy and prevent double wrapping of proxy
        if (!$saveHandler instanceof AbstractProxy && $saveHandler instanceof \SessionHandlerInterface) {
            $saveHandler = new SessionHandlerProxy($saveHandler);
        } elseif (!$saveHandler instanceof AbstractProxy) {
            $saveHandler = new SessionHandlerProxy(new StrictSessionHandler(new \SessionHandler()));
        }
        $this->saveHandler = $saveHandler;

        if (headers_sent() || \PHP_SESSION_ACTIVE === session_status()) {
            return;
        }

        if ($this->saveHandler instanceof SessionHandlerProxy) {
            session_set_save_handler($this->saveHandler, false);
        }
    }

    /**
     * Load the session with attributes.
     *
     * After starting the session, PHP retrieves the session from whatever handlers
     * are set to (either PHP's internal, or a custom save handler set with session_set_save_handler()).
     * PHP takes the return value from the read() handler, unserializes it
     * and populates $_SESSION with the result automatically.
     */
    protected function loadSession(?array &$session = null): void
    {
        if (null === $session) {
            $session = &$_SESSION;
        }

        $bags = array_merge($this->bags, [$this->metadataBag]);

        foreach ($bags as $bag) {
            $key = $bag->getStorageKey();
            $session[$key] = isset($session[$key]) && \is_array($session[$key]) ? $session[$key] : [];
            $bag->initialize($session[$key]);
        }

        $this->started = true;
        $this->closed = false;
    }
}
