<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Models\Class_;
use Osm\Framework\TestCase;
use function Osm\id;
use function Osm\standard_column;

class test_04_creating_and_dropping_tables extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;
    public bool $use_db = true;

    public function test_creating_table_from_dehydrated_data() {
        // GIVEN the meta-schema
        $data = $this->app->data;
        $meta = $data->meta;
        $property = $data->arrayOf($meta['class'], "Undefined model ':key'");

        // AND a dehydrated endpoint definition
        $data = [
            (object)[
                'id' => $inventory = id(),
                'properties' => [
                    'qty' => (object)[
                        'name' => 'qty',
                        'type' => 'number',
                        'column' => (object)['type' => 'integer'],
                    ],
                ],
            ],
            (object)[
                'id' => $products = id(),
                'endpoint' => '/products',
                'properties' => [
                    'id' => standard_column('id'),
                    'json' => standard_column('json'),
                    'sku' => (object)[
                        'name' => 'sku',
                        'type' => 'string',
                        'column' => (object)['type' => 'string'],
                    ],
                    'description' => (object)[
                        'name' => 'description',
                        'type' => 'string',
                    ],
                    'inventory' => (object)[
                        'name' => 'inventory',
                        'type' => 'object',
                        'object_class_id' => $inventory,
                    ],
                ],
            ],
            (object)[
                'id' => $orders = id(),
                'endpoint' => '/orders',
                'properties' => [
                    'id' => standard_column('id'),
                    'json' => standard_column('json'),
                ],
            ],
            (object)[
                'id' => $orderLines = id(),
                'endpoint' => '/orders/lines',
                'properties' => [
                    'id' => standard_column('id'),
                    'json' => standard_column('json'),
                    'order_id' => (object)[
                        'name' => 'order_id',
                        'type' => 'id',
                        'column' => (object)[
                            'type' => 'integer',
                            'unsigned' => true,
                        ],
                        'foreign' => (object)[
                            'class_id' => $orders,
                            'on_delete' => 'cascade',
                        ],
                    ],
                    'parent_line_id' => (object)[
                        'name' => 'parent_line_id',
                        'type' => 'id',
                        'column' => (object)[
                            'type' => 'integer',
                            'unsigned' => true,
                            'nullable' => true,
                        ],
                        'foreign' => (object)[
                            'class_id' => $orderLines,
                            'on_delete' => 'cascade',
                        ],
                    ],
                ],
            ],
        ];

        // WHEN you create a table for each endpoint
        foreach ($property->hydrateAndResolve($data) as $class) {
            /* @var Class_ $class */
            $class->createTable($this->db);
        }

        // THEN the table is there
        $schema = $this->db->connection->getSchemaBuilder();

        $this->assertFalse($schema->hasTable('inventory'));

        $this->assertTrue($schema->hasTable('products'));
        $this->assertTrue($schema->hasColumn('products', 'id'));
        $this->assertTrue($schema->hasColumn('products', 'json'));
        $this->assertTrue($schema->hasColumn('products', 'sku'));
        $this->assertFalse($schema->hasColumn('products', 'description'));
        $this->assertTrue($schema->hasColumn('products', 'inventory__qty'));

        $this->assertTrue($schema->hasTable('orders'));
        $this->assertTrue($schema->hasColumn('orders', 'id'));
        $this->assertTrue($schema->hasColumn('orders', 'json'));

        $this->assertTrue($schema->hasTable('orders__lines'));
        $this->assertTrue($schema->hasColumn('orders__lines', 'id'));
        $this->assertTrue($schema->hasColumn('orders__lines', 'json'));
        $this->assertTrue($schema->hasColumn('orders__lines', 'order_id'));
    }

    public function test_creating_table_from_reflected_data() {
        // GIVEN the meta-schema
        $data = $this->app->data;
        $meta = $data->meta;
        $property = $data->arrayOf($meta['class'], "Undefined model ':key'");

        // AND a dehydrated endpoint definition
        $data = $data->reflect('M01_products',
            module: \Osm\Data\Samples\Products\Module::class);

        // WHEN you create a table for each endpoint
        foreach ($property->hydrateAndResolve($data) as $class) {
            /* @var Class_ $class */
            $class->createTable($this->db);
        }

        // THEN the table is there
        $schema = $this->db->connection->getSchemaBuilder();

        $this->assertFalse($schema->hasTable('inventory'));

        $this->assertTrue($schema->hasTable('products'));
        $this->assertTrue($schema->hasColumn('products', 'id'));
        $this->assertTrue($schema->hasColumn('products', 'json'));
        $this->assertTrue($schema->hasColumn('products', 'sku'));
        $this->assertFalse($schema->hasColumn('products', 'description'));
        $this->assertTrue($schema->hasColumn('products', 'inventory__qty'));

        $this->assertTrue($schema->hasTable('orders'));
        $this->assertTrue($schema->hasColumn('orders', 'id'));
        $this->assertTrue($schema->hasColumn('orders', 'json'));

        $this->assertTrue($schema->hasTable('orders__lines'));
        $this->assertTrue($schema->hasColumn('orders__lines', 'id'));
        $this->assertTrue($schema->hasColumn('orders__lines', 'json'));
        $this->assertTrue($schema->hasColumn('orders__lines', 'order_id'));
    }

    public function test_altering_table_from_dehydrated_data() {
        // GIVEN the meta-schema
        $data = $this->app->data;
        $meta = $data->meta;
        $property = $data->arrayOf($meta['class'], "Undefined model ':key'");

        // AND a dehydrated endpoint definition
        /* @var Class_[] $classes */
        $classes = $property->hydrateAndResolve([
            'order' => (object)[
                'id' => $orders = id(),
                'endpoint' => '/orders',
                'properties' => [
                    'id' => standard_column('id'),
                    'json' => standard_column('json'),
                ],
            ],
            'order_line' => (object)[
                'id' => id(),
                'endpoint' => '/orders/lines',
                'properties' => [
                    'id' => standard_column('id'),
                    'json' => standard_column('json'),
                    'order_id' => (object)[
                        'id' => $order_id = id(),
                        'name' => 'order_id',
                        'type' => 'id',
                        'column' => (object)[
                            'type' => 'integer',
                            'unsigned' => true,
                        ],
                        'foreign' => (object)[
                            'class_id' => $orders,
                            'on_delete' => 'cascade',
                        ],
                    ],
                ],
            ],
        ]);

        // AND you create a table for each endpoint
        foreach ($classes as $class) {
            /* @var Class_ $class */
            $class->createTable($this->db);
        }

        // WHEN you add/or remove properties
        $classes['order']->alterTable($this->db, (object)[
            'properties' => [
                'no' => (object)[
                    'id' => id(),
                    'name' => 'no',
                    'type' => 'string',
                    'column' => (object)['type' => 'string'],
                ],
            ],
        ]);
        $classes['order_line']->alterTable($this->db, (object)[
            'properties' => [
                'order_id' => (object)[
                    'id' => "deleted-{$order_id}",
                ],
            ],
        ]);

        // THEN the columns are added/removed accordingly
        $schema = $this->db->connection->getSchemaBuilder();

        $this->assertTrue($schema->hasColumn('orders', 'no'));
        $this->assertFalse($schema->hasColumn('orders__lines', 'order_id'));
    }


    public function test_deleting_table_from_dehydrated_data() {
        // GIVEN the meta-schema
        $data = $this->app->data;
        $meta = $data->meta;
        $property = $data->arrayOf($meta['class'], "Undefined model ':key'");

        // AND a dehydrated endpoint definition
        /* @var Class_[] $classes */
        $classes = $property->hydrateAndResolve([
            'order' => (object)[
                'id' => $orders = id(),
                'endpoint' => '/orders',
                'properties' => [
                    'id' => standard_column('id'),
                    'json' => standard_column('json'),
                ],
            ],
        ]);

        // AND you create a table for each endpoint
        foreach ($classes as $class) {
            /* @var Class_ $class */
            $class->createTable($this->db);
        }

        // WHEN you add/or remove properties
        $classes['order']->dropTable($this->db);

        // THEN the columns are added/removed accordingly
        $schema = $this->db->connection->getSchemaBuilder();

        $this->assertFalse($schema->hasTable('orders'));
    }
}