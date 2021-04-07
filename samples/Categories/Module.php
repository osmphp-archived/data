<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Categories;

use Osm\Core\Module as BaseModule;
use Osm\Data\Samples\App;
use Osm\Data\Sheets\Sheets;

class Module extends BaseModule
{
    public static ?string $app_class_name = App::class;

    public static array $requires = [
        \Osm\Data\Samples\Base\Module::class,
    ];

    public static array $traits = [
        Sheets::class => Traits\SheetsTrait::class,
    ];
}