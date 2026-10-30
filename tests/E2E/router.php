<?php

declare(strict_types=1);

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Support\Facades\DB;

$path = rawurldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/');
$projectRoot = dirname(__DIR__, 2);
$qaStoragePath = getenv('LARAVEL_STORAGE_PATH');

if ($qaStoragePath === false) {
    http_response_code(500);

    return true;
}

$_SERVER['LARAVEL_STORAGE_PATH'] = $qaStoragePath;

if ($path === '/__e2e/health') {
    if (($_SERVER['REMOTE_ADDR'] ?? '') !== '127.0.0.1' || getenv('APP_ENV') !== 'e2e') {
        http_response_code(403);

        return true;
    }

    require $projectRoot.'/vendor/autoload.php';
    $app = require $projectRoot.'/bootstrap/app.php';
    $app->make(Kernel::class)->bootstrap();

    header('Content-Type: application/json');
    echo json_encode([
        'app_env' => config('app.env'),
        'database_connection' => config('database.default'),
        'database_name' => DB::selectOne('SELECT DATABASE() AS name')->name,
        'database_user' => DB::selectOne('SELECT CURRENT_USER() AS name')->name,
        'storage_root' => storage_path('app/public'),
        'session_driver' => config('session.driver'),
        'cache_store' => config('cache.default'),
        'mail_transport' => config('mail.default'),
    ], JSON_THROW_ON_ERROR);

    return true;
}

if (str_starts_with($path, '/storage/')) {
    $storageRoot = getenv('LARAVEL_STORAGE_PATH');
    $publicRoot = $storageRoot ? realpath($storageRoot.'/app/public') : false;
    $file = $publicRoot ? realpath($publicRoot.substr($path, strlen('/storage'))) : false;

    if (! $publicRoot || ! $file || ! str_starts_with($file, $publicRoot.DIRECTORY_SEPARATOR) || ! is_file($file)) {
        http_response_code(404);

        return true;
    }

    header('Content-Type: '.(mime_content_type($file) ?: 'application/octet-stream'));
    readfile($file);

    return true;
}

if (is_file($projectRoot.'/public'.$path)) {
    return false;
}

require $projectRoot.'/public/index.php';

return true;
