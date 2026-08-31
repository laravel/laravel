<?php declare(strict_types=1);
/*
 * This file is part of PHPUnit.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace PHPUnit\Util\PHP;

use const PHP_BINARY;
use const PHP_SAPI;
use function array_keys;
use function array_merge;
use function assert;
use function fclose;
use function file_get_contents;
use function file_put_contents;
use function function_exists;
use function fwrite;
use function ini_get_all;
use function is_array;
use function is_file;
use function is_resource;
use function proc_close;
use function proc_open;
use function str_starts_with;
use function stream_get_contents;
use function sys_get_temp_dir;
use function tempnam;
use function trim;
use function unlink;
use function xdebug_is_debugger_active;
use PHPUnit\Event\Facade;
use PHPUnit\Event\Facade as EventFacade;
use PHPUnit\Framework\ChildProcessResultProcessor;
use PHPUnit\Framework\Test;
use PHPUnit\Runner\CodeCoverage;
use SebastianBergmann\Environment\Runtime;

/**
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise for PHPUnit
 *
 * @internal This class is not covered by the backward compatibility promise for PHPUnit
 */
final readonly class JobRunner
{
    private ChildProcessResultProcessor $processor;

    public function __construct(ChildProcessResultProcessor $processor)
    {
        $this->processor = $processor;
    }

    /**
     * @param non-empty-string $processResultFile
     */
    public function runTestJob(Job $job, string $processResultFile, Test $test): void
    {
        $result = $this->run($job);

        $processResult = '';

        if (is_file($processResultFile)) {
            $processResult = file_get_contents($processResultFile);

            assert($processResult !== false);

            @unlink($processResultFile);
        }

        $this->processor->process(
            $test,
            $processResult,
            $result->stderr(),
        );

        EventFacade::emitter()->testRunnerFinishedChildProcess($result->stdout(), $result->stderr());
    }

    /**
     * @throws PhpProcessException
     */
    public function run(Job $job): Result
    {
        $temporaryFile = null;

        if ($job->hasInput()) {
            $temporaryFile = tempnam(sys_get_temp_dir(), 'phpunit_');

            if ($temporaryFile === false ||
                file_put_contents($temporaryFile, $job->code()) === false) {
                // @codeCoverageIgnoreStart
                throw new PhpProcessException(
                    'Unable to write temporary file',
                );
                // @codeCoverageIgnoreEnd
            }

            $job = new Job(
                $job->input(),
                $job->phpSettings(),
                $job->environmentVariables(),
                $job->arguments(),
                null,
                $job->redirectErrors(),
                $job->requiresXdebug(),
            );
        }

        assert($temporaryFile !== '');

        return $this->runProcess($job, $temporaryFile);
    }

    /**
     * @param ?non-empty-string $temporaryFile
     *
     * @throws PhpProcessException
     */
    private function runProcess(Job $job, ?string $temporaryFile): Result
    {
        $environmentVariables = null;

        if ($job->hasEnvironmentVariables()) {
            /** @phpstan-ignore nullCoalesce.variable */
            $environmentVariables = $_SERVER ?? [];

            unset($environmentVariables['argv'], $environmentVariables['argc']);

            $environmentVariables = array_merge($environmentVariables, $job->environmentVariables());

            foreach ($environmentVariables as $key => $value) {
                if (is_array($value)) {
                    unset($environmentVariables[$key]);
                }
            }

            unset($key, $value);
        }

        $pipeSpec = [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ];

        if ($job->redirectErrors()) {
            $pipeSpec[2] = ['redirect', 1];
        }

        $process = proc_open(
            $this->buildCommand($job, $temporaryFile),
            $pipeSpec,
            $pipes,
            null,
            $environmentVariables,
        );

        if (!is_resource($process)) {
            // @codeCoverageIgnoreStart
            throw new PhpProcessException(
                'Unable to spawn worker process',
            );
            // @codeCoverageIgnoreEnd
        }

        Facade::emitter()->testRunnerStartedChildProcess();

        fwrite($pipes[0], $job->code());
        fclose($pipes[0]);

        $stdout = '';
        $stderr = '';

        if (isset($pipes[1])) {
            $stdout = stream_get_contents($pipes[1]);

            fclose($pipes[1]);
        }

        if (isset($pipes[2])) {
            $stderr = stream_get_contents($pipes[2]);

            fclose($pipes[2]);
        }

        proc_close($process);

        if ($temporaryFile !== null) {
            unlink($temporaryFile);
        }

        assert($stdout !== false);
        assert($stderr !== false);

        return new Result($stdout, $stderr);
    }

    /**
     * @return non-empty-list<string>
     */
    private function buildCommand(Job $job, ?string $file): array
    {
        $runtime     = new Runtime;
        $command     = [PHP_BINARY];
        $phpSettings = $job->phpSettings();

        $xdebugModeConfiguredExplicitly = false;

        foreach ($phpSettings as $phpSetting) {
            if (str_starts_with($phpSetting, 'xdebug.mode')) {
                $xdebugModeConfiguredExplicitly = true;

                break;
            }
        }

        if ($runtime->hasPCOV()) {
            $pcovSettings = ini_get_all('pcov');

            assert($pcovSettings !== false);

            $phpSettings = array_merge(
                $phpSettings,
                $runtime->getCurrentSettings(
                    array_keys($pcovSettings),
                ),
            );
        } elseif ($runtime->hasXdebug()) {
            assert(function_exists('xdebug_is_debugger_active'));

            $xdebugSettings = ini_get_all('xdebug');

            assert($xdebugSettings !== false);

            $phpSettings = array_merge(
                $phpSettings,
                $runtime->getCurrentSettings(
                    array_keys($xdebugSettings),
                ),
            );

            if (
                !$xdebugModeConfiguredExplicitly &&
                !CodeCoverage::instance()->isActive() &&
                xdebug_is_debugger_active() === false &&
                !$job->requiresXdebug()
            ) {
                // disable xdebug to speedup test execution
                $phpSettings['xdebug.mode'] = 'xdebug.mode=off';
            }
        }

        $command = array_merge($command, $this->settingsToParameters($phpSettings));

        if (PHP_SAPI === 'phpdbg') {
            $command[] = '-qrr';

            if ($file === null) {
                $command[] = 's=';
            }
        }

        if ($file !== null) {
            $command[] = '-f';
            $command[] = $file;
        }

        if ($job->hasArguments()) {
            if ($file === null) {
                $command[] = '--';
            }

            foreach ($job->arguments() as $argument) {
                $command[] = trim($argument);
            }
        }

        return $command;
    }

    /**
     * @param list<string> $settings
     *
     * @return list<string>
     */
    private function settingsToParameters(array $settings): array
    {
        $buffer = [];

        foreach ($settings as $setting) {
            $buffer[] = '-d';
            $buffer[] = $setting;
        }

        return $buffer;
    }
}
