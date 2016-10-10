<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\CssSelector\Parser\Tokenizer;

/**
 * CSS selector tokenizer patterns builder.
 *
 * This component is a port of the Python cssselector library,
 * which is copyright Ian Bicking, @see https://github.com/SimonSapin/cssselect.
 *
 * @author Jean-François Simon <jeanfrancois.simon@sensiolabs.com>
 */
class TokenizerPatterns
{
    /**
     * @var string
     */
    private $unicodeEscapePattern;

    /**
     * @var string
     */
    private $simpleEscapePattern;

    /**
     * @var string
     */
    private $newLineEscapePattern;

    /**
     * @var string
     */
    private $escapePattern;

    /**
     * @var string
     */
    private $stringEscapePattern;

    /**
     * @var string
     */
    private $nonAsciiPattern;

    /**
     * @var string
     */
    private $nmCharPattern;

    /**
     * @var string
     */
    private $nmStartPattern;

    /**
     * @var string
     */
    private $identifierPattern;

    /**
     * @var string
     */
    private $hashPattern;

    /**
     * @var string
     */
    private $numberPattern;

    /**
     * @var string
     */
    private $quotedStringPattern;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->unicodeEscapePattern = '\\\\([0-9a-f]{1,6})(?:\r\n|[ \n\r\t\f])?';
        $this->simpleEscapePattern = '\\\\(.)';
        $this->newLineEscapePattern = '\\\\(?:\n|\r\n|\r|\f)';
        $this->escapePattern = $this->unicodeEscapePattern.'|\\\\[^\n\r\f0-9a-f]';
        $this->stringEscapePattern = $this->newLineEscapePattern.'|'.$this->escapePattern;
        $this->nonAsciiPattern = '[^\x00-\x7F]';
        $this->nmCharPattern = '[_a-z0-9-]|'.$this->escapePattern.'|'.$this->nonAsciiPattern;
        $this->nmStartPattern = '[_a-z]|'.$this->escapePattern.'|'.$this->nonAsciiPattern;
        $this->identifierPattern = '(?:'.$this->nmStartPattern.')(?:'.$this->nmCharPattern.')*';
        $this->hashPattern = '#((?:'.$this->nmCharPattern.')+)';
        $this->numberPattern = '[+-]?(?:[0-9]*\.[0-9]+|[0-9]+)';
        $this->quotedStringPattern = '([^\n\r\f%s]|'.$this->stringEscapePattern.')*';
    }

    /**
     * @return string
     */
    public function getNewLineEscapePattern()
    {
        return '~^'.$this->newLineEscapePattern.'~';
    }

    /**
     * @return string
     */
    public function getSimpleEscapePattern()
    {
        return '~^'.$this->simpleEscapePattern.'~';
    }

    /**
     * @return string
     */
    public function getUnicodeEscapePattern()
    {
        return '~^'.$this->unicodeEscapePattern.'~i';
    }

    /**
     * @return string
     */
    public function getIdentifierPattern()
    {
        return '~^'.$this->identifierPattern.'~i';
    }

    /**
     * @return string
     */
    public function getHashPattern()
    {
        return '~^'.$this->hashPattern.'~i';
    }

    /**
     * @return string
     */
    public function getNumberPattern()
    {
        return '~^'.$this->numberPattern.'~';
    }

    /**
     * @param string $quote
     *
     * @return string
     */
    public function getQuotedStringPattern($quote)
    {
        return '~^'.sprintf($this->quotedStringPattern, $quote).'~i';
    }
}
