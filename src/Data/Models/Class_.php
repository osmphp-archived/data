<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Migration;
use Osm\Data\Data\Model;

/**
 * @property string $name #[Migration('schema')]
 * @property int $parent_id #[Migration('schema')]
 * @property string $endpoint #[Migration('schema')]
 * @property Property[] $properties
 */
#[Name('class'), Migration('schema'), Endpoint('/classes')]
class Class_ extends Model
{

}