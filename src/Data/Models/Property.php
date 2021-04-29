<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Migration;
use Osm\Data\Data\Model;
use function Osm\dehydrate;

/**
 * @property int $parent_id #[Migration('schema')]
 * @property string $name #[Migration('schema')]
 * @property string $type #[Migration('schema')]
 */
#[Name('property'), Migration('schema'), Endpoint('/properties')]
class Property extends Model
{
    public function hydrate(mixed $dehydrated): mixed {
        throw new NotImplemented($this);
    }

    public function dehydrate(mixed $hydrated): mixed {
        throw new NotImplemented($this);
    }
}