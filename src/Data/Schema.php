<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Data\Properties\Array_;
use Osm\Framework\Cache\Descendants;
use Osm\Framework\Db\Db;
use function Osm\create;
use function Osm\merge;

/**
 * @property Property[] $all #[Serialized]
 * @property int[] $child_ids #[Serialized]
 * @property int[] $endpoint_ids #[Serialized]
 * @property Db $db
 * @property Property[] $properties
 * @property Array_[] $endpoints
 * @property Data $data
 * @property MetaProperties\Array_ $meta
 */
class Schema extends Object_
{
    protected function get_all(): array {
        $this->load();
        return $this->all;
    }

    protected function get_child_ids(): array {
        $this->load();
        return $this->child_ids;
    }
    protected function get_endpoint_ids(): array {
        $this->load();
        return $this->endpoint_ids;
    }

    protected function load(): void {
        $this->all = [];
        $this->child_ids = [];
        $this->endpoint_ids = [];


        foreach ($this->db->table('properties')->get() as $item) {
            $item = $this->mergeData($item);

            $this->all[$item->id] = $this->meta->items->hydrate($item);

            $parentId = $item->parent_id ?? 0;
            if (!isset($this->child_ids[$parentId])) {
                $this->child_ids[$parentId] = [];
            }
            $this->child_ids[$parentId][] = $item->id;

            if ($item->endpoint) {
                $this->endpoint_ids[$item->endpoint] = $item->id;
            }
        }
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_properties(): array {
        $properties = [];

        foreach ($this->child_ids[0] ?? [] as $id) {
            $properties[$this->all[$id]->name] = $this->all[$id];
        }

        return $properties;
    }

    protected function get_endpoints(): array {
        return array_map(fn($id) => $this->all[$id],
            $this->endpoint_ids);
    }

    protected function get_data(): Data {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->data;
    }

    protected function get_meta(): MetaProperty {
        return $this->parseRecord($this->db->table('properties')
            ->where('endpoint', '/properties')
            ->whereNull('parent_id')
            ->first());
    }

    protected function parseRecord(\stdClass $item): MetaProperty {
        $item = $this->mergeData($item);

        $property = $this->createProperty($item);

        if ($property instanceof MetaProperties\Object_) {
            $property->properties = $this->loadChildProperties($property);
        }
        elseif($property instanceof MetaProperties\Array_) {
            $property->items->properties = $this->loadChildProperties($property);
        }

        return $property;
    }

    protected function mergeData(\stdClass $item): \stdClass {
        if (isset($item->data)) {
            $data = json_decode($item->data);
            unset($item->data);
            $item = merge($data, $item);
        }

        return $item;
    }

    protected function createProperty(\stdClass $property): MetaProperty
    {
        return match ($property->type) {
            'object' => MetaProperties\Object_::new((array)$property),
            'array' => $this->createArray($property),
            'string' => MetaProperties\String_::new((array)$property),
            'number' => MetaProperties\Number::new((array)$property),
            'boolean' => MetaProperties\Boolean::new((array)$property),
        };
    }

    protected function loadChildProperties(MetaProperty $property): array {
        return $this->db->table('properties')
            ->where('parent_id', $property->id)
            ->get()
            ->map(fn($item) => $this->parseRecord($item))
            ->keyBy('name')
            ->toArray();
    }

    protected function createArray(\stdClass $property): MetaProperties\Array_ {
        $property->items = $this->createProperty($property->items);
        return MetaProperties\Array_::new((array)$property);
    }
}