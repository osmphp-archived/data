<?php

declare(strict_types=1);

namespace Osm {

    use Osm\Data\Data\CheckedArray;

    function object_empty(\stdClass $object): bool {
        /** @noinspection PhpLoopNeverIteratesInspection */
        foreach ($object as $value) {
            return false;
        }

        return true;
    }

    function array_check(array $items, string|\Closure $message): CheckedArray {
        return new CheckedArray($items, $message);
    }
}