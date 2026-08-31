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

use PhpParser\Node;

/**
 * Completion analysis result.
 *
 * Shared state for the completion pipeline.
 *
 * The analyzer establishes the initial context, refiners may narrow it, and
 * later stages reuse the same request metadata and parse-derived hints.
 */
class AnalysisResult
{
    public int $kinds;
    public string $prefix;
    public ?string $leftSide;
    public ?Node $leftSideNode;
    /** @var string[] Fully-qualified class names (supports union types) */
    public array $leftSideTypes;
    public $leftSideValue;
    public string $input;
    /** @var array Tokenized input */
    public array $tokens;
    /** @var array Raw readline callback metadata, if available */
    public array $readlineInfo;
    /** @var bool Whether php-parser successfully parsed the input */
    public bool $parseSucceeded;

    /**
     * @param string|string[]|null $leftSideTypes
     */
    public function __construct(
        int $kinds,
        string $prefix = '',
        ?string $leftSide = null,
        $leftSideTypes = [],
        $leftSideValue = null,
        array $tokens = [],
        string $input = '',
        ?Node $leftSideNode = null,
        array $readlineInfo = [],
        bool $parseSucceeded = false
    ) {
        $this->kinds = $kinds;
        $this->prefix = $prefix;
        $this->leftSide = $leftSide;
        $this->leftSideNode = $leftSideNode;
        $this->leftSideTypes = (array) $leftSideTypes;
        $this->leftSideValue = $leftSideValue;
        $this->tokens = $tokens;
        $this->input = $input;
        $this->readlineInfo = $readlineInfo;
        $this->parseSucceeded = $parseSucceeded;
    }

    /**
     * Return a copy with updated completion context for a later pipeline stage.
     */
    public function withContext(int $kinds, string $prefix = '', ?string $leftSide = null, ?Node $leftSideNode = null): self
    {
        // Types and value are cleared because CompletionEngine re-resolves
        // them after all refiners have run.
        $copy = clone $this;
        $copy->kinds = $kinds;
        $copy->prefix = $prefix;
        $copy->leftSide = $leftSide;
        $copy->leftSideNode = $leftSideNode;
        $copy->leftSideTypes = [];
        $copy->leftSideValue = null;

        return $copy;
    }
}
