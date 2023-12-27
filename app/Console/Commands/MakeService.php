<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $servicePath = app_path("/Services/{$name}.php");

        if (File::exists($servicePath)) {
            $this->error("Service {$name} already exists!");
        } else {
            File::put($servicePath, $this->generateServiceContent($name));

            $this->info("Service {$name} created successfully!");
        }
    }

    private function generateServiceContent($name)
    {
        return "<?php\n\nnamespace App\Services;\n\nclass {$name}\n{\n    // Your service logic here\n}\n";
    }
}
