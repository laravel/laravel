<?php

namespace Illuminate\Database\Console\Seeds;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'make:seeder')]
class SeederMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:seeder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new seeder class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Seeder';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        parent::handle();
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/seeder.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return is_file($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = str_replace('\\', '/', Str::replaceFirst($this->rootNamespace(), '', $name));

        if (is_dir($this->laravel->databasePath().'/seeds')) {
            return $this->laravel->databasePath().'/seeds/'.$name.'.php';
        }

        return $this->laravel->databasePath().'/seeders/'.$name.'.php';
    }

    /**
     * Get the root namespace for the class.
     *
     * @return string
     */
    protected function rootNamespace()
    {
        return 'Database\Seeders\\';
    }
}
