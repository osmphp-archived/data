<?php

declare(strict_types=1);

namespace Osm\Data\Import;

use Osm\Core\Object_;

abstract class Schema extends Object_
{
    abstract public function import(string $path, array $variables = []): void;
}