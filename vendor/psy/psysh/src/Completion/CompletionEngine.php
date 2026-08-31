<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion;

use PhpParser\Node as AstNode;
use Psy\CodeCleaner;
use Psy\Completion\Refiner\AnalysisRefinerInterface;
use Psy\Completion\Refiner\CommandSyntaxRefiner;
use Psy\Completion\Refiner\PartialInputRefiner;
use Psy\Completion\Source\CatalogSource;
use Psy\Completion\Source\ClassConstantSource;
use Psy\Completion\Source\KeywordSource;
use Psy\Completion\Source\MagicMethodSource;
use Psy\Completion\Source\MagicPropertySource;
use Psy\Completion\Source\MethodSource;
use Psy\Completion\Source\NamespaceSource;
use Psy\Completion\Source\ObjectMethodSource;
use Psy\Completion\Source\ObjectPropertySource;
use Psy\Completion\Source\PropertySource;
use Psy\Completion\Source\SourceInterface;
use Psy\Completion\Source\StaticMethodSource;
use Psy\Completion\Source\StaticPropertySource;
use Psy\Completion\Source\VariableSource;
use Psy\Context;
use Psy\Readline\Interactive\Helper\DebugLog;

/**
 * Completion pipeline coordinator.
 *
 * Each stage focuses on one job: parse the input, refine the context, then
 * collect candidates from applicable sources.
 */
class CompletionEngine
{
    private Context $context;
    private ContextAnalyzer $analyzer;
    private TypeResolver $typeResolver;
    private SymbolCatalog $symbolCatalog;

    /** @var SourceInterface[] */
    private array $sources = [];

    /** @var AnalysisRefinerInterface[] */
    private array $refiners = [];

    public function __construct(Context $context, ?CodeCleaner $cleaner = null, ?SymbolCatalog $symbolCatalog = null)
    {
        $this->context = $context;
        $this->analyzer = new ContextAnalyzer($cleaner);
        $this->typeResolver = new TypeResolver($context, $cleaner);
        $this->symbolCatalog = $symbolCatalog ?? new SymbolCatalog();
        $this->addRefiner(new PartialInputRefiner());
        $this->addRefiner(new CommandSyntaxRefiner());
    }

    /**
     * Register the standard PsySH completion source set.
     *
     * @param SourceInterface[] $additionalSources Pre-initialized sources to include
     */
    public function registerDefaultSources(array $additionalSources = []): void
    {
        // Context-aware sources.
        $this->addSource(new VariableSource($this->context));
        $this->addSource(new ObjectMethodSource());
        $this->addSource(new ObjectPropertySource());

        // Static reflection sources.
        $this->addSource(new MethodSource());
        $this->addSource(new PropertySource());
        $this->addSource(new StaticMethodSource());
        $this->addSource(new StaticPropertySource());
        $this->addSource(new ClassConstantSource());

        // Docblock magic sources.
        $this->addSource(new MagicMethodSource());
        $this->addSource(new MagicPropertySource());

        // Symbol sources (shared symbol catalog snapshot cache).
        $this->addSource(new CatalogSource(CompletionKind::CLASS_NAME, [$this->symbolCatalog, 'getClasses'], $this->symbolCatalog));
        $this->addSource(new CatalogSource(CompletionKind::INTERFACE_NAME, [$this->symbolCatalog, 'getInterfaces'], $this->symbolCatalog));
        $this->addSource(new CatalogSource(CompletionKind::TRAIT_NAME, [$this->symbolCatalog, 'getTraits'], $this->symbolCatalog));
        $this->addSource(new CatalogSource(CompletionKind::ATTRIBUTE_NAME, [$this->symbolCatalog, 'getAttributeClasses'], $this->symbolCatalog));
        $this->addSource(new CatalogSource(CompletionKind::FUNCTION_NAME, [$this->symbolCatalog, 'getFunctions'], $this->symbolCatalog));
        $this->addSource(new CatalogSource(CompletionKind::CONSTANT, [$this->symbolCatalog, 'getConstants'], $this->symbolCatalog));
        $this->addSource(new NamespaceSource($this->symbolCatalog));

        // Additional pre-initialized sources.
        foreach ($additionalSources as $source) {
            $this->addSource($source);
        }

        // Generic sources.
        $this->addSource(new KeywordSource());
    }

    /**
     * Add a completion source.
     */
    public function addSource(SourceInterface $source): void
    {
        if (!\in_array($source, $this->sources, true)) {
            $this->sources[] = $source;
        }
    }

    /**
     * Add an analysis refiner.
     *
     * Refiners translate broad parser output into the narrower completion lanes
     * that sources consume.
     */
    public function addRefiner(AnalysisRefinerInterface $refiner): void
    {
        if (!\in_array($refiner, $this->refiners, true)) {
            $this->refiners[] = $refiner;
        }
    }

    /**
     * Get completions for a normalized request.
     *
     * @return string[]
     */
    public function getCompletions(CompletionRequest $request): array
    {
        $start = \microtime(true);
        DebugLog::log('Completion', 'START', [
            'mode'   => $request->getMode(),
            'input'  => $request->getBuffer(),
            'cursor' => $request->getCursor(),
        ]);

        $analysis = $this->analyzer->analyze(
            $request->getBuffer(),
            $request->getCursor(),
            $request->getReadlineInfo()
        );
        foreach ($this->refiners as $refiner) {
            $analysis = $refiner->refine($analysis);
        }
        DebugLog::log('Completion', 'ANALYZED', [
            'kinds'    => $analysis->kinds,
            'prefix'   => $analysis->prefix,
            'leftSide' => $analysis->leftSide ?? 'null',
        ]);

        $leftSide = $analysis->leftSide;
        if ($leftSide !== null) {
            $leftSideNode = $analysis->leftSideNode;
            $analysis->leftSideTypes = $leftSideNode instanceof AstNode
                ? $this->typeResolver->resolveNodeTypes($leftSideNode, $leftSide)
                : $this->typeResolver->resolveTypes($leftSide);
            $analysis->leftSideValue = $this->typeResolver->resolveValue($leftSide);
            DebugLog::log('Completion', 'RESOLVED_TYPES', [
                'types'     => empty($analysis->leftSideTypes) ? 'none' : \implode('|', $analysis->leftSideTypes),
                'has_value' => $analysis->leftSideValue !== null,
            ]);
        }

        $results = $this->collectFromSources($analysis);
        $results = \array_values(\array_unique(\array_filter($results, fn ($match) => $match !== '' && $match !== null)));

        if ($analysis->prefix !== '' && !empty($results)) {
            $before = \count($results);
            $results = FuzzyMatcher::filter($analysis->prefix, $results);
            DebugLog::log('Completion', 'FUZZY_FILTER', [
                'before' => $before,
                'after'  => \count($results),
            ]);
        }

        $latencyMs = (\microtime(true) - $start) * 1000;
        DebugLog::log('Completion', 'METRICS', [
            'latency_ms' => \round($latencyMs, 2),
            'results'    => \count($results),
        ]);

        return $results;
    }

    /**
     * Collect completions from all applicable sources.
     *
     * @return string[]
     */
    private function collectFromSources(AnalysisResult $analysis): array
    {
        $completions = [];

        foreach ($this->sources as $source) {
            if (!$source->appliesToKind($analysis->kinds)) {
                continue;
            }

            $sourceCompletions = $source->getCompletions($analysis);
            if (!empty($sourceCompletions)) {
                DebugLog::log('Completion', 'SOURCE_MATCHED', [
                    'source' => \get_class($source),
                    'count'  => \count($sourceCompletions),
                ]);

                $completions = \array_merge($completions, $sourceCompletions);
            }
        }

        return $completions;
    }
}
