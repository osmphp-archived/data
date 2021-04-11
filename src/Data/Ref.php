<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $endpoint #[Serialized]
 * @property string $property #[Serialized]
 * @property ?string $on_delete #[Serialized]
 */
class Ref extends Object_
{
    protected function get_property(): string {
        return 'id';
    }
}