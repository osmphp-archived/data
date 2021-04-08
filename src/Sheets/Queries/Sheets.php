<?php

declare(strict_types=1);

namespace Osm\Data\Sheets\Queries;

use Osm\Core\Attributes\Name;
use Osm\Core\Exceptions\NotImplemented;
use Osm\Data\Sheets\Ddl;
use Osm\Data\Sheets\Query;

#[Name('sheets')]
class Sheets extends Query
{
    public function insert(\stdClass $data): int {
        return $this->db->transaction(function() use ($data){
            $id = parent::insert($data);

            $ddl = Ddl::new();
            try {
                $ddl->run(Ddl\Refresh::new());
                $ddl->run(Ddl\CreateTable::new());

                $this->createChildTables($ddl);
            }
            catch (\Throwable $e) {
                $ddl->undo();

                throw $e;
            }

            return $id;
        });
    }

    protected function createChildTables(Ddl $ddl) {
        throw new NotImplemented($this);
    }
}