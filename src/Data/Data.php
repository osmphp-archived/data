<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Framework\Cache\Attributes\Cached;
use Osm\Framework\Cache\Descendants;
use function Osm\create;

/**
 * @property Schema $schema #[Cached('data|schema', callback: 'schemaLoaded')]
 */
class Data extends Object_
{
    public function query(string $endpoint): Query|Object_ {
        return create(Query::class, $endpoint->endpoint,
            ['endpoint' => $endpoint]);
    }

    protected function get_schema(): Schema {
        return Schema::new();
    }

    protected function schemaLoaded(): void {
        $this->schema->loaded();
    }
}