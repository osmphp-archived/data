<?php

declare(strict_types=1);

namespace Osm\Data\Data\Filters;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Data\Data\Filter;
use Osm\Data\Data\Property;

class And_ extends Filter
{
    /**
     * @var Filter[]
     */
    public array $filters = [];

    public function filter(Property $property, TableQuery $query): void {
        foreach ($this->filters as $filter) {
            $filter->filter($property, $query);
        }
    }
}