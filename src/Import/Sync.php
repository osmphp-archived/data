<?php

declare(strict_types=1);

namespace Osm\Data\Import;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

class Sync extends Object_
{
    public function syncDirectory() {
        throw new NotImplemented();
    }
}