<?php

declare(strict_types=1);

namespace Osm\Data\Data\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_PROPERTY)]
class Schema
{
    public function __construct(public string $name) {
    }
}