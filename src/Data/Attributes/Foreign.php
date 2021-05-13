<?php

declare(strict_types=1);

namespace Osm\Data\Data\Attributes;

use Osm\Core\Exceptions\NotImplemented;

#[\Attribute(\Attribute::TARGET_PROPERTY)]
class Foreign implements ModelsData
{
    public function __construct(
        public string $class,
        public ?string $on_delete = null,
    )
    {
    }

    public function model(\stdClass $classOrProperty): void {
        $foreign = new \stdClass();

        foreach ($this as $key => $value) {
            if ($value !== null) {
                $foreign->$key = $value;
            }
        }

        $classOrProperty->foreign = $foreign;
    }
}