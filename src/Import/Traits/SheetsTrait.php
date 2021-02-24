<?php

declare(strict_types=1);

namespace Osm\Data\Import\Traits;

use Osm\Core\App;
use Osm\Data\Import\Module;
use Symfony\Component\Console\Output\OutputInterface;

trait SheetsTrait
{
    public function import(string $path, ?OutputInterface &$output = null,
        array $rules = [], string $relativePath = ''): void
    {
        global $osm_app; /* @var App $osm_app */

        /* @var Module $module */
        $module = $osm_app->modules[Module::class];

        $module->import($path, $output, $rules, $relativePath);
    }
}