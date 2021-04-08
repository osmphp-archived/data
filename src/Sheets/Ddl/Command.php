<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Ddl;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;

class Command extends Object_
{
    public function run(): void {
        throw new NotImplemented($this);
    }

    public function undo(): void {
        throw new NotImplemented($this);
    }
}