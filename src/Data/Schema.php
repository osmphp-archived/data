<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Array_;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Db\Db;
use function Osm\__;
use function Osm\create;
use function Osm\merge;

/**
 * @property Property[] $all #[Serialized]
 * @property int[] $child_ids #[Serialized]
 * @property int[] $endpoint_ids #[Serialized]
 * @property Db $db
 * @property Property[] $properties
 * @property Properties\Array_[] $endpoints
 * @property Data $data
 * @property \stdClass $meta
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

            $this->all[$item->id] = $this->hydrate($this->meta->items, $item);

            $parentId = $item->parent_id ?? 0;
            if (!isset($this->child_ids[$parentId])) {
                $this->child_ids[$parentId] = [];
            }
            $this->child_ids[$parentId][] = $item->id;

            if ($item->endpoint) {
                $this->endpoint_ids[$item->endpoint] = $item->id;
            }
        }

        $this->loaded();
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

    protected function get_meta(): \stdClass {
        return json_decode(file_get_contents(__DIR__ . '/properties.json'))
            ->properties;

    }

    protected function mergeData(\stdClass $item): \stdClass {
        if (isset($item->data)) {
            $data = json_decode($item->data);
            unset($item->data);
            $item = merge($data, $item);
        }

        return $item;
    }

    public function hydrate(\stdClass $meta, mixed $item): mixed
    {
        return match ($meta->type) {
            'object' => $this->hydrateObject($meta, $item),
            'array' => $this->hydrateArray($meta, $item),
            'string', 'number', 'boolean' => $this->hydrateScalar($item),
            default => throw new NotSupported(__(
                "Unsupported property type :type", ['type' => $meta->type]))
        };
    }

    protected function hydrateObject(\stdClass $meta, ?\stdClass $item)
        : Object_|\stdClass|null
    {
        if ($item === null) {
            return null;
        }

        $object = isset($meta->class)
            ? create($meta->class, $item->type ?? null)
            : new \stdClass();

        foreach ($meta->properties as $propertyName => $property) {
            if (($value = $this->hydrate($property,
                $item->$propertyName ?? null)) !== null)
            {
                $object->$propertyName = $value;
            }
        }

        return $object;
    }

    protected function hydrateArray(\stdClass $meta, ?array $item): ?array
    {
        if ($item === null) {
            return null;
        }

        return array_map(fn($value) => $this->hydrate($meta->items,
            $value), $item);
    }

    protected function hydrateScalar(mixed $item): mixed
    {
        return $item;
    }

    public function loaded() {
        $this->all = $this->resolveRefs(null, $this->meta, $this->all);
    }

    protected function resolveRefs(?\stdClass $parentMeta, \stdClass $meta,
        mixed $item, ?Object_ $container = null): mixed
    {
        return match ($meta->type) {
            'object' => $this->resolveObjectRefs($parentMeta, $meta, $item, $container),
            'array' => $this->resolveArrayRefs($meta, $item, $container),
            'string', 'number', 'boolean' => $item,
        };
    }

    protected function resolveObjectRefs(?\stdClass $parentMeta, \stdClass $meta,
        mixed $item, ?Object_ $container): mixed
    {
        if (isset($meta->ref->container)) {
            return $container;
        }

        if ($item === null) {
            return null;
        }

        if ($parentMeta->type == 'array' && isset($parentMeta->endpoint)) {
            $container = $item;
        }

        foreach ($meta->properties ?? [] as $propertyName => $property) {
            $item->$propertyName = $this->resolveRefs($meta, $property,
            $item->$propertyName ?? null, $container);
        }

        return $item;
    }

    protected function resolveArrayRefs(\stdClass $meta, mixed $item,
        ?Object_ $container): array|Array_|null
    {
        if ($item === null) {
            return null;
        }

        if ($item instanceof Array_) {
            return $item->map(fn($value) =>
                $this->resolveRefs($meta, $meta->items, $value, $container));
        }

        return array_map(fn($value) => $this->resolveRefs($meta, $meta->items,
            $value, $container), $item);
    }

    public function modify(callable $callback) {
        $callback($blueprint = Blueprints::new());
        $blueprint->run();
    }
}