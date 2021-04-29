<?php

declare(strict_types=1);

namespace Osm\Data\Data\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class SubtypeBy
{
    public function __construct(public string $property_name) {
    }
}