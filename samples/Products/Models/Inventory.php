<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Products\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Model;

/**
 * @property float $qty #[Schema('products')]
 */
#[Name('product_inventory'), Schema('products')]
class Inventory extends Model
{

}