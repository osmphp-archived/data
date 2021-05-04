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
use Osm\Data\Data\Attributes\Ref as RefAttribute;
use Osm\Data\Data\Attributes\Schema as SchemaAttribute;
use Osm\Data\Data\Exceptions\ReflectionError;
use Osm\Data\Data\Module as DataModule;
use function Osm\__;
use function Osm\id;

/**
 * @property string $module
 * @property string $schema
 * @property int[] $class_ids
 *
 * @property DataModule $data_module
 * @property string $namespace
 *
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
            $this->reflectClass($this->modelName($name),
                $osm_app->classes[$className], $this->modelName($name) == $name);
        }

        $this->removeEmptyClasses();
        $this->resolveClassIds();
        return $this;
    }

    protected function reflectClass(string $name, Class_ $class, bool $isModel)
        : void
    {
        $model = $this->createClass($name);

        if ($isModel) {
            $model->id = id();

            foreach ($class->attributes as $attribute) {
                $this->reflectAttribute($model, $attribute);
            }
        }

        foreach ($class->properties as $property) {
            $this->reflectProperty($model, $property);
        }
    }

    protected function get_data_module(): BaseModule {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[DataModule::class];
    }

    protected function reflectAttribute(\stdClass $modelOrProperty,
        mixed $attribute): void
    {
        if (is_array($attribute)) {
            foreach ($attribute as $attributeItem) {
                if ($attributeItem instanceof ModelsData) {
                    $attributeItem->model($modelOrProperty);
                }
            }
        }

        if ($attribute instanceof ModelsData) {
            $attribute->model($modelOrProperty);
        }
    }

    protected function reflectProperty(\stdClass $model, Property $property)
        : void
    {
        if (!$this->propertyBelongs($property)) {
            return;
        }

        $target = $this->createProperty($model, $property);
        $this->inferType($target, $property);

        foreach ($property->attributes as $attribute) {
            $this->reflectAttribute($target, $attribute);
        }
    }

    protected function classBelongs(Class_ $class): bool {
        if (!isset($class->attributes[SchemaAttribute::class])) {
            return false;
        }

        if ($class->attributes[SchemaAttribute::class]?->name != $this->schema) {
            return false;
        }

        if (!str_starts_with($class->name, $this->namespace)) {
            return false;
        }

        return true;
    }

    protected function propertyBelongs(Property $property): bool {
        if (!isset($property->attributes[SchemaAttribute::class])) {
            return false;
        }

        if ($property->attributes[SchemaAttribute::class]?->name != $this->schema) {
            return false;
        }

        if (!str_starts_with($property->class->name, $this->namespace)) {
            return false;
        }

        return true;
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
            case '?string':
                $target->type = 'string';
                break;
            case 'int':
            case '?int':
            case 'float':
            case '?float':
                $target->type = 'number';
                break;
            case 'bool':
            case '?bool':
                $target->type = 'boolean';
                break;
            case 'stdClass':
            case '?stdClass':
                $target->type = 'object';
                break;
            default:
                if (class_exists($property->type)) {
                    $target->type = isset($property->attributes[RefAttribute::class])
                        ? 'ref'
                        : 'object';

                    if (is_subclass_of($property->type, Model::class,
                        true))
                    {
                        $model = $this->modelName($osm_app->classes[$property->type]
                            ->attributes[Name::class]->name);
                        $target->object_class = $model;
                    }
                    else {
                        throw new ReflectionError(__(
                            "':property' type should derive from ':model_class'", [
                                'property' => $property->class_name . '::' .
                                    $property->name,
                                'model_class' => Model::class,
                            ]));
                    }

                    break;
                }
                else {
                    throw new NotImplemented();
                }
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

    protected function createProperty(\stdClass $model, Property $property)
        : \stdClass
    {
        if (!isset($model->properties[$property->name])) {
            $model->properties[$property->name] = (object)[
                'name' => $property->name,
            ];
        }
        return $model->properties[$property->name];
    }

    protected function modelName(string $name): string {
        return ($pos = strpos($name, '/')) !== false
            ? substr($name, 0, $pos)
            : $name;
    }

    protected function get_namespace(): string {
        return substr($this->module, 0, strrpos($this->module, '\\') + 1);
    }

    protected function removeEmptyClasses(): void {
        foreach (array_keys($this->classes) as $model) {
            if (empty($this->classes[$model]->properties)) {
                unset($this->classes[$model]);
            }
        }
    }

    protected function resolveClassIds(): void {
        foreach ($this->classes as $class) {
            if (!isset($class->id)) {
                if (!isset($this->class_ids[$class->name])) {
                    throw new ReflectionError(__(
                        "ID for class ':class' not provided",
                        ['class' => $class->name]));
                }

                $class->id = $this->class_ids[$class->name];
            }

            foreach ($class->properties as $property) {
                $this->resolveClassId($property);
            }
        }
    }

    protected function resolveClassId(\stdClass $property): void {
        switch ($property->type) {
            case 'array':
                $this->resolveClassId($property->item);
                break;
            case 'object':
            case 'ref':
                if (isset($property->object_class)) {
                    if (isset($this->class_ids[$property->object_class])) {
                        $property->object_class_id =
                            $this->class_ids[$property->object_class];
                        unset($property->object_class);
                    }
                    elseif (isset($this->classes[$property->object_class])) {
                        $property->object_class_id =
                            $this->classes[$property->object_class]->id;
                        unset($property->object_class);
                    }
                    else {
                        throw new ReflectionError(__(
                            "ID for class ':class' not provided",
                            ['class' => $property->object_class]));
                    }
                }
                break;
        }
    }
}