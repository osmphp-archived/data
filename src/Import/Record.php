<?php

declare(strict_types=1);

namespace Osm\Data\Import;

/**
 * @property string $sheet_name
 * @property Schema[] $children
 */
class Record extends Schema
{
    public function import(string $path, array $variables = []): void {

    }
}