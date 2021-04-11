<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Data\Data\Filters\Condition;
use Osm\Data\Data\Hints\Property;
use Osm\Framework\Cache\Attributes\Cached;
use Osm\Framework\Cache\Cache;
use Osm\Framework\Cache\Descendants;
use Osm\Framework\Db\Db;
use function Osm\merge;

/**
 * @property string[] $endpoints #[Cached('data|endpoints')]
 * @property Db $db
 * @property Cache $cache
 * @property Descendants $descendants
 */
class Data extends Object_
{
    protected array $arrays = [];
    protected array $types = [];

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_cache(): Cache {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->cache;
    }

    protected function get_descendants(): Descendants {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->descendants;
    }

    protected function get_endpoints(): array {
        return $this->db->table('properties')
            ->whereNotNull('endpoint')
            ->pluck('endpoint', 'endpoint')
            ->toArray();
    }

    public function query(string $endpoint): Query {
        return Query::new(['array' => $this->cachedArray($endpoint)]);
    }

    protected function cachedArray(string $endpoint): \stdClass|Property {
        if (!isset($this->arrays[$endpoint])) {
            $key = str_replace('/', '__',
                "data|endpoints|{$endpoint}");

            $this->arrays[$endpoint] =
                $this->cache->get($key, fn() => $this->array($endpoint));
        }

        return $this->arrays[$endpoint];
    }

    protected function array(string $endpoint): \stdClass|Property {
        if ($endpoint == '/properties') {
            return $this->propertiesArray();
        }

        throw new NotImplemented($this);

        $record = $this->db->table('properties')
            ->where('endpoint', $endpoint)
            ->first();

        return $record;
    }


    protected function propertiesArray(): \stdClass|Property {
        $record = $this->db->table('properties')
            ->where('endpoint', '/properties')
            ->first();

        return $this->normalizeRecord($record);
    }

    protected function childProperties(int $id): array {
        $properties = [];

        $records = $this->db->table('properties')
            ->where('parent_id', $id)
            ->get();

        foreach ($records as $record) {
            $record = $this->normalizeRecord($record);
            $properties[$record->name] = $this->normalizeRecord($record);
        }

        return $properties;
    }

    protected function normalizeRecord(\stdClass $record): \stdClass {
        if (isset($record->data)) {
            $data = json_decode($record->data);
            unset($record->data);
            $record = merge($data, $record);
        }

        if ($record->type == 'object') {
            $record->properties = $this->childProperties($record->id);
        }
        elseif($record->type == 'array') {
            if (!isset($record->items)) {
                $record->items = (object)[];
            }

            if (isset($record->items__type)) {
                $record->items->type = $record->items__type;
                unset($record->items__type);
            }

            $record->items->properties = $this->childProperties($record->id);
        }

        return $record;
    }

    public function type(string $typeName): Type {
        if (!isset($this->types[$typeName])) {
            $new = "{$this->descendants->byName(Type::class)[$typeName]}::new";
            $this->types[$typeName] = $new();
        }

        return $this->types[$typeName];
    }

    public function select(\stdClass|Property $property, TableQuery $query,
        string $expr, string $alias = 'this', $joins = []): void
    {
        $this->type($property->type)->select($property, $query,
            $expr, $alias, $joins);
    }

    public function filter(\stdClass|Property $property, TableQuery $query,
        string $expr, Condition $condition, string $alias = 'this',
        $joins = []): void
    {
        $this->type($property->type)->filter($property, $query,
            $expr, $condition, $alias, $joins);
    }
}