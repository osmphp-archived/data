<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Data\Data\Hints\Property;
use Osm\Framework\Search\Query as SearchQuery;

/**
 * @property Data $data
 */
class Filter extends Object_
{
    public function search(\stdClass|Property $property, SearchQuery $query)
        : void
    {
        throw new NotImplemented($this);
    }

    public function filter(\stdClass|Property $property, TableQuery $query)
        : void
    {
        throw new NotImplemented($this);
    }

    /** @noinspection PhpUnused */
    protected function get_data(): Data {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->data;
    }
}