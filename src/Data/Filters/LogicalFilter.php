<?php

declare(strict_types=1);

namespace Osm\Data\Data\Filters;

class LogicalFilter extends Filter
{
    /**
     * @var Filter[]
     */
    public array $filters = [];
}