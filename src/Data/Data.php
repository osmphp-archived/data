<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Models\Property;
use Osm\Framework\Cache\Attributes\Cached;
use Osm\Framework\Cache\Descendants;
use function Osm\create;
use Osm\Data\Data\Models\Properties;

/**
 * @property Class_[] $meta #[Cached('data|meta')]
 */
class Data extends Object_
{
    public function reflect(?string $schema, array $classIds = [],
        string $module = null): array
    {
        return Reflector::new([
            'module' => $module,
            'schema' => $schema,
            'class_ids' => $classIds,
        ])->reflect()->classes;
    }

    protected function get_meta(): array {
        $meta = MetaLoader::new();
        $classes = $this->reflect(null);

        $hydrated = $meta->hydrateClasses($classes);
        return $meta->resolve($hydrated, $meta->identities);
    }
}