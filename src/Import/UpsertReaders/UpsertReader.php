<?php

declare(strict_types=1);

namespace Osm\Data\Import\UpsertReaders;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

/**
 * @property string $name
 */
class UpsertReader extends Object_
{
    public static ?string $ext;

    public function read(string $filename): \stdClass {
        throw new NotImplemented();
    }
}