<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Types;

use Osm\Core\App;
use Osm\Core\Attributes\Name;
use Osm\Data\Sheets\Data;
use Osm\Data\Sheets\Enums\Types;
use Osm\Data\Sheets\Type;

/**
 * @property string $backref
 * @property Data $data
 * @property string $sheet_name
 */
#[Name(Types::SHEET)]
class Sheet extends Type
{
    public function save(array &$values, \stdClass $data): void {
        // no data is saved directly into the main table
    }

    public function insertIntoChildSheet(\stdClass $data): void {
        if (isset($data->{$this->column->name})) {
            $items = $data->{$this->column->name};
            if (!is_array($items)) {
                $items = [$items];
            }

            foreach ($items as $item) {
                $item->{$this->backref} = $data->id;
                $this->data->sheet($this->sheet_name)->insert($item);
            }
        }
    }

    /** @noinspection PhpUnused */
    protected function get_data(): Data {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->data;
    }

    /** @noinspection PhpUnused */
    protected function get_sheet_name(): string {
        return "{$this->column->sheet_name}__{$this->column->name}";
    }
}