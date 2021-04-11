<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Illuminate\Database\Query\Builder as TableQuery;
use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Data\Data\Filters\Condition;
use Osm\Framework\Cache\Attributes\Cached;
use Osm\Framework\Cache\Cache;
use Osm\Framework\Cache\Descendants;
use Osm\Framework\Db\Db;
use function Osm\merge;

/**
 * @property Schema $schema #[Cached('data|schema')]
 */
class Data extends Object_
{
    public function query(Property $endpoint): Query {
        return Query::new(['endpoint' => $endpoint]);
    }

    protected function get_schema(): Schema {
        return Schema::new();
    }
}