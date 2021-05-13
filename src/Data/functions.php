<?php

declare(strict_types=1);

namespace Osm {

    use Osm\Core\App;
    use Osm\Core\Array_;
    use Osm\Data\Data\MetaLoader;
    use Osm\Data\Data\Models\ArrayClass;
    use Osm\Data\Data\Models\Class_;
    use Osm\Data\Data\Models\Property;
    use Osm\Data\Data\Module;
    use Osm\Data\Data\Reflector;
    use Osm\Data\Data\Models\Properties;

    function object_empty(\stdClass $object): bool {
        /** @noinspection PhpLoopNeverIteratesInspection */
        foreach ($object as $value) {
            return false;
        }

        return true;
    }

    function id(string $prefix = 'new-'): string {
        static $counter = 0;

        return $prefix . ++$counter;
    }

    function standard_column(string $columnName): \stdClass {
        global $osm_app; /* @var App $osm_app */

        return deep_clone($osm_app->data->dehydrated_meta['class']
            ->properties[$columnName]);
    }

    function deep_clone(mixed $value): mixed {
        if (is_object($value)) {
            $value = clone $value;

            foreach ($value as $key => $item) {
                $value->$key = deep_clone($item);
            }

            return $value;
        }

        if (is_array($value)) {
            return array_map(fn($item) => deep_clone($item), $value);
        }

        return $value;
    }
}