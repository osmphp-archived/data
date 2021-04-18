<?php

declare(strict_types=1);

namespace Osm\Data\Data\Queries;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Property;
use Osm\Data\Data\Query;

#[Name('/properties')]
class Properties extends Query
{
    public function insert(\stdClass $data): int {
        return $this->db->transaction(function() use ($data) {
            $id = parent::insert($data);

            /* @var Property $property */
            $property = $this->endpoint->items->hydrate($data);
            $this->data->schema->modify(function (Blueprints $data) use ($property) {
                $property->create($data);
            });

            return $id;
        });
    }
}