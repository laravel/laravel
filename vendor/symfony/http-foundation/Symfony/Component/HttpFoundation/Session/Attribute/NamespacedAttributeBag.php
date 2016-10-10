<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation\Session\Attribute;

/**
 * This class provides structured storage of session attributes using
 * a name spacing character in the key.
 *
 * @author Drak <drak@zikula.org>
 */
class NamespacedAttributeBag extends AttributeBag
{
    /**
     * Namespace character.
     *
     * @var string
     */
    private $namespaceCharacter;

    /**
     * Constructor.
     *
     * @param string $storageKey         Session storage key.
     * @param string $namespaceCharacter Namespace character to use in keys.
     */
    public function __construct($storageKey = '_sf2_attributes', $namespaceCharacter = '/')
    {
        $this->namespaceCharacter = $namespaceCharacter;
        parent::__construct($storageKey);
    }

    /**
     * {@inheritdoc}
     */
    public function has($name)
    {
        $attributes = $this->resolveAttributePath($name);
        $name = $this->resolveKey($name);

        if (null === $attributes) {
            return false;
        }

        return array_key_exists($name, $attributes);
    }

    /**
     * {@inheritdoc}
     */
    public function get($name, $default = null)
    {
        $attributes = $this->resolveAttributePath($name);
        $name = $this->resolveKey($name);

        if (null === $attributes) {
            return $default;
        }

        return array_key_exists($name, $attributes) ? $attributes[$name] : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function set($name, $value)
    {
        $attributes = & $this->resolveAttributePath($name, true);
        $name = $this->resolveKey($name);
        $attributes[$name] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function remove($name)
    {
        $retval = null;
        $attributes = & $this->resolveAttributePath($name);
        $name = $this->resolveKey($name);
        if (null !== $attributes && array_key_exists($name, $attributes)) {
            $retval = $attributes[$name];
            unset($attributes[$name]);
        }

        return $retval;
    }

    /**
     * Resolves a path in attributes property and returns it as a reference.
     *
     * This method allows structured namespacing of session attributes.
     *
     * @param string  $name         Key name
     * @param bool    $writeContext Write context, default false
     *
     * @return array
     */
    protected function &resolveAttributePath($name, $writeContext = false)
    {
        $array = & $this->attributes;
        $name = (strpos($name, $this->namespaceCharacter) === 0) ? substr($name, 1) : $name;

        // Check if there is anything to do, else return
        if (!$name) {
            return $array;
        }

        $parts = explode($this->namespaceCharacter, $name);
        if (count($parts) < 2) {
            if (!$writeContext) {
                return $array;
            }

            $array[$parts[0]] = array();

            return $array;
        }

        unset($parts[count($parts)-1]);

        foreach ($parts as $part) {
            if (null !== $array && !array_key_exists($part, $array)) {
                $array[$part] = $writeContext ? array() : null;
            }

            $array = & $array[$part];
        }

        return $array;
    }

    /**
     * Resolves the key from the name.
     *
     * This is the last part in a dot separated string.
     *
     * @param string $name
     *
     * @return string
     */
    protected function resolveKey($name)
    {
        if (false !== $pos = strrpos($name, $this->namespaceCharacter)) {
            $name = substr($name, $pos+1);
        }

        return $name;
    }
}
