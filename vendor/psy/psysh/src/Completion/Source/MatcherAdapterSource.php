<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion\Source;

use Psy\Completion\AnalysisResult;
use Psy\Completion\CompletionKind;
use Psy\TabCompletion\Matcher\AbstractMatcher;
use Psy\TabCompletion\Matcher\ClassAttributesMatcher;
use Psy\TabCompletion\Matcher\ClassMethodsMatcher;
use Psy\TabCompletion\Matcher\ClassNamesMatcher;
use Psy\TabCompletion\Matcher\CommandsMatcher;
use Psy\TabCompletion\Matcher\ConstantsMatcher;
use Psy\TabCompletion\Matcher\FunctionsMatcher;
use Psy\TabCompletion\Matcher\KeywordsMatcher;
use Psy\TabCompletion\Matcher\MagicMethodsMatcher;
use Psy\TabCompletion\Matcher\MagicPropertiesMatcher;
use Psy\TabCompletion\Matcher\ObjectAttributesMatcher;
use Psy\TabCompletion\Matcher\ObjectMethodsMatcher;
use Psy\TabCompletion\Matcher\VariablesMatcher;

/**
 * Compatibility adapter for legacy TabCompletion matchers.
 *
 * Wraps one or more legacy AbstractMatcher instances and adapts them to work
 * with the CompletionEngine system, so users with custom matchers can continue
 * using them alongside the new AST-based completion sources.
 *
 * Default matchers that have been superseded by new-style completion sources
 * are automatically filtered out to avoid duplicate results.
 */
class MatcherAdapterSource implements SourceInterface
{
    /**
     * Matchers superseded by new-style completion sources.
     *
     * @var string[]
     */
    private const SUPERSEDED_MATCHERS = [
        ClassAttributesMatcher::class,
        ClassMethodsMatcher::class,
        ClassNamesMatcher::class,
        CommandsMatcher::class,
        ConstantsMatcher::class,
        FunctionsMatcher::class,
        KeywordsMatcher::class,
        MagicMethodsMatcher::class,
        MagicPropertiesMatcher::class,
        ObjectAttributesMatcher::class,
        ObjectMethodsMatcher::class,
        VariablesMatcher::class,
    ];

    /** @var AbstractMatcher[] */
    private array $matchers;

    /**
     * @param AbstractMatcher[] $matchers Legacy matchers to wrap
     */
    public function __construct(array $matchers)
    {
        $this->matchers = \array_filter(
            $matchers,
            fn ($matcher) => !\in_array(\get_class($matcher), self::SUPERSEDED_MATCHERS, true)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        // Legacy matchers don't understand command argument contexts.
        return ($kinds & ~CompletionKind::COMMAND_ARGUMENT) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        $tokens = $this->convertToTokens($analysis);
        $info = $this->buildReadlineInfo($analysis);

        $matches = [];
        foreach ($this->matchers as $matcher) {
            if ($matcher->hasMatched($tokens)) {
                $matches = \array_merge($matches, $matcher->getMatches($tokens, $info));
            }
        }

        return $matches;
    }

    /**
     * Convert AnalysisResult to token array for legacy matchers.
     *
     * Legacy matchers expect tokens from token_get_all(). We reconstruct
     * a reasonable approximation from the analysis result.
     *
     * @return array Token array compatible with legacy matchers
     */
    private function convertToTokens(AnalysisResult $analysis): array
    {
        if (!empty($analysis->tokens)) {
            return $this->filterTokens($analysis->tokens);
        }

        // Reconstruct a token array from the analysis result
        $tokens = [];

        if ($analysis->leftSide !== null) {
            $leftTokens = \token_get_all('<?php '.$analysis->leftSide);
            $tokens = \array_merge($tokens, \array_slice($leftTokens, 1));
        }

        if (($analysis->kinds & CompletionKind::OBJECT_MEMBER) !== 0) {
            $tokens[] = [\T_OBJECT_OPERATOR, '->', 0];
        } elseif (($analysis->kinds & CompletionKind::STATIC_MEMBER) !== 0) {
            $tokens[] = [\T_DOUBLE_COLON, '::', 0];
        }

        if ($analysis->prefix !== '') {
            $tokens[] = [\T_STRING, $analysis->prefix, 0];
        }

        return $this->filterTokens($tokens);
    }

    /**
     * Filter tokens to remove whitespace (legacy matcher expectation).
     *
     * @return array Filtered token array
     */
    private function filterTokens(array $tokens): array
    {
        $filtered = \array_filter($tokens, fn ($token) => !AbstractMatcher::tokenIs($token, AbstractMatcher::T_WHITESPACE));

        return \array_values($filtered);
    }

    /**
     * Build readline_info array for legacy matchers.
     *
     * @return array readline_info-like array
     */
    private function buildReadlineInfo(AnalysisResult $analysis): array
    {
        if ($analysis->readlineInfo !== []) {
            return $analysis->readlineInfo;
        }

        $lineBuffer = '';
        if ($analysis->leftSide !== null) {
            $lineBuffer .= $analysis->leftSide;

            if (($analysis->kinds & CompletionKind::OBJECT_MEMBER) !== 0) {
                $lineBuffer .= '->';
            } elseif (($analysis->kinds & CompletionKind::STATIC_MEMBER) !== 0) {
                $lineBuffer .= '::';
            }
        }
        $lineBuffer .= $analysis->prefix;

        return [
            'line_buffer' => $lineBuffer,
            'point'       => \strlen($lineBuffer),
            'end'         => \strlen($lineBuffer),
        ];
    }
}
