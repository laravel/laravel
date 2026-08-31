<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\HttpKernel\CacheWarmer;

use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Aggregates several cache warmers into a single one.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 *
 * @final
 */
class CacheWarmerAggregate implements CacheWarmerInterface
{
    private bool $optionalsEnabled = false;
    private bool $onlyOptionalsEnabled = false;

    /**
     * @param iterable<mixed, CacheWarmerInterface> $warmers
     */
    public function __construct(
        private iterable $warmers = [],
        private bool $debug = false,
        private ?string $deprecationLogsFilepath = null,
    ) {
    }

    public function enableOptionalWarmers(): void
    {
        $this->optionalsEnabled = true;
    }

    public function enableOnlyOptionalWarmers(): void
    {
        $this->onlyOptionalsEnabled = $this->optionalsEnabled = true;
    }

    public function warmUp(string $cacheDir, ?string $buildDir = null, ?SymfonyStyle $io = null): array
    {
        if ($collectDeprecations = $this->debug && !\defined('PHPUNIT_COMPOSER_INSTALL')) {
            $collectedLogs = [];
            $previousHandler = set_error_handler(static function ($type, $message, $file, $line) use (&$collectedLogs, &$previousHandler) {
                if (\E_USER_DEPRECATED !== $type && \E_DEPRECATED !== $type) {
                    return $previousHandler ? $previousHandler($type, $message, $file, $line) : false;
                }

                if (isset($collectedLogs[$message])) {
                    ++$collectedLogs[$message]['count'];

                    return null;
                }

                $backtrace = debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 3);
                // Clean the trace by removing first frames added by the error handler itself.
                for ($i = 0; isset($backtrace[$i]); ++$i) {
                    if (isset($backtrace[$i]['file'], $backtrace[$i]['line']) && $backtrace[$i]['line'] === $line && $backtrace[$i]['file'] === $file) {
                        $backtrace = \array_slice($backtrace, 1 + $i);
                        break;
                    }
                }

                $collectedLogs[$message] = [
                    'type' => $type,
                    'message' => $message,
                    'file' => $file,
                    'line' => $line,
                    'trace' => $backtrace,
                    'count' => 1,
                ];

                return null;
            });
        }

        $preload = [];
        try {
            foreach ($this->warmers as $warmer) {
                if (!$this->optionalsEnabled && $warmer->isOptional()) {
                    continue;
                }
                if ($this->onlyOptionalsEnabled && !$warmer->isOptional()) {
                    continue;
                }

                $start = microtime(true);
                foreach ($warmer->warmUp($cacheDir, $buildDir) as $item) {
                    if (is_dir($item) || (str_starts_with($item, \dirname($cacheDir)) && !is_file($item)) || ($buildDir && str_starts_with($item, \dirname($buildDir)) && !is_file($item))) {
                        throw new \LogicException(\sprintf('"%s::warmUp()" should return a list of files or classes but "%s" is none of them.', $warmer::class, $item));
                    }
                    $preload[] = $item;
                }

                if ($io?->isDebug()) {
                    $io->info(\sprintf('"%s" completed in %0.2fms.', $warmer::class, 1000 * (microtime(true) - $start)));
                }
            }
        } finally {
            if ($collectDeprecations) {
                restore_error_handler();

                if (is_file($this->deprecationLogsFilepath)) {
                    $previousLogs = unserialize(file_get_contents($this->deprecationLogsFilepath), ['allowed_classes' => false]);
                    if (\is_array($previousLogs)) {
                        $collectedLogs = array_merge($previousLogs, $collectedLogs);
                    }
                }

                file_put_contents($this->deprecationLogsFilepath, serialize(array_values($collectedLogs)));
            }
        }

        return array_values(array_unique($preload));
    }

    public function isOptional(): bool
    {
        return false;
    }
}
