<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Routes\Api;

use Osm\Framework\Areas\Api;
use Osm\Framework\Areas\Attributes\Area;
use Symfony\Component\HttpFoundation\Response;
use function Osm\json_response;

#[Area(Api::class)]
class Insert extends Route
{
    public function run(): Response {
        return json_response($this->query->insert(
            json_decode($this->http->content)));
    }
}