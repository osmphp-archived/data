<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Data\Samples\App;
use Osm\Runtime\Apps;
use PHPUnit\Framework\TestCase;

class test_03_search extends TestCase
{
    public function test_something() {
        Apps::run(Apps::create(App::class), function(App $app) {
            // GIVEN a sample table, created in the database
            $products = $app->indexes->t_products;
            $products->up();

            try {
                // WHEN you insert a record into it
                $a1 = $products->insert(['sku' => 'a1']);

                // THEN it's there
                $this->assertEquals('a1',
                    $products->filter("id={$a1}")->value('sku'));
            }
            finally {
                $products->down();
            }
        });
    }
}