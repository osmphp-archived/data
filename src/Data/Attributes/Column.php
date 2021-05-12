<?php

declare(strict_types=1);

namespace Osm\Data\Data\Attributes;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Column implements ModelsData
{
    public function __construct(
        public string $type,
        public ?bool $nullable = null,
        public ?bool $unsigned = null,
        public ?bool $auto_increment = null,
        public ?bool $index = null,
        public ?bool $unique = null,
        public ?int $length = null,
    )
    {
    }

    public function model(\stdClass $classOrProperty): void {
        $column = new \stdClass();

        foreach ($this as $key => $value) {
            if ($value !== null) {
                $column->$key = $value;
            }
        }

        $classOrProperty->column = $column;
    }
}