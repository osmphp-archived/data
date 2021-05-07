<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Meta;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Model;

/**
 * @property string $name #[Schema('M01_schema')]
 * @property int $parent_id #[Schema('M01_schema')]
 * @property string $endpoint #[Schema('M01_schema')]
 * @property Property[] $properties #[Schema('M01_schema')]
 * @property string $subtype_by #[Schema('M01_schema')]
 */
#[Name('class'), Schema('M01_schema'), Endpoint('/classes'), Meta]
class Class_ extends Model
{

}