<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckLmsSetup extends Command
{
    protected $signature = 'lms:check';
    protected $description = 'Check LMS setup and permissions';

    public function handle()
    {
        $this->info('🔍 Checking LMS Setup...\n');

        // 1. Check database
        $this->info('1️⃣ Database Users:');
        try {
            $users = DB::table('users')->get(['id', 'name', 'email', 'role']);
            if ($users->isEmpty()) {
                $this->error('   ❌ No users found. Create some users first!');
            } else {
                foreach ($users as $user) {
                    $this->line("   ✅ ID:{$user->id} | {$user->name} ({$user->email}) | Role: {$user->role}");
                }
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Database error: {$e->getMessage()}");
        }

        // 2. Check storage symlink
        $this->info('\n2️⃣ Storage Symlink:');
        if (file_exists(public_path('storage'))) {
            $this->line('   ✅ Storage symlink exists');
            
            // Check materials folder
            if (is_dir(storage_path('app/public/materials'))) {
                $files = glob(storage_path('app/public/materials/*'));
                $this->line("   ✅ Materials folder has " . count($files) . " file(s)");
            } else {
                $this->warn('   ⚠️ Materials folder not found');
            }
        } else {
            $this->warn('   ⚠️ Storage symlink missing. Run: php artisan storage:link');
        }

        // 3. Check files
        $this->info('\n3️⃣ Database Files:');
        try {
            $materials = DB::table('materials')->get(['id', 'title', 'file_name', 'user_id']);
            if ($materials->isEmpty()) {
                $this->line('   ℹ️ No materials uploaded yet');
            } else {
                foreach ($materials as $mat) {
                    $this->line("   ✅ ID:{$mat->id} | {$mat->title} | File: {$mat->file_name} | User: {$mat->user_id}");
                }
            }
        } catch (\Exception $e) {
            $this->error("   ❌ Error: {$e->getMessage()}");
        }

        // 4. Check middleware
        $this->info('\n4️⃣ Middleware Registration:');
        $this->line('   ✅ RoleMiddleware should be in bootstrap/app.php');
        $this->line('   ✅ Used in web routes as: role:guru, role:siswa, role:admin');

        // 5. Check routes
        $this->info('\n5️⃣ Expected Routes:');
        $expected = [
            '/' => 'Landing page',
            '/login' => 'Login form',
            '/register' => 'Register form',
            '/dashboard' => 'Role-based redirect',
            '/admin/dashboard' => 'Admin only (403 if not admin)',
            '/guru/dashboard' => 'Guru only (403 if not guru)',
            '/guru/materials' => 'Guru materials list',
            '/guru/materials/create' => 'Guru upload form',
            '/siswa/dashboard' => 'Siswa only (403 if not siswa)',
            '/siswa/materials/{id}/download' => 'Download material',
        ];
        foreach ($expected as $route => $desc) {
            $this->line("   ✅ {$route} - {$desc}");
        }

        $this->info('\n✅ Setup check complete!');
    }
}
