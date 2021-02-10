<?php

declare(strict_types=1);

namespace Osm\Data\Import\Traits;

use Osm\Core\Attributes\Runs;
use Osm\Data\Import\Import;
use Osm\Data\Import\Sync;

trait SheetsTrait
{
    #[Runs(Import::class)]
    public function import(string $path): Import {
        $task = Import::new(['path' => $path]);

        $task->importDirectory();

        return $task;
    }

    #[Runs(Sync::class)]
    public function sync(string $path): Sync {
        $task = Sync::new(['path' => $path]);

        $task->syncDirectory();

        return $task;
    }
}