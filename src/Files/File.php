<?php

/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace Osm\Data\Files;

use Osm\Core\Object_;
use Osm\Data\Files\Rules\Rule;
use Osm\Core\Attributes\Expected;

/**
 * A logical "file" that contains data to be imported, or instructions on
 * renaming rows, or instructions to delete rows
 *
 * @property Rule $rule #[Expected] The rule object that recognized this
 *      logical file, and knows how to actually import it
 * @property string $sheet_name #[Expected] The name of the sheet that this
 *      logical file will modify
 * @property string[] $files #[Expected] One or more file paths that should be
 *      imported as one logical file
 * @property \stdClass $data #[Expected] The data fetched from the file name,
 *      or in `.osmdatafiles` file, without even reading the contents of
 *      this logical file.
 */
class File extends Object_
{

}