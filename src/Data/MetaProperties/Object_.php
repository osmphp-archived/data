<?php

declare(strict_types=1);

namespace Osm\Data\Data\MetaProperties;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\MetaProperty;
use Osm\Data\Data\Property;
use function Osm\create;

/**
 * @property MetaProperty[] $properties
 * @property string $class
 */
#[Name('object')]
class Object_ extends MetaProperty
{
    public function hydrate(mixed $item): mixed {
        $data = [];
        foreach ($this->properties as $propertyName => $property) {
            if (isset($item->$propertyName)) {
                $data[$propertyName] = $property->hydrate($item->$propertyName);
            }
        }

        return $this->class
            ? create($this->class, $item->type ?? null, $data)
            : (object)$data;
    }
}