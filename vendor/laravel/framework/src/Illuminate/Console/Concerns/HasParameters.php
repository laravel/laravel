<?php

namespace Illuminate\Console\Concerns;

use Symfony\Component\Console\Completion\CompletionInput;
use Symfony\Component\Console\Completion\CompletionSuggestions;
use Symfony\Component\Console\Completion\Suggestion;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

trait HasParameters
{
    /**
     * Specify the arguments and options on the command.
     *
     * @return void
     */
    protected function specifyParameters()
    {
        // We will loop through all of the arguments and options for the command and
        // set them all on the base command instance. This specifies what can get
        // passed into these commands as "parameters" to control the execution.
        foreach ($this->getArguments() as $arguments) {
            if ($arguments instanceof InputArgument) {
                $this->getDefinition()->addArgument($arguments);
            } else {
                $this->addArgument(...$arguments);
            }
        }

        foreach ($this->getOptions() as $options) {
            if ($options instanceof InputOption) {
                $this->getDefinition()->addOption($options);
            } else {
                $this->addOption(...$options);
            }
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return (InputArgument|array{
     *    0: non-empty-string,
     *    1?: int-mask-of<InputArgument::REQUIRED|InputArgument::OPTIONAL|InputArgument::IS_ARRAY>,
     *    2?: string,
     *    3?: mixed,
     *    4?: list<string|Suggestion>|\Closure(CompletionInput, CompletionSuggestions): list<string|Suggestion>
     * })[]
     */
    protected function getArguments()
    {
        return [];
    }

    /**
     * Get the console command options.
     *
     * @return (InputOption|array{
     *    0: non-empty-string,
     *    1?: string|non-empty-array<string>,
     *    2?: int-mask-of<InputOption::VALUE_*>,
     *    3?: string,
     *    4?: mixed,
     *    5?: list<string|Suggestion>|\Closure(CompletionInput, CompletionSuggestions): list<string|Suggestion>
     * })[]
     */
    protected function getOptions()
    {
        return [];
    }
}
