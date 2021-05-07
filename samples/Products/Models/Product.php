<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Products\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Attributes\SubtypeBy;
use Osm\Data\Data\Model;

/**
 * @property string $sku #[Schema('M01_products')]
 * @property string $type #[Schema('M01_products')]
 * @property Product[] $related_products #[Schema('M01_products')]
 * @property Inventory $inventory #[Schema('M01_products')]
 */
#[Name('product'), Schema('M01_products'), Endpoint('/products'), SubtypeBy('type')]
class Product extends Model
{
}