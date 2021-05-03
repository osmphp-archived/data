<?php

declare(strict_types=1);

namespace Osm\Data\Data\Attributes;

interface ModelsData
{
    public function model(\stdClass $classOrProperty): void;
}