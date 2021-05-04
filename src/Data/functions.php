<?php

declare(strict_types=1);

namespace Osm {

    use Osm\Data\Data\Reflection;

    function object_empty(\stdClass $object): bool {
        /** @noinspection PhpLoopNeverIteratesInspection */
        foreach ($object as $value) {
            return false;
        }

        return true;
    }

    function reflect(string $schema, array $classIds = [],
        string $module = null): Reflection
    {
        return Reflection::new([
            'module' => $module,
            'schema' => $schema,
            'class_ids' => $classIds,
        ])->reflect();
    }

    function id(string $prefix = 'new-'): string {
        static $counter = 0;

        return $prefix . ++$counter;
    }
}