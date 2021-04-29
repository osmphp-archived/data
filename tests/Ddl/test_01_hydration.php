<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Core\Array_;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Samples\Products\Models\Product;
use Osm\Framework\TestCase;
use Osm\Data\Data\Models\Properties;

class test_01_hydration extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_scalar() {
        // GIVEN a scalar property definition
        $property = Properties\String_::new();

        // WHEN you hydrate/dehydrate a value
        $value = 'sku1';
        $hydrated = $property->hydrate($value);
        $dehydrated = $property->dehydrate($hydrated);

        // THEN it remains unchanged
        $this->assertEquals('sku1', $hydrated);
        $this->assertEquals('sku1', $dehydrated);

    }

    public function test_plain_object() {
        // GIVEN an object property
        $class = Class_::new([
            'properties' => new Array_([
                'sku' => Properties\String_::new(),
            ], 'Unknown property :key'),
        ]);
        $property = Properties\Object_::new([
            'object_class' => $class,
        ]);

        // WHEN you hydrate/dehydrate a value
        $value = (object)['sku' => 'sku1'];
        $hydrated = $property->hydrate($value);
        $dehydrated = $property->dehydrate($hydrated);

        // THEN it hydrates to a plain object
        // AND dehydrates back to a plain object
        $this->assertInstanceOf(\stdClass::class, $hydrated);
        $this->assertEquals('sku1', $hydrated->sku);

        $this->assertInstanceOf(\stdClass::class, $dehydrated);
        $this->assertEquals('sku1', $dehydrated->sku);
    }

    public function test_plain_object_array() {
        // GIVEN an array of objects property
        $class = Class_::new([
            'properties' => new Array_([
                'sku' => Properties\String_::new(),
            ], 'Unknown property :key'),
        ]);
        $item = Properties\Object_::new([
            'object_class' => $class,
        ]);
        $property = Properties\Array_::new([
            'item' => $item,
        ]);

        // WHEN you hydrate/dehydrate a value
        $value = [(object)['sku' => 'sku1']];
        $hydrated = $property->hydrate($value);
        $dehydrated = $property->dehydrate($hydrated);

        // THEN it hydrates to a plain object array,
        // AND dehydrates back to a plain object array
        $this->assertTrue(is_array($hydrated));
        $this->assertCount(1, $hydrated);
        $this->assertInstanceOf(\stdClass::class, $hydrated[0]);
        $this->assertEquals('sku1', $hydrated[0]->sku);

        $this->assertTrue(is_array($dehydrated));
        $this->assertCount(1, $dehydrated);
        $this->assertInstanceOf(\stdClass::class, $dehydrated[0]);
        $this->assertEquals('sku1', $dehydrated[0]->sku);
    }

    public function test_typed_object() {
        // GIVEN a typed object property
        $class = Class_::new([
            'name' => 'product',
            'properties' => new Array_([
                'sku' => Properties\String_::new(),
            ], 'Unknown property :key'),
        ]);
        $property = Properties\Object_::new([
            'object_class' => $class,
        ]);

        // WHEN you hydrate/dehydrate a value
        $value = (object)['sku' => 'sku1'];
        $hydrated = $property->hydrate($value);
        $dehydrated = $property->dehydrate($hydrated);

        // THEN it hydrates to a plain object
        // AND dehydrates back to a plain object
        $this->assertInstanceOf(Product::class, $hydrated);
        $this->assertEquals('sku1', $hydrated->sku);

        $this->assertInstanceOf(\stdClass::class, $dehydrated);
        $this->assertEquals('sku1', $dehydrated->sku);
    }

}