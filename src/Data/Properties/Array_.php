<?php

declare(strict_types=1);

namespace Osm\Data\Data\Properties;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Exceptions\UnknownProperty;
use Osm\Data\Data\Filters\Condition;
use Osm\Data\Data\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Data\Query;
use function Osm\object_empty;

/**
 * @property ?string $endpoint #[Serialized]
 * @property Object_|Property $items #[Serialized]
 * @property ?string $key #[Serialized]
 */
#[Name('array')]
class Array_ extends Property
{
    public function __construct(array $data = []) {
        if (isset($data['items'])) {
            $data['items']->id = $data['id'];
            $data['items'] = $this->data->create(Property::class,
                $data['items']);
        }

        parent::__construct($data);
    }

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

        $id = $query->db->table($query->table)->insertGetId((array)$values);

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
        if (!$this->items->ref) {
            throw new NotImplemented($this);
        }

        $endpoint = $this->data->schema->endpoints[$this->items->ref->endpoint];

        foreach ($this->array($value) as $item) {
            $item->{$this->items->ref->property} = $id;
            $this->data->query($endpoint)->insert($item);
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
            $array[] = $item;
        }

        return $array;
    }
}