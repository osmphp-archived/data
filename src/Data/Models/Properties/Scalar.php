<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models\Properties;

use Osm\Data\Data\Model;
use Osm\Data\Data\Models\Property;

class Scalar extends Property
{
    public function hydrate(mixed $dehydrated, array &$identities = null)
        : mixed
    {
        if ($identities === null) {
            $identities = [];
        }

        return $dehydrated;
    }

    public function dehydrate(mixed $hydrated): mixed {
        return $hydrated;
    }

    public function resolve(mixed $hydrated, array &$identities = null,
        ?Model $parent = null): void
    {
        // do nothing
    }
}