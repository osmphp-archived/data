<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Framework\Cache\Descendants;
use Osm\Framework\Db\Db;
use function Osm\merge;

/**
 * @property Property[] $all #[Serialized]
 * @property int[] $child_ids #[Serialized]
 * @property int[] $endpoint_ids #[Serialized]
 * @property Db $db
 * @property Property[] $properties
 * @property Property[] $endpoints
 * @property Data $data
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
            if (isset($item->data)) {
                $data = json_decode($item->data);
                unset($item->data);
                $item = merge($data, $item);
            }

            $this->all[$item->id] = $this->data->create(Property::class, $item);

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
}