<?php

declare(strict_types=1);

namespace Osm\Data\Data\Filters;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Data\Data\Filter;
use Osm\Framework\Search\Query as SearchQuery;
use Osm\Data\Data\Hints\Property;

/**
 * @property string $expr
 * @property string $op
 * @property mixed $value
 */
class Condition extends Filter
{
    public function filter(\stdClass|Property $property, TableQuery $query)
        : void
    {
        $this->data->filter($property, $query, $this->expr, $this);
    }
}