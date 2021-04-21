<?php

declare(strict_types=1);

namespace Osm\Data\Data\Properties;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Column;
use Osm\Data\Data\Property;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Data\Query;
use function Osm\create;

/**
 * @property ?Column $column #[Serialized]
 */
class Scalar extends Property
{
    public function select(TableQuery $query, string $expr) {
        if (isset($this->column)) {
            $query->addSelect("this.{$this->name}");
        }
        else {
            $query->addSelect(
                "this.data->{$this->name} AS {$this->name}");
        }
    }

    public function inserting(Query $query, \stdClass $values, \stdClass $data,
        mixed $value, string $prefix = ''): void
    {
        if (isset($this->column)) {
            $values->{$prefix . $this->name} = $value;
        }
        else {
            $data->{$this->name} = $value;
        }
    }

    public function hydrate(mixed $item): mixed {
        return $item;
    }

    public function create(Blueprints $data): void {
        if (!$this->column) {
            return;
        }

        throw new NotImplemented($this);
    }
}