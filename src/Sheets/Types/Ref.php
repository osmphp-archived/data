<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Types;

use Osm\Core\Attributes\Name;
use Osm\Data\Sheets\Enums\Types;
use Osm\Data\Sheets\Type;

#[Name(Types::REF)]
class Ref extends Type
{
    public function save(array &$values, \stdClass $data): void {
        if (isset($data->{$this->column->name})) {
            $values["{$this->column->name}_id"] = $data->{$this->column->name};
        }
    }

}