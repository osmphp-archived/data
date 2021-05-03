<?php

declare(strict_types=1);

namespace Osm\Data\Data\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Schema
{
    public function __construct(public string $name) {
    }
}