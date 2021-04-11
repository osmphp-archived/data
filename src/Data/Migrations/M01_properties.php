<?php

/** @noinspection PhpUnused */
declare(strict_types=1);

namespace Osm\Data\Data\Migrations;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\App;
use Osm\Framework\Db\Db;
use Osm\Framework\Migrations\Migration;
use function Osm\object_empty;

/**
 * @property Db $db
 * @property \stdClass $root_property
 */
class M01_properties extends Migration
{
    protected function get_db(): Db {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->db;
    }

    public function create(): void {
        $this->db->create('properties', function(Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')
                ->on('properties')->onDelete('cascade');

            $table->string('endpoint')->nullable()->unique();
            $table->json('data');
        });
    }

    public function drop(): void {
        $this->db->drop('properties');
    }

    public function insert_old(): void {
        $properties = $this->db->table('properties')->insertGetId([
            'endpoint' => '/properties',
            'data' => json_encode((object)[
                'name' => 'properties',
                'type' => 'array',
                'items' => (object)['type' => 'object'],
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $properties,
            'data' => json_encode((object)[
                'name' => 'id',
                'type' => 'number',
                'column' => (object)[],
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $properties,
            'data' => json_encode((object)[
                'name' => 'parent',
                'type' => 'object',
                'ref' => (object)[
                    'endpoint' => '/properties',
                    'on_delete' => 'cascade',
                ],
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $properties,
            'data' => json_encode((object)[
                'name' => 'endpoint',
                'type' => 'string',
                'column' => (object)[],
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $properties,
            'data' => json_encode((object)[
                'name' => 'name',
                'type' => 'string',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $properties,
            'data' => json_encode((object)[
                'name' => 'type',
                'type' => 'string',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $properties,
            'data' => json_encode((object)[
                'name' => 'properties',
                'type' => 'array',
                'items' => (object)[
                    'type' => 'object',
                    'ref' => (object)[
                        'endpoint' => '/properties',
                        'property' => 'parent',
                    ],
                ],
            ]),
        ]);

        $items = $this->db->table('properties')->insertGetId([
            'parent_id' => $properties,
            'data' => json_encode((object)[
                'name' => 'items',
                'type' => 'object',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $items,
            'data' => json_encode((object)[
                'name' => 'type',
                'type' => 'string',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $items,
            'data' => json_encode((object)[
                'name' => 'properties',
                'type' => 'array',
                'key' => 'name',
                'items' => (object)[
                    'type' => 'object',
                    'ref' => (object)[
                        'endpoint' => '/properties',
                        'property' => 'parent',
                    ],
                ],
            ]),
        ]);

        $column = $this->db->table('properties')->insertGetId([
            'parent_id' => $properties,
            'data' => json_encode((object)[
                'name' => 'column',
                'type' => 'object',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $column,
            'data' => json_encode((object)[
                'name' => 'unsigned',
                'type' => 'boolean',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $column,
            'data' => json_encode((object)[
                'name' => 'auto_increment',
                'type' => 'boolean',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $column,
            'data' => json_encode((object)[
                'name' => 'unique',
                'type' => 'boolean',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $column,
            'data' => json_encode((object)[
                'name' => 'index',
                'type' => 'boolean',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $column,
            'data' => json_encode((object)[
                'name' => 'type',
                'type' => 'string',
            ]),
        ]);


        $this->db->table('properties')->insert([
            'parent_id' => $column,
            'data' => json_encode((object)[
                'name' => 'default',
                'type' => 'string',
            ]),
        ]);

        $ref = $this->db->table('properties')->insertGetId([
            'parent_id' => $properties,
            'data' => json_encode((object)[
                'name' => 'ref',
                'type' => 'object',
            ]),
        ]);

        $this->db->table('properties')->insert([
            'parent_id' => $ref,
            'data' => json_encode((object)[
                'name' => 'endpoint',
                'type' => 'string',
            ]),
        ]);


        $this->db->table('properties')->insert([
            'parent_id' => $ref,
            'data' => json_encode((object)[
                'name' => 'on_delete',
                'type' => 'string',
            ]),
        ]);

    }

    protected function get_root_property(): \stdClass {
        return json_decode(file_get_contents(__DIR__ . '/properties.json'));
    }

    public function insert(): void {
        foreach ($this->root_property as $name => $property) {
            $this->insertProperty($name, $property, null);
        }
    }

    protected function insertProperty(string $name, \stdClass $property,
        ?int $parentId): void
    {
        switch ($property->type) {
            case 'object':
                $this->insertObject($name, $property, $parentId);
                break;
            case 'array':
                $this->insertArray($name, $property, $parentId);
                break;
            default:
                $this->insertScalar($name, $property, $parentId);
                break;
        }
    }

    protected function insertObject(string $name, \stdClass $property,
        ?int $parentId): void
    {
        $id = $this->db->table('properties')->insertGetId(
            $this->values($name, $property, $parentId));

        foreach ($property->properties ?? [] as $key => $value) {
            $this->insertProperty($key, $value, $id);
        }
    }

    protected function insertArray(string $name, \stdClass $property,
        ?int $parentId): void
    {
        $id = $this->db->table('properties')->insertGetId(
            $this->values($name, $property, $parentId));

        foreach ($property->items->properties ?? [] as $key => $value) {
            $this->insertProperty($key, $value, $id);
        }
    }

    protected function insertScalar(string $name, \stdClass $property,
        ?int $parentId): void
    {
        $this->db->table('properties')->insert(
            $this->values($name, $property, $parentId));
    }

    protected function values(string $name, \stdClass $property,
        ?int $parentId): array
    {
        $values = ['parent_id' => $parentId];
        $data = (object)['name' => $name];

        foreach ($property as $key => $value) {
            if ($key == 'endpoint') {
                $values[$key] = $value;
                continue;
            }

            if ($key == 'properties') {
                continue;
            }

            if ($key == 'items') {
                $value = clone $value;
                unset($value->properties);
            }

            $data->$key = $value;
        }

        if (!object_empty($data)) {
            $values['data'] = json_encode($data);
        }

        return $values;
    }
}