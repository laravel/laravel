<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Reflection;

/**
 * A fake Reflector for namespaces.
 */
class ReflectionNamespace implements \Reflector
{
    private string $name;

    /**
     * Construct a ReflectionNamespace object.
     *
     * @param string $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Gets the constant name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * This can't (and shouldn't) do anything :).
     *
     * @throws \RuntimeException
     */
    public static function export($name)
    {
        throw new \RuntimeException('Not yet implemented because it\'s unclear what I should do here :)');
    }

    /**
     * To string.
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->getName();
    }
}
