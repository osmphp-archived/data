<?php

declare(strict_types=1);

namespace Osm\Data\Import\Traits;

use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Files\File;
use Symfony\Component\Console\Output\OutputInterface;

trait RuleTrait
{
    public function import(File $file, OutputInterface $output): void {
        throw new NotImplemented();
    }
}