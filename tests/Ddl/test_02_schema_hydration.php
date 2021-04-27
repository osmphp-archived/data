<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Core\Object_;
use Osm\Framework\TestCase;
use Osm\Data\Data\Properties;

class test_02_schema_hydration extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;

    public function test_that_scalar_values_are_hydrated_without_changes() {
        $schema = $this->app->data->schema;

        $this->assertEquals(5, $schema->hydrate((object)[
            'type' => 'number',
        ], 5));
        $this->assertEquals('value', $schema->hydrate((object)[
            'type' => 'string',
        ], 'value'));
        $this->assertEquals(true, $schema->hydrate((object)[
            'type' => 'boolean',
        ], true));
    }

    public function test_that_objects_are_hydrated_with_all_defined_properties() {
        $schema = $this->app->data->schema;

        $hydrated = $schema->hydrate((object)[
            'type' => 'object',
            'class' => Object_::class,
            'properties' => [
                'id' => (object)['type' => 'number'],
            ],
        ], (object)['id' => 5, 'parent_id' => 1]);

        $this->assertInstanceOf(Object_::class, $hydrated);
        $this->assertEquals(5, $hydrated->id);

        // undefined properties are always ignored
        $this->assertNull($hydrated->parent_id);
    }

    public function test_meta_hydration() {
        // GIVEN the currently installed data meta-schema
        $properties = $this->app->data->schema->endpoints['/properties'];

        // WHEN you check child properties
        /* @var Properties\Number $id */
        $id = $properties->items->properties['id'];

        // THEN the hydrated property objects contain the same properties
        // as specified in the original meta schema
        $this->assertEquals('properties', $properties->name);
        $this->assertEquals('/properties', $properties->endpoint);

        // AND property objects are of the correct class, as specified in
        // `class` properties in the schema
        $this->assertInstanceOf(Properties\Array_::class, $properties);
        $this->assertInstanceOf(Properties\Number::class, $id);

        // AND their cross-references are initialized
        $this->assertTrue($id->column->property === $id);
        $this->assertTrue($id->parent === $properties);
    }
}