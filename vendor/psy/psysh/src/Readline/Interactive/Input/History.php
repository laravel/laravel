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

use Psy\Util\Str;

/**
 * Command history manager.
 *
 * Manages a list of previously entered commands with navigation support.
 */
class History
{
    private const HISTORY_SIGNATURE = [
        'type'    => 'psysh-history',
        'format'  => 'jsonl',
        'version' => 1,
    ];

    /** @var array[] History entries (index 0 = oldest, last index = most recent) */
    private array $entries = [];

    /** @var int Current position in history (-1 = not in history, index entries in when navigating) */
    private int $position = -1;

    /** Temporary entry saved when entering history, restored on forward exit */
    private ?string $temporaryEntry = null;

    /** Search term for filtered history navigation (null = no filtering) */
    private ?string $searchTerm = null;

    /** Last command returned during navigation, for skipping consecutive dupes */
    private ?string $lastNavigatedCommand = null;

    private int $maxSize;
    private bool $eraseDups;
    private int $revision = 0;
    private int $sessionStartIndex = 0;

    public function __construct(int $maxSize = 10000, bool $eraseDups = false)
    {
        $this->maxSize = $maxSize;
        $this->eraseDups = $eraseDups;
    }

    /**
     * Add an entry to history.
     *
     * Skips empty lines and duplicates of the most recent entry.
     * If eraseDups is enabled, removes any previous occurrence of the same command.
     */
    public function add(string $line): void
    {
        if (\trim($line) === '') {
            return;
        }

        $lastIndex = \count($this->entries) - 1;
        if ($lastIndex >= 0 && $this->entries[$lastIndex]['command'] === $line) {
            return;
        }

        if ($this->eraseDups) {
            $this->removeDuplicateEntries($line);
        }

        $this->entries[] = [
            'command'   => $line,
            'timestamp' => \time(),
            'lines'     => \substr_count($line, "\n") + 1,
        ];

        if ($this->maxSize > 0 && \count($this->entries) > $this->maxSize) {
            $trimmed = \count($this->entries) - $this->maxSize;
            $this->entries = \array_slice($this->entries, -$this->maxSize);
            $this->sessionStartIndex = \max(0, $this->sessionStartIndex - $trimmed);
        }

        $this->revision++;
    }

    /**
     * Get the previous history entry.
     *
     * Moves backward in history (toward older entries).
     * When a search term is set, skips entries that don't match.
     *
     * @return string|null The previous entry, or null if at the beginning
     */
    public function getPrevious(): ?string
    {
        if (empty($this->entries)) {
            return null;
        }

        $start = $this->position === -1 ? \count($this->entries) - 1 : $this->position - 1;

        for ($i = $start; $i >= 0; $i--) {
            $command = $this->entries[$i]['command'];
            if ($this->matchesSearchTerm($command) && $command !== $this->lastNavigatedCommand) {
                $this->position = $i;
                $this->lastNavigatedCommand = $command;

                return $command;
            }
        }

        return null;
    }

    /**
     * Get the next history entry.
     *
     * Moves forward in history (toward newer entries).
     * When a search term is set, skips entries that don't match.
     * Restores the temporary entry when moving past the most recent match.
     *
     * @return string|null The next entry, or the saved input when exiting history
     */
    public function getNext(): ?string
    {
        if ($this->position === -1) {
            return null;
        }

        $lastIndex = \count($this->entries) - 1;

        for ($i = $this->position + 1; $i <= $lastIndex; $i++) {
            $command = $this->entries[$i]['command'];
            if ($this->matchesSearchTerm($command) && $command !== $this->lastNavigatedCommand) {
                $this->position = $i;
                $this->lastNavigatedCommand = $command;

                return $command;
            }
        }

        // No more matches forward — exit history, restore original input
        $this->position = -1;
        $this->lastNavigatedCommand = null;
        $temp = $this->temporaryEntry;
        $this->temporaryEntry = null;

        return $temp ?? '';
    }

    /**
     * Reset history navigation.
     *
     * Call this when a line is accepted to prepare for next input.
     */
    public function reset(): void
    {
        $this->position = -1;
        $this->temporaryEntry = null;
        $this->searchTerm = null;
        $this->lastNavigatedCommand = null;
    }

    /**
     * Save the current input as temporary entry for restoring when exiting history forward.
     */
    public function saveTemporaryEntry(string $entry): void
    {
        $this->temporaryEntry = $entry;
    }

    /**
     * Set the search term for filtered history navigation.
     *
     * When set, getPrevious() and getNext() will only return entries
     * containing the search term as a substring.
     *
     * Uses smart case: case-insensitive unless the term contains uppercase.
     */
    public function setSearchTerm(?string $term): void
    {
        $this->searchTerm = ($term !== null && $term !== '') ? $term : null;
    }

    /**
     * Get the current search term for filtered navigation.
     */
    public function getSearchTerm(): ?string
    {
        return $this->searchTerm;
    }

    /**
     * Check if currently navigating history.
     */
    public function isInHistory(): bool
    {
        return $this->position !== -1;
    }

    /**
     * Get the current position in history.
     */
    public function getPosition(): int
    {
        return $this->position;
    }

    /**
     * Get all history entries.
     *
     * @return array[] History entries (index 0 = oldest, last index = most recent)
     */
    public function getAll(): array
    {
        return $this->entries;
    }

    /**
     * Get commands added during the current REPL session.
     *
     * @return string[]
     */
    public function getSessionCommands(): array
    {
        if ($this->sessionStartIndex >= \count($this->entries)) {
            return [];
        }

        return \array_column(\array_slice($this->entries, $this->sessionStartIndex), 'command');
    }

    /**
     * Search history for entries matching a query.
     *
     * @param string $query   The search query (smart-case: case-sensitive if query contains uppercase)
     * @param bool   $reverse If true, return oldest matches first; if false, newest first
     *
     * @return string[] Matching command strings
     */
    public function search(string $query, bool $reverse = false): array
    {
        if ($query === '') {
            $commands = $this->deduplicateCommands(\array_column($this->entries, 'command'));

            return $reverse ? $commands : \array_reverse($commands);
        }

        $caseSensitive = self::isSearchCaseSensitive($query);
        $matches = [];
        foreach ($this->entries as $entry) {
            $found = $caseSensitive
                ? \strpos($entry['command'], $query) !== false
                : \stripos($entry['command'], $query) !== false;
            if ($found) {
                $matches[] = $entry['command'];
            }
        }

        $matches = $this->deduplicateCommands($matches);

        return $reverse ? $matches : \array_reverse($matches);
    }

    /**
     * Deduplicate commands, keeping only the last (most recent) occurrence.
     *
     * @param string[] $commands
     *
     * @return string[]
     */
    private function deduplicateCommands(array $commands): array
    {
        $seen = [];
        $result = [];
        foreach (\array_reverse($commands) as $command) {
            if (!isset($seen[$command])) {
                $seen[$command] = true;
                $result[] = $command;
            }
        }

        return \array_reverse($result);
    }

    /**
     * Get the number of entries in history.
     */
    public function getCount(): int
    {
        return \count($this->entries);
    }

    /**
     * Get the current history revision.
     *
     * Increments on every mutation (add, clear, load, import).
     */
    public function getRevision(): int
    {
        return $this->revision;
    }

    /**
     * Clear all history.
     */
    public function clear(): void
    {
        $this->entries = [];
        $this->position = -1;
        $this->temporaryEntry = null;
        $this->searchTerm = null;
        $this->lastNavigatedCommand = null;
        $this->sessionStartIndex = 0;
        $this->revision++;
    }

    /**
     * Load history from file.
     *
     * Loads JSONL (interactive history format) entries.
     * Optionally accepts a JSON signature line at the top of the file.
     * Non-JSON lines are ignored.
     */
    public function loadFromFile(string $path): void
    {
        $lines = $this->readLinesFromFile($path);
        if ($lines === null) {
            return;
        }

        $hasJsonSignature = false;
        $entries = [];
        foreach ($lines as $i => $line) {
            if ($i === 0 && self::isJsonHistorySignatureLine($line)) {
                $hasJsonSignature = true;

                continue;
            }

            $entry = $this->parseJsonLine($line);
            if ($entry !== null) {
                $entries[] = $entry;
            }
        }

        // If the file has content but no valid JSONL entries, leave history unchanged.
        if (!empty($lines) && empty($entries) && !$hasJsonSignature) {
            return;
        }

        $this->setEntries($entries);
    }

    /**
     * Import history from a legacy plain-text file.
     *
     * Legacy format stores one command per line.
     */
    public function importFromFile(string $path): void
    {
        $lines = $this->readLinesFromFile($path);
        if ($lines === null) {
            return;
        }

        // libedit legacy files may start with a version signature line.
        if (!empty($lines) && $lines[0] === '_HiStOrY_V2_') {
            \array_shift($lines);
        }

        $entries = [];
        foreach ($lines as $line) {
            $entry = $this->parseLegacyLine($line);
            if ($entry !== null) {
                $entries[] = $entry;
            }
        }

        $this->setEntries($entries);
    }

    /**
     * Save history to file.
     *
     * Saves in JSONL format with metadata.
     */
    public function saveToFile(string $path): void
    {
        $dir = \dirname($path);
        if (!\is_dir($dir)) {
            @\mkdir($dir, 0700, true);
        }

        $jsonFlags = \JSON_UNESCAPED_SLASHES | \JSON_UNESCAPED_UNICODE;
        $lines = \array_map(fn ($entry) => \json_encode($entry, $jsonFlags), $this->entries);
        \array_unshift($lines, \json_encode(self::HISTORY_SIGNATURE, $jsonFlags));

        $content = \implode("\n", $lines)."\n";

        @\file_put_contents($path, $content);
    }

    /**
     * Check whether a line is valid JSON history (entry or signature).
     */
    public static function isJsonHistoryLine(string $line): bool
    {
        $data = @\json_decode(\trim($line), true);
        if (!\is_array($data)) {
            return false;
        }

        if (isset($data['command']) && \is_string($data['command'])) {
            return true;
        }

        return self::isJsonHistorySignatureData($data);
    }

    /**
     * Get display command for history navigation.
     *
     * For multi-line commands, returns a single-line representation.
     * Used by the `hist` command for display purposes.
     *
     * @param array $entry History entry
     */
    public function getDisplayCommand(array $entry): string
    {
        $command = $entry['command'];
        $lines = $entry['lines'] ?? 1;

        if ($lines === 1) {
            return $command;
        }

        return self::collapseToSingleLine($command);
    }

    /**
     * Collapse multi-line text to a single line.
     */
    public static function collapseToSingleLine(string $text): string
    {
        $lines = \explode("\n", $text);
        $lines = \array_map('trim', $lines);
        $lines = \array_filter($lines, fn ($line) => $line !== '');

        return \implode(' ', $lines);
    }

    /**
     * Parse a JSONL line into a history entry.
     *
     * @return array|null
     */
    private function parseJsonLine(string $line): ?array
    {
        $data = @\json_decode(\trim($line), true);

        if (!\is_array($data) || !isset($data['command']) || !\is_string($data['command'])) {
            return null;
        }

        return [
            'command'   => $data['command'],
            'timestamp' => $data['timestamp'] ?? \time(),
            'lines'     => $data['lines'] ?? (\substr_count($data['command'], "\n") + 1),
        ];
    }

    /**
     * Parse a legacy plain-text history line into an entry.
     */
    private function parseLegacyLine(string $line): ?array
    {
        // GNU/libedit history comments and timestamps use NUL-prefixed lines.
        if ($line === '' || $line[0] === "\0") {
            return null;
        }

        // Ignore comment/timestamp suffix after NUL.
        if (($pos = \strpos($line, "\0")) !== false) {
            $line = \substr($line, 0, $pos);
        }

        $command = Str::unvis(\rtrim($line, "\r"));
        if ($command === '') {
            return null;
        }

        return [
            'command'   => $command,
            'timestamp' => \time(),
            'lines'     => \substr_count($command, "\n") + 1,
        ];
    }

    /**
     * Check whether a line is a JSON history signature.
     */
    private static function isJsonHistorySignatureLine(string $line): bool
    {
        $data = @\json_decode(\trim($line), true);

        return \is_array($data) && self::isJsonHistorySignatureData($data);
    }

    /**
     * Check whether decoded JSON matches the history signature schema.
     *
     * @param array $data
     */
    private static function isJsonHistorySignatureData(array $data): bool
    {
        return ($data['type'] ?? null) === self::HISTORY_SIGNATURE['type']
            && ($data['format'] ?? null) === self::HISTORY_SIGNATURE['format']
            && \is_int($data['version'] ?? null)
            && $data['version'] === self::HISTORY_SIGNATURE['version'];
    }

    /**
     * Read and normalize non-empty lines from a file.
     *
     * @return string[]|null
     */
    private function readLinesFromFile(string $path): ?array
    {
        if (!\file_exists($path) || !\is_readable($path)) {
            return null;
        }

        $content = @\file_get_contents($path);
        if ($content === false) {
            return null;
        }

        $lines = \explode("\n", $content);

        return \array_values(\array_filter($lines, fn ($line) => \trim($line) !== ''));
    }

    /**
     * Check whether a search term is case-sensitive (contains uppercase).
     */
    public static function isSearchCaseSensitive(string $term): bool
    {
        return $term !== \mb_strtolower($term);
    }

    /**
     * Check whether a command matches the current search term.
     *
     * Smart case: case-insensitive unless the search term contains uppercase.
     */
    private function matchesSearchTerm(string $command): bool
    {
        if ($this->searchTerm === null) {
            return true;
        }

        if (self::isSearchCaseSensitive($this->searchTerm)) {
            return \strpos($command, $this->searchTerm) !== false;
        }

        return \stripos($command, $this->searchTerm) !== false;
    }

    /**
     * Replace history entries, enforcing the configured max size.
     *
     * @param array $entries
     */
    private function setEntries(array $entries): void
    {
        if ($this->maxSize > 0 && \count($entries) > $this->maxSize) {
            $entries = \array_slice($entries, -$this->maxSize);
        }

        $this->entries = \array_values($entries);
        $this->position = -1;
        $this->temporaryEntry = null;
        $this->searchTerm = null;
        $this->lastNavigatedCommand = null;
        $this->sessionStartIndex = \count($this->entries);
        $this->revision++;
    }

    private function removeDuplicateEntries(string $line): void
    {
        $entries = [];

        foreach ($this->entries as $i => $entry) {
            if ($entry['command'] === $line) {
                if ($i < $this->sessionStartIndex) {
                    $this->sessionStartIndex--;
                }

                continue;
            }

            $entries[] = $entry;
        }

        $this->entries = $entries;
    }
}
