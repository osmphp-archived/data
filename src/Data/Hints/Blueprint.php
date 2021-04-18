<?php

declare(strict_types=1);

namespace Osm\Data\Data\Hints;

/**
 * @property string $name
 * @property string $type
 * @property callable[] $callbacks
 */
class Blueprint
{
    const CREATE_TABLE = 'create_table';
    const ALTER_TABLE = 'alter_table';
    const DROP_TABLE = 'drop_table';
}