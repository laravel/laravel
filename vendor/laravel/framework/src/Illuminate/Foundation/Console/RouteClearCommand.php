<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'route:clear')]
class RouteClearCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'route:clear';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the route cache file';

    /**
     * The filesystem instance.
     *
     * @var \Illuminate\Filesystem\Filesystem
     */
    protected $files;

    /**
     * Create a new route clear command instance.
     *
     * @param  \Illuminate\Filesystem\Filesystem  $files
     */
    public function __construct(Filesystem $files)
    {
        parent::__construct();

        $this->files = $files;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->files->delete($this->laravel->getCachedRoutesPath());

        $this->components->info('Route cache cleared successfully.');
    }
}
