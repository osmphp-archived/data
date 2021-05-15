<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Products\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Models\Record;
use Osm\Data\Data\Attributes\Column;

/**
 * @property string $note #[Schema('M01_products'),
 *      Column('string')]
 * @property string $no #[Schema('M02_products'),
 *      Column('string')]
 */
#[Name('order'), Schema('M01_products'), Endpoint('/orders')]
class Order extends Record
{
}