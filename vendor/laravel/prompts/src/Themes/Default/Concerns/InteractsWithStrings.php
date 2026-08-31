<?php

namespace Laravel\Prompts\Themes\Default\Concerns;

trait InteractsWithStrings
{
    /**
     * Get the length of the longest line.
     *
     * @param  array<string>  $lines
     */
    protected function longest(array $lines, int $padding = 0): int
    {
        return max(
            $this->minWidth,
            count($lines) > 0 ? max(array_map(fn ($line) => mb_strwidth($this->stripEscapeSequences($line)) + $padding, $lines)) : null
        );
    }

    /**
     * Pad text ignoring ANSI escape sequences.
     */
    protected function pad(string $text, int $length, string $char = ' '): string
    {
        $rightPadding = str_repeat($char, max(0, $length - mb_strwidth($this->stripEscapeSequences($text))));

        return "{$text}{$rightPadding}";
    }

    /**
     * Strip ANSI escape sequences from the given text.
     */
    protected function stripEscapeSequences(string $text): string
    {
        // Strip OSC 8 hyperlink sequences.
        $text = preg_replace("/\e\]8;[^\e]*\e\\\\/", '', $text);

        // Strip ANSI escape sequences.
        $text = preg_replace("/\e[^m]*m/", '', $text);

        // Strip Symfony named style tags.
        $text = preg_replace("/<(info|comment|question|error)>(.*?)<\/\\1>/", '$2', $text);

        // Strip Symfony inline style tags.
        return preg_replace("/<(?:(?:[fb]g|options)=[a-z,;]+)+>(.*?)<\/>/i", '$1', $text);
    }

    /**
     * Multi-byte version of wordwrap.
     *
     * @param  non-empty-string  $break
     */
    protected function mbWordwrap(
        string $string,
        int $width = 75,
        string $break = "\n",
        bool $cut_long_words = false
    ): string {
        $lines = explode($break, $string);
        $result = [];

        foreach ($lines as $originalLine) {
            if (mb_strwidth($originalLine) <= $width) {
                $result[] = $originalLine;

                continue;
            }

            $words = explode(' ', $originalLine);
            $line = null;
            $lineWidth = 0;

            if ($cut_long_words) {
                foreach ($words as $index => $word) {
                    $characters = mb_str_split($word);
                    $strings = [];
                    $str = '';

                    foreach ($characters as $character) {
                        $tmp = $str.$character;

                        if (mb_strwidth($tmp) > $width) {
                            $strings[] = $str;
                            $str = $character;
                        } else {
                            $str = $tmp;
                        }
                    }

                    if ($str !== '') {
                        $strings[] = $str;
                    }

                    $words[$index] = implode(' ', $strings);
                }

                $words = explode(' ', implode(' ', $words));
            }

            foreach ($words as $word) {
                $tmp = ($line === null) ? $word : $line.' '.$word;

                // Look for zero-width joiner characters (combined emojis)
                preg_match('/\p{Cf}/u', $word, $joinerMatches);

                $wordWidth = count($joinerMatches) > 0 ? 2 : mb_strwidth($word);

                $lineWidth += $wordWidth;

                if ($line !== null) {
                    // Space between words
                    $lineWidth += 1;
                }

                if ($lineWidth <= $width) {
                    $line = $tmp;
                } else {
                    $result[] = $line;
                    $line = $word;
                    $lineWidth = $wordWidth;
                }
            }

            if ($line !== '') {
                $result[] = $line;
            }

            $line = null;
        }

        return implode($break, $result);
    }

    /**
     * Word wrap text while preserving ANSI escape sequences.
     *
     * @return array<int, string>
     */
    protected function ansiWordwrap(string $text, int $width): array
    {
        // Parse segments and build character array with codes
        $segments = $this->parseAnsiText($text);
        $plainText = $this->stripEscapeSequences($text);
        $chars = [];

        foreach ($segments as $segment) {
            $segmentChars = mb_str_split($segment['text']);

            foreach ($segmentChars as $char) {
                $chars[] = ['char' => $char, 'codes' => $segment['codes'], 'link' => $segment['link']];
            }
        }

        // Word wrap the plain text
        $wrappedLines = $this->mbWordwrap($plainText, $width, "\n", false);
        $plainLines = explode("\n", $wrappedLines);

        // Rebuild each wrapped line with ANSI codes
        $result = [];
        $charIndex = 0;

        foreach ($plainLines as $plainLine) {
            $line = '';
            $lastCodes = '';
            $lastLink = '';
            $lineChars = mb_str_split($plainLine);

            foreach ($lineChars as $lineChar) {
                // Find matching character in original (handling spaces removed by wordwrap)
                while ($charIndex < count($chars) && $chars[$charIndex]['char'] !== $lineChar) {
                    // Skip spaces that wordwrap removed
                    if ($chars[$charIndex]['char'] === ' ') {
                        $charIndex++;
                    } else {
                        break;
                    }
                }

                if ($charIndex < count($chars)) {
                    $link = $chars[$charIndex]['link'];
                    $codes = $chars[$charIndex]['codes'];

                    if ($link !== $lastLink) {
                        if ($lastLink !== '') {
                            $line .= "\e]8;;\e\\";
                        }

                        if ($link !== '') {
                            $line .= $link;
                        }

                        $lastLink = $link;
                    }

                    if ($codes !== $lastCodes) {
                        if ($lastCodes !== '') {
                            $line .= "\e[0m";
                        }

                        if ($codes !== '') {
                            $line .= $codes;
                        }

                        $lastCodes = $codes;
                    }

                    $line .= $lineChar;
                    $charIndex++;
                } else {
                    $line .= $lineChar;
                }
            }

            // Close any open ANSI codes
            if ($lastCodes !== '' && ! str_ends_with($line, "\e[0m")) {
                $line .= "\e[0m";
            }

            if ($lastLink !== '') {
                $line .= "\e]8;;\e\\";
            }

            $result[] = $line;
        }

        return $result;
    }

    /**
     * Parse text into segments with their associated ANSI codes.
     *
     * @return array<int, array{text: string, codes: string, link: string}>
     */
    protected function parseAnsiText(string $text): array
    {
        $segments = [];
        $currentCodes = '';
        $currentLink = '';
        $currentText = '';
        $i = 0;
        $textLength = strlen($text);

        while ($i < $textLength) {
            if ($text[$i] === "\e" && ($i + 1 < $textLength)) {
                if ($text[$i + 1] === '[') {
                    // Save current segment if it has text
                    if ($currentText !== '') {
                        $segments[] = ['text' => $currentText, 'codes' => $currentCodes, 'link' => $currentLink];
                        $currentText = '';
                    }

                    // Extract CSI escape sequence
                    $escapeSequence = '';
                    while ($i < $textLength) {
                        $escapeSequence .= $text[$i];
                        $i++;

                        if (preg_match('/^\\e\\[[0-9;]*m$/', $escapeSequence)) {
                            if ($escapeSequence === "\e[0m") {
                                $currentCodes = '';
                            } else {
                                $currentCodes = $escapeSequence;
                            }
                            break;
                        }
                    }

                    continue;
                } elseif ($text[$i + 1] === ']') {
                    // Save current segment if it has text
                    if ($currentText !== '') {
                        $segments[] = ['text' => $currentText, 'codes' => $currentCodes, 'link' => $currentLink];
                        $currentText = '';
                    }

                    // Extract OSC sequence (read until ST: ESC \)
                    $escapeSequence = "\e]";
                    $i += 2;

                    while ($i < $textLength) {
                        if ($text[$i] === "\e" && ($i + 1 < $textLength) && $text[$i + 1] === '\\') {
                            $escapeSequence .= "\e\\";
                            $i += 2;
                            break;
                        }
                        $escapeSequence .= $text[$i];
                        $i++;
                    }

                    if ($escapeSequence === "\e]8;;\e\\") {
                        $currentLink = '';
                    } else {
                        $currentLink = $escapeSequence;
                    }

                    continue;
                }
            }

            $currentText .= $text[$i];
            $i++;
        }

        // Add final segment
        if ($currentText !== '') {
            $segments[] = ['text' => $currentText, 'codes' => $currentCodes, 'link' => $currentLink];
        }

        return $segments;
    }
}
