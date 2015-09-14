<?php

#namespace FlotCMS;

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
    return Admin::makeUI(["request" => $request]);
});

/*
account stuff
*/
$oRouter->respond('GET', '/flot-manage/logout/', function () {

    return "logout lol";
});


/*
test
*/
$oRouter->respond('GET', '/test/', function () {

	/*
	*/
	return TestController::test();
});


/*
app/http boilerplate
*/

$oRouter->onHttpError(function ($code, $router) {
    switch ($code) {
        case 404:

            $router->response()->body(
        		View::render("pages\\general\\404", [])
        	);
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