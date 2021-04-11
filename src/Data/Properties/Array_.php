<?php

declare(strict_types=1);

namespace Osm\Data\Data\Properties;

use Osm\Core\Attributes\Name;
use Osm\Data\Data\Property;

/**
 * @property Property $items
 */
#[Name('array')]
class Array_ extends Property
{
    public function __construct(array $data = []) {
        if (isset($data['items'])) {
            $data['items'] = $this->schema->createProperty((object)[
                'type' => $data['items']->type,
                'id' => $data['id'],
            ]);
        }

        parent::__construct($data);
    }
}