<?php

require __DIR__.'../../bootstrap/autoload.php';



// define our aliases

use Carbon\Carbon as Carbon;
use Phroute\Phroute\RouteCollector;
use Klein\Klein as Router;


$oRouter = new Router();

/* klein routing */

/*
admin ui
*/
$oRouter->respond('GET', '/flot-manage/.[:section]?.[:action]?', function ($request) {
	/*
    
    */
    $o = new Admin();

    $o->makeUI(["request" => $request]);
});

/*
account stuff
*/
$oRouter->respond('GET', '/flot-manage/logout/', function () {

    echo "logout lol";
});


$oRouter->dispatch();