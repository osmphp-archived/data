<?php

declare(strict_types=1);

use Osm\Data\Samples\App;
use Osm\Runtime\Apps;
use function Osm\handle_errors;

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';
umask(0);
handle_errors();

Apps::$project_path = dirname(dirname(__DIR__));
try {
    Apps::run(Apps::create(App::class), function (App $app) {
        $app->handleHttpRequest()->send();
    });
}
catch (\Throwable $e) {
    \Osm\exception_response($e)->send();
}
