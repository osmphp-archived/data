<?php

declare(strict_types=1);

namespace Osm\Data\Data\Attributes;

#[\Attribute(\Attribute::TARGET_CLASS)]
class Endpoint implements ModelsData
{
    public function __construct(public string $route) {
    }

    public function model(\stdClass $classOrProperty): void {
        $classOrProperty->endpoint = $this->route;
    }
}