<?php

namespace Illuminate\Support\Facades;

use Illuminate\Contracts\Console\Kernel as ConsoleKernelContract;

/**
 * @method static int handle(\Symfony\Component\Console\Input\InputInterface $input, \Symfony\Component\Console\Output\OutputInterface|null $output = null)
 * @method static void terminate(\Symfony\Component\Console\Input\InputInterface $input, int $status)
 * @method static void whenCommandLifecycleIsLongerThan(\DateTimeInterface|\Carbon\CarbonInterval|float|int $threshold, callable $handler)
 * @method static \Illuminate\Support\Carbon|null commandStartedAt()
 * @method static \Illuminate\Console\Scheduling\Schedule resolveConsoleSchedule()
 * @method static \Illuminate\Foundation\Console\ClosureCommand command(string $signature, \Closure $callback)
 * @method static void registerCommand(\Symfony\Component\Console\Command\Command $command)
 * @method static int call(\Symfony\Component\Console\Command\Command|string $command, array $parameters = [], \Symfony\Component\Console\Output\OutputInterface|null $outputBuffer = null)
 * @method static \Illuminate\Foundation\Bus\PendingDispatch queue(string $command, array $parameters = [])
 * @method static array all()
 * @method static string output()
 * @method static void bootstrap()
 * @method static void bootstrapWithoutBootingProviders()
 * @method static void setArtisan(\Illuminate\Console\Application|null $artisan)
 * @method static \Illuminate\Foundation\Console\Kernel addCommands(array $commands)
 * @method static \Illuminate\Foundation\Console\Kernel addCommandPaths(array $paths)
 * @method static \Illuminate\Foundation\Console\Kernel addCommandRoutePaths(array $paths)
 *
 * @see \Illuminate\Foundation\Console\Kernel
 */
class Artisan extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return ConsoleKernelContract::class;
    }
}
