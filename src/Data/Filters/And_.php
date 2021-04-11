<?php

declare(strict_types=1);

namespace Osm\Data\Data\Filters;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Data\Data\Filter;
use Osm\Framework\Search\Query as SearchQuery;
use Osm\Data\Data\Hints\Property;

class And_ extends Filter
{
    /**
     * @var Filter[]
     */
    public array $filters = [];

    public function search(\stdClass|Property $property, SearchQuery $query)
        : void
    {
        foreach ($this->filters as $filter) {
            $filter->search($property, $query);
        }
    }

    public function filter(\stdClass|Property $property, TableQuery $query)
        : void
    {
        foreach ($this->filters as $filter) {
            $filter->filter($property, $query);
        }
    }
}