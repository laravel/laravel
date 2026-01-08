<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class MigrateAndSeedModules extends Command
{
    protected $signature = 'modules:migrate-seed';
    protected $description = 'Run migrations and seeders for all modules and the main project';

    public function handle()
    {
        // Ejecutar migrate:fresh para la base de datos general
        $this->info('Running fresh migrations for the main project...');
        Artisan::call('migrate:fresh --seed', [], $this->getOutput());


        // Debe estar en Mayusculas para q Linux lo reconozca porq es sencible a may/min;
        $modulesPath = 'Modules';
        // Usamos una versión más compatible con PHP anterior a 7.4
        $modules = array_diff(scandir($modulesPath), ['.', '..']);

        foreach ($modules as $module) {
            $this->runModuleMigrationsAndSeeders($module, "{$modulesPath}/{$module}");
        }

        $this->info('All fresh migrations and seeders have been run successfully.');
        return 0;
    }

    protected function runModuleMigrationsAndSeeders($module, $modulePath)
    {
        $connectionName = $module; // Usando el nombre del módulo como conexión
        $migrationPath = "{$modulePath}/Database/Migrations";
        $seederClass = "Modules\\{$module}\\Database\\Seeders\\{$module}DatabaseSeeder";

        $this->info("Resetting database for module: {$module}");
        $this->resetDatabase($connectionName);

        $this->info("Running migrations for module: {$module}");
        if (is_dir($migrationPath)) {
            Artisan::call('migrate', ['--database' => $connectionName, '--path' => $migrationPath], $this->getOutput());
        } else {
            $this->warn("Migration path not found for module: {$module}");
        }

        $this->info("Running seeder for module: {$module}");
        if (class_exists($seederClass)) {
            Artisan::call('db:seed', ['--database' => $connectionName, '--class' => $seederClass], $this->getOutput());
        } else {
            $this->warn("Seeder class {$seederClass} not found for module: {$module}. (CHECK composer autoload psr-4!!)");
        }
    }

    protected function resetDatabase($connection)
    {
        $schema = DB::connection($connection)->getSchemaBuilder();
        $tableNames = method_exists($schema, 'getTables')
            ? array_column($schema->getTables(), 'name')
            : $schema->getConnection()->getDoctrineSchemaManager()->listTableNames();

        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=0');
        foreach ($tableNames as $table) {
            try {
                DB::connection($connection)->table($table)->truncate();
            } catch (QueryException $e) {
                // Ignorar errores de tablas que no existen
                if (!str_contains($e->getMessage(), "doesn't exist")) {
                    throw $e; // Relanzar excepciones que no sean por tablas inexistentes
                }
            }
        }
        DB::connection($connection)->statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
