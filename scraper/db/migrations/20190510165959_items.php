<?php

use Phinx\Migration\AbstractMigration;

class Items extends AbstractMigration
{

    /**
     * @throws RuntimeException
     */
    public function change()
    {
        $table = $this->table('items');
        $table
            ->addColumn('resource', 'string', ['length' => 255])
            ->addColumn('detail_url', 'string', ['length' => 2083])
            ->addColumn('title', 'string', ['length' => 255])
            ->addColumn('description', 'text')
            ->addIndex('resource')
            ->create();
    }

}
