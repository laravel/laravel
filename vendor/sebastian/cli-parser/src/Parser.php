<?php declare(strict_types=1);
/*
 * This file is part of sebastian/cli-parser.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\CliParser;

use function array_map;
use function array_merge;
use function array_shift;
use function array_slice;
use function assert;
use function count;
use function current;
use function explode;
use function is_array;
use function is_int;
use function is_string;
use function key;
use function next;
use function preg_replace;
use function reset;
use function sort;
use function str_ends_with;
use function str_starts_with;
use function strlen;
use function strstr;
use function substr;

final class Parser
{
    /**
     * @param list<string> $argv
     * @param list<string> $longOptions
     *
     * @throws AmbiguousOptionException
     * @throws OptionDoesNotAllowArgumentException
     * @throws RequiredOptionArgumentMissingException
     * @throws UnknownOptionException
     *
     * @return array{0: list<array{0: non-empty-string, 1: ?non-empty-string}>, 1: list<non-empty-string>}
     */
    public function parse(array $argv, string $shortOptions, ?array $longOptions = null): array
    {
        if (empty($argv)) {
            return [[], []];
        }

        $options    = [];
        $nonOptions = [];

        if ($longOptions !== null) {
            sort($longOptions);
        }

        if (isset($argv[0][0]) && $argv[0][0] !== '-') {
            array_shift($argv);
        }

        reset($argv);

        $argv = array_map('trim', $argv);

        while (false !== $arg = current($argv)) {
            $i = key($argv);

            assert(is_int($i));

            next($argv);

            if ($arg === '') {
                continue;
            }

            if ($arg === '--') {
                $nonOptions = array_merge($nonOptions, array_slice($argv, $i + 1));

                break;
            }

            if ($arg[0] !== '-' || (strlen($arg) > 1 && $arg[1] === '-' && $longOptions === null)) {
                $nonOptions[] = $arg;

                continue;
            }

            if (strlen($arg) > 1 && $arg[1] === '-' && is_array($longOptions)) {
                $this->parseLongOption(
                    substr($arg, 2),
                    $longOptions,
                    $options,
                    $argv,
                );

                continue;
            }

            $this->parseShortOption(
                substr($arg, 1),
                $shortOptions,
                $options,
                $argv,
            );
        }

        return [$options, $nonOptions];
    }

    /**
     * @param list<array{0: non-empty-string, 1: ?non-empty-string}> $options
     * @param list<string>                                           $argv
     *
     * @throws RequiredOptionArgumentMissingException
     */
    private function parseShortOption(string $argument, string $shortOptions, array &$options, array &$argv): void
    {
        $argumentLength = strlen($argument);

        for ($i = 0; $i < $argumentLength; $i++) {
            $option         = $argument[$i];
            $optionArgument = null;

            if ($argument[$i] === ':' || ($spec = strstr($shortOptions, $option)) === false) {
                throw new UnknownOptionException('-' . $option);
            }

            if (strlen($spec) > 1 && $spec[1] === ':') {
                if ($i + 1 < $argumentLength) {
                    $options[] = [$option, substr($argument, $i + 1)];

                    break;
                }

                if (!(strlen($spec) > 2 && $spec[2] === ':')) {
                    $optionArgument = current($argv);

                    if ($optionArgument === false) {
                        throw new RequiredOptionArgumentMissingException('-' . $option);
                    }

                    assert(is_string($optionArgument));

                    next($argv);
                }
            }

            $options[] = [$option, $optionArgument];
        }
    }

    /**
     * @param list<string>                                           $longOptions
     * @param list<array{0: non-empty-string, 1: ?non-empty-string}> $options
     * @param list<string>                                           $argv
     *
     * @throws AmbiguousOptionException
     * @throws OptionDoesNotAllowArgumentException
     * @throws RequiredOptionArgumentMissingException
     * @throws UnknownOptionException
     */
    private function parseLongOption(string $argument, array $longOptions, array &$options, array &$argv): void
    {
        $count          = count($longOptions);
        $list           = explode('=', $argument);
        $option         = $list[0];
        $optionArgument = null;

        if (count($list) > 1) {
            $optionArgument = $list[1];
        }

        $optionLength = strlen($option);

        foreach ($longOptions as $i => $longOption) {
            $opt_start = substr($longOption, 0, $optionLength);

            if ($opt_start !== $option) {
                continue;
            }

            $opt_rest = substr($longOption, $optionLength);

            if ($opt_rest !== '' && $i + 1 < $count && $option[0] !== '=' && str_starts_with($longOptions[$i + 1], $option)) {
                throw new AmbiguousOptionException('--' . $option);
            }

            if (str_ends_with($longOption, '=')) {
                if (!str_ends_with($longOption, '==') && !strlen((string) $optionArgument)) {
                    if (false === $optionArgument = current($argv)) {
                        throw new RequiredOptionArgumentMissingException('--' . $option);
                    }

                    next($argv);
                }
            } elseif ($optionArgument !== null) {
                throw new OptionDoesNotAllowArgumentException('--' . $option);
            }

            $fullOption = '--' . preg_replace('/={1,2}$/', '', $longOption);
            $options[]  = [$fullOption, $optionArgument];

            return;
        }

        throw new UnknownOptionException('--' . $option);
    }
}
