<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Attributes\Name;
use Osm\Core\BaseModule;
use Osm\Core\Class_;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Property;
use Osm\Data\Data\Attributes\ModelsData;
use Osm\Data\Data\Attributes\Schema as SchemaAttribute;
use Osm\Data\Data\Module as DataModule;

/**
 * @property string $schema
 * @property DataModule $data_module
 */
class Reflection extends Object_
{
    /**
     * @var \stdClass[]
     */
    public array $classes = [];

    public function reflect(): static {
        global $osm_app; /* @var App $osm_app */

        foreach ($this->data_module->models as $name => $className) {
            $this->reflectClass($this->modelName($name), $osm_app->classes[$className]);
        }

        return $this;
    }

    protected function reflectClass(string $name, Class_ $class): void {
        global $osm_app; /* @var App $osm_app */

        if ($this->belongs($class)) {
            $target = $this->createClass($name);

            foreach ($class->attributes as $attribute) {
                $this->reflectAttribute($target, $attribute);
            }
        }

        foreach ($class->properties as $property) {
            $this->reflectProperty($name, $property);
        }
    }

    protected function get_data_module(): BaseModule {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[DataModule::class];
    }

    protected function reflectAttribute(\stdClass $classOrProperty,
        mixed $attribute): void
    {
        if (is_array($attribute)) {
            foreach ($attribute as $attributeItem) {
                if ($attributeItem instanceof ModelsData) {
                    $attributeItem->model($classOrProperty);
                }
            }
        }

        if ($attribute instanceof ModelsData) {
            $attribute->model($classOrProperty);
        }
    }

    protected function reflectProperty(string $modelName, Property $property)
        : void
    {
        if (!$this->belongs($property)) {
            return;
        }

        $target = $this->createProperty($modelName, $property);
        $this->inferType($target, $property);

        foreach ($property->attributes as $attribute) {
            $this->reflectAttribute($target, $attribute);
        }
    }

    protected function belongs(Class_|Property $classOrProperty): bool {
        if (!isset($classOrProperty->attributes[SchemaAttribute::class])) {
            return false;
        }

        return $classOrProperty->attributes[SchemaAttribute::class]?->name ==
            $this->schema;
    }

    protected function inferType(\stdClass $target, Property $property): void {
        if ($property->array) {
            $target->type = 'array';
            $target->item = (object)[];
            $this->inferItemType($target->item, $property);
            return;
        }

        $this->inferItemType($target, $property);
    }

    protected function inferItemType(\stdClass $target, Property $property): void {
        global $osm_app; /* @var App $osm_app */

        switch ($property->type) {
            case 'string':
                $target->type = 'string';
                break;
            default:
                $target->type = 'object';

                if (is_subclass_of($property->type, Model::class,
                    true))
                {
                    $target->object_class = $this->createClass(
                        $osm_app->classes[$property->type]
                            ->attributes[Name::class]->name);
                }
                break;
        };
    }

    protected function createClass(string $modelName): \stdClass {
        if (!isset($this->classes[$modelName])) {
            $this->classes[$modelName] = (object)[
                'name' => $modelName,
                'properties' => [],
            ];
        }
        return $this->classes[$modelName];
    }

    protected function createProperty(string $modelName, Property $property): \stdClass {
        $class = $this->createClass($modelName);

        if (!isset($class->properties[$property->name])) {
            $class->properties[$property->name] = (object)[
                'name' => $property->name,
            ];
        }
        return $class->properties[$property->name];
    }

    protected function modelName(string $name): string {
        return ($pos = strpos($name, '/')) !== false
            ? substr($name, 0, $pos)
            : $name;
    }
}