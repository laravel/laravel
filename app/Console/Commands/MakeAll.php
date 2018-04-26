<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeAll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:all {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Controller, Requests, Migration, Factory, Seeder, Resource and Test for a Model';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $names = $this->get_names();
        $last_name = array_last(array_last($names));

        $this->make_model($last_name);
        $this->info('Model.......✓');
        $this->info('Migration...✓');

        $this->make_controller($last_name);
        $this->info('Controller..✓');

        $this->make_requests($names);
        $this->info('Requests....✓');

        //$this->make_seeder($last_name);
        //$this->info('Seeder......✓');

        $this->make_factory($last_name);
        $this->info('Factory.....✓');

        $this->make_resource($last_name);
        $this->info('Resource....✓');

        $this->make_test($last_name);
        $this->info('Test........✓');
    }

    public function make_controller($name)
    {
        $model = 'Models/' . $name;
        $this->callSilent('make:controller', ['name' => $name . 'Controller', '--api' => true, '-m' => $model]);
    }

    public function make_requests($names)
    {
        $this->callSilent('make:request', ['name' => array_last($names['list']) . '/' . array_last($names['list']) . 'StoreRequest']);
        $this->callSilent('make:request', ['name' => array_last($names['list']) . '/' . array_last($names['list']) . 'UpdateRequest']);
    }

    public function make_seeder($name)
    {
        $this->callSilent('make:seeder', ['name' => str_plural($name) . 'TableSeeder']);
    }

    public function make_factory($name)
    {
        $model = 'Models/' . $name;
        $this->callSilent('make:factory', ['name' => $name . 'Factory', '-m' => $model]);
    }

    public function make_resource($name)
    {
        $this->callSilent('make:resource', ['name' => $name . 'Resource']);
    }

    public function make_model($name)
    {
        $name = 'Models/' . $name;
        $this->callSilent('make:model', ['name' => $name, '-m' => true]);
    }

    public function make_test($name)
    {
        $this->callSilent('make:test', ['name' => 'Api/' . $name . 'Test']);
    }

    /**
     * Reformatting data in form of an array
     * $data = ['namespace':string,'list':[]]
     * @return array
     */

    public function get_names()
    {
        $names = explode('/', str_replace('\\', '/', $this->argument('name')));
        $names = array_map('ucfirst', $names);
        $names = array_map('str_singular', $names);

        $namespace = '';

        foreach ($names as $key => $name) {
            if (!$key) {
                $namespace = $name;
            } else
                $namespace = $namespace . '/' . $name;
        }

        $names = [
            'namespace' => $namespace,
            'list' => $names
        ];

        return $names;
    }
}
