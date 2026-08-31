<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Input;

/**
 * Represents a key press.
 */
class Key
{
    public const TYPE_CHAR = 'char';
    public const TYPE_CONTROL = 'control';
    public const TYPE_ESCAPE = 'escape';
    public const TYPE_EOF = 'eof';
    public const TYPE_PASTE = 'paste';
    public const TYPE_MOUSE = 'mouse';

    private string $value;
    private string $type;

    public function __construct(string $value, string $type)
    {
        $this->value = $value;
        $this->type = $type;
    }

    /**
     * Get the raw key value.
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Get the key type.
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * Check if this is a regular character key.
     */
    public function isChar(): bool
    {
        return $this->type === self::TYPE_CHAR;
    }

    /**
     * Check if this is a control character.
     */
    public function isControl(): bool
    {
        return $this->type === self::TYPE_CONTROL;
    }

    /**
     * Check if this is an escape sequence.
     */
    public function isEscape(): bool
    {
        return $this->type === self::TYPE_ESCAPE;
    }

    /**
     * Check if this is an EOF signal.
     */
    public function isEof(): bool
    {
        return $this->type === self::TYPE_EOF;
    }

    /**
     * Check if this is pasted content.
     */
    public function isPaste(): bool
    {
        return $this->type === self::TYPE_PASTE;
    }

    /**
     * Check if this is a mouse event.
     */
    public function isMouse(): bool
    {
        return $this->type === self::TYPE_MOUSE;
    }

    /**
     * Create a normalized copy of a key.
     */
    public static function normalized(self $key): self
    {
        $value = $key->value;
        $type = $key->type;

        // Normalize CR to LF for deterministic line-accept handling.
        if ($type === self::TYPE_CHAR && $value === "\r") {
            $value = "\n";
        }

        // Normalize CSI-u key event type suffixes (e.g. \033[13;2:1u -> \033[13;2u).
        if ($type === self::TYPE_ESCAPE && \preg_match('/^(\033\[\d+(?:;\d+)*):\d+u$/', $value, $matches)) {
            $value = $matches[1].'u';
        }

        return new self($value, $type);
    }

    public function __toString(): string
    {
        if ($this->type === self::TYPE_CONTROL) {
            $ord = \ord($this->value);
            if ($ord < 32) {
                $char = \chr($ord + 96);

                return 'control:'.$char;
            } elseif ($ord === 127) {
                return 'control:?';
            }
        } elseif ($this->type === self::TYPE_ESCAPE) {
            return 'escape:'.\substr($this->value, 1);
        } elseif ($this->type === self::TYPE_CHAR) {
            return 'char:'.$this->value;
        }

        return $this->type.':'.$this->value;
    }
}
