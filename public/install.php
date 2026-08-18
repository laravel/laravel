<?php

declare(strict_types=1);

session_name('mealflow_installer');
session_start();

$basePath = dirname(__DIR__);
$lockFile = $basePath.'/storage/app/installed.json';
$envFile = $basePath.'/.env';
$requirements = [
    'PHP 8.2 or newer' => version_compare(PHP_VERSION, '8.2.0', '>='),
    'PDO MySQL extension' => extension_loaded('pdo_mysql'),
    'Mbstring extension' => extension_loaded('mbstring'),
    'OpenSSL extension' => extension_loaded('openssl'),
    'Fileinfo extension' => extension_loaded('fileinfo'),
    'GD extension' => extension_loaded('gd'),
    'ZIP extension' => extension_loaded('zip'),
    'Storage directory writable' => is_writable($basePath.'/storage/app'),
    'Project directory writable' => is_writable($basePath),
    'Composer dependencies installed' => file_exists($basePath.'/vendor/autoload.php'),
];

function e(string $value): string { return htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); }
function envValue(string $value): string { return '"'.str_replace(['\\', '"', '$'], ['\\\\', '\\"', '\\$'], $value).'"'; }
function old(string $key, string $default = ''): string { return e((string) ($_SESSION['installer_data'][$key] ?? $default)); }
function url(string $path = ''): string { return 'install.php'.($path ? '?step='.$path : ''); }

if (file_exists($lockFile)) {
    $installed = json_decode((string) file_get_contents($lockFile), true) ?: [];
    render('Installed', '<div class="success"><strong>MealFlow is already installed.</strong><p>Installed '.e((string) ($installed['installed_at'] ?? '')).'. The installer is now locked.</p></div><a class="button" href="/login">Open admin login</a>', 4);
    exit;
}

$step = max(1, min(3, (int) ($_GET['step'] ?? 1)));
$errors = [];
$_SESSION['installer_csrf'] ??= bin2hex(random_bytes(24));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (! hash_equals($_SESSION['installer_csrf'], (string) ($_POST['_token'] ?? ''))) {
        http_response_code(419); exit('Installer session expired. Refresh the page and try again.');
    }

    if ($step === 2) {
        foreach (['app_url','db_host','db_port','db_name','db_user'] as $field) {
            if (trim((string) ($_POST[$field] ?? '')) === '') $errors[] = ucwords(str_replace('_', ' ', $field)).' is required.';
        }
        if (! filter_var($_POST['app_url'] ?? '', FILTER_VALIDATE_URL)) $errors[] = 'Enter a valid application URL including https://.';
        if (! $errors) {
            $data = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $_POST);
            try {
                $pdo = new PDO('mysql:host='.$data['db_host'].';port='.$data['db_port'].';dbname='.$data['db_name'].';charset=utf8mb4', $data['db_user'], (string) ($data['db_password'] ?? ''), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
                $pdo->query('SELECT 1');
                $_SESSION['installer_data'] = $data;
                header('Location: '.url('3')); exit;
            } catch (Throwable $exception) { $errors[] = 'Database connection failed: '.$exception->getMessage(); }
        }
    }

    if ($step === 3) {
        $data = array_merge($_SESSION['installer_data'] ?? [], array_map(fn ($v) => is_string($v) ? trim($v) : $v, $_POST));
        if (! isset($data['db_name'])) { header('Location: '.url('2')); exit; }
        foreach (['company_name','company_code','admin_name','admin_email','admin_password'] as $field) if (($data[$field] ?? '') === '') $errors[] = ucwords(str_replace('_', ' ', $field)).' is required.';
        if (! filter_var($data['admin_email'] ?? '', FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid administrator email.';
        if (strlen((string) ($data['admin_password'] ?? '')) < 12) $errors[] = 'Administrator password must contain at least 12 characters.';
        if (! preg_match('/^[A-Za-z0-9_-]+$/', (string) ($data['company_code'] ?? ''))) $errors[] = 'Company code may contain letters, numbers, dashes and underscores only.';

        if (! $errors) {
            try {
                $appKey = 'base64:'.base64_encode(random_bytes(32));
                $env = implode("\n", [
                    'APP_NAME="MealFlow"', 'APP_ENV=production', 'APP_KEY='.$appKey, 'APP_DEBUG=false', 'APP_URL='.envValue(rtrim($data['app_url'], '/')), '',
                    'APP_LOCALE=en', 'APP_FALLBACK_LOCALE=en', 'APP_TIMEZONE=Asia/Dubai', 'LOG_CHANNEL=stack', 'LOG_LEVEL=warning', '',
                    'DB_CONNECTION=mysql', 'DB_HOST='.envValue($data['db_host']), 'DB_PORT='.(int) $data['db_port'], 'DB_DATABASE='.envValue($data['db_name']), 'DB_USERNAME='.envValue($data['db_user']), 'DB_PASSWORD='.envValue((string) ($data['db_password'] ?? '')), '',
                    'SESSION_DRIVER=database', 'SESSION_LIFETIME=120', 'SESSION_ENCRYPT=true', 'SESSION_SECURE_COOKIE=true', 'SESSION_SAME_SITE=lax', '',
                    'CACHE_STORE=database', 'QUEUE_CONNECTION=database', 'FILESYSTEM_DISK=local', 'MAIL_MAILER=log', '',
                ]);
                $temporary = $envFile.'.installer-'.bin2hex(random_bytes(5));
                if (file_put_contents($temporary, $env, LOCK_EX) === false || ! rename($temporary, $envFile)) throw new RuntimeException('Unable to write the .env file. Check project-directory permissions.');
                @unlink($basePath.'/bootstrap/cache/config.php');

                require $basePath.'/vendor/autoload.php';
                $app = require $basePath.'/bootstrap/app.php';
                $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();
                Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                Illuminate\Support\Facades\DB::transaction(function () use ($data): void {
                    $company = App\Models\Company::firstOrCreate(['code' => strtoupper($data['company_code'])], ['name' => $data['company_name']]);
                    App\Models\User::firstOrCreate(['email' => strtolower($data['admin_email'])], ['company_id' => $company->id, 'name' => $data['admin_name'], 'password' => $data['admin_password'], 'role' => App\Enums\Role::CompanyAdmin, 'is_active' => true]);
                });
                Illuminate\Support\Facades\Artisan::call('optimize:clear');
                file_put_contents($lockFile, json_encode(['installed_at' => gmdate('c'), 'app_url' => $data['app_url']], JSON_PRETTY_PRINT), LOCK_EX);
                unset($_SESSION['installer_data'], $_SESSION['installer_csrf']);
                session_destroy();
                header('Location: /login?installed=1'); exit;
            } catch (Throwable $exception) { $errors[] = 'Installation failed: '.$exception->getMessage(); }
        }
        $_SESSION['installer_data'] = $data;
    }
}

$errorHtml = $errors ? '<div class="error"><strong>Please fix the following:</strong><ul><li>'.implode('</li><li>', array_map('e', $errors)).'</li></ul></div>' : '';

if ($step === 1) {
    $rows = '';
    foreach ($requirements as $label => $passed) $rows .= '<div class="check"><span>'.e($label).'</span><b class="'.($passed ? 'pass' : 'fail').'">'.($passed ? 'Ready' : 'Missing').'</b></div>';
    $canContinue = ! in_array(false, $requirements, true);
    render('Server check', '<p>We will check your Hostinger account before configuring MealFlow.</p><div class="checks">'.$rows.'</div>'.($canContinue ? '<a class="button" href="'.url('2').'">Continue to database →</a>' : '<div class="error">Enable the missing PHP extensions or permissions in hPanel, then refresh this page.</div>'), 1);
} elseif ($step === 2) {
    render('Database setup', $errorHtml.'<p>Create an empty MySQL database in hPanel, then enter its credentials.</p><form method="post"><input type="hidden" name="_token" value="'.e($_SESSION['installer_csrf']).'"><label>Website URL<input name="app_url" value="'.old('app_url', 'https://'). '" placeholder="https://meals.example.com" required></label><div class="grid"><label>Database host<input name="db_host" value="'.old('db_host', 'localhost').'" required></label><label>Port<input name="db_port" value="'.old('db_port', '3306').'" required></label></div><label>Database name<input name="db_name" value="'.old('db_name').'" required></label><label>Database username<input name="db_user" value="'.old('db_user').'" required></label><label>Database password<input type="password" name="db_password" autocomplete="new-password"></label><button>Test connection and continue →</button></form>', 2);
} else {
    render('Company & administrator', $errorHtml.'<p>Create the first company and its administrator account.</p><form method="post"><input type="hidden" name="_token" value="'.e($_SESSION['installer_csrf']).'"><div class="grid"><label>Company name<input name="company_name" value="'.old('company_name').'" required></label><label>Company code<input name="company_code" value="'.old('company_code').'" placeholder="ACME" required></label></div><label>Administrator name<input name="admin_name" value="'.old('admin_name').'" required></label><label>Administrator email<input type="email" name="admin_email" value="'.old('admin_email').'" required></label><label>Administrator password<input type="password" name="admin_password" minlength="12" required><small>Minimum 12 characters</small></label><button>Install MealFlow</button></form>', 3);
}

function render(string $title, string $content, int $step): void
{
    $progress = '';
    foreach ([1 => 'Requirements', 2 => 'Database', 3 => 'Admin'] as $number => $label) $progress .= '<div class="'.($number <= $step ? 'active' : '').'"><b>'.$number.'</b><span>'.$label.'</span></div>';
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>'.e($title).' — MealFlow Installer</title><style>
    :root{font-family:Inter,system-ui,sans-serif;color:#17221c;background:#edf5f1}*{box-sizing:border-box}body{margin:0;min-height:100vh;display:grid;place-items:center;padding:25px;background:radial-gradient(circle at top left,#cef0e1,#f4f7f5 55%)}.shell{width:min(680px,100%)}.brand{font-size:27px;font-weight:850;color:#145c42;margin-bottom:18px}.brand span{display:block;font-size:11px;letter-spacing:.12em;text-transform:uppercase;color:#607069}.card{background:#fff;border:1px solid #dce6e1;border-radius:18px;padding:34px;box-shadow:0 25px 70px #163d2d18}.steps{display:grid;grid-template-columns:repeat(3,1fr);margin-bottom:30px}.steps div{display:flex;align-items:center;gap:8px;color:#8a9690;border-bottom:3px solid #dce6e1;padding-bottom:12px}.steps .active{color:#176c4d;border-color:#2c9a70}.steps b{display:grid;place-items:center;width:25px;height:25px;border-radius:50%;background:#edf3f0}.steps .active b{background:#176c4d;color:white}h1{font-size:27px;margin:0 0 7px}p{color:#637069}.checks{margin:24px 0}.check{display:flex;justify-content:space-between;padding:12px 4px;border-bottom:1px solid #edf1ef}.pass{color:#137a51}.fail{color:#b42318}.button,button{display:inline-block;background:#176c4d;color:white;border:0;border-radius:9px;padding:13px 18px;text-decoration:none;font-weight:750;cursor:pointer;margin-top:14px}label{display:grid;gap:6px;font-size:13px;font-weight:700;margin:15px 0}input{width:100%;border:1px solid #c9d6cf;border-radius:8px;padding:12px;font:inherit}.grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}.error,.success{padding:14px 16px;border-radius:9px;margin:16px 0}.error{background:#fee4e2;color:#912018}.success{background:#dff5ea;color:#145c42}small{color:#6b7771;font-weight:500}@media(max-width:580px){.card{padding:24px}.grid{grid-template-columns:1fr}.steps span{display:none}}
    </style></head><body><div class="shell"><div class="brand">MealFlow<span>Secure installation wizard</span></div><section class="card"><div class="steps">'.$progress.'</div><h1>'.e($title).'</h1>'.$content.'</section></div></body></html>';
}
