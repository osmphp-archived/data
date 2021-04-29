<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models\Properties;

use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Exceptions\InvalidType;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Models\Property;
use Osm\Data\Data\Attributes\Migration;
use function Osm\__;

/**
 * @property int $type_class_id #[Migration('schema')]
 * @property Class_ $object_class
 */
#[Name('object')]
class Object_ extends Property
{
    public string $type = 'object';

    public function hydrate(mixed $dehydrated): mixed {
        if ($dehydrated === null) {
            return null;
        }

        if (!is_object($dehydrated)) {
            throw new InvalidType(__("Object expected"));
        }

        if (!$this->object_class) {
            return $dehydrated;
        }

        $data = [];

        foreach ($dehydrated as $propertyName => $value) {
            $property = $this->object_class->properties[$propertyName];
            if ($value = $property->hydrate($value)) {
                $data[$propertyName] = $value;
            }
        }

        if (!$this->object_class->name) {
            return (object)$data;
        }

        throw new NotImplemented($this);
    }
}