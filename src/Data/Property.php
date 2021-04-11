<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property int $id #[Serialized]
 * @property ?int $parent_id #[Serialized]
 * @property string $name #[Serialized]
 * @property string $type #[Serialized]
 * @property Schema $schema
 * @property ?Property $parent
 */
class Property extends Object_
{
    protected function get_schema(): Schema {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->data->schema;
    }

    protected function get_parent(): ?Property {
        return $this->parent_id ? $this->schema->all[$this->parent_id] : null;
    }
}