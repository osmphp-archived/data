<?php

declare(strict_types=1);

namespace Osm\Data\Import\UpsertReaders;

class Json extends UpsertReader
{
    public string $name = 'json';

    public function read(string $filename): \stdClass {
        return json_decode(file_get_contents($filename));
    }
}