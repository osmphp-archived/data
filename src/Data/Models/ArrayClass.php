<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Meta;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Model;

/**
 * @property string $key #[Schema('M01_schema')]
 * @property string $not_found_message #[Schema('M01_schema')]
 * @property string $not_found_method #[Schema('M01_schema')]
 */
#[Name('array_class'), Schema('M01_schema'), Meta]
class ArrayClass extends Model
{

}