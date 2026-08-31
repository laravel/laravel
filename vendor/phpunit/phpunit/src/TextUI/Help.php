<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\TextUI;

use const PHP_EOL;
use function count;
use function defined;
use function explode;
use function max;
use function preg_replace_callback;
use function str_pad;
use function str_repeat;
use function strlen;
use function wordwrap;
use PHPUnit\Util\Color;
use SebastianBergmann\Environment\Console;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final class Help
{
    private const LEFT_MARGIN              = '  ';
    private int $lengthOfLongestOptionName = 0;
    private readonly int $columnsAvailableForDescription;
    private bool $hasColor;

    public function __construct(?int $width = null, ?bool $withColor = null)
    {
        if ($width === null) {
            $width = (new Console)->getNumberOfColumns();
        }

        if ($withColor === null) {
            $this->hasColor = (new Console)->hasColorSupport();
        } else {
            $this->hasColor = $withColor;
        }

        foreach ($this->elements() as $options) {
            foreach ($options as $option) {
                if (isset($option['arg'])) {
                    $this->lengthOfLongestOptionName = max($this->lengthOfLongestOptionName, strlen($option['arg']));
                }
            }
        }

        $this->columnsAvailableForDescription = $width - $this->lengthOfLongestOptionName - 4;
    }

    public function generate(): string
    {
        if ($this->hasColor) {
            return $this->writeWithColor();
        }

        return $this->writeWithoutColor();
    }

    private function writeWithoutColor(): string
    {
        $buffer = '';

        foreach ($this->elements() as $section => $options) {
            $buffer .= "{$section}:" . PHP_EOL;

            if ($section !== 'Usage') {
                $buffer .= PHP_EOL;
            }

            foreach ($options as $option) {
                if (isset($option['spacer'])) {
                    $buffer .= PHP_EOL;
                }

                if (isset($option['text'])) {
                    $buffer .= self::LEFT_MARGIN . $option['text'] . PHP_EOL;
                }

                if (isset($option['arg'])) {
                    $arg = str_pad($option['arg'], $this->lengthOfLongestOptionName);

                    $buffer .= self::LEFT_MARGIN . $arg . ' ' . $option['desc'] . PHP_EOL;
                }
            }

            $buffer .= PHP_EOL;
        }

        return $buffer;
    }

    private function writeWithColor(): string
    {
        $buffer = '';

        foreach ($this->elements() as $section => $options) {
            $buffer .= Color::colorize('fg-yellow', "{$section}:") . PHP_EOL;

            if ($section !== 'Usage') {
                $buffer .= PHP_EOL;
            }

            foreach ($options as $option) {
                if (isset($option['spacer'])) {
                    $buffer .= PHP_EOL;
                }

                if (isset($option['text'])) {
                    $buffer .= self::LEFT_MARGIN . $option['text'] . PHP_EOL;
                }

                if (isset($option['arg'])) {
                    $arg = Color::colorize('fg-green', str_pad($option['arg'], $this->lengthOfLongestOptionName));
                    $arg = preg_replace_callback(
                        '/(<[^>]+>)/',
                        static fn ($matches) => Color::colorize('fg-cyan', $matches[0]),
                        $arg,
                    );

                    $desc = explode(PHP_EOL, wordwrap($option['desc'], $this->columnsAvailableForDescription, PHP_EOL));

                    $buffer .= self::LEFT_MARGIN . $arg . ' ' . $desc[0] . PHP_EOL;

                    for ($i = 1; $i < count($desc); $i++) {
                        $buffer .= str_repeat(' ', $this->lengthOfLongestOptionName + 3) . $desc[$i] . PHP_EOL;
                    }
                }
            }

            $buffer .= PHP_EOL;
        }

        return $buffer;
    }

    /**
     * @return array<non-empty-string, non-empty-list<array{arg: non-empty-string, desc: non-empty-string}|array{spacer: ''}|array{text: non-empty-string}>>
     */
    private function elements(): array
    {
        $elements = [
            'Usage' => [
                ['text' => 'phpunit [options] <directory|file> ...'],
            ],

            'Configuration' => [
                ['arg' => '--bootstrap <file>', 'desc' => 'A PHP script that is included before the tests run'],
                ['arg' => '-c|--configuration <file>', 'desc' => 'Read configuration from XML file'],
                ['arg' => '--no-configuration', 'desc' => 'Ignore default configuration file (phpunit.xml)'],
                ['arg' => '--extension <class>', 'desc' => 'Register test runner extension with bootstrap <class>'],
                ['arg' => '--no-extensions', 'desc' => 'Do not register test runner extensions'],
                ['arg' => '--include-path <path(s)>', 'desc' => 'Prepend PHP\'s include_path with given path(s)'],
                ['arg' => '-d <key[=value]>', 'desc' => 'Sets a php.ini value'],
                ['arg' => '--cache-directory <dir>', 'desc' => 'Specify cache directory'],
                ['arg' => '--generate-configuration', 'desc' => 'Generate configuration file with suggested settings'],
                ['arg' => '--migrate-configuration', 'desc' => 'Migrate configuration file to current format'],
                ['arg' => '--generate-baseline <file>', 'desc' => 'Generate baseline for issues'],
                ['arg' => '--use-baseline <file>', 'desc' => 'Use baseline to ignore issues'],
                ['arg' => '--ignore-baseline', 'desc' => 'Do not use baseline to ignore issues'],
            ],

            'Selection' => [
                ['arg' => '--list-suites', 'desc' => 'List available test suites'],
                ['arg' => '--testsuite <name>', 'desc' => 'Only run tests from the specified test suite(s)'],
                ['arg' => '--exclude-testsuite <name>', 'desc' => 'Exclude tests from the specified test suite(s)'],
                ['arg' => '--list-groups', 'desc' => 'List available test groups'],
                ['arg' => '--group <name>', 'desc' => 'Only run tests from the specified group(s)'],
                ['arg' => '--exclude-group <name>', 'desc' => 'Exclude tests from the specified group(s)'],
                ['arg' => '--covers <name>', 'desc' => 'Only run tests that intend to cover <name>'],
                ['arg' => '--uses <name>', 'desc' => 'Only run tests that intend to use <name>'],
                ['arg' => '--requires-php-extension <name>', 'desc' => 'Only run tests that require PHP extension <name>'],
                ['arg' => '--list-test-files', 'desc' => 'List available test files'],
                ['arg' => '--list-tests', 'desc' => 'List available tests'],
                ['arg' => '--list-tests-xml <file>', 'desc' => 'List available tests in XML format'],
                ['arg' => '--filter <pattern>', 'desc' => 'Filter which tests to run'],
                ['arg' => '--exclude-filter <pattern>', 'desc' => 'Exclude tests for the specified filter pattern'],
                ['arg' => '--test-suffix <suffixes>', 'desc' => 'Only search for test in files with specified suffix(es). Default: Test.php,.phpt'],
            ],

            'Execution' => [
                ['arg' => '--process-isolation', 'desc' => 'Run each test in a separate PHP process'],
                ['arg'    => '--globals-backup', 'desc' => 'Backup and restore $GLOBALS for each test'],
                ['arg'    => '--static-backup', 'desc' => 'Backup and restore static properties for each test'],
                ['spacer' => ''],

                ['arg'    => '--strict-coverage', 'desc' => 'Be strict about code coverage metadata'],
                ['arg'    => '--strict-global-state', 'desc' => 'Be strict about changes to global state'],
                ['arg'    => '--disallow-test-output', 'desc' => 'Be strict about output during tests'],
                ['arg'    => '--enforce-time-limit', 'desc' => 'Enforce time limit based on test size'],
                ['arg'    => '--default-time-limit <sec>', 'desc' => 'Timeout in seconds for tests that have no declared size'],
                ['arg'    => '--do-not-report-useless-tests', 'desc' => 'Do not report tests that do not test anything'],
                ['spacer' => ''],

                ['arg'    => '--stop-on-defect', 'desc' => 'Stop after first error, failure, warning, or risky test'],
                ['arg'    => '--stop-on-error', 'desc' => 'Stop after first error'],
                ['arg'    => '--stop-on-failure', 'desc' => 'Stop after first failure'],
                ['arg'    => '--stop-on-warning', 'desc' => 'Stop after first warning'],
                ['arg'    => '--stop-on-risky', 'desc' => 'Stop after first risky test'],
                ['arg'    => '--stop-on-deprecation', 'desc' => 'Stop after first test that triggered a deprecation'],
                ['arg'    => '--stop-on-notice', 'desc' => 'Stop after first test that triggered a notice'],
                ['arg'    => '--stop-on-skipped', 'desc' => 'Stop after first skipped test'],
                ['arg'    => '--stop-on-incomplete', 'desc' => 'Stop after first incomplete test'],
                ['spacer' => ''],

                ['arg'    => '--fail-on-empty-test-suite', 'desc' => 'Signal failure using shell exit code when no tests were run'],
                ['arg'    => '--fail-on-warning', 'desc' => 'Signal failure using shell exit code when a warning was triggered'],
                ['arg'    => '--fail-on-risky', 'desc' => 'Signal failure using shell exit code when a test was considered risky'],
                ['arg'    => '--fail-on-deprecation', 'desc' => 'Signal failure using shell exit code when a deprecation was triggered'],
                ['arg'    => '--fail-on-phpunit-deprecation', 'desc' => 'Signal failure using shell exit code when a PHPUnit deprecation was triggered'],
                ['arg'    => '--fail-on-phpunit-warning', 'desc' => 'Signal failure using shell exit code when a PHPUnit warning was triggered'],
                ['arg'    => '--fail-on-notice', 'desc' => 'Signal failure using shell exit code when a notice was triggered'],
                ['arg'    => '--fail-on-skipped', 'desc' => 'Signal failure using shell exit code when a test was skipped'],
                ['arg'    => '--fail-on-incomplete', 'desc' => 'Signal failure using shell exit code when a test was marked incomplete'],
                ['arg'    => '--fail-on-all-issues', 'desc' => 'Signal failure using shell exit code when an issue is triggered'],
                ['spacer' => ''],

                ['arg'    => '--do-not-fail-on-empty-test-suite', 'desc' => 'Do not signal failure using shell exit code when no tests were run'],
                ['arg'    => '--do-not-fail-on-warning', 'desc' => 'Do not signal failure using shell exit code when a warning was triggered'],
                ['arg'    => '--do-not-fail-on-risky', 'desc' => 'Do not signal failure using shell exit code when a test was considered risky'],
                ['arg'    => '--do-not-fail-on-deprecation', 'desc' => 'Do not signal failure using shell exit code when a deprecation was triggered'],
                ['arg'    => '--do-not-fail-on-phpunit-deprecation', 'desc' => 'Do not signal failure using shell exit code when a PHPUnit deprecation was triggered'],
                ['arg'    => '--do-not-fail-on-phpunit-warning', 'desc' => 'Do not signal failure using shell exit code when a PHPUnit warning was triggered'],
                ['arg'    => '--do-not-fail-on-notice', 'desc' => 'Do not signal failure using shell exit code when a notice was triggered'],
                ['arg'    => '--do-not-fail-on-skipped', 'desc' => 'Do not signal failure using shell exit code when a test was skipped'],
                ['arg'    => '--do-not-fail-on-incomplete', 'desc' => 'Do not signal failure using shell exit code when a test was marked incomplete'],
                ['spacer' => ''],

                ['arg'    => '--cache-result', 'desc' => 'Write test results to cache file'],
                ['arg'    => '--do-not-cache-result', 'desc' => 'Do not write test results to cache file'],
                ['spacer' => ''],

                ['arg' => '--order-by <order>', 'desc' => 'Run tests in order: default|defects|depends|duration|no-depends|random|reverse|size'],
                ['arg' => '--random-order-seed <N>', 'desc' => 'Use the specified random seed when running tests in random order'],
            ],

            'Reporting' => [
                ['arg' => '--colors <flag>', 'desc' => 'Use colors in output ("never", "auto" or "always")'],
                ['arg'    => '--columns <n>', 'desc' => 'Number of columns to use for progress output'],
                ['arg'    => '--columns max', 'desc' => 'Use maximum number of columns for progress output'],
                ['arg'    => '--stderr', 'desc' => 'Write to STDERR instead of STDOUT'],
                ['spacer' => ''],

                ['arg'    => '--no-progress', 'desc' => 'Disable output of test execution progress'],
                ['arg'    => '--no-results', 'desc' => 'Disable output of test results'],
                ['arg'    => '--no-output', 'desc' => 'Disable all output'],
                ['spacer' => ''],

                ['arg'    => '--display-incomplete', 'desc' => 'Display details for incomplete tests'],
                ['arg'    => '--display-skipped', 'desc' => 'Display details for skipped tests'],
                ['arg'    => '--display-deprecations', 'desc' => 'Display details for deprecations triggered by tests'],
                ['arg'    => '--display-phpunit-deprecations', 'desc' => 'Display details for PHPUnit deprecations'],
                ['arg'    => '--display-errors', 'desc' => 'Display details for errors triggered by tests'],
                ['arg'    => '--display-notices', 'desc' => 'Display details for notices triggered by tests'],
                ['arg'    => '--display-warnings', 'desc' => 'Display details for warnings triggered by tests'],
                ['arg'    => '--display-all-issues', 'desc' => 'Display details for all issues that are triggered'],
                ['arg'    => '--reverse-list', 'desc' => 'Print defects in reverse order'],
                ['spacer' => ''],

                ['arg'    => '--teamcity', 'desc' => 'Replace default progress and result output with TeamCity format'],
                ['arg'    => '--testdox', 'desc' => 'Replace default result output with TestDox format'],
                ['arg'    => '--testdox-summary', 'desc' => 'Repeat TestDox output for tests with errors, failures, or issues'],
                ['spacer' => ''],

                ['arg' => '--debug', 'desc' => 'Replace default progress and result output with debugging information'],
            ],

            'Logging' => [
                ['arg' => '--log-junit <file>', 'desc' => 'Write test results in JUnit XML format to file'],
                ['arg' => '--log-teamcity <file>', 'desc' => 'Write test results in TeamCity format to file'],
                ['arg' => '--testdox-html <file>', 'desc' => 'Write test results in TestDox format (HTML) to file'],
                ['arg' => '--testdox-text <file>', 'desc' => 'Write test results in TestDox format (plain text) to file'],
                ['arg' => '--log-events-text <file>', 'desc' => 'Stream events as plain text to file'],
                ['arg' => '--log-events-verbose-text <file>', 'desc' => 'Stream events as plain text with extended information to file'],
                ['arg' => '--no-logging', 'desc' => 'Ignore logging configured in the XML configuration file'],
            ],

            'Code Coverage' => [
                ['arg' => '--coverage-clover <file>', 'desc' => 'Write code coverage report in Clover XML format to file'],
                ['arg' => '--coverage-cobertura <file>', 'desc' => 'Write code coverage report in Cobertura XML format to file'],
                ['arg' => '--coverage-crap4j <file>', 'desc' => 'Write code coverage report in Crap4J XML format to file'],
                ['arg' => '--coverage-html <dir>', 'desc' => 'Write code coverage report in HTML format to directory'],
                ['arg' => '--coverage-php <file>', 'desc' => 'Write serialized code coverage data to file'],
                ['arg' => '--coverage-text=<file>', 'desc' => 'Write code coverage report in text format to file [default: standard output]'],
                ['arg' => '--only-summary-for-coverage-text', 'desc' => 'Option for code coverage report in text format: only show summary'],
                ['arg' => '--show-uncovered-for-coverage-text', 'desc' => 'Option for code coverage report in text format: show uncovered files'],
                ['arg' => '--coverage-xml <dir>', 'desc' => 'Write code coverage report in XML format to directory'],
                ['arg' => '--warm-coverage-cache', 'desc' => 'Warm static analysis cache'],
                ['arg' => '--coverage-filter <dir>', 'desc' => 'Include <dir> in code coverage reporting'],
                ['arg' => '--path-coverage', 'desc' => 'Report path coverage in addition to line coverage'],
                ['arg' => '--disable-coverage-ignore', 'desc' => 'Disable metadata for ignoring code coverage'],
                ['arg' => '--no-coverage', 'desc' => 'Ignore code coverage reporting configured in the XML configuration file'],
            ],
        ];

        if (defined('__PHPUNIT_PHAR__')) {
            $elements['PHAR'] = [
                ['arg' => '--manifest', 'desc' => 'Print Software Bill of Materials (SBOM) in plain-text format'],
                ['arg' => '--sbom', 'desc' => 'Print Software Bill of Materials (SBOM) in CycloneDX XML format'],
                ['arg' => '--composer-lock', 'desc' => 'Print composer.lock file used to build the PHAR'],
            ];
        }

        $elements['Miscellaneous'] = [
            ['arg' => '-h|--help', 'desc' => 'Prints this usage information'],
            ['arg' => '--version', 'desc' => 'Prints the version and exits'],
            ['arg' => '--atleast-version <min>', 'desc' => 'Checks that version is greater than <min> and exits'],
            ['arg' => '--check-version', 'desc' => 'Checks whether PHPUnit is the latest version and exits'],
            ['arg' => '--check-php-configuration', 'desc' => 'Checks whether PHP configuration follows best practices'],
        ];

        return $elements;
    }
}
