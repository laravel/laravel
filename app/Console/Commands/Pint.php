<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class Pint extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pint
                            {--test : If you would like Pint to simply inspect your code for style errors without actually changing the files, you may use the --test option}
                            {--dirty : If you would like Pint to only modify the files that have uncommitted changes according to Git, you may use the --dirty option}
                            {argument?* : Additional arguments for ./vendor/bin/pint like app/Models to run Pint on specific directory}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run Pint command';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $options = [
            'test' => $this->option('test'),
            'dirty' => $this->option('dirty'),
            'verbose' => $this->option('verbose'),
        ];

        $additionalArguments = $this->argument('argument');

        $command = array_filter([
            './vendor/bin/pint',
            $options['test'] ? '--test' : null,
            $options['dirty'] ? '--dirty' : null,
            $options['verbose'] ? '-v' : null,
            ...$additionalArguments,
        ]);

        $process = new Process($command);

        $process->setTimeout(null);

        $process->run(function ($type, $output) {
            $this->output->write($output);
        });

        if (! $process->isSuccessful()) {
            $this->error($process->getErrorOutput());

            return 1;
        }

        $this->info('Pint command successfully executed');
    }
}
