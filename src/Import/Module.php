<?php

declare(strict_types=1);

namespace Osm\Data\Import;

use Osm\Core\Module as BaseModule;
use Osm\Data\Sheets\Sheets;

class Module extends BaseModule
{
    public static array $traits = [
        Sheets::class => Traits\SheetsTrait::class,
    ];

    public static array $requires = [
        \Osm\Framework\Translations\Module::class,
    ];
}