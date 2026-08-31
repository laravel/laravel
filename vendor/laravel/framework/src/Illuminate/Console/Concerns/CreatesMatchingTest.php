<?php

namespace Illuminate\Console\Concerns;

use Illuminate\Support\Stringable;
use Symfony\Component\Console\Input\InputOption;

trait CreatesMatchingTest
{
    /**
     * Add the standard command options for generating matching tests.
     *
     * @return void
     */
    protected function addTestOptions()
    {
        foreach (['test' => 'Test', 'pest' => 'Pest', 'phpunit' => 'PHPUnit'] as $option => $name) {
            $this->getDefinition()->addOption(new InputOption(
                $option,
                null,
                InputOption::VALUE_NONE,
                "Generate an accompanying {$name} test for the {$this->type}"
            ));
        }
    }

    /**
     * Create the matching test case if requested.
     *
     * @param  string  $path
     * @return bool
     */
    protected function handleTestCreation($path)
    {
        if (! $this->option('test') && ! $this->option('pest') && ! $this->option('phpunit')) {
            return false;
        }

        return $this->call('make:test', [
            'name' => (new Stringable($path))->after($this->laravel['path'])->beforeLast('.php')->append('Test')->replace('\\', '/'),
            '--pest' => $this->option('pest'),
            '--phpunit' => $this->option('phpunit'),
            '--force' => $this->hasOption('force') && $this->option('force'),
        ]) == 0;
    }
}
