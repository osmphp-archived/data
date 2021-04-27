<?php

declare(strict_types=1);

namespace Osm\Data\Data\Exceptions;

use Osm\Data\Data\Property;
use function Osm\__;

class UndefinedProperty extends \Exception
{
    public function __construct(Property $parent, string $propertyName,
        $code = 0, \Throwable $previous = null)
    {
        for (; $parent; $parent = $parent->parent) {
            $propertyName = "{$parent->name}.{$propertyName}";
        }

        parent::__construct(__("Unknown property ':property'",
            ['property' => $propertyName]), $code, $previous);
    }
}