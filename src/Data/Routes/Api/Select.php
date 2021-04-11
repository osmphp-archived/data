<?php

declare(strict_types=1);

namespace Osm\Data\Data\Routes\Api;

use Osm\Data\Data\Query;
use Osm\Data\Data\RequestParser;
use Osm\Framework\Areas\Api;
use Osm\Framework\Areas\Attributes\Area;
use Symfony\Component\HttpFoundation\Response;
use function Osm\json_response;

/**
 * @property Query $raw_query
 */
#[Area(Api::class)]
class Select extends Route
{
    public function match(): ?Route {
        if ($endpoint = $this->schema->endpoints[$this->http->path] ?? null) {
            $this->endpoint = $endpoint;

            return match ($this->http->request->getMethod()) {
                'GET' => $this,
                'POST' => Update::new(['endpoint' => $endpoint]),
                'DELETE' => Delete::new(['endpoint' => $endpoint]),
                default => null,
            };
        }

        if ($this->http->request->getMethod() != 'POST') {
            return null;
        }

        foreach ($this->schema->endpoints as $path => $endpoint) {
            if ($this->http->path == "{$path}/insert") {
                return Insert::new(['endpoint' => $endpoint]);
            }
        }

        return null;
    }

    public function run(): Response {
        return json_response($this->query->get());
    }

    /** @noinspection PhpUnused */
    protected function get_query(): Query {
        RequestParser::new(['query' => $query = $this->raw_query])
            ->count()
            ->select()
            ->filters();

        return $query;
    }

    /** @noinspection PhpUnused */
    protected function get_raw_query(): Query {
        return parent::get_query();
    }
}