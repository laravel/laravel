<?php

namespace App\Console\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeDtoCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dto {name : The name of the DTO class}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'DTO';

    /**
     * Get the stub file for the generator.
     * otherwise fall back to the bundled stub next to this class.
     *
     * @return string
     */
    protected function getStub()
    {
        $projectStub = base_path('stubs/dto.stub');

        if ($this->files->exists($projectStub)) {
            return $projectStub;
        }

        // fallback to a stub shipped with the app (optional)
        return __DIR__.'/../../stubs/dto.stub';
    }

    /**
     * Default namespace for the generated class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\DTOs';
    }
}
