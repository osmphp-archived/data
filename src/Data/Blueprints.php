<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\App;
use Osm\Data\Data\Hints\Blueprint;
use Osm\Data\Data\Properties\Object_;
use Illuminate\Database\Schema\Blueprint as TableBlueprint;
use Osm\Framework\Db\Db;

/**
 * @property Db $db
 */
class Blueprints extends Object_
{
    /**
     * @var \stdClass[]|Blueprint[]
     */
    protected array $blueprints = [];
    /**
     * @var \stdClass[]|Blueprint[]
     */
    protected array $blueprint_stack = [];

    public function run(): void {
        $undo = [];

        try {
            foreach ($this->blueprints as $blueprint) {
                $this->{"run_{$blueprint->type}"}($blueprint, $undo);
            }
        }
        catch (\Throwable $e) {
            foreach ($undo as $callback) {
                $callback();
            }

            throw $e;
        }
    }

    protected function run_create_table(\stdClass|Blueprint $blueprint,
        array &$undo): void
    {
        if (empty($blueprint->callbacks)) {
            return;
        }

        $this->db->create($blueprint->name,
            function(TableBlueprint $blueprint) use ($blueprint) {
                foreach ($blueprint->callbacks as $callback) {
                    $callback($blueprint);
                }
            }
        );

        $undo[] = function() use ($blueprint) {
            $this->db->drop($blueprint->name);
        };
    }

    public function createTable(string $table, callable $callback) {
        $this->blueprints[] = $blueprint = (object)[
            'type' => Blueprint::CREATE_TABLE,
            'name' => $table,
            'callbacks' => [],
        ];

        array_push($this->blueprint_stack, $blueprint);

        try {
            $callback($this);
        }
        finally {
            array_pop($this->blueprint_stack);
        }
    }

    public function blueprint(): ?Blueprint {
        if (!($count = count($this->blueprint_stack))) {
            return null;
        }

        return $this->blueprint_stack[$count - 1];
    }

    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }
}