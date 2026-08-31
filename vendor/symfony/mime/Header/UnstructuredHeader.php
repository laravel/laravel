<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Mime\Header;

/**
 * A Simple MIME Header.
 *
 * @author Chris Corbyn
 */
class UnstructuredHeader extends AbstractHeader
{
    private string $value;

    public function __construct(string $name, string $value)
    {
        parent::__construct($name);

        $this->setValue($value);
    }

    /**
     * @param string $body
     */
    public function setBody(mixed $body): void
    {
        $this->setValue($body);
    }

    public function getBody(): string
    {
        return $this->getValue();
    }

    /**
     * Get the (unencoded) value of this header.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Set the (unencoded) value of this header.
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * Get the value of this header prepared for rendering.
     */
    public function getBodyAsString(): string
    {
        return $this->encodeWords($this, $this->value);
    }
}
