<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InstallerTest extends TestCase
{
    use RefreshDatabase;

    public function test_installer_creates_admin_and_marks_installed(): void
    {
        // Ensure no .env write during test; focus on migration + user
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);

        $res = $this->post('/install', [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);
        $res->assertRedirect('/dashboard');
        $this->assertDatabaseHas('users', ['email' => 'admin@example.com', 'role' => 'admin']);
        $this->assertDatabaseHas('settings', ['key' => 'installed']);
    }
}
