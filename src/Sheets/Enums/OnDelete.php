<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Enums;

class OnDelete
{
    const CASCADE = 'cascade';
    const RESTRICT = 'restrict';
    const SET_NULL = null;
}