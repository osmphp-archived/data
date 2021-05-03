<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Attributes\SubtypeBy;
use Osm\Data\Data\Model;

/**
 * @property int $parent_id #[Schema('schema')]
 * @property string $name #[Schema('schema')]
 * @property string $type #[Schema('schema')]
 */
#[Name('property'), Schema('schema'), Endpoint('/properties'), SubtypeBy('type')]
class Property extends Model
{
    public function hydrate(mixed $dehydrated): mixed {
        throw new NotImplemented($this);
    }

    public function dehydrate(mixed $hydrated): mixed {
        throw new NotImplemented($this);
    }

    public function parent(mixed $hydrated, ?Model $parent = null): void {
        throw new NotImplemented($this);
    }
}