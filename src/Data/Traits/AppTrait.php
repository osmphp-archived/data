<?php

declare(strict_types=1);

namespace Osm\Data\Data\Traits;

use Osm\Data\Data\Data;
use Osm\Data\Data\Loader;

/**
 * \stdClass $schema #[Cached('schema')]
 * @property Data $data
 */
trait AppTrait
{
    protected function get_schema(): \stdClass {
        return Loader::new()->load();
    }

    protected function get_data(): Data {
        return Data::new();
    }
}