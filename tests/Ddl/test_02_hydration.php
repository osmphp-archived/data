<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Data\Samples\App;
use Osm\Runtime\Apps;
use PHPUnit\Framework\TestCase;
use Osm\Data\Data\Properties;

class test_02_hydration extends TestCase
{
    public function test_meta_hydration() {
        Apps::run(Apps::create(App::class), function(App $app) {
            // GIVEN the currently installed data meta-schema
            $properties = $app->data->schema->endpoints['/properties'];

            // WHEN you check child properties
            /* @var Properties\Number $id */
            $id = $properties->items->properties['id'];

            // THEN the hydrated property objects contain the same properties
            // as specified in the original meta schema
            $this->assertEquals('properties', $properties->name);
            $this->assertEquals('/properties', $properties->endpoint);

            // AND property objects are of the correct class. What is a
            // "correct" class? It's specified in the schema. For example,
            // `$properties` is
            $this->assertInstanceOf(Properties\Array_::class, $properties);

            // AND their cross-references are initialized
            $this->assertTrue($id->column->property === $id);
        });
    }

    public function test_hydration() {
        Apps::run(Apps::create(App::class), function(App $app) {
            // GIVEN the currently installed data schema

            $metaProperties = $app->data->schema->endpoints['/properties'];

            // WHEN you hydrate the meta-schema and check child properties

            /* @var Properties\Array_ $properties */
            $properties = $metaProperties->hydrate(json_decode(file_get_contents(
                dirname(dirname(__DIR__)) .'/src/Data/properties.json')));

            /* @var Properties\Number $id */
            $id = $properties->items->properties['id'];

            // THEN their cross-references are initialized

            $this->assertTrue($id->column->property === $id);
        });
    }
}