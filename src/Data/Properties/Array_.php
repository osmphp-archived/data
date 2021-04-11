<?php

declare(strict_types=1);

namespace Osm\Data\Data\Properties;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\Attributes\Name;
use Osm\Data\Data\Filters\Condition;
use Osm\Data\Data\Property;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?string $endpoint #[Serialized]
 * @property Object_|Property $items #[Serialized]
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
}