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

/**
 * PHP keyword completion source.
 *
 * Provides completions for function-like PHP keywords (echo, isset, etc.).
 */
class KeywordSource implements SourceInterface
{
    /** @var string[] */
    private array $keywords = [
        'array',
        'clone',
        'declare',
        'die',
        'echo',
        'empty',
        'eval',
        'exit',
        'fn',
        'include',
        'include_once',
        'isset',
        'list',
        'print',
        'require',
        'require_once',
        'unset',
        'yield',
    ];

    public function __construct()
    {
        if (\PHP_VERSION_ID >= 80000) {
            $this->keywords[] = 'match';
            \sort($this->keywords);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & CompletionKind::KEYWORD) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        return $this->keywords;
    }
}
