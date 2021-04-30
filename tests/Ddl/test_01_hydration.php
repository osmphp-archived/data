<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Core\Array_;
use Osm\Core\Exceptions\UndefinedArrayKey;
use Osm\Data\Data\Models\ArrayClass;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Samples\Products\Models\Product;
use Osm\Data\Samples\Products\Models\Products;
use Osm\Data\Samples\Products\Models\TaxRate;
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
            ], "Unknown property ':key'"),
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
            ], "Unknown property ':key'"),
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

    public function test_model() {
        // GIVEN a model property
        $class = Class_::new([
            'name' => 'tax_rate',
            'properties' => new Array_([
                'country_code' => Properties\String_::new(),
            ], "Unknown property ':key'"),
        ]);
        $property = Properties\Object_::new([
            'object_class' => $class,
        ]);

        // WHEN you hydrate/dehydrate a value
        $value = (object)['country_code' => 'US'];
        $hydrated = $property->hydrate($value);
        $dehydrated = $property->dehydrate($hydrated);

        // THEN it hydrates to a model object
        // AND dehydrates back to a plain object
        $this->assertInstanceOf(TaxRate::class, $hydrated);
        $this->assertEquals('US', $hydrated->country_code);

        $this->assertInstanceOf(\stdClass::class, $dehydrated);
        $this->assertEquals('US', $dehydrated->country_code);
    }

    public function test_model_subtypes() {
        // GIVEN a subtyped model property
        $class = Class_::new([
            'name' => 'product',
            'subtype_by' => 'type',
            'properties' => new Array_([
                'sku' => Properties\String_::new(),
                'type' => Properties\String_::new(),
            ], "Unknown property ':key'"),
        ]);
        $property = Properties\Object_::new([
            'object_class' => $class,
        ]);

        // WHEN you hydrate/dehydrate a value
        $value = (object)['type' => 'configurable', 'sku' => 'sku1'];
        $hydrated = $property->hydrate($value);
        $dehydrated = $property->dehydrate($hydrated);

        // THEN it hydrates to a subtyped model
        // AND dehydrates back to a plain object
        $this->assertInstanceOf(Products\Configurable::class, $hydrated);
        $this->assertEquals('sku1', $hydrated->sku);

        $this->assertInstanceOf(\stdClass::class, $dehydrated);
        $this->assertEquals('sku1', $dehydrated->sku);
    }

    public function test_typed_array() {
        // GIVEN a managed array of objects property
        $class = Class_::new([
            'properties' => new Array_([
                'sku' => Properties\String_::new(),
            ], "Unknown property ':key'"),
        ]);
        $item = Properties\Object_::new([
            'object_class' => $class,
        ]);
        $property = Properties\Array_::new([
            'item' => $item,
            'array_class' => ArrayClass::new([
                'key' => 'sku',
                'not_found_message' => "There is no product having ':key' SKU",
            ]),
        ]);

        // WHEN you hydrate/dehydrate a value
        $value = ['sku1' => (object)['sku' => 'sku1']];
        $hydrated = $property->hydrate($value);
        $dehydrated = $property->dehydrate($hydrated);

        // THEN it hydrates to a managed array of plain objects,
        // AND dehydrates back to a plain object array
        $this->assertInstanceOf(Array_::class, $hydrated);
        $this->assertCount(1, $hydrated);
        $this->assertInstanceOf(\stdClass::class, $hydrated['sku1']);
        $this->assertEquals('sku1', $hydrated['sku1']->sku);

        $this->assertTrue(is_array($dehydrated));
        $this->assertCount(1, $dehydrated);
        $this->assertInstanceOf(\stdClass::class, $dehydrated['sku1']);
        $this->assertEquals('sku1', $dehydrated['sku1']->sku);

        $this->expectException(UndefinedArrayKey::class);
        $a = $hydrated['sku2'];
    }
}