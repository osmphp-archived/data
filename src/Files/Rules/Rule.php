<?php

/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace Osm\Data\Files\Rules;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Expected;

/**
 * @property string $pattern #[Expected]
 * @property ?\stdClass $data #[Expected]
 */
class Rule extends Object_
{
    public static ?string $name;

    public function process(array &$before, array &$after) {
        throw new NotImplemented();
    }
}