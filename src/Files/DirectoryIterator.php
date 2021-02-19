<?php

/** @noinspection PhpUnusedAliasInspection */
declare(strict_types=1);

namespace Osm\Data\Files;

use Osm\Core\App;
use Osm\Core\Object_;
use Osm\Core\Attributes\Expected;
use Osm\Data\Files\Instructions\Instruction;
use Osm\Data\Files\Rules\Rule;

/**
 * @property string $path #[Expected]
 * @property Module $module
 */
class DirectoryIterator extends Object_
{
    /**
     * @param Rule[] $rules
     * @param string $path
     * @return \Generator
     */
    public function iterate(array $rules = [], string $path = ''): \Generator
    {
        $absolutePath = $path ? "{$this->path}/{$path}" : $this->path;

        /* @var Rule[] $rules */
        $rules = array_merge($rules,
            $this->module->parseFile("{$absolutePath}/.osmdatafiles"));

        $before = [];
        $dirs = [];
        $after = [];

        foreach (new \DirectoryIterator($absolutePath) as $fileInfo) {
            /* @var \SplFileInfo $fileInfo */
            if ($fileInfo->isDot()) {
                continue;
            }

            if (str_starts_with($fileInfo->getFilename(), '.')) {
                continue;
            }

            $relativePath = $path
                ? "{$path}/{$fileInfo->getFilename()}"
                : $fileInfo->getFilename();

            if ($fileInfo->isDir()) {
                $dirs[] = $relativePath;
                continue;
            }

            foreach ($rules as $rule) {
                $rule->recognize($this->path, $relativePath, $before, $after);
            }
        }

        foreach ($before as $instruction) {
            yield $instruction;
        }

        foreach ($dirs as $dir) {
            foreach ($this->iterate($rules, $dir) as $instruction) {
                yield $instruction;
            }
        }

        foreach ($after as $instruction) {
            yield $instruction;
        }
    }

    /**
     * @param string $path
     * @return Rule[]
     */
    public function collectParentRules(string $path): array {
        if (!$path) {
            return [];
        }

        $rules = [];
        $absolutePath = $this->path;

        foreach (explode('/', $path) as $part) {
            $rules = array_merge($rules, $this->module->parseFile(
                "{$absolutePath}/.osmdatafiles"));
            $absolutePath .= "/{$part}";
        }

        return $rules;
    }

    /** @noinspection PhpUnused */
    protected function get_module(): Module {
        global $osm_app; /* @var App $osm_app */

        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $osm_app->modules[Module::class];
    }
}