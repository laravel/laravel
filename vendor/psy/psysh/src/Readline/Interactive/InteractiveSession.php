<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive;

/**
 * Owns terminal lifecycle for interactive readline sessions.
 */
class InteractiveSession
{
    private Terminal $terminal;
    private bool $active = false;
    private bool $shutdownRegistered = false;
    private bool $bracketedPasteEnabled = false;

    public function __construct(Terminal $terminal)
    {
        $this->terminal = $terminal;
    }

    /**
     * Start interactive terminal lifecycle.
     *
     * @throws \RuntimeException if raw mode cannot be enabled
     */
    public function start(): void
    {
        if ($this->active) {
            return;
        }

        $this->registerShutdownHandler();
        if (!$this->terminal->enableRawMode()) {
            throw new \RuntimeException('Unable to enable raw mode for interactive readline.');
        }

        if ($this->bracketedPasteEnabled) {
            $this->terminal->enableBracketedPaste();
        }

        $this->active = true;
    }

    /**
     * Stop interactive terminal lifecycle.
     */
    public function stop(): void
    {
        if (!$this->active) {
            return;
        }

        if ($this->terminal->isBracketedPasteEnabled()) {
            $this->terminal->disableBracketedPaste();
        }

        if ($this->terminal->isMouseReportingEnabled()) {
            $this->terminal->disableMouseReporting();
        }

        if ($this->terminal->isAltScreenEnabled()) {
            $this->terminal->disableAltScreen();
        }

        $this->terminal->disableRawMode();
        $this->active = false;
    }

    /**
     * Enable or disable bracketed paste mode for the active session.
     */
    public function setUseBracketedPaste(bool $enabled): void
    {
        $this->bracketedPasteEnabled = $enabled;

        if (!$this->active) {
            return;
        }

        if ($enabled) {
            $this->terminal->enableBracketedPaste();
        } else {
            $this->terminal->disableBracketedPaste();
        }
    }

    /**
     * Determine whether the session is active.
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Register a shutdown function to ensure the terminal is restored on exit.
     */
    private function registerShutdownHandler(): void
    {
        if ($this->shutdownRegistered) {
            return;
        }

        \register_shutdown_function([$this, 'stop']);
        $this->shutdownRegistered = true;
    }

    /**
     * Stop the session on destruction.
     */
    public function __destruct()
    {
        $this->stop();
    }
}
