<?php

declare(strict_types=1);

namespace Osm\Data\Data\Routes\Api;

use Osm\Data\Data\Data;
use Osm\Data\Data\Query;
use Osm\Data\Data\RequestParser;
use Osm\Framework\Areas\Api;
use Osm\Framework\Areas\Attributes\Area;
use Symfony\Component\HttpFoundation\Response;
use function Osm\json_response;

/**
 * @property Data $data
 * @property Query $query
 */
#[Area(Api::class)]
class Update extends Route
{
    public function run(): Response {
        return json_response($this->query->update(
            json_decode($this->http->content)));
    }

    /** @noinspection PhpUnused */
    protected function get_query(): Query {
        RequestParser::new(['query' => $query = parent::get_query()])
            ->filters();

        return $query;
    }
}