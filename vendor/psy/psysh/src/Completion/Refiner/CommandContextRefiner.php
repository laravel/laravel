<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Completion\Refiner;

use Psy\Command\Command;
use Psy\CommandArgumentCompletionAware;
use Psy\CommandAware;
use Psy\CommandMapTrait;
use Psy\Completion\AnalysisResult;
use Psy\Completion\CompletionKind;

/**
 * Hands command tails to command-owned completion once shell syntax is known.
 *
 * It resolves which command owns the current tail so argument completion can
 * follow command-specific rules and vocabulary.
 */
class CommandContextRefiner implements AnalysisRefinerInterface, CommandAware
{
    use CommandMapTrait;

    /**
     * @param Command[] $commands Array of PsySH commands
     */
    public function __construct(array $commands)
    {
        $this->setCommands($commands);
    }

    /**
     * {@inheritdoc}
     */
    public function refine(AnalysisResult $analysis): AnalysisResult
    {
        if (!$this->supportsRefinement($analysis)) {
            return $analysis;
        }

        if (!\preg_match('/^\s*([^\s]+)(\s+.*)$/s', $analysis->input, $matches)) {
            return $analysis;
        }

        $commandName = $matches[1];
        $command = $this->commandMap[$commandName] ?? null;

        if (!$command instanceof CommandArgumentCompletionAware) {
            return $analysis;
        }

        if (!$command->supportsArgumentCompletion($analysis)) {
            return $analysis;
        }

        return $analysis->withContext(CompletionKind::COMMAND_ARGUMENT, $analysis->prefix, $commandName);
    }

    private function supportsRefinement(AnalysisResult $analysis): bool
    {
        if (($analysis->kinds & CompletionKind::COMMAND_OPTION) !== 0) {
            return false;
        }

        return ($analysis->kinds & CompletionKind::COMMAND_ELIGIBLE) !== 0;
    }
}
