<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\Object_;
use Osm\Data\Data\Attributes\Migration;

/**
 * @property int $id #[Migration('standard')]
 * @property \stdClass $json #[Migration('standard')]
 */
class Model extends Object_
{

}