<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\Object_;
use Osm\Core\Attributes\Serialized;

/**
 * @property string $table #[Serialized]
 * @property string $on_delete #[Serialized]
 */
class Ref extends Object_
{

}