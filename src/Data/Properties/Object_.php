<?php

declare(strict_types=1);

namespace Osm\Data\Data\Properties;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Exceptions\UndefinedProperty;
use Osm\Data\Data\Filters\Condition;
use Osm\Data\Data\Property;
use Osm\Data\Data\Query;
use Osm\Data\Data\Ref;
use Osm\Core\Attributes\Serialized;
use function Osm\__;
use function Osm\create;
use function Osm\object_empty;

/**
 * @property Property[] $properties
 * @property ?Ref $ref #[Serialized]
 * @property string $class #[Serialized]
 */
#[Name('object')]
class Object_ extends Property
{
    protected function get_properties(): array|\Osm\Core\Array_ {
        $properties = [];

        foreach ($this->data->schema->child_ids[$this->id] ?? [] as $id) {
            $properties[$this->data->schema->all[$id]->name] =
                $this->data->schema->all[$id];
        }

        return new \Osm\Core\Array_($properties, fn(string $property) =>
            __("Undefined property ':this.:property'", [
                    'this' => $this->full_name,
                    'property' => $property,
                ]));
    }

    public function filter(TableQuery $query, string $expr,
        Condition $condition): void
    {
        if ($this->ref) {
            if (!$expr) {
                $condition->apply($query, "this.{$this->name}_id");
                return;
            }
        }

        throw new NotImplemented($this);
    }

    public function inserting(Query $query, \stdClass $values, \stdClass $data,
        mixed $value, string $prefix = ''): void
    {
        if ($value === null) {
            return;
        }

        if (!is_object($value)) {
            if ($this->ref) {
                $values->{$this->name . '_id'} = $value;
                return;
            }

            throw new NotImplemented($this);
        }

        $object = new \stdClass();
        $prefix = "{$prefix}{$this->name}__";

        foreach ($value as $propertyName => $propertyValue) {
            if (!isset($this->properties[$propertyName])) {
                throw new UndefinedProperty($this, $propertyName);
            }

            $property = $this->properties[$propertyName];
            $property->inserting($query, $values, $object, $propertyValue, $prefix);
        }

        if (!object_empty($object)) {
            $data->{$this->name} = $object;
        }
    }

    public function inserted(Query $query, mixed $value, int $id): void {
        foreach ($this->properties as $property) {
            if (isset($value->{$property->name})) {
                $property->inserted($query, $value->{$property->name}, $id);
            }
        }
    }

    public function hydrate(mixed $item): mixed {
        if ($item === null) {
            return null;
        }

        $class = $this->class;
        $data = [];

        if (is_object($item)) {
            foreach ($item as $propertyName => $value) {
                $property = $this->properties[$propertyName];
                if ($value = $property->hydrate($value)) {
                    $data[$propertyName] = $value;
                }
            }
        }
        elseif ($this->ref && is_int($item)) {
            $class = $this->data->schema->endpoints[$this->ref->endpoint]
                ->items->class;
            $data['id'] = $item;
        }
        else {
            throw new NotImplemented($this);
        }

        return $class
            ? create($class, $item->type ?? null, $data)
            : (object)$data;
    }

    public function create(Blueprints $data): void {
        throw new NotImplemented($this);
    }

    protected function get_full_name(): string {
        return $this->parent
            ? "{$this->parent->full_name}.{$this->name}"
            : $this->name;
    }
}