<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Framework\Cache\Attributes\Cached;
use Osm\Framework\Cache\Descendants;

/**
 * @property Schema $schema #[Cached('data|schema')]
 * @property Descendants $descendants
 */
class Data extends Object_
{
    public function query(Property $endpoint): Query {
        return Query::new(['endpoint' => $endpoint]);
    }

    protected function get_schema(): Schema {
        return Schema::new();
    }

    public function create(string $className, \stdClass $item): Object_ {
        $new = isset($item->type)
            ? "{$this->descendants->byName($className)[$item->type]}::new"
            : "{$className}::new";

        return $new((array)$item);
    }

    protected function get_descendants(): Descendants {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->descendants;
    }
}