<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Traits;

use Osm\Data\Sheets\Sheets;

/**
 * @property Sheets $sheets
 */
trait AppTrait
{
    /** @noinspection PhpUnused */
    protected function get_sheets(): Sheets {
        return Sheets::new();
    }
}