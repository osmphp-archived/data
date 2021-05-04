<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models\Properties;

use Osm\Core\App;
use Osm\Core\Array_ as CoreArray;
use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Exceptions\CircularReference;
use Osm\Data\Data\Exceptions\InvalidType;
use Osm\Data\Data\Model;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Models\Property;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Module as DataModule;
use Osm\Framework\Cache\Descendants;
use function Osm\__;
use function Osm\create;

/**
 * @property int $type_class_id #[Schema('schema')]
 * @property Class_ $object_class
 * @property DataModule $data_module
 */
#[Name('property/object')]
class Object_ extends Property
{
    public string $type = 'object';

    public function hydrate(mixed $dehydrated, array &$identities = null)
        : mixed
    {
        if ($identities === null) {
            $identities = [];
        }

        if ($dehydrated === null) {
            return null;
        }

        if (!is_object($dehydrated)) {
            throw new InvalidType(__("Object expected"));
        }

        // register all dehydrated objects in `$identities['all']` map. If some
        // object gets to be registered twice, it's a circular reference
        if (!isset($identities['all'])) {
            $identities['all'] = new \WeakMap();
        }
        if (isset($identities['all'][$dehydrated])) {
            throw new CircularReference(__("Circular reference detected"));
        }
        $identities['all'][$dehydrated] = true;

        if (!$this->object_class) {
            return $dehydrated;
        }

        $data = [];

        foreach ($dehydrated as $propertyName => $value) {
            $property = $this->object_class->properties[$propertyName];
            if ($value = $property->hydrate($value, $identities)) {
                $data[$propertyName] = $value;
            }
        }

        $hydrated = ($className = $this->className($data))
            ? create($className, null, $data)
            : (object)$data;

        if (isset($hydrated->id) && isset($this->object_class->endpoint)) {
            if (!isset($identities[$this->object_class->endpoint])) {
                $identities[$this->object_class->endpoint] = [];
                $identities[$this->object_class->endpoint][$hydrated->id]
                    = $hydrated;
            }
        }

        return $hydrated;

    }

    public function dehydrate(mixed $hydrated): mixed {
        if ($hydrated === null) {
            return null;
        }

        if (!is_object($hydrated)) {
            throw new InvalidType(__("Object expected"));
        }

        if (!$this->object_class) {
            return $hydrated;
        }

        $dehydrated = new \stdClass();

        foreach ($hydrated as $propertyName => $value) {
            $property = $this->object_class->properties[$propertyName] ?? null;
            if ($property && ($value = $property->dehydrate($value))) {
                $dehydrated->$propertyName = $value;
            }
        }

        return $dehydrated;
    }

    protected function get_data_module(): BaseModule {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[DataModule::class];
    }

    protected function className(array $data): ?string {
        if (!isset($this->object_class->name)) {
            return null;
        }

        $name = $this->object_class->name;

        if ($this->object_class->subtype_by &&
            isset($data[$this->object_class->subtype_by]))
        {
            $name = "{$name}/{$data[$this->object_class->subtype_by]}";
        }

        return $this->data_module->models[$name];
    }

    public function resolve(mixed $hydrated, array &$identities = null,
        ?Model $parent = null): void
    {
        if ($hydrated === null) {
            return;
        }

        if (!is_object($hydrated)) {
            throw new InvalidType(__("Object expected"));
        }

        if (!$this->object_class) {
            return;
        }

        if ($parent) {
            $hydrated->__parent = $parent;
        }

        foreach ($hydrated as $propertyName => $value) {
            $property = $this->object_class->properties[$propertyName] ?? null;
            if ($property) {
                $property->resolve($value, $identities, $hydrated);
            }
        }
    }
}