<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;

#[AsCommand(name: 'reload')]
class ReloadCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'reload';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reload running services';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->components->info('Reloading services.');

        $exceptions = Collection::wrap(explode(',', $this->option('except') ?? ''))
            ->map(fn ($except) => trim($except))
            ->filter()
            ->unique()
            ->flip();

        $tasks = Collection::wrap($this->getReloadTasks())
            ->reject(fn ($command, $key) => $exceptions->hasAny([$command, $key]))
            ->toArray();

        foreach ($tasks as $description => $command) {
            $this->components->task($description, fn () => $this->callSilently($command) == 0);
        }

        $this->newLine();
    }

    /**
     * Get the commands that should be reloaded.
     *
     * @return array
     */
    public function getReloadTasks()
    {
        return [
            'queue' => 'queue:restart',
            'schedule' => 'schedule:interrupt',
            ...ServiceProvider::$reloadCommands,
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['except', 'e', InputOption::VALUE_OPTIONAL, 'The commands to skip'],
        ];
    }
}
