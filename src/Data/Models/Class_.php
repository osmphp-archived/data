<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Meta;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Exceptions\MissingColumn;
use Osm\Data\Data\Attributes\Column;
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
    public function createTable(Blueprints $blueprints): void {
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

        $blueprints->createTable($this->table, function(Blueprints $blueprints) {
            foreach ($this->properties as $property) {
                $property->createColumn($blueprints);
            }
        });
    }

    protected function get_table(): ?string {
        return $this->endpoint
            ? str_replace('/', '__',
                ltrim($this->endpoint, '/'))
            : null;
    }
}