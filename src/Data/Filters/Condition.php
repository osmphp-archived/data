<?php

declare(strict_types=1);

namespace Osm\Data\Data\Filters;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Filter;
use Osm\Data\Data\Property;

/**
 * @property string $expr
 * @property string $op
 * @property mixed $value
 */
class Condition extends Filter
{
    public function filter(Property $property, TableQuery $query): void {
        $property->filter($query, $this->expr, $this);
    }

    public function apply(TableQuery $query, string $column) {
        switch ($this->op) {
            case '=':
                $query->where($column, $this->value);
                break;
            default:
                throw new NotImplemented($this);
        }
    }
}