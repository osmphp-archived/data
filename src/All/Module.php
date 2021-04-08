<?php

declare(strict_types=1);

namespace Osm\Data\All;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Data\Data\Module::class,
    ];
}