<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Framework\TestCase;
use Osm\Data\Data\Properties;

class test_03_hydration extends TestCase
{
    public string $app_class_name = \Osm\Data\Samples\App::class;
    
    public function test_hydration() {
        // GIVEN the currently installed data schema

        $metaProperties = $this->app->data->schema->endpoints['/properties'];

        // WHEN you hydrate the meta-schema and check child properties

        /* @var Properties\Array_ $topProperties */
        $topProperties = $metaProperties->hydrate(json_decode(file_get_contents(
            dirname(dirname(__DIR__)) .'/src/Data/properties.json')));

        /* @var Properties\Number $id */
        $id = $topProperties['properties']->items->properties['id'];

        // THEN their cross-references are initialized

        $this->assertTrue($id->column->property === $id);
    }
}