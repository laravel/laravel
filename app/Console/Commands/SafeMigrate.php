<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class SafeMigrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:safe {--force : Force the operation to run in production}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migrations safely, skipping migrations for tables that already exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting safe migration process...');
        
        // Get all migration files
        $migrationFiles = $this->getMigrationFiles();
        
        // Migration tables that already exist in the database
        $existingTables = $this->getExistingTables();
        
        $this->info('Found ' . count($migrationFiles) . ' migration files to process.');
        $this->info('Found ' . count($existingTables) . ' existing tables in database.');
        
        $skipped = 0;
        $migrated = 0;
        
        foreach ($migrationFiles as $migration) {
            $tableName = $this->getTableFromMigration($migration);
            $migrationPath = $migration->getPathname();
            $relativePath = str_replace(database_path('migrations') . '/', '', $migrationPath);
            
            if ($tableName && in_array($tableName, $existingTables)) {
                $this->warn("Skipping migration for table '{$tableName}' which already exists.");
                $skipped++;
            } else {
                $this->info("Migrating: {$relativePath}");
                $this->runMigration($migrationPath);
                $migrated++;
            }
        }
        
        $this->info("Migration complete: {$migrated} tables migrated, {$skipped} tables skipped.");
        
        return Command::SUCCESS;
    }
    
    /**
     * Get all migration files.
     */
    private function getMigrationFiles()
    {
        return collect(File::files(database_path('migrations')))
            ->filter(function (SplFileInfo $file) {
                return $file->getExtension() === 'php';
            })
            ->values()
            ->all();
    }
    
    /**
     * Get existing tables from the database.
     */
    private function getExistingTables()
    {
        $tables = [];
        
        if (DB::connection()->getDriverName() === 'sqlite') {
            $rows = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
            foreach ($rows as $row) {
                $tables[] = $row->name;
            }
        } else {
            $rows = DB::select('SHOW TABLES');
            foreach ($rows as $row) {
                $tables[] = reset($row);
            }
        }
        
        return $tables;
    }
    
    /**
     * Parse the migration file to determine the table name.
     */
    private function getTableFromMigration(SplFileInfo $file)
    {
        $content = file_get_contents($file->getPathname());
        
        // Look for Schema::create('table_name', function...
        if (preg_match("/Schema::create\('([^']+)'/", $content, $matches)) {
            return $matches[1];
        }
        
        // Alternative syntax: Schema::create("table_name", function...
        if (preg_match('/Schema::create\("([^"]+)"/', $content, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Run a single migration.
     */
    private function runMigration($path)
    {
        $relativePath = str_replace(database_path('migrations') . '/', '', $path);
        $command = "migrate --path=database/migrations/{$relativePath}";
        
        if ($this->option('force')) {
            $command .= " --force";
        }
        
        $this->call($command);
    }
}
