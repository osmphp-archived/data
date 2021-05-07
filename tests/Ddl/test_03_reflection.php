<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Core\Array_;
use Osm\Core\Exceptions\UndefinedArrayKey;
use Osm\Data\Data\Models\ArrayClass;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Models\Property;
use Osm\Data\Data\Module;
use Osm\Data\Samples\Products\Models\Product;
use Osm\Data\Samples\Products\Models\Products;
use Osm\Data\Samples\Products\Models\TaxRate;
use Osm\Framework\TestCase;
use Osm\Data\Data\Models\Properties;
use function Osm\meta_schema;
use function Osm\reflect;

class test_03_reflection extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_raw_reflection() {
        // GIVEN all the model classes defined in the codebase

        // WHEN you reflect over classes and properties marked with
        // #[Schema('M01_products')]
        $classes = $this->app->data->reflect('M01_products',
            module: \Osm\Data\Samples\Products\Module::class);

        // THEN you can read the collected raw class and property information
        $this->assertTrue(isset($classes['product']));
        $class = $classes['product'];
        $this->assertInstanceOf(\stdClass::class, $class);
        $this->assertEquals('product', $class->name);
        $this->assertEquals('/products', $class->endpoint);
        $this->assertEquals('type', $class->subtype_by);

        $this->assertTrue(isset($class->properties['sku']));
        $property = $class->properties['sku'];
        $this->assertInstanceOf(\stdClass::class, $property);
        $this->assertEquals('sku', $property->name);
        $this->assertEquals('string', $property->type);

        $this->assertTrue(isset($class->properties['related_products']));
        $property = $class->properties['related_products'];
        $this->assertInstanceOf(\stdClass::class, $property);
        $this->assertEquals('related_products', $property->name);
        $this->assertEquals('array', $property->type);
        $this->assertEquals('object', $property->item->type);
        $this->assertTrue($property->item->object_class_id == $class->id);

        $this->assertTrue(isset($class->properties['inventory']));
        $property = $class->properties['inventory'];
        $this->assertInstanceOf(\stdClass::class, $property);
        $this->assertEquals('inventory', $property->name);
        $this->assertEquals('object', $property->type);
        $this->assertTrue($property->object_class_id ==
            $classes['product_inventory']->id);

        $this->assertTrue(isset($class->properties['id']));
        $this->assertTrue(isset($class->properties['json']));
    }

    public function test_hydrated_meta_schema() {
        // GIVEN all the model classes defined in the codebase

        // WHEN you reflect, hydrate and resolve all the model classes
        // marked with the `Meta` attribute - `class`, `property` and other
        // models that define the structure of any model
        $meta = $this->app->data->meta;

        // THEN the hydrated class/property structure contains enough
        // information to hydrate and resolve any reflected model
        $this->assertTrue(isset($meta['property']));
        $this->assertTrue(isset($meta['class']));
        $this->assertTrue(isset($meta['array_class']));

        $class = $meta['property'];
        $this->assertInstanceOf(Class_::class, $class);

        $this->assertTrue(isset($class->properties['id']));
        $property = $class->properties['id'];
        $this->assertInstanceOf(Property::class, $property);
        $this->assertTrue($property->__parent === $class);

        $this->assertTrue(isset($class->properties['item']));
        $property = $class->properties['item'];
        $this->assertInstanceOf(Properties\Object_::class, $property);
        $this->assertTrue($property->__parent === $class);
        $this->assertTrue($property->object_class === $meta['property']);

        $this->assertTrue(isset($class->properties['object_class']));
        $property = $class->properties['object_class'];
        $this->assertInstanceOf(Properties\Object_::class, $property);
        $this->assertTrue($property->__parent === $class);
        $this->assertTrue($property->object_class === $meta['class']);

        $this->assertTrue(isset($class->properties['array_class']));
        $property = $class->properties['array_class'];
        $this->assertInstanceOf(Properties\Object_::class, $property);
        $this->assertTrue($property->__parent === $class);
        $this->assertTrue($property->object_class === $meta['array_class']);

        $class = $meta['class'];
        $this->assertInstanceOf(Class_::class, $class);

        $this->assertTrue(isset($class->properties['properties']));
        $property = $class->properties['properties'];
        $this->assertInstanceOf(Properties\Array_::class, $property);
        $this->assertTrue($property->__parent === $class);

        $item = $property->item;
        $this->assertInstanceOf(Properties\Object_::class, $item);
        $this->assertTrue($item->__parent === $property);
        $this->assertTrue($item->object_class === $meta['property']);
    }

    public function test_hydrated_reflection() {
        // GIVEN all the model classes defined in the codebase
        $data = $this->app->data;
        $meta = $data->meta;

        // WHEN you reflect over classes and properties marked with
        // #[Schema('M01_products')]
        $dehydrated = $data->reflect('M01_products',
            module: \Osm\Data\Samples\Products\Module::class);
        $classes = $meta->hydrateAndResolve($dehydrated);

        // THEN you can read the collected raw class and property information
        $this->assertTrue(isset($classes['product']));
        $class = $classes['product'];
        $this->assertInstanceOf(\stdClass::class, $class);
        $this->assertEquals('product', $class->name);
        $this->assertEquals('/products', $class->endpoint);
        $this->assertEquals('type', $class->subtype_by);

        $this->assertTrue(isset($class->properties['sku']));
        $property = $class->properties['sku'];
        $this->assertInstanceOf(\stdClass::class, $property);
        $this->assertEquals('sku', $property->name);
        $this->assertEquals('string', $property->type);

        $this->assertTrue(isset($class->properties['related_products']));
        $property = $class->properties['related_products'];
        $this->assertInstanceOf(\stdClass::class, $property);
        $this->assertEquals('related_products', $property->name);
        $this->assertEquals('array', $property->type);
        $this->assertEquals('object', $property->item->type);
        $this->assertTrue($property->item->object_class_id == $class->id);

        $this->assertTrue(isset($class->properties['inventory']));
        $property = $class->properties['inventory'];
        $this->assertInstanceOf(\stdClass::class, $property);
        $this->assertEquals('inventory', $property->name);
        $this->assertEquals('object', $property->type);
        $this->assertTrue($property->object_class_id ==
            $classes['product_inventory']->id);

        $this->assertTrue(isset($class->properties['id']));
        $this->assertTrue(isset($class->properties['json']));
    }
}