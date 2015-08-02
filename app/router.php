<?php

require __DIR__.'../../bootstrap/autoload.php';



// define our aliases

use Carbon\Carbon as Carbon;
use Phroute\Phroute\RouteCollector;
use Klein\Klein as Klein;




$klein = new Klein();

$klein->respond('GET', '/flot-manage/.[:section]?', function ($request) {

    $o = new View();

    return $o->render("admin", ["request" => $request]);
});


$klein->dispatch();