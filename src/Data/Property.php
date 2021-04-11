<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;
use Osm\Data\Data\Filters\Condition;

/**
 * @property int $id #[Serialized]
 * @property ?int $parent_id #[Serialized]
 * @property string $name #[Serialized]
 * @property string $type #[Serialized]
 * @property Data $data
 * @property ?Property $parent
 */
class Property extends Object_
{
    protected function get_data(): Data {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->data;
    }

    protected function get_parent(): ?Property {
        return $this->parent_id
            ? $this->data->schema->all[$this->parent_id]
            : null;
    }

    public function select(TableQuery $query, string $expr) {
        throw new NotImplemented($this);
    }

    public function filter(TableQuery $query, string $expr,
        Condition $condition): void
    {
        throw new NotImplemented($this);
    }

}