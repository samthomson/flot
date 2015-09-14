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

    return $o->makeUI(["request" => $request]);
});

/*
account stuff
*/
$oRouter->respond('GET', '/flot-manage/logout/', function () {

    return "logout lol";
});


/*
app/http boilerplate
*/

$oRouter->onHttpError(function ($code, $router) {
    switch ($code) {
        case 404:

			$o = new View();

			$htmlBody = $o->render("partials\\404", []);



            $router->response()->body($htmlBody);
            break;
        case 405:
            $router->response()->body(
                'You can\'t do that!'
            );
            break;
        default:
            $router->response()->body(
                "An error happened which flot doesn\'t understand... (Error:,. $code)"
            );
    }
});


$oRouter->dispatch();