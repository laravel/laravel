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

use Symfony\Component\Mime\Encoder\Rfc2231Encoder;

/**
 * @author Chris Corbyn
 */
final class ParameterizedHeader extends UnstructuredHeader
{
    /**
     * RFC 2231's definition of a token.
     *
     * @var string
     */
    public const TOKEN_REGEX = '(?:[\x21\x23-\x27\x2A\x2B\x2D\x2E\x30-\x39\x41-\x5A\x5E-\x7E]+)';

    private ?Rfc2231Encoder $encoder = null;
    private array $parameters = [];

    public function __construct(string $name, string $value, array $parameters = [])
    {
        parent::__construct($name, $value);

        foreach ($parameters as $k => $v) {
            $this->setParameter($k, $v);
        }

        if ('content-type' !== strtolower($name)) {
            $this->encoder = new Rfc2231Encoder();
        }
    }

    public function setParameter(string $parameter, ?string $value): void
    {
        $this->setParameters(array_merge($this->getParameters(), [$parameter => $value]));
    }

    public function getParameter(string $parameter): string
    {
        return $this->getParameters()[$parameter] ?? '';
    }

    /**
     * @param string[] $parameters
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * @return string[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getBodyAsString(): string
    {
        $body = parent::getBodyAsString();
        foreach ($this->parameters as $name => $value) {
            if (null !== $value) {
                $body .= '; '.$this->createParameter($name, $value);
            }
        }

        return $body;
    }

    /**
     * Generate a list of all tokens in the final header.
     *
     * This doesn't need to be overridden in theory, but it is for implementation
     * reasons to prevent potential breakage of attributes.
     */
    protected function toTokens(?string $string = null): array
    {
        $tokens = parent::toTokens(parent::getBodyAsString());

        // Try creating any parameters
        foreach ($this->parameters as $name => $value) {
            if (null !== $value) {
                // Add the semi-colon separator
                $tokens[\count($tokens) - 1] .= ';';
                $tokens = array_merge($tokens, $this->generateTokenLines(' '.$this->createParameter($name, $value)));
            }
        }

        return $tokens;
    }

    /**
     * Render an RFC 2047 compliant header parameter from the $name and $value.
     */
    private function createParameter(string $name, string $value): string
    {
        $origValue = $value;

        $encoded = false;
        // Allow room for parameter name, indices, "=" and DQUOTEs
        $maxValueLength = $this->getMaxLineLength() - \strlen($name.'=*N"";') - 1;
        $firstLineOffset = 0;

        // If it's not already a valid parameter value...
        if (!preg_match('/^'.self::TOKEN_REGEX.'$/D', $value)) {
            // TODO: text, or something else??
            // ... and it's not ascii
            if (!preg_match('/^[\x00-\x08\x0B\x0C\x0E-\x7F]*$/D', $value)) {
                $encoded = true;
                // Allow space for the indices, charset and language
                $maxValueLength = $this->getMaxLineLength() - \strlen($name.'*N*="";') - 1;
                $firstLineOffset = \strlen($this->getCharset()."'".$this->getLanguage()."'");
            }

            if (\in_array($name, ['name', 'filename'], true) && 'form-data' === $this->getValue() && 'content-disposition' === strtolower($this->getName()) && preg_match('//u', $value)) {
                // WHATWG HTML living standard 4.10.21.8 2 specifies:
                // For field names and filenames for file fields, the result of the
                // encoding in the previous bullet point must be escaped by replacing
                // any 0x0A (LF) bytes with the byte sequence `%0A`, 0x0D (CR) with `%0D`
                // and 0x22 (") with `%22`.
                // The user agent must not perform any other escapes.
                $value = str_replace(['"', "\r", "\n"], ['%22', '%0D', '%0A'], $value);

                if (\strlen($value) <= $maxValueLength) {
                    return $name.'="'.$value.'"';
                }

                $value = $origValue;
            }
        }

        // Encode if we need to
        if ($encoded || \strlen($value) > $maxValueLength) {
            if (null !== $this->encoder) {
                $value = $this->encoder->encodeString($origValue, $this->getCharset(), $firstLineOffset, $maxValueLength);
            } else {
                // We have to go against RFC 2183/2231 in some areas for interoperability
                $value = $this->getTokenAsEncodedWord($origValue);
                $encoded = false;
            }
        }

        $valueLines = $this->encoder ? explode("\r\n", $value) : [$value];

        // Need to add indices
        if (\count($valueLines) > 1) {
            $paramLines = [];
            foreach ($valueLines as $i => $line) {
                $paramLines[] = $name.'*'.$i.$this->getEndOfParameterValue($line, true, 0 === $i);
            }

            return implode(";\r\n ", $paramLines);
        }

        return $name.$this->getEndOfParameterValue($valueLines[0], $encoded, true);
    }

    /**
     * Returns the parameter value from the "=" and beyond.
     *
     * @param string $value to append
     */
    private function getEndOfParameterValue(string $value, bool $encoded = false, bool $firstLine = false): string
    {
        $forceHttpQuoting = 'form-data' === $this->getValue() && 'content-disposition' === strtolower($this->getName());
        if ($forceHttpQuoting || !preg_match('/^'.self::TOKEN_REGEX.'$/D', $value)) {
            $value = '"'.$value.'"';
        }
        $prepend = '=';
        if ($encoded) {
            $prepend = '*=';
            if ($firstLine) {
                $prepend = '*='.$this->getCharset()."'".$this->getLanguage()."'";
            }
        }

        return $prepend.$value;
    }
}
