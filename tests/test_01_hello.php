<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Data\Samples\App;
use Osm\Runtime\Apps;
use PHPUnit\Framework\TestCase;

class test_01_hello extends TestCase
{
    public function test_hi() {
        Apps::run(Apps::create(App::class), function() {
            // GIVEN

            // WHEN you

            // THEN
            $this->assertTrue(true);
        });
    }
}