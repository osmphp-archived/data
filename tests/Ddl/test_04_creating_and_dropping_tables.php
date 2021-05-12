<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Models\Class_;
use Osm\Framework\TestCase;
use function Osm\standard_column;

class test_04_creating_and_dropping_tables extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;
    public bool $use_db = true;

    public function test_creating_table() {
        // GIVEN the meta-schema
        $data = $this->app->data;
        $meta = $data->meta;
        $property = $data->arrayOf($meta['class'], "Undefined model ':key'");

        // AND a dehydrated endpoint definition
        $data = [
            (object)[
                'endpoint' => '/products',
                'properties' => [
                    'id' => standard_column('id'),
                    'json' => standard_column('json'),
                    'sku' => (object)['type' => 'string'],
                ],
            ]
        ];

        // WHEN you create a table for each endpoint
        $blueprints = Blueprints::new(['db' => $this->db]);
        foreach ($property->hydrateAndResolve($data) as $class) {
            /* @var Class_ $class */
            $class->createTable($blueprints);
        }
        $blueprints->run();

        // THEN the table is there
        $schema = $this->db->connection->getSchemaBuilder();
        $this->assertTrue($schema->hasTable('products'));
    }
}