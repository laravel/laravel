<?php

namespace Tests\Feature;

use Illuminate\Filesystem\Filesystem;
use Tests\TestCase;

class MakeApiCommandTest extends TestCase
{
    private Filesystem $files;

    /**
     * @var array<int, string>
     */
    private array $pathsToDelete = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->files = new Filesystem;
    }

    protected function tearDown(): void
    {
        foreach ($this->pathsToDelete as $path) {
            if ($this->files->isFile($path)) {
                $this->files->delete($path);
            }
        }

        parent::tearDown();
    }

    public function test_it_generates_model_and_migration_without_creating_duplicates(): void
    {
        $model = app_path('Models/ProfessionalProbe.php');

        $this->pathsToDelete[] = $model;

        $this->artisan('make:api ProfessionalProbe --model --migration')
            ->assertSuccessful();

        $migration = $this->firstMigrationFor('professional_probes');
        $this->pathsToDelete[] = $migration;

        $this->assertFileExists($model);
        $this->assertFileExists($migration);
        $this->assertStringContainsString('class ProfessionalProbe extends Model', $this->files->get($model));
        $this->assertStringContainsString("Schema::create('professional_probes'", $this->files->get($migration));

        $this->artisan('make:api ProfessionalProbe --migration')
            ->assertSuccessful();

        $this->assertCount(1, $this->migrationsFor('professional_probes'));
    }

    public function test_it_generates_versioned_api_stack_while_keeping_models_unversioned(): void
    {
        $paths = [
            app_path('Models/ProfessionalApi.php'),
            app_path('Http/Controllers/Api/V2/ProfessionalApiController.php'),
            app_path('Services/V2/ProfessionalApiService.php'),
            app_path('Http/Requests/V2/ProfessionalApiRequest.php'),
            app_path('Http/Resources/V2/ProfessionalApiResource.php'),
            base_path('tests/Feature/V2/ProfessionalApiApiTest.php'),
        ];

        $this->pathsToDelete = array_merge($this->pathsToDelete, $paths);

        $this->artisan('make:api ProfessionalApi --controller --service --request --resource --test --model --api-version=v2 --force')
            ->assertSuccessful();

        foreach ($paths as $path) {
            $this->assertFileExists($path);
        }

        $controller = $this->files->get(app_path('Http/Controllers/Api/V2/ProfessionalApiController.php'));
        $service = $this->files->get(app_path('Services/V2/ProfessionalApiService.php'));

        $this->assertStringContainsString('namespace App\Http\Controllers\Api\V2;', $controller);
        $this->assertStringContainsString('use App\Models\ProfessionalApi;', $controller);
        $this->assertStringContainsString('use App\Services\V2\ProfessionalApiService;', $controller);
        $this->assertStringContainsString('namespace App\Services\V2;', $service);
        $this->assertStringContainsString('use App\Models\ProfessionalApi;', $service);
    }

    private function firstMigrationFor(string $table): string
    {
        $migration = $this->migrationsFor($table)[0] ?? null;

        $this->assertNotNull($migration, "Migration for [{$table}] was not generated.");

        return $migration;
    }

    /**
     * @return array<int, string>
     */
    private function migrationsFor(string $table): array
    {
        $matches = glob(database_path("migrations/*_create_{$table}_table.php")) ?: [];

        sort($matches);

        return $matches;
    }
}
