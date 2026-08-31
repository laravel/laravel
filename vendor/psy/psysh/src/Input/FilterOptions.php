<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Input;

use Psy\Exception\ErrorException;
use Psy\Exception\RuntimeException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;

/**
 * Parse, validate and match --grep, --insensitive and --invert command options.
 */
class FilterOptions
{
    private bool $filter = false;
    private ?string $pattern = null;
    private bool $insensitive = false;
    private bool $invert = false;

    /**
     * Get input option definitions for filtering.
     *
     * @return InputOption[]
     */
    public static function getOptions(): array
    {
        return [
            new InputOption('grep', 'G', InputOption::VALUE_REQUIRED, 'Limit to items matching the given pattern (string or regex).'),
            new InputOption('insensitive', 'i', InputOption::VALUE_NONE, 'Case-insensitive search (requires --grep).'),
            new InputOption('invert', 'v', InputOption::VALUE_NONE, 'Inverted search (requires --grep).'),
        ];
    }

    /**
     * Bind input and prepare filter.
     *
     * @param InputInterface $input
     */
    public function bind(InputInterface $input)
    {
        $this->validateInput($input);

        if (!$pattern = $input->getOption('grep')) {
            $this->filter = false;

            return;
        }

        if (!$this->stringIsRegex($pattern)) {
            $pattern = '/'.\preg_quote($pattern, '/').'/';
        }

        if ($insensitive = $input->getOption('insensitive')) {
            $pattern .= 'i';
        }

        $this->validateRegex($pattern);

        $this->filter = true;
        $this->pattern = $pattern;
        $this->insensitive = $insensitive;
        $this->invert = $input->getOption('invert');
    }

    /**
     * Check whether the bound input has filter options.
     */
    public function hasFilter(): bool
    {
        return $this->filter;
    }

    /**
     * Check whether a string matches the current filter options.
     *
     * @param string     $string
     * @param array|null $matches
     */
    public function match(string $string, ?array &$matches = null): bool
    {
        if ($this->filter === false || $this->pattern === null) {
            return true;
        }

        return \preg_match($this->pattern, $string, $matches) xor $this->invert;
    }

    /**
     * Validate that grep, invert and insensitive input options are consistent.
     *
     * @throws RuntimeException if input is invalid
     *
     * @param InputInterface $input
     */
    private function validateInput(InputInterface $input)
    {
        if (!$input->getOption('grep')) {
            foreach (['invert', 'insensitive'] as $option) {
                if ($input->getOption($option)) {
                    throw new RuntimeException('--'.$option.' does not make sense without --grep');
                }
            }
        }
    }

    /**
     * Check whether a string appears to be a regular expression.
     *
     * @param string $string
     */
    private function stringIsRegex(string $string): bool
    {
        return \substr($string, 0, 1) === '/' && \substr($string, -1) === '/' && \strlen($string) >= 3;
    }

    /**
     * Validate that $pattern is a valid regular expression.
     *
     * @throws RuntimeException if pattern is invalid
     *
     * @param string $pattern
     */
    private function validateRegex(string $pattern)
    {
        \set_error_handler([ErrorException::class, 'throwException']);
        try {
            // @phan-suppress-next-line PhanParamSuspiciousOrder - intentionally testing regex against empty string
            \preg_match($pattern, '');
        } catch (ErrorException $e) {
            throw new RuntimeException(\str_replace('preg_match(): ', 'Invalid regular expression: ', $e->getRawMessage()));
        } finally {
            \restore_error_handler();
        }
    }
}
