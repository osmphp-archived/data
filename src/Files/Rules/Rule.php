<?php

/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace Osm\Data\Files\Rules;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Expected;
use Osm\Data\Files\Exceptions\IterationError;
use Osm\Data\Files\File;
use function Osm\merge;
use function Osm\__;

/**
 * @property string $pattern #[Expected]
 * @property ?\stdClass $data #[Expected]
 * @property string $regex
 */
class Rule extends Object_
{
    public static ?string $name;
    const VARIABLE_REGEX = '/\*(?<recursive>\*)?(?:\{(?<variable>[^}]+)\})?/';

    /** @noinspection PhpUnusedParameterInspection */
    public function recognize(string $rootPath, string $path,
        array &$before, array &$after)
    {
        throw new NotImplemented();
    }

    protected function recognizeFilename(string $path): ?\stdClass {
        if (!preg_match($this->regex, $path, $match)) {
            return null;
        }

        $data = new \stdClass();

        foreach ($match as $key => $value) {
            if (!is_numeric($key)) {
                $data->$key = $value;
            }
        }

        if ($this->data) {
            $data = merge($data, $this->data);
        }

        return $data;
    }

    /** @noinspection PhpUnused */
    protected function get_regex(): string {
        // cut off the file extension
        $pattern = $this->pattern;
        if (($pos = mb_strrpos($pattern, '.')) !== false) {
            $ext = mb_substr($pattern, $pos + 1);
            $pattern = mb_substr($pattern, 0, $pos);
        }
        else {
            $ext = null;
        }

        $regex = '';
        $pos = 0;

        preg_replace_callback(static::VARIABLE_REGEX, function ($match)
            use (&$regex, &$pos, $pattern)
        {
            // add the text between {variables}
            $regex .= preg_quote(mb_substr($pattern, $pos, $match[0][1] - $pos),
                '/');
            $pos = $match[0][1] + mb_strlen($match[0][0]);

            $variable = isset($match['variable'])
                ? "<{$match['variable'][0]}>"
                : ':';

            if (isset($match['recursive'])) {
                $regex .= "(?{$variable}.+)";
            }
            else {
                $regex .= "(?{$variable}[^\\/]+)";
            }

        }, $pattern, -1, $count, PREG_OFFSET_CAPTURE);

        $regex .= preg_quote(mb_substr($pattern, $pos), '/');

        if ($ext !== null) {
            $regex .= "\\.(?<_ext>{$ext})";
        }

        return "/^{$regex}$/u";
    }

    protected function eval(string $pattern, \stdClass $data): string {
        return preg_replace_callback(static::VARIABLE_REGEX, function ($match)
            use($data)
        {
            return $data->{$match['variable']};

        }, $pattern);
    }


    protected function recognizeSheetName(\stdClass $data, string $path): string {
        if (!isset($data->_sheet_name)) {
            throw new IterationError(__(":filename: Can't infer the sheet name", [
                'filename' => $path,
            ]));
        }
        $sheetName = $data->_sheet_name;
        unset($data->_sheet_name);

        return $sheetName;
    }

    protected function recognizeExtension(\stdClass $data): string {
        $ext = $data->_ext ?? '';
        unset($data->_ext);

        return $ext;
    }

    protected function recognizeKey(\stdClass $data): string {
        if (!isset($data->_key)) {
            $data->_key = "{name}";
        }

        $key = $this->eval($data->_key, $data);
        unset($data->_key);

        return $key;
    }

    protected function addFile(array &$target, string $absolutePath,
        \stdClass $data, string $ext, string $sheetName, ?string $key = null)
        : void
    {
        if (!$key) {
            $key = "{$sheetName}||" . count($target);
        }

        /* @var File $file */
        if (!isset($target[$key])) {
            $target[$key] = $file = File::new([
                'rule' => $this,
                'sheet_name' => $sheetName,
                'files' => [ $ext => $absolutePath ],
                'data' => $data,
            ]);
        }
        else {
            $file = $target[$key];
            $file->files[$ext] = $absolutePath;
            $file->data = merge($file->data, $data);
        }
    }
}