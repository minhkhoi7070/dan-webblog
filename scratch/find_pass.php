<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$users = User::all();
$passwords = ['password', 'password123', 'admin123', '12345678', 'admin', 'password@123', 'secret', 'admin@123'];

foreach ($users as $u) {
    foreach ($passwords as $p) {
        if (Hash::check($p, $u->password)) {
            echo "MATCH: User {$u->id} ({$u->email}): {$p}\n";
            break;
        }
    }
}
