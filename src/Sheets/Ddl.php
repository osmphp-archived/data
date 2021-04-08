<?php

declare(strict_types=1);

namespace Osm\Data\Sheets;

use Osm\Core\Object_;
use Osm\Data\Sheets\Ddl\Command;

class Ddl extends Object_
{
    /**
     * @var Ddl\Command[]
     */
    protected array $commands = [];

    public function run(Command $command): void {
        $command->run();
        $this->commands[] = $command;
    }

    public function undo(): void {
        foreach (array_reverse($this->commands) as $command) {
            $command->undo();
        }
    }
}