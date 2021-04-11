<?php

declare(strict_types=1);

namespace Osm\Data\Data\Properties;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Data\Data\Column;
use Osm\Data\Data\Property;
use Osm\Core\Attributes\Serialized;

/**
 * @property ?Column $column #[Serialized]
 */
class Scalar extends Property
{
    public function __construct(array $data = []) {
        if (isset($data['column'])) {
            $data['column'] = $this->data->create(Column::class, $data['column']);
        }

        parent::__construct($data);
    }

    public function select(TableQuery $query, string $expr) {
        if (isset($this->column)) {
            $query->addSelect("this.{$this->name}");
        }
        else {
            $query->addSelect(
                "this.data->{$this->name} AS {$this->name}");
        }

    }
}