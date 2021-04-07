<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Traits;

use Osm\Data\Sheets\Data;

/**
 * @property Data $data
 */
trait AppTrait
{
    protected function get_data(): Data {
        return Data::new();
    }
}