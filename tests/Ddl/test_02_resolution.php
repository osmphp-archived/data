<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Core\Array_;
use Osm\Core\Exceptions\UndefinedArrayKey;
use Osm\Data\Data\Exceptions\CircularReference;
use Osm\Data\Data\Models\ArrayClass;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Samples\Products\Models\Product;
use Osm\Data\Samples\Products\Models\Products;
use Osm\Data\Samples\Products\Models\TaxRate;
use Osm\Framework\TestCase;
use Osm\Data\Data\Models\Properties;
use function Osm\id;

class test_02_resolution extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_circular_references() {
        // GIVEN an array of objects property
        $property = Properties\Array_::new([
            'item' => Properties\Object_::new([
                'object_class' => $categoryClass = Class_::new([
                    'properties' => new Array_([
                        'parent' => $parentProperty = Properties\Object_::new(),
                    ], "Unknown property ':key'"),
                ]),
            ]),
        ]);
        $parentProperty->object_class = $categoryClass;

        // WHEN you hydrate data containing circular references
        $dehydrated = [
            $category1 = (object)[],
            $category2 = (object)[],
        ];
        $category1->parent = $category2;
        $category2->parent = $category1;

        // THEN an exception is thrown
        $this->expectException(CircularReference::class);
        $property->hydrate($dehydrated);
    }

    public function test_parent_references() {
        // GIVEN a class with a scalar, object and an array property
        $property = Properties\Array_::new([
            'item' => Properties\Object_::new([
                'object_class' => $productClass = Class_::new([
                    'name' => 'product',
                    'properties' => new Array_([
                        'sku' => Properties\String_::new(),
                        'inventory' => Properties\Object_::new([
                            'object_class' => Class_::new([
                                'name' => 'product_inventory',
                                'properties' => new Array_([
                                    'qty' => Properties\Number::new(),
                                    'in_stock' => Properties\Boolean::new(),
                                ], "Unknown property ':key'"),
                            ]),
                        ]),
                    ], "Unknown property ':key'"),
                ]),
            ]),
            'array_class' => ArrayClass::new([
                'key' => 'sku',
                'not_found_message' => "There is no product having ':key' SKU",
            ]),
        ]);
        $productClass->properties['related_products'] = Properties\Array_::new([
            'item' => Properties\Object_::new([
                'object_class' => $productClass,
            ]),
        ]);

        // WHEN you parent an object
        $products = $property->hydrate([
            'sku1' => (object)[
                'inventory' => (object)[
                    'qty' => 10,
                    'in_stock' => true,
                ],
                'related_products' => [
                    (object)['sku' => 'sku2'],
                ],
            ],
        ]);
        $property->resolve($products);

        // THEN all the typed child objects have references to the
        // typed parent objects
        $this->assertTrue($products['sku1']->inventory->__parent
            === $products['sku1']);
        $this->assertTrue($products['sku1']->related_products[0]->__parent
            === $products['sku1']);
    }

    public function test_identity_map() {
        // GIVEN a class with an endpoint, it may not have a `name`
        $property = Properties\Object_::new([
            'object_class' => Class_::new([
                'endpoint' => '/products',
                'properties' => new Array_([
                    'id' => Properties\Id::new(),
                    'sku' => Properties\String_::new(),
                ], "Unknown property ':key'"),
            ]),
        ]);

        // WHEN you hydrate an object with an `id`
        $dehydrated = (object)['id' => 1, 'sku' => 'sku1'];
        $hydrated = $property->hydrate($dehydrated, $identities);

        // THEN the hydrated object is added to `$identities`
        $this->assertNotNull($identities,
            '$identities map should be returned either way, even if empty');
        $this->assertTrue(isset($identities['/products'][1]),
            'A hydrated product object is not added to $identities map');
        $this->assertTrue($identities['/products'][1] == $hydrated,
            'Wrong hydrated object is added to $identities map');
    }

    public function test_id_references() {
        // GIVEN an array of objects property
        $property = Properties\Array_::new([
            'item' => Properties\Object_::new([
                'object_class' => $categoryClass = Class_::new([
                    'endpoint' => '/categories',
                    'properties' => new Array_([
                        'id' => Properties\Id::new(),
                        'parent_id' => Properties\Id::new(),
                        'parent' => $parentProperty = Properties\Ref::new([
                            'name' => 'parent',
                        ]),
                    ], "Unknown property ':key'"),
                ]),
            ]),
        ]);
        $parentProperty->object_class = $categoryClass;

        // WHEN you hydrate and resolve an object with an `id` reference
        $value = [
            1 => (object)['id' => 1],
            2 => (object)['id' => 2, 'parent_id' => 1],
        ];
        $hydrated = $property->hydrateAndResolve($value);
        $dehydrated = $property->dehydrate($hydrated);

        // THEN the parent reference is resolved in the hydrated object
        $this->assertTrue(isset($hydrated[2]->parent),
            'Reference property not hydrated');
        $this->assertTrue($hydrated[2]->parent === $hydrated[1],
            'Hydrated reference not resolved by ID');

        // AND removed from the dehydrated object
        $this->assertFalse(isset($dehydrated[2]->parent),
            'Dehydrated reference not removed');
    }

    public function test_temporary_id_references() {
        // GIVEN an array of objects property
        $property = Properties\Array_::new([
            'item' => Properties\Object_::new([
                'object_class' => $categoryClass = Class_::new([
                    'endpoint' => '/categories',
                    'properties' => new Array_([
                        'id' => Properties\Id::new(),
                        'parent_id' => Properties\Id::new(),
                        'parent' => $parentProperty = Properties\Ref::new([
                            'name' => 'parent',
                        ]),
                    ], "Unknown property ':key'"),
                ]),
            ]),
        ]);
        $parentProperty->object_class = $categoryClass;

        // WHEN you hydrate and resolve an object with an `id` reference
        $value = [
            1 => (object)['id' => $id1 = id()],
            2 => (object)['id' => $id2 = id(), 'parent_id' => $id1],
        ];
        $hydrated = $property->hydrateAndResolve($value);
        $dehydrated = $property->dehydrate($hydrated);

        // THEN the parent reference is resolved in the hydrated object
        $this->assertTrue(isset($hydrated[2]->parent),
            'Reference property not hydrated');
        $this->assertTrue($hydrated[2]->parent === $hydrated[1],
            'Hydrated reference not resolved by ID');

        // AND removed from the dehydrated object
        $this->assertFalse(isset($dehydrated[2]->parent),
            'Dehydrated reference not removed');
    }
}