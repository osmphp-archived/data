<?php

declare(strict_types=1);

/* @see \Osm\Framework\Settings\Hints\Settings */
return (object)[
    'locale' => 'en_US',

    /* @see \Osm\Framework\Logs\Hints\LogSettings */
    'logs' => (object)[
        'elastic' => true,
        'db' => true,
    ],
];