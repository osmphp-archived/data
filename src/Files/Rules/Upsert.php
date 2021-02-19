<?php

declare(strict_types=1);

namespace Osm\Data\Files\Rules;

class Upsert extends Rule
{
    public static ?string $name = 'upsert';

    public function recognize(string $rootPath, string $path,
        array &$before, array &$after)
    {
        if (!($data = $this->recognizeFilename($path))) {
            return;
        }
        $sheetName = $this->recognizeSheetName($data, $path);
        $ext = $this->recognizeExtension($data);
        $key = $this->recognizeKey($data);
        $this->addFile($before, "{$rootPath}/{$path}",
            $data, $ext, $sheetName, "{$sheetName}|{$key}");
    }
}