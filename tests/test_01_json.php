<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Data\Import\Json;
use Osm\Data\Import\Record;
use Osm\Data\Samples\App;
use Osm\Runtime\Apps;
use PHPUnit\Framework\TestCase;

class test_01_json extends TestCase
{
    public function test_json() {
        Apps::run(Apps::create(App::class), function(App $app) {
            // GIVEN the `sample-data/import` directory containing
            // some data for the sheets defined in the sample modules
            // AND there is a schema for processing it
            $schema = Record::new([
                'sheet_name' => 'categories',
                'children' => [
                    '{path}.json' => Json::new(),
                ],
            ]);

            // WHEN you import the data
            $schema->import('sample-data/import/categories/root.json');

            // THEN there are no warnings or errors
            $this->assertCount(0, $task->messages);
        });
    }
}