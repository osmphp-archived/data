<?php

declare(strict_types=1);

namespace Osm\Data\Import;

use Osm\Core\App;
use Osm\Core\Module as BaseModule;
use Osm\Data\Files\Module as FilesModule;
use Osm\Data\Files\Rules\Rule;
use Osm\Data\Files\Rules\Upsert;
use Osm\Data\Import\UpsertReaders\UpsertReader;
use Osm\Data\Sheets\Sheets;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @property FilesModule $files
 * @property UpsertReader[] $upsert_readers
 */
class Module extends BaseModule
{
    public static array $traits = [
        Sheets::class => Traits\SheetsTrait::class,
        Rule::class => Traits\RuleTrait::class,
        Upsert::class => Traits\UpsertTrait::class,
    ];

    public static array $requires = [
        \Osm\Framework\Translations\Module::class,
        FilesModule::class,
    ];

    public function import(string $path, ?OutputInterface &$output = null,
        array $rules = [], string $relativePath = ''): void
    {
        if (!$output) {
            $output = new BufferedOutput();
        }

        foreach ($this->files->iterate($path, $rules, $relativePath) as $file) {
            $file->rule->import($file, $output);
        }
    }

    /** @noinspection PhpUnused */
    protected function get_files(): BaseModule|FilesModule {
        global $osm_app; /* @var App $osm_app */

        return $osm_app->modules[FilesModule::class];
    }

    /** @noinspection PhpUnused */
    protected function get_upsert_readers(): array {
        global $osm_app; /* @var App $osm_app */

        $readers = [];

        foreach ($osm_app->classes as $class) {
            if (!is_subclass_of($class->name, UpsertReader::class, true)) {
                continue;
            }

            $new = "{$class->name}::new";

            /* @var UpsertReader $reader */
            $reader = $new();
            $readers[$reader->name] = $reader;
        }

        return $readers;
    }
}