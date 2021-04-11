<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Data\Data\Filters\Condition;
use Osm\Data\Data\Hints\Property;
use Osm\Data\Data\Hints\Result;
use Osm\Data\Data\Properties\Array_;
use Osm\Framework\Db\Db;
use Osm\Framework\Search\Search;

/**
 * @property Array_ $endpoint
 * @property string $table
 * @property Filters\And_ $filter
 * @property Db $db
 * @property Search $search
 * @property Data $data
 * @property bool $count
 */
class Query extends Object_
{
    /**
     * @var string[]
     */
    public array $select = [];

    public function whereEquals(string $expr, mixed $value): static
    {
        $this->filter->filters[] = Condition::new([
            'query' => $this,
            'expr' => $expr,
            'op' => '=',
            'value' => $value,
        ]);

        return $this;
    }

    public function get(string ...$exprs): array|\stdClass|Result {
        $this->select(...$exprs);
        $query = $this->db->table($this->table, 'this');
        $this->filter->filter($this->endpoint, $query);

        if (!empty($this->select)) {
            foreach ($this->select as $expr) {
                $this->data->select($this->array, $query, $expr);
            }

            $items = $query->get()->toArray();
        }
        else {
            $items = $query->pluck('id')->toArray();
        }

        if (!$this->count) {
            return $items;
        }

        $query = $this->db->table($this->table, 'this');
        $this->filter->filter($this->array, $query);
        $count = $query->value($this->db->raw('COUNT(this.id)'));

        return (object)[
            'count' => $count,
            'items' => $items,
        ];
    }

    public function count(bool $count = true): static {
        $this->count = $count;
        return $this;
    }

    public function rows(string ...$columnNames): array {
        return $this->get(...$columnNames)->rows;
    }

    public function first(string ...$columnNames): ?\stdClass {
        return $this->rows(...$columnNames)[0] ?? null;
    }

    public function value($columnName): mixed {
        if (($row = $this->first($columnName)) === null) {
            return null;
        }

        foreach ($row as $property => $value) {
            return $value;
        }

        return null;
    }

    /** @noinspection PhpUnused */
    protected function get_filter(): Filters\And_ {
        return Filters\And_::new();
    }

    /** @noinspection PhpUnused */
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    /** @noinspection PhpUnused */
    protected function get_search(): Search {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->search;
    }

    /** @noinspection PhpUnused */
    protected function get_data(): Data {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->data;
    }

    public function select(...$exprs): static {
        $this->select = array_unique(array_merge($this->select, $exprs));

        return $this;
    }

    protected function get_table(): string {
        return str_replace('/', '__',
            ltrim($this->endpoint->endpoint, '/'));
    }
}