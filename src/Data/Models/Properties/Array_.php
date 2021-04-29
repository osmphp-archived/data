<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models\Properties;

use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Exceptions\InvalidType;
use Osm\Data\Data\Models\ArrayClass;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Models\Property;
use Osm\Data\Data\Attributes\Migration;
use function Osm\__;

/**
 * @property Property $item
 * @property ArrayClass $array_class
 */
#[Name('array')]
class Array_ extends Property
{
    public string $type = 'array';

    public function hydrate(mixed $dehydrated): mixed {
        if ($dehydrated === null) {
            return null;
        }

        if (!is_array($dehydrated)) {
            throw new InvalidType(__("Array expected"));
        }

        $hydrated = array_map(
            fn($value) => $this->item->hydrate($value),
            $dehydrated);

        if (!$this->array_class) {
            return $hydrated;
        }

        throw new NotImplemented($this);
    }
}