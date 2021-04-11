<?php

declare(strict_types=1);

namespace Osm\Data\Data\Types;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Data\Data\Hints\Property;
use Osm\Data\Data\Type;

class Scalar extends Type
{
    public function select(\stdClass|Property $property, TableQuery $query,
        ?string $expr, string $alias, array $joins): void
    {
        if (isset($property->db)) {
            $query->addSelect("{$alias}.{$property->name}");
        }
        else {
            $query->addSelect(
                "{$alias}.data->{$property->name} AS {$property->name}");
        }
    }
}