<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpFoundation;

/**
 * Represents an Accept-* header item.
 *
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class AcceptHeaderItem
{
    private float $quality = 1.0;
    private int $index = 0;
    private array $attributes = [];

    public function __construct(
        private string $value,
        array $attributes = [],
    ) {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }
    }

    /**
     * Builds an AcceptHeaderInstance instance from a string.
     */
    public static function fromString(?string $itemValue): self
    {
        $parts = HeaderUtils::split($itemValue ?? '', ';=');

        $part = array_shift($parts);
        $attributes = HeaderUtils::combine($parts);

        return new self($part[0], $attributes);
    }

    /**
     * Returns header value's string representation.
     */
    public function __toString(): string
    {
        $string = $this->value.($this->quality < 1 ? ';q='.$this->quality : '');
        if (\count($this->attributes) > 0) {
            $string .= '; '.HeaderUtils::toString($this->attributes, ';');
        }

        return $string;
    }

    /**
     * Set the item value.
     *
     * @return $this
     */
    public function setValue(string $value): static
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Returns the item value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the item quality.
     *
     * @return $this
     */
    public function setQuality(float $quality): static
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * Returns the item quality.
     */
    public function getQuality(): float
    {
        return $this->quality;
    }

    /**
     * Set the item index.
     *
     * @return $this
     */
    public function setIndex(int $index): static
    {
        $this->index = $index;

        return $this;
    }

    /**
     * Returns the item index.
     */
    public function getIndex(): int
    {
        return $this->index;
    }

    /**
     * Tests if an attribute exists.
     */
    public function hasAttribute(string $name): bool
    {
        return isset($this->attributes[$name]);
    }

    /**
     * Returns an attribute by its name.
     */
    public function getAttribute(string $name, mixed $default = null): mixed
    {
        return $this->attributes[$name] ?? $default;
    }

    /**
     * Returns all attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set an attribute.
     *
     * @return $this
     */
    public function setAttribute(string $name, string $value): static
    {
        if ('q' === $name) {
            $this->quality = (float) $value;
        } else {
            $this->attributes[$name] = $value;
        }

        return $this;
    }
}
