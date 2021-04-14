<?php

declare(strict_types=1);

namespace Osm\Data\Data\MetaProperties;

use Osm\Data\Data\MetaProperty;

class Scalar extends MetaProperty
{
    public function hydrate(mixed $item): mixed {
        return $item;
    }

}