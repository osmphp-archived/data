<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Traits;

use Osm\Data\Sheets\Sheets;

/**
 * @property Sheets $data
 */
trait AppTrait
{
    /** @noinspection PhpUnused */
    protected function get_data(): Sheets {
        return Sheets::new();
    }
}