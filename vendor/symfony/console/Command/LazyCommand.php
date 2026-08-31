<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Nicolas Grekas <p@tchwork.com>
 */
final class LazyCommand extends Command
{
    private \Closure|Command $command;

    public function __construct(
        string $name,
        array $aliases,
        string $description,
        bool $isHidden,
        \Closure $commandFactory,
        private ?bool $isEnabled = true,
    ) {
        $this->setName($name)
            ->setAliases($aliases)
            ->setHidden($isHidden)
            ->setDescription($description);

        $this->command = $commandFactory;
    }

    public function ignoreValidationErrors(): void
    {
        $this->getCommand()->ignoreValidationErrors();
    }

    public function setApplication(?Application $application): void
    {
        if ($this->command instanceof parent) {
            $this->command->setApplication($application);
        }

        parent::setApplication($application);
    }

    public function setHelperSet(HelperSet $helperSet): void
    {
        if ($this->command instanceof parent) {
            $this->command->setHelperSet($helperSet);
        }

        parent::setHelperSet($helperSet);
    }

    public function isEnabled(): bool
    {
        return $this->isEnabled ?? $this->getCommand()->isEnabled();
    }

    public function run(InputInterface $input, OutputInterface $output): int
    {
        return $this->getCommand()->run($input, $output);
    }

    public function complete(CompletionInput $input, CompletionSuggestions $suggestions): void
    {
        $this->getCommand()->complete($input, $suggestions);
    }

    public function setCode(callable $code): static
    {
        $this->getCommand()->setCode($code);

        return $this;
    }

    /**
     * @internal
     */
    public function mergeApplicationDefinition(bool $mergeArgs = true): void
    {
        $this->getCommand()->mergeApplicationDefinition($mergeArgs);
    }

    public function setDefinition(array|InputDefinition $definition): static
    {
        $this->getCommand()->setDefinition($definition);

        return $this;
    }

    public function getDefinition(): InputDefinition
    {
        return $this->getCommand()->getDefinition();
    }

    public function getNativeDefinition(): InputDefinition
    {
        return $this->getCommand()->getNativeDefinition();
    }

    /**
     * @param array|\Closure(CompletionInput,CompletionSuggestions):list<string|Suggestion> $suggestedValues The values used for input completion
     */
    public function addArgument(string $name, ?int $mode = null, string $description = '', mixed $default = null, array|\Closure $suggestedValues = []): static
    {
        $this->getCommand()->addArgument($name, $mode, $description, $default, $suggestedValues);

        return $this;
    }

    /**
     * @param array|\Closure(CompletionInput,CompletionSuggestions):list<string|Suggestion> $suggestedValues The values used for input completion
     */
    public function addOption(string $name, string|array|null $shortcut = null, ?int $mode = null, string $description = '', mixed $default = null, array|\Closure $suggestedValues = []): static
    {
        $this->getCommand()->addOption($name, $shortcut, $mode, $description, $default, $suggestedValues);

        return $this;
    }

    public function setProcessTitle(string $title): static
    {
        $this->getCommand()->setProcessTitle($title);

        return $this;
    }

    public function setHelp(string $help): static
    {
        $this->getCommand()->setHelp($help);

        return $this;
    }

    public function getHelp(): string
    {
        return $this->getCommand()->getHelp();
    }

    public function getProcessedHelp(): string
    {
        return $this->getCommand()->getProcessedHelp();
    }

    public function getSynopsis(bool $short = false): string
    {
        return $this->getCommand()->getSynopsis($short);
    }

    public function addUsage(string $usage): static
    {
        $this->getCommand()->addUsage($usage);

        return $this;
    }

    public function getUsages(): array
    {
        return $this->getCommand()->getUsages();
    }

    public function getHelper(string $name): HelperInterface
    {
        return $this->getCommand()->getHelper($name);
    }

    public function getCommand(): parent
    {
        if (!$this->command instanceof \Closure) {
            return $this->command;
        }

        $command = $this->command = ($this->command)();
        $command->setApplication($this->getApplication());

        if (null !== $this->getHelperSet()) {
            $command->setHelperSet($this->getHelperSet());
        }

        $command->setName($this->getName())
            ->setAliases($this->getAliases())
            ->setHidden($this->isHidden())
            ->setDescription($this->getDescription());

        // Will throw if the command is not correctly initialized.
        $command->getDefinition();

        return $command;
    }
}
