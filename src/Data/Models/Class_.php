<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\App;
use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Meta;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Exceptions\MissingColumn;
use Osm\Data\Data\Attributes\Column;
use Osm\Framework\Db\Db;
use function Osm\__;
use function Osm\standard_column;

/**
 * @property string $name #[Schema('M01_schema'),
 *      Column('string', nullable: true, unique: true)]
 * @property string $endpoint #[Schema('M01_schema'),
 *      Column('string', nullable: true, unique: true)]
 * @property Property[] $properties #[Schema('M01_schema')]
 * @property string $subtype_by #[Schema('M01_schema'),
 *      Column('string', nullable: true)]
 * @property ?string $table
 */
#[Name('class'), Schema('M01_schema'), Endpoint('/classes'), Meta]
class Class_ extends Record
{
    public function createTable(Db $db): void {
        if (!$this->table) {
            return;
        }

        if (!isset($this->properties['id'])) {
            throw new MissingColumn(__(
                "':column' column is expected, consider defining it as follows: ':settings'", [
                    'column' => 'id',
                    'settings' => json_encode(standard_column('id')),
                ]));
        }

        if (!isset($this->properties['json'])) {
            throw new MissingColumn(__(
                "':column' column is expected, consider defining it as follows: ':settings'", [
                    'column' => 'json',
                    'settings' => json_encode(standard_column('json')),
                ]));
        }

        $db->create($this->table, function(Blueprint $table) use ($db) {
            foreach ($this->properties as $property) {
                $property->createColumn($db, $table);
            }
        });

        $db->rolledBack(function(Db $db) {
            $db->drop($this->table);
        });
    }

    public function alterTable(Db $db, \stdClass $changes): void {
        global $osm_app; /* @var App $osm_app */

        if (!$this->table) {
            return;
        }

        /* @var Property $deletedProperties */
        $deletedProperties = [];

        foreach ($changes->properties ?? [] as $property) {
            if (!$property->id) {
                throw new NotImplemented($this);
            }

            if (!is_string($property->id)) {
                throw new NotImplemented($this);
            }

            if (str_starts_with($property->id, 'new-')) {
                $schema = $osm_app->data->objectOf(
                    $osm_app->data->meta['property']);
                $hydratedProperty = $schema->hydrateAndResolve($property);

                $hydratedProperty->createColumn($db, $this->table);
                continue;
            }

            if (str_starts_with($property->id, 'deleted-')) {
                $key = substr($property->id, strlen('deleted-'));
                if (is_numeric($key)) {
                    $key = (int)$key;
                }

                $hydratedProperty = null;

                foreach ($this->properties as $existingProperty) {
                    if ($existingProperty->id === $key) {
                        $hydratedProperty = $existingProperty;
                        break;
                    }
                }

                if (!$hydratedProperty) {
                    throw new NotImplemented($this);
                }

                $deletedProperties[] = $hydratedProperty;
            }
        }

        foreach ($deletedProperties as $hydratedProperty) {
            $hydratedProperty->dropColumn($db, $this->table);
        }
    }

    public function dropTable(Db $db): void {
        if (!$this->table) {
            return;
        }

        $db->drop($this->table);
    }

    protected function get_table(): ?string {
        return $this->endpoint
            ? str_replace('/', '__',
                ltrim($this->endpoint, '/'))
            : null;
    }
}