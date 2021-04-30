<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Products\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Migration;
use Osm\Data\Data\Model;

/**
 * @property float $qty #[Migration('products')]
 */
#[Name('product_inventory'), Migration('products')]
class Inventory extends Model
{

}