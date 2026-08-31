<?php

declare(strict_types=1);

namespace NunoMaduro\Collision;

use SebastianBergmann\CodeCoverage\CodeCoverage;
use SebastianBergmann\CodeCoverage\Node\Directory;
use SebastianBergmann\CodeCoverage\Node\File;
use SebastianBergmann\CodeCoverage\Report\Facade;
use SebastianBergmann\Environment\Runtime;
use Symfony\Component\Console\Output\OutputInterface;

use function Termwind\render;
use function Termwind\renderUsing;
use function Termwind\terminal;

/**
 * @internal
 */
final class Coverage
{
    /**
     * Returns the coverage path.
     */
    public static function getPath(): string
    {
        return implode(DIRECTORY_SEPARATOR, [
            dirname(__DIR__),
            '.temp',
            'coverage',
        ]);
    }

    /**
     * Runs true there is any code coverage driver available.
     */
    public static function isAvailable(): bool
    {
        $runtime = new Runtime;

        if (! $runtime->canCollectCodeCoverage()) {
            return false;
        }

        if ($runtime->hasPCOV() || $runtime->hasPHPDBGCodeCoverage()) {
            return true;
        }

        if (self::usingXdebug()) {
            $mode = getenv('XDEBUG_MODE') ?: ini_get('xdebug.mode');

            return $mode && in_array('coverage', explode(',', $mode), true);
        }

        return true;
    }

    /**
     * If the user is using Xdebug.
     */
    public static function usingXdebug(): bool
    {
        return (new Runtime)->hasXdebug();
    }

    /**
     * Reports the code coverage report to the
     * console and returns the result in float.
     */
    public static function report(OutputInterface $output, bool $hideFullCoverage = false): float
    {
        if (! file_exists($reportPath = self::getPath())) {
            if (self::usingXdebug()) {
                $output->writeln(
                    "  <fg=black;bg=yellow;options=bold> WARN </> Unable to get coverage using Xdebug. Did you set <href=https://xdebug.org/docs/code_coverage#mode>Xdebug's coverage mode</>?</>",
                );

                return 0.0;
            }

            $output->writeln(
                '  <fg=black;bg=yellow;options=bold> WARN </> No coverage driver detected.</> Did you install <href=https://xdebug.org/>Xdebug</> or <href=https://github.com/krakjoe/pcov>PCOV</>?',
            );

            return 0.0;
        }

        /** @var CodeCoverage $codeCoverage */
        $codeCoverage = require $reportPath;
        unlink($reportPath);

        // @phpstan-ignore-next-line
        if (is_array($codeCoverage)) {
            /** @var Facade $test */
            $facade = Facade::fromSerializedData($codeCoverage); // @phpstan-ignore-line

            /** @var Directory<File|Directory> $report */
            $report = (fn () => $this->report)->call($facade); // @phpstan-ignore-line
        } else {
            /** @var Directory<File|Directory> $report */
            $report = $codeCoverage->getReport();
        }

        $totalCoverage = $report->percentageOfExecutedLines();

        foreach ($report->getIterator() as $file) {
            if (! $file instanceof File) {
                continue;
            }
            $dirname = dirname($file->id());
            $basename = basename($file->id(), '.php');

            $name = $dirname === '.' ? $basename : implode(DIRECTORY_SEPARATOR, [
                $dirname,
                $basename,
            ]);

            $percentage = $file->numberOfExecutableLines() === 0
                ? '100.0'
                : number_format($file->percentageOfExecutedLines()->asFloat(), 1, '.', '');

            if ($percentage === '100.0' && $hideFullCoverage) {
                continue;
            }

            $uncoveredLines = '';

            $percentageOfExecutedLinesAsString = $file->percentageOfExecutedLines()->asString();

            if (! in_array($percentageOfExecutedLinesAsString, ['0.00%', '100.00%', '100.0%', ''], true)) {
                $uncoveredLines = trim(implode(', ', self::getMissingCoverage($file)));
                $uncoveredLines = sprintf('<span>%s</span>', $uncoveredLines).' <span class="text-gray"> / </span>';
            }

            $color = $percentage === '100.0' ? 'green' : ($percentage === '0.0' ? 'red' : 'yellow');

            $truncateAt = max(1, terminal()->width() - 12);

            renderUsing($output);
            render(<<<HTML
                <div class="flex mx-2">
                    <span class="truncate-{$truncateAt}">{$name}</span>
                    <span class="flex-1 content-repeat-[.] text-gray mx-1"></span>
                    <span class="text-{$color}">$uncoveredLines {$percentage}%</span>
                </div>
            HTML);
        }

        $totalCoverageAsString = $totalCoverage->asFloat() === 0.0
            ? '0.0'
            : number_format($totalCoverage->asFloat(), 1, '.', '');

        renderUsing($output);
        render(<<<HTML
            <div class="mx-2">
                <hr class="text-gray" />
                <div class="w-full text-right">
                    <span class="ml-1 font-bold">Total: {$totalCoverageAsString} %</span>
                </div>
            </div>
        HTML);

        return $totalCoverage->asFloat();
    }

    /**
     * Generates an array of missing coverage on the following format:.
     *
     * ```
     * ['11', '20..25', '50', '60..80'];
     * ```
     *
     * @param  File  $file
     * @return array<int, string>
     */
    public static function getMissingCoverage($file): array
    {
        $shouldBeNewLine = true;

        $eachLine = function (array $array, array $tests, int $line) use (&$shouldBeNewLine): array {
            if ($tests !== []) {
                $shouldBeNewLine = true;

                return $array;
            }

            if ($shouldBeNewLine) {
                $array[] = (string) $line;
                $shouldBeNewLine = false;

                return $array;
            }

            $lastKey = count($array) - 1;

            if (array_key_exists($lastKey, $array) && str_contains((string) $array[$lastKey], '..')) {
                [$from] = explode('..', (string) $array[$lastKey]);
                $array[$lastKey] = $line > $from ? sprintf('%s..%s', $from, $line) : sprintf('%s..%s', $line, $from);

                return $array;
            }

            $array[$lastKey] = sprintf('%s..%s', $array[$lastKey], $line);

            return $array;
        };

        $array = [];
        foreach (array_filter($file->lineCoverageData(), 'is_array') as $line => $tests) {
            $array = $eachLine($array, $tests, $line);
        }

        return $array;
    }
}
