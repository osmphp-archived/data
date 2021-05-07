<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models\Properties;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Exceptions\ResolutionError;
use Osm\Data\Data\Model;
use Osm\Data\Data\Models\Class_;
use Osm\Data\Data\Models\Property;
use Osm\Data\Data\Attributes\Schema;
use function Osm\__;

/**
 * @property int $object_class_id #[Schema('M01_schema')]
 * @property Class_ $object_class
 */
#[Name('property/ref')]
class Ref extends Property
{
    public string $type = 'ref';

    public function dehydrate(mixed $hydrated): mixed {
        return null;
    }

    public function resolve(mixed $hydrated, array &$identities = null,
        Model|\stdClass|null $parent = null): void
    {
        if (!$identities) {
            throw new ResolutionError(__(
                "Can't resolve reference properties without \$identities map"));
        }

        if (!$parent || !is_object($parent)) {
            throw new ResolutionError(__(
                "Reference property :property doesn't belong to an object",
                ['property' => $this->name]));
        }

        if (!isset($this->object_class->endpoint)) {
            throw new ResolutionError(__(
                "Reference property :property class should have an endpoint",
                ['property' => $this->name]));
        }

        $parent->{$this->name} = isset($parent->{"{$this->name}_id"})
            ? $identities[$this->object_class->endpoint][$parent->{"{$this->name}_id"}] ?? null
            : null;
    }
}