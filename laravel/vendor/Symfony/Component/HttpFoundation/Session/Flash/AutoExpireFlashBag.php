<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Flash;

/**
 * AutoExpireFlashBag flash message container.
 *
 * @author Drak <drak@zikula.org>
 */
class AutoExpireFlashBag implements FlashBagInterface
{
    private $name = 'flashes';

    /**
     * Flash messages.
     *
     * @var array
     */
    private $flashes = array();

    /**
     * The storage key for flashes in the session
     *
     * @var string
     */
    private $storageKey;

    /**
     * Constructor.
     *
     * @param string $storageKey The key used to store flashes in the session.
     */
    public function __construct($storageKey = '_sf2_flashes')
    {
        $this->storageKey = $storageKey;
        $this->flashes = array('display' => array(), 'new' => array());
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(array &$flashes)
    {
        $this->flashes = &$flashes;

        // The logic: messages from the last request will be stored in new, so we move them to previous
        // This request we will show what is in 'display'.  What is placed into 'new' this time round will
        // be moved to display next time round.
        $this->flashes['display'] = array_key_exists('new', $this->flashes) ? $this->flashes['new'] : array();
        $this->flashes['new'] = array();
    }

    /**
     * {@inheritdoc}
     */
    public function add($type, $message)
    {
        $this->flashes['new'][$type][] = $message;
    }

    /**
     * {@inheritdoc}
     */
    public function peek($type, array $default = array())
    {
        return $this->has($type) ? $this->flashes['display'][$type] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function peekAll()
    {
        return array_key_exists('display', $this->flashes) ? (array) $this->flashes['display'] : array();
    }

    /**
     * {@inheritdoc}
     */
    public function get($type, array $default = array())
    {
        $return = $default;

        if (!$this->has($type)) {
            return $return;
        }

        if (isset($this->flashes['display'][$type])) {
            $return = $this->flashes['display'][$type];
            unset($this->flashes['display'][$type]);
        }

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function all()
    {
        $return = $this->flashes['display'];
        $this->flashes = array('new' => array(), 'display' => array());

        return $return;
    }

    /**
     * {@inheritdoc}
     */
    public function setAll(array $messages)
    {
        $this->flashes['new'] = $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function set($type, $messages)
    {
        $this->flashes['new'][$type] = (array) $messages;
    }

    /**
     * {@inheritdoc}
     */
    public function has($type)
    {
        return array_key_exists($type, $this->flashes['display']) && $this->flashes['display'][$type];
    }

    /**
     * {@inheritdoc}
     */
    public function keys()
    {
        return array_keys($this->flashes['display']);
    }

    /**
     * {@inheritdoc}
     */
    public function getStorageKey()
    {
        return $this->storageKey;
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        return $this->all();
    }
}
