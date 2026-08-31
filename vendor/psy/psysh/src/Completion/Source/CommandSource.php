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
use Psy\CommandAware;
use Psy\Completion\AnalysisResult;
use Psy\Completion\CompletionKind;

/**
 * PsySH command completion source.
 *
 * Provides completions for PsySH commands (e.g., ls, doc, show).
 */
class CommandSource implements CommandAware, SourceInterface
{
    /** @var string[] */
    private array $commandNames = [];

    /**
     * @param Command[] $commands Array of PsySH commands
     */
    public function __construct(array $commands)
    {
        $this->setCommands($commands);
    }

    /**
     * Set commands for completion.
     *
     * @param Command[] $commands
     */
    public function setCommands(array $commands): void
    {
        $names = [];
        foreach ($commands as $command) {
            $names[] = $command->getName();
            foreach ($command->getAliases() as $alias) {
                $names[] = $alias;
            }
        }

        \sort($names);
        $this->commandNames = $names;
    }

    /**
     * {@inheritdoc}
     */
    public function appliesToKind(int $kinds): bool
    {
        return ($kinds & CompletionKind::COMMAND) !== 0;
    }

    /**
     * {@inheritdoc}
     */
    public function getCompletions(AnalysisResult $analysis): array
    {
        return $this->commandNames;
    }
}
