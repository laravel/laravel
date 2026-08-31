<?php

declare(strict_types=1);

/*
 * This file is part of the league/commonmark package.
 *
 * (c) Colin O'Dell <colinodell@gmail.com>
 *
 * Original code based on the CommonMark JS reference parser (https://bitly.com/commonmark-js)
 *  - (c) John MacFarlane
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace League\CommonMark\Util;

final class HtmlElement implements \Stringable
{
    /** @psalm-readonly */
    private string $tagName;

    /** @var array<string, string|bool> */
    private array $attributes = [];

    /** @var \Stringable|\Stringable[]|string */
    private $contents;

    /** @psalm-readonly */
    private bool $selfClosing;

    /**
     * @param string                                $tagName     Name of the HTML tag
     * @param array<string, string|string[]|bool>   $attributes  Array of attributes (values should be unescaped)
     * @param \Stringable|\Stringable[]|string|null $contents    Inner contents, pre-escaped if needed
     * @param bool                                  $selfClosing Whether the tag is self-closing
     */
    public function __construct(string $tagName, array $attributes = [], $contents = '', bool $selfClosing = false)
    {
        $this->tagName     = $tagName;
        $this->selfClosing = $selfClosing;

        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }

        $this->setContents($contents ?? '');
    }

    /** @psalm-immutable */
    public function getTagName(): string
    {
        return $this->tagName;
    }

    /**
     * @return array<string, string|bool>
     *
     * @psalm-immutable
     */
    public function getAllAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * @return string|bool|null
     *
     * @psalm-immutable
     */
    public function getAttribute(string $key)
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * @param string|string[]|bool $value
     */
    public function setAttribute(string $key, $value = true): self
    {
        if (\is_array($value)) {
            $this->attributes[$key] = \implode(' ', \array_unique($value));
        } else {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * @return \Stringable|\Stringable[]|string
     *
     * @psalm-immutable
     */
    public function getContents(bool $asString = true)
    {
        if (! $asString) {
            return $this->contents;
        }

        return $this->getContentsAsString();
    }

    /**
     * Sets the inner contents of the tag (must be pre-escaped if needed)
     *
     * @param \Stringable|\Stringable[]|string $contents
     *
     * @return $this
     */
    public function setContents($contents): self
    {
        $this->contents = $contents ?? '';

        return $this;
    }

    /** @psalm-immutable */
    public function __toString(): string
    {
        $result = '<' . $this->tagName;

        foreach ($this->attributes as $key => $value) {
            if ($value === true) {
                $result .= ' ' . $key;
            } elseif ($value === false) {
                continue;
            } else {
                $result .= ' ' . $key . '="' . Xml::escape($value) . '"';
            }
        }

        if ($this->contents !== '') {
            $result .= '>' . $this->getContentsAsString() . '</' . $this->tagName . '>';
        } elseif ($this->selfClosing && $this->tagName === 'input') {
            $result .= '>';
        } elseif ($this->selfClosing) {
            $result .= ' />';
        } else {
            $result .= '></' . $this->tagName . '>';
        }

        return $result;
    }

    /** @psalm-immutable */
    private function getContentsAsString(): string
    {
        if (\is_string($this->contents)) {
            return $this->contents;
        }

        if (\is_array($this->contents)) {
            return \implode('', $this->contents);
        }

        return (string) $this->contents;
    }
}
