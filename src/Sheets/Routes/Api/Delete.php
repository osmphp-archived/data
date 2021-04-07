<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Routes\Api;

use Osm\Data\Sheets\Data;
use Osm\Data\Sheets\Query;
use Osm\Data\Sheets\RequestParser;
use Osm\Framework\Areas\Api;
use Osm\Framework\Areas\Attributes\Area;
use Symfony\Component\HttpFoundation\Response;
use function Osm\json_response;

/**
 * @property Data $data
 * @property Query $query
 */
#[Area(Api::class)]
class Delete extends Route
{
    public function run(): Response {
        return json_response($this->query->delete());
    }

    /** @noinspection PhpUnused */
    protected function get_query(): Query {
        RequestParser::new(['query' => $query = parent::get_query()])
            ->filters();

        return $query;
    }
}