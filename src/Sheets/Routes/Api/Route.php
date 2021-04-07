<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Routes\Api;

use Osm\Core\App;
use Osm\Data\Sheets\Data;
use Osm\Data\Sheets\Query;
use Osm\Framework\Http\Route as BaseRoute;

/**
 * @property Data $data
 * @property string $sheet_name
 * @property Query $query
 */
class Route extends BaseRoute
{
    public function match(): ?Route {
        // `Select` route does all the matching
        return null;
    }

    /** @noinspection PhpUnused */
    protected function get_data(): Data {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->data;
    }

    /** @noinspection PhpUnused */
    protected function get_query(): Query {
        return $this->data->sheet($this->sheet_name);
    }
}