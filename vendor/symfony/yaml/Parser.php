<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Yaml;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Tag\TaggedValue;

/**
 * Parser parses YAML strings to convert them to PHP arrays.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class Parser
{
    public const TAG_PATTERN = '(?P<tag>![\w!.\/:-]+)';
    public const BLOCK_SCALAR_HEADER_PATTERN = '(?P<separator>\||>)(?P<modifiers>\+|\-|\d+|\+\d+|\-\d+|\d+\+|\d+\-)?(?P<comments> +#.*)?';
    public const REFERENCE_PATTERN = '#^&(?P<ref>[^ ]++) *+(?P<value>.*)#u';
    public const DEFAULT_MAX_NESTING_LEVEL = 128;
    public const DEFAULT_MAX_ALIASES_FOR_COLLECTIONS = 128;

    private ?string $filename = null;
    private int $offset = 0;
    private int $numberOfParsedLines = 0;
    private ?int $totalNumberOfLines = null;
    private array $lines = [];
    private int $currentLineNb = -1;
    private string $currentLine = '';
    private array $refs = [];
    private array $skippedLineNumbers = [];
    private array $locallySkippedLineNumbers = [];
    private array $refsBeingParsed = [];
    private ?ParserState $state = null;

    public function __construct(int $maxNestingLevel = self::DEFAULT_MAX_NESTING_LEVEL, int $maxAliasesForCollections = self::DEFAULT_MAX_ALIASES_FOR_COLLECTIONS)
    {
        if ($maxNestingLevel < 1) {
            throw new \InvalidArgumentException('The maximum nesting depth must be greater than 0.');
        }

        if ($maxAliasesForCollections < 0) {
            throw new \InvalidArgumentException('The maximum number of collection aliases must be greater than or equal to 0.');
        }

        $this->getState()->maxNestingLevel = $maxNestingLevel;
        $this->getState()->maxAliasesForCollections = $maxAliasesForCollections;
    }

    /**
     * Parses a YAML file into a PHP value.
     *
     * @param string                     $filename The path to the YAML file to be parsed
     * @param int-mask-of<Yaml::PARSE_*> $flags    A bit field of Yaml::PARSE_* constants to customize the YAML parser behavior
     *
     * @throws ParseException If the file could not be read or the YAML is not valid
     */
    public function parseFile(string $filename, int $flags = 0): mixed
    {
        if (!is_file($filename)) {
            throw new ParseException(\sprintf('File "%s" does not exist.', $filename));
        }

        if (!is_readable($filename)) {
            throw new ParseException(\sprintf('File "%s" cannot be read.', $filename));
        }

        $this->filename = $filename;

        try {
            return $this->parse(file_get_contents($filename), $flags);
        } finally {
            $this->filename = null;
        }
    }

    /**
     * Parses a YAML string to a PHP value.
     *
     * @param string                     $value A YAML string
     * @param int-mask-of<Yaml::PARSE_*> $flags A bit field of Yaml::PARSE_* constants to customize the YAML parser behavior
     *
     * @throws ParseException If the YAML is not valid
     */
    public function parse(string $value, int $flags = 0): mixed
    {
        if (false === preg_match('//u', $value)) {
            throw new ParseException('The YAML value does not appear to be valid UTF-8.', -1, null, $this->filename);
        }

        $this->refs = [];
        $state = $this->getState();
        $state->reset();
        $state->aliasesEnabled = 0 === (Yaml::PARSE_EXCEPTION_ON_ALIAS & $flags);

        try {
            $data = $this->doParse($value, $flags);
        } finally {
            $this->refsBeingParsed = [];
            $this->offset = 0;
            $this->lines = [];
            $this->currentLine = '';
            $this->numberOfParsedLines = 0;
            $this->refs = [];
            $this->skippedLineNumbers = [];
            $this->locallySkippedLineNumbers = [];
            $this->totalNumberOfLines = null;
            $state->reset();
        }

        return $data;
    }

    private function getState(): ParserState
    {
        return $this->state ??= new ParserState();
    }

    private function doParse(string $value, int $flags): mixed
    {
        $this->currentLineNb = -1;
        $this->currentLine = '';
        $value = $this->cleanup($value);
        $this->lines = explode("\n", $value);
        $this->numberOfParsedLines = \count($this->lines);
        $this->locallySkippedLineNumbers = [];
        $this->totalNumberOfLines ??= $this->numberOfParsedLines;

        if (!$this->moveToNextLine()) {
            return null;
        }

        $data = [];
        $context = null;
        $allowOverwrite = false;

        while ($this->isCurrentLineEmpty()) {
            if (!$this->moveToNextLine()) {
                return null;
            }
        }

        // Resolves the tag and returns if end of the document
        if (null !== ($tag = $this->getLineTag($this->currentLine, $flags, false)) && !$this->moveToNextLine()) {
            return new TaggedValue($tag, '');
        }

        do {
            if ($this->isCurrentLineEmpty()) {
                continue;
            }

            // tab?
            if ("\t" === $this->currentLine[0]) {
                throw new ParseException('A YAML file cannot contain tabs as indentation.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
            }

            Inline::initialize($flags, $this->getRealCurrentLineNb(), $this->filename);

            $isRef = $mergeNode = false;
            if ('-' === $this->currentLine[0] && self::preg_match('#^\-((?P<leadspaces>\s+)(?P<value>.+))?$#u', rtrim($this->currentLine), $values)) {
                if ($context && 'mapping' == $context) {
                    throw new ParseException('You cannot define a sequence item when in a mapping.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                }
                $context = 'sequence';

                if (isset($values['value']) && '&' === $values['value'][0] && self::preg_match(self::REFERENCE_PATTERN, $values['value'], $matches)) {
                    $isRef = $matches['ref'];
                    $this->refsBeingParsed[] = $isRef;
                    $values['value'] = $matches['value'];
                }

                if (isset($values['value'][1]) && '?' === $values['value'][0] && ' ' === $values['value'][1]) {
                    throw new ParseException('Complex mappings are not supported.', $this->getRealCurrentLineNb() + 1, $this->currentLine);
                }

                // array
                if (isset($values['value']) && str_starts_with(ltrim($values['value'], ' '), '-')) {
                    // Inline first child
                    $currentLineNumber = $this->getRealCurrentLineNb();

                    $sequenceIndentation = \strlen($values['leadspaces']) + 1;
                    $sequenceYaml = substr($this->currentLine, $sequenceIndentation);
                    $sequenceYaml .= "\n".$this->getNextEmbedBlock($sequenceIndentation, true);

                    $data[] = $this->parseBlock($currentLineNumber, rtrim($sequenceYaml), $flags);
                } elseif (!isset($values['value']) || '' == trim($values['value'], ' ') || str_starts_with(ltrim($values['value'], ' '), '#')) {
                    $data[] = $this->parseBlock($this->getRealCurrentLineNb() + 1, $this->getNextEmbedBlock(null, true) ?? '', $flags);
                } elseif (null !== $subTag = $this->getLineTag(ltrim($values['value'], ' '), $flags)) {
                    $data[] = new TaggedValue(
                        $subTag,
                        $this->parseBlock($this->getRealCurrentLineNb() + 1, $this->getNextEmbedBlock(null, true), $flags)
                    );
                } else {
                    if (
                        isset($values['leadspaces'])
                        && (
                            '!' === $values['value'][0]
                            || self::preg_match('#^(?P<key>'.Inline::REGEX_QUOTED_STRING.'|[^ \'"\{\[].*?) *\:(\s+(?P<value>.+?))?\s*$#u', $this->trimTag($values['value']), $matches)
                        )
                    ) {
                        $block = $values['value'];
                        if ($this->isNextLineIndented() || isset($matches['value']) && '>-' === $matches['value']) {
                            $block .= "\n".$this->getNextEmbedBlock($this->getCurrentLineIndentation() + \strlen($values['leadspaces']) + 1);
                        }

                        $data[] = $this->parseBlock($this->getRealCurrentLineNb(), $block, $flags);
                    } else {
                        $data[] = $this->parseValue($values['value'], $flags, $context);
                    }
                }
                if ($isRef) {
                    $this->refs[$isRef] = end($data);
                    array_pop($this->refsBeingParsed);
                }
            } elseif (
                self::preg_match('#^(?P<key>(?:![^\s]++\s++)?(?:'.Inline::REGEX_QUOTED_STRING.'|[^ \'"\[\{!].*?)) *\:(( |\t)++(?P<value>.+))?$#u', rtrim($this->currentLine), $values)
                && (!str_contains($values['key'], ' #') || \in_array($values['key'][0], ['"', "'"], true))
            ) {
                if ($context && 'sequence' == $context) {
                    throw new ParseException('You cannot define a mapping item when in a sequence.', $this->currentLineNb + 1, $this->currentLine, $this->filename);
                }
                $context = 'mapping';

                try {
                    $key = Inline::parseScalar($values['key']);
                } catch (ParseException $e) {
                    $e->setParsedLine($this->getRealCurrentLineNb() + 1);
                    $e->setSnippet($this->currentLine);

                    throw $e;
                }

                if (!\is_string($key) && !\is_int($key)) {
                    throw new ParseException((is_numeric($key) ? 'Numeric' : 'Non-string').' keys are not supported. Quote your evaluable mapping keys instead.', $this->getRealCurrentLineNb() + 1, $this->currentLine);
                }

                // Convert float keys to strings, to avoid being converted to integers by PHP
                if (\is_float($key)) {
                    $key = (string) $key;
                }

                if ('<<' === $key && (!isset($values['value']) || '&' !== $values['value'][0] || !self::preg_match('#^&(?P<ref>[^ ]+)#u', $values['value'], $refMatches))) {
                    $mergeNode = true;
                    $allowOverwrite = true;
                    if (isset($values['value'][0]) && '*' === $values['value'][0]) {
                        $refName = substr(rtrim($values['value']), 1);
                        if (!\array_key_exists($refName, $this->refs)) {
                            if (false !== $pos = array_search($refName, $this->refsBeingParsed, true)) {
                                throw new ParseException(\sprintf('Circular reference [%s] detected for reference "%s".', implode(', ', array_merge(\array_slice($this->refsBeingParsed, $pos), [$refName])), $refName), $this->currentLineNb + 1, $this->currentLine, $this->filename);
                            }

                            throw new ParseException(\sprintf('Reference "%s" does not exist.', $refName), $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }

                        $refValue = $this->refs[$refName];

                        $this->getState()->countAlias($refValue, $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);

                        if (Yaml::PARSE_OBJECT_FOR_MAP & $flags && $refValue instanceof \stdClass) {
                            $refValue = (array) $refValue;
                        }

                        if (!\is_array($refValue)) {
                            throw new ParseException('YAML merge keys used with a scalar value instead of an array.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }

                        $data += $refValue; // array union
                    } else {
                        if (isset($values['value']) && '' !== $values['value']) {
                            $value = $values['value'];
                        } else {
                            $value = $this->getNextEmbedBlock();
                        }
                        $parsed = $this->parseBlock($this->getRealCurrentLineNb() + 1, $value, $flags);

                        if (Yaml::PARSE_OBJECT_FOR_MAP & $flags && $parsed instanceof \stdClass) {
                            $parsed = (array) $parsed;
                        }

                        if (!\is_array($parsed)) {
                            throw new ParseException('YAML merge keys used with a scalar value instead of an array.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }

                        if (isset($parsed[0])) {
                            // If the value associated with the merge key is a sequence, then this sequence is expected to contain mapping nodes
                            // and each of these nodes is merged in turn according to its order in the sequence. Keys in mapping nodes earlier
                            // in the sequence override keys specified in later mapping nodes.
                            foreach ($parsed as $parsedItem) {
                                if (Yaml::PARSE_OBJECT_FOR_MAP & $flags && $parsedItem instanceof \stdClass) {
                                    $parsedItem = (array) $parsedItem;
                                }

                                if (!\is_array($parsedItem)) {
                                    throw new ParseException('Merge items must be arrays.', $this->getRealCurrentLineNb() + 1, $parsedItem, $this->filename);
                                }

                                $data += $parsedItem; // array union
                            }
                        } else {
                            // If the value associated with the key is a single mapping node, each of its key/value pairs is inserted into the
                            // current mapping, unless the key already exists in it.
                            $data += $parsed; // array union
                        }
                    }
                } elseif ('<<' !== $key && isset($values['value']) && '&' === $values['value'][0] && self::preg_match(self::REFERENCE_PATTERN, $values['value'], $matches)) {
                    $isRef = $matches['ref'];
                    $this->refsBeingParsed[] = $isRef;
                    $values['value'] = $matches['value'];
                }

                $subTag = null;
                if ($mergeNode) {
                    // Merge keys
                } elseif (!isset($values['value']) || '' === $values['value'] || str_starts_with($values['value'], '#') || (null !== $subTag = $this->getLineTag($values['value'], $flags)) || '<<' === $key) {
                    // hash
                    // if next line is less indented or equal, then it means that the current value is null
                    if (!$this->isNextLineIndented() && !$this->isNextLineUnIndentedCollection()) {
                        // Spec: Keys MUST be unique; first one wins.
                        // But overwriting is allowed when a merge node is used in current block.
                        if ($allowOverwrite || !isset($data[$key])) {
                            if (!$allowOverwrite && \array_key_exists($key, $data)) {
                                trigger_deprecation('symfony/yaml', '7.2', 'Duplicate key "%s" detected on line %d whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated and will throw a ParseException in 8.0.', $key, $this->getRealCurrentLineNb() + 1);
                            }

                            if (null !== $subTag) {
                                $data[$key] = new TaggedValue($subTag, '');
                            } else {
                                $data[$key] = null;
                            }
                        } else {
                            throw new ParseException(\sprintf('Duplicate key "%s" detected.', $key), $this->getRealCurrentLineNb() + 1, $this->currentLine);
                        }
                    } else {
                        // remember the parsed line number here in case we need it to provide some contexts in error messages below
                        $realCurrentLineNbKey = $this->getRealCurrentLineNb();
                        $value = $this->parseBlock($this->getRealCurrentLineNb() + 1, $this->getNextEmbedBlock(), $flags);
                        if ('<<' === $key) {
                            $this->refs[$refMatches['ref']] = $value;

                            if (Yaml::PARSE_OBJECT_FOR_MAP & $flags && $value instanceof \stdClass) {
                                $value = (array) $value;
                            }

                            $data += $value;
                        } elseif ($allowOverwrite || !isset($data[$key])) {
                            if (!$allowOverwrite && \array_key_exists($key, $data)) {
                                trigger_deprecation('symfony/yaml', '7.2', 'Duplicate key "%s" detected on line %d whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated and will throw a ParseException in 8.0.', $key, $this->getRealCurrentLineNb() + 1);
                            }

                            // Spec: Keys MUST be unique; first one wins.
                            // But overwriting is allowed when a merge node is used in current block.
                            if (null !== $subTag) {
                                $data[$key] = new TaggedValue($subTag, $value);
                            } else {
                                $data[$key] = $value;
                            }
                        } else {
                            throw new ParseException(\sprintf('Duplicate key "%s" detected.', $key), $realCurrentLineNbKey + 1, $this->currentLine);
                        }
                    }
                } else {
                    $value = $this->parseValue(rtrim($values['value']), $flags, $context);
                    // Spec: Keys MUST be unique; first one wins.
                    // But overwriting is allowed when a merge node is used in current block.
                    if ($allowOverwrite || !isset($data[$key])) {
                        if (!$allowOverwrite && \array_key_exists($key, $data)) {
                            trigger_deprecation('symfony/yaml', '7.2', 'Duplicate key "%s" detected on line %d whilst parsing YAML. Silent handling of duplicate mapping keys in YAML is deprecated and will throw a ParseException in 8.0.', $key, $this->getRealCurrentLineNb() + 1);
                        }

                        $data[$key] = $value;
                    } else {
                        throw new ParseException(\sprintf('Duplicate key "%s" detected.', $key), $this->getRealCurrentLineNb() + 1, $this->currentLine);
                    }
                }
                if ($isRef) {
                    $this->refs[$isRef] = $data[$key];
                    array_pop($this->refsBeingParsed);
                }
            } elseif ('"' === $this->currentLine[0] || "'" === $this->currentLine[0]) {
                if (null !== $context) {
                    throw new ParseException('Unable to parse.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                }

                try {
                    return Inline::parse($this->lexInlineQuotedString(), $flags, $this->refs, $this->state);
                } catch (ParseException $e) {
                    $e->setParsedLine($this->getRealCurrentLineNb() + 1);
                    $e->setSnippet($this->currentLine);

                    throw $e;
                }
            } elseif ('{' === $this->currentLine[0]) {
                if (null !== $context) {
                    throw new ParseException('Unable to parse.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                }

                try {
                    $parsedMapping = Inline::parse($this->lexInlineMapping(), $flags, $this->refs, $this->state);

                    while ($this->moveToNextLine()) {
                        if (!$this->isCurrentLineEmpty()) {
                            throw new ParseException('Unable to parse.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }
                    }

                    return $parsedMapping;
                } catch (ParseException $e) {
                    $e->setParsedLine($this->getRealCurrentLineNb() + 1);
                    $e->setSnippet($this->currentLine);

                    throw $e;
                }
            } elseif ('[' === $this->currentLine[0]) {
                if (null !== $context) {
                    throw new ParseException('Unable to parse.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                }

                try {
                    $parsedSequence = Inline::parse($this->lexInlineSequence(), $flags, $this->refs, $this->state);

                    while ($this->moveToNextLine()) {
                        if (!$this->isCurrentLineEmpty()) {
                            throw new ParseException('Unable to parse.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }
                    }

                    return $parsedSequence;
                } catch (ParseException $e) {
                    $e->setParsedLine($this->getRealCurrentLineNb() + 1);
                    $e->setSnippet($this->currentLine);

                    throw $e;
                }
            } else {
                // multiple documents are not supported
                if ('---' === $this->currentLine) {
                    throw new ParseException('Multiple documents are not supported.', $this->currentLineNb + 1, $this->currentLine, $this->filename);
                }

                if (isset($this->currentLine[1]) && '?' === $this->currentLine[0] && ' ' === $this->currentLine[1]) {
                    throw new ParseException('Complex mappings are not supported.', $this->getRealCurrentLineNb() + 1, $this->currentLine);
                }

                // 1-liner optionally followed by newline(s)
                if (\is_string($value) && $this->lines[0] === trim($value)) {
                    try {
                        $value = Inline::parse($this->lines[0], $flags, $this->refs, $this->state);
                    } catch (ParseException $e) {
                        $e->setParsedLine($this->getRealCurrentLineNb() + 1);
                        $e->setSnippet($this->currentLine);

                        throw $e;
                    }

                    return $value;
                }

                // try to parse the value as a multi-line string as a last resort
                if (0 === $this->currentLineNb) {
                    $previousLineWasNewline = false;
                    $previousLineWasTerminatedWithBackslash = false;
                    $value = '';

                    foreach ($this->lines as $line) {
                        $trimmedLine = trim($line);
                        if ('#' === ($trimmedLine[0] ?? '')) {
                            continue;
                        }
                        // If the indentation is not consistent at offset 0, it is to be considered as a ParseError
                        if (0 === $this->offset && isset($line[0]) && ' ' === $line[0]) {
                            throw new ParseException('Unable to parse.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }

                        if (str_contains($line, ': ')) {
                            throw new ParseException('Mapping values are not allowed in multi-line blocks.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }

                        if ('' === $trimmedLine) {
                            $value .= "\n";
                        } elseif (!$previousLineWasNewline && !$previousLineWasTerminatedWithBackslash) {
                            $value .= ' ';
                        }

                        if ('' !== $trimmedLine && str_ends_with($line, '\\')) {
                            $value .= ltrim(substr($line, 0, -1));
                        } elseif ('' !== $trimmedLine) {
                            $value .= $trimmedLine;
                        }

                        if ('' === $trimmedLine) {
                            $previousLineWasNewline = true;
                            $previousLineWasTerminatedWithBackslash = false;
                        } elseif (str_ends_with($line, '\\')) {
                            $previousLineWasNewline = false;
                            $previousLineWasTerminatedWithBackslash = true;
                        } else {
                            $previousLineWasNewline = false;
                            $previousLineWasTerminatedWithBackslash = false;
                        }
                    }

                    try {
                        return Inline::parse(trim($value), 0, $this->refs, $this->state);
                    } catch (ParseException) {
                        // fall-through to the ParseException thrown below
                    }
                }

                throw new ParseException('Unable to parse.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
            }
        } while ($this->moveToNextLine());

        if (null !== $tag) {
            $data = new TaggedValue($tag, $data);
        }

        if (Yaml::PARSE_OBJECT_FOR_MAP & $flags && 'mapping' === $context && !\is_object($data)) {
            $object = new \stdClass();

            foreach ($data as $key => $value) {
                $object->$key = $value;
            }

            $data = $object;
        }

        return $data ?: null;
    }

    private function parseBlock(int $offset, string $yaml, int $flags): mixed
    {
        $skippedLineNumbers = $this->skippedLineNumbers;

        foreach ($this->locallySkippedLineNumbers as $lineNumber) {
            if ($lineNumber < $offset) {
                continue;
            }

            $skippedLineNumbers[] = $lineNumber;
        }

        $parser = new self();
        $parser->offset = $offset;
        $parser->totalNumberOfLines = $this->totalNumberOfLines;
        $parser->skippedLineNumbers = $skippedLineNumbers;
        $parser->refs = &$this->refs;
        $parser->refsBeingParsed = $this->refsBeingParsed;
        $parser->state = $this->state;

        $this->getState()->enterNestingLevel($offset + 1, $this->currentLine, $this->filename);

        try {
            return $parser->doParse($yaml, $flags);
        } finally {
            $this->getState()->leaveNestingLevel();
        }
    }

    /**
     * Returns the current line number (takes the offset into account).
     *
     * @internal
     */
    public function getRealCurrentLineNb(): int
    {
        $realCurrentLineNumber = $this->currentLineNb + $this->offset;

        foreach ($this->skippedLineNumbers as $skippedLineNumber) {
            if ($skippedLineNumber > $realCurrentLineNumber) {
                break;
            }

            ++$realCurrentLineNumber;
        }

        return $realCurrentLineNumber;
    }

    private function getCurrentLineIndentation(): int
    {
        if (' ' !== ($this->currentLine[0] ?? '')) {
            return 0;
        }

        return \strlen($this->currentLine) - \strlen(ltrim($this->currentLine, ' '));
    }

    /**
     * Returns the next embed block of YAML.
     *
     * @param int|null $indentation The indent level at which the block is to be read, or null for default
     * @param bool     $inSequence  True if the enclosing data structure is a sequence
     *
     * @throws ParseException When indentation problem are detected
     */
    private function getNextEmbedBlock(?int $indentation = null, bool $inSequence = false): string
    {
        $oldLineIndentation = $this->getCurrentLineIndentation();

        if (!$this->moveToNextLine()) {
            return '';
        }

        if (null === $indentation) {
            $newIndent = null;
            $movements = 0;

            do {
                $EOF = false;

                // empty and comment-like lines do not influence the indentation depth
                if ($this->isCurrentLineEmpty() || $this->isCurrentLineComment()) {
                    $EOF = !$this->moveToNextLine();

                    if (!$EOF) {
                        ++$movements;
                    }
                } else {
                    $newIndent = $this->getCurrentLineIndentation();
                }
            } while (!$EOF && null === $newIndent);

            for ($i = 0; $i < $movements; ++$i) {
                $this->moveToPreviousLine();
            }

            $unindentedEmbedBlock = $this->isStringUnIndentedCollectionItem();

            if (!$this->isCurrentLineEmpty() && 0 === $newIndent && !$unindentedEmbedBlock) {
                throw new ParseException('Indentation problem.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
            }
        } else {
            $newIndent = $indentation;
        }

        $data = [];

        if ($this->getCurrentLineIndentation() >= $newIndent) {
            $data[] = substr($this->currentLine, $newIndent ?? 0);
        } elseif ($this->isCurrentLineEmpty() || $this->isCurrentLineComment()) {
            $data[] = $this->currentLine;
        } else {
            $this->moveToPreviousLine();

            return '';
        }

        if ($inSequence && $oldLineIndentation === $newIndent && isset($data[0][0]) && '-' === $data[0][0]) {
            // the previous line contained a dash but no item content, this line is a sequence item with the same indentation
            // and therefore no nested list or mapping
            $this->moveToPreviousLine();

            return '';
        }

        $isItUnindentedCollection = $this->isStringUnIndentedCollectionItem();
        $isItComment = $this->isCurrentLineComment();

        while ($this->moveToNextLine()) {
            if ($isItComment && !$isItUnindentedCollection) {
                $isItUnindentedCollection = $this->isStringUnIndentedCollectionItem();
                $isItComment = $this->isCurrentLineComment();
            }

            $indent = $this->getCurrentLineIndentation();

            if ($isItUnindentedCollection && !$this->isCurrentLineEmpty() && !$this->isStringUnIndentedCollectionItem() && $newIndent === $indent) {
                $this->moveToPreviousLine();
                break;
            }

            if ($this->isCurrentLineBlank()) {
                $data[] = substr($this->currentLine, $newIndent ?? 0);
                continue;
            }

            if ($indent >= $newIndent) {
                $data[] = substr($this->currentLine, $newIndent ?? 0);
            } elseif ($this->isCurrentLineComment()) {
                $data[] = $this->currentLine;
            } elseif (0 == $indent) {
                $this->moveToPreviousLine();

                break;
            } else {
                throw new ParseException('Indentation problem.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
            }
        }

        return implode("\n", $data);
    }

    private function hasMoreLines(): bool
    {
        return (\count($this->lines) - 1) > $this->currentLineNb;
    }

    /**
     * Moves the parser to the next line.
     */
    private function moveToNextLine(): bool
    {
        if ($this->currentLineNb >= $this->numberOfParsedLines - 1) {
            return false;
        }

        $this->currentLine = $this->lines[++$this->currentLineNb];

        return true;
    }

    /**
     * Moves the parser to the previous line.
     */
    private function moveToPreviousLine(): bool
    {
        if ($this->currentLineNb < 1) {
            return false;
        }

        $this->currentLine = $this->lines[--$this->currentLineNb];

        return true;
    }

    /**
     * Parses a YAML value.
     *
     * @param string $value   A YAML value
     * @param int    $flags   A bit field of Yaml::PARSE_* constants to customize the YAML parser behavior
     * @param string $context The parser context (either sequence or mapping)
     *
     * @throws ParseException When reference does not exist
     */
    private function parseValue(string $value, int $flags, string $context): mixed
    {
        if (str_starts_with($value, '*')) {
            if (false !== $pos = strpos($value, '#')) {
                $value = substr($value, 1, $pos - 2);
            } else {
                $value = substr($value, 1);
            }

            if (!\array_key_exists($value, $this->refs)) {
                if (false !== $pos = array_search($value, $this->refsBeingParsed, true)) {
                    throw new ParseException(\sprintf('Circular reference [%s] detected for reference "%s".', implode(', ', array_merge(\array_slice($this->refsBeingParsed, $pos), [$value])), $value), $this->currentLineNb + 1, $this->currentLine, $this->filename);
                }

                throw new ParseException(\sprintf('Reference "%s" does not exist.', $value), $this->currentLineNb + 1, $this->currentLine, $this->filename);
            }

            $this->getState()->countAlias($this->refs[$value], $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);

            return $this->refs[$value];
        }

        if (\in_array($value[0], ['!', '|', '>'], true) && self::preg_match('/^(?:'.self::TAG_PATTERN.' +)?'.self::BLOCK_SCALAR_HEADER_PATTERN.'$/', $value, $matches)) {
            $modifiers = $matches['modifiers'] ?? '';

            $data = $this->parseBlockScalar($matches['separator'], preg_replace('#\d+#', '', $modifiers), abs((int) $modifiers));

            if ('' !== $matches['tag'] && '!' !== $matches['tag']) {
                if ('!!binary' === $matches['tag']) {
                    return Inline::evaluateBinaryScalar($data);
                }

                return new TaggedValue(substr($matches['tag'], 1), $data);
            }

            return $data;
        }

        try {
            if ('' !== $value && '{' === $value[0]) {
                $cursor = \strlen(rtrim($this->currentLine)) - \strlen(rtrim($value));

                return Inline::parse($this->lexInlineMapping($cursor), $flags, $this->refs, $this->state);
            } elseif ('' !== $value && '[' === $value[0]) {
                $cursor = \strlen(rtrim($this->currentLine)) - \strlen(rtrim($value));

                return Inline::parse($this->lexInlineSequence($cursor), $flags, $this->refs, $this->state);
            }

            switch ($value[0] ?? '') {
                case '"':
                case "'":
                    $cursor = \strlen(rtrim($this->currentLine)) - \strlen(rtrim($value));
                    $parsedValue = Inline::parse($this->lexInlineQuotedString($cursor), $flags, $this->refs, $this->state);

                    if (isset($this->currentLine[$cursor]) && preg_replace('/\s*(#.*)?$/A', '', substr($this->currentLine, $cursor))) {
                        throw new ParseException(\sprintf('Unexpected characters near "%s".', substr($this->currentLine, $cursor)));
                    }

                    return $parsedValue;
                default:
                    $lines = [];

                    while ($this->moveToNextLine()) {
                        // unquoted strings end before the first unindented line
                        if (0 === $this->getCurrentLineIndentation()) {
                            $this->moveToPreviousLine();

                            break;
                        }

                        if ($this->isCurrentLineComment()) {
                            break;
                        }

                        if ('mapping' === $context && str_contains($this->currentLine, ': ') && !$this->isCurrentLineComment()) {
                            throw new ParseException('A colon cannot be used in an unquoted mapping value.', $this->getRealCurrentLineNb() + 1, $this->currentLine, $this->filename);
                        }

                        $lines[] = trim($this->currentLine);
                    }

                    for ($i = 0, $linesCount = \count($lines), $previousLineBlank = false; $i < $linesCount; ++$i) {
                        if ('' === $lines[$i]) {
                            $value .= "\n";
                            $previousLineBlank = true;
                        } elseif ($previousLineBlank) {
                            $value .= $lines[$i];
                            $previousLineBlank = false;
                        } else {
                            $value .= ' '.$lines[$i];
                            $previousLineBlank = false;
                        }
                    }

                    Inline::$parsedLineNumber = $this->getRealCurrentLineNb();

                    $parsedValue = Inline::parse($value, $flags, $this->refs, $this->state);

                    if ('mapping' === $context && \is_string($parsedValue) && '"' !== $value[0] && "'" !== $value[0] && '[' !== $value[0] && '{' !== $value[0] && '!' !== $value[0] && str_contains($parsedValue, ': ')) {
                        throw new ParseException('A colon cannot be used in an unquoted mapping value.', $this->getRealCurrentLineNb() + 1, $value, $this->filename);
                    }

                    return $parsedValue;
            }
        } catch (ParseException $e) {
            $e->setParsedLine($this->getRealCurrentLineNb() + 1);
            $e->setSnippet($this->currentLine);

            throw $e;
        }
    }

    /**
     * Parses a block scalar.
     *
     * @param string $style       The style indicator that was used to begin this block scalar (| or >)
     * @param string $chomping    The chomping indicator that was used to begin this block scalar (+ or -)
     * @param int    $indentation The indentation indicator that was used to begin this block scalar
     */
    private function parseBlockScalar(string $style, string $chomping = '', int $indentation = 0): string
    {
        $notEOF = $this->moveToNextLine();
        if (!$notEOF) {
            return '';
        }

        $isCurrentLineBlank = $this->isCurrentLineBlank();
        $blockLines = [];

        // leading blank lines are consumed before determining indentation
        while ($notEOF && $isCurrentLineBlank) {
            // newline only if not EOF
            if ($notEOF = $this->moveToNextLine()) {
                $blockLines[] = '';
                $isCurrentLineBlank = $this->isCurrentLineBlank();
            }
        }

        // determine indentation if not specified
        if (0 === $indentation) {
            $currentLineLength = \strlen($this->currentLine);

            for ($i = 0; $i < $currentLineLength && ' ' === $this->currentLine[$i]; ++$i) {
                ++$indentation;
            }
        }

        if ($indentation > 0) {
            $pattern = \sprintf('/^ {%d}(.*)$/', $indentation);

            while (
                $notEOF && (
                    $isCurrentLineBlank
                    || self::preg_match($pattern, $this->currentLine, $matches)
                )
            ) {
                if ($isCurrentLineBlank && \strlen($this->currentLine) > $indentation) {
                    $blockLines[] = substr($this->currentLine, $indentation);
                } elseif ($isCurrentLineBlank) {
                    $blockLines[] = '';
                } else {
                    $blockLines[] = $matches[1];
                }

                // newline only if not EOF
                if ($notEOF = $this->moveToNextLine()) {
                    $isCurrentLineBlank = $this->isCurrentLineBlank();
                }
            }
        } elseif ($notEOF) {
            $blockLines[] = '';
        }

        if ($notEOF) {
            $blockLines[] = '';
            $this->moveToPreviousLine();
        } elseif (!$this->isCurrentLineLastLineInDocument()) {
            $blockLines[] = '';
        }

        // folded style
        if ('>' === $style) {
            $text = '';
            $previousLineIndented = false;
            $previousLineBlank = false;

            for ($i = 0, $blockLinesCount = \count($blockLines); $i < $blockLinesCount; ++$i) {
                if ('' === $blockLines[$i]) {
                    $text .= "\n";
                    $previousLineIndented = false;
                    $previousLineBlank = true;
                } elseif (' ' === $blockLines[$i][0]) {
                    $text .= "\n".$blockLines[$i];
                    $previousLineIndented = true;
                    $previousLineBlank = false;
                } elseif ($previousLineIndented) {
                    $text .= "\n".$blockLines[$i];
                    $previousLineIndented = false;
                    $previousLineBlank = false;
                } elseif ($previousLineBlank || 0 === $i) {
                    $text .= $blockLines[$i];
                    $previousLineIndented = false;
                    $previousLineBlank = false;
                } else {
                    $text .= ' '.$blockLines[$i];
                    $previousLineIndented = false;
                    $previousLineBlank = false;
                }
            }
        } else {
            $text = implode("\n", $blockLines);
        }

        // deal with trailing newlines
        if ('' === $chomping) {
            $text = preg_replace('/\n+$/', "\n", $text);
        } elseif ('-' === $chomping) {
            $text = preg_replace('/\n+$/', '', $text);
        }

        return $text;
    }

    /**
     * Returns true if the next line is indented.
     */
    private function isNextLineIndented(): bool
    {
        $currentIndentation = $this->getCurrentLineIndentation();
        $movements = 0;

        do {
            $EOF = !$this->moveToNextLine();

            if (!$EOF) {
                ++$movements;
            }
        } while (!$EOF && ($this->isCurrentLineEmpty() || $this->isCurrentLineComment()));

        if ($EOF) {
            for ($i = 0; $i < $movements; ++$i) {
                $this->moveToPreviousLine();
            }

            return false;
        }

        $ret = $this->getCurrentLineIndentation() > $currentIndentation;

        for ($i = 0; $i < $movements; ++$i) {
            $this->moveToPreviousLine();
        }

        return $ret;
    }

    private function isCurrentLineEmpty(): bool
    {
        return $this->isCurrentLineBlank() || $this->isCurrentLineComment();
    }

    private function isCurrentLineBlank(): bool
    {
        return '' === $this->currentLine || '' === trim($this->currentLine, ' ');
    }

    private function isCurrentLineComment(): bool
    {
        // checking explicitly the first char of the trim is faster than loops or strpos
        $ltrimmedLine = '' !== $this->currentLine && ' ' === $this->currentLine[0] ? ltrim($this->currentLine, ' ') : $this->currentLine;

        return '' !== $ltrimmedLine && '#' === $ltrimmedLine[0];
    }

    private function isCurrentLineLastLineInDocument(): bool
    {
        return ($this->offset + $this->currentLineNb) >= ($this->totalNumberOfLines - 1);
    }

    private function cleanup(string $value): string
    {
        $value = str_replace(["\r\n", "\r"], "\n", $value);

        // strip YAML header
        $count = 0;
        $value = preg_replace('#^%YAML[: ][\d.]++[^\n]*+\n#u', '', $value, -1, $count);
        $this->offset += $count;

        // remove leading comments
        $trimmedValue = preg_replace('#^(?:\#[^\n]*+\n)++#', '', $value, -1, $count);
        if (1 === $count) {
            // items have been removed, update the offset
            $this->offset += substr_count($value, "\n") - substr_count($trimmedValue, "\n");
            $value = $trimmedValue;
        }

        // remove start of the document marker (---)
        $trimmedValue = preg_replace('#^---[^\n]*+\n#', '', $value, -1, $count);
        if (1 === $count) {
            // items have been removed, update the offset
            $this->offset += substr_count($value, "\n") - substr_count($trimmedValue, "\n");
            $value = $trimmedValue;

            // remove end of the document marker (...)
            $value = preg_replace('#\.\.\.\s*+$#', '', $value);
        }

        return $value;
    }

    private function isNextLineUnIndentedCollection(): bool
    {
        $currentIndentation = $this->getCurrentLineIndentation();
        $movements = 0;

        do {
            $EOF = !$this->moveToNextLine();

            if (!$EOF) {
                ++$movements;
            }
        } while (!$EOF && ($this->isCurrentLineEmpty() || $this->isCurrentLineComment()));

        if ($EOF) {
            return false;
        }

        $ret = $this->getCurrentLineIndentation() === $currentIndentation && $this->isStringUnIndentedCollectionItem();

        for ($i = 0; $i < $movements; ++$i) {
            $this->moveToPreviousLine();
        }

        return $ret;
    }

    private function isStringUnIndentedCollectionItem(): bool
    {
        return '-' === rtrim($this->currentLine) || str_starts_with($this->currentLine, '- ');
    }

    /**
     * A local wrapper for "preg_match" which will throw a ParseException if there
     * is an internal error in the PCRE engine.
     *
     * This avoids us needing to check for "false" every time PCRE is used
     * in the YAML engine
     *
     * @throws ParseException on a PCRE internal error
     *
     * @internal
     */
    public static function preg_match(string $pattern, string $subject, ?array &$matches = null, int $flags = 0, int $offset = 0): int
    {
        if (false === $ret = preg_match($pattern, $subject, $matches, $flags, $offset)) {
            throw new ParseException(preg_last_error_msg());
        }

        return $ret;
    }

    /**
     * Trim the tag on top of the value.
     *
     * Prevent values such as "!foo {quz: bar}" to be considered as
     * a mapping block.
     */
    private function trimTag(string $value): string
    {
        if ('!' === $value[0]) {
            return ltrim(substr($value, 1, strcspn($value, " \r\n", 1)), ' ');
        }

        return $value;
    }

    private function getLineTag(string $value, int $flags, bool $nextLineCheck = true): ?string
    {
        if ('' === $value || '!' !== $value[0] || 1 !== self::preg_match('/^'.self::TAG_PATTERN.' *( +#.*)?$/', $value, $matches)) {
            return null;
        }

        if ($nextLineCheck && !$this->isNextLineIndented()) {
            return null;
        }

        $tag = substr($matches['tag'], 1);

        // Built-in tags
        if ($tag && '!' === $tag[0]) {
            throw new ParseException(\sprintf('The built-in tag "!%s" is not implemented.', $tag), $this->getRealCurrentLineNb() + 1, $value, $this->filename);
        }

        if (Yaml::PARSE_CUSTOM_TAGS & $flags) {
            return $tag;
        }

        throw new ParseException(\sprintf('Tags support is not enabled. You must use the flag "Yaml::PARSE_CUSTOM_TAGS" to use "%s".', $matches['tag']), $this->getRealCurrentLineNb() + 1, $value, $this->filename);
    }

    private function lexInlineQuotedString(int &$cursor = 0): string
    {
        $quotation = $this->currentLine[$cursor];
        $value = $quotation;
        ++$cursor;

        $previousLineWasNewline = true;
        $previousLineWasTerminatedWithBackslash = false;
        $lineNumber = 0;

        do {
            if (++$lineNumber > 1) {
                $cursor += strspn($this->currentLine, ' ', $cursor);
            }

            if ($this->isCurrentLineBlank()) {
                $value .= "\n";
            } elseif (!$previousLineWasNewline && !$previousLineWasTerminatedWithBackslash) {
                $value .= ' ';
            }

            for (; \strlen($this->currentLine) > $cursor; ++$cursor) {
                switch ($this->currentLine[$cursor]) {
                    case '\\':
                        if ("'" === $quotation) {
                            $value .= '\\';
                        } elseif (isset($this->currentLine[++$cursor])) {
                            $value .= '\\'.$this->currentLine[$cursor];
                        }

                        break;
                    case $quotation:
                        ++$cursor;

                        if ("'" === $quotation && isset($this->currentLine[$cursor]) && "'" === $this->currentLine[$cursor]) {
                            $value .= "''";
                            break;
                        }

                        return $value.$quotation;
                    default:
                        $value .= $this->currentLine[$cursor];
                }
            }

            if ($this->isCurrentLineBlank()) {
                $previousLineWasNewline = true;
                $previousLineWasTerminatedWithBackslash = false;
            } elseif ('\\' === $this->currentLine[-1]) {
                $previousLineWasNewline = false;
                $previousLineWasTerminatedWithBackslash = true;
            } else {
                $previousLineWasNewline = false;
                $previousLineWasTerminatedWithBackslash = false;
            }

            if ($this->hasMoreLines()) {
                $cursor = 0;
            }
        } while ($this->moveToNextLine());

        throw new ParseException('Malformed inline YAML string.');
    }

    private function lexUnquotedString(int &$cursor): string
    {
        $offset = $cursor;

        while ($cursor < \strlen($this->currentLine)) {
            if (\in_array($this->currentLine[$cursor], ['[', ']', '{', '}', ',', ':'], true)) {
                break;
            }

            if (\in_array($this->currentLine[$cursor], [' ', "\t"], true) && '#' === ($this->currentLine[$cursor + 1] ?? '')) {
                break;
            }

            ++$cursor;
        }

        if ($cursor === $offset) {
            throw new ParseException('Malformed unquoted YAML string.');
        }

        return substr($this->currentLine, $offset, $cursor - $offset);
    }

    private function lexInlineAnchorOrAlias(int &$cursor): string
    {
        $offset = $cursor;
        ++$cursor;

        while ($cursor < \strlen($this->currentLine) && !\in_array($this->currentLine[$cursor], [' ', "\t", ',', '[', ']', '{', '}'], true)) {
            ++$cursor;
        }

        return substr($this->currentLine, $offset, $cursor - $offset);
    }

    private function lexInlineMapping(int &$cursor = 0, bool $consumeUntilEol = true): string
    {
        return $this->lexInlineStructure($cursor, '}', $consumeUntilEol);
    }

    private function lexInlineSequence(int &$cursor = 0, bool $consumeUntilEol = true): string
    {
        return $this->lexInlineStructure($cursor, ']', $consumeUntilEol);
    }

    private function lexInlineStructure(int &$cursor, string $closingTag, bool $consumeUntilEol = true): string
    {
        $value = $this->currentLine[$cursor];
        ++$cursor;

        do {
            $this->consumeWhitespaces($cursor);

            while (isset($this->currentLine[$cursor])) {
                switch ($this->currentLine[$cursor]) {
                    case '"':
                    case "'":
                        $value .= $this->lexInlineQuotedString($cursor);
                        break;
                    case ':':
                    case ',':
                        $value .= $this->currentLine[$cursor];
                        ++$cursor;
                        break;
                    case '{':
                        $value .= $this->lexInlineMapping($cursor, false);
                        break;
                    case '[':
                        $value .= $this->lexInlineSequence($cursor, false);
                        break;
                    case '&':
                    case '*':
                        $value .= $this->lexInlineAnchorOrAlias($cursor);
                        break;
                    case $closingTag:
                        $value .= $this->currentLine[$cursor];
                        ++$cursor;

                        if ($consumeUntilEol && isset($this->currentLine[$cursor]) && ($whitespaces = strspn($this->currentLine, ' ', $cursor) + $cursor) < \strlen($this->currentLine) && '#' !== $this->currentLine[$whitespaces]) {
                            throw new ParseException(\sprintf('Unexpected token "%s".', trim(substr($this->currentLine, $cursor))));
                        }

                        return $value;
                    case '#':
                        break 2;
                    default:
                        $value .= $this->lexUnquotedString($cursor);
                }

                if ($this->consumeWhitespaces($cursor)) {
                    $value .= ' ';
                }
            }

            if ($this->hasMoreLines()) {
                $cursor = 0;
            }
        } while ($this->moveToNextLine());

        throw new ParseException('Malformed inline YAML string.');
    }

    private function consumeWhitespaces(int &$cursor): bool
    {
        $whitespacesConsumed = 0;

        do {
            $whitespaceOnlyTokenLength = strspn($this->currentLine, " \t", $cursor);
            $whitespacesConsumed += $whitespaceOnlyTokenLength;
            $cursor += $whitespaceOnlyTokenLength;

            if (isset($this->currentLine[$cursor])) {
                return 0 < $whitespacesConsumed;
            }

            if ($this->hasMoreLines()) {
                $cursor = 0;
            }
        } while ($this->moveToNextLine());

        return 0 < $whitespacesConsumed;
    }
}
