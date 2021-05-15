<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Data\Data\Models\ArrayClass;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Models\Property;
use Osm\Framework\Cache\Attributes\Cached;
use Osm\Framework\Cache\Descendants;
use Osm\Framework\Db\Db;
use function Osm\create;
use Osm\Data\Data\Models\Properties;

/**
 * @property \stdClass[] $dehydrated_meta #[Cached('data|meta')]
 * @property Class_[] $meta
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

    protected function get_dehydrated_meta(): array {
        return $this->reflect(null);
    }

    protected function get_meta(): array {
        $meta = MetaLoader::new();
        $hydrated = $meta->hydrateClasses($this->dehydrated_meta);
        return $meta->resolve($hydrated, $meta->identities);
    }

    public function arrayOf(Class_ $item, string $notFoundMessage)
        : Properties\Array_
    {
        return Properties\Array_::new([
            'item' => Properties\Object_::new([
                'object_class' => $item]),
            'array_class' => ArrayClass::new([
                'not_found_message' => $notFoundMessage,
            ]),
        ]);
    }

    public function objectOf(Class_ $item): Properties\Object_ {
        return Properties\Object_::new(['object_class' => $item]);
    }

    public function migrateUp(Db $db, string $schema,
        string $module = null): void
    {
        $meta = $this->meta;

        $property = $this->arrayOf($meta['class'],
            "Undefined model ':key'");
        $dehydrated = $this->reflect($schema, module: $module);

        foreach ($property->hydrateAndResolve($dehydrated) as $class) {
            /* @var Class_ $class */
            $class->createTable($db);
        }
    }

    public function migrateDown(Db $db, string $schema,
        string $module = null): void
    {
        $meta = $this->meta;

        $property = $this->objectOf($meta['class']);
        $dehydrated = $this->reflect($schema, module: $module);

        foreach ($dehydrated as $item) {
            /* @var Class_ $class */
            $class = $property->hydrateAndResolve($dehydrated);
            $class->createTable($db);
        }
    }
}