<?php

declare(strict_types=1);

namespace Osm\Data\Data\Types;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\Attributes\Name;
use Osm\Data\Data\Hints\Property;
use Osm\Data\Data\Type;

#[Name('array')]
class Array_ extends Type
{
    public function select(Property|\stdClass $property, TableQuery $query,
        ?string $expr, string $alias, array $joins): void
    {
        if (($pos = strpos($expr, '.')) !== false) {
            $property = $property->items->properties[substr($expr, 0, $pos)];
            $expr = substr($expr, $pos + 1);
        }
        else {
            $property = $property->items->properties[$expr];
            $expr = null;
        }

        $this->data->select($property, $query, $expr, $alias, $joins);
    }
}