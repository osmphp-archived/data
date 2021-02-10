<?php

/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace Osm\Data\Sheets;

use Osm\Core\Object_;
use Osm\Core\Attributes\Expected;
use function Osm\__;

/**
 * @property string $path #[Expected]
 */
class ImportTask extends Object_
{
    /**
     * @var object[]|Message[]
     */
    public array $messages = [];

    /**
     * @var string[]
     */
    protected array $processed = [];

    public function import(): void {
        $this->importDirectory();
        $this->processRecordsets();
    }

    public function importDirectory(string $path = ''): void {
        $this->processDirectory($path);

        $absolutePath = $path ? "{$this->path}/{$path}" : $this->path;

        foreach (new \DirectoryIterator($absolutePath) as $fileInfo) {
            /* @var \SplFileInfo $fileInfo */
            if ($fileInfo->isDot()) {
                continue;
            }

            $relativePath = $path
                ? "{$path}/{$fileInfo->getFilename()}"
                : $fileInfo->getFilename();

            if ($fileInfo->isDir()) {
                $this->importDirectory($relativePath);
                continue;
            }

            $this->importFile($relativePath);
        }
    }

    public function importFile(string $path): void {
        if ($this->ignore($path)) {
            return;
        }

        // process each file only once
        if (isset($this->processed[$path])) {
            return;
        }

        // if its a single record file (json, md, png, ...),
        // import it right away
        if ($this->processFile($path)) {
            return;
        }

        // if its a multiple record file (csv), schedule it to be
        // processed in the second phase
        if ($this->scheduleRecordset($path)) {
            return;
        }

        // otherwise, it is not clear how to process the file -
        // report it to the user and continue
        $this->warn($path, __("':type' file type not supported",
            ['type' => pathinfo($path, PATHINFO_EXTENSION)]));
    }

    protected function ignore(string $path): bool {
        return false;
    }

    protected function processDirectory(string $path): void {
    }

    protected function processFile(string $path): bool {
        return false;
    }

    protected function scheduleRecordset(string $path): bool {
        return false;
    }

    protected function processRecordsets(): void {
    }

    protected function warn(string $path, string $message): void {
        $this->messages[] = (object)[
            'type' => 'warning',
            'path' => $path,
            'message' => $message,
        ];
    }
}