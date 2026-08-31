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
use Psy\Completion\SymbolCatalog;

/**
 * Catalog-backed completion source.
 *
 * Provides completions for symbol types backed by SymbolCatalog (classes,
 * interfaces, traits, functions, constants).
 */
class CatalogSource implements SourceInterface
{
    private int $kind;
    private SymbolCatalog $catalog;

    private \Closure $catalogMethod;

    /**
     * @param int      $kind          CompletionKind bitmask to match
     * @param callable $catalogMethod Callable that takes a SymbolCatalog and returns string[]
     */
    public function __construct(int $kind, callable $catalogMethod, ?SymbolCatalog $catalog = null)
    {
        $this->kind = $kind;
        $this->catalogMethod = \Closure::fromCallable($catalogMethod);
        $this->catalog = $catalog ?? new SymbolCatalog();
    }

    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & $this->kind) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        $prefix = $analysis->prefix;
        $all = ($this->catalogMethod)($this->catalog);

        // If we have a prefix, try prefix matching first
        if ($prefix !== '') {
            $lowerPrefix = \strtolower($prefix);
            $matches = [];

            foreach ($all as $name) {
                if (\strpos(\strtolower($name), $lowerPrefix) === 0) {
                    $matches[] = $name;
                }
            }

            // If we found enough prefix matches, return those (sorted)
            if (\count($matches) >= 10) {
                \sort($matches);

                return $matches;
            }
        }

        return $all;
    }
}
