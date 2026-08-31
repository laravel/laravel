<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'lang:publish')]
class LangPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lang:publish
                    {--existing : Publish and overwrite only the files that have already been published}
                    {--force : Overwrite any existing files}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all language files that are available for customization';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! is_dir($langPath = $this->laravel->basePath('lang/en'))) {
            (new Filesystem)->makeDirectory($langPath, recursive: true);
        }

        $stubs = [
            realpath(__DIR__.'/../../Translation/lang/en/auth.php') => 'auth.php',
            realpath(__DIR__.'/../../Translation/lang/en/pagination.php') => 'pagination.php',
            realpath(__DIR__.'/../../Translation/lang/en/passwords.php') => 'passwords.php',
            realpath(__DIR__.'/../../Translation/lang/en/validation.php') => 'validation.php',
        ];

        foreach ($stubs as $from => $to) {
            $to = $langPath.DIRECTORY_SEPARATOR.ltrim($to, DIRECTORY_SEPARATOR);

            if ((! $this->option('existing') && (! file_exists($to) || $this->option('force')))
                || ($this->option('existing') && file_exists($to))) {
                file_put_contents($to, file_get_contents($from));
            }
        }

        $this->components->info('Language files published successfully.');
    }
}
