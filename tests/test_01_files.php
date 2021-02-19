<?php

declare(strict_types=1);

namespace Osm\Data\Tests;

use Osm\Data\Files\File;
use Osm\Data\Files\Module;
use Osm\Data\Files\Rules;
use Osm\Data\Samples\App;
use Osm\Runtime\Apps;
use PHPUnit\Framework\TestCase;

class test_01_files extends TestCase
{
    public function test_rule_parsing() {
        Apps::run(Apps::create(App::class), function(App $app) {
            // GIVEN a set of data file rules
            $text = <<<EOT
**{name}.png|jpg|gif upsert {
    # indented comment 
    "_sheet_name": "public_files" 
}

*{_sheet_name}.csv upsert_multiple
*{_sheet_name}/*.csv upsert_multiple
*{_sheet_name}/renamed_*.csv rename_multiple
*{_sheet_name}/deleted_*.csv delete_multiple
*{_sheet_name}/*{name}.json upsert
*{_sheet_name}/*{name}.renamed.json rename
*{_sheet_name}/*{name}.deleted.json delete
env.json upsert { "_sheet_name": "env" }

# comment
readme.md ignore

EOT;

            // WHEN you parse the rule set
            /* @var Module $module */
            $module = $app->modules[Module::class];
            $rules = $module->parse($text);

            // THEN rule objects are created
            $this->assertCount(10, $rules);
            $this->assertInstanceOf(Rules\Upsert::class, $rules[0]);
        });
    }

    public function test_directory_processing() {
        Apps::run(Apps::create(App::class), function(App $app) {
            // GIVEN a sample import files in `sample-data/01` directory

            // WHEN you process them
            /* @var Module $module */
            $module = $app->modules[Module::class];
            $files = iterator_to_array($module->iterate('sample-data/01'));

            // THEN rule objects are created
            $this->assertCount(2, $files);
            $this->assertInstanceOf(File::class, $files[0]);
        });
    }
}