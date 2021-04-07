<?php

declare(strict_types=1);

namespace Osm\Data\Sheets;

use Osm\Core\Object_;
use function Osm\get_descendant_classes_by_name;

/**
 * @property string[] $__classes
 */
class Sheets extends Object_
{
    protected function default(string $property): mixed {
        if (isset($this->__classes[$property])) {
            $new = "{$this->__classes[$property]}::new";
            return $new();
        }
        return parent::default($property);
    }

    /** @noinspection PhpUnused */
    protected function get___classes(): array {
        return get_descendant_classes_by_name(Sheet::class);
    }
}