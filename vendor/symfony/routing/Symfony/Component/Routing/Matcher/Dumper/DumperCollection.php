<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Routing\Matcher\Dumper;

/**
 * Collection of routes.
 *
 * @author Arnaud Le Blanc <arnaud.lb@gmail.com>
 */
class DumperCollection implements \IteratorAggregate
{
    /**
     * @var DumperCollection|null
     */
    private $parent;

    /**
     * @var (DumperCollection|DumperRoute)[]
     */
    private $children = array();

    /**
     * @var array
     */
    private $attributes = array();

    /**
     * Returns the children routes and collections.
     *
     * @return (DumperCollection|DumperRoute)[] Array of DumperCollection|DumperRoute
     */
    public function all()
    {
        return $this->children;
    }

    /**
     * Adds a route or collection
     *
     * @param DumperRoute|DumperCollection The route or collection
     */
    public function add($child)
    {
        if ($child instanceof DumperCollection) {
            $child->setParent($this);
        }
        $this->children[] = $child;
    }

    /**
     * Sets children.
     *
     * @param array $children The children
     */
    public function setAll(array $children)
    {
        foreach ($children as $child) {
            if ($child instanceof DumperCollection) {
                $child->setParent($this);
            }
        }
        $this->children = $children;
    }

    /**
     * Returns an iterator over the children.
     *
     * @return \Iterator The iterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->children);
    }

    /**
     * Returns the root of the collection.
     *
     * @return DumperCollection The root collection
     */
    public function getRoot()
    {
        return (null !== $this->parent) ? $this->parent->getRoot() : $this;
    }

    /**
     * Returns the parent collection.
     *
     * @return DumperCollection|null The parent collection or null if the collection has no parent
     */
    protected function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the parent collection.
     *
     * @param DumperCollection $parent The parent collection
     */
    protected function setParent(DumperCollection $parent)
    {
        $this->parent = $parent;
    }

    /**
     * Returns true if the attribute is defined.
     *
     * @param string $name The attribute name
     *
     * @return bool    true if the attribute is defined, false otherwise
     */
    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    /**
     * Returns an attribute by name.
     *
     * @param string $name    The attribute name
     * @param mixed  $default Default value is the attribute doesn't exist
     *
     * @return mixed The attribute value
     */
    public function getAttribute($name, $default = null)
    {
        return $this->hasAttribute($name) ? $this->attributes[$name] : $default;
    }

    /**
     * Sets an attribute by name.
     *
     * @param string $name  The attribute name
     * @param mixed  $value The attribute value
     */
    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $value;
    }

    /**
     * Sets multiple attributes.
     *
     * @param array $attributes The attributes
     */
    public function setAttributes($attributes)
    {
        $this->attributes = $attributes;
    }
}
