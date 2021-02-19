<?php

declare(strict_types=1);

namespace Osm\Data\Files\Rules;

use Osm\Data\Files\Exceptions\IterationError;
use Osm\Data\Files\File;
use function Osm\__;
use function Osm\merge;

/** @noinspection PhpUnused */
class UpsertMultiple extends Rule
{
    public static ?string $name = 'upsert_multiple';

    public function recognize(string $rootPath, string $path,
        array &$before, array &$after)
    {
        if (!($data = $this->recognizeFilename($path))) {
            return;
        }
        $sheetName = $this->recognizeSheetName($data, $path);
        $ext = $this->recognizeExtension($data);
        $this->addFile($after, "{$rootPath}/{$path}",
            $data, $ext, $sheetName);
    }
}