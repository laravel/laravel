<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:database-backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'عمل نسخة احتياطية من قاعدة البيانات';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // إنشاء مجلد النسخ الاحتياطي إذا لم يكن موجوداً
        $backupPath = storage_path('app/backup');
        if (!File::exists($backupPath)) {
            File::makeDirectory($backupPath, 0755, true);
            $this->info("تم إنشاء مجلد النسخ الاحتياطي: " . $backupPath);
        }

        $connection = config('database.default');
        $filename = "backup-" . date("Y-m-d-H-i-s");
        
        // التحقق من نوع قاعدة البيانات واستخدام الطريقة المناسبة
        if ($connection === 'sqlite') {
            $this->backupSqlite($filename);
        } elseif ($connection === 'mysql') {
            $this->backupMysql($filename);
        } else {
            $this->error("نوع قاعدة البيانات '{$connection}' غير مدعوم للنسخ الاحتياطي التلقائي.");
            $this->info("يرجى استخدام الأدوات الخاصة بقاعدة البيانات لديك لعمل نسخة احتياطية.");
            return 1;
        }
        
        return 0;
    }

    /**
     * عمل نسخة احتياطية من قاعدة بيانات SQLite
     */
    private function backupSqlite($filename)
    {
        $databasePath = DB::connection()->getDatabaseName();
        if (!File::exists($databasePath)) {
            $this->error("ملف قاعدة بيانات SQLite غير موجود: " . $databasePath);
            return;
        }

        $backupPath = storage_path("app/backup/{$filename}.sqlite");
        
        try {
            File::copy($databasePath, $backupPath);
            $this->info("تم عمل نسخة احتياطية من قاعدة بيانات SQLite بنجاح إلى: " . $backupPath);
        } catch (\Exception $e) {
            $this->error("فشل عمل نسخة احتياطية: " . $e->getMessage());
        }
    }

    /**
     * عمل نسخة احتياطية من قاعدة بيانات MySQL
     */
    private function backupMysql($filename)
    {
        $backupPath = storage_path("app/backup/{$filename}.sql");
        
        $host = config('database.connections.mysql.host');
        $database = config('database.connections.mysql.database');
        $username = config('database.connections.mysql.username');
        $password = config('database.connections.mysql.password');
        
        // التحقق من وجود أداة mysqldump
        exec('which mysqldump', $output, $returnVar);
        if ($returnVar !== 0) {
            $this->error("أداة mysqldump غير موجودة في النظام.");
            $this->info("يرجى تثبيت MySQL Client أو استخدام طريقة أخرى للنسخ الاحتياطي.");
            return;
        }

        // إعداد أمر النسخ الاحتياطي
        $command = sprintf(
            'mysqldump --user="%s" --password="%s" --host="%s" "%s" > "%s"',
            $username,
            $password,
            $host,
            $database,
            $backupPath
        );
        
        // تنفيذ الأمر وإظهار نتيجته
        exec($command, $output, $returnVar);
        
        if ($returnVar === 0) {
            $this->info("تم عمل نسخة احتياطية من قاعدة بيانات MySQL بنجاح إلى: " . $backupPath);
        } else {
            $this->error("فشل عمل نسخة احتياطية. الرجاء التحقق من إعدادات قاعدة البيانات وصلاحيات المستخدم.");
        }
    }
}
