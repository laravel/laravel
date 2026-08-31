<?php

namespace Illuminate\Testing\Concerns;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\ParallelTesting;
use Illuminate\Testing\ParallelConsoleOutput;
use PHPUnit\TextUI\Configuration\PhpHandler;
use RuntimeException;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

trait RunsInParallel
{
    /**
     * The application resolver callback.
     *
     * @var \Closure|null
     */
    protected static $applicationResolver;

    /**
     * The runner resolver callback.
     *
     * @var \Closure|null
     */
    protected static $runnerResolver;

    /**
     * The original test runner options.
     *
     * @var \ParaTest\Runners\PHPUnit\Options|\ParaTest\Options
     */
    protected $options;

    /**
     * The output instance.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * The original test runner.
     *
     * @var \ParaTest\Runners\PHPUnit\RunnerInterface|\ParaTest\RunnerInterface
     */
    protected $runner;

    /**
     * Creates a new test runner instance.
     *
     * @param  \ParaTest\Runners\PHPUnit\Options|\ParaTest\Options  $options
     * @param  \Symfony\Component\Console\Output\OutputInterface  $output
     */
    public function __construct($options, OutputInterface $output)
    {
        $this->options = $options;

        if ($output instanceof ConsoleOutput) {
            $output = new ParallelConsoleOutput($output);
        }

        $runnerResolver = static::$runnerResolver ?: function ($options, OutputInterface $output) {
            $wrapperRunnerClass = class_exists(\ParaTest\WrapperRunner\WrapperRunner::class)
                ? \ParaTest\WrapperRunner\WrapperRunner::class
                : \ParaTest\Runners\PHPUnit\WrapperRunner::class;

            return new $wrapperRunnerClass($options, $output);
        };

        $this->runner = $runnerResolver($options, $output);
    }

    /**
     * Set the application resolver callback.
     *
     * @param  \Closure|null  $resolver
     * @return void
     */
    public static function resolveApplicationUsing($resolver)
    {
        static::$applicationResolver = $resolver;
    }

    /**
     * Set the runner resolver callback.
     *
     * @param  \Closure|null  $resolver
     * @return void
     */
    public static function resolveRunnerUsing($resolver)
    {
        static::$runnerResolver = $resolver;
    }

    /**
     * Runs the test suite.
     *
     * @return int
     */
    public function execute(): int
    {
        $configuration = $this->options instanceof \ParaTest\Options
            ? $this->options->configuration
            : $this->options->configuration();

        (new PhpHandler())->handle($configuration->php());

        $this->forEachProcess(function () {
            ParallelTesting::callSetUpProcessCallbacks();
        });

        try {
            $potentialExitCode = $this->runner->run();
        } finally {
            $this->forEachProcess(function () {
                ParallelTesting::callTearDownProcessCallbacks();
            });
        }

        return $potentialExitCode ?? $this->getExitCode();
    }

    /**
     * Returns the highest exit code encountered throughout the course of test execution.
     *
     * @return int
     */
    public function getExitCode(): int
    {
        return $this->runner->getExitCode();
    }

    /**
     * Apply the given callback for each process.
     *
     * @param  callable  $callback
     * @return void
     */
    protected function forEachProcess($callback)
    {
        $processes = $this->options instanceof \ParaTest\Options
            ? $this->options->processes
            : $this->options->processes();

        Collection::range(1, $processes)->each(function ($token) use ($callback) {
            tap($this->createApplication(), function ($app) use ($callback, $token) {
                ParallelTesting::resolveTokenUsing(fn () => $token);

                $callback($app);
            })->flush();
        });
    }

    /**
     * Creates the application.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     *
     * @throws \RuntimeException
     */
    protected function createApplication()
    {
        $applicationResolver = static::$applicationResolver ?: function () {
            if (trait_exists(\Tests\CreatesApplication::class)) {
                $applicationCreator = new class
                {
                    use \Tests\CreatesApplication;
                };

                return $applicationCreator->createApplication();
            } elseif (file_exists($path = (Application::inferBasePath().'/bootstrap/app.php'))) {
                $app = require $path;

                $app->make(Kernel::class)->bootstrap();

                return $app;
            }

            throw new RuntimeException('Parallel Runner unable to resolve application.');
        };

        return $applicationResolver();
    }
}
