<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Products;

use Osm\Core\BaseModule;
use Osm\Data\Samples\App;

class Module extends BaseModule
{
    public static ?string $app_class_name = App::class;

    public static array $requires = [
        \Osm\Data\Samples\Base\Module::class,
    ];
}