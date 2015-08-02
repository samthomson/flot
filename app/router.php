<?php

require __DIR__.'../../bootstrap/autoload.php';



// define our aliases

use Carbon\Carbon as Carbon;
use Phroute\Phroute\RouteCollector;
use Klein\Klein as Klein;




$klein = new Klein();

$klein->respond('GET', '/flot-admin', function () {
    //include __DIR__.'/views/admin.html';

    //$o = new View();

    //return $o->render("admin");

    $loader = new Twig_Loader_Array(array(
    'index' => 'Hello {{ name }}!',
));
$twig = new Twig_Environment($loader);

echo $twig->render('index', array('name' => 'Fabien'));

});


$klein->dispatch();