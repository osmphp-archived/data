<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Products\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Migration;
use Osm\Data\Data\Model;

/**
 * @property string $country_code #[Migration('taxes')]
 */
#[Name('tax_rate'), Migration('products'), Endpoint('/tax-rates')]
class TaxRate extends Model
{

}