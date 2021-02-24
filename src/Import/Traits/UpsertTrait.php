<?php

declare(strict_types=1);

namespace Osm\Data\Import\Traits;

use Osm\Core\App;
use Osm\Data\Files\File;
use Osm\Data\Import\Module;
use Osm\Data\Sheets\Sheet;
use Symfony\Component\Console\Output\OutputInterface;
use function Osm\merge;

trait UpsertTrait
{
    use RuleTrait;

    public function import(File $file, OutputInterface $output): void {
        global $osm_app; /* @var App $osm_app */

        /* @var Module $module */
        $module = $osm_app->modules[Module::class];

        /* @var Sheet $sheet */
        $sheet = $osm_app->sheets->{$file->sheet_name};

        foreach ($file->files as $format => $filename) {
            $reader = $module->upsert_readers[$format];
            $file->data = merge($file->data, $reader->read($filename));
        }

        $sheet->upsert($file->data);
    }
}