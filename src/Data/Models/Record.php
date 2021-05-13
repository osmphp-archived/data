<?php

declare(strict_types=1);

namespace Osm\Data\Data\Models;

use Osm\Data\Data\Model;
use Osm\Data\Data\Attributes\Schema;
use Osm\Data\Data\Attributes\Column;

/**
 * @property int $id #[Schema('standard'),
 *      Column('integer', unsigned: true, auto_increment: true)]
 * @property \stdClass $json #[Schema('standard'),
 *      Column('json', nullable: true)]
 */
class Record extends Model
{
}