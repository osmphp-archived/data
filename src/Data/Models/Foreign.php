<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Illuminate\Database\Schema\Blueprint;
use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Meta;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Attributes\Ref;
use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Model;

/**
 * @property int $class_id #[Schema('M01_schema')]
 * @property string $on_delete #[Schema('M01_schema')]
 * @property Class_ $class #[Schema('M01_schema'), Ref]
 * @property Property $property
 */
#[Name('foreign'), Schema('M01_schema'), Meta]
class Foreign extends Model
{
    public function create(Blueprints $blueprints, string $prefix = ''): void {
        $blueprints->blueprint()->callbacks[] = function(Blueprint $table)
            use ($prefix)
        {
            $constraint = $table->foreign($this->property->name)
                ->references('id')
                ->on($this->class->table);

            if ($this->on_delete) {
                $constraint->onDelete($this->on_delete);
            }
        };
    }

    protected function get_property(): Model {
        return $this->__parent;
    }
}