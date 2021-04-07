<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Routes\Api;

use Osm\Data\Sheets\Query;
use Osm\Data\Sheets\RequestParser;
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
        if ($name = $this->data->sheet_names[$this->http->path] ?? null) {
            $this->sheet_name = $name;

            return match ($this->http->request->getMethod()) {
                'GET' => $this->raw_query->no_select_route ? null : $this,
                'POST' => $this->raw_query->no_update_route
                    ? null
                    : Update::new(['sheet_name' => $name]),
                'DELETE' => $this->raw_query->no_delete_route
                    ? null
                    : Delete::new(['sheet_name' => $name]),
                default => null,
            };
        }

        if ($this->http->request->getMethod() != 'POST') {
            return null;
        }

        foreach ($this->data->sheet_names as $path => $name) {
            if ($this->http->path == "{$path}/insert") {
                $this->sheet_name = $name;

                return $this->raw_query->no_insert_route
                    ? null
                    : Insert::new(['sheet_name' => $name]);
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