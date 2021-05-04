<?php

declare(strict_types=1);

namespace Osm\Data\Data\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class SubtypeBy implements ModelsData
{
    public function __construct(public string $property_name) {
    }

    public function model(\stdClass $classOrProperty): void {
        $classOrProperty->subtype_by = $this->property_name;
    }
}