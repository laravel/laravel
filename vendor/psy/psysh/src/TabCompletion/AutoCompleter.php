<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\TabCompletion;

use Psy\Completion\CompletionEngine;
use Psy\Completion\CompletionRequest;
use Psy\Completion\Source\MatcherAdapterSource;
use Psy\TabCompletion\Matcher\AbstractMatcher;

/**
 * A readline tab completion service.
 *
 * @author Marc Garcia <markcial@gmail.com>
 */
class AutoCompleter
{
    /** @var Matcher\AbstractMatcher[] */
    protected $matchers = [];
    private ?CompletionEngine $completionEngine = null;

    /**
     * Register a tab completion Matcher.
     *
     * @param AbstractMatcher $matcher
     */
    public function addMatcher(AbstractMatcher $matcher)
    {
        $this->matchers[] = $matcher;

        if ($this->completionEngine !== null) {
            $this->completionEngine->addSource(new MatcherAdapterSource([$matcher]));
        }
    }

    /**
     * Use the canonical completion engine for readline callbacks.
     */
    public function setCompletionEngine(CompletionEngine $completionEngine): void
    {
        $this->completionEngine = $completionEngine;

        if ($this->matchers !== []) {
            $this->completionEngine->addSource(new MatcherAdapterSource($this->matchers));
        }
    }

    /**
     * Activate readline tab completion.
     */
    public function activate()
    {
        \readline_completion_function([&$this, 'callback']);
    }

    /**
     * Handle readline completion.
     *
     * @param string $input Readline current word
     * @param int    $index Current word index
     * @param array  $info  readline_info() data
     *
     * @return array
     */
    public function processCallback(string $input, int $index, array $info = []): array
    {
        $line = $this->normalizeLineBuffer($input, $info);

        if ($this->completionEngine === null) {
            throw new \LogicException('AutoCompleter requires a CompletionEngine.');
        }

        $matches = $this->completionEngine->getCompletions(
            new CompletionRequest($line, \mb_strlen($line), CompletionRequest::MODE_TAB, $info)
        );

        return !empty($matches) ? $matches : [''];
    }

    /**
     * The readline_completion_function callback handler.
     *
     * @see processCallback
     *
     * @param string $input
     * @param int    $index
     *
     * @return array
     */
    public function callback(string $input, int $index): array
    {
        return $this->processCallback($input, $index, \readline_info());
    }

    private function normalizeLineBuffer(string $input, array $info): string
    {
        $line = $info['line_buffer'] ?? '';
        if (isset($info['end'])) {
            $line = \substr($line, 0, $info['end']);
        }
        if ($line === '' && $input !== '') {
            $line = $input;
        }

        return $line;
    }

    /**
     * Remove readline callback handler on destruct.
     */
    public function __destruct()
    {
        // PHP didn't implement the whole readline API when they first switched
        // to libedit. And they still haven't.
        if (\function_exists('readline_callback_handler_remove')) {
            \readline_callback_handler_remove();
        }
    }
}
