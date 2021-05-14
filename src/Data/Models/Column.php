<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Meta;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Model;

/**
 * @property string $type #[Schema('M01_schema')]
 * @property bool $nullable #[Schema('M01_schema')]
 * @property bool $unsigned #[Schema('M01_schema')]
 * @property bool $auto_increment #[Schema('M01_schema')]
 * @property bool $index #[Schema('M01_schema')]
 * @property bool $unique #[Schema('M01_schema')]
 * @property int $length #[Schema('M01_schema')]
 * @property Property $property
 * @property array $modifiers
 */
#[Name('column'), Schema('M01_schema'), Meta]
class Column extends Model
{
    public function create(Blueprint $table, string $prefix = ''): void {
        $table->addColumn($this->type, $prefix . $this->property->name,
            array_filter($this->modifiers ?? [],
                fn($item) => $item !== null));
    }

    public function drop(Blueprint $table, string $prefix = ''): void {
        $table->dropColumn($prefix . $this->property->name);
    }

    protected function get_property(): Model {
        return $this->__parent;
    }

    protected function get_modifiers(): array {
        return [
            'nullable' => $this->nullable,
            'unsigned' => $this->unsigned,
            'autoIncrement' => $this->auto_increment,
            'index' => $this->index,
            'unique' => $this->unique,
            'length' => $this->length
                ?? ($this->type == 'string' ? 255 : null),
        ];
    }
}