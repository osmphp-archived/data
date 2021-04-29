<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Migration;
use Osm\Data\Data\Model;

/**
 * @property string $key #[Migration('schema')]
 * @property string $not_found_message #[Migration('schema')]
 * @property string $not_found_method #[Migration('schema')]
 */
#[Name('array_class'), Migration('schema')]
class ArrayClass extends Model
{

}