<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Meta;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Attributes\SubtypeBy;
use Osm\Data\Data\Blueprints;
use Osm\Data\Data\Model;
use Osm\Data\Data\Models\Column as ColumnModel;
use Osm\Data\Data\Models\Foreign as ForeignModel;
use Osm\Data\Data\Attributes\Column;
use Osm\Data\Data\Attributes\Foreign;

//\Osm\Data\Data\Models\
/**
 * @property int $parent_property_id #[Schema('M01_schema'),
 *      Column('integer', unsigned: true, nullable: true),
 *      Foreign('property', on_delete: 'cascade')]
 * @property string $name #[Schema('M01_schema'),
 *      Column('string', index: true)]
 * @property string $type #[Schema('M01_schema'),
 *      Column('string')]
 * @property ColumnModel $column #[Schema('M01_schema')]
 * @property ForeignModel $foreign #[Schema('M01_schema')]
 */
#[Name('property'), Schema('M01_schema'), Endpoint('/classes/properties'),
    SubtypeBy('type'), Meta]
class Property extends Record
{
    const SOME = 'some';

    public function hydrate(mixed $dehydrated, array &$identities = null)
        : mixed
    {
        throw new NotImplemented($this);
    }

    public function dehydrate(mixed $hydrated): mixed {
        throw new NotImplemented($this);
    }

    public function resolve(mixed $hydrated, array &$identities = null,
        Model|\stdClass|null $parent = null): void
    {
        throw new NotImplemented($this);
    }

    public function hydrateAndResolve(mixed $dehydrated,
        array &$identities = null): mixed
    {
        $hydrated = $this->hydrate($dehydrated, $identities);
        $this->resolve($hydrated, $identities);

        return $hydrated;
    }

    public function createColumn(Blueprints $blueprints, string $prefix = '')
        : void
    {
        if ($this->column) {
            $this->column->create($blueprints, $prefix);
        }

        if ($this->foreign) {
            $this->foreign->create($blueprints, $prefix);
        }
    }
}