<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Types;

use Osm\Core\Attributes\Name;
use Osm\Data\Sheets\Enums\Types;
use Osm\Data\Sheets\Type;

#[Name(Types::OBJECT_)]
class Object_ extends Type
{
    public function save(array &$values, \stdClass $data): void {
        if (property_exists($data, $this->column->name)) {
            $value = $data->{$this->column->name};
            if ($value === null | is_string($value)) {
                $values["{$this->column->name}_name"] = $value;
                $values["{$this->column->name}_data"] = null;
            }
            else {
                $value = clone $value;
                $values["{$this->column->name}_name"] = $value->name;
                unset($value->name);
                $values["{$this->column->name}_data"] = json_encode($value);
            }
        }
    }
}