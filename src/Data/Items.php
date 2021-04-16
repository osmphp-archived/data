<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use function Osm\create;

/**
 * @property Property $property
 * @property string $type #[Serialized]
 * @property string $class #[Serialized]
 * @property Data $data
 * @property Property[] $properties
 */
class Items extends Object_
{
    protected function get_data(): Data {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->data;
    }

    protected function get_properties(): array {
        if ($this->property->ref) {
            return $this->data->schema
                ->endpoints[$this->property->ref->endpoint]
                ->items->properties;
        }

        $properties = [];

        foreach ($this->data->schema->child_ids[$this->property->id] ?? [] as $id) {
            $properties[$this->data->schema->all[$id]->name] =
                $this->data->schema->all[$id];
        }

        return $properties;
    }

    public function hydrate(?\stdClass $item): Object_|\stdClass|null {
        if ($item === null) {
            return null;
        }

        $data = [];
        foreach ($item as $propertyName => $value) {
            $property = $this->properties[$propertyName];
            if ($value = $property->hydrate($value)) {
                $data[$propertyName] = $value;
            }
        }

        return $this->class
            ? create($this->class, $item->type ?? null, $data)
            : (object)$data;
    }
}