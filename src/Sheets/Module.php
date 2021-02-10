<?php

/** @noinspection PhpUnused */
declare(strict_types=1);

namespace Osm\Data\Sheets;

use Osm\Core\App;
use Osm\Core\Module as BaseModule;

class Module extends BaseModule
{
    public static array $traits = [
        App::class => Traits\AppTrait::class,
    ];
}