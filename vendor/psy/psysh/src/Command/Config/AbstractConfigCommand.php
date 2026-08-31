<?php

/*
 * This file is part of Psy Shell.
 *
 * (c) 2012-2026 Justin Hileman
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Psy\Command\Config;

use Psy\Command\Command;
use Psy\Configuration;
use Psy\Output\Theme;
use Symfony\Component\Console\Formatter\OutputFormatter;

/**
 * Base class for runtime configuration subcommands.
 */
abstract class AbstractConfigCommand extends Command
{
    private ?Configuration $config = null;
    private ?array $options = null;

    public function setConfiguration(Configuration $config): void
    {
        $this->config = $config;
        $this->options = null;
    }

    /**
     * @return array Associative array of option definitions keyed by lowercase name
     */
    protected function getOptions(): array
    {
        if ($this->options !== null) {
            return $this->options;
        }

        $config = $this->getConfig();
        $booleanParser = function (string $name, string $acceptedValues): callable {
            return function (string $value) use ($name, $acceptedValues): bool {
                switch (\strtolower($value)) {
                    case '1':
                    case 'true':
                    case 'yes':
                    case 'on':
                        return true;

                    case '0':
                    case 'false':
                    case 'no':
                    case 'off':
                        return false;

                    default:
                        throw new \InvalidArgumentException(\sprintf('Invalid %s value: %s. Accepted values: %s', $name, $value, $acceptedValues));
                }
            };
        };
        $semicolonsSuppressReturnParser = function (string $name, string $acceptedValues): callable {
            return function (string $value) use ($name, $acceptedValues) {
                switch (\strtolower($value)) {
                    case '1':
                    case 'true':
                    case 'yes':
                    case 'on':
                        return true;

                    case '0':
                    case 'false':
                    case 'no':
                    case 'off':
                        return false;

                    case Configuration::SEMICOLONS_SUPPRESS_RETURN_DOUBLE:
                        return Configuration::SEMICOLONS_SUPPRESS_RETURN_DOUBLE;

                    default:
                        throw new \InvalidArgumentException(\sprintf('Invalid %s value: %s. Accepted values: %s', $name, $value, $acceptedValues));
                }
            };
        };
        $enumParser = function (string $name, array $values, string $acceptedValues): callable {
            return function (string $value) use ($name, $values, $acceptedValues): string {
                if (!\in_array($value, $values, true)) {
                    throw new \InvalidArgumentException(\sprintf('Invalid %s value: %s. Accepted values: %s', $name, $value, $acceptedValues));
                }

                return $value;
            };
        };
        $configEnumParser = function (string $name, array $values, string $acceptedValues): callable {
            return function (string $value) use ($name, $values, $acceptedValues): string {
                if (\in_array($value, $values, true)) {
                    return $value;
                }

                try {
                    $resolved = $this->resolveConfigurationConstant($value);
                } catch (\Throwable $e) {
                    throw new \InvalidArgumentException(\sprintf('Invalid %s value: %s. Accepted values: %s', $name, $value, $acceptedValues), 0, $e);
                }

                if (!\is_string($resolved) || !\in_array($resolved, $values, true)) {
                    throw new \InvalidArgumentException(\sprintf('Invalid %s value: %s. Accepted values: %s', $name, $value, $acceptedValues));
                }

                return $resolved;
            };
        };

        $this->options = [
            'verbosity' => [
                'name'           => 'verbosity',
                'acceptedValues' => [
                    Configuration::VERBOSITY_QUIET,
                    Configuration::VERBOSITY_NORMAL,
                    Configuration::VERBOSITY_VERBOSE,
                    Configuration::VERBOSITY_VERY_VERBOSE,
                    Configuration::VERBOSITY_DEBUG,
                ],
                'parser' => $configEnumParser('verbosity', [
                    Configuration::VERBOSITY_QUIET,
                    Configuration::VERBOSITY_NORMAL,
                    Configuration::VERBOSITY_VERBOSE,
                    Configuration::VERBOSITY_VERY_VERBOSE,
                    Configuration::VERBOSITY_DEBUG,
                ], 'quiet|normal|verbose|very_verbose|debug'),
                'getter' => function () use ($config): string {
                    return $config->verbosity();
                },
                'setter' => function (string $value) use ($config): void {
                    $config->setVerbosity($value);
                },
                'refresh' => true,
            ],
            'useunicode' => [
                'name'           => 'useUnicode',
                'acceptedValues' => ['on', 'off'],
                'parser'         => $booleanParser('useUnicode', 'on|off'),
                'getter'         => function () use ($config): bool {
                    return $config->useUnicode();
                },
                'setter' => function (bool $value) use ($config): void {
                    $config->setUseUnicode($value);
                },
                'refresh' => false,
            ],
            'errorlogginglevel' => [
                'name'           => 'errorLoggingLevel',
                'acceptedValues' => ['<php-expression>'],
                'parser'         => function (string $value): int {
                    if (\preg_match('/^\d+$/', $value)) {
                        return (int) $value;
                    }

                    try {
                        $resolved = $this->getShell()->execute($value, true);
                    } catch (\Throwable $e) {
                        throw new \InvalidArgumentException(\sprintf('Invalid errorLoggingLevel value: %s. Accepted values: <php-expression>', $value), 0, $e);
                    }

                    if (!\is_int($resolved)) {
                        throw new \InvalidArgumentException(\sprintf('Invalid errorLoggingLevel value: %s. Accepted values: <php-expression>', $value));
                    }

                    return $resolved;
                },
                'getter' => function () use ($config): string {
                    return $this->formatErrorLoggingLevel($config->errorLoggingLevel());
                },
                'setter' => function (int $value) use ($config): void {
                    $config->setErrorLoggingLevel($value);
                },
                'refresh' => false,
            ],
            'clipboardcommand' => [
                'name'           => 'clipboardCommand',
                'acceptedValues' => ['auto', '<command>'],
                'parser'         => function (string $value): ?string {
                    return \strtolower($value) === 'auto' ? null : $value;
                },
                'getter' => function () use ($config): string {
                    return $config->clipboardCommand() ?? 'auto';
                },
                'setter' => function (?string $value) use ($config): void {
                    $config->setClipboardCommand($value);
                },
                'refresh' => false,
            ],
            'useosc52clipboard' => [
                'name'           => 'useOsc52Clipboard',
                'acceptedValues' => ['on', 'off'],
                'parser'         => $booleanParser('useOsc52Clipboard', 'on|off'),
                'getter'         => function () use ($config): bool {
                    return $config->useOsc52Clipboard();
                },
                'setter' => function (bool $value) use ($config): void {
                    $config->setUseOsc52Clipboard($value);
                },
                'refresh' => false,
            ],
            'colormode' => [
                'name'           => 'colorMode',
                'acceptedValues' => [
                    Configuration::COLOR_MODE_AUTO,
                    Configuration::COLOR_MODE_FORCED,
                    Configuration::COLOR_MODE_DISABLED,
                ],
                'parser' => $configEnumParser('colorMode', [
                    Configuration::COLOR_MODE_AUTO,
                    Configuration::COLOR_MODE_FORCED,
                    Configuration::COLOR_MODE_DISABLED,
                ], 'auto|forced|disabled'),
                'getter' => function () use ($config): string {
                    return $config->colorMode();
                },
                'setter' => function (string $value) use ($config): void {
                    $config->setColorMode($value);
                },
                'refresh' => true,
            ],
            'theme' => [
                'name'           => 'theme',
                'acceptedValues' => Theme::BUILTIN_THEMES,
                'parser'         => $enumParser('theme', Theme::BUILTIN_THEMES, \implode('|', Theme::BUILTIN_THEMES)),
                'getter'         => function () use ($config): string {
                    return $config->theme()->getName() ?? 'custom';
                },
                'setter' => function (string $value) use ($config): bool {
                    $before = $config->theme();
                    $config->setTheme($value);

                    return !$before->equals($config->theme());
                },
                'refresh' => true,
            ],
            'pager' => [
                'name'           => 'pager',
                'acceptedValues' => ['default', 'off', '<command>'],
                'parser'         => function (string $value) {
                    switch (\strtolower($value)) {
                        case 'default':
                        case 'on':
                        case 'yes':
                        case 'true':
                        case '1':
                            return null;
                        case 'off':
                        case 'no':
                        case 'false':
                        case '0':
                            return false;
                        default:
                            return $value;
                    }
                },
                'getter' => function () use ($config): string {
                    $pager = $config->getPager();

                    if ($pager === false) {
                        return 'off';
                    }

                    if ($pager === null) {
                        return 'default';
                    }

                    if ($pager === true) {
                        return 'builtin';
                    }

                    if (\is_string($pager)) {
                        return $pager;
                    }

                    return \get_class($pager);
                },
                'setter' => function ($value) use ($config): void {
                    if ($value === null) {
                        $config->setDefaultPager();

                        return;
                    }

                    $config->setPager($value);
                },
                'refresh' => true,
            ],
            'requiresemicolons' => [
                'name'           => 'requireSemicolons',
                'acceptedValues' => ['on', 'off'],
                'parser'         => $booleanParser('requireSemicolons', 'on|off'),
                'getter'         => function () use ($config): bool {
                    return $config->requireSemicolons();
                },
                'setter' => function (bool $value) use ($config): void {
                    $config->setRequireSemicolons($value);
                },
                'refresh' => true,
            ],
            'semicolonssuppressreturn' => [
                'name'           => 'semicolonsSuppressReturn',
                'acceptedValues' => ['on', 'off', Configuration::SEMICOLONS_SUPPRESS_RETURN_DOUBLE],
                'parser'         => $semicolonsSuppressReturnParser('semicolonsSuppressReturn', 'on|off|double'),
                'getter'         => function () use ($config) {
                    return $config->semicolonsSuppressReturn();
                },
                'setter' => function ($value) use ($config): void {
                    $config->setSemicolonsSuppressReturn($value);
                },
                'refresh' => false,
            ],
            'usebracketedpaste' => [
                'name'           => 'useBracketedPaste',
                'acceptedValues' => ['on', 'off'],
                'parser'         => $booleanParser('useBracketedPaste', 'on|off'),
                'getter'         => function () use ($config): bool {
                    return $config->useBracketedPaste();
                },
                'setter' => function (bool $value) use ($config): void {
                    $config->setUseBracketedPaste($value);
                },
                'refresh' => true,
            ],
            'usesyntaxhighlighting' => [
                'name'           => 'useSyntaxHighlighting',
                'acceptedValues' => ['on', 'off'],
                'parser'         => $booleanParser('useSyntaxHighlighting', 'on|off'),
                'getter'         => function () use ($config): bool {
                    return $config->useSyntaxHighlighting();
                },
                'setter' => function (bool $value) use ($config): void {
                    $config->setUseSyntaxHighlighting($value);
                },
                'refresh' => true,
            ],
            'usesuggestions' => [
                'name'           => 'useSuggestions',
                'acceptedValues' => ['on', 'off'],
                'parser'         => $booleanParser('useSuggestions', 'on|off'),
                'getter'         => function () use ($config): bool {
                    return $config->useSuggestions();
                },
                'setter' => function (bool $value) use ($config): void {
                    $config->setUseSuggestions($value);
                },
                'refresh' => true,
            ],
        ];

        return $this->options;
    }

    protected function getOption(string $key): ?array
    {
        return $this->getOptions()[\strtolower($key)] ?? null;
    }

    /**
     * @return string[]
     */
    protected function getOptionNames(): array
    {
        return \array_map(
            fn (array $option): string => $option['name'],
            \array_values($this->getOptions())
        );
    }

    /**
     * @param mixed $value
     */
    protected function formatValue($value): string
    {
        if (\is_bool($value)) {
            return $value ? 'true' : 'false';
        }

        if ($value === null) {
            return 'null';
        }

        return (string) $value;
    }

    protected function formatAcceptedValues(array $option): string
    {
        return OutputFormatter::escape(\implode('|', $option['acceptedValues']));
    }

    protected function formatErrorLoggingLevel(int $value): string
    {
        if ($value === 0) {
            return '0';
        }

        foreach ($this->getErrorLoggingConstants() as $name => $constantValue) {
            if ($value === $constantValue) {
                return $name;
            }
        }

        $allMask = $this->getErrorLoggingAllMask();
        if (($value & $allMask) === $value) {
            $included = $this->formatErrorLoggingFlags($value);
            $missingValue = $allMask & ~$value;
            $missing = $this->formatErrorLoggingFlags($missingValue);

            if ($included !== null && $missing !== null && $this->countErrorLoggingFlags($missingValue) < $this->countErrorLoggingFlags($value)) {
                return 'E_ALL & ~'.$this->wrapErrorLoggingFlags($missing);
            }

            if ($included !== null) {
                return $included;
            }
        }

        return (string) $value;
    }

    protected function formatOptionName(string $name): string
    {
        return \sprintf('<info>%s</info>', $name);
    }

    /**
     * @param string[] $names
     */
    protected function formatOptionNames(array $names): string
    {
        return \implode(', ', \array_map(fn (string $name): string => $this->formatOptionName($name), $names));
    }

    protected function unsupportedMessage(string $key): string
    {
        return \sprintf('Configuration option `%s` is not runtime-configurable.', $key);
    }

    protected function getConfig(): Configuration
    {
        if ($this->config === null) {
            throw new \RuntimeException('Configuration not available.');
        }

        return $this->config;
    }

    /**
     * @return int[] Error logging constants keyed by name
     */
    private function getErrorLoggingConstants(): array
    {
        $names = [
            'E_ALL',
            'E_ERROR',
            'E_WARNING',
            'E_PARSE',
            'E_NOTICE',
            'E_CORE_ERROR',
            'E_CORE_WARNING',
            'E_COMPILE_ERROR',
            'E_COMPILE_WARNING',
            'E_USER_ERROR',
            'E_USER_WARNING',
            'E_USER_NOTICE',
            'E_RECOVERABLE_ERROR',
            'E_DEPRECATED',
            'E_USER_DEPRECATED',
        ];

        // E_STRICT was deprecated in PHP 8.4. The constant is still defined,
        // but `\constant('E_STRICT')` triggers a deprecation notice.
        if (\PHP_VERSION_ID < 80400) {
            $names[] = 'E_STRICT';
        }

        $constants = [];

        foreach ($names as $name) {
            if (\defined($name)) {
                /** @var int $value */
                $value = \constant($name);
                $constants[$name] = $value;
            }
        }

        return $constants;
    }

    /**
     * @return int[] Error logging flag constants keyed by name, excluding E_ALL
     */
    private function getErrorLoggingFlagConstants(): array
    {
        $constants = $this->getErrorLoggingConstants();
        unset($constants['E_ALL']);

        return $constants;
    }

    private function getErrorLoggingAllMask(): int
    {
        return \PHP_VERSION_ID < 80400 ? (\E_ALL | \E_STRICT) : \E_ALL;
    }

    private function formatErrorLoggingFlags(int $value): ?string
    {
        if ($value === 0) {
            return null;
        }

        $parts = [];
        $covered = 0;

        foreach ($this->getErrorLoggingFlagConstants() as $name => $constantValue) {
            if ($constantValue !== 0 && ($value & $constantValue) === $constantValue) {
                $parts[] = $name;
                $covered |= $constantValue;
            }
        }

        if ($parts === [] || $covered !== $value) {
            return null;
        }

        return \implode(' | ', $parts);
    }

    private function countErrorLoggingFlags(int $value): int
    {
        $count = 0;

        foreach ($this->getErrorLoggingFlagConstants() as $constantValue) {
            if ($constantValue !== 0 && ($value & $constantValue) === $constantValue) {
                $count++;
            }
        }

        return $count;
    }

    private function wrapErrorLoggingFlags(string $expression): string
    {
        return \strpos($expression, ' | ') === false ? $expression : '('.$expression.')';
    }

    private function resolveConfigurationConstant(string $value): string
    {
        if (!\preg_match('/^\\\\?(?:Psy\\\\)?Configuration::([A-Z_]+)$/', $value, $matches)) {
            throw new \InvalidArgumentException('Unsupported configuration constant expression.');
        }

        $constant = 'Psy\\Configuration::'.$matches[1];

        if (!\defined($constant)) {
            throw new \InvalidArgumentException('Unknown configuration constant.');
        }

        $resolved = \constant($constant);

        if (!\is_string($resolved)) {
            throw new \InvalidArgumentException('Configuration constant does not resolve to a string value.');
        }

        return $resolved;
    }
}
