<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$u = User::find(1);
$passwords = [
    'password', 'password123', 'admin123', '123456', '12345678', '123456789',
    'Password@123', 'Admin@123', 'Password123!', 'Admin123!', 'admin', 'admin@blogmnm.test',
    'khoik7070', 'khoik7070@gmail.com', 'blogmnm', 'secret', 'root',
];

foreach ($passwords as $p) {
    if (Hash::check($p, $u->password)) {
        echo "MATCH: {$p}\n";
        exit(0);
    }
}
echo "NOT FOUND\n";
