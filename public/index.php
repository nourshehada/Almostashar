<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// ✅ زيادة وقت التنفيذ والذاكرة
set_time_limit(120);
ini_set('memory_limit', '512M');

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../vendor/autoload.php';

/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
