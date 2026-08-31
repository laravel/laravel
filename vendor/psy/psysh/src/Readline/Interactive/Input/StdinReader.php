<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Readline\Interactive\Input;

/**
 * Read characters from stdin.
 */
class StdinReader
{
    /** @var resource */
    private $stream;

    /**
     * @param resource $stream
     */
    public function __construct($stream = \STDIN)
    {
        $this->stream = $stream;
    }

    /**
     * Check if there's pending input (useful for paste detection).
     */
    public function hasPendingInput(): bool
    {
        // Memory streams don't support stream_select.
        $meta = \stream_get_meta_data($this->stream);
        if (isset($meta['stream_type']) && $meta['stream_type'] === 'MEMORY') {
            return false;
        }

        \stream_set_blocking($this->stream, false);

        $read = [$this->stream];
        $write = null;
        $except = null;

        $ready = @\stream_select($read, $write, $except, 0, 0);

        \stream_set_blocking($this->stream, true);

        return $ready > 0;
    }

    /**
     * Read all available input (used for handling pastes).
     *
     * @return string The pasted content
     */
    public function readPastedContent(): string
    {
        // Memory streams don't support stream_select.
        $meta = \stream_get_meta_data($this->stream);
        if (isset($meta['stream_type']) && $meta['stream_type'] === 'MEMORY') {
            $content = '';
            while (($char = \fgetc($this->stream)) !== false) {
                $content .= $char;
            }

            return $content;
        }

        \stream_set_blocking($this->stream, false);

        $content = '';
        $timeout = 50000;
        $start = \microtime(true);

        while ((\microtime(true) - $start) < ($timeout / 1000000)) {
            $char = \fgetc($this->stream);

            if ($char === false) {
                $read = [$this->stream];
                $write = null;
                $except = null;
                if (@\stream_select($read, $write, $except, 0, 1000) > 0) {
                    continue;
                }
                break;
            }

            $content .= $char;
        }

        \stream_set_blocking($this->stream, true);

        return $content;
    }

    /**
     * Read a single key press from the input stream.
     */
    public function readKey(): Key
    {
        $char = \fgetc($this->stream);

        if ($char === false) {
            return new Key('', Key::TYPE_EOF);
        }

        // Escape sequences must be handled before paste detection.
        if ($char === "\033") {
            $escapeKey = $this->readEscapeSequence($char);

            if ($escapeKey->getValue() === "\033[200~") {
                return $this->readBracketedPaste();
            }

            // SGR mouse reports (\033[<button;col;row{M,m}) are reshaped
            // into TYPE_MOUSE keys with a friendly value (e.g. 'wheel-up').
            if (\preg_match('/^\033\[<(\d+);\d+;\d+([Mm])$/', $escapeKey->getValue(), $m)) {
                $value = $this->mouseValue((int) $m[1], $m[2]);
                if ($value !== null) {
                    return new Key($value, Key::TYPE_MOUSE);
                }
            }

            return $escapeKey;
        }

        if ($this->hasPendingInput()) {
            $pastedContent = $char.$this->readPastedContent();

            // No ungetc support in PHP, so return any buffered content as a paste.
            if (\strlen($pastedContent) > 1) {
                return new Key($pastedContent, Key::TYPE_PASTE);
            }
        }

        // CR/LF are handled as regular chars, not control characters.
        $ord = \ord($char);
        if ($ord < 32 && $char !== "\n" && $char !== "\r") {
            return new Key($char, Key::TYPE_CONTROL);
        }

        if ($ord === 127) {
            return new Key($char, Key::TYPE_CONTROL);
        }

        return new Key($char, Key::TYPE_CHAR);
    }

    /**
     * Map an SGR mouse button code + event letter into a friendly Key value
     * like 'wheel-up'. Modifier bits in the high nibble are ignored. Returns
     * null for buttons we don't care to surface (motion-only reports, etc.).
     */
    private function mouseValue(int $button, string $event): ?string
    {
        // Mask off the modifier (shift/meta/ctrl) and motion bits.
        // Wheel events live in 64..67; primary buttons in 0..2.
        $base = $button & 0b11000011;

        if ($event === 'M') {
            switch ($base) {
                case 0:  return 'press-left';
                case 1:  return 'press-middle';
                case 2:  return 'press-right';
                case 64: return 'wheel-up';
                case 65: return 'wheel-down';
                case 66: return 'wheel-left';
                case 67: return 'wheel-right';
            }
        } elseif ($event === 'm') {
            // SGR releases ('m') only fire for primary buttons; wheel events
            // never release. We don't currently care about releases.
            return null;
        }

        return null;
    }

    /**
     * Read an escape sequence from the input stream.
     */
    protected function readEscapeSequence(string $prefix): Key
    {
        \stream_set_blocking($this->stream, false);

        $sequence = $prefix;
        $timeout = 100000;
        $start = \microtime(true);

        while ((\microtime(true) - $start) < ($timeout / 1000000)) {
            $char = \fgetc($this->stream);

            if ($char === false) {
                \usleep(1000); // 1ms
                continue;
            }

            $sequence .= $char;

            if ($this->isCompleteEscapeSequence($sequence)) {
                break;
            }

            if (\strlen($sequence) > 32) {
                break;
            }
        }

        \stream_set_blocking($this->stream, true);

        return new Key($sequence, Key::TYPE_ESCAPE);
    }

    /**
     * Check if an escape sequence is complete.
     */
    protected function isCompleteEscapeSequence(string $sequence): bool
    {
        // Esc+Enter remaps (common in terminal keybind customization).
        if ($sequence === "\033\r" || $sequence === "\033\n") {
            return true;
        }

        // Arrow keys: \033[A-D, Home/End: \033[H, \033[F
        if (\preg_match('/^\033\[[A-DHF]$/', $sequence)) {
            return true;
        }

        // Function keys, Delete, bracketed paste: \033[NNN~ or \033[27;2;13~
        if (\preg_match('/^\033\[\d+(?:[;:]\d+)*~$/', $sequence)) {
            return true;
        }

        // Modified keys: \033[1;3C (Alt+Right), \033[1;5D (Ctrl+Left), etc.
        if (\preg_match('/^\033\[\d+(?:[;:]\d+)*[A-Za-z]$/', $sequence)) {
            return true;
        }

        // CSI-u protocol: \033[13;2u (Shift+Enter), \033[97;5u (Ctrl+A), etc.
        if (\preg_match('/^\033\[\d+(?:[;:]\d+)*u$/', $sequence)) {
            return true;
        }

        // SS3 keys: \033OM (keypad Enter), \033OA, etc.
        if (\preg_match('/^\033O[A-Z]$/', $sequence)) {
            return true;
        }

        // SGR mouse reports: \033[<button;col;row{M,m}.
        if (\preg_match('/^\033\[<\d+;\d+;\d+[Mm]$/', $sequence)) {
            return true;
        }

        // Alt+char: \033b, \033f, etc.
        if (\preg_match('/^\033[a-z]$/i', $sequence)) {
            return true;
        }

        return false;
    }

    /**
     * Read bracketed paste content.
     *
     * Called after detecting \033[200~ sequence.
     * Reads until \033[201~ is encountered.
     */
    protected function readBracketedPaste(): Key
    {
        $content = '';
        $escapeBuffer = '';

        while (true) {
            $char = \fgetc($this->stream);

            if ($char === false) {
                break;
            }

            if ($char === "\033") {
                $escapeBuffer = $char;

                \stream_set_blocking($this->stream, false);
                $timeout = 10000;
                $start = \microtime(true);

                while ((\microtime(true) - $start) < ($timeout / 1000000)) {
                    $nextChar = \fgetc($this->stream);

                    if ($nextChar === false) {
                        \usleep(500);
                        continue;
                    }

                    $escapeBuffer .= $nextChar;

                    if ($escapeBuffer === "\033[201~") {
                        \stream_set_blocking($this->stream, true);

                        return new Key($content, Key::TYPE_PASTE);
                    }

                    if ($this->isCompleteEscapeSequence($escapeBuffer)) {
                        break;
                    }

                    if (\strlen($escapeBuffer) > 32) {
                        break;
                    }
                }

                \stream_set_blocking($this->stream, true);

                $content .= $escapeBuffer;
                $escapeBuffer = '';
            } else {
                $content .= $char;
            }
        }

        return new Key($content, Key::TYPE_PASTE);
    }

    /**
     * Get the underlying stream resource.
     *
     * @return resource
     */
    public function getStream()
    {
        return $this->stream;
    }
}
