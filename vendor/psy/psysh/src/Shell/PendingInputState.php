<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Shell;

use Psy\Input\SilentInput;

/**
 * Tracks pending code entered during the current shell session.
 */
class PendingInputState
{
    /** @var string|false */
    private $pendingCode = false;
    private array $pendingCodeBuffer = [];
    private bool $pendingCodeBufferOpen = false;
    private array $pendingCodeStack = [];

    public function hasCode(): bool
    {
        return $this->pendingCodeBuffer !== [];
    }

    public function hasValidCode(): bool
    {
        return $this->hasCode() && !$this->pendingCodeBufferOpen && $this->pendingCode !== false;
    }

    public function pushCurrentCode(): void
    {
        $this->pendingCodeStack[] = [$this->pendingCodeBuffer, $this->pendingCodeBufferOpen, $this->pendingCode];
    }

    public function restorePreviousCode(): void
    {
        $this->clear();

        if ($this->pendingCodeStack === []) {
            return;
        }

        [$this->pendingCodeBuffer, $this->pendingCodeBufferOpen, $this->pendingCode] = \array_pop($this->pendingCodeStack);
    }

    public function clear(): void
    {
        $this->pendingCodeBuffer = [];
        $this->pendingCodeBufferOpen = false;
        $this->pendingCode = false;
    }

    public function appendLine(string $code, bool $silent): string
    {
        $trimmed = \rtrim($code);
        if (\substr($trimmed, -1) === '\\') {
            $this->pendingCodeBufferOpen = true;
            $code = \substr($trimmed, 0, -1);
        } else {
            $this->pendingCodeBufferOpen = false;
        }

        $this->pendingCodeBuffer[] = $silent ? new SilentInput($code) : $code;

        return $code;
    }

    /**
     * @return string|false
     */
    public function getPendingCode()
    {
        return $this->pendingCode;
    }

    /**
     * @param string|false $pendingCode
     */
    public function setPendingCode($pendingCode): void
    {
        $this->pendingCode = $pendingCode;
    }

    public function getPendingCodeBuffer(): array
    {
        return $this->pendingCodeBuffer;
    }
}
