<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\BaseModule;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Data\Data\Models\ArrayClass;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Models\Property;
use Osm\Data\Data\Models\Properties;

/**
 * @property Module $data_module
 */
class MetaLoader extends Object_
{
    public array $identities = ['/classes' => []];

    public function hydrateClasses(array $dehydrated): array {
        return array_map(fn($item) => $this->hydrateClass($item), $dehydrated);
    }

    protected function hydrateClass(\stdClass $dehydrated): Class_ {
        $data = $this->hydrateAttributes($dehydrated);

        return $this->identities['/classes'][$data['id']] = Class_::new($data);
    }

    protected function hydrateAttributes(\stdClass $dehydrated): array {
        $data = [];

        foreach ($dehydrated as $key => $value) {
            $data[$key] = method_exists($this, "hydrate_{$key}")
                ? $this->{"hydrate_{$key}"}($value)
                : $value;
        }

        return $data;
    }

    protected function hydrate_properties(mixed $dehydrated): array {
        return array_map(fn($item) => $this->hydrateProperty($item), $dehydrated);
    }

    protected function hydrate_item(mixed $dehydrated): Property {
        return $this->hydrateProperty($dehydrated);
    }

    protected function hydrateProperty(\stdClass $dehydrated): Property {
        $data = $this->hydrateAttributes($dehydrated);

        $new = "{$this->data_module->models["property/{$data['type']}"]}::new";

        return $new($data);
    }


    public function resolve(mixed $hydrated, array $identities
        , Model $parent = null): mixed
    {
        if ($hydrated === null) {
            return null;
        }

        if (is_array($hydrated)) {
            foreach ($hydrated as $key => $value) {
                $hydrated[$key] = $this->resolve($value, $identities, $parent);
            }

            return $hydrated;
        }

        if (is_object($hydrated)) {
            if (!($hydrated instanceof Model)) {
                return $hydrated;
            }

            foreach ($hydrated as $key => $value) {
                $hydrated->$key = $this->resolve($value, $identities, $hydrated);
            }

            if ($parent) {
                $hydrated->__parent = $parent;
            }

            foreach ($hydrated as $key => $value) {
                if (($pos = strrpos($key, '_id')) ===
                    strlen($key) - strlen('_id'))
                {
                    $ref = substr($key, 0, $pos);
                    $hydrated->$ref = $identities['/classes'][$hydrated->$key];
                }
            }

            return $hydrated;
        }

        return $hydrated;
    }

    protected function get_data_module(): BaseModule {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[Module::class];
    }
}