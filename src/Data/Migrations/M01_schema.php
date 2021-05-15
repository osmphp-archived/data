<?php

/** @noinspection PhpUnused */
declare(strict_types=1);

namespace Osm\Data\Data\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\App;
use Osm\Data\Data\Data;
use Osm\Data\Data\Module;
use Osm\Framework\Db\Db;
use Osm\Framework\Migrations\Migration;
use function Osm\migrate_schema;
use function Osm\object_empty;

/**
 * @property Db $db
 * @property Data $data
 * @property \stdClass $root_property
 */
class M01_schema extends Migration
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    protected function get_data(): Data {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->data;
    }

    public function create(): void {
        $this->data->migrateUp($this->db, 'M01_schema', Module::class);
    }

    public function drop(): void {
        $this->data->migrateDown($this->db, 'M01_schema', Module::class);
    }
}