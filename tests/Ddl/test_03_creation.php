<?php

declare(strict_types=1);

namespace Osm\Data\Tests\Ddl;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Samples\App;
use Osm\Framework\Http\Client;
use Osm\Runtime\Apps;
use PHPUnit\Framework\TestCase;

class test_03_creation extends TestCase
{
    protected ?Client $client;

    protected function api(callable $callback): void {
        Apps::run(Apps::create(App::class),
            function(App $app) use ($callback) {
                $app->db->dryRun(function() use ($callback){
                    $this->client = new Client();
                    try {
                        $callback();
                    }
                    finally {
                        $this->client = null;
                    }
                });
            });
    }

    protected function request(string $request): mixed {
        $headers = [];
        $method = '';
        $url = '';
        $content = '';

        foreach (explode(PHP_EOL, $request) as $line) {
            $line = trim($line);

            if ($method) {
                $content .= $line . PHP_EOL;
                continue;
            }

            if (preg_match('/(?<method>GET|POST|DELETE) (?<url>.*)/',
                $line, $match))
            {
                $method = $match['method'];
                $url = "/api{$match['url']}";
                continue;
            }

            // parse and send headers
            throw new NotImplemented();
        }

        $this->client->request($method, $url, content: $content ?: null);

        $response = $this->client->getInternalResponse();

        $this->assertEquals(200, $response->getStatusCode(),
            $response->getContent());

        return $response->getContent()
            ? json_decode($response->getContent())
            : null;
    }

    public function test_json_scalars() {
        // GIVEN an HTTP client processing requests in this very process
        $this->api(function() {
            // WHEN you insert an array with assigned endpoint
            $response = $this->request(<<<EOT
POST /properties/insert
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

            // THEN the underlying table is actually created
            global $osm_app; /* @var App $osm_app */
            $schema = $osm_app->db->connection->getSchemaBuilder();
            $this->assertTrue($schema->hasTable('products'));

            // AND there is no dedicated column - as requested
            $this->assertFalse($schema->hasColumn('products', 'sku'));
        });
    }

    public function test_explicit_scalars() {
        // GIVEN an HTTP client processing requests in this very process
        $this->api(function() {
            // WHEN you insert an array with assigned endpoint
            $response = $this->request(<<<EOT
POST /properties/insert
{
    "name": "products",
    "type": "array",
    "endpoint": "/products",
    "items": {
        "type": "object",
        "properties": {
            "sku": {
                "type": "string",
                "column": { "unique": true }
            }
        }
    }
}
EOT);

            // THEN the underlying table is actually created
            global $osm_app; /* @var App $osm_app */
            $schema = $osm_app->db->connection->getSchemaBuilder();
            $this->assertTrue($schema->hasTable('products'));

            // AND there is no dedicated column - as requested
            $this->assertTrue($schema->hasColumn('products', 'sku'));
        });
    }
}