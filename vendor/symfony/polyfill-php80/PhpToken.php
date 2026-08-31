<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Polyfill\Php80;

/**
 * @author Fedonyuk Anton <info@ensostudio.ru>
 *
 * @internal
 */
class PhpToken implements \Stringable
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var string
     */
    public $text;

    /**
     * @var -1|positive-int
     */
    public $line;

    /**
     * @var int
     */
    public $pos;

    /**
     * @param -1|positive-int $line
     */
    public function __construct(int $id, string $text, int $line = -1, int $position = -1)
    {
        $this->id = $id;
        $this->text = $text;
        $this->line = $line;
        $this->pos = $position;
    }

    public function getTokenName(): ?string
    {
        if ('UNKNOWN' === $name = token_name($this->id)) {
            $name = \strlen($this->text) > 1 || \ord($this->text) < 32 ? null : $this->text;
        }

        return $name;
    }

    /**
     * @param int|string|array $kind
     */
    public function is($kind): bool
    {
        foreach ((array) $kind as $value) {
            if (\in_array($value, [$this->id, $this->text], true)) {
                return true;
            }
        }

        return false;
    }

    public function isIgnorable(): bool
    {
        return \in_array($this->id, [\T_WHITESPACE, \T_COMMENT, \T_DOC_COMMENT, \T_OPEN_TAG], true);
    }

    public function __toString(): string
    {
        return (string) $this->text;
    }

    /**
     * @return list<static>
     */
    public static function tokenize(string $code, int $flags = 0): array
    {
        $line = 1;
        $position = 0;
        $tokens = token_get_all($code, $flags);
        foreach ($tokens as $index => $token) {
            if (\is_string($token)) {
                $id = \ord($token);
                $text = $token;
            } else {
                [$id, $text, $line] = $token;
            }
            $tokens[$index] = new static($id, $text, $line, $position);
            $position += \strlen($text);
        }

        return $tokens;
    }
}
