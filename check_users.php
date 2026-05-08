#!/usr/bin/env php
<?php
define('LARAVEL_START', microtime(true));

if (file_exists($__composer_autoload_path = __DIR__.'/vendor/autoload.php')) {
    require $__composer_autoload_path;
} else {
    fwrite(
        STDERR,
        'Composer dependencies not installed, run `composer install` first'.PHP_EOL
    );
    exit(1);
}

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$status = $kernel->handle(
    $input = new \Symfony\Component\Console\Input\StringInput(''), 
    new \Symfony\Component\Console\Output\NullOutput
);

use App\Models\User;

$users = User::all();

echo "\n===== DAFTAR AKUN PENGGUNA =====\n\n";
echo "Total Akun: " . count($users) . "\n\n";

foreach($users as $user) {
    echo "Email: " . $user->email . " | Nama: " . $user->name . " | Role: " . $user->role . "\n";
}

echo "\n===== PASSWORD UNTUK SEMUA AKUN =====\n";
echo "Password: password\n";
echo "\n";
?>
