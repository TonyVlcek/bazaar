<?php

use Phinx\Migration\AbstractMigration;

class RootPages extends AbstractMigration
{

    public function change()
    {
        $table = $this->table('root_pages');
        $table
            ->addColumn('resource', 'string', ['length' => 255])
            ->addColumn('url', 'string', ['length' => 2083])
            ->addIndex('resource')
            ->create();
    }

}
