<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Data\Samples\App;
use Osm\Framework\Http\Client;
use Osm\Runtime\Apps;
use PHPUnit\Framework\TestCase;

class test_01_creation extends TestCase
{
    public function test_json_column() {
        Apps::run(Apps::create(App::class), function(App $app) {
            $app->db->dryRun(function() use($app) {
                // GIVEN an HTTP client processing requests in this very process
                $client = new Client();

                // WHEN you insert an array with assigned endpoint
                $client->request('POST', '/api/properties/insert', content: <<<EOT
{
    "name": "products",
    "type": "array",
    "endpoint": "/products",
    "items": {
        "type": "object",
        "properties": {
            "sku": {
                "type": "string"
            }
        }
    }
}
EOT);
                $response = $client->getInternalResponse();

                // THEN there are no errors
                $this->assertEquals(200, $response->getStatusCode(),
                    $response->getContent());

                // AND the underlying table is actually created
                $schema = $app->db->connection->getSchemaBuilder();
                $this->assertTrue($schema->hasTable('products'));

                // AND there is no dedicated column - as requested
                $this->assertFalse($schema->hasColumn('products', 'sku'));
            });
        });
    }
}