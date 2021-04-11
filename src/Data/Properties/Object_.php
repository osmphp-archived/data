<?php

declare(strict_types=1);

namespace Osm\Data\Data\Properties;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Filters\Condition;
use Osm\Data\Data\Property;
use Osm\Data\Data\Ref;
use Osm\Core\Attributes\Serialized;

/**
 * @property Property[] $properties
 * @property ?Ref $ref #[Serialized]
 */
#[Name('object')]
class Object_ extends Property
{
    public function __construct(array $data = []) {
        if (isset($data['ref'])) {
            $data['ref'] = $this->data->create(Ref::class, $data['ref']);
        }

        parent::__construct($data);
    }

    protected function get_properties(): array {
        $properties = [];

        foreach ($this->data->schema->child_ids[$this->id] ?? [] as $id) {
            $properties[$this->data->schema->all[$id]->name] =
                $this->data->schema->all[$id];
        }

        return $properties;
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
        $a = 1;
    }
}