<?php

use App\Models\User;
use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$user = User::first();
$session = app('session')->driver();
$session->start();
Auth::guard('web')->setUser($user);
$session->put(Auth::guard('web')->getName(), $user->getAuthIdentifier());
$session->save();

echo 'COOKIE:'.encrypt($session->getId());
