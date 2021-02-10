<?php

/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace Osm\Data\Import;

use Osm\Core\Object_;
use Osm\Core\Attributes\Expected;
use function Osm\__;

/**
 * @property string $path #[Expected]
 */
class Import extends Object_
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
        if ($rule = $this->findDirectoryRule($path)) {
            $rule->processDirectory($path);
        }

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

        // if we can process the file according to some rule, do it
        if ($rule = $this->findFileRule($path)) {
            $rule->processFile($path);
        }

        // otherwise, it is not clear how to process the file -
        // report it to the user and continue
        $this->warn($path, __(" doesn't match any import rule",
            ['type' => pathinfo($path, PATHINFO_EXTENSION)]));
    }

    protected function ignore(string $path): bool {
        return false;
    }

    protected function processDirectory(string $path): void {
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

    protected function findFileRule(string $path): ?Rule {
        foreach ($this->rules as $rule) {
            if ($rule->matchFile($path)) {
                return $rule;
            }
        }

        return null;
    }

    protected function findDirectoryRule(string $path): ?Rule {
        foreach ($this->rules as $rule) {
            if ($rule->matchDirectory($path)) {
                return $rule;
            }
        }

        return null;
    }
}