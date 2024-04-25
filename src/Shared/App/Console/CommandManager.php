<?php

declare(strict_types=1);

namespace Lightit\Shared\App\Console;

use Lightit\Shared\App\Console\Commands\TestCommand;

class CommandManager
{
    /**
     * @return string[]
     */
    public static function getCommands(): array
    {
        return [
            TestCommand::class,
        ];
    }
}
