<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\ExecutionLoop;

use PhpParser\Parser;
use Psy\ConfigPaths;
use Psy\Exception\ParseErrorException;
use Psy\OutputAware;
use Psy\ParserFactory;
use Psy\Shell;
use Psy\Util\Docblock;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A runkit-based code reloader, which is pretty much magic.
 *
 * @todo Remove RunkitReloader once we drop support for PHP 7.x :(
 */
class RunkitReloader extends AbstractListener implements OutputAware
{
    private Parser $parser;
    private ?OutputInterface $output = null;
    private array $timestamps = [];

    /**
     * Only enabled if Runkit is installed.
     */
    public static function isSupported(): bool
    {
        // runkit_import was removed in runkit7-4.0.0a1
        return \extension_loaded('runkit') || \extension_loaded('runkit7') && \function_exists('runkit_import');
    }

    /**
     * Construct a Runkit Reloader.
     */
    public function __construct()
    {
        $this->parser = (new ParserFactory())->createParser();
    }

    /**
     * {@inheritdoc}
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Reload code on input.
     */
    public function onInput(Shell $shell, string $input)
    {
        $this->reload($shell);

        return null;
    }

    /**
     * Look through included files and update anything with a new timestamp.
     */
    private function reload(Shell $shell)
    {
        \clearstatcache();
        $modified = [];

        foreach (\get_included_files() as $file) {
            $timestamp = \filemtime($file);

            if (!isset($this->timestamps[$file])) {
                $this->timestamps[$file] = $timestamp;
                continue;
            }

            if ($this->timestamps[$file] === $timestamp) {
                continue;
            }

            if (!$this->lintFile($file)) {
                $msg = \sprintf('Modified file "%s" could not be reloaded', $file);
                $shell->writeException(new ParseErrorException($msg));
                continue;
            }

            $modified[] = $file;
            $this->timestamps[$file] = $timestamp;
        }

        if (\count($modified) === 0) {
            return;
        }

        // Clear magic method/property cache since docblocks may have changed
        Docblock::clearMagicCache();

        // Notify user about reload attempts
        if ($this->output) {
            if (\count($modified) === 1) {
                $this->output->writeln(\sprintf('<whisper>Reloading %s</whisper>', ConfigPaths::prettyPath($modified[0])));
            } else {
                $this->output->writeln(\sprintf('<whisper>Reloading %d files</whisper>', \count($modified)));
            }
        }

        foreach ($modified as $file) {
            $flags = (
                RUNKIT_IMPORT_FUNCTIONS |
                RUNKIT_IMPORT_CLASSES |
                RUNKIT_IMPORT_CLASS_METHODS |
                RUNKIT_IMPORT_CLASS_CONSTS |
                RUNKIT_IMPORT_CLASS_PROPS |
                RUNKIT_IMPORT_OVERRIDE
            );

            // these two const cannot be used with RUNKIT_IMPORT_OVERRIDE  in runkit7
            if (\extension_loaded('runkit7')) {
                $flags &= ~RUNKIT_IMPORT_CLASS_PROPS & ~RUNKIT_IMPORT_CLASS_STATIC_PROPS;
                runkit7_import($file, $flags);
            } else {
                runkit_import($file, $flags);
            }
        }
    }

    /**
     * Check if file has valid PHP syntax.
     */
    private function lintFile(string $file): bool
    {
        // first try to parse it
        try {
            $this->parser->parse(\file_get_contents($file));
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }
}
