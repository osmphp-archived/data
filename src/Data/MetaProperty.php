<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

/**
 * @property int $id
 * @property MetaProperty $parent
 */
class MetaProperty extends Object_
{
    public function hydrate(mixed $item): mixed {
        throw new NotImplemented($this);
    }
}