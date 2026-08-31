<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\CodeAnalysis;

use PhpParser\Error;
use PhpParser\Parser;
use Psy\ParserFactory;

/**
 * Maintains a cached analysis snapshot for code buffers.
 *
 * Uses a small LRU cache (2 entries) to avoid thrashing when callers
 * alternate between full-buffer and partial (before-cursor) text.
 */
class BufferAnalyzer
{
    private const CACHE_SIZE = 2;

    private Parser $parser;

    /**
     * LRU cache of recent analyses, most-recently-used first.
     *
     * @var array<int, array{code: string, analysis: BufferAnalysis}>
     */
    private array $cache = [];

    public function __construct(?Parser $parser = null)
    {
        $this->parser = $parser ?? (new ParserFactory())->createParser();
    }

    /**
     * Analyze the given code, using cached data when possible.
     */
    public function analyze(string $code): BufferAnalysis
    {
        foreach ($this->cache as $i => $entry) {
            if ($entry['code'] === $code) {
                if ($i > 0) {
                    \array_splice($this->cache, $i, 1);
                    \array_unshift($this->cache, $entry);
                }

                return $entry['analysis'];
            }
        }

        $analysis = $this->buildAnalysis($code);

        \array_unshift($this->cache, ['code' => $code, 'analysis' => $analysis]);
        if (\count($this->cache) > self::CACHE_SIZE) {
            \array_pop($this->cache);
        }

        return $analysis;
    }

    /**
     * Check whether appending a semicolon would make the code parseable.
     */
    public function canBeFixedWithSemicolon(string $code): bool
    {
        try {
            $this->parser->parse('<?php '.$code.";\n");

            return true;
        } catch (Error $e) {
            return false;
        }
    }

    private function buildAnalysis(string $code): BufferAnalysis
    {
        $tokens = @\token_get_all('<?php '.$code);

        $tokenPositions = [];
        $position = 0;
        foreach ($tokens as $index => $token) {
            $text = \is_array($token) ? $token[1] : $token;
            $length = \mb_strlen($text);
            $tokenPositions[$index] = ['start' => $position, 'end' => $position + $length];
            $position += $length;
        }

        $ast = null;
        $lastError = null;
        try {
            // Trailing newline improves heredoc EOF behavior to match runtime checks.
            $ast = $this->parser->parse('<?php '.$code."\n");
        } catch (Error $e) {
            $lastError = $e;
        }

        return $this->createAnalysis($code, $tokens, $tokenPositions, $ast, $lastError);
    }

    /**
     * @param string                                  $code
     * @param array<int, array|string>                $tokens
     * @param array<int, array{start: int, end: int}> $tokenPositions
     * @param array<int, mixed>|null                  $ast
     */
    protected function createAnalysis(string $code, array $tokens, array $tokenPositions, ?array $ast, ?Error $lastError): BufferAnalysis
    {
        return new BufferAnalysis($code, $tokens, $tokenPositions, $ast, $lastError);
    }
}
