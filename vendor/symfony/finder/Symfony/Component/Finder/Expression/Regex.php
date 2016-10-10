<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Finder\Expression;

/**
 * @author Jean-François Simon <contact@jfsimon.fr>
 */
class Regex implements ValueInterface
{
    const START_FLAG = '^';
    const END_FLAG   = '$';
    const BOUNDARY   = '~';
    const JOKER      = '.*';
    const ESCAPING   = '\\';

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var array
     */
    private $options;

    /**
     * @var bool
     */
    private $startFlag;

    /**
     * @var bool
     */
    private $endFlag;

    /**
     * @var bool
     */
    private $startJoker;

    /**
     * @var bool
     */
    private $endJoker;

    /**
     * @param string $expr
     *
     * @return Regex
     *
     * @throws \InvalidArgumentException
     */
    public static function create($expr)
    {
        if (preg_match('/^(.{3,}?)([imsxuADU]*)$/', $expr, $m)) {
            $start = substr($m[1], 0, 1);
            $end   = substr($m[1], -1);

            if (
                ($start === $end && !preg_match('/[*?[:alnum:] \\\\]/', $start))
                || ($start === '{' && $end === '}')
                || ($start === '(' && $end === ')')
            ) {
                return new self(substr($m[1], 1, -1), $m[2], $end);
            }
        }

        throw new \InvalidArgumentException('Given expression is not a regex.');
    }

    /**
     * @param string $pattern
     * @param string $options
     * @param string $delimiter
     */
    public function __construct($pattern, $options = '', $delimiter = null)
    {
        if (null !== $delimiter) {
            // removes delimiter escaping
            $pattern = str_replace('\\'.$delimiter, $delimiter, $pattern);
        }

        $this->parsePattern($pattern);
        $this->options = $options;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        return self::BOUNDARY
            .$this->renderPattern()
            .self::BOUNDARY
            .$this->options;
    }

    /**
     * {@inheritdoc}
     */
    public function renderPattern()
    {
        return ($this->startFlag ? self::START_FLAG : '')
            .($this->startJoker ? self::JOKER : '')
            .str_replace(self::BOUNDARY, '\\'.self::BOUNDARY, $this->pattern)
            .($this->endJoker ? self::JOKER : '')
            .($this->endFlag ? self::END_FLAG : '');
    }

    /**
     * {@inheritdoc}
     */
    public function isCaseSensitive()
    {
        return !$this->hasOption('i');
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return Expression::TYPE_REGEX;
    }

    /**
     * {@inheritdoc}
     */
    public function prepend($expr)
    {
        $this->pattern = $expr.$this->pattern;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function append($expr)
    {
        $this->pattern .= $expr;

        return $this;
    }

    /**
     * @param string $option
     *
     * @return bool
     */
    public function hasOption($option)
    {
        return false !== strpos($this->options, $option);
    }

    /**
     * @param string $option
     *
     * @return Regex
     */
    public function addOption($option)
    {
        if (!$this->hasOption($option)) {
            $this->options .= $option;
        }

        return $this;
    }

    /**
     * @param string $option
     *
     * @return Regex
     */
    public function removeOption($option)
    {
        $this->options = str_replace($option, '', $this->options);

        return $this;
    }

    /**
     * @param bool $startFlag
     *
     * @return Regex
     */
    public function setStartFlag($startFlag)
    {
        $this->startFlag = $startFlag;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasStartFlag()
    {
        return $this->startFlag;
    }

    /**
     * @param bool $endFlag
     *
     * @return Regex
     */
    public function setEndFlag($endFlag)
    {
        $this->endFlag = (bool) $endFlag;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasEndFlag()
    {
        return $this->endFlag;
    }

    /**
     * @param bool $startJoker
     *
     * @return Regex
     */
    public function setStartJoker($startJoker)
    {
        $this->startJoker = $startJoker;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasStartJoker()
    {
        return $this->startJoker;
    }

    /**
     * @param bool $endJoker
     *
     * @return Regex
     */
    public function setEndJoker($endJoker)
    {
        $this->endJoker = (bool) $endJoker;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasEndJoker()
    {
        return $this->endJoker;
    }

    /**
     * @param array $replacement
     *
     * @return Regex
     */
    public function replaceJokers($replacement)
    {
        $replace = function ($subject) use ($replacement) {
            $subject = $subject[0];
            $replace = 0 === substr_count($subject, '\\') % 2;

            return $replace ? str_replace('.', $replacement, $subject) : $subject;
        };

        $this->pattern = preg_replace_callback('~[\\\\]*\\.~', $replace, $this->pattern);

        return $this;
    }

    /**
     * @param string $pattern
     */
    private function parsePattern($pattern)
    {
        if ($this->startFlag = self::START_FLAG === substr($pattern, 0, 1)) {
            $pattern = substr($pattern, 1);
        }

        if ($this->startJoker = self::JOKER === substr($pattern, 0, 2)) {
            $pattern = substr($pattern, 2);
        }

        if ($this->endFlag = (self::END_FLAG === substr($pattern, -1) && self::ESCAPING !== substr($pattern, -2, -1))) {
            $pattern = substr($pattern, 0, -1);
        }

        if ($this->endJoker = (self::JOKER === substr($pattern, -2) && self::ESCAPING !== substr($pattern, -3, -2))) {
            $pattern = substr($pattern, 0, -2);
        }

        $this->pattern = $pattern;
    }
}
