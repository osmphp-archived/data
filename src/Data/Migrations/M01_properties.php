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

    protected function get_root_property(): \stdClass {
        return json_decode(file_get_contents(
            dirname(__DIR__) . '/properties.json'));
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