<?php

declare(strict_types=1);

namespace Osm\Data\Files\Rules;

use Osm\Data\Files\Exceptions\ProcessingError;
use function Osm\__;
use function Osm\merge;
use Osm\Data\Files\Instructions;

class Record extends Rule
{
    public static ?string $name = 'record';

    public function process(string $rootPath, string $path,
        array &$before, array &$after)
    {
        if (!($data = $this->match($path))) {
            return;
        }

        if ($this->data) {
            $data = merge($data, $this->data);
        }

        if (!isset($data->_sheet_name)) {
            throw new ProcessingError(__(":filename: Can't infer the sheet name", [
                'filename' => $path,
            ]));
        }
        $sheetName = $data->_sheet_name;
        unset($data->_sheet_name);

        $ext = $data->_ext ?? '';
        unset($data->_ext);

        if (!isset($data->_key)) {
            $data->_key = "{name}";
        }

        $key = $this->eval($data->_key, $data);
        unset($data->_key);

        if (!isset($before["{$sheetName}|{$key}"])) {
            $before["{$sheetName}|{$key}"] = $instruction = Instructions\Record::new([
                'sheet_name' => $sheetName,
                'files' => [
                    $ext => "{$rootPath}/{$path}",
                ],
                'data' => $data,
            ]);
        }
        else {
            $instruction = $before["{$sheetName}|{$key}"];
            $instruction->files[$ext] = "{$rootPath}/{$path}";
            $instruction->data = merge($instruction->data, $data);
        }
    }
}