<?php

declare(strict_types=1);

namespace Osm\Data\Data\Properties;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Property;

/**
 * @property Property[] $properties
 */
#[Name('object')]
class Object_ extends Property
{
    protected function get_properties(): array {
        $properties = [];

        foreach ($this->schema->child_ids[$this->id] ?? [] as $id) {
            $properties[$this->schema->all[$id]->name] = $this->schema->all[$id];
        }

        return $properties;
    }
}