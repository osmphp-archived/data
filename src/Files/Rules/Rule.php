<?php

/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace Osm\Data\Files\Rules;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Core\Object_;
use Osm\Core\Attributes\Expected;

/**
 * @property string $pattern #[Expected]
 * @property ?\stdClass $data #[Expected]
 * @property string $regex
 */
class Rule extends Object_
{
    public static ?string $name;
    const VARIABLE_REGEX = '/\{(?<variable>[^}]+)(?<recursive>\*)?\}/';

    public function process(string $rootPath, string $path,
        array &$before, array &$after)
    {
        throw new NotImplemented();
    }

    protected function match(string $path): ?\stdClass {
        if (!preg_match($this->regex, $path, $match)) {
            return null;
        }

        $data = new \stdClass();

        foreach ($match as $key => $value) {
            if (!is_numeric($key)) {
                $data->$key = $value;
            }
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

            if (isset($match['recursive'])) {
                $regex .= "(?<{$match['variable'][0]}>.+)";
            }
            else {
                $regex .= "(?<{$match['variable'][0]}>[^\\/]+)";
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

}