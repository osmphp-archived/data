<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Core\Array_;
use Osm\Core\Exceptions\UndefinedArrayKey;
use Osm\Data\Data\Models\ArrayClass;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Module;
use Osm\Data\Samples\Products\Models\Product;
use Osm\Data\Samples\Products\Models\Products;
use Osm\Data\Samples\Products\Models\TaxRate;
use Osm\Framework\TestCase;
use Osm\Data\Data\Models\Properties;
use function Osm\reflect;

class test_03_reflection extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_raw_reflection() {
        // GIVEN all the model classes defined in the codebase

        // WHEN you reflect over classes and properties marked with
        // #[Schema('products')]
        $classes = reflect('products',
            module: \Osm\Data\Samples\Products\Module::class)->classes;

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
    }
}