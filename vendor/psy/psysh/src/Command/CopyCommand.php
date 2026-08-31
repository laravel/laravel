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

use Psy\Clipboard\ClipboardMethod;
use Psy\Clipboard\NullClipboardMethod;
use Psy\Configuration;
use Psy\Input\CodeArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Copy a value to the clipboard.
 */
class CopyCommand extends ReflectingCommand
{
    private ?Configuration $config = null;

    /**
     * Set the configuration instance.
     *
     * @param Configuration $config
     */
    public function setConfiguration(Configuration $config)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure(): void
    {
        $this
            ->setName('copy')
            ->setDefinition([
                new CodeArgument('expression', CodeArgument::OPTIONAL, 'Expression to copy.'),
            ])
            ->setDescription('Copy a value to the clipboard.')
            ->setHelp(
                <<<'HELP'
                Copy a value to the clipboard.

                When given:
                - an expression, copy the exported value of the expression to the clipboard.
                - no arguments, copy the last evaluated result (<info>$_</info>) to the clipboard.

                e.g.
                <return>>>> copy new Foo()</return>
                <return>>>> copy User::all()->toArray()</return>
                <return>>>> copy</return>
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
        $expression = $input->getArgument('expression');
        $value = $expression === null ? $this->context->get('_') : $this->resolveCode($expression);

        if (\is_object($value)) {
            $this->setCommandScopeVariables(new \ReflectionObject($value));
        }

        if (!$this->getClipboardMethod()->copy($this->exportValue($value, $output), $output)) {
            $output->writeln('<error>Unable to copy value to clipboard.</error>');

            return 1;
        }

        $output->writeln('<info>Copied to clipboard.</info>');

        return 0;
    }

    private function getClipboardMethod(): ClipboardMethod
    {
        return $this->config ? $this->config->getClipboard() : new NullClipboardMethod(false);
    }

    private function exportValue($value, OutputInterface $output): string
    {
        $export = '';
        $warnings = [];
        \set_error_handler(static function (int $errno, string $errstr) use (&$warnings): bool {
            $warnings[$errstr] = true;

            return true;
        });

        try {
            $export = (string) \var_export($value, true);
        } finally {
            \restore_error_handler();
        }

        foreach (\array_keys($warnings) as $warning) {
            if ($warning === 'var_export does not handle circular references') {
                $output->writeln('<warning>Value contains circular references; copied export may be incomplete.</warning>');

                break;
            }

            $output->writeln(\sprintf('<warning>%s</warning>', $warning));

            break;
        }

        return $export;
    }
}
