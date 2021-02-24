<?php

declare(strict_types=1);

namespace Osm\Data\Sheets;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

class Sheet extends Object_
{
    public static ?string $name;

    public function upsert(\stdClass $data): void {
        throw new NotImplemented();
    }
}