<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\Object_;
use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Data\Data\Hints\Property;

class Type extends Object_
{
    public function select(TableQuery $query, \stdClass|Property $property)
        : void
    {
        if (isset($property->db)) {
            $query->addSelect("this.{$property->name}");
        }
        else {
            $query->addSelect("this.data->{$property->name} AS {$property->name}");
        }
    }
}