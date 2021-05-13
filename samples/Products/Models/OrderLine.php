<?php

declare(strict_types=1);

namespace Osm\Data\Samples\Products\Models;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Attributes\Endpoint;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Models\Record;
use Osm\Data\Data\Attributes\Column;
use Osm\Data\Data\Attributes\Foreign;

/**
 * @property int $order_id #[Schema('M01_products'),
 *      Column('integer', unsigned: true),
 *      Foreign('order', on_delete: 'cascade')]
 * @property int $parent_line_id #[Schema('M01_products'),
 *      Column('integer', unsigned: true, nullable: true),
 *      Foreign('order_line', on_delete: 'cascade')]
 */
#[Name('order_line'), Schema('M01_products'), Endpoint('/orders/lines')]
class OrderLine extends Record
{
}