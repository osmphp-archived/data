<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Model;

/**
 * @property string $name #[Schema('schema')]
 * @property int $parent_id #[Schema('schema')]
 * @property string $endpoint #[Schema('schema')]
 * @property Property[] $properties
 * @property string $subtype_by #[Schema('schema')]
 */
#[Name('class'), Schema('schema'), Endpoint('/classes')]
class Class_ extends Model
{

}