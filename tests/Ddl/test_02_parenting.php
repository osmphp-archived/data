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

class test_02_parenting extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_parenting() {
        // GIVEN a class with a scalar, object and an array property
        $inventoryClass = Class_::new([
            'name' => 'product_inventory',
            'properties' => new Array_([
                'qty' => Properties\Number::new(),
                'in_stock' => Properties\Boolean::new(),
            ], "Unknown property ':key'"),
        ]);
        $productClass = Class_::new([
            'name' => 'product',
            'properties' => new Array_([
                'sku' => Properties\String_::new(),
                'inventory' => Properties\Object_::new([
                    'object_class' => $inventoryClass,
                ]),
            ], "Unknown property ':key'"),
        ]);
        $productClass->properties['related_products'] = Properties\Array_::new([
            'item' => Properties\Object_::new([
                'object_class' => $productClass,
            ]),
        ]);
        $property = Properties\Array_::new([
            'item' => Properties\Object_::new([
                'object_class' => $productClass,
            ]),
            'array_class' => ArrayClass::new([
                'key' => 'sku',
                'not_found_message' => "There is no product having ':key' SKU",
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
        $property->parent($products);

        // THEN all the typed child objects have references to the
        // typed parent objects
        $this->assertTrue($products['sku1']->inventory->__parent
            === $products['sku1']);
        $this->assertTrue($products['sku1']->related_products[0]->__parent
            === $products['sku1']);
    }
}