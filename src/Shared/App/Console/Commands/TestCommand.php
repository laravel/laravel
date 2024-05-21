<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Console\Commands;

use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class TestCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:test-command';

    /**
     * @var string
     */
    protected $description = 'Command Tester';

    public function handle(LoggerInterface $logger): int
    {
        $logger->info('Hi, Im am Logger! How are u?');
        $this->info('Done');

        return Command::SUCCESS;
    }
}
