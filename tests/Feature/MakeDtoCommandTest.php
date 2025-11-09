<?php

namespace Feature;

use Tests\TestCase;

class MakeDtoCommandTest extends TestCase
{
    protected function tearDown(): void
    {
        // Cleanup any generated files
        $path = app_path('DTOs/UserData.php');
        if (file_exists($path)) {
            @unlink($path);
        }

        parent::tearDown();
    }

    public function test_it_creates_dto_file()
    {
        $path = app_path('DTOs/UserData.php');

        // Ensure not exist before
        if (file_exists($path)) {
            @unlink($path);
        }

        // Run the artisan command
        $this->artisan('make:dto', ['name' => 'UserData'])
            ->assertExitCode(0);

        // Assert file created
        $this->assertFileExists($path, 'DTO file was not created');

        $content = file_get_contents($path);

        // Basic assertions about generated content
        $this->assertStringContainsString('namespace App\\DTOs', $content);
        $this->assertStringContainsString('class UserData', $content);
        $this->assertStringContainsString('public static function fromArray', $content);
        $this->assertStringContainsString('public function toArray', $content);
    }
}
