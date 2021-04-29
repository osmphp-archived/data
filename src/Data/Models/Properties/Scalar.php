<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models\Properties;

use Osm\Data\Data\Models\Property;

class Scalar extends Property
{
    public function hydrate(mixed $dehydrated): mixed {
        return $dehydrated;
    }

    public function dehydrate(mixed $hydrated): mixed {
        return $hydrated;
    }
}