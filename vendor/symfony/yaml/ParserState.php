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
 * @internal
 */
final class ParserState
{
    public int $maxNestingLevel = Parser::DEFAULT_MAX_NESTING_LEVEL;
    public int $currentNestingLevel = 0;
    public int $maxAliasesForCollections = Parser::DEFAULT_MAX_ALIASES_FOR_COLLECTIONS;
    public int $collectionAliasCount = 0;
    public bool $aliasesEnabled = true;

    public function reset(): void
    {
        $this->currentNestingLevel = 0;
        $this->collectionAliasCount = 0;
        $this->aliasesEnabled = true;
    }

    public function enterNestingLevel(int $line, ?string $snippet, ?string $filename): void
    {
        if (++$this->currentNestingLevel > $this->maxNestingLevel) {
            --$this->currentNestingLevel;

            throw new ParseException(\sprintf('Maximum nesting depth of %d exceeded.', $this->maxNestingLevel), $line, $snippet, $filename);
        }
    }

    public function leaveNestingLevel(): void
    {
        if ($this->currentNestingLevel > 0) {
            --$this->currentNestingLevel;
        }
    }

    public function countAlias(mixed $refValue, int $line, ?string $snippet, ?string $filename): void
    {
        if (!$this->aliasesEnabled) {
            throw new ParseException('Aliases are disabled.', $line, $snippet, $filename);
        }

        if ($refValue instanceof TaggedValue) {
            $refValue = $refValue->getValue();
        }

        if (!\is_array($refValue) && !$refValue instanceof \stdClass) {
            return;
        }

        if (++$this->collectionAliasCount > $this->maxAliasesForCollections) {
            throw new ParseException(\sprintf('Maximum number of collection aliases (%d) exceeded. This limit can be increased via the Parser constructor.', $this->maxAliasesForCollections), $line, $snippet, $filename);
        }
    }
}
