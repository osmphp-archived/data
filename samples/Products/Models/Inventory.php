<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Products\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Model;
use Osm\Data\Data\Attributes\Column;

/**
 * @property float $qty #[Schema('M01_products'), Column('integer')]
 */
#[Name('product_inventory'), Schema('M01_products')]
class Inventory extends Model
{

}