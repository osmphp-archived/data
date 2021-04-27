<?php

declare(strict_types=1);

namespace Osm {

    function object_empty(\stdClass $object): bool {
        /** @noinspection PhpLoopNeverIteratesInspection */
        foreach ($object as $value) {
            return false;
        }

        return true;
    }
}