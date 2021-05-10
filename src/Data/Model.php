<?php

declare(strict_types=1);

namespace Osm\Data\Data;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Exceptions\NotSupported;
use Osm\Core\Object_;
use Osm\Data\Data\Attributes\Schema;
use Osm\Core\Attributes\Serialized;
use function Osm\__;

/**
 * @property int $id #[Schema('standard')]
 * @property \stdClass $json #[Schema('standard')]
 * @property Model $__parent
 */
class Model extends Object_
{
//    /**
//     * @var string[]
//     */
//    #[Serialized]
//    public array $__serialized = [];

//    public function __sleep(): array {
//        return array_unique(array_merge(parent::__sleep(), $this->__serialized));
//    }
}