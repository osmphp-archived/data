<?php

/** @noinspection PhpUnused */
declare(strict_types=1);

namespace Osm\Data\Sheets\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\App;
use Osm\Data\Sheets\Enums\IntSize;
use Osm\Data\Sheets\Enums\OnDelete;
use Osm\Data\Sheets\Enums\Indexes;
use Osm\Data\Sheets\Enums\Types;
use Osm\Framework\Db\Db;
use Osm\Framework\Migrations\Migration;

/**
 * @property Db $db
 */
class M02_columns extends Migration
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function create(): void {
        $this->db->create('sheets__columns', function(Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('sheet_id');
            $table->foreign('sheet_id')->references('id')
                ->on('sheets')->onDelete('cascade');

            $table->string('name')->index();
            $table->unique(['sheet_id', 'name']);

            $table->string('type_name', 80);
            $table->json('type_data')->nullable();

            $table->unsignedSmallInteger('partition_no')->default(0);

//            $table->string('foreign_action', 80)->nullable();
//
//            $table->boolean('filterable')->default(false);
//            $table->boolean('sortable')->default(false);
//
//            $table->string('formula', 80)->nullable();
        });
    }

    public function drop(): void {
        $this->db->drop('sheets__columns');
    }

    public function insert(): void {
        #region Get sheet IDs
        $sheets = $this->db->table('sheets')
            ->where('name', 'sheets')
            ->value('id');
        $columns = $this->db->table('sheets')
            ->where('name', 'sheets__columns')
            ->value('id');
        $options = $this->db->table('sheets')
            ->where('name', 'sheets__columns__options')
            ->value('id');
        #endregion

        #region sheets
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $sheets,
            'name' => 'id',
            'type_name' => Types::PK,
        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $sheets,
            'name' => 'columns',
            'type_name' => Types::SHEET,
            'type_data' =>json_encode((object)[
                'backref' => 'sheet',
            ]),

        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $sheets,
            'name' => 'name',
            'type_name' => Types::STRING_,
            'type_data' =>json_encode((object)[
                'index' => Indexes::INDEX,
            ]),
        ]);
        #endregion

        #region columns
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $columns,
            'name' => 'id',
            'type_name' => Types::PK,
        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $columns,
            'name' => 'sheet',
            'type_name' => Types::REF,
            'type_data' => json_encode((object)[
                'nullable' => true,
                'refs' => 'sheets',
                'on_delete' => OnDelete::CASCADE,
            ]),
        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $columns,
            'name' => 'options',
            'type_name' => Types::SHEET,
            'type_data' => json_encode((object)[
                'backref' => 'column',
            ]),
        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $columns,
            'name' => 'name',
            'type_name' => Types::STRING_,
            'type_data' => json_encode((object)[
                'index' => Indexes::INDEX,
            ]),
        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $columns,
            'name' => 'type',
            'type_name' => Types::OBJECT_,
            'type_data' => json_encode((object)[
                'class_names' => 'sheet_column_types',
            ]),
        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $columns,
            'name' => 'partition_no',
            'type_name' => Types::INT_,
            'type_data' => json_encode((object)[
                'unsigned' => true,
                'size' => IntSize::SMALL,
            ]),
        ]);
        #endregion

        #region options
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $options,
            'name' => 'id',
            'type_name' => Types::PK,
        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $options,
            'name' => 'column',
            'type_name' => Types::REF,
            'type_data' => json_encode((object)[
                'refs' => 'sheets__columns',
                'on_delete' => OnDelete::CASCADE,
            ]),
        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $options,
            'name' => 'title',
            'type_name' => Types::STRING_,
            'type_data' =>json_encode((object)[
                'index' => Indexes::INDEX,
            ]),
        ]);
        $this->db->table('sheets__columns')->insert([
            'sheet_id' => $options,
            'name' => 'sort_order',
            'type_name' => Types::INT_,
            'type_data' =>json_encode((object)[
                'default' => 0,
            ]),
        ]);
        #endregion
    }
}