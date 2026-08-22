<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

require __DIR__.'/../vendor/autoload.php';

$app = require_once __DIR__.'/../bootstrap/app.php';

// Vercel's deployment filesystem is read-only outside of /tmp, and /tmp itself
// is wiped between cold starts — so storage/ and bootstrap/cache/ (this app's
// defaults) can't be written to at runtime. Redirect Laravel's writable paths
// there and recreate the subdirectories it expects on every cold start.
$tmpStorage = '/tmp/storage';
foreach (['app/public', 'framework/cache/data', 'framework/sessions', 'framework/views', 'framework/testing', 'logs'] as $dir) {
    $path = $tmpStorage.'/'.$dir;
    if (!is_dir($path)) {
        mkdir($path, 0775, true);
    }
}
$app->useStoragePath($tmpStorage);

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
