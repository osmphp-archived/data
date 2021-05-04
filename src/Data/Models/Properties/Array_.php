<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models\Properties;

use Osm\Core\Array_ as CoreArray;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Exceptions\InvalidType;
use Osm\Data\Data\Model;
use Osm\Data\Data\Models\ArrayClass;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Models\Property;
use Osm\Data\Data\Attributes\Schema;
use function Osm\__;

/**
 * @property Property $item
 * @property ArrayClass $array_class
 */
#[Name('property/array')]
class Array_ extends Property
{
    public string $type = 'array';

    public function hydrate(mixed $dehydrated, array &$identities = null)
        : mixed
    {
        if ($identities === null) {
            $identities = [];
        }

        if ($dehydrated === null) {
            return null;
        }

        if (!is_array($dehydrated)) {
            throw new InvalidType(__("Array expected"));
        }

        $hydrated = array_map(
            fn($value) => $this->item->hydrate($value, $identities),
            $dehydrated);

        if (!$this->array_class) {
            return $hydrated;
        }

        if ($this->array_class->not_found_message) {
            return new CoreArray($hydrated,
                $this->array_class->not_found_message);
        }

        throw new NotImplemented($this);
    }

    public function dehydrate(mixed $hydrated): mixed {
        if ($hydrated === null) {
            return null;
        }

        if (is_array($hydrated)) {
            return array_map(
                fn($value) => $this->item->dehydrate($value),
                $hydrated);
        }

        if ($hydrated instanceof CoreArray) {
            return $hydrated
                ->map(fn($value) => $this->item->dehydrate($value))
                ->items;
        }

        throw new InvalidType(__("Array expected"));
    }

    public function resolve(mixed $hydrated, array &$identities = null,
        ?Model $parent = null): void
    {
        if ($hydrated === null) {
            return;
        }

        if (!is_array($hydrated) && !($hydrated instanceof CoreArray)) {
            throw new InvalidType(__("Array expected"));
        }

        foreach ($hydrated as $value) {
            $this->item->resolve($value, $identities, $parent);
        }
    }
}