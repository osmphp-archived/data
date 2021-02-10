<?php

declare(strict_types=1);

namespace Osm\Data\Sheets;

use Osm\Core\Attributes\Runs;
use Osm\Core\Object_;

class Sheets extends Object_
{
    #[Runs(ImportTask::class)]
    public function import(string $path): ImportTask {
        $task = ImportTask::new(['path' => $path]);

        $task->importDirectory();

        return $task;
    }
}