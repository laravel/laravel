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

use PhpParser\NodeTraverser;
use PhpParser\Parser;
use PhpParser\PrettyPrinter;
use Psy\ConfigPaths;
use Psy\Exception\ParseErrorException;
use Psy\OutputAware;
use Psy\ParserFactory;
use Psy\Shell;
use Psy\Util\DependencyChecker;
use Psy\Util\Docblock;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * A uopz-based code reloader for modern PHP.
 *
 * This reloader uses the uopz extension to dynamically reload modified files
 * without restarting the REPL session. It parses changed files and uses uopz
 * functions to override methods, functions, and constants.
 *
 * Reload flow:
 *   1. On each input, check included files for timestamp changes
 *   2. Parse modified files and reload safe elements (methods, unconditional functions)
 *   3. Skip unsafe elements (conditional functions/constants) and track in skippedFiles
 *   4. When `yolo` command enables force-reload, re-process skipped files with
 *      safety checks bypassed
 *
 * Known limitations:
 *  - Cannot add/remove class properties
 *  - Cannot change class inheritance or interfaces
 *  - Cannot change method signatures (parameter types/counts)
 *
 * However, it can:
 *  - Reload method implementations (including private/protected)
 *  - Reload function implementations
 *  - Reload class and global constants
 *  - Add new methods and functions
 */
class UopzReloader extends AbstractListener implements OutputAware
{
    private Parser $parser;
    private PrettyPrinter\Standard $printer;
    private ?OutputInterface $output = null;
    private ?Shell $shell = null;

    /** @var array<string, int> File path => last processed timestamp */
    private array $timestamps = [];

    /**
     * File paths with skipped elements, awaiting force-reload via yolo.
     *
     * @var array<string, int> File path => last processed timestamp
     */
    private array $skippedFiles = [];

    /** @var bool Whether to bypass safety warnings (set by yolo command) */
    private bool $forceReload = false;

    /**
     * Only enabled if uopz extension is installed with required functions.
     *
     * Requires uopz 5.0+ which provides uopz_set_return() and uopz_redefine().
     */
    public static function isSupported(): bool
    {
        return \extension_loaded('uopz') && DependencyChecker::functionsAvailable([
            'uopz_set_return',
            'uopz_redefine',
            'uopz_unset_return',
            'uopz_undefine',
        ]);
    }

    /**
     * Construct a Uopz Reloader.
     */
    public function __construct()
    {
        $this->parser = (new ParserFactory())->createParser();
        $this->printer = new PrettyPrinter\Standard();
    }

    /**
     * {@inheritdoc}
     */
    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    /**
     * Enable or disable force-reload mode.
     *
     * When enabled, safety checks are bypassed and any pending skipped files
     * are immediately re-processed.
     */
    public function setForceReload(bool $force)
    {
        $this->forceReload = $force;

        // Re-process any skipped files now that force-reload is enabled
        if ($force && !empty($this->skippedFiles) && $this->shell !== null) {
            $this->reloadSkippedFiles();
        }
    }

    /**
     * Re-process files that were previously skipped.
     */
    private function reloadSkippedFiles(): void
    {
        $files = $this->skippedFiles;
        $this->skippedFiles = [];

        if (\count($files) === 1) {
            $this->writeInfo(\sprintf('YOLO: Force-reloading %s', ConfigPaths::prettyPath(\array_key_first($files))));
        } else {
            $this->writeInfo(\sprintf('YOLO: Force-reloading %d files', \count($files)));
        }

        foreach ($files as $file => $timestamp) {
            $this->reloadFile($file);
            $this->timestamps[$file] = $timestamp;
        }
    }

    /**
     * Reload code on input.
     */
    public function onInput(Shell $shell, string $input)
    {
        $this->shell = $shell;
        $this->reload();

        return null;
    }

    /**
     * Look through included files and update anything with a new timestamp.
     */
    private function reload(): void
    {
        \clearstatcache();
        $modified = [];

        foreach (\get_included_files() as $file) {
            // Skip files that no longer exist
            if (!\file_exists($file)) {
                continue;
            }

            $timestamp = \filemtime($file);

            if (!isset($this->timestamps[$file])) {
                $this->timestamps[$file] = $timestamp;
                continue;
            }

            if ($this->timestamps[$file] === $timestamp) {
                continue;
            }

            if (!$this->lintFile($file)) {
                $this->writeError(\sprintf('Modified file "%s" has syntax errors and cannot be reloaded', ConfigPaths::prettyPath($file)));
                continue;
            }

            $modified[$file] = $timestamp;
        }

        if (\count($modified) === 0) {
            return;
        }

        // Clear magic method/property cache since docblocks may have changed
        Docblock::clearMagicCache();

        // Notify user about reload attempts
        if ($this->forceReload) {
            if (\count($modified) === 1) {
                $this->writeInfo(\sprintf('YOLO: Force-reloading %s', ConfigPaths::prettyPath(\array_key_first($modified))));
            } else {
                $this->writeInfo(\sprintf('YOLO: Force-reloading %d files', \count($modified)));
            }
        } else {
            if (\count($modified) === 1) {
                $this->writeInfo(\sprintf('Reloading %s', ConfigPaths::prettyPath(\array_key_first($modified))));
            } else {
                $this->writeInfo(\sprintf('Reloading %d files', \count($modified)));
            }
        }

        foreach ($modified as $file => $timestamp) {
            $hadSkips = $this->reloadFile($file);
            $this->timestamps[$file] = $timestamp;
            if ($hadSkips) {
                // Track for later force-reload via yolo
                $this->skippedFiles[$file] = $timestamp;
            } else {
                unset($this->skippedFiles[$file]);
            }
        }
    }

    /**
     * Reload a single file by parsing it and applying uopz overrides.
     *
     * @return bool True if any elements were skipped (need yolo to force)
     */
    private function reloadFile(string $file): bool
    {
        try {
            $code = \file_get_contents($file);
            $ast = $this->parser->parse($code);

            if ($ast === null) {
                return false;
            }

            $traverser = new NodeTraverser();
            $reloader = new UopzReloaderVisitor($this->printer, $this->forceReload);
            $traverser->addVisitor($reloader);
            $traverser->traverse($ast);

            // Check if there were any warnings about limitations
            if ($reloader->hasWarnings()) {
                foreach ($reloader->getWarnings() as $warning) {
                    $this->writeWarning($warning);
                }
            }

            return $reloader->hasSkips();
        } catch (\Throwable $e) {
            $this->writeError(\sprintf('Failed to reload %s: %s', ConfigPaths::prettyPath($file), $e->getMessage()));

            return false;
        }
    }

    /**
     * Write an info message.
     */
    private function writeInfo(string $message): void
    {
        if ($this->output) {
            $this->output->writeln(\sprintf('<whisper>%s</whisper>', $message));
        }
    }

    /**
     * Write a warning message.
     */
    private function writeWarning(string $message): void
    {
        if ($this->output) {
            $this->output->writeln(\sprintf('<comment>Warning: %s</comment>', $message));
        }
    }

    /**
     * Write an error message using shell exception handling.
     */
    private function writeError(string $message): void
    {
        if ($this->shell) {
            try {
                $this->shell->writeException(new ParseErrorException($message));

                return;
            } catch (\Throwable $e) {
                // Shell not fully initialized, fall back to output
            }
        }

        if ($this->output) {
            $this->output->writeln(\sprintf('<error>Error: %s</error>', $message));
        }
    }

    /**
     * Check if file has valid PHP syntax.
     */
    private function lintFile(string $file): bool
    {
        try {
            $this->parser->parse(\file_get_contents($file));
        } catch (\Throwable $e) {
            return false;
        }

        return true;
    }
}
