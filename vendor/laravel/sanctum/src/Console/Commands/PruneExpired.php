<?php

namespace Laravel\Sanctum\Console\Commands;

use Illuminate\Console\Command;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'sanctum:prune-expired')]
class PruneExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sanctum:prune-expired {--hours=24 : The number of hours to retain expired Sanctum tokens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune tokens expired for more than specified number of hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $model = Sanctum::$personalAccessTokenModel;

        $hours = $this->option('hours');

        $this->components->task(
            'Pruning tokens with expired expires_at timestamps',
            fn () => $model::where('expires_at', '<', now()->subHours($hours))->delete()
        );

        if ($expiration = config('sanctum.expiration')) {
            $this->components->task(
                'Pruning tokens with expired expiration value based on configuration file',
                fn () => $model::where('created_at', '<', now()->subMinutes($expiration + ($hours * 60)))->delete()
            );
        } else {
            $this->components->warn('Expiration value not specified in configuration file.');
        }

        $this->components->info("Tokens expired for more than [$hours hours] pruned successfully.");

        return 0;
    }
}
