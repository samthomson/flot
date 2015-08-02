<?php

require __DIR__.'../../vendor/autoload.php';

// define our aliases

use Carbon\Carbon as Carbon;
use Phroute\Phroute\RouteCollector;
use Klein\Klein as Klein;


$klein = new Klein();

$klein->respond('GET', '/flot-admin', function () {
    return 'Hello World!';
});


$klein->dispatch();