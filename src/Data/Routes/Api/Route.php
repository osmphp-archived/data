<?php

declare(strict_types=1);

namespace Osm\Data\Data\Routes\Api;

use Osm\Core\App;
use Osm\Data\Data\Data;
use Osm\Data\Data\Property;
use Osm\Data\Data\Query;
use Osm\Data\Data\Schema;
use Osm\Framework\Http\Route as BaseRoute;

/**
 * @property Data $data
 * @property Schema $schema
 * @property Property $endpoint
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
        return $this->data->query($this->endpoint);
    }

    protected function get_schema(): Schema {
        return $this->data->schema;
    }
}