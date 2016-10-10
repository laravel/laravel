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
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NativeSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\NativeProxy;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\AbstractProxy;
use Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy;

/**
 * This provides a base class for session attribute storage.
 *
 * @author Drak <drak@zikula.org>
 */
class NativeSessionStorage implements SessionStorageInterface
{
    /**
     * Array of SessionBagInterface
     *
     * @var SessionBagInterface[]
     */
    protected $bags;

    /**
     * @var bool
     */
    protected $started = false;

    /**
     * @var bool
     */
    protected $closed = false;

    /**
     * @var AbstractProxy
     */
    protected $saveHandler;

    /**
     * @var MetadataBag
     */
    protected $metadataBag;

    /**
     * Constructor.
     *
     * Depending on how you want the storage driver to behave you probably
     * want to override this constructor entirely.
     *
     * List of options for $options array with their defaults.
     * @see http://php.net/session.configuration for options
     * but we omit 'session.' from the beginning of the keys for convenience.
     *
     * ("auto_start", is not supported as it tells PHP to start a session before
     * PHP starts to execute user-land code. Setting during runtime has no effect).
     *
     * cache_limiter, "nocache" (use "0" to prevent headers from being sent entirely).
     * cookie_domain, ""
     * cookie_httponly, ""
     * cookie_lifetime, "0"
     * cookie_path, "/"
     * cookie_secure, ""
     * entropy_file, ""
     * entropy_length, "0"
     * gc_divisor, "100"
     * gc_maxlifetime, "1440"
     * gc_probability, "1"
     * hash_bits_per_character, "4"
     * hash_function, "0"
     * name, "PHPSESSID"
     * referer_check, ""
     * serialize_handler, "php"
     * use_cookies, "1"
     * use_only_cookies, "1"
     * use_trans_sid, "0"
     * upload_progress.enabled, "1"
     * upload_progress.cleanup, "1"
     * upload_progress.prefix, "upload_progress_"
     * upload_progress.name, "PHP_SESSION_UPLOAD_PROGRESS"
     * upload_progress.freq, "1%"
     * upload_progress.min-freq, "1"
     * url_rewriter.tags, "a=href,area=href,frame=src,form=,fieldset="
     *
     * @param array                                                            $options Session configuration options.
     * @param AbstractProxy|NativeSessionHandler|\SessionHandlerInterface|null $handler
     * @param MetadataBag                                                      $metaBag MetadataBag.
     */
    public function __construct(array $options = array(), $handler = null, MetadataBag $metaBag = null)
    {
        session_cache_limiter(''); // disable by default because it's managed by HeaderBag (if used)
        ini_set('session.use_cookies', 1);

        if (version_compare(phpversion(), '5.4.0', '>=')) {
            session_register_shutdown();
        } else {
            register_shutdown_function('session_write_close');
        }

        $this->setMetadataBag($metaBag);
        $this->setOptions($options);
        $this->setSaveHandler($handler);
    }

    /**
     * Gets the save handler instance.
     *
     * @return AbstractProxy
     */
    public function getSaveHandler()
    {
        return $this->saveHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function start()
    {
        if ($this->started && !$this->closed) {
            return true;
        }

        if (version_compare(phpversion(), '5.4.0', '>=') && \PHP_SESSION_ACTIVE === session_status()) {
            throw new \RuntimeException('Failed to start the session: already started by PHP.');
        }

        if (version_compare(phpversion(), '5.4.0', '<') && isset($_SESSION) && session_id()) {
            // not 100% fool-proof, but is the most reliable way to determine if a session is active in PHP 5.3
            throw new \RuntimeException('Failed to start the session: already started by PHP ($_SESSION is set).');
        }

        if (ini_get('session.use_cookies') && headers_sent($file, $line)) {
            throw new \RuntimeException(sprintf('Failed to start the session because headers have already been sent by "%s" at line %d.', $file, $line));
        }

        // ok to try and start the session
        if (!session_start()) {
            throw new \RuntimeException('Failed to start the session');
        }

        $this->loadSession();
        if (!$this->saveHandler->isWrapper() && !$this->saveHandler->isSessionHandlerInterface()) {
            // This condition matches only PHP 5.3 with internal save handlers
            $this->saveHandler->setActive(true);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        if (!$this->started && !$this->closed) {
            return ''; // returning empty is consistent with session_id() behaviour
        }

        return $this->saveHandler->getId();
    }

    /**
     * {@inheritdoc}
     */
    public function setId($id)
    {
        $this->saveHandler->setId($id);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->saveHandler->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->saveHandler->setName($name);
    }

    /**
     * {@inheritdoc}
     */
    public function regenerate($destroy = false, $lifetime = null)
    {
        if (null !== $lifetime) {
            ini_set('session.cookie_lifetime', $lifetime);
        }

        if ($destroy) {
            $this->metadataBag->stampNew();
        }

        $ret = session_regenerate_id($destroy);

        // workaround for https://bugs.php.net/bug.php?id=61470 as suggested by David Grudl
        if ('files' === $this->getSaveHandler()->getSaveHandlerName()) {
            session_write_close();
            if (isset($_SESSION)) {
                $backup = $_SESSION;
                session_start();
                $_SESSION = $backup;
            } else {
                session_start();
            }

            $this->loadSession();
        }

        return $ret;
    }

    /**
     * {@inheritdoc}
     */
    public function save()
    {
        session_write_close();

        if (!$this->saveHandler->isWrapper() && !$this->saveHandler->isSessionHandlerInterface()) {
            // This condition matches only PHP 5.3 with internal save handlers
            $this->saveHandler->setActive(false);
        }

        $this->closed = true;
        $this->started = false;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        // clear out the bags
        foreach ($this->bags as $bag) {
            $bag->clear();
        }

        // clear out the session
        $_SESSION = array();

        // reconnect the bags to the session
        $this->loadSession();
    }

    /**
     * {@inheritdoc}
     */
    public function registerBag(SessionBagInterface $bag)
    {
        $this->bags[$bag->getName()] = $bag;
    }

    /**
     * {@inheritdoc}
     */
    public function getBag($name)
    {
        if (!isset($this->bags[$name])) {
            throw new \InvalidArgumentException(sprintf('The SessionBagInterface %s is not registered.', $name));
        }

        if ($this->saveHandler->isActive() && !$this->started) {
            $this->loadSession();
        } elseif (!$this->started) {
            $this->start();
        }

        return $this->bags[$name];
    }

    /**
     * Sets the MetadataBag.
     *
     * @param MetadataBag $metaBag
     */
    public function setMetadataBag(MetadataBag $metaBag = null)
    {
        if (null === $metaBag) {
            $metaBag = new MetadataBag();
        }

        $this->metadataBag = $metaBag;
    }

    /**
     * Gets the MetadataBag.
     *
     * @return MetadataBag
     */
    public function getMetadataBag()
    {
        return $this->metadataBag;
    }

    /**
     * {@inheritdoc}
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Sets session.* ini variables.
     *
     * For convenience we omit 'session.' from the beginning of the keys.
     * Explicitly ignores other ini keys.
     *
     * @param array $options Session ini directives array(key => value).
     *
     * @see http://php.net/session.configuration
     */
    public function setOptions(array $options)
    {
        $validOptions = array_flip(array(
            'cache_limiter', 'cookie_domain', 'cookie_httponly',
            'cookie_lifetime', 'cookie_path', 'cookie_secure',
            'entropy_file', 'entropy_length', 'gc_divisor',
            'gc_maxlifetime', 'gc_probability', 'hash_bits_per_character',
            'hash_function', 'name', 'referer_check',
            'serialize_handler', 'use_cookies',
            'use_only_cookies', 'use_trans_sid', 'upload_progress.enabled',
            'upload_progress.cleanup', 'upload_progress.prefix', 'upload_progress.name',
            'upload_progress.freq', 'upload_progress.min-freq', 'url_rewriter.tags',
        ));

        foreach ($options as $key => $value) {
            if (isset($validOptions[$key])) {
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
     *     ini_set('session.save_path', /tmp');
     *
     * or pass in a NativeSessionHandler instance which configures session.save_handler in the
     * constructor, for a template see NativeFileSessionHandler or use handlers in
     * composer package drak/native-session
     *
     * @see http://php.net/session-set-save-handler
     * @see http://php.net/sessionhandlerinterface
     * @see http://php.net/sessionhandler
     * @see http://github.com/drak/NativeSession
     *
     * @param AbstractProxy|NativeSessionHandler|\SessionHandlerInterface|null $saveHandler
     *
     * @throws \InvalidArgumentException
     */
    public function setSaveHandler($saveHandler = null)
    {
        if (!$saveHandler instanceof AbstractProxy &&
            !$saveHandler instanceof NativeSessionHandler &&
            !$saveHandler instanceof \SessionHandlerInterface &&
            null !== $saveHandler) {
            throw new \InvalidArgumentException('Must be instance of AbstractProxy or NativeSessionHandler; implement \SessionHandlerInterface; or be null.');
        }

        // Wrap $saveHandler in proxy and prevent double wrapping of proxy
        if (!$saveHandler instanceof AbstractProxy && $saveHandler instanceof \SessionHandlerInterface) {
            $saveHandler = new SessionHandlerProxy($saveHandler);
        } elseif (!$saveHandler instanceof AbstractProxy) {
            $saveHandler = version_compare(phpversion(), '5.4.0', '>=') ?
                new SessionHandlerProxy(new \SessionHandler()) : new NativeProxy();
        }
        $this->saveHandler = $saveHandler;

        if ($this->saveHandler instanceof \SessionHandlerInterface) {
            if (version_compare(phpversion(), '5.4.0', '>=')) {
                session_set_save_handler($this->saveHandler, false);
            } else {
                session_set_save_handler(
                    array($this->saveHandler, 'open'),
                    array($this->saveHandler, 'close'),
                    array($this->saveHandler, 'read'),
                    array($this->saveHandler, 'write'),
                    array($this->saveHandler, 'destroy'),
                    array($this->saveHandler, 'gc')
                );
            }
        }
    }

    /**
     * Load the session with attributes.
     *
     * After starting the session, PHP retrieves the session from whatever handlers
     * are set to (either PHP's internal, or a custom save handler set with session_set_save_handler()).
     * PHP takes the return value from the read() handler, unserializes it
     * and populates $_SESSION with the result automatically.
     *
     * @param array|null $session
     */
    protected function loadSession(array &$session = null)
    {
        if (null === $session) {
            $session = &$_SESSION;
        }

        $bags = array_merge($this->bags, array($this->metadataBag));

        foreach ($bags as $bag) {
            $key = $bag->getStorageKey();
            $session[$key] = isset($session[$key]) ? $session[$key] : array();
            $bag->initialize($session[$key]);
        }

        $this->started = true;
        $this->closed = false;
    }
}
