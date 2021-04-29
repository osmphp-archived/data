<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Products;

use Osm\Core\BaseModule;

class Module extends BaseModule
{
    public static array $requires = [
        \Osm\Data\Samples\Base\Module::class,
    ];
}