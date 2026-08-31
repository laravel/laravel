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

use Psy\Command\Command;
use Psy\CommandArgumentCompletionAware;
use Psy\CommandAware;
use Psy\CommandMapTrait;
use Psy\Completion\AnalysisResult;
use Psy\Completion\CompletionKind;

/**
 * Command positional argument completion source.
 *
 * Delegates argument-tail completion to commands that explicitly opt in.
 */
class CommandArgumentSource implements CommandAware, SourceInterface
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
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & CompletionKind::COMMAND_ARGUMENT) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        $commandName = \is_string($analysis->leftSide) ? $analysis->leftSide : null;
        $command = $commandName !== null ? ($this->commandMap[$commandName] ?? null) : null;

        if (!$command instanceof CommandArgumentCompletionAware) {
            return [];
        }

        // CommandContextRefiner already verified supportsArgumentCompletion()
        // before setting COMMAND_ARGUMENT kind.
        return $command->getArgumentCompletions($analysis);
    }
}
