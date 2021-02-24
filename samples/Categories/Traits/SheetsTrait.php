<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Categories\Traits;

use Osm\Data\Sheets\Sheet;

/**
 * @property Sheet $t_categories
 */
trait SheetsTrait
{
    /** @noinspection PhpUnused */
    protected function get_t_categories(): Sheet {
        return Sheet::new(['name' => 't_categories']);
    }
}