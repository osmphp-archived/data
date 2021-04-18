<?php

declare(strict_types=1);

namespace Osm\Data\Data\Properties;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Exceptions\UnknownProperty;
use Osm\Data\Data\Filters\Condition;
use Osm\Data\Data\Items;
use Osm\Data\Data\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Data\Query;
use Osm\Data\Data\Ref;
use function Osm\create;
use function Osm\object_empty;
use function Osm\log;
use Illuminate\Database\Schema\Blueprint as TableBlueprint;

/**
 * @property ?string $endpoint #[Serialized]
 * @property Items $items #[Serialized]
 * @property ?string $key #[Serialized]
 * @property ?Ref $ref #[Serialized]
 * @property string $table
 */
#[Name('array')]
class Array_ extends Property
{
    public function filter(TableQuery $query, string $expr,
        Condition $condition): void
    {
        $this->property($expr)->filter($query, $expr, $condition);
    }

    public function select(TableQuery $query, string $expr) {
        $this->property($expr)->select($query, $expr);
    }

    protected function property(string &$expr): Property {
        if (($pos = strpos($expr, '.')) !== false) {
            $property = $this->items->properties[substr($expr, 0, $pos)];
            $expr = substr($expr, $pos + 1);
        }
        else {
            $property = $this->items->properties[$expr];
            $expr = '';
        }

        return $property;
    }

    public function insert(Query $query, \stdClass $data): int {
        if ($this->computed) {
            throw new NotImplemented($this);
        }

        if ($this->items->type != 'object') {
            throw new NotImplemented($this);
        }

        $values = (object)['data' => new \stdClass()];

        foreach ($data as $propertyName => $value) {
            if (!isset($this->items->properties[$propertyName])) {
                throw new UnknownProperty($this, "items.{$propertyName}");
            }

            $property = $this->items->properties[$propertyName];

            $property->inserting($query, $values, $values->data, $value);
        }

        if (object_empty($values->data)) {
            unset($values->data);
        }
        else {
            $values->data = json_encode($values->data);
        }

        $id = $values->id = $query->db->table($query->table)
            ->insertGetId((array)$values);

        foreach ($this->items->properties as $property) {
            if (isset($data->{$property->name})) {
                $property->inserted($query, $data->{$property->name}, $id);
            }
        }

        return $id;
    }

    public function inserting(Query $query, \stdClass $values, \stdClass $data,
        mixed $value, string $prefix = ''): void
    {
    }

    public function inserted(Query $query, mixed $value, int $id): void {
        if (!$this->ref) {
            throw new NotImplemented($this);
        }

        $endpoint = $this->data->schema->endpoints[$this->ref->endpoint];

        foreach ($this->array($value) as $item) {
            $item->{$this->ref->backref} = $id;
            $this->data->query($endpoint)->doInsert($item);
        }
    }

    protected function array(mixed $value): array {
        if (is_array($value)) {
            return $value;
        }

        if (!is_object($value)) {
            throw new NotImplemented($this);
        }

        if (!$this->key) {
            throw new NotImplemented($this);
        }

        $array = [];

        foreach ($value as $key => $item) {
            $item->{$this->key} = $key;
            $array[$key] = $item;
        }

        return $array;
    }

    public function hydrate(mixed $item): mixed {
        if ($item === null) {
            return null;
        }

        if (is_object($item)) {
            $item = $this->array($item);
        }

        return array_map(fn($value) => $this->items->hydrate($value), $item);
    }

    public function create(Blueprints $data): void {
        if (!$this->endpoint) {
            return;
        }

        $data->createTable($this->table, function() use ($data) {
            foreach ($this->items->properties as $property) {
                $property->create($data);
            }

            $data->blueprint()->callbacks[] = function(TableBlueprint $table) {
                $table->json('data')->nullable();
            };
        });
    }

    protected function get_table(): string {
        return str_replace('/', '__',
            ltrim($this->endpoint, '/'));
    }
}