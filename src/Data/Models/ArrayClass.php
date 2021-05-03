<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Model;

/**
 * @property string $key #[Schema('schema')]
 * @property string $not_found_message #[Schema('schema')]
 * @property string $not_found_method #[Schema('schema')]
 */
#[Name('array_class'), Schema('schema')]
class ArrayClass extends Model
{

}