<?php

require __DIR__.'/../vendor/autoload.php';

// define our aliases

use Carbon\Carbon as Carbon;

echo "flot";


// try carbon dates, can it be added to psr autoloader

printf("Right now is %s", Carbon::now()->toDateTimeString());