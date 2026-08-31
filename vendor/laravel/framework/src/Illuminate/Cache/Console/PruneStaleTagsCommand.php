<?php

namespace Illuminate\Cache\Console;

use Illuminate\Cache\CacheManager;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;

#[AsCommand(name: 'cache:prune-stale-tags')]
class PruneStaleTagsCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'cache:prune-stale-tags';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune stale cache tags from the cache (Redis only)';

    /**
     * Execute the console command.
     *
     * @param  \Illuminate\Cache\CacheManager  $cache
     * @return int|null
     */
    public function handle(CacheManager $cache)
    {
        $cache = $cache->store($this->argument('store'));

        if (method_exists($cache->getStore(), 'flushStaleTags')) {
            $cache->flushStaleTags();
        }

        $this->components->info('Stale cache tags pruned successfully.');
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['store', InputArgument::OPTIONAL, 'The name of the store you would like to prune tags from'],
        ];
    }
}
