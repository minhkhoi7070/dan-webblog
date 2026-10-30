$ErrorActionPreference = 'Stop'
$projectRoot = (Resolve-Path (Join-Path $PSScriptRoot '..\..')).Path
Set-Location $projectRoot

if (-not (Test-Path '.env.e2e')) {
    throw 'Missing ignored .env.e2e'
}

$env:APP_ENV = 'e2e'
$env:LARAVEL_STORAGE_PATH = (Resolve-Path 'storage/e2e').Path

php -S 127.0.0.1:8001 -t public tests/E2E/router.php
