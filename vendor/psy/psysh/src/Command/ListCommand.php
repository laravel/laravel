<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command;

use Psy\Command\ListCommand\ClassConstantEnumerator;
use Psy\Command\ListCommand\ClassEnumerator;
use Psy\Command\ListCommand\ConstantEnumerator;
use Psy\Command\ListCommand\FunctionEnumerator;
use Psy\Command\ListCommand\GlobalVariableEnumerator;
use Psy\Command\ListCommand\MethodEnumerator;
use Psy\Command\ListCommand\PropertyEnumerator;
use Psy\Command\ListCommand\VariableEnumerator;
use Psy\Exception\RuntimeException;
use Psy\Input\CodeArgument;
use Psy\Input\FilterOptions;
use Psy\VarDumper\Presenter;
use Psy\VarDumper\PresenterAware;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * List available local variables, object properties, etc.
 */
class ListCommand extends ReflectingCommand implements PresenterAware
{
    protected Presenter $presenter;
    protected array $enumerators;

    /**
     * PresenterAware interface.
     *
     * @param Presenter $presenter
     */
    public function setPresenter(Presenter $presenter)
    {
        $this->presenter = $presenter;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        list($grep, $insensitive, $invert) = FilterOptions::getOptions();

        $this
            ->setName('ls')
            ->setAliases(['dir'])
            ->setDefinition([
                new CodeArgument('target', CodeArgument::OPTIONAL, 'A target class or object to list.'),

                new InputOption('vars', '', InputOption::VALUE_NONE, 'Display variables.'),
                new InputOption('constants', 'c', InputOption::VALUE_NONE, 'Display defined constants.'),
                new InputOption('functions', 'f', InputOption::VALUE_NONE, 'Display defined functions.'),
                new InputOption('classes', 'k', InputOption::VALUE_NONE, 'Display declared classes.'),
                new InputOption('interfaces', 'I', InputOption::VALUE_NONE, 'Display declared interfaces.'),
                new InputOption('traits', 't', InputOption::VALUE_NONE, 'Display declared traits.'),

                new InputOption('no-inherit', '', InputOption::VALUE_NONE, 'Exclude inherited methods, properties and constants.'),

                new InputOption('properties', 'p', InputOption::VALUE_NONE, 'Display class or object properties (public properties by default).'),
                new InputOption('methods', 'm', InputOption::VALUE_NONE, 'Display class or object methods (public methods by default).'),

                $grep,
                $insensitive,
                $invert,

                new InputOption('globals', 'g', InputOption::VALUE_NONE, 'Include global variables.'),
                new InputOption('internal', 'n', InputOption::VALUE_NONE, 'Limit to internal functions and classes.'),
                new InputOption('user', 'u', InputOption::VALUE_NONE, 'Limit to user-defined constants, functions and classes.'),
                new InputOption('category', 'C', InputOption::VALUE_REQUIRED, 'Limit to constants in a specific category (e.g. "date").'),

                new InputOption('all', 'a', InputOption::VALUE_NONE, 'Include private and protected methods and properties.'),
                new InputOption('long', 'l', InputOption::VALUE_NONE, 'List in long format: includes class names and method signatures.'),
            ])
            ->setDescription('List local, instance or class variables, methods and constants.')
            ->setHelp(
                <<<'HELP'
List variables, constants, classes, interfaces, traits, functions, methods,
and properties.

Called without options, this will return a list of variables currently in scope.

If a target object is provided, list properties, constants and methods of that
target. If a class, interface or trait name is passed instead, list constants
and methods on that class.

e.g.
<return>>>> ls</return>
<return>>>> ls $foo</return>
<return>>>> ls -k --grep mongo -i</return>
<return>>>> ls -al ReflectionClass</return>
<return>>>> ls --constants --category date</return>
<return>>>> ls -l --functions --grep /^array_.*/</return>
<return>>>> ls -l --properties new DateTime()</return>
HELP
            );
    }

    /**
     * {@inheritdoc}
     *
     * @return int 0 if everything went fine, or an exit code
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->validateInput($input);
        $this->initEnumerators();
        $shellOutput = $this->shellOutput($output);

        $method = $input->getOption('long') ? 'writeLong' : 'write';

        if ($target = $input->getArgument('target')) {
            list($target, $reflector) = $this->getTargetAndReflector($target, $output);
        } else {
            $reflector = null;
        }

        if ($input->getOption('long')) {
            $shellOutput->startPaging();
        }

        foreach ($this->enumerators as $enumerator) {
            $this->$method($output, $enumerator->enumerate($input, $reflector, $target));
        }

        if ($input->getOption('long')) {
            $shellOutput->stopPaging();
        }

        // Set some magic local variables
        if ($reflector !== null) {
            $this->setCommandScopeVariables($reflector);
        }

        return 0;
    }

    /**
     * Initialize Enumerators.
     */
    protected function initEnumerators()
    {
        if (!isset($this->enumerators)) {
            $mgr = $this->presenter;

            $this->enumerators = [
                new ClassConstantEnumerator($mgr),
                new ClassEnumerator($mgr),
                new ConstantEnumerator($mgr),
                new FunctionEnumerator($mgr),
                new GlobalVariableEnumerator($mgr),
                new PropertyEnumerator($mgr),
                new MethodEnumerator($mgr),
                new VariableEnumerator($mgr, $this->context),
            ];
        }
    }

    /**
     * Write the list items to $output.
     *
     * @param OutputInterface $output
     * @param array           $result List of enumerated items
     */
    protected function write(OutputInterface $output, array $result)
    {
        if (\count($result) === 0) {
            return;
        }

        $formatter = $output->getFormatter();

        foreach ($result as $label => $items) {
            // Pre-format each item individually to avoid O(n^2) performance
            // in Symfony's OutputFormatter when processing large strings with many style tags.
            $names = \array_map(fn ($item) => $formatter->format($this->formatItemName($item)), $items);

            // Pre-format the label and join with pre-formatted names
            $line = $formatter->format(\sprintf('<strong>%s</strong>: ', $label)).\implode(', ', $names);

            // Write raw since we've already formatted everything
            $output->writeln($line, OutputInterface::OUTPUT_RAW);
        }
    }

    /**
     * Write the list items to $output.
     *
     * Items are listed one per line, and include the item signature.
     *
     * @param OutputInterface $output
     * @param array           $result List of enumerated items
     */
    protected function writeLong(OutputInterface $output, array $result)
    {
        if (\count($result) === 0) {
            return;
        }

        $table = $this->getTable($output);
        $first = true;

        foreach ($result as $label => $items) {
            if (!$first) {
                $output->writeln('');
            }
            $output->writeln(\sprintf('<strong>%s:</strong>', $label));

            $table->setRows([]);
            foreach ($items as $item) {
                $table->addRow([$this->formatItemName($item), $item['value']]);
            }

            $table->render();
            $first = false;
        }
    }

    /**
     * Format an item name given its visibility.
     *
     * @param array $item
     */
    private function formatItemName(array $item): string
    {
        return \sprintf('<%s>%s</%s>', $item['style'], OutputFormatter::escape($item['name']), $item['style']);
    }

    /**
     * Validate that input options make sense, provide defaults when called without options.
     *
     * @throws RuntimeException if options are inconsistent
     *
     * @param InputInterface $input
     */
    private function validateInput(InputInterface $input)
    {
        if (!$input->getArgument('target')) {
            // if no target is passed, there can be no properties or methods
            foreach (['properties', 'methods', 'no-inherit'] as $option) {
                if ($input->getOption($option)) {
                    throw new RuntimeException('--'.$option.' does not make sense without a specified target');
                }
            }

            foreach (['globals', 'vars', 'constants', 'functions', 'classes', 'interfaces', 'traits'] as $option) {
                if ($input->getOption($option)) {
                    return;
                }
            }

            // default to --vars if no other options are passed
            $input->setOption('vars', true);
        } else {
            // if a target is passed, classes, functions, etc don't make sense
            foreach (['vars', 'globals'] as $option) {
                if ($input->getOption($option)) {
                    throw new RuntimeException('--'.$option.' does not make sense with a specified target');
                }
            }

            // @todo ensure that 'functions', 'classes', 'interfaces', 'traits' only accept namespace target?
            foreach (['constants', 'properties', 'methods', 'functions', 'classes', 'interfaces', 'traits'] as $option) {
                if ($input->getOption($option)) {
                    return;
                }
            }

            // default to --constants --properties --methods if no other options are passed
            $input->setOption('constants', true);
            $input->setOption('properties', true);
            $input->setOption('methods', true);
        }
    }
}
